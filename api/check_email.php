<?php
/**
 * SoulScript - Live Email Availability Checker API
 * Checks if a buyer email already exists for smart checkout handling.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$email = trim($_GET['email'] ?? ($_POST['email'] ?? ''));

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['exists' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE LOWER(buyer_email) = LOWER(?)");
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode([
            'exists' => true,
            'count' => (int)$count,
            'message' => 'Account exists for this email address. Please enter your existing account password during checkout to add this gift to your portal.'
        ]);
    } else {
        echo json_encode([
            'exists' => false,
            'count' => 0,
            'message' => 'New email address'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['exists' => false, 'message' => 'Check error: ' . $e->getMessage()]);
}
