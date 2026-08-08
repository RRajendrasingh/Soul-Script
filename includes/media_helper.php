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

    // 2. Relative upload paths -> attach APP_URL
    if (strpos($url, 'uploads/') === 0) {
        return APP_URL . '/' . $url;
    }
    if (strpos($url, '/uploads/') === 0) {
        return APP_URL . $url;
    }

    // 3. Rewrite legacy localhost / 127.0.0.1 origins to current production APP_URL
    if (preg_match('#https?://(?:localhost|127\.0\.0\.1)(?::\d+)?(?:/[^/]*)?(/uploads/.*)$#i', $url, $m)) {
        return APP_URL . $m[1];
    }

    // 4. Absolute HTTP/HTTPS URLs
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }

    // Default: prepend APP_URL and clean leading slashes
    return APP_URL . '/' . ltrim($url, '/');
}
