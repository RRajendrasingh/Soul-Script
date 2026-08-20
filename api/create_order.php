<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

session_start();
$isAdmin = !empty($_SESSION['admin_logged_in']);

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

    // Check if user is currently logged in via session
    $sessionEmail = trim($_SESSION['buyer_email'] ?? '');
    $isLoggedInBuyer = (!empty($sessionEmail) && strtolower($sessionEmail) === strtolower($buyer_email));

    // Account Security Check: If email already exists, verify existing account password unless buyer is logged in via session
    $stmtCheck = $db->prepare("SELECT buyer_password_hash FROM orders WHERE LOWER(buyer_email) = LOWER(?) AND buyer_password_hash IS NOT NULL LIMIT 1");
    $stmtCheck->execute([$buyer_email]);
    $existingAccount = $stmtCheck->fetch();

    if ($existingAccount && !empty($existingAccount['buyer_password_hash'])) {
        if ($isLoggedInBuyer) {
            // Automatically attach to logged in buyer's existing account password hash
            $buyer_password_hash = $existingAccount['buyer_password_hash'];
        } else {
            // Unauthenticated guest: Verify that provided password matches existing account password
            if (empty($buyer_password) || hashHintAnswer($buyer_password) !== $existingAccount['buyer_password_hash']) {
                sendJsonError('An account already exists with this email address! Please enter your correct existing account password, or log in at digitalyogi24.com/edit.php', 400);
            }
            $buyer_password_hash = $existingAccount['buyer_password_hash'];
        }
    }

    // Fetch template price
    $stmt = $db->prepare("SELECT price_inr FROM templates WHERE template_id = ? AND active = 1");
    $stmt->execute([$template_id]);
    $template = $stmt->fetch();

    if (!$template) {
        sendJsonError('Invalid template selection');
    }

    $order_id = 'ord_' . time() . '_' . rand(1000, 9999);
    $razorpay_order_id = null;

    // ADMIN SUPER BYPASS: Logged in website owners get 100% FREE instantly active orders!
    $paymentStatus = $isAdmin ? 'paid' : 'pending';
    $amountPaid = $isAdmin ? 0 : (float)$template['price_inr'];

    // Generate Official Razorpay Order ID via API
    if (!$isAdmin && defined('RAZORPAY_KEY_ID') && defined('RAZORPAY_KEY_SECRET') && strpos(RAZORPAY_KEY_ID, 'rzp_') === 0) {
        try {
            $ch = curl_init('https://api.razorpay.com/v1/orders');
            curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'amount' => (int)round($amountPaid * 100),
                'currency' => 'INR',
                'receipt' => $order_id,
                'notes' => [
                    'order_id' => $order_id,
                    'template_id' => $template_id,
                    'buyer_email' => $buyer_email
                ]
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $rzpResp = curl_exec($ch);
            $rzpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($rzpCode === 200) {
                $rzpData = json_decode($rzpResp, true);
                if (!empty($rzpData['id'])) {
                    $razorpay_order_id = $rzpData['id'];
                }
            }
        } catch (Exception $exRzp) {}
    }

    if (empty($razorpay_order_id)) {
        $razorpay_order_id = 'order_rzp_' . base_convert(time(), 10, 36);
    }

    $stmt = $db->prepare("
        INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, buyer_password_hash, template_id, amount_paid, payment_status, razorpay_order_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $order_id,
        $buyer_name,
        $buyer_phone,
        $buyer_email,
        $buyer_password_hash,
        $template_id,
        $amountPaid,
        $paymentStatus,
        $razorpay_order_id
    ]);

    sendJsonSuccess([
        'order' => [
            'order_id' => $order_id,
            'buyer_name' => $buyer_name,
            'buyer_email' => $buyer_email,
            'amount_paid' => $amountPaid,
            'template_id' => $template_id,
            'payment_status' => $paymentStatus,
            'razorpay_order_id' => $razorpay_order_id,
        ],
        'is_admin_order' => $isAdmin,
        'razorpay_key_id' => RAZORPAY_KEY_ID
    ]);
} catch (Exception $e) {
    sendJsonError('Order creation failed: ' . $e->getMessage(), 500);
}
