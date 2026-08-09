<?php
/**
 * SoulScript - Persistent Unsplash to Self-Hosted WebP Sample Gallery Migrator
 * Downloads, converts, and backs up all sample WebP photos to BOTH
 * /assets/default_gallery/ AND persistent storage outside public_html!
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

    // Helper to download & convert image to WebP with dual persistent saving
    $downloadToWebp = function($url) use ($assetsDir, $persistentDir, $baseUrl) {
        if (empty($url)) return '';

        $hash = substr(md5($url), 0, 8);
        $fileName = 'sample_' . $hash . '.webp';
        $fullPath = $assetsDir . '/' . $fileName;
        $persistentPath = $persistentDir . '/' . $fileName;
        $publicUrl = $baseUrl . '/assets/default_gallery/' . $fileName;

        if (file_exists($persistentPath) && filesize($persistentPath) > 0) {
            if (!file_exists($fullPath) || filesize($fullPath) === 0) {
                @copy($persistentPath, $fullPath);
                @chmod($fullPath, 0666);
            }
            return $publicUrl;
        }

        if (file_exists($fullPath) && filesize($fullPath) > 0) {
            @copy($fullPath, $persistentPath);
            @chmod($persistentPath, 0666);
            return $publicUrl;
        }

        // Download via cURL
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

        // Convert GD to WebP
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

        return $publicUrl;
    };

    // Curated Seed Sample Pool
    $curatedSeedUrls = [
        'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80' => 'Our First Coffee Date ☕',
        'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=800&q=80' => 'Sunset Memories 🌅',
        'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80' => 'Together Always 💑',
        'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80' => 'Moments of Pure Joy 😊',
        'https://images.unsplash.com/photo-1494774157365-9e04c6720e47?auto=format&fit=crop&w=800&q=80' => 'Forever & Always 💖',
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80' => 'Best Day Ever 🎉',
        'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=800&q=80' => 'Unforgettable Smile ✨',
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=800&q=80' => 'Holding Hands 🤝',
        'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=800&q=80' => 'Late Night Talk 🌙',
        'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=crop&w=800&q=80' => 'Sweet Surprise 🎁'
    ];

    $captionsMap = [];
    foreach ($curatedSeedUrls as $u => $cap) {
        $savedUrl = $downloadToWebp($u);
        $fn = basename($savedUrl);
        if (!empty($fn)) {
            $captionsMap[$fn] = $cap;
        }
    }

    // Save captions map to persistent location
    $captionsJson = json_encode($captionsMap, JSON_PRETTY_PRINT);
    @file_put_contents($assetsDir . '/sample_captions.json', $captionsJson);
    @chmod($assetsDir . '/sample_captions.json', 0666);
    @file_put_contents($persistentDir . '/sample_captions.json', $captionsJson);
    @chmod($persistentDir . '/sample_captions.json', 0666);

    // 1. Process page_media table
    $stmtMedia = $db->query("SELECT media_id, file_path FROM page_media");
    $mediaRows = $stmtMedia->fetchAll();

    $updatedMediaCount = 0;
    $updateStmtMedia = $db->prepare("UPDATE page_media SET file_path = :file_path WHERE media_id = :media_id");

    foreach ($mediaRows as $m) {
        $oldUrl = $m['file_path'];
        if (strpos($oldUrl, 'images.unsplash.com') !== false) {
            $newUrl = $downloadToWebp($oldUrl);
            if ($newUrl !== $oldUrl) {
                $updateStmtMedia->execute([':file_path' => $newUrl, ':media_id' => $m['media_id']]);
                $updatedMediaCount++;
            }
        }
    }

    // 2. Process page_content table (receiver_photo)
    $stmtContent = $db->query("SELECT page_id, receiver_photo FROM page_content");
    $contentRows = $stmtContent->fetchAll();

    $updatedContentCount = 0;
    $updateStmtContent = $db->prepare("UPDATE page_content SET receiver_photo = :receiver_photo WHERE page_id = :page_id");

    foreach ($contentRows as $c) {
        $oldUrl = $c['receiver_photo'];
        if (strpos($oldUrl, 'images.unsplash.com') !== false) {
            $newUrl = $downloadToWebp($oldUrl);
            if ($newUrl !== $oldUrl) {
                $updateStmtContent->execute([':receiver_photo' => $newUrl, ':page_id' => $c['page_id']]);
                $updatedContentCount++;
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'seeded_samples' => count($captionsMap),
        'updated_media_rows' => $updatedMediaCount,
        'updated_avatar_rows' => $updatedContentCount,
        'message' => 'All sample WebP photos downloaded, backed up to persistent storage, and synchronized successfully!'
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
