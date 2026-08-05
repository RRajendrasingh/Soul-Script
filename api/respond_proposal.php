<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

$page_id      = trim($input['page_id'] ?? '');
$response_type = trim($input['response'] ?? '');
$partner_note = trim($input['partner_note'] ?? '');

if (!$page_id || !in_array($response_type, ['yes', 'lets_talk'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid response type']);
    exit;
}

try {
    $db = getDB();

    $stmt = $db->prepare("
        SELECT p.page_id, p.url_slug, o.buyer_name, o.buyer_phone, o.buyer_email
        FROM pages p
        JOIN orders o ON p.order_id = o.order_id
        WHERE p.page_id = ?
    ");
    $stmt->execute([$page_id]);
    $pageInfo = $stmt->fetch();

    if (!$pageInfo) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Proposal page not found']);
        exit;
    }

    // Save or update response
    $stmtResp = $db->prepare("
        INSERT INTO proposal_responses (page_id, response, partner_note, responded_at)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE response = VALUES(response), partner_note = VALUES(partner_note), responded_at = NOW()
    ");
    $stmtResp->execute([$page_id, $response_type, $partner_note]);

    // Buyer Notification Log / SMS / Email Alert
    $alertText = sprintf(
        "[PROPOSAL ANSWER ALERT] Partner clicked '%s' on reveal page /gift/%s! Buyer: %s (%s). Partner Note: %s",
        strtoupper($response_type),
        $pageInfo['url_slug'],
        $pageInfo['buyer_name'],
        $pageInfo['buyer_phone'],
        $partner_note ?: 'No note'
    );
    error_log($alertText);

    echo json_encode([
        'success' => true,
        'message' => "Your response has been registered! " . htmlspecialchars($pageInfo['buyer_name']) . " has been notified.",
        'proposal_response' => [
            'page_id' => $page_id,
            'response' => $response_type,
            'partner_note' => $partner_note,
            'responded_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
