<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

$buyer_name  = trim($input['buyer_name'] ?? '');
$buyer_phone = trim($input['buyer_phone'] ?? '');
$buyer_email = trim($input['buyer_email'] ?? '');
$template_id = trim($input['template_id'] ?? '');

if (!$buyer_name || !$buyer_phone || !$buyer_email || !$template_id) {
    sendJsonError('Missing required buyer details');
}

// Name length validation (2 to 60 chars)
if (mb_strlen($buyer_name) < 2 || mb_strlen($buyer_name) > 60) {
    sendJsonError('Full Name must be between 2 and 60 characters.');
}

// Buyer Phone length (8 to 20)
if (mb_strlen($buyer_phone) < 8 || mb_strlen($buyer_phone) > 20) {
    sendJsonError('Valid phone number required.');
}

// Buyer Email validation
if (!filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
    sendJsonError('Invalid email address format (e.g. name@example.com).');
}

$buyer_password = trim($input['buyer_password'] ?? '');
$buyer_password_hash = $buyer_password ? hashHintAnswer($buyer_password) : null;

try {
    $db = getDB();

    // Strict Account Security Check: If email already exists, verify existing account password
    $stmtCheck = $db->prepare("SELECT buyer_password_hash FROM orders WHERE LOWER(buyer_email) = LOWER(?) AND buyer_password_hash IS NOT NULL LIMIT 1");
    $stmtCheck->execute([$buyer_email]);
    $existingAccount = $stmtCheck->fetch();

    if ($existingAccount && !empty($existingAccount['buyer_password_hash'])) {
        // Account exists! Verify that provided password matches existing account password
        if (empty($buyer_password) || hashHintAnswer($buyer_password) !== $existingAccount['buyer_password_hash']) {
            sendJsonError('An account already exists with this email address! Please enter your correct existing account password, or log in at digitalyogi24.com/edit.php', 400);
        }
        $buyer_password_hash = $existingAccount['buyer_password_hash'];
    }

    // Fetch template price
    $stmt = $db->prepare("SELECT price_inr FROM templates WHERE template_id = ? AND active = 1");
    $stmt->execute([$template_id]);
    $template = $stmt->fetch();

    if (!$template) {
        sendJsonError('Invalid template selection');
    }

    $order_id = 'ord_' . time() . '_' . rand(1000, 9999);
    $razorpay_order_id = 'order_rzp_' . base_convert(time(), 10, 36);

    $stmt = $db->prepare("
        INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, buyer_password_hash, template_id, amount_paid, payment_status, razorpay_order_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())
    ");
    $stmt->execute([
        $order_id,
        $buyer_name,
        $buyer_phone,
        $buyer_email,
        $buyer_password_hash,
        $template_id,
        $template['price_inr'],
        $razorpay_order_id
    ]);

    sendJsonSuccess([
        'order' => [
            'order_id' => $order_id,
            'buyer_name' => $buyer_name,
            'buyer_email' => $buyer_email,
            'amount_paid' => (float)$template['price_inr'],
            'template_id' => $template_id,
            'payment_status' => 'pending',
            'razorpay_order_id' => $razorpay_order_id,
        ],
        'razorpay_key_id' => RAZORPAY_KEY_ID
    ]);
} catch (Exception $e) {
    sendJsonError('Order creation failed: ' . $e->getMessage(), 500);
}
