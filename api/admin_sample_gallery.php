<?php
/**
 * SoulScript - Admin Sample Gallery Manager API
 * Manages self-hosted default sample WebP assets & captions in /assets/default_gallery/
 * Includes Auto-Healing protection from /uploads_persistent/default_gallery/
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/media_helper.php';

header('Content-Type: application/json');

$assetsDir = __DIR__ . '/../assets/default_gallery';
if (!is_dir($assetsDir)) {
    @mkdir($assetsDir, 0777, true);
    @chmod($assetsDir, 0777);
}

// Persistent Backup Directory (Immune to Hostinger Git Deployment Wipes)
$persistentDir = getPersistentUploadsDir() . '/default_gallery';
if (!is_dir($persistentDir)) {
    @mkdir($persistentDir, 0777, true);
    @chmod($persistentDir, 0777);
}

// Auto-Heal: Sync persistent backup files to public assets directory if public files were wiped
$syncPersistentToPublic = function() use ($assetsDir, $persistentDir) {
    if (is_dir($persistentDir)) {
        $pFiles = array_diff(scandir($persistentDir), ['.', '..']);
        foreach ($pFiles as $pf) {
            $pPath = $persistentDir . '/' . $pf;
            $pubPath = $assetsDir . '/' . $pf;
            if (is_file($pPath) && (!file_exists($pubPath) || filesize($pubPath) === 0)) {
                @copy($pPath, $pubPath);
                @chmod($pubPath, 0666);
            }
        }
    }
};

$syncPersistentToPublic();

$captionsFile = $assetsDir . '/sample_captions.json';
$persistentCaptionsFile = $persistentDir . '/sample_captions.json';

$loadCaptions = function() use ($captionsFile, $persistentCaptionsFile) {
    if (!file_exists($captionsFile) && file_exists($persistentCaptionsFile)) {
        @copy($persistentCaptionsFile, $captionsFile);
    }
    if (file_exists($captionsFile)) {
        $json = @file_get_contents($captionsFile);
        $data = @json_decode($json, true);
        if (is_array($data)) return $data;
    }
    return [];
};

$saveCaptions = function($captionsMap) use ($captionsFile, $persistentCaptionsFile) {
    $json = json_encode($captionsMap, JSON_PRETTY_PRINT);
    @file_put_contents($captionsFile, $json);
    @chmod($captionsFile, 0666);
    @file_put_contents($persistentCaptionsFile, $json);
    @chmod($persistentCaptionsFile, 0666);
};

$baseUrl = rtrim(APP_URL, '/');

// Category Auto-Resolver Helper
$resolveCategory = function($file, $entry, $caption) {
    if (is_array($entry) && !empty($entry['category'])) {
        return $entry['category'];
    }
    $lStr = strtolower($file . ' ' . $caption);
    if (strpos($lStr, 'rakhi') !== false || strpos($lStr, 'sister') !== false || strpos($lStr, 'shagun') !== false) {
        return 'raksha_bandhan';
    }
    if (strpos($lStr, 'bday') !== false || strpos($lStr, 'birthday') !== false || strpos($lStr, 'cake') !== false || strpos($lStr, 'confetti') !== false) {
        return 'birthday';
    }
    if (strpos($lStr, 'proposal') !== false || strpos($lStr, 'ring') !== false || strpos($lStr, 'marry') !== false) {
        return 'proposal';
    }
    if (strpos($lStr, 'distance') !== false || strpos($lStr, 'airport') !== false || strpos($lStr, 'city') !== false || strpos($lStr, 'reunion') !== false) {
        return 'long_distance';
    }
    return 'anniversary';
};

// GET: List all sample WebP assets & captions & categories
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $syncPersistentToPublic();
    $files = is_dir($assetsDir) ? array_diff(scandir($assetsDir), ['.', '..']) : [];
    $captionsMap = $loadCaptions();
    $samples = [];

    $defaultCaptionsPool = [
        'Our First Coffee Date ☕',
        'Sunset Memories 🌅',
        'Together Always 💑',
        'Moments of Pure Joy 😊',
        'Forever & Always 💖',
        'Best Day Ever 🎉',
        'Unforgettable Smile ✨',
        'Holding Hands 🤝',
        'Late Night Talk 🌙',
        'Sweet Surprise 🎁'
    ];

    $index = 0;
    foreach ($files as $file) {
        $full = $assetsDir . '/' . $file;
        if (is_file($full) && strpos($file, '.json') === false && preg_match('/\.(webp|jpg|jpeg|png)$/i', $file)) {
            $entry = $captionsMap[$file] ?? null;
            $caption = is_array($entry) ? ($entry['caption'] ?? '') : (is_string($entry) ? $entry : '');
            if (!$caption) {
                $caption = $defaultCaptionsPool[$index % count($defaultCaptionsPool)];
            }
            $category = is_array($entry) ? ($entry['category'] ?? '') : '';
            if (!$category) {
                $category = $resolveCategory($file, $entry, $caption);
            }

            $samples[] = [
                'filename' => $file,
                'caption' => $caption,
                'category' => $category,
                'url' => $baseUrl . '/assets/default_gallery/' . $file,
                'size_kb' => round(filesize($full) / 1024, 1),
                'updated_at' => date('Y-m-d H:i:s', filemtime($full))
            ];
            $index++;
        }
    }

    echo json_encode([
        'status' => 'success',
        'total_samples' => count($samples),
        'samples' => array_values($samples)
    ], JSON_PRETTY_PRINT);
    exit;
}

// Admin Check for POST operations
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized admin access.']);
    exit;
}

// POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $captionsMap = $loadCaptions();

    if ($action === 'upload') {
        $photoData = $_POST['photo_data'] ?? '';
        $caption = trim($_POST['caption'] ?? 'Romantic Memory 💕');

        if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['photo_file']['tmp_name'];
            $fileData = file_get_contents($tmpPath);
            $mime = mime_content_type($tmpPath);
            $photoData = 'data:' . $mime . ';base64,' . base64_encode($fileData);
        }

        if (empty($photoData) || strpos($photoData, 'data:image') !== 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid image payload provided.']);
            exit;
        }

        preg_match('/data:image\/(.*?);base64,(.*)/', $photoData, $matches);
        $imageData = base64_decode($matches[2] ?? '');
        if (empty($imageData)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Base64 decoding failed.']);
            exit;
        }

        $shortHash = substr(md5(uniqid((string)rand(), true)), 0, 8);
        $fileName = 'sample_' . $shortHash . '.webp';
        $fullPath = $assetsDir . '/' . $fileName;
        $persistentPath = $persistentDir . '/' . $fileName;

        // GD WebP compression & resizing
        $img = @imagecreatefromstring($imageData);
        if ($img !== false && function_exists('imagewebp')) {
            $w = imagesx($img);
            $h = imagesy($img);
            $maxDim = 1200;

            if ($w > $maxDim || $h > $maxDim) {
                if ($w > $h) {
                    $newW = $maxDim;
                    $newH = round(($h * $maxDim) / $w);
                } else {
                    $newH = $maxDim;
                    $newW = round(($w * $maxDim) / $h);
                }
                $resized = imagecreatetruecolor($newW, $newH);
                imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                imagedestroy($img);
                $img = $resized;
            }

            ob_start();
            imagewebp($img, null, 82);
            $webpData = ob_get_clean();
            imagedestroy($img);
            if (!empty($webpData)) {
                $imageData = $webpData;
            }
        }

        // Save to persistent backup FIRST
        @file_put_contents($persistentPath, $imageData);
        @chmod($persistentPath, 0666);

        // Save to public web directory SECOND
        @file_put_contents($fullPath, $imageData);
        @chmod($fullPath, 0666);

        // Save caption & category to metadata map
        $category = trim($_POST['category'] ?? 'anniversary');
        if (!in_array($category, ['anniversary', 'birthday', 'proposal', 'raksha_bandhan', 'long_distance'])) {
            $category = 'anniversary';
        }

        $captionsMap[$fileName] = [
            'caption' => $caption,
            'category' => $category
        ];
        $saveCaptions($captionsMap);

        echo json_encode([
            'status' => 'success',
            'filename' => $fileName,
            'caption' => $caption,
            'category' => $category,
            'url' => $baseUrl . '/assets/default_gallery/' . $fileName,
            'size_kb' => round(filesize($fullPath) / 1024, 1)
        ]);
        exit;
    }

    if ($action === 'update_caption') {
        $filename = basename($_POST['filename'] ?? '');
        $caption = trim($_POST['caption'] ?? '');
        $category = trim($_POST['category'] ?? '');

        if (empty($filename)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing filename parameter.']);
            exit;
        }

        $existing = $captionsMap[$filename] ?? [];
        $existingCaption = is_array($existing) ? ($existing['caption'] ?? '') : (is_string($existing) ? $existing : '');
        $existingCat = is_array($existing) ? ($existing['category'] ?? '') : '';

        $captionsMap[$filename] = [
            'caption' => $caption ?: $existingCaption,
            'category' => $category ?: ($existingCat ?: 'anniversary')
        ];
        $saveCaptions($captionsMap);

        echo json_encode(['status' => 'success', 'message' => 'Caption & Category updated successfully.']);
        exit;
    }

    if ($action === 'delete') {
        $filename = basename($_POST['filename'] ?? '');
        if (empty($filename) || strpos($filename, 'sample_') !== 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid file parameter.']);
            exit;
        }

        $fullPath = $assetsDir . '/' . $filename;
        $persistentPath = $persistentDir . '/' . $filename;

        if (file_exists($fullPath) || file_exists($persistentPath)) {
            @unlink($fullPath);
            @unlink($persistentPath);
            unset($captionsMap[$filename]);
            $saveCaptions($captionsMap);
            echo json_encode(['status' => 'success', 'message' => 'Sample file deleted successfully.']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'File not found on disk.']);
        }
        exit;
    }
}
