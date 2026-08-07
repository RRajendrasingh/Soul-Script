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
    $host = strtolower($_SERVER['HTTP_HOST']);
    if ($host !== 'localhost' && $host !== '127.0.0.1' && strpos($host, 'localhost:') !== 0) {
        $isLocal = false;
    }
}

// Database Credentials (Auto-switch between Local XAMPP and Hostinger Production)
define('DB_HOST', defined('PROD_DB_HOST') && !$isLocal ? PROD_DB_HOST : (getenv('DB_HOST') ?: '127.0.0.1'));
define('DB_PORT', defined('PROD_DB_PORT') && !$isLocal ? PROD_DB_PORT : (getenv('DB_PORT') ?: '3306'));
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
