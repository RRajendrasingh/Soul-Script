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

// Official Showcase Pages that MUST NEVER be deleted
$showcaseSlugs = ['ananya-rohan', 'kavya-aarav', 'priya-aman', 'aanya-kabir', 'mona-aman', 'manvi-rakhi-v2'];

if ($isLoggedIn) {
    $db = getDB();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'execute_safe_reset') {
        try {
            // 1. Delete all Rakhi Voucher Allocations & Vault test codes
            $db->exec("DELETE FROM rakhi_voucher_allocations");
            $db->exec("DELETE FROM rakhi_vouchers_vault");

            // 2. Identify Showcase Page IDs & Order IDs to protect
            $inClause = "'" . implode("','", $showcaseSlugs) . "'";
            $showcaseRows = $db->query("SELECT page_id, order_id FROM pages WHERE url_slug IN ($inClause)")->fetchAll();
            
            $protectedPageIds = array_filter(array_column($showcaseRows, 'page_id'));
            $protectedOrderIds = array_filter(array_column($showcaseRows, 'order_id'));

            // 3. Delete non-showcase reasons, proposals, page_content, and pages
            if (!empty($protectedPageIds)) {
                $pIdsIn = "'" . implode("','", $protectedPageIds) . "'";
                $db->exec("DELETE FROM reasons_list WHERE page_id NOT IN ($pIdsIn)");
                $db->exec("DELETE FROM proposal_responses WHERE page_id NOT IN ($pIdsIn)");
                $db->exec("DELETE FROM page_content WHERE page_id NOT IN ($pIdsIn)");
                $db->exec("DELETE FROM pages WHERE page_id NOT IN ($pIdsIn)");
            } else {
                $db->exec("DELETE FROM reasons_list");
                $db->exec("DELETE FROM proposal_responses");
                $db->exec("DELETE FROM page_content");
                $db->exec("DELETE FROM pages");
            }

            // 4. Delete non-showcase orders
            if (!empty($protectedOrderIds)) {
                $oIdsIn = "'" . implode("','", $protectedOrderIds) . "'";
                $db->exec("DELETE FROM orders WHERE order_id NOT IN ($oIdsIn)");
            } else {
                $db->exec("DELETE FROM orders");
            }

            $msg = "🎉 Safe System Reset Complete! All test orders, test customer pages, and dummy vouchers have been completely wiped. Official showcase demo pages were safely preserved. All metrics are now 0.";
            $msgType = 'success';
        } catch (Exception $e) {
            $msg = "❌ Error during reset: " . $e->getMessage();
            $msgType = 'error';
        }
    }

    // Fetch Current DB Counts
    $orderCount       = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pageCount        = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
    $rakhiOrdersCount = $db->query("SELECT COUNT(*) FROM rakhi_voucher_allocations")->fetchColumn();
    $vaultCodeCount   = $db->query("SELECT COUNT(*) FROM rakhi_vouchers_vault")->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'System Reset & Maintenance — ' . APP_NAME;
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-16 relative z-10 space-y-8">

    <?php if (!$isLoggedIn): ?>
      <!-- LOGIN SCREEN -->
      <div class="max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="shield-check" class="w-7 h-7"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Admin Vault Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">System Reset &amp; Production Maintenance</p>
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
            Unlock Maintenance Access
          </button>
        </form>
      </div>

    <?php else: ?>
      <?php require_once __DIR__ . '/nav_header.php'; ?>

      <!-- HEADER -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#4d444b]/40 pb-6">
        <div>
          <span class="text-[10px] uppercase font-extrabold tracking-[0.2em] text-[#eac34a] block">Database Clean Slate</span>
          <h1 class="text-3xl font-bold font-serif text-[#e8e0e3]">⚙️ System Reset &amp; Maintenance</h1>
          <p class="text-xs text-[#d0c3cb] mt-1">Purge test orders, test pages, and dummy vouchers before beta testing or public launch.</p>
        </div>
      </div>

      <?php if ($msg): ?>
        <div class="p-4 rounded-2xl text-xs font-bold <?php echo $msgType === 'success' ? 'bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9]' : 'bg-rose-900/40 border border-rose-500/40 text-rose-300'; ?>">
          <?php echo htmlspecialchars($msg); ?>
        </div>
      <?php endif; ?>

      <!-- CURRENT DATABASE STATE METRICS -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#d0c3cb]/70">Total Orders in DB</span>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]"><?php echo number_format($orderCount); ?></div>
        </div>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#d0c3cb]/70">Total Pages in DB</span>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]"><?php echo number_format($pageCount); ?></div>
        </div>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#d0c3cb]/70">Rakhi Allocations</span>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]"><?php echo number_format($rakhiOrdersCount); ?></div>
        </div>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-1">
          <span class="text-[10px] uppercase font-extrabold text-[#d0c3cb]/70">Vault Codes in DB</span>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]"><?php echo number_format($vaultCodeCount); ?></div>
        </div>
      </div>

      <!-- SAFE RESET CARD -->
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-rose-500/40 shadow-2xl space-y-6">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-rose-950/60 text-rose-400 border border-rose-500/40 flex items-center justify-center shrink-0">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
          </div>
          <div class="space-y-1">
            <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">1-Click Safe Test Data Purge</h3>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              This action resets your database for fresh beta testing or full public launch. It wipes all scratch test orders and test vouchers while keeping all showcase gallery pages completely safe.
            </p>
          </div>
        </div>

        <!-- WHAT GETS DELETED & WHAT IS PROTECTED -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <div class="bg-[#151215] p-4 rounded-2xl border border-rose-500/30 space-y-2">
            <span class="text-xs font-bold text-rose-400 flex items-center gap-1.5">
              <i data-lucide="trash-2" class="w-4 h-4"></i>
              <span>What Gets Deleted:</span>
            </span>
            <ul class="text-[11px] text-[#d0c3cb] space-y-1 list-disc list-inside">
              <li>All test customer orders &amp; payments</li>
              <li>All test recipient gift pages &amp; stories</li>
              <li>All test Amazon voucher codes in vault</li>
              <li>All test voucher allocations (Counters &rarr; 0)</li>
            </ul>
          </div>

          <div class="bg-[#151215] p-4 rounded-2xl border border-[#a4e4b9]/30 space-y-2">
            <span class="text-xs font-bold text-[#a4e4b9] flex items-center gap-1.5">
              <i data-lucide="shield-check" class="w-4 h-4"></i>
              <span>What Is Permanently Safe:</span>
            </span>
            <ul class="text-[11px] text-[#d0c3cb] space-y-1 list-disc list-inside">
              <li>Admin login credentials &amp; settings</li>
              <li>Official sample showcase demo pages</li>
              <li>Amazon Affiliate Store products catalog</li>
              <li>Core website templates &amp; pricing configuration</li>
            </ul>
          </div>
        </div>

        <form method="POST" onsubmit="return confirm('⚠️ ARE YOU SURE? This will purge all test orders and dummy vouchers. Official showcase pages will be preserved.');" class="pt-2">
          <input type="hidden" name="action" value="execute_safe_reset">
          <button type="submit" class="w-full py-4 bg-gradient-to-r from-rose-600 via-rose-700 to-rose-800 hover:brightness-110 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xl transition-all cursor-pointer flex items-center justify-center gap-2">
            <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
            <span>Execute 1-Click Safe Reset (Purge Test Data)</span>
          </button>
        </form>
      </div>

    <?php endif; ?>

  </main>
</body>
</html>
