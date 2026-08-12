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

    // Handle Test Unlock Mode Switch Action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_test_mode') {
        $mode = trim($_POST['test_mode'] ?? 'production');
        $customDate = trim($_POST['override_date'] ?? '');
        $overrideFile = __DIR__ . '/../config/rakhi_unlock_override.json';
        file_put_contents($overrideFile, json_encode([
            'test_mode' => $mode,
            'override_date' => $customDate,
            'updated_at' => date('Y-m-d H:i:s')
        ]));
        $msg = "⚡ Unlock Mode Settings Saved Successfully! Mode: " . strtoupper($mode);
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

    // Handle Bulk CSV / Text Import
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_vouchers') {
        $amount = intval($_POST['voucher_amount'] ?? 100);
        $rawCodes = [];

        // Check if CSV File Uploaded
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['csv_file']['tmp_name'];
            $fileContent = file_get_contents($tmpPath);
            $lines = explode("\n", str_replace("\r", "", $fileContent));
            foreach ($lines as $line) {
                $cols = explode(",", $line);
                $code = trim($cols[0] ?? '');
                if (!empty($code) && strtolower($code) !== 'code' && strtolower($code) !== 'voucher_code') {
                    $rawCodes[] = $code;
                }
            }
        }

        // Check if Text Box Filled
        if (!empty($_POST['bulk_text_codes'])) {
            $textLines = explode("\n", str_replace("\r", "", $_POST['bulk_text_codes']));
            foreach ($textLines as $tLine) {
                $tCode = trim($tLine);
                if (!empty($tCode)) {
                    $rawCodes[] = $tCode;
                }
            }
        }

        $rawCodes = array_unique($rawCodes);
        $addedCount = 0;

        if (!empty($rawCodes)) {
            $insStmt = $db->prepare("INSERT IGNORE INTO rakhi_vouchers_vault (voucher_code, amount, status) VALUES (?, ?, 'available')");
            foreach ($rawCodes as $c) {
                $insStmt->execute([$c, $amount]);
                if ($insStmt->rowCount() > 0) {
                    $addedCount++;
                }
            }

            // Auto-assign newly added codes to unassigned pending allocations matching this amount
            $stmtUnassigned = $db->prepare("SELECT * FROM rakhi_voucher_allocations WHERE voucher_code IS NULL AND allocated_amount = ? ORDER BY id ASC");
            $stmtUnassigned->execute([$amount]);
            $unassignedList = $stmtUnassigned->fetchAll();

            foreach ($unassignedList as $un) {
                $stmtAvail = $db->prepare("SELECT * FROM rakhi_vouchers_vault WHERE status = 'available' AND amount = ? ORDER BY id ASC LIMIT 1");
                $stmtAvail->execute([$amount]);
                $availCode = $stmtAvail->fetch();

                if ($availCode) {
                    $db->prepare("UPDATE rakhi_vouchers_vault SET status = 'assigned', assigned_order_id = ?, assigned_at = NOW() WHERE id = ?")->execute([$un['order_id'], $availCode['id']]);
                    $db->prepare("UPDATE rakhi_voucher_allocations SET voucher_code = ? WHERE id = ?")->execute([$availCode['voucher_code'], $un['id']]);
                }
            }

            $msg = "✅ Successfully imported {$addedCount} Amazon Vouchers (₹{$amount}) into vault and auto-assigned pending orders!";
            $msgType = 'success';
        } else {
            $msg = "❌ Please upload a valid CSV file or paste voucher codes.";
            $msgType = 'error';
        }
    }

    // Fetch Metrics
    $totalAllocations = $db->query("SELECT COUNT(*) FROM rakhi_voucher_allocations")->fetchColumn();
    $totalVaultCodes  = $db->query("SELECT COUNT(*) FROM rakhi_vouchers_vault")->fetchColumn();
    $availableVault   = $db->query("SELECT COUNT(*) FROM rakhi_vouchers_vault WHERE status = 'available'")->fetchColumn();
    $unassignedOrders = $db->query("SELECT COUNT(*) FROM rakhi_voucher_allocations WHERE voucher_code IS NULL")->fetchColumn();

    // Breakdown by denomination
    $denomStats = $db->query("
        SELECT allocated_amount, COUNT(*) as total_orders, 
               SUM(CASE WHEN voucher_code IS NOT NULL THEN 1 ELSE 0 END) as assigned_count
        FROM rakhi_voucher_allocations 
        GROUP BY allocated_amount 
        ORDER BY allocated_amount ASC
    ")->fetchAll();

    // Recent Allocations
    $recentAllocations = $db->query("
        SELECT a.*, o.buyer_name, o.buyer_email, o.created_at as order_date, t.name as template_name
        FROM rakhi_voucher_allocations a
        JOIN orders o ON a.order_id = o.order_id
        LEFT JOIN templates t ON o.template_id = t.template_id
        ORDER BY a.id DESC LIMIT 50
    ")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Rakhi Voucher Vault Manager — ' . APP_NAME;
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

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-16 relative z-10 space-y-8">

    <?php if (!$isLoggedIn): ?>
      <!-- LOGIN SCREEN -->
      <div class="max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="shield-check" class="w-7 h-7"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Admin Vault Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">Raksha Bandhan Voucher &amp; Affiliate Control Center</p>
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
          <button type="submit" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
            Log In To Vault Manager
          </button>
        </form>
      </div>

    <?php else: ?>

      <!-- DASHBOARD HEADER -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#4d444b]/40 pb-6">
        <div>
          <span class="text-[10px] uppercase font-extrabold tracking-[0.2em] text-[#eac34a] block">Festive Control Center</span>
          <h1 class="text-3xl font-bold font-serif text-[#e8e0e3]">🎁 Rakhi Amazon Voucher Vault</h1>
          <p class="text-xs text-[#d0c3cb] mt-1">Manage bulk Amazon voucher codes, view probability allocations, and upload CSV batches.</p>
        </div>
        <a href="?logout=1" class="px-4 py-2.5 rounded-xl bg-[#151215] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-md">
          <i data-lucide="log-out" class="w-4 h-4"></i>
          <span>Log Out</span>
        </a>
      </div>

      <?php if ($msg): ?>
        <div class="p-4 rounded-2xl text-xs font-bold <?php echo $msgType === 'success' ? 'bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9]' : 'bg-rose-900/40 border border-rose-500/40 text-rose-300'; ?>">
          <?php echo htmlspecialchars($msg); ?>
        </div>
      <?php endif; ?>

      <!-- METRICS GRID -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#d0c3cb]/70">Total Rakhi Orders</span>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]"><?php echo number_format($totalAllocations); ?></div>
        </div>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#eac34a]/30 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#eac34a]">Vault Codes Available</span>
          <div class="text-2xl font-bold font-serif text-[#eac34a]"><?php echo number_format($availableVault); ?></div>
        </div>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#d0c3cb]/70">Total Loaded Codes</span>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]"><?php echo number_format($totalVaultCodes); ?></div>
        </div>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-rose-500/30 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-rose-400">Unassigned Pending</span>
          <div class="text-2xl font-bold font-serif text-rose-400"><?php echo number_format($unassignedOrders); ?></div>
        </div>
      </div>

      <!-- TEST MODE & DATE OVERRIDE CARD -->
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-[#4d444b]/40 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 flex items-center justify-center font-bold">
              <i data-lucide="sliders" class="w-5 h-5"></i>
            </div>
            <div>
              <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">🛠️ Test Unlock &amp; Target Unlock Date Settings</h3>
              <p class="text-xs text-[#d0c3cb]">Set custom reveal date/time or toggle instant test unlock mode anytime.</p>
            </div>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-bold font-mono <?php 
            if ($currentMode === 'unlocked_now') {
                echo 'bg-[#1e3b20] text-[#a4e4b9] border border-[#a4e4b9]/40';
            } elseif ($currentMode === 'custom_date') {
                echo 'bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40';
            } else {
                echo 'bg-[#151215] text-[#d0c3cb] border border-[#4d444b]';
            }
          ?>">
            Status: <?php 
              if ($currentMode === 'unlocked_now') echo '⚡ TEST UNLOCKED NOW';
              elseif ($currentMode === 'custom_date') echo '📅 CUSTOM DATE (' . htmlspecialchars($currentOverrideDate) . ')';
              else echo '🟢 PRODUCTION (28 AUG 12:00 PM)';
            ?>
          </span>
        </div>

        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="save_test_mode">
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="block text-xs font-bold text-[#d0c3cb]">Unlock Mode</label>
              <select name="test_mode" id="testModeSelect" onchange="toggleCustomDateInput()" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
                <option value="production" <?php echo $currentMode === 'production' ? 'selected' : ''; ?>>🟢 Production Mode (Default: 28 Aug 2026, 12:00 PM IST)</option>
                <option value="custom_date" <?php echo $currentMode === 'custom_date' ? 'selected' : ''; ?>>📅 Custom Target Date &amp; Time Picker</option>
                <option value="unlocked_now" <?php echo $currentMode === 'unlocked_now' ? 'selected' : ''; ?>>⚡ Instant Test Unlock Mode (Unlocked Right Now for Testing!)</option>
              </select>
            </div>

            <div id="customDateContainer" class="space-y-1 <?php echo $currentMode === 'custom_date' ? '' : 'hidden'; ?>">
              <label class="block text-xs font-bold text-[#eac34a]">Select Target Date &amp; Time IST</label>
              <input type="datetime-local" name="override_date" value="<?php echo htmlspecialchars($currentOverrideDate ?: '2026-08-14T12:00'); ?>" class="w-full bg-[#151215] border border-[#eac34a]/60 rounded-xl px-4 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
            </div>
          </div>

          <button type="submit" class="px-6 py-2.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all cursor-pointer whitespace-nowrap">
            Save Unlock Mode &amp; Date Settings
          </button>
        </form>

        <script>
          function toggleCustomDateInput() {
            const select = document.getElementById('testModeSelect');
            const container = document.getElementById('customDateContainer');
            if (select && container) {
              if (select.value === 'custom_date') {
                container.classList.remove('hidden');
              } else {
                container.classList.add('hidden');
              }
            }
          }
        </script>
      </div>

      <!-- BULK CSV & TEXT IMPORT FORM -->
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#4d444b]/40 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 flex items-center justify-center font-bold">
              <i data-lucide="upload-cloud" class="w-5 h-5"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">1-Click Bulk Amazon Voucher Import</h3>
              <p class="text-xs text-[#d0c3cb]">Upload CSV Excel file or paste gift codes to stock your vault instantly.</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <a href="download_sample.php?type=csv" class="px-3.5 py-2 bg-[#151215] hover:bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md transition-all">
              <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
              <span>Download Excel (.csv)</span>
            </a>
            <a href="download_sample.php?type=txt" class="px-3.5 py-2 bg-[#151215] hover:bg-[#3b1e3b] text-[#d0c3cb] border border-[#4d444b] rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md transition-all">
              <i data-lucide="file-text" class="w-4 h-4"></i>
              <span>Download Text (.txt)</span>
            </a>
          </div>
        </div>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="import_vouchers">

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Voucher Denomination</label>
              <select name="voucher_amount" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
                <option value="100">₹100 Voucher (Standard 75%)</option>
                <option value="150">₹150 Voucher (18%)</option>
                <option value="250">₹250 Voucher (5%)</option>
                <option value="500">₹500 Voucher (1.5%)</option>
                <option value="2000">₹2,000 Mega Bumper (0.5%)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Upload CSV / Excel File</label>
              <input type="file" name="csv_file" accept=".csv,.txt,.xlsx" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#d0c3cb] file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#eac34a] file:text-[#241a00]">
            </div>

            <div class="sm:col-span-1">
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Or Paste Codes (One Per Line)</label>
              <textarea name="bulk_text_codes" rows="3" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none font-mono" placeholder="AMZ-100-XXXX&#10;AMZ-100-YYYY"></textarea>
            </div>
          </div>

          <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#eac34a] via-[#f7d774] to-[#cca830] hover:brightness-110 text-[#241a00] font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer flex items-center justify-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Import &amp; Auto-Assign Vouchers to Orders</span>
          </button>
        </form>
      </div>

      <!-- DENOMINATION BREAKDOWN TABLE -->
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b]/40 shadow-xl space-y-4">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Probability Tier Allocations</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
          <?php foreach ($denomStats as $st): ?>
            <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b]/30 space-y-1">
              <span class="text-xs font-extrabold text-[#eac34a]">₹<?php echo $st['allocated_amount']; ?> Tier</span>
              <div class="text-xs text-[#d0c3cb]">Orders: <strong><?php echo $st['total_orders']; ?></strong></div>
              <div class="text-xs text-[#a4e4b9]">Assigned: <strong><?php echo $st['assigned_count']; ?></strong></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- RECENT ALLOCATIONS TABLE -->
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b]/40 shadow-xl space-y-4 overflow-x-auto">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Recent Orders &amp; Voucher Status</h3>
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#4d444b]/40 text-[#d0c3cb] font-extrabold">
              <th class="py-3 px-3">Order ID</th>
              <th class="py-3 px-3">Buyer Name</th>
              <th class="py-3 px-3">Tier Amount</th>
              <th class="py-3 px-3">Assigned Code</th>
              <th class="py-3 px-3">Status</th>
              <th class="py-3 px-3">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#4d444b]/20">
            <?php foreach ($recentAllocations as $row): ?>
              <tr class="hover:bg-[#151215]/50 transition-all">
                <td class="py-3 px-3 font-mono text-[#eac34a]"><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td class="py-3 px-3"><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                <td class="py-3 px-3 font-extrabold text-[#e8e0e3]">₹<?php echo $row['allocated_amount']; ?></td>
                <td class="py-3 px-3 font-mono text-[#d0c3cb]"><?php echo htmlspecialchars($row['voucher_code'] ?: '⚠️ Pending Vault Code'); ?></td>
                <td class="py-3 px-3">
                  <?php if (!empty($row['voucher_code'])): ?>
                    <span class="px-2 py-0.5 rounded-full bg-[#1e3b20] text-[#a4e4b9] text-[10px] font-bold">Ready for Unlock</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 rounded-full bg-rose-900/40 text-rose-300 text-[10px] font-bold">Needs Code</span>
                  <?php endif; ?>
                </td>
                <td class="py-3 px-3 text-[#d0c3cb]/70"><?php echo date('d M Y, h:i A', strtotime($row['order_date'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>

  </main>
</body>
</html>
