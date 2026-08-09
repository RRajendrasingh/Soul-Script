<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$rawBody = file_get_contents('php://input');
$input = json_decode($rawBody, true) ?: $_POST;

$order_id = $input['order_id'] ?? null;
$razorpay_payment_id = $input['razorpay_payment_id'] ?? null;
$status = $input['status'] ?? 'paid';
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? null;

// Handle real Razorpay Webhook event payload
if (isset($input['event']) && strpos($input['event'], 'payment.captured') !== false) {
    $payloadEntity = $input['payload']['payment']['entity'] ?? [];
    $razorpay_payment_id = $payloadEntity['id'] ?? null;
    $notes = $payloadEntity['notes'] ?? [];
    $order_id = $notes['order_id'] ?? null;

    // Verify webhook signature if configured
    if ($signature && defined('RAZORPAY_WEBHOOK_SECRET') && RAZORPAY_WEBHOOK_SECRET !== 'whsec_soulscript_secret') {
        $expectedSignature = hash_hmac('sha256', $rawBody, RAZORPAY_WEBHOOK_SECRET);
        if (!hash_equals($expectedSignature, $signature)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid Webhook Signature']);
            exit;
        }
    }
}

if (!$order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing order_id']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    if ($status === 'captured' || $status === 'paid' || $status === 'success') {
        $payment_id = $razorpay_payment_id ?: ('pay_' . time());
        $stmt = $db->prepare("UPDATE orders SET payment_status = 'paid', razorpay_payment_id = ? WHERE order_id = ?");
        $stmt->execute([$payment_id, $order_id]);

        echo json_encode([
            'success' => true,
            'message' => 'Payment status updated to paid via server webhook',
            'order_id' => $order_id
        ]);
    } else {
        $stmt = $db->prepare("UPDATE orders SET payment_status = 'failed' WHERE order_id = ?");
        $stmt->execute([$order_id]);

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Payment marked as failed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
