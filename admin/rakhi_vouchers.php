<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/voucher_helper.php';

$loginError = '';
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: " . APP_URL . "/admin/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_user'], $_POST['admin_pass'])) {
    if ($_POST['admin_user'] === ADMIN_USER && $_POST['admin_pass'] === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $loginError = 'Invalid admin username or password.';
    }
}

$isLoggedIn = !empty($_SESSION['admin_logged_in']);
$msg = '';
$msgType = 'success';

if ($isLoggedIn) {
    $db = getDB();

    // 1. Handle Test Unlock Mode Switch Action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_test_mode') {
        $mode = trim($_POST['test_mode'] ?? 'production');
        $customDate = trim($_POST['override_date'] ?? '');
        $overrideFile = __DIR__ . '/../config/rakhi_unlock_override.json';
        file_put_contents($overrideFile, json_encode([
            'test_mode' => $mode,
            'override_date' => $customDate,
            'updated_at' => date('Y-m-d H:i:s')
        ]));
        $msg = "⚡ Unlock Mode Settings Saved! Current Mode: " . strtoupper($mode);
        $msgType = 'success';
    }

    // Read current mode state
    $overrideData = [];
    $overrideFile = __DIR__ . '/../config/rakhi_unlock_override.json';
    if (file_exists($overrideFile)) {
        $overrideData = json_decode(file_get_contents($overrideFile), true) ?: [];
    }
    $currentMode = $overrideData['test_mode'] ?? 'production';
    $currentOverrideDate = $overrideData['override_date'] ?? '';

    // 2. Handle Bulk CSV / Text Import (Lottery Pool)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_vouchers') {
        $defaultAmount = intval($_POST['voucher_amount'] ?? 100);
        $entriesToInsert = [];

        // Check if CSV File Uploaded
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['csv_file']['tmp_name'];
            $fileContent = file_get_contents($tmpPath);
            $lines = explode("\n", str_replace("\r", "", $fileContent));
            foreach ($lines as $line) {
                $cols = explode(",", $line);
                $code = trim($cols[0] ?? '');
                $rowAmount = isset($cols[1]) && is_numeric(trim($cols[1])) ? intval(trim($cols[1])) : $defaultAmount;
                if (!empty($code) && strtolower($code) !== 'code' && strtolower($code) !== 'voucher_code') {
                    $entriesToInsert[] = ['code' => $code, 'amount' => $rowAmount];
                }
            }
        }

        // Check if Text Box Filled
        if (!empty($_POST['bulk_text_codes'])) {
            $textLines = explode("\n", str_replace("\r", "", $_POST['bulk_text_codes']));
            foreach ($textLines as $tLine) {
                $cols = explode(",", $tLine);
                $tCode = trim($cols[0] ?? '');
                $tAmount = isset($cols[1]) && is_numeric(trim($cols[1])) ? intval(trim($cols[1])) : $defaultAmount;
                if (!empty($tCode)) {
                    $entriesToInsert[] = ['code' => $tCode, 'amount' => $tAmount];
                }
            }
        }

        $addedCount = 0;
        $updatedCount = 0;

        if (!empty($entriesToInsert)) {
            $insStmt = $db->prepare("
                INSERT INTO rakhi_vouchers_vault (voucher_code, amount, status) 
                VALUES (?, ?, 'available')
                ON DUPLICATE KEY UPDATE 
                amount = IF(status = 'available', VALUES(amount), amount)
            ");

            foreach ($entriesToInsert as $entry) {
                $insStmt->execute([$entry['code'], $entry['amount']]);
                $rCount = $insStmt->rowCount();
                if ($rCount === 1) $addedCount++;
                elseif ($rCount === 2) $updatedCount++;
            }

            // Lottery Draw: Auto-assign random available codes to any unassigned pending orders
            $unassignedList = $db->query("SELECT * FROM rakhi_voucher_allocations WHERE voucher_code IS NULL ORDER BY id ASC")->fetchAll();
            $assignedCount = 0;
            foreach ($unassignedList as $un) {
                $availCode = $db->query("SELECT * FROM rakhi_vouchers_vault WHERE status = 'available' ORDER BY RAND() LIMIT 1")->fetch();
                if ($availCode) {
                    $db->prepare("UPDATE rakhi_vouchers_vault SET status = 'assigned', assigned_order_id = ?, assigned_at = NOW() WHERE id = ?")->execute([$un['order_id'], $availCode['id']]);
                    $db->prepare("UPDATE rakhi_voucher_allocations SET voucher_code = ?, allocated_amount = ? WHERE id = ?")->execute([$availCode['voucher_code'], $availCode['amount'], $un['id']]);
                    $assignedCount++;
                }
            }

            $msg = "✅ Added " . count($entriesToInsert) . " vouchers into lottery pool & assigned {$assignedCount} pending orders!";
            $msgType = 'success';
        } else {
            $msg = "❌ Please upload a CSV file or paste voucher codes.";
            $msgType = 'error';
        }
    }

    // 3. Handle Manual Random Sync Action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sync_all_allocations') {
        $unassignedList = $db->query("SELECT * FROM rakhi_voucher_allocations WHERE voucher_code IS NULL ORDER BY id ASC")->fetchAll();
        $assignedCount = 0;
        foreach ($unassignedList as $un) {
            $availCode = $db->query("SELECT * FROM rakhi_vouchers_vault WHERE status = 'available' ORDER BY RAND() LIMIT 1")->fetch();
            if ($availCode) {
                $db->prepare("UPDATE rakhi_vouchers_vault SET status = 'assigned', assigned_order_id = ?, assigned_at = NOW() WHERE id = ?")->execute([$un['order_id'], $availCode['id']]);
                $db->prepare("UPDATE rakhi_voucher_allocations SET voucher_code = ?, allocated_amount = ? WHERE id = ?")->execute([$availCode['voucher_code'], $availCode['amount'], $un['id']]);
                $assignedCount++;
            }
        }
        $msg = "🎲 Random Lottery Sync: {$assignedCount} orders received random available vouchers!";
        $msgType = 'success';
    }

    // 4. Handle Clear Unused Vault Action (Test Mode)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_available_vault') {
        $deleted = $db->exec("DELETE FROM rakhi_vouchers_vault WHERE status = 'available'");
        $msg = "🗑️ Cleared {$deleted} unassigned codes from vault.";
        $msgType = 'success';
    }

    // Purge any accidental allocations for 0-amount or demo showcase orders
    try {
        $db->exec("
            DELETE rva FROM rakhi_voucher_allocations rva
            JOIN orders o ON rva.order_id = o.order_id
            WHERE o.amount_paid <= 0 OR o.order_id LIKE 'ord_demo_%'
        ");
    } catch (Exception $exPurge) {}

    // Auto-sync only REAL PAID Rakhi orders (amount_paid > 0) that don't have allocation records yet
    try {
        $stmtPaidUnallocated = $db->query("
            SELECT o.order_id, p.page_id 
            FROM orders o 
            LEFT JOIN pages p ON o.order_id = p.order_id 
            LEFT JOIN rakhi_voucher_allocations rva ON o.order_id = rva.order_id 
            WHERE o.payment_status = 'paid' 
              AND o.amount_paid > 0
              AND o.order_id NOT LIKE 'ord_demo_%'
              AND (o.template_id = 'raksha_bandhan_royal' OR o.template_id LIKE '%rakhi%') 
              AND rva.id IS NULL
        ");
        $unallocatedOrders = $stmtPaidUnallocated->fetchAll();
        foreach ($unallocatedOrders as $uOrd) {
            allocateRakhiVoucher($uOrd['order_id'], $uOrd['page_id'] ?? null);
        }
    } catch (Exception $exSync) {}

    // Fetch Simple Dashboard Metrics (Real Paid Orders Only)
    $totalOrders      = $db->query("SELECT COUNT(*) FROM rakhi_voucher_allocations a JOIN orders o ON a.order_id = o.order_id WHERE o.payment_status = 'paid' AND o.amount_paid > 0 AND o.order_id NOT LIKE 'ord_demo_%'")->fetchColumn();
    $availableVault   = $db->query("SELECT COUNT(*) FROM rakhi_vouchers_vault WHERE status = 'available'")->fetchColumn();
    $assignedOrders   = $db->query("SELECT COUNT(*) FROM rakhi_voucher_allocations a JOIN orders o ON a.order_id = o.order_id WHERE a.voucher_code IS NOT NULL AND o.payment_status = 'paid' AND o.amount_paid > 0 AND o.order_id NOT LIKE 'ord_demo_%'")->fetchColumn();
    $unassignedOrders = $db->query("SELECT COUNT(*) FROM rakhi_voucher_allocations a JOIN orders o ON a.order_id = o.order_id WHERE a.voucher_code IS NULL AND o.payment_status = 'paid' AND o.amount_paid > 0 AND o.order_id NOT LIKE 'ord_demo_%'")->fetchColumn();

    // Unified Master Table Query: Real Paid Customer Orders Only
    $masterList = $db->query("
        SELECT a.*, o.buyer_name, o.buyer_email, o.amount_paid, o.created_at as order_date, p.url_slug as gift_slug
        FROM rakhi_voucher_allocations a
        JOIN orders o ON a.order_id = o.order_id
        LEFT JOIN pages p ON a.order_id = p.order_id
        WHERE o.payment_status = 'paid'
          AND o.amount_paid > 0
          AND o.order_id NOT LIKE 'ord_demo_%'
        ORDER BY a.id DESC LIMIT 100
    ")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Rakhi Voucher Vault & Lucky Draw — ' . APP_NAME;
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-20 relative z-10 space-y-8">

    <?php if (!$isLoggedIn): ?>
      <!-- LOGIN SCREEN -->
      <div class="max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="shield-check" class="w-7 h-7"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Admin Vault Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">Raksha Bandhan Voucher &amp; Lucky Draw Manager</p>
        </div>
        <?php if ($loginError): ?>
          <div class="p-3 bg-rose-900/40 border border-rose-500/40 text-rose-300 rounded-xl text-xs font-semibold">
            <?php echo htmlspecialchars($loginError); ?>
          </div>
        <?php endif; ?>
        <form method="POST" class="space-y-4 text-left">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Username</label>
            <input type="text" name="admin_user" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Password</label>
            <input type="password" name="admin_pass" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#eac34a] to-[#d4af37] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:brightness-110 transition-all cursor-pointer">
            Unlock Vault Access
          </button>
        </form>
      </div>

    <?php else: ?>
      <?php require_once __DIR__ . '/nav_header.php'; ?>

      <!-- DASHBOARD HEADER -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#4d444b]/40 pb-6">
        <div>
          <span class="text-[10px] uppercase font-extrabold tracking-[0.2em] text-[#eac34a] block">Festive Control Center</span>
          <h1 class="text-3xl font-bold font-serif text-[#e8e0e3]">🎁 Rakhi Amazon Voucher &amp; Lucky Draw</h1>
          <p class="text-xs text-[#d0c3cb] mt-1">Upload voucher codes into the lottery pool — codes are randomly assigned to Rakhi buyers!</p>
        </div>
      </div>

      <?php if ($msg): ?>
        <div class="p-4 rounded-2xl text-xs font-bold <?php echo $msgType === 'success' ? 'bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9]' : 'bg-rose-900/40 border border-rose-500/40 text-rose-300'; ?>">
          <?php echo htmlspecialchars($msg); ?>
        </div>
      <?php endif; ?>

      <!-- SIMPLE 3-CARD METRICS -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#d0c3cb]/70">🛒 Total Rakhi Orders</span>
          <div class="text-3xl font-bold font-serif text-[#e8e0e3]"><?php echo number_format($totalOrders); ?></div>
          <p class="text-[11px] text-[#d0c3cb]/60"><?php echo number_format($assignedOrders); ?> assigned, <?php echo number_format($unassignedOrders); ?> pending</p>
        </div>

        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#eac34a]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#eac34a]">🎁 Available in Vault Pool</span>
          <div class="text-3xl font-bold font-serif text-[#eac34a]"><?php echo number_format($availableVault); ?></div>
          <p class="text-[11px] text-[#eac34a]/70">Ready to be drawn for new buyers</p>
        </div>

        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#a4e4b9]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#a4e4b9]">🎲 Lucky Draw Status</span>
          <div class="text-xl font-bold font-serif text-[#a4e4b9]">
            <?php echo $unassignedOrders === 0 ? '100% Assigned ✅' : $unassignedOrders . ' Orders Need Codes ⚠️'; ?>
          </div>
          <p class="text-[11px] text-[#a4e4b9]/70">Pure Random Draw from Vault</p>
        </div>
      </div>

      <!-- UNLOCK SETTINGS & QUICK CONTROLS -->
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-[#4d444b]/40 pb-3">
          <div class="flex items-center gap-2.5">
            <i data-lucide="clock" class="w-5 h-5 text-[#eac34a]"></i>
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">Target Unlock Date &amp; Test Mode</h3>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-bold font-mono <?php 
            if ($currentMode === 'unlocked_now') echo 'bg-[#1e3b20] text-[#a4e4b9] border border-[#a4e4b9]/40';
            elseif ($currentMode === 'custom_date') echo 'bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40';
            else echo 'bg-[#151215] text-[#d0c3cb] border border-[#4d444b]';
          ?>">
            Active: <?php 
              if ($currentMode === 'unlocked_now') echo '⚡ TEST UNLOCKED NOW';
              elseif ($currentMode === 'custom_date') echo '📅 CUSTOM (' . htmlspecialchars($currentOverrideDate) . ')';
              else echo '🟢 PRODUCTION (28 AUG 12:00 PM)';
            ?>
          </span>
        </div>

        <form method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
          <input type="hidden" name="action" value="save_test_mode">
          
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Unlock Mode</label>
            <select name="test_mode" id="testModeSelect" onchange="toggleCustomDateInput()" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
              <option value="production" <?php echo $currentMode === 'production' ? 'selected' : ''; ?>>🟢 Production (28 Aug 2026, 12:00 PM IST)</option>
              <option value="custom_date" <?php echo $currentMode === 'custom_date' ? 'selected' : ''; ?>>📅 Custom Date &amp; Time</option>
              <option value="unlocked_now" <?php echo $currentMode === 'unlocked_now' ? 'selected' : ''; ?>>⚡ Instant Unlocked Now (Test Mode)</option>
            </select>
          </div>

          <div id="customDateContainer" class="<?php echo $currentMode === 'custom_date' ? '' : 'hidden'; ?>">
            <label class="block text-xs font-bold text-[#eac34a] mb-1">Custom Date &amp; Time (IST)</label>
            <input type="datetime-local" name="override_date" value="<?php echo htmlspecialchars($currentOverrideDate ?: '2026-08-16T14:10'); ?>" class="w-full bg-[#151215] border border-[#eac34a]/60 rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
          </div>

          <div>
            <button type="submit" class="w-full py-2 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all cursor-pointer">
              Save Unlock Settings
            </button>
          </div>
        </form>

        <script>
          function toggleCustomDateInput() {
            const select = document.getElementById('testModeSelect');
            const container = document.getElementById('customDateContainer');
            if (select && container) {
              container.classList.toggle('hidden', select.value !== 'custom_date');
            }
          }
        </script>
      </div>

      <!-- 1-CLICK BULK VOUCHER STOCKER -->
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/40 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-[#4d444b]/40 pb-3">
          <div class="flex items-center gap-2.5">
            <i data-lucide="upload-cloud" class="w-5 h-5 text-[#eac34a]"></i>
            <div>
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">Add Amazon Vouchers to Lottery Pool</h3>
              <p class="text-xs text-[#d0c3cb]">Upload CSV or paste codes below — codes will be automatically drawn for pending &amp; new orders!</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <a href="download_sample.php?type=csv" class="px-3 py-1.5 bg-[#151215] hover:bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 rounded-xl text-xs font-bold flex items-center gap-1 shadow-sm">
              <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
              <span>Sample CSV</span>
            </a>
          </div>
        </div>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="import_vouchers">

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Default Amount (if CSV has 1 column)</label>
              <select name="voucher_amount" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
                <option value="100">₹100 Amazon Voucher</option>
                <option value="150">₹150 Amazon Voucher</option>
                <option value="250">₹250 Amazon Voucher</option>
                <option value="500">₹500 Amazon Voucher</option>
                <option value="2000">₹2,000 Mega Bumper Voucher</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Upload CSV (code, amount)</label>
              <input type="file" name="csv_file" accept=".csv,.txt,.xlsx" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-2 py-1.5 text-xs text-[#d0c3cb] file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#eac34a] file:text-[#241a00]">
            </div>

            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Or Paste Codes (CODE, AMOUNT)</label>
              <textarea name="bulk_text_codes" rows="2" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-1.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none font-mono" placeholder="AMZ-100-XXXX, 100&#10;AMZ-500-YYYY, 500"></textarea>
            </div>
          </div>

          <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-[#eac34a] to-[#cca830] hover:brightness-110 text-[#241a00] font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer flex items-center gap-1.5">
              <i data-lucide="plus-circle" class="w-4 h-4"></i>
              <span>Add Vouchers to Pool</span>
            </button>

            <div class="flex items-center gap-2">
              <button type="submit" form="syncForm" class="px-3.5 py-2 bg-[#3b1e3b] hover:bg-[#eac34a] hover:text-[#241a00] text-[#eac34a] border border-[#eac34a]/40 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1">
                <i data-lucide="shuffle" class="w-3.5 h-3.5"></i>
                <span>Random Draw for Pending</span>
              </button>
              <button type="submit" form="clearForm" onclick="return confirm('Delete all unused available codes from vault?');" class="px-3.5 py-2 bg-[#2a060b] hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/40 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Clear Unused</span>
              </button>
            </div>
          </div>
        </form>

        <form id="syncForm" method="POST" class="hidden">
          <input type="hidden" name="action" value="sync_all_allocations">
        </form>
        <form id="clearForm" method="POST" class="hidden">
          <input type="hidden" name="action" value="clear_available_vault">
        </form>
      </div>

      <!-- UNIFIED MASTER TABLE: RAKHI ORDERS & LUCKY DRAWS -->
      <div class="bg-[#221f21] p-5 sm:p-6 rounded-3xl border border-[#4d444b]/40 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#4d444b]/40 pb-3">
          <div>
            <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">📋 Rakhi Orders &amp; Assigned Lucky Vouchers</h3>
            <p class="text-xs text-[#d0c3cb]">All customer orders and the Amazon voucher codes they will unlock on Raksha Bandhan.</p>
          </div>
          <span class="text-xs text-[#eac34a] font-bold bg-[#151215] px-3 py-1 rounded-full border border-[#eac34a]/30 self-start sm:self-auto">
            <?php echo count($masterList); ?> Total Orders
          </span>
        </div>

        <div class="overflow-x-auto -mx-5 sm:mx-0 px-5 sm:px-0">
          <table class="w-full text-left border-collapse text-xs min-w-[780px]">
            <thead>
              <tr class="border-b border-[#4d444b]/40 text-[#d0c3cb] font-extrabold text-[11px] uppercase tracking-wider">
                <th class="py-3 px-3 whitespace-nowrap">Order ID</th>
                <th class="py-3 px-3 whitespace-nowrap">Buyer Details</th>
                <th class="py-3 px-3 whitespace-nowrap">Gift Page Link</th>
                <th class="py-3 px-3 whitespace-nowrap">🎁 Lucky Voucher Code</th>
                <th class="py-3 px-3 whitespace-nowrap">Amount</th>
                <th class="py-3 px-3 whitespace-nowrap">Unlock Status</th>
                <th class="py-3 px-3 whitespace-nowrap">Order Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#4d444b]/20">
              <?php if (empty($masterList)): ?>
                <tr>
                  <td colspan="7" class="py-8 text-center text-[#d0c3cb]">No Rakhi orders found yet.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($masterList as $row): ?>
                  <tr class="hover:bg-[#151215]/50 transition-all">
                    <td class="py-3.5 px-3 font-mono text-[#eac34a] font-bold whitespace-nowrap">
                      <?php echo htmlspecialchars($row['order_id']); ?>
                    </td>
                    <td class="py-3.5 px-3 min-w-[140px]">
                      <div class="font-bold text-[#e8e0e3] leading-tight"><?php echo htmlspecialchars($row['buyer_name']); ?></div>
                      <div class="text-[10px] text-[#d0c3cb]/70 font-mono mt-0.5"><?php echo htmlspecialchars($row['buyer_email']); ?></div>
                    </td>
                    <td class="py-3.5 px-3 whitespace-nowrap">
                      <?php if (!empty($row['gift_slug'])): ?>
                        <a href="<?php echo APP_URL . '/gift/' . urlencode($row['gift_slug']); ?>" target="_blank" class="inline-flex items-center gap-1.5 text-[#eac34a] hover:underline font-mono bg-[#151215] px-2.5 py-1 rounded-lg border border-[#eac34a]/30">
                          <span>/gift/<?php echo htmlspecialchars($row['gift_slug']); ?></span>
                          <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                      <?php else: ?>
                        <span class="text-[#d0c3cb]/50">—</span>
                      <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-3 whitespace-nowrap">
                      <?php if (!empty($row['voucher_code'])): ?>
                        <span class="font-mono bg-[#151215] px-3 py-1.5 rounded-xl border border-[#a4e4b9]/30 text-white font-bold tracking-widest inline-block shadow-inner">
                          <?php echo htmlspecialchars($row['voucher_code']); ?>
                        </span>
                      <?php else: ?>
                        <span class="px-2.5 py-1 rounded-lg bg-rose-900/40 text-rose-300 font-bold text-[10px] whitespace-nowrap">
                          ⚠️ Needs Code from Vault
                        </span>
                      <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-3 font-black text-sm text-[#a4e4b9] whitespace-nowrap">
                      ₹<?php echo $row['allocated_amount']; ?>
                    </td>
                    <td class="py-3.5 px-3 whitespace-nowrap">
                      <?php if (!empty($row['voucher_code'])): ?>
                        <span class="px-2.5 py-1 rounded-full bg-[#1e3b20] text-[#a4e4b9] text-[10px] font-bold inline-flex items-center gap-1 whitespace-nowrap">
                          <i data-lucide="check-circle" class="w-3 h-3"></i>
                          <span>Ready for Unlock</span>
                        </span>
                      <?php else: ?>
                        <span class="px-2.5 py-1 rounded-full bg-rose-900/40 text-rose-300 text-[10px] font-bold whitespace-nowrap">
                          Pending Code
                        </span>
                      <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-3 text-[#d0c3cb]/70 font-mono whitespace-nowrap">
                      <?php echo date('d M Y, h:i A', strtotime($row['order_date'])); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php endif; ?>

  </main>
</body>
</html>
