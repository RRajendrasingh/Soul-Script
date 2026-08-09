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
function getPersistentUploadsDir() {
    $domainRoot = dirname(__DIR__, 2);
    $persistentDir = $domainRoot . '/uploads_persistent';
    if (!is_dir($persistentDir)) {
        @mkdir($persistentDir, 0777, true);
        @chmod($persistentDir, 0777);
    }
    return $persistentDir;
}

/**
 * Universal Media URL Resolver for PHP with Auto-Healing Protection
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

    // 2. Auto-heal missing disk upload files from persistent storage
    if (strpos($url, '/uploads/') !== false) {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        if (preg_match('/uploads\/(page_[^\/]+)\/(.+)$/', $path, $m)) {
            $pageId = $m[1];
            $fileName = $m[2];

            $publicFile = __DIR__ . '/../uploads/' . $pageId . '/' . $fileName;
            $persistentFile = getPersistentUploadsDir() . '/' . $pageId . '/' . $fileName;

            // Auto-Restore if public file was cleared by Git deployment
            if (!file_exists($publicFile) && file_exists($persistentFile)) {
                $publicFolder = dirname($publicFile);
                if (!is_dir($publicFolder)) {
                    @mkdir($publicFolder, 0777, true);
                    @chmod($publicFolder, 0777);
                }
                @copy($persistentFile, $publicFile);
                @chmod($publicFile, 0666);
            }
        }

        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return $url;
        }
        return $baseUrl . '/' . ltrim($url, '/');
    }

    // 3. Absolute HTTP/HTTPS URLs -> Return as-is
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }

    // Default: prepend APP_URL
    $cleanPath = ltrim($url, '/');
    return $baseUrl . '/' . $cleanPath;
}

/**
 * Fail-Proof Base64 Image Saver
 * Saves uploaded Base64 images to BOTH persistent storage (outside public_html) and public uploads folder
 */
function saveUploadedBase64Image($photoData, $page_id, $filePrefix = 'photo') {
    if (empty($photoData)) return '';
    $photoData = trim($photoData);

    // If Base64 string -> Decode and save to disk
    if (strpos($photoData, 'data:image') === 0) {
        preg_match('/data:image\/(.*?);base64,(.*)/', $photoData, $matches);
        $rawExt = strtolower($matches[1] ?? 'webp');
        if ($rawExt === 'jpeg') $rawExt = 'jpg';
        
        $allowedExts = ['webp', 'jpg', 'png', 'gif'];
        $ext = in_array($rawExt, $allowedExts) ? $rawExt : 'webp';

        $imageData = base64_decode($matches[2] ?? '');
        if (empty($imageData)) return $photoData;

        // 1. Target Public Directory
        $baseUploadDir = __DIR__ . '/../uploads';
        $publicTargetDir = $baseUploadDir . '/' . $page_id;
        if (!is_dir($publicTargetDir)) {
            @mkdir($publicTargetDir, 0777, true);
            @chmod($publicTargetDir, 0777);
        }

        // 2. Target Persistent Backup Directory (outside public_html - immune to git wipes)
        $persistentTargetDir = getPersistentUploadsDir() . '/' . $page_id;
        if (!is_dir($persistentTargetDir)) {
            @mkdir($persistentTargetDir, 0777, true);
            @chmod($persistentTargetDir, 0777);
        }

        // Clean Market-Standard Prefix & Hash
        $cleanPrefix = 'media';
        if (strpos($filePrefix, 'avatar') !== false || strpos($filePrefix, 'partner') !== false) {
            $cleanPrefix = 'avatar';
        }
        $shortHash = substr(md5(uniqid((string)rand(), true)), 0, 8);
        $fileName = $cleanPrefix . '_' . $shortHash . '.' . $ext;

        $publicDiskPath = $publicTargetDir . '/' . $fileName;
        $persistentDiskPath = $persistentTargetDir . '/' . $fileName;

        // Write to persistent directory FIRST
        @file_put_contents($persistentDiskPath, $imageData);
        @chmod($persistentDiskPath, 0666);

        // Write to public web directory SECOND
        $bytesWritten = @file_put_contents($publicDiskPath, $imageData);
        @chmod($publicDiskPath, 0666);

        if ($bytesWritten !== false && $bytesWritten > 0) {
            return rtrim(APP_URL, '/') . '/uploads/' . $page_id . '/' . $fileName;
        } else {
            return $photoData; // Fallback
        }
    }

    return resolveMediaUrl($photoData);
}

