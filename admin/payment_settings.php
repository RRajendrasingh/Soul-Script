<?php
/**
 * GiftReveal - Admin Payment Gateway Settings
 * Database-Backed (MySQL system_settings) + Persistent Auto-Healing Storage
 * Prevents Git deployment config resets forever.
 */

require_once __DIR__ . '/../config/db.php';

session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$currentMode = getSystemSetting('razorpay_mode', 'live');
[$activeKeyId, $activeKeySecret] = getEffectiveRazorpayCredentials();
$webhookSecret = getSystemSetting('razorpay_webhook_secret', defined('RAZORPAY_WEBHOOK_SECRET') ? RAZORPAY_WEBHOOK_SECRET : 'whsec_soulscript_secret');

$stmtUp = $db->query("SELECT updated_at FROM system_settings WHERE setting_key = 'razorpay_key_id' LIMIT 1");
$lastUpdated = $stmtUp->fetchColumn() ?: date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $page_title = 'Payment Gateway Settings — ' . (defined('APP_NAME') ? APP_NAME : 'GiftReveal') . ' Admin';
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] min-h-screen relative overflow-x-hidden font-sans selection:bg-[#eac34a] selection:text-[#151215]">
  <!-- Ambient Luxury Background Glows -->
  <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-gradient-to-b from-[#3b1e3b]/30 via-[#221f21]/20 to-transparent blur-[120px] pointer-events-none z-0"></div>

  <?php 
  $current_page = 'admin';
  $isAdminPage = true;
  require_once __DIR__ . '/../includes/header.php'; 
  ?>

  <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-20 relative z-10 space-y-8">
    <?php require_once __DIR__ . '/nav_header.php'; ?>

    <!-- Header Banner -->
    <div class="bg-[#221f21]/80 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-[#4d444b] shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-[#eac34a] mb-1">
          <i data-lucide="shield-check" class="w-4 h-4"></i> Database-Backed Security
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold font-serif text-[#e8e0e3]">Payment Gateway Settings</h1>
        <p class="text-xs sm:text-sm text-[#d0c3cb] mt-1">Manage Razorpay API Keys with 100% protection against Git deployment resets.</p>
      </div>

      <div class="flex items-center gap-2">
        <span id="gatewayStatusBadge" class="px-4 py-2 rounded-xl text-xs font-bold shadow-lg flex items-center gap-1.5 <?php echo $currentMode === 'live' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40'; ?>">
          <span class="w-2 h-2 rounded-full <?php echo $currentMode === 'live' ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'; ?>"></span>
          <span><?php echo strtoupper($currentMode); ?> MODE ACTIVE</span>
        </span>
      </div>
    </div>

    <!-- Security Information Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-[#1b171b] border border-[#3b1e3b] rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#eac34a]/10 border border-[#eac34a]/30 flex items-center justify-center text-[#eac34a] shrink-0">
          <i data-lucide="database" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[11px] text-[#b8a7b3] uppercase tracking-wider block">Storage Mode</span>
          <strong class="text-xs text-[#e8e0e3]">MySQL system_settings</strong>
        </div>
      </div>

      <div class="bg-[#1b171b] border border-[#3b1e3b] rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
          <i data-lucide="shield-alert" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[11px] text-[#b8a7b3] uppercase tracking-wider block">Git Wipe Protection</span>
          <strong class="text-xs text-emerald-400">100% Locked &amp; Immune</strong>
        </div>
      </div>

      <div class="bg-[#1b171b] border border-[#3b1e3b] rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#e4b9df]/10 border border-[#e4b9df]/30 flex items-center justify-center text-[#e4b9df] shrink-0">
          <i data-lucide="clock" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-[11px] text-[#b8a7b3] uppercase tracking-wider block">Last Synced</span>
          <strong class="text-xs text-[#e4b9df]"><?php echo date('d M Y, h:i A', strtotime($lastUpdated)); ?></strong>
        </div>
      </div>
    </div>

    <!-- Payment Configuration Form -->
    <div class="bg-[#1b171b] border border-[#eac34a]/30 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
      <div class="flex items-center justify-between border-b border-[#3b1e3b] pb-4">
        <div>
          <h2 class="text-lg font-bold font-serif text-[#e8e0e3]">Razorpay Standard Checkout Configuration</h2>
          <p class="text-xs text-[#b8a7b3] mt-0.5">UPI (GPay, PhonePe, Paytm), Debit/Credit Cards &amp; NetBanking</p>
        </div>
        <a href="https://dashboard.razorpay.com" target="_blank" class="text-xs text-[#eac34a] hover:underline flex items-center gap-1 font-semibold">
          <span>Razorpay Dashboard</span>
          <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
        </a>
      </div>

      <form id="paymentSettingsForm" onsubmit="savePaymentSettings(event)" class="space-y-6">
        <!-- Mode Switcher -->
        <div>
          <label class="block text-xs font-semibold text-[#e8e0e3] mb-2 uppercase tracking-wider">Gateway Environment Mode *</label>
          <div class="grid grid-cols-2 gap-3 max-w-md">
            <label class="flex items-center gap-3 bg-[#151215] border border-[#4d444b] rounded-2xl p-3.5 cursor-pointer hover:border-[#eac34a] transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-950/20">
              <input type="radio" name="razorpay_mode" value="live" <?php echo $currentMode === 'live' ? 'checked' : ''; ?> class="text-emerald-500 focus:ring-emerald-400">
              <div>
                <strong class="text-xs text-[#e8e0e3] block">🟢 Live Mode</strong>
                <span class="text-[10px] text-[#b8a7b3]">Real Customer Payments</span>
              </div>
            </label>

            <label class="flex items-center gap-3 bg-[#151215] border border-[#4d444b] rounded-2xl p-3.5 cursor-pointer hover:border-[#eac34a] transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-950/20">
              <input type="radio" name="razorpay_mode" value="test" <?php echo $currentMode === 'test' ? 'checked' : ''; ?> class="text-amber-500 focus:ring-amber-400">
              <div>
                <strong class="text-xs text-[#e8e0e3] block">🟡 Test Mode</strong>
                <span class="text-[10px] text-[#b8a7b3]">Sandbox Simulation</span>
              </div>
            </label>
          </div>
        </div>

        <!-- Key ID & Key Secret -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Razorpay Key ID *</label>
            <input type="text" id="settingKeyId" required value="<?php echo htmlspecialchars($activeKeyId); ?>" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 text-xs font-mono text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="rzp_live_xxxxxxxxxxxxxx">
            <span class="text-[10px] text-[#b8a7b3] mt-1 block">Starts with <code class="text-[#eac34a]">rzp_live_</code> or <code class="text-[#eac34a]">rzp_test_</code></span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Razorpay Key Secret *</label>
            <div class="relative">
              <input type="password" id="settingKeySecret" required value="<?php echo htmlspecialchars($activeKeySecret); ?>" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl pl-3.5 pr-10 py-2.5 text-xs font-mono text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="••••••••••••••••••••••••">
              <button type="button" onclick="toggleSecretVisibility()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#b8a7b3] hover:text-[#eac34a] p-1 cursor-pointer">
                <i id="secretToggleIcon" data-lucide="eye" class="w-4 h-4"></i>
              </button>
            </div>
            <span class="text-[10px] text-[#b8a7b3] mt-1 block">Stored securely in MySQL Database &amp; encrypted sessions</span>
          </div>
        </div>

        <!-- Webhook Secret -->
        <div>
          <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Webhook Secret (Optional)</label>
          <input type="text" id="settingWebhookSecret" value="<?php echo htmlspecialchars($webhookSecret); ?>" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 text-xs font-mono text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="whsec_xxxxxxxxxxxxxx">
          <span class="text-[10px] text-[#b8a7b3] mt-1 block">Configured for Razorpay Webhook URL: <code class="text-[#eac34a]"><?php echo APP_URL; ?>/api/webhook_razorpay.php</code></span>
        </div>

        <!-- Submit & Test Button -->
        <div class="pt-4 border-t border-[#3b1e3b] flex flex-col sm:flex-row items-center justify-between gap-4">
          <div id="saveStatusNotice" class="text-xs font-semibold text-emerald-400 hidden flex items-center gap-1.5">
            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            <span id="saveStatusText">Settings saved successfully!</span>
          </div>

          <button type="submit" id="saveSettingsBtn" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#eac34a] text-[#241a00] font-bold text-xs hover:bg-[#ffe088] transition-all shadow-lg flex items-center justify-center gap-2 cursor-pointer">
            <i data-lucide="save" class="w-4 h-4"></i>
            <span id="saveBtnText">Save Payment Settings</span>
          </button>
        </div>
      </form>
    </div>
  </main>

  <script>
    function toggleSecretVisibility() {
      const input = document.getElementById('settingKeySecret');
      const icon = document.getElementById('secretToggleIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
      } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
      }
      if (typeof lucide === 'object') lucide.createIcons();
    }

    async function savePaymentSettings(e) {
      e.preventDefault();
      const btn = document.getElementById('saveSettingsBtn');
      const btnText = document.getElementById('saveBtnText');
      const notice = document.getElementById('saveStatusNotice');
      const noticeText = document.getElementById('saveStatusText');

      const mode = document.querySelector('input[name="razorpay_mode"]:checked').value;
      const keyId = document.getElementById('settingKeyId').value.trim();
      const keySecret = document.getElementById('settingKeySecret').value.trim();
      const webhookSecret = document.getElementById('settingWebhookSecret').value.trim();

      btn.disabled = true;
      btnText.innerText = 'Saving to Database...';

      try {
        const res = await fetch('/api/admin.php?action=save_payment_settings', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            razorpay_mode: mode,
            razorpay_key_id: keyId,
            razorpay_key_secret: keySecret,
            razorpay_webhook_secret: webhookSecret
          })
        });
        const data = await res.json();

        if (data.success) {
          notice.className = 'text-xs font-semibold text-emerald-400 flex items-center gap-1.5';
          noticeText.innerText = '✓ ' + data.message;
          notice.classList.remove('hidden');

          // Update badge
          const badge = document.getElementById('gatewayStatusBadge');
          if (mode === 'live') {
            badge.className = 'px-4 py-2 rounded-xl text-xs font-bold shadow-lg flex items-center gap-1.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span><span>LIVE MODE ACTIVE</span>';
          } else {
            badge.className = 'px-4 py-2 rounded-xl text-xs font-bold shadow-lg flex items-center gap-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/40';
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-400"></span><span>TEST MODE ACTIVE</span>';
          }
        } else {
          notice.className = 'text-xs font-semibold text-rose-400 flex items-center gap-1.5';
          noticeText.innerText = '❌ ' + (data.message || 'Failed to save settings');
          notice.classList.remove('hidden');
        }
      } catch (err) {
        notice.className = 'text-xs font-semibold text-rose-400 flex items-center gap-1.5';
        noticeText.innerText = '❌ Server connection error: ' + err.message;
        notice.classList.remove('hidden');
      } finally {
        btn.disabled = false;
        btnText.innerText = 'Save Payment Settings';
        if (typeof lucide === 'object') lucide.createIcons();
      }
    }
  </script>
</body>
</html>
