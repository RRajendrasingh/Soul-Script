<?php
/**
 * GiftReveal - Automated Email Receipt Helper
 * Generates and sends branded HTML invoices & access receipts to buyers.
 */

if (!defined('APP_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

/**
 * Sends a branded HTML payment receipt email to the buyer after order completion.
 *
 * @param array $data Order & page data
 * @return bool True if mail dispatch attempted
 */
function sendOrderReceiptEmail($data) {
    $appName       = defined('APP_NAME') ? APP_NAME : 'GiftReveal';
    $domainHost    = parse_url(APP_URL, PHP_URL_HOST) ?? 'giftreveal.in';
    $buyer_name    = htmlspecialchars($data['buyer_name'] ?? 'Valued Customer');
    $buyer_email   = trim($data['buyer_email'] ?? '');
    $order_id      = htmlspecialchars($data['order_id'] ?? 'ORD-' . time());
    $template_name = htmlspecialchars($data['template_name'] ?? 'Romantic Surprise Website');
    $amount_paid   = htmlspecialchars($data['amount_paid'] ?? '299');
    $url_slug      = htmlspecialchars($data['url_slug'] ?? '');
    $edit_url      = APP_URL . '/edit.php';
    $live_url      = APP_URL . '/gift/' . $url_slug;
    $date_str      = date('j M Y, h:i A');

    if (empty($buyer_email) || !filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $subject = "🎉 Payment Receipt & Access Link for your Surprise Website - " . $appName;

    // Premium HTML Email Template
    $htmlMessage = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Payment Receipt - ' . $appName . '</title>
      <style>
        body { margin: 0; padding: 0; background-color: #100d10; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #e8e0e3; }
        .container { max-width: 600px; margin: 0 auto; background-color: #221f21; border-radius: 20px; overflow: hidden; border: 1px solid #4d444b; }
        .header { background: linear-gradient(135deg, #3b1e3b 0%, #151215 100%); padding: 30px 20px; text-align: center; border-b: 1px solid #eac34a; }
        .logo-title { color: #eac34a; font-size: 26px; font-weight: bold; font-family: Georgia, serif; margin: 0; letter-spacing: 1px; }
        .subtitle { color: #d0c3cb; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px; }
        .content { padding: 30px 25px; }
        .greeting { font-size: 18px; font-weight: bold; color: #ffffff; margin-bottom: 10px; }
        .intro { font-size: 14px; color: #d0c3cb; line-height: 1.6; margin-bottom: 25px; }
        .badge { display: inline-block; background-color: #3b1e3b; color: #eac34a; font-size: 11px; font-weight: bold; padding: 6px 14px; border-radius: 50px; border: 1px solid rgba(234,195,74,0.4); text-transform: uppercase; tracking: 1px; margin-bottom: 20px; }
        .receipt-card { background-color: #151215; border-radius: 14px; border: 1px solid #4d444b; padding: 20px; margin-bottom: 25px; }
        .receipt-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(77,68,75,0.4); font-size: 13px; }
        .receipt-row:last-child { border-bottom: none; font-weight: bold; font-size: 14px; color: #eac34a; }
        .label { color: #d0c3cb; }
        .value { color: #ffffff; text-align: right; }
        .btn-container { text-align: center; margin: 30px 0; }
        .btn-primary { display: inline-block; background-color: #eac34a; color: #241a00; font-weight: bold; font-size: 14px; text-decoration: none; padding: 14px 28px; border-radius: 12px; text-transform: uppercase; letter-spacing: 1px; margin: 6px; box-shadow: 0 4px 15px rgba(234,195,74,0.3); }
        .btn-secondary { display: inline-block; background-color: #151215; color: #e8e0e3; font-weight: bold; font-size: 14px; text-decoration: none; padding: 14px 28px; border-radius: 12px; border: 1px solid #4d444b; text-transform: uppercase; letter-spacing: 1px; margin: 6px; }
        .info-box { background-color: rgba(59,30,59,0.5); border-left: 4px solid #eac34a; padding: 15px; border-radius: 8px; font-size: 12px; color: #d0c3cb; line-height: 1.5; margin-bottom: 25px; }
        .footer { background-color: #151215; padding: 20px; text-align: center; font-size: 12px; color: #8a7b85; border-t: 1px solid #4d444b; }
        .footer a { color: #eac34a; text-decoration: none; }
      </style>
    </head>
    <body>
      <div style="padding: 20px 10px;">
        <div class="container">
          <!-- Header -->
          <div class="header">
            <h1 class="logo-title">' . $appName . '</h1>
            <div class="subtitle">Personalized Surprise Websites</div>
          </div>

          <!-- Main Content -->
          <div class="content">
            <div class="badge">✅ Payment Confirmed & Receipt</div>
            <div class="greeting">Dear ' . $buyer_name . ',</div>
            <div class="intro">
              Thank you for choosing <strong>' . $appName . '</strong>! Your payment of <strong>₹' . $amount_paid . '</strong> was successful, and your surprise website has been generated.
            </div>

            <!-- Receipt Details -->
            <div class="receipt-card">
              <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 13px; color: #e8e0e3;">
                <tr>
                  <td style="padding: 8px 0; color: #d0c3cb; border-bottom: 1px solid #4d444b;">Order ID:</td>
                  <td style="padding: 8px 0; text-align: right; font-family: monospace; font-weight: bold; border-bottom: 1px solid #4d444b;">' . $order_id . '</td>
                </tr>
                <tr>
                  <td style="padding: 8px 0; color: #d0c3cb; border-bottom: 1px solid #4d444b;">Date & Time:</td>
                  <td style="padding: 8px 0; text-align: right; border-bottom: 1px solid #4d444b;">' . $date_str . '</td>
                </tr>
                <tr>
                  <td style="padding: 8px 0; color: #d0c3cb; border-bottom: 1px solid #4d444b;">Selected Plan:</td>
                  <td style="padding: 8px 0; text-align: right; border-bottom: 1px solid #4d444b;">' . $template_name . '</td>
                </tr>
                <tr>
                  <td style="padding: 8px 0; color: #d0c3cb; border-bottom: 1px solid #4d444b;">Registered Email:</td>
                  <td style="padding: 8px 0; text-align: right; border-bottom: 1px solid #4d444b;">' . $buyer_email . '</td>
                </tr>
                <tr>
                  <td style="padding: 10px 0 0 0; font-weight: bold; color: #eac34a; font-size: 15px;">Total Paid:</td>
                  <td style="padding: 10px 0 0 0; text-align: right; font-weight: bold; color: #eac34a; font-size: 15px;">₹' . $amount_paid . '</td>
                </tr>
              </table>
            </div>

            <!-- Action Buttons -->
            <div class="btn-container">
              <a href="' . $edit_url . '" class="btn-primary" target="_blank">✏️ Manage & Edit Dashboard</a>
              <a href="' . $live_url . '" class="btn-secondary" target="_blank">🔗 View Live Surprise Page</a>
            </div>

            <!-- Buyer Account Notice -->
            <div class="info-box">
              🔑 <strong>How to manage your gift anytime:</strong><br>
              Visit <a href="' . $edit_url . '" style="color: #eac34a; text-decoration: underline;">' . $domainHost . '/edit.php</a> and log in using your registered email (<code>' . $buyer_email . '</code>) and the account password you set during checkout.
            </div>
          </div>

          <!-- Footer -->
          <div class="footer">
            <p>Made with Endless Love by ' . $appName . ' • <a href="' . APP_URL . '">' . $domainHost . '</a></p>
            <p style="font-size: 11px; margin-top: 5px;">Need help? Reply directly to this email or contact support at support@' . $domainHost . '.</p>
          </div>
        </div>
      </div>
    </body>
    </html>
    ';

    // Headers
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $appName . ' <no-reply@' . $domainHost . '>',
        'Reply-To: support@' . $domainHost,
        'X-Mailer: PHP/' . phpversion()
    ];

    // Non-blocking mail attempt
    try {
        return @mail($buyer_email, $subject, $htmlMessage, implode("\r\n", $headers));
    } catch (Exception $e) {
        error_log("GiftReveal Mail Error: " . $e->getMessage());
        return false;
    }
}
