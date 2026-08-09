<?php
/**
 * SoulScript - Demo Media to Admin Sample Gallery Synchronizer
 * Audits all demo pages (page_demo_01, page_demo_02, page_demo_05, etc.) and registers
 * all demo photos & captions into Admin Sample Gallery (/assets/default_gallery/)
 * with persistent backup protection!
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

    $persistentDir = getPersistentUploadsDir() . '/default_gallery';
    if (!is_dir($persistentDir)) {
        @mkdir($persistentDir, 0777, true);
        @chmod($persistentDir, 0777);
    }

    $baseUrl = rtrim(APP_URL, '/');
    $captionsFile = $assetsDir . '/sample_captions.json';
    $persistentCaptionsFile = $persistentDir . '/sample_captions.json';

    $captionsMap = file_exists($captionsFile) ? (@json_decode(file_get_contents($captionsFile), true) ?: []) : [];

    // Helper to register photo into Admin Sample Gallery
    $registerToSampleGallery = function($url, $caption = '') use ($assetsDir, $persistentDir, $baseUrl, &$captionsMap) {
        if (empty($url)) return $url;

        $url = trim($url);

        // If already a self-hosted sample asset, ensure it exists in persistent storage
        if (strpos($url, '/assets/default_gallery/') !== false) {
            $fileName = basename(parse_url($url, PHP_URL_PATH));
            if (!empty($fileName)) {
                $pubPath = $assetsDir . '/' . $fileName;
                $perPath = $persistentDir . '/' . $fileName;
                if (file_exists($pubPath) && !file_exists($perPath)) {
                    @copy($pubPath, $perPath);
                    @chmod($perPath, 0666);
                }
                if (!empty($caption) && empty($captionsMap[$fileName])) {
                    $captionsMap[$fileName] = $caption;
                }
            }
            return $url;
        }

        // Generate unique hash
        $hash = substr(md5($url), 0, 8);
        $fileName = 'sample_' . $hash . '.webp';
        $fullPath = $assetsDir . '/' . $fileName;
        $persistentPath = $persistentDir . '/' . $fileName;
        $publicUrl = $baseUrl . '/assets/default_gallery/' . $fileName;

        if (file_exists($persistentPath) && filesize($persistentPath) > 500) {
            if (!file_exists($fullPath) || filesize($fullPath) === 0) {
                @copy($persistentPath, $fullPath);
                @chmod($fullPath, 0666);
            }
            if (!empty($caption)) $captionsMap[$fileName] = $caption;
            return $publicUrl;
        }

        // Fetch image via cURL if external or relative
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SoulScript/2.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $imageData = curl_exec($ch);
        curl_close($ch);

        if (empty($imageData)) {
            return $url;
        }

        // Convert to WebP using GD
        $img = @imagecreatefromstring($imageData);
        if ($img !== false && function_exists('imagewebp')) {
            ob_start();
            imagewebp($img, null, 82);
            $webpData = ob_get_clean();
            imagedestroy($img);
            if (!empty($webpData)) {
                $imageData = $webpData;
            }
        }

        @file_put_contents($persistentPath, $imageData);
        @chmod($persistentPath, 0666);

        @file_put_contents($fullPath, $imageData);
        @chmod($fullPath, 0666);

        if (!empty($caption)) {
            $captionsMap[$fileName] = $caption;
        }

        return $publicUrl;
    };

    // 1. Process all demo rows in page_media
    $stmtMedia = $db->query("SELECT media_id, page_id, file_path, caption FROM page_media WHERE page_id LIKE 'page_demo_%'");
    $mediaRows = $stmtMedia->fetchAll();

    $updatedMediaCount = 0;
    $updateStmtMedia = $db->prepare("UPDATE page_media SET file_path = :file_path WHERE media_id = :media_id");

    foreach ($mediaRows as $m) {
        $oldUrl = $m['file_path'];
        $cap = !empty($m['caption']) ? $m['caption'] : 'Romantic Memory 💕';
        $newUrl = $registerToSampleGallery($oldUrl, $cap);
        if (!empty($newUrl) && $newUrl !== $oldUrl) {
            $updateStmtMedia->execute([':file_path' => $newUrl, ':media_id' => $m['media_id']]);
            $updatedMediaCount++;
        }
    }

    // 2. Process all demo rows in page_content (receiver_photo)
    $stmtContent = $db->query("SELECT page_id, partner_name, receiver_photo FROM page_content WHERE page_id LIKE 'page_demo_%'");
    $contentRows = $stmtContent->fetchAll();

    $updatedContentCount = 0;
    $updateStmtContent = $db->prepare("UPDATE page_content SET receiver_photo = :receiver_photo WHERE page_id = :page_id");

    foreach ($contentRows as $c) {
        $oldUrl = $c['receiver_photo'];
        $cap = !empty($c['partner_name']) ? ($c['partner_name'] . ' Avatar') : 'Partner Avatar 💕';
        $newUrl = $registerToSampleGallery($oldUrl, $cap);
        if (!empty($newUrl) && $newUrl !== $oldUrl) {
            $updateStmtContent->execute([':receiver_photo' => $newUrl, ':page_id' => $c['page_id']]);
            $updatedContentCount++;
        }
    }

    // Save updated captions map
    $json = json_encode($captionsMap, JSON_PRETTY_PRINT);
    @file_put_contents($captionsFile, $json);
    @chmod($captionsFile, 0666);
    @file_put_contents($persistentCaptionsFile, $json);
    @chmod($persistentCaptionsFile, 0666);

    echo json_encode([
        'status' => 'success',
        'total_registered_samples' => count($captionsMap),
        'updated_demo_media' => $updatedMediaCount,
        'updated_demo_avatars' => $updatedContentCount,
        'message' => 'All demo page photos and captions successfully synchronized with Admin Sample Gallery!'
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
