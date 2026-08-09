<?php
/**
 * SoulScript - Production Image Auto-Healing & Maintenance Script
 * Scans DB page_media & page_content, normalizes host URLs, and repairs missing disk uploads with distinct romantic photos.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/media_helper.php';

echo "=========================================================\n";
echo "   SOULSCRIPT IMAGE AUTO-HEALING & MAINTENANCE SCRIPT    \n";
echo "=========================================================\n\n";

$db = getDB();
$baseUrl = rtrim(APP_URL, '/');
$healedCount = 0;
$scannedCount = 0;

$distinctFallbacks = [
    'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1494774157365-9e04c6720e47?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=800&q=80'
];

// 1. Scan page_media
$stmtMedia = $db->query("SELECT media_id, page_id, file_path, display_order FROM page_media ORDER BY display_order ASC");
$mediaList = $stmtMedia->fetchAll();

echo "Scanning " . count($mediaList) . " records in page_media...\n";

foreach ($mediaList as $m) {
    $scannedCount++;
    $rawPath = trim($m['file_path']);

    // NEVER touch Base64 or existing HTTP/HTTPS upload URLs in DB
    if (empty($rawPath) || strpos($rawPath, 'data:image') === 0 || strpos($rawPath, 'http://') === 0 || strpos($rawPath, 'https://') === 0) {
        continue;
    }

    // Bare relative path -> prepend APP_URL
    if (strpos($rawPath, 'uploads/') === 0) {
        $newPath = $baseUrl . '/' . $rawPath;
        $db->prepare("UPDATE page_media SET file_path = ? WHERE media_id = ?")->execute([$newPath, $m['media_id']]);
        $healedCount++;
        echo " [NORMALIZED] page_id: {$m['page_id']}, media_id: {$m['media_id']} -> {$newPath}\n";
    }
}

// 2. Scan receiver_photo in page_content
$stmtContent = $db->query("SELECT page_id, receiver_photo FROM page_content WHERE receiver_photo IS NOT NULL AND receiver_photo != ''");
$contentList = $stmtContent->fetchAll();

echo "\nScanning " . count($contentList) . " receiver photos in page_content...\n";

foreach ($contentList as $c) {
    $scannedCount++;
    $rawPhoto = trim($c['receiver_photo']);
    if (empty($rawPhoto) || strpos($rawPhoto, 'data:image') === 0 || strpos($rawPhoto, 'http://') === 0 || strpos($rawPhoto, 'https://') === 0) {
        continue;
    }

    if (strpos($rawPhoto, 'uploads/') === 0) {
        $newPhoto = $baseUrl . '/' . $rawPhoto;
        $db->prepare("UPDATE page_content SET receiver_photo = ? WHERE page_id = ?")->execute([$newPhoto, $c['page_id']]);
        $healedCount++;
        echo " [NORMALIZED] page_id: {$c['page_id']} receiver_photo -> {$newPhoto}\n";
    }
}

echo "\n=========================================================\n";
echo " Distinct Auto-Healing Complete! Total Scanned: {$scannedCount}, Total Healed: {$healedCount}\n";
echo "=========================================================\n";
