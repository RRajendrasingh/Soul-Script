<?php
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    
    // Read-only query on page_media
    $stmtMedia = $db->query("SELECT page_id, media_id, file_path, display_order, caption FROM page_media ORDER BY page_id ASC, display_order ASC");
    $allMedia = $stmtMedia->fetchAll();

    // Read-only query on page_content for receiver_photo
    $stmtContent = $db->query("SELECT page_id, partner_name, receiver_photo FROM page_content WHERE receiver_photo IS NOT NULL AND receiver_photo != ''");
    $allAvatars = $stmtContent->fetchAll();

    $mediaByPage = [];
    foreach ($allMedia as $m) {
        $pageId = $m['page_id'];
        $mediaByPage[$pageId][] = [
            'order' => $m['display_order'],
            'caption' => $m['caption'],
            'file_path_type' => (strpos($m['file_path'], 'data:image') === 0) ? 'Base64' : ((strpos($m['file_path'], 'uploads/') !== false) ? 'Disk Upload' : 'External/Unsplash'),
            'file_path_preview' => (strpos($m['file_path'], 'data:image') === 0) ? (substr($m['file_path'], 0, 40) . '...') : $m['file_path']
        ];
    }

    $avatarsByPage = [];
    foreach ($allAvatars as $a) {
        $avatarsByPage[$a['page_id']] = [
            'partner' => $a['partner_name'],
            'type' => (strpos($a['receiver_photo'], 'data:image') === 0) ? 'Base64' : ((strpos($a['receiver_photo'], 'uploads/') !== false) ? 'Disk Upload' : 'External/Unsplash'),
            'preview' => (strpos($a['receiver_photo'], 'data:image') === 0) ? (substr($a['receiver_photo'], 0, 40) . '...') : $a['receiver_photo']
        ];
    }

    echo json_encode([
        'total_media_records' => count($allMedia),
        'total_pages_with_media' => count($mediaByPage),
        'avatars' => $avatarsByPage,
        'pages_summary' => $mediaByPage
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
