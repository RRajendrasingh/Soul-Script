<?php
/**
 * SoulScript - Production Image Auto-Healing & Maintenance Script
 * Scans DB page_media & page_content, normalizes host URLs, and repairs missing disk uploads.
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

$sampleFallbacks = [
    'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=800&q=80'
];

// 1. Scan page_media
$stmtMedia = $db->query("SELECT media_id, page_id, file_path, display_order FROM page_media");
$mediaList = $stmtMedia->fetchAll();

echo "Scanning " . count($mediaList) . " records in page_media...\n";

foreach ($mediaList as $m) {
    $scannedCount++;
    $rawPath = trim($m['file_path']);
    $needsUpdate = false;
    $newPath = $rawPath;

    // Check if Base64
    if (strpos($rawPath, 'data:image') === 0) {
        continue;
    }

    // Check for uploads/ relative or absolute path
    $pos = strpos($rawPath, 'uploads/');
    if ($pos !== false) {
        $relPath = substr($rawPath, $pos);
        $fullDiskPath = __DIR__ . '/' . $relPath;

        if (!file_exists($fullDiskPath) || filesize($fullDiskPath) === 0) {
            // Missing file on disk -> Heal with high quality Unsplash fallback
            $fallbackIdx = ($m['display_order'] - 1) % count($sampleFallbacks);
            $newPath = $sampleFallbacks[$fallbackIdx];
            $needsUpdate = true;
            echo " [HEALED] page_id: {$m['page_id']}, media_id: {$m['media_id']} (Missing disk file -> Fallback URL)\n";
        } else {
            // Normalizing domain if needed
            $targetUrl = $baseUrl . '/' . $relPath;
            if ($rawPath !== $targetUrl) {
                $newPath = $targetUrl;
                $needsUpdate = true;
                echo " [NORMALIZED] page_id: {$m['page_id']}, media_id: {$m['media_id']} -> {$newPath}\n";
            }
        }
    }

    if ($needsUpdate) {
        $db->prepare("UPDATE page_media SET file_path = ? WHERE media_id = ?")->execute([$newPath, $m['media_id']]);
        $healedCount++;
    }
}

// 2. Scan receiver_photo in page_content
$stmtContent = $db->query("SELECT page_id, receiver_photo FROM page_content WHERE receiver_photo IS NOT NULL AND receiver_photo != ''");
$contentList = $stmtContent->fetchAll();

echo "\nScanning " . count($contentList) . " receiver photos in page_content...\n";

foreach ($contentList as $c) {
    $scannedCount++;
    $rawPhoto = trim($c['receiver_photo']);
    if (strpos($rawPhoto, 'data:image') === 0) continue;

    $pos = strpos($rawPhoto, 'uploads/');
    if ($pos !== false) {
        $relPath = substr($rawPhoto, $pos);
        $fullDiskPath = __DIR__ . '/' . $relPath;

        if (!file_exists($fullDiskPath) || filesize($fullDiskPath) === 0) {
            $newPhoto = $sampleFallbacks[0];
            $db->prepare("UPDATE page_content SET receiver_photo = ? WHERE page_id = ?")->execute([$newPhoto, $c['page_id']]);
            $healedCount++;
            echo " [HEALED] page_id: {$c['page_id']} receiver_photo (Missing disk file -> Fallback URL)\n";
        }
    }
}

echo "\n=========================================================\n";
echo " Scan Complete! Total Scanned: {$scannedCount}, Total Healed: {$healedCount}\n";
echo "=========================================================\n";
