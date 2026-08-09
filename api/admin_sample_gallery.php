<?php
/**
 * SoulScript - Admin Sample Gallery Manager API
 * Manages self-hosted default sample WebP assets stored in /assets/default_gallery/
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/media_helper.php';

header('Content-Type: application/json');

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized admin access.']);
    exit;
}

$assetsDir = __DIR__ . '/../assets/default_gallery';
if (!is_dir($assetsDir)) {
    @mkdir($assetsDir, 0777, true);
    @chmod($assetsDir, 0777);
}

$baseUrl = rtrim(APP_URL, '/');

// GET: List all sample WebP assets
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $files = is_dir($assetsDir) ? array_diff(scandir($assetsDir), ['.', '..']) : [];
    $samples = [];

    foreach ($files as $file) {
        $full = $assetsDir . '/' . $file;
        if (is_file($full) && (strpos($file, 'sample_') === 0 || preg_match('/\.(webp|jpg|png)$/i', $file))) {
            $samples[] = [
                'filename' => $file,
                'url' => $baseUrl . '/assets/default_gallery/' . $file,
                'size_kb' => round(filesize($full) / 1024, 1),
                'updated_at' => date('Y-m-d H:i:s', filemtime($full))
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'total_samples' => count($samples),
        'samples' => array_values($samples)
    ], JSON_PRETTY_PRINT);
    exit;
}

// POST: Action dispatch (Upload or Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $photoData = $_POST['photo_data'] ?? '';
        
        // Handle direct file upload if present
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

        // Compress / Convert to WebP using GD if available
        $img = @imagecreatefromstring($imageData);
        if ($img !== false && function_exists('imagewebp')) {
            // Constrain max dimensions to 1200px
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

        @file_put_contents($fullPath, $imageData);
        @chmod($fullPath, 0666);

        echo json_encode([
            'status' => 'success',
            'filename' => $fileName,
            'url' => $baseUrl . '/assets/default_gallery/' . $fileName,
            'size_kb' => round(filesize($fullPath) / 1024, 1)
        ]);
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
        if (file_exists($fullPath)) {
            @unlink($fullPath);
            echo json_encode(['status' => 'success', 'message' => 'Sample file deleted successfully.']);
        } else {
            http_response_code(444);
            echo json_encode(['status' => 'error', 'message' => 'File not found on disk.']);
        }
        exit;
    }
}
