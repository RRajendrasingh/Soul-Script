<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');

$input = json_decode($rawInput, true) ?? [];
$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide both Email and Secret Password.']);
    exit;
}

try {
    $db = getDB();
    $passHash = hashHintAnswer($password); // SHA-256 with salt

    // 1. Fetch ALL created pages for this buyer
    $stmt = $db->prepare("
        SELECT p.edit_token, p.page_id, p.template_id, p.url_slug, c.partner_name, p.created_at, o.buyer_name, o.buyer_email, o.order_id
        FROM orders o
        JOIN pages p ON o.order_id = p.order_id
        LEFT JOIN page_content c ON p.page_id = c.page_id
        WHERE LOWER(o.buyer_email) = LOWER(?) AND o.buyer_password_hash = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$email, $passHash]);
    $pages = $stmt->fetchAll();

    if ($pages && count($pages) > 0) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $firstPage = $pages[0];
        $_SESSION['edit_token'] = $firstPage['edit_token'];
        $_SESSION['buyer_email'] = $firstPage['buyer_email'];

        $pagesList = array_map(function($p) {
            return [
                'edit_token' => $p['edit_token'],
                'page_id' => $p['page_id'],
                'template_id' => $p['template_id'],
                'url_slug' => $p['url_slug'],
                'partner_name' => htmlspecialchars_decode($p['partner_name'] ?? 'Partner', ENT_QUOTES),
                'created_at' => $p['created_at']
            ];
        }, $pages);

        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'edit_token' => $firstPage['edit_token'],
            'page_id' => $firstPage['page_id'],
            'url_slug' => $firstPage['url_slug'],
            'buyer_name' => $firstPage['buyer_name'],
            'pages' => $pagesList
        ]);
        exit;
    }

    // 2. Check if valid order exists without a page created yet
    $stmtOrder = $db->prepare("
        SELECT o.order_id, o.buyer_name, o.buyer_email
        FROM orders o
        WHERE LOWER(o.buyer_email) = LOWER(?) AND o.buyer_password_hash = ?
        ORDER BY o.created_at DESC
        LIMIT 1
    ");
    $stmtOrder->execute([$email, $passHash]);
    $orderResult = $stmtOrder->fetch();

    if ($orderResult) {
        echo json_encode([
            'success' => true,
            'message' => 'Order verified! Please complete your gift creation.',
            'redirect_url' => APP_URL . '/create.php?order_id=' . $orderResult['order_id'],
            'buyer_name' => $orderResult['buyer_name']
        ]);
        exit;
    }

    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid Email or Secret Edit Password. Please try again.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
