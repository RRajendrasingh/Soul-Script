<?php
/**
 * SoulScript - Universal Media Resolver (Single Source of Truth)
 * Standardizes all image & media URLs across PHP and JS to prevent path fragility.
 */

if (!defined('APP_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

if (!defined('DEFAULT_MEDIA_FALLBACK')) {
    define('DEFAULT_MEDIA_FALLBACK', rtrim(APP_URL, '/') . '/assets/default_gallery/sample_fallback.webp');
}

/**
 * Universal Media URL Resolver for PHP
 *
 * @param string|null $url Raw URL or path
 * @param string $fallback Default fallback image URL
 * @return string Full, absolute, clean web URL
 */
function resolveMediaUrl($url, $fallback = '') {
    $fallbackUrl = !empty($fallback) ? $fallback : DEFAULT_MEDIA_FALLBACK;
    if (empty($url)) {
        return $fallbackUrl;
    }

    $url = trim($url);
    $baseUrl = rtrim(APP_URL, '/');

    // 1. Base64 Data Payload -> Return as-is
    if (strpos($url, 'data:image') === 0) {
        return $url;
    }

    // 2. Absolute HTTP/HTTPS URLs (e.g. https://digitalyogi24.com/uploads/... or Unsplash) -> ALWAYS Return as-is!
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }

    // 3. Relative uploads path -> Prepend current APP_URL
    if (strpos($url, 'uploads/') === 0) {
        return $baseUrl . '/' . $url;
    }

    // Default: prepend APP_URL and clean leading slashes
    $cleanPath = ltrim($url, '/');
    return $baseUrl . '/' . $cleanPath;
}

/**
 * Fail-Proof Base64 Image Saver
 * Saves uploaded Base64 images to disk with 0777 permissions and returns public URL,
 * or falls back to data URL if host disk is non-writable.
 */
function saveUploadedBase64Image($photoData, $page_id, $filePrefix = 'photo') {
    if (empty($photoData)) return '';
    $photoData = trim($photoData);

    // If Base64 string -> Decode and save to /uploads/{page_id}/ disk folder
    if (strpos($photoData, 'data:image') === 0) {
        preg_match('/data:image\/(.*?);base64,(.*)/', $photoData, $matches);
        $rawExt = strtolower($matches[1] ?? 'webp');
        if ($rawExt === 'jpeg') $rawExt = 'jpg';
        
        $allowedExts = ['webp', 'jpg', 'png', 'gif'];
        $ext = in_array($rawExt, $allowedExts) ? $rawExt : 'webp';

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

        // Clean Market-Standard Prefix & Hash
        $cleanPrefix = 'media';
        if (strpos($filePrefix, 'avatar') !== false || strpos($filePrefix, 'partner') !== false) {
            $cleanPrefix = 'avatar';
        }
        $shortHash = substr(md5(uniqid((string)rand(), true)), 0, 8);
        $fileName = $cleanPrefix . '_' . $shortHash . '.' . $ext;
        $fullDiskPath = $targetDir . '/' . $fileName;

        $bytesWritten = @file_put_contents($fullDiskPath, $imageData);
        if ($bytesWritten !== false && $bytesWritten > 0 && file_exists($fullDiskPath)) {
            @chmod($fullDiskPath, 0666);
            // Return market-standard clean Web URL
            return rtrim(APP_URL, '/') . '/uploads/' . $page_id . '/' . $fileName;
        } else {
            error_log("SoulScript Image Disk Error: Failed writing to $fullDiskPath. Preserving Base64 fallback.");
            return $photoData; // Fail-safe fallback if disk permission fails
        }
    }

    return resolveMediaUrl($photoData);
}

