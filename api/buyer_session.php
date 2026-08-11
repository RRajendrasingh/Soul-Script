<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$buyerEmail = trim($_SESSION['buyer_email'] ?? '');

if (empty($buyerEmail)) {
    echo json_encode([
        'logged_in' => false
    ]);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT buyer_name, buyer_phone, buyer_email
        FROM orders
        WHERE LOWER(buyer_email) = LOWER(?)
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$buyerEmail]);
    $buyer = $stmt->fetch();

    if ($buyer) {
        echo json_encode([
            'logged_in' => true,
            'buyer_name' => htmlspecialchars_decode($buyer['buyer_name'], ENT_QUOTES),
            'buyer_phone' => preg_replace('/^\+91/', '', $buyer['buyer_phone']),
            'buyer_email' => $buyer['buyer_email']
        ]);
    } else {
        echo json_encode([
            'logged_in' => true,
            'buyer_email' => $buyerEmail
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'logged_in' => false,
        'error' => $e->getMessage()
    ]);
}
