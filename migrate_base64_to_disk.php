<?php
/**
 * SoulScript - Safe Base64-to-Disk Migration Utility
 * Converts existing Base64 MySQL entries into clean Web URLs on Hostinger disk.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/media_helper.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    $baseUrl = rtrim(APP_URL, '/');
    $convertedMedia = 0;
    $convertedContent = 0;

    // 1. Scan page_media
    $stmtMedia = $db->query("SELECT media_id, page_id, file_path, display_order FROM page_media WHERE file_path LIKE 'data:image%'");
    $base64MediaList = $stmtMedia->fetchAll();

    foreach ($base64MediaList as $m) {
        $page_id = $m['page_id'];
        $photoData = trim($m['file_path']);
        $displayOrder = $m['display_order'];

        preg_match('/data:image\/(.*?);base64,(.*)/', $photoData, $matches);
        $rawExt = strtolower($matches[1] ?? 'jpg');
        if ($rawExt === 'jpeg') $rawExt = 'jpg';
        $ext = in_array($rawExt, ['jpg', 'png', 'webp', 'gif']) ? $rawExt : 'jpg';

        $imageData = base64_decode($matches[2] ?? '');
        if (!empty($imageData)) {
            $targetDir = __DIR__ . '/uploads/' . $page_id;
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
                @chmod($targetDir, 0777);
            }

            $fileName = 'scrapbook_' . $displayOrder . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $fullDiskPath = $targetDir . '/' . $fileName;

            $written = @file_put_contents($fullDiskPath, $imageData);
            if ($written !== false && $written > 0 && file_exists($fullDiskPath)) {
                @chmod($fullDiskPath, 0666);
                $cleanUrl = $baseUrl . '/uploads/' . $page_id . '/' . $fileName;

                $db->prepare("UPDATE page_media SET file_path = ? WHERE media_id = ?")->execute([$cleanUrl, $m['media_id']]);
                $convertedMedia++;
            }
        }
    }

    // 2. Scan receiver_photo in page_content
    $stmtContent = $db->query("SELECT page_id, receiver_photo FROM page_content WHERE receiver_photo LIKE 'data:image%'");
    $base64ContentList = $stmtContent->fetchAll();

    foreach ($base64ContentList as $c) {
        $page_id = $c['page_id'];
        $photoData = trim($c['receiver_photo']);

        preg_match('/data:image\/(.*?);base64,(.*)/', $photoData, $matches);
        $rawExt = strtolower($matches[1] ?? 'jpg');
        if ($rawExt === 'jpeg') $rawExt = 'jpg';
        $ext = in_array($rawExt, ['jpg', 'png', 'webp', 'gif']) ? $rawExt : 'jpg';

        $imageData = base64_decode($matches[2] ?? '');
        if (!empty($imageData)) {
            $targetDir = __DIR__ . '/uploads/' . $page_id;
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
                @chmod($targetDir, 0777);
            }

            $fileName = 'avatar_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $fullDiskPath = $targetDir . '/' . $fileName;

            $written = @file_put_contents($fullDiskPath, $imageData);
            if ($written !== false && $written > 0 && file_exists($fullDiskPath)) {
                @chmod($fullDiskPath, 0666);
                $cleanUrl = $baseUrl . '/uploads/' . $page_id . '/' . $fileName;

                $db->prepare("UPDATE page_content SET receiver_photo = ? WHERE page_id = ?")->execute([$cleanUrl, $page_id]);
                $convertedContent++;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Migration complete!',
        'converted_media_photos' => $convertedMedia,
        'converted_receiver_photos' => $convertedContent
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
