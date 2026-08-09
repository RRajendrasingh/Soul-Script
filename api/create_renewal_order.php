<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/expiration_helper.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$token = trim($input['token'] ?? $_GET['token'] ?? '');

if (!$token) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Edit token is required for renewal']);
    exit;
}

try {
    $db = getDB();

    // Fetch page & order details
    $stmt = $db->prepare("
        SELECT p.*, o.buyer_name, o.buyer_phone, o.buyer_email
        FROM pages p
        JOIN orders o ON p.order_id = o.order_id
        WHERE p.edit_token = ?
    ");
    $stmt->execute([$token]);
    $page = $stmt->fetch();

    if (!$page) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Page not found']);
        exit;
    }

    $renewalOrderId = 'ord_renew_' . time() . '_' . rand(1000, 9999);
    $renewalAmount = RENEWAL_FEE_INR;

    // Create renewal record in orders table
    $stmtOrder = $db->prepare("
        INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmtOrder->execute([
        $renewalOrderId,
        $page['buyer_name'],
        $page['buyer_phone'] ?? '+919999999999',
        $page['buyer_email'],
        'renewal_1_year',
        $renewalAmount
    ]);

    echo json_encode([
        'success' => true,
        'order_id' => $renewalOrderId,
        'amount_inr' => $renewalAmount,
        'razorpay_key' => RAZORPAY_KEY_ID,
        'page_id' => $page['page_id'],
        'url_slug' => $page['url_slug'],
        'message' => 'Renewal order created successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
