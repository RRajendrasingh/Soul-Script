<?php
// ─────────────────────────────────────────────────────────────────────────────
// GiftReveal — Application Configuration
// All environment-specific credentials (DB, APP_URL, Razorpay, Admin) are
// defined in config/config.env.php which is NOT committed to Git.
// ─────────────────────────────────────────────────────────────────────────────

if (!headers_sent()) {
    header("X-Robots-Tag: noindex, nofollow", true);
}

// Ensure Indian Standard Time (IST) globally across all timestamps
date_default_timezone_set('Asia/Kolkata');

if (!defined('APP_NAME')) define('APP_NAME', 'GiftReveal');
if (!defined('APP_TAGLINE')) define('APP_TAGLINE', 'Personalized Surprise Reveal Websites');

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
if (!defined('RAZORPAY_KEY_ID'))         define('RAZORPAY_KEY_ID', 'rzp_test_TSO6FpIhNqiwSy');
if (!defined('RAZORPAY_KEY_SECRET'))     define('RAZORPAY_KEY_SECRET', 'E8uEAHS3yi7gi1Zlviiw0qMp');
if (!defined('RAZORPAY_WEBHOOK_SECRET')) define('RAZORPAY_WEBHOOK_SECRET', 'whsec_soulscript_secret');

/**
 * Returns active Razorpay Key ID and Key Secret, safely prioritizing the MySQL Database
 * settings table, persistent backups, and overriding any obsolete server constants.
 */
function getEffectiveRazorpayCredentials() {
    // 1. Check MySQL Database System Settings Table (Top Priority - Never wiped by Git)
    if (function_exists('getSystemSetting')) {
        $dbKeyId = getSystemSetting('razorpay_key_id');
        $dbKeySecret = getSystemSetting('razorpay_key_secret');
        if (!empty($dbKeyId) && !empty($dbKeySecret) && strpos($dbKeyId, 'rzp_') === 0) {
            return [trim($dbKeyId), trim($dbKeySecret)];
        }
    }

    // 2. Check Persistent Storage Backup Outside public_html
    $domainRoot = dirname(__DIR__, 2);
    $persistentEnvPath = $domainRoot . '/config_persistent/config.env.php';
    if (!file_exists($persistentEnvPath)) {
        $persistentEnvPath = '/home/u810420317/domains/digitalyogi24.com/config_persistent/config.env.php';
    }
    if (file_exists($persistentEnvPath)) {
        $envContent = @file_get_contents($persistentEnvPath);
        if ($envContent) {
            if (preg_match("/define\('RAZORPAY_KEY_ID',\s*'([^']+)'\)/", $envContent, $mKey) &&
                preg_match("/define\('RAZORPAY_KEY_SECRET',\s*'([^']+)'\)/", $envContent, $mSec)) {
                if (!empty($mKey[1]) && !empty($mSec[1]) && strpos($mKey[1], 'rzp_') === 0) {
                    return [trim($mKey[1]), trim($mSec[1])];
                }
            }
        }
    }

    // 3. Fallback to Active Constants
    $keyId = defined('RAZORPAY_KEY_ID') && RAZORPAY_KEY_ID !== 'rzp_test_TRy0uKsxMEi8qc' ? RAZORPAY_KEY_ID : 'rzp_live_TSOjmYdb9XfC1N';
    $keySecret = defined('RAZORPAY_KEY_SECRET') && RAZORPAY_KEY_SECRET !== 'vwPpfvspIVU2umCjUkqox947' ? RAZORPAY_KEY_SECRET : '2wzCZ0Xq6i95fX0FjIqX1x8p';
    return [trim($keyId), trim($keySecret)];
}

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
    if (function_exists('resolveMediaUrl')) {
        return resolveMediaUrl($url);
    }
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
