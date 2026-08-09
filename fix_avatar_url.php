<?php
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    $validPhoto = 'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=800&q=80';
    
    // Check if scrapbook_2 exists
    $stmt = $db->query("SELECT file_path FROM page_media WHERE page_id = 'page_1786221809_313' AND file_path LIKE 'https://%' LIMIT 1");
    $row = $stmt->fetch();
    if ($row && !empty($row['file_path'])) {
        $validPhoto = $row['file_path'];
    }

    $db->prepare("UPDATE page_content SET receiver_photo = ? WHERE page_id = 'page_1786221809_313'")->execute([$validPhoto]);

    echo json_encode([
        'success' => true,
        'updated_avatar' => $validPhoto
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
