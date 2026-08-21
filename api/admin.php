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

    if ($action === 'list' || $action === 'export_csv') {
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $dateRange = trim($_GET['date_range'] ?? 'all');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limitRaw = trim($_GET['limit'] ?? '50');
        
        $limit = ($limitRaw === 'all' || $limitRaw === '0') ? 0 : max(1, (int)$limitRaw);

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
            $sql .= " AND (o.order_id LIKE ? OR o.buyer_name LIKE ? OR o.buyer_email LIKE ? OR o.buyer_phone LIKE ? OR p.url_slug LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
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

        if ($dateRange === 'today') {
            $sql .= " AND DATE(o.created_at) = CURDATE()";
        } elseif ($dateRange === '7days') {
            $sql .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($dateRange === '30days') {
            $sql .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }

        // Count Total Matching Records for Pagination
        $countSql = "SELECT COUNT(*) FROM (" . $sql . ") as filtered_count";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = (int)$countStmt->fetchColumn();

        $sql .= " ORDER BY o.created_at DESC";

        // If Exporting CSV
        if ($action === 'export_csv') {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $allMatchingOrders = $stmt->fetchAll();

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="soulscript_orders_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['Order ID', 'Date & Time', 'Buyer Name', 'Email', 'Phone', 'Template', 'Amount Paid', 'Payment Status', 'Secret URL', 'Proposal Response', 'Partner Note']);

            foreach ($allMatchingOrders as $ord) {
                $secretUrl = !empty($ord['url_slug']) ? APP_URL . '/gift/' . $ord['url_slug'] : 'Not generated';
                fputcsv($output, [
                    $ord['order_id'],
                    $ord['created_at'],
                    $ord['buyer_name'],
                    $ord['buyer_email'],
                    $ord['buyer_phone'],
                    $ord['template_name'] ?: $ord['template_id'],
                    $ord['amount_paid'],
                    strtoupper($ord['payment_status']),
                    $secretUrl,
                    $ord['proposal_response'] ?: 'No response yet',
                    $ord['proposal_note'] ?: ''
                ]);
            }
            fclose($output);
            exit;
        }

        // Apply Pagination SQL Limit
        if ($limit > 0) {
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        $totalPages = $limit > 0 ? (int)ceil($totalRecords / $limit) : 1;

        // Calculate Global Stats
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
            'pagination' => [
                'total_records' => $totalRecords,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => $totalPages
            ],
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

    if ($action === 'get_payment_settings') {
        $mode = getSystemSetting('razorpay_mode', 'live');
        [$effKeyId, $effKeySecret] = getEffectiveRazorpayCredentials();
        $whSecret = getSystemSetting('razorpay_webhook_secret', defined('RAZORPAY_WEBHOOK_SECRET') ? RAZORPAY_WEBHOOK_SECRET : 'whsec_soulscript_secret');
        
        $stmtUpdated = $db->query("SELECT updated_at FROM system_settings WHERE setting_key = 'razorpay_key_id' LIMIT 1");
        $lastUpdated = $stmtUpdated->fetchColumn() ?: date('Y-m-d H:i:s');

        echo json_encode([
            'success' => true,
            'settings' => [
                'razorpay_mode' => $mode,
                'razorpay_key_id' => $effKeyId,
                'razorpay_key_secret' => $effKeySecret,
                'razorpay_webhook_secret' => $whSecret,
                'last_updated' => $lastUpdated
            ]
        ]);
        exit;
    }

    if ($action === 'save_payment_settings') {
        $mode = trim($input['razorpay_mode'] ?? 'live');
        $keyId = trim($input['razorpay_key_id'] ?? '');
        $keySecret = trim($input['razorpay_key_secret'] ?? '');
        $whSecret = trim($input['razorpay_webhook_secret'] ?? '');

        if (empty($keyId) || empty($keySecret)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Both Razorpay Key ID and Key Secret are required']);
            exit;
        }

        if (strpos($keyId, 'rzp_') !== 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid Razorpay Key ID format. Must start with rzp_live_ or rzp_test_']);
            exit;
        }

        // Persist to MySQL Database (Zero risk of Git wipe)
        setSystemSetting('razorpay_mode', $mode);
        setSystemSetting('razorpay_key_id', $keyId);
        setSystemSetting('razorpay_key_secret', $keySecret);
        if (!empty($whSecret)) {
            setSystemSetting('razorpay_webhook_secret', $whSecret);
        }

        // Also sync persistent outside-webroot backup if directory exists or can be created
        $persistentDir = '/home/u810420317/domains/digitalyogi24.com/config_persistent';
        if (!is_dir($persistentDir)) @mkdir($persistentDir, 0777, true);
        if (is_dir($persistentDir)) {
            $envData = "<?php\n" .
                "define('RAZORPAY_KEY_ID', '" . addslashes($keyId) . "');\n" .
                "define('RAZORPAY_KEY_SECRET', '" . addslashes($keySecret) . "');\n" .
                (!empty($whSecret) ? "define('RAZORPAY_WEBHOOK_SECRET', '" . addslashes($whSecret) . "');\n" : "");
            @file_put_contents($persistentDir . '/config.env.php', $envData);
            @chmod($persistentDir . '/config.env.php', 0666);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Payment Gateway Settings successfully saved to Database & Persistent Storage!'
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
