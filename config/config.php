<?php
// SoulScript Configuration File
if (!headers_sent()) {
    header("X-Robots-Tag: noindex, nofollow", true);
}

define('APP_NAME', 'SoulScript');
define('APP_TAGLINE', 'Personalized Surprise Reveal Websites');
// Environment Auto-Detection (Local vs Hostinger Live Server)
if (!defined('APP_URL')) {
    if (getenv('APP_URL')) {
        define('APP_URL', getenv('APP_URL'));
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if ($host === 'localhost' || $host === '127.0.0.1') {
            define('APP_URL', $protocol . $host . '/soulscript');
        } else {
            define('APP_URL', $protocol . $host);
        }
    }
}

// Load custom server environment overrides if present (e.g., config.env.php on Hostinger)
if (file_exists(__DIR__ . '/config.env.php')) {
    require_once __DIR__ . '/config.env.php';
}

$isLocal = true;
if (isset($_SERVER['HTTP_HOST'])) {
    $host = strtolower(explode(':', $_SERVER['HTTP_HOST'])[0]);
    if ($host !== 'localhost' && $host !== '127.0.0.1') {
        $isLocal = false;
    }
}

// Database Credentials (Auto-switch between Local XAMPP and Hostinger Production)
define('DB_HOST', defined('PROD_DB_HOST') && !$isLocal ? PROD_DB_HOST : (getenv('DB_HOST') ?: '127.0.0.1'));
define('DB_PORT', defined('PROD_DB_PORT') && !$isLocal ? PROD_DB_PORT : (getenv('DB_PORT') ?: '3307'));
define('DB_NAME', defined('PROD_DB_NAME') && !$isLocal ? PROD_DB_NAME : (getenv('DB_NAME') ?: 'soulscript_db'));
define('DB_USER', defined('PROD_DB_USER') && !$isLocal ? PROD_DB_USER : (getenv('DB_USER') ?: 'root'));
define('DB_PASS', defined('PROD_DB_PASS') && !$isLocal ? PROD_DB_PASS : (getenv('DB_PASS') ?: ''));

// Razorpay Credentials
define('RAZORPAY_KEY_ID', getenv('RAZORPAY_KEY_ID') ?: 'rzp_test_soulscript_key');
define('RAZORPAY_KEY_SECRET', getenv('RAZORPAY_KEY_SECRET') ?: 'rzp_test_secret_key');
define('RAZORPAY_WEBHOOK_SECRET', getenv('RAZORPAY_WEBHOOK_SECRET') ?: 'whsec_soulscript_secret');

// Admin Credentials
define('ADMIN_USER', getenv('ADMIN_USER') ?: 'admin');
define('ADMIN_PASS', getenv('ADMIN_PASS') ?: 'soulscript123'); // Change in production

// Storage
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', APP_URL . '/uploads');

// Security & Salt
define('HASH_SALT', 'soulscript_secure_salt_2026');

/**
 * Normalizes image & media URLs so localhost/127.0.0.1 or relative paths automatically convert to current APP_URL
 */
function normalizeMediaUrl($url) {
    if (empty($url)) return '';
    $url = trim($url);

    // If it's a relative path starting with /uploads/ or uploads/
    if (strpos($url, 'uploads/') === 0) {
        return APP_URL . '/' . $url;
    }
    if (strpos($url, '/uploads/') === 0) {
        return APP_URL . $url;
    }

    // Replace any legacy domain or localhost/127.0.0.1 URL containing /uploads/ with current APP_URL
    if (preg_match('#https?://[^/]+(?:/soulscript)?(/uploads/.*)$#i', $url, $matches)) {
        return APP_URL . $matches[1];
    }

    return $url;
}
