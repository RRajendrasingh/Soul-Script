<?php
/**
 * SoulScript - Universal Media Resolver (Single Source of Truth)
 * Standardizes all image & media URLs across PHP and JS to prevent path fragility.
 */

if (!defined('APP_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

/**
 * Universal Media URL Resolver for PHP
 *
 * @param string|null $url Raw URL or path
 * @param string $fallback Default fallback image URL
 * @return string Full, absolute, clean web URL
 */
function resolveMediaUrl($url, $fallback = '') {
    if (empty($url)) {
        return $fallback;
    }

    $url = trim($url);

    // 1. Base64 Data Payload -> Return as-is
    if (strpos($url, 'data:image') === 0) {
        return $url;
    }

    // 2. Extract uploads/ relative path from ANY previous URL domain/host
    $pos = strpos($url, 'uploads/');
    if ($pos !== false) {
        return APP_URL . '/' . substr($url, $pos);
    }

    // 3. Absolute HTTP/HTTPS URLs (external Unsplash, etc.)
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }

    // Default: prepend APP_URL and clean leading slashes
    return APP_URL . '/' . ltrim($url, '/');
}

/**
 * Fail-Proof Base64 Image Saver
 * Saves uploaded Base64 images to disk with 0777 permissions and returns public URL,
 * or falls back to data URL if host disk is non-writable.
 */
function saveUploadedBase64Image($photoData, $page_id, $filePrefix = 'photo') {
    if (empty($photoData)) return '';
    $photoData = trim($photoData);

    if (strpos($photoData, 'data:image') === 0) {
        preg_match('/data:image\/(.*?);base64,(.*)/', $photoData, $matches);
        $rawExt = strtolower($matches[1] ?? 'jpg');
        if ($rawExt === 'jpeg') $rawExt = 'jpg';
        
        $allowedExts = ['jpg', 'png', 'webp', 'gif'];
        $ext = in_array($rawExt, $allowedExts) ? $rawExt : 'jpg';

        $imageData = base64_decode($matches[2] ?? '');
        if (empty($imageData)) return $photoData;

        $baseUploadDir = __DIR__ . '/../uploads';
        if (!is_dir($baseUploadDir)) {
            @mkdir($baseUploadDir, 0777, true);
            @chmod($baseUploadDir, 0777);
        }

        $targetDir = $baseUploadDir . '/' . $page_id;
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
            @chmod($targetDir, 0777);
        }

        $fileName = $filePrefix . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $fullDiskPath = $targetDir . '/' . $fileName;

        $bytesWritten = @file_put_contents($fullDiskPath, $imageData);
        if ($bytesWritten !== false && $bytesWritten > 0) {
            @chmod($fullDiskPath, 0666);
            return APP_URL . '/uploads/' . $page_id . '/' . $fileName;
        } else {
            error_log("SoulScript Image Disk Error: Failed writing to $fullDiskPath");
            return $photoData; // Fail-safe fallback to Base64 data URL
        }
    }

    return resolveMediaUrl($photoData);
}
