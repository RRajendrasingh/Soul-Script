<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM templates WHERE active = 1 ORDER BY price_inr ASC");
    $templates = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'templates' => $templates
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
