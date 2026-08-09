<?php
/**
 * SoulScript - Unsplash to Self-Hosted WebP Sample Gallery Migrator
 * Converts all external Unsplash URLs in database tables (page_media, page_content)
 * into self-hosted WebP files stored under /assets/default_gallery/
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/media_helper.php';

header('Content-Type: application/json');

try {
    $db = getDB();

    $assetsDir = __DIR__ . '/assets/default_gallery';
    if (!is_dir($assetsDir)) {
        @mkdir($assetsDir, 0777, true);
        @chmod($assetsDir, 0777);
    }

    $baseUrl = rtrim(APP_URL, '/');

    // Helper to download & convert Unsplash image to WebP
    $downloadToWebp = function($url) use ($assetsDir, $baseUrl) {
        if (empty($url) || strpos($url, 'images.unsplash.com') === false) {
            return $url;
        }

        $hash = substr(md5($url), 0, 8);
        $fileName = 'sample_' . $hash . '.webp';
        $fullPath = $assetsDir . '/' . $fileName;
        $publicUrl = $baseUrl . '/assets/default_gallery/' . $fileName;

        if (file_exists($fullPath) && filesize($fullPath) > 0) {
            return $publicUrl;
        }

        // Fetch image payload
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SoulScript/2.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $imageData = curl_exec($ch);
        curl_close($ch);

        if (empty($imageData)) {
            return $url; // Return original if download fails
        }

        // Attempt GD conversion to WebP if supported
        $img = @imagecreatefromstring($imageData);
        if ($img !== false && function_exists('imagewebp')) {
            ob_start();
            imagewebp($img, null, 82);
            $webpData = ob_get_clean();
            imagedestroy($img);
            if (!empty($webpData)) {
                @file_put_contents($fullPath, $webpData);
                @chmod($fullPath, 0666);
                return $publicUrl;
            }
        }

        // Fallback save raw
        @file_put_contents($fullPath, $imageData);
        @chmod($fullPath, 0666);
        return $publicUrl;
    };

    // 1. Process page_media table
    $stmtMedia = $db->query("SELECT media_id, file_path FROM page_media WHERE file_path LIKE '%images.unsplash.com%'");
    $mediaRows = $stmtMedia->fetchAll();

    $updatedMediaCount = 0;
    $updateStmtMedia = $db->prepare("UPDATE page_media SET file_path = :file_path WHERE media_id = :media_id");

    foreach ($mediaRows as $m) {
        $newUrl = $downloadToWebp($m['file_path']);
        if ($newUrl !== $m['file_path']) {
            $updateStmtMedia->execute([
                ':file_path' => $newUrl,
                ':media_id' => $m['media_id']
            ]);
            $updatedMediaCount++;
        }
    }

    // 2. Process page_content table (receiver_photo)
    $stmtContent = $db->query("SELECT page_id, receiver_photo FROM page_content WHERE receiver_photo LIKE '%images.unsplash.com%'");
    $contentRows = $stmtContent->fetchAll();

    $updatedContentCount = 0;
    $updateStmtContent = $db->prepare("UPDATE page_content SET receiver_photo = :receiver_photo WHERE page_id = :page_id");

    foreach ($contentRows as $c) {
        $newUrl = $downloadToWebp($c['receiver_photo']);
        if ($newUrl !== $c['receiver_photo']) {
            $updateStmtContent->execute([
                ':receiver_photo' => $newUrl,
                ':page_id' => $c['page_id']
            ]);
            $updatedContentCount++;
        }
    }

    // Create default fallback sample file if missing
    $fallbackPath = $assetsDir . '/sample_fallback.webp';
    if (!file_exists($fallbackPath)) {
        $sampleSrc = 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80';
        $fallbackUrl = $downloadToWebp($sampleSrc);
        if (file_exists($assetsDir . '/' . basename($fallbackUrl))) {
            @copy($assetsDir . '/' . basename($fallbackUrl), $fallbackPath);
        }
    }

    echo json_encode([
        'status' => 'success',
        'updated_media_rows' => $updatedMediaCount,
        'updated_avatar_rows' => $updatedContentCount,
        'message' => 'All Unsplash URLs migrated to self-hosted WebP sample gallery assets successfully!'
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
