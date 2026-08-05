<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

$buyer_name  = trim($input['buyer_name'] ?? '');
$buyer_phone = trim($input['buyer_phone'] ?? '');
$buyer_email = trim($input['buyer_email'] ?? '');
$template_id = trim($input['template_id'] ?? '');

if (!$buyer_name || !$buyer_phone || !$buyer_email || !$template_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required buyer details']);
    exit;
}

// Name length validation (2 to 60 chars)
if (mb_strlen($buyer_name) < 2 || mb_strlen($buyer_name) > 60) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Full Name must be between 2 and 60 characters.']);
    exit;
}

// Indian Mobile Number validation (Must be 10 digits starting with 6, 7, 8, or 9; optional +91 or 0 prefix)
$clean_phone = preg_replace('/[^0-9]/', '', $buyer_phone);
if (strlen($clean_phone) === 12 && substr($clean_phone, 0, 2) === '91') {
    $clean_phone = substr($clean_phone, 2);
} elseif (strlen($clean_phone) === 11 && substr($clean_phone, 0, 1) === '0') {
    $clean_phone = substr($clean_phone, 1);
}

if (!preg_match('/^[6-9]\d{9}$/', $clean_phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9 (e.g. 9876543210 or +91 9876543210).']);
    exit;
}

// Email format validation
if (!filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address format (e.g. name@example.com).']);
    exit;
}

$buyer_password = trim($input['buyer_password'] ?? '');
$buyer_password_hash = $buyer_password ? hashHintAnswer($buyer_password) : null;

try {
    $db = getDB();
    
    // Fetch template price
    $stmt = $db->prepare("SELECT price_inr FROM templates WHERE template_id = ? AND active = 1");
    $stmt->execute([$template_id]);
    $template = $stmt->fetch();

    if (!$template) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid template selection']);
        exit;
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

    echo json_encode([
        'success' => true,
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
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Order creation failed: ' . $e->getMessage()]);
}
