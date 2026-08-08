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

    // 2. Extract /uploads/ relative path from ANY previous URL domain/host
    $pos = strpos($url, '/uploads/');
    if ($pos !== false) {
        return APP_URL . substr($url, $pos);
    }
    if (strpos($url, 'uploads/') === 0) {
        return APP_URL . '/' . $url;
    }

    // 3. Absolute HTTP/HTTPS URLs (external Unsplash, etc.)
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }

    // Default: prepend APP_URL and clean leading slashes
    return APP_URL . '/' . ltrim($url, '/');
}
