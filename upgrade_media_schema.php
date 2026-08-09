<?php
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    
    // Upgrade page_media.file_path to LONGTEXT to support Base64 images without truncation
    $db->exec("ALTER TABLE page_media MODIFY file_path LONGTEXT NOT NULL");
    
    // Ensure receiver_photo is LONGTEXT
    $db->exec("ALTER TABLE page_content MODIFY receiver_photo LONGTEXT DEFAULT NULL");

    echo json_encode([
        'success' => true,
        'message' => 'Successfully upgraded page_media.file_path and page_content.receiver_photo columns to LONGTEXT in MySQL database!'
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
