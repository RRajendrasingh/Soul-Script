<?php
/**
 * SoulScript - Automated Forgot Password & Reset API
 * Supports 10-second password reset via Email / WhatsApp Verification.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/email_helper.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = trim($input['action'] ?? $_GET['action'] ?? 'request_reset');

try {
    $db = getDB();

    if ($action === 'request_reset') {
        $email = trim($input['email'] ?? '');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid registered email address required.']);
            exit;
        }

        // Check if buyer email exists in orders
        $stmt = $db->prepare("SELECT order_id, buyer_name, buyer_email, buyer_phone FROM orders WHERE LOWER(buyer_email) = LOWER(?) ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$email]);
        $buyer = $stmt->fetch();

        if (!$buyer) {
            http_response_code(44);
            echo json_encode(['success' => false, 'message' => 'No SoulScript account found with this email address.']);
            exit;
        }

        // Generate 15-min Reset Token
        $reset_token = bin2hex(random_bytes(16));
        $expires_at  = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Store reset token on recent order
        $db->prepare("UPDATE orders SET reset_token = ?, reset_expires_at = ? WHERE LOWER(buyer_email) = LOWER(?)")
           ->execute([$reset_token, $expires_at, strtolower($email)]);

        $reset_link = APP_URL . '/edit.php?reset_token=' . $reset_token;

        // WhatsApp Direct Help Link for 1-Click Verification
        $wa_msg  = urlencode("Hi SoulScript Support! I need help resetting my password for email: {$email}. Order ID: {$buyer['order_id']}");
        $wa_link = "https://wa.me/919999999999?text={$wa_msg}";

        // Send Email Reset Notice if mail is available
        $subject = "🔑 Reset Your SoulScript Account Password";
        $htmlBody = "
            <div style='font-family: Arial, sans-serif; background: #151215; color: #e8e0e3; padding: 25px; border-radius: 15px; border: 1px solid #4d444b;'>
              <h2 style='color: #eac34a;'>SoulScript Password Reset</h2>
              <p>Hi " . htmlspecialchars($buyer['buyer_name']) . ",</p>
              <p>We received a request to reset your password for your SoulScript buyer portal account.</p>
              <p style='text-align: center; margin: 25px 0;'>
                <a href='{$reset_link}' style='background: #eac34a; color: #241a00; font-weight: bold; text-decoration: none; padding: 12px 24px; border-radius: 10px; text-transform: uppercase;'>Reset Password Now</a>
              </p>
              <p style='font-size: 12px; color: #d0c3cb;'>This link is valid for 15 minutes. If you did not request this, you can safely ignore this email.</p>
            </div>
        ";
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: SoulScript <no-reply@digitalyogi24.com>'
        ];
        @mail($email, $subject, $htmlBody, implode("\r\n", $headers));

        echo json_encode([
            'success' => true,
            'message' => 'Password reset instructions generated successfully!',
            'reset_link' => $reset_link,
            'whatsapp_link' => $wa_link
        ]);
        exit;
    }

    if ($action === 'perform_reset') {
        $reset_token  = trim($input['reset_token'] ?? '');
        $new_password = trim($input['new_password'] ?? '');

        if (!$reset_token || !$new_password || strlen($new_password) < 4) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid reset token and new password (min 4 chars) required.']);
            exit;
        }

        // Verify Token
        $stmt = $db->prepare("SELECT order_id, buyer_email FROM orders WHERE reset_token = ? AND reset_expires_at > NOW() LIMIT 1");
        $stmt->execute([$reset_token]);
        $buyer = $stmt->fetch();

        if (!$buyer) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid or expired password reset token. Please request a new reset link.']);
            exit;
        }

        // Update password hash for all orders of this buyer
        $new_hash = hashHintAnswer($new_password);
        $db->prepare("UPDATE orders SET buyer_password_hash = ?, reset_token = NULL, reset_expires_at = NULL WHERE LOWER(buyer_email) = LOWER(?)")
           ->execute([$new_hash, strtolower($buyer['buyer_email'])]);

        echo json_encode([
            'success' => true,
            'message' => 'Your account password has been reset successfully! You can now log in with your new password.'
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
