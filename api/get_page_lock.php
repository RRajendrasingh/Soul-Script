<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$slug = trim($_GET['slug'] ?? '');
$userIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

if (!$slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Slug is required']);
    exit;
}

try {
    $db = getDB();

    // Fetch page & lock screen metadata ONLY
    $stmt = $db->prepare("
        SELECT p.page_id, p.template_id, p.url_slug, p.status, p.expires_at, c.*
        FROM pages p
        JOIN page_content c ON p.page_id = c.page_id
        WHERE LOWER(p.url_slug) = LOWER(?)
    ");
    $stmt->execute([$slug]);
    $page = $stmt->fetch();

    if (!$page) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Page not found or link is invalid']);
        exit;
    }

    if ($page['status'] === 'expired' || strtotime($page['expires_at']) < time()) {
        http_response_code(410);
        echo json_encode(['success' => false, 'message' => 'This surprise page has expired.']);
        exit;
    }

    // Check failed attempts rate limiter
    $stmtLock = $db->prepare("SELECT attempts_count, locked_until FROM failed_attempts WHERE slug = ? AND ip_address = ?");
    $stmtLock->execute([$slug, $userIp]);
    $lockInfo = $stmtLock->fetch();

    $isLocked = false;
    $remainingSeconds = 0;

    if ($lockInfo && !empty($lockInfo['locked_until'])) {
        $lockedUntilTime = strtotime($lockInfo['locked_until']);
        if ($lockedUntilTime > time()) {
            $isLocked = true;
            $remainingSeconds = $lockedUntilTime - time();
        }
    }

    // STRICT PRIVACY PROTECTION: RETURN NO SENSITIVE DATA
    echo json_encode([
        'success' => true,
        'page_id' => $page['page_id'],
        'url_slug' => $page['url_slug'],
        'template_id' => $page['template_id'],
        'partner_name' => htmlspecialchars_decode($page['partner_name'] ?? '', ENT_QUOTES),
        'buyer_name' => htmlspecialchars_decode($page['buyer_name'] ?? '', ENT_QUOTES),
        'hint_question' => htmlspecialchars_decode($page['hint_question'] ?? '', ENT_QUOTES),
        'receiver_photo' => $page['receiver_photo'] ?? '',
        'status' => $page['status'],
        'expires_at' => $page['expires_at'],
        'is_locked' => $isLocked,
        'locked_until_seconds' => $remainingSeconds
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
