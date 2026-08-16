<?php
// ─────────────────────────────────────────────────────────────────────────────
// SoulScript — Application Configuration
// All environment-specific credentials (DB, APP_URL, Razorpay, Admin) are
// defined in config/config.env.php which is NOT committed to Git.
// ─────────────────────────────────────────────────────────────────────────────

if (!headers_sent()) {
    header("X-Robots-Tag: noindex, nofollow", true);
}

define('APP_NAME', 'SoulScript');
define('APP_TAGLINE', 'Personalized Surprise Reveal Websites');

// Load environment-specific credentials.
// config.env.php must define: APP_URL, DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
// and optionally: RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET, RAZORPAY_WEBHOOK_SECRET, ADMIN_USER, ADMIN_PASS
if (file_exists(__DIR__ . '/config.env.php')) {
    require_once __DIR__ . '/config.env.php';
}

// ── App URL ───────────────────────────────────────────────────────────────────
// Should always be set in config.env.php (e.g. https://digitalyogi24.com).
// The fallback below auto-detects from the request host as a last resort only.
if (!defined('APP_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    define('APP_URL', $protocol . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
}

// ── Database ──────────────────────────────────────────────────────────────────
// All values come from config.env.php. Defaults auto-detect Local vs Production if config.env.php is missing.
$isLocalHost = php_sapi_name() === 'cli' || (isset($_SERVER['HTTP_HOST']) && (
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false
));

if (!defined('DB_HOST')) define('DB_HOST', $isLocalHost ? '127.0.0.1' : 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', $isLocalHost ? '3307' : '3306');
if (!defined('DB_NAME')) define('DB_NAME', $isLocalHost ? 'soulscript_db' : 'u810420317_SoulScript');
if (!defined('DB_USER')) define('DB_USER', $isLocalHost ? 'root' : 'u810420317_soulscript');
if (!defined('DB_PASS')) define('DB_PASS', $isLocalHost ? '' : 'Soulscript@#@32');

// ── Razorpay ──────────────────────────────────────────────────────────────────
// Currently test-mode keys. Update config.env.php to switch to live keys later.
if (!defined('RAZORPAY_KEY_ID'))         define('RAZORPAY_KEY_ID', 'rzp_test_soulscript_key');
if (!defined('RAZORPAY_KEY_SECRET'))     define('RAZORPAY_KEY_SECRET', 'rzp_test_secret_key');
if (!defined('RAZORPAY_WEBHOOK_SECRET')) define('RAZORPAY_WEBHOOK_SECRET', 'whsec_soulscript_secret');

// ── Admin ─────────────────────────────────────────────────────────────────────
if (!defined('ADMIN_USER')) define('ADMIN_USER', 'admin');
if (!defined('ADMIN_PASS')) define('ADMIN_PASS', 'soulscript123');

// ── Storage ───────────────────────────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', APP_URL . '/uploads');

// ── Security ──────────────────────────────────────────────────────────────────
define('HASH_SALT', 'soulscript_secure_salt_2026');

/**
 * Normalizes image & media URLs so legacy localhost / relative paths
 * are always rewritten to the current APP_URL on any server environment.
 */
function normalizeMediaUrl($url) {
    if (empty($url)) return '';
    $url = trim($url);

    if (strpos($url, 'data:image') === 0) {
        return $url;
    }

    $pos = strpos($url, '/uploads/');
    if ($pos !== false) {
        return APP_URL . substr($url, $pos);
    }
    if (strpos($url, 'uploads/') === 0) {
        return APP_URL . '/' . $url;
    }

    return $url;
}
