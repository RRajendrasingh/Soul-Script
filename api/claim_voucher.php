<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../includes/voucher_helper.php';

$raw = file_get_contents('php://input');
$input = json_decode($raw, true) ?: [];

$orderId = trim($_GET['order_id'] ?? $input['order_id'] ?? '');
$pageId  = trim($_GET['page_id'] ?? $input['page_id'] ?? '');
$action  = trim($_GET['action'] ?? $input['action'] ?? 'status');

if (empty($orderId) && empty($pageId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameter: order_id or page_id'
    ]);
    exit;
}

$status = getRakhiVoucherUnlockStatus($orderId, $pageId);

if ($action === 'claim' && $status['unlocked']) {
    try {
        $db = getDB();
        if (!empty($orderId)) {
            $upd = $db->prepare("UPDATE rakhi_voucher_allocations SET is_claimed = 1, claimed_at = NOW() WHERE order_id = ? AND is_claimed = 0");
            $upd->execute([$orderId]);
        } elseif (!empty($pageId)) {
            $upd = $db->prepare("UPDATE rakhi_voucher_allocations SET is_claimed = 1, claimed_at = NOW() WHERE page_id = ? AND is_claimed = 0");
            $upd->execute([$pageId]);
        }
        $status['is_claimed'] = 1;
    } catch (Exception $e) {}
}

$affiliateProducts = getAffiliateProducts();

echo json_encode([
    'success' => true,
    'voucher_status' => $status,
    'affiliate_products' => $affiliateProducts
]);
