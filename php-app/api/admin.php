<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Authentication Check: Verify session login or valid admin key
$adminKey = $_SERVER['HTTP_X_ADMIN_KEY'] ?? $_GET['admin_key'] ?? $input['admin_key'] ?? $_POST['admin_key'] ?? '';
$isSessionAuth = !empty($_SESSION['admin_logged_in']);
$isKeyAuth = (defined('ADMIN_PASS') && !empty($adminKey) && $adminKey === ADMIN_PASS);

if (!$isSessionAuth && !$isKeyAuth) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access: Admin session or valid admin key required']);
    exit;
}

$action = $_GET['action'] ?? $input['action'] ?? $_POST['action'] ?? 'list';

try {
    $db = getDB();

    if ($action === 'list') {
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $sql = "
            SELECT o.*, t.name as template_name, p.page_id, p.url_slug, p.edit_token, p.status as page_status, p.expires_at,
                   pr.response as proposal_response, pr.partner_note as proposal_note, pr.responded_at as proposal_responded_at
            FROM orders o
            LEFT JOIN templates t ON o.template_id = t.template_id
            LEFT JOIN pages p ON o.order_id = p.order_id
            LEFT JOIN proposal_responses pr ON p.page_id = pr.page_id
            WHERE 1=1
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (o.buyer_name LIKE ? OR o.buyer_email LIKE ? OR p.url_slug LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($status !== '') {
            if ($status === 'paid' || $status === 'pending' || $status === 'failed') {
                $sql .= " AND o.payment_status = ?";
                $params[] = $status;
            } elseif ($status === 'live' || $status === 'expired') {
                $sql .= " AND p.status = ?";
                $params[] = $status;
            }
        }

        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        // Calculate Stats
        $statsStmt = $db->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN amount_paid ELSE 0 END) as total_revenue
            FROM orders
        ");
        $stats = $statsStmt->fetch();

        $livePagesStmt = $db->query("SELECT COUNT(*) FROM pages WHERE status = 'live'");
        $stats['live_pages'] = (int)$livePagesStmt->fetchColumn();

        echo json_encode([
            'success' => true,
            'orders' => $orders,
            'stats' => $stats,
            'app_url' => APP_URL
        ]);
        exit;
    }

    if ($action === 'expire_page') {
        $page_id = $input['page_id'] ?? $_POST['page_id'] ?? '';
        $stmt = $db->prepare("UPDATE pages SET status = 'expired' WHERE page_id = ?");
        $stmt->execute([$page_id]);
        echo json_encode(['success' => true, 'message' => 'Page marked as expired']);
        exit;
    }

    if ($action === 'delete_page') {
        $page_id = $input['page_id'] ?? $_POST['page_id'] ?? '';
        $stmt = $db->prepare("DELETE FROM pages WHERE page_id = ?");
        $stmt->execute([$page_id]);
        echo json_encode(['success' => true, 'message' => 'Page deleted permanently']);
        exit;
    }

    if ($action === 'delete_order') {
        $order_id = $input['order_id'] ?? $_POST['order_id'] ?? '';
        
        // Delete pages associated with this order
        $stmtPage = $db->prepare("DELETE FROM pages WHERE order_id = ?");
        $stmtPage->execute([$order_id]);
        
        // Delete order record
        $stmtOrder = $db->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmtOrder->execute([$order_id]);
        
        echo json_encode(['success' => true, 'message' => 'Order entry deleted permanently']);
        exit;
    }

    if ($action === 'simulate_payment') {
        $order_id = $input['order_id'] ?? $_POST['order_id'] ?? '';
        $stmt = $db->prepare("UPDATE orders SET payment_status = 'paid', razorpay_payment_id = ? WHERE order_id = ?");
        $stmt->execute(['sim_pay_' . time(), $order_id]);
        echo json_encode(['success' => true, 'message' => 'Payment status updated to paid (Simulation)']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
