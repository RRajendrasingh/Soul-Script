<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

if (isset($_GET['logout'])) {
    unset($_SESSION['buyer_email'], $_SESSION['buyer_name'], $_SESSION['buyer_token'], $_SESSION['edit_token'], $_SESSION['buyer_page_id'], $_SESSION['buyer_slug']);
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    @session_destroy();
    header("Location: " . APP_URL . "/edit.php");
    exit;
}

$urlToken = trim($_GET['token'] ?? '');
$urlSlug = trim($_GET['slug'] ?? '');

if (empty($urlToken) && !empty($urlSlug)) {
    try {
        $db = getDB();
        $stmtSlug = $db->prepare("SELECT edit_token FROM pages WHERE LOWER(url_slug) = LOWER(?) LIMIT 1");
        $stmtSlug->execute([$urlSlug]);
        $foundToken = $stmtSlug->fetchColumn();
        if ($foundToken) {
            $urlToken = $foundToken;
            $_SESSION['edit_token'] = $foundToken;
        }
    } catch (Exception $e) {}
}

if (!empty($urlToken)) {
    $_SESSION['edit_token'] = $urlToken;
}

$token = !empty($urlToken) ? $urlToken : '';
$buyerEmail = trim($_SESSION['buyer_email'] ?? '');

$serverBuyerPages = [];
$serverPendingOrders = [];

if (!empty($buyerEmail)) {
    try {
        $db = getDB();
        // 1. Fetch ALL created pages for this buyer
        $stmtP = $db->prepare("
            SELECT p.edit_token, p.page_id, p.template_id, p.url_slug, c.partner_name, p.created_at, o.buyer_name, o.buyer_email, o.order_id
            FROM orders o
            JOIN pages p ON o.order_id = p.order_id
            LEFT JOIN page_content c ON p.page_id = c.page_id
            WHERE LOWER(o.buyer_email) = LOWER(?)
            ORDER BY p.created_at DESC
        ");
        $stmtP->execute([$buyerEmail]);
        $serverBuyerPages = $stmtP->fetchAll() ?: [];

        // 2. Fetch ALL pending paid orders (paid, but page not customized yet)
        $stmtPending = $db->prepare("
            SELECT o.order_id, o.template_id, o.buyer_name, o.buyer_email, o.created_at, t.name as template_name
            FROM orders o
            LEFT JOIN pages p ON o.order_id = p.order_id
            LEFT JOIN templates t ON o.template_id = t.template_id
            WHERE LOWER(o.buyer_email) = LOWER(?) AND o.payment_status = 'paid' AND p.page_id IS NULL
            ORDER BY o.created_at DESC
        ");
        $stmtPending->execute([$buyerEmail]);
        $rawPending = $stmtPending->fetchAll() ?: [];
        $serverPendingOrders = array_map(function($po) {
            $po['redirect_url'] = APP_URL . '/create.php?order_id=' . urlencode($po['order_id']);
            return $po;
        }, $rawPending);
    } catch (Exception $eS) {}
}

$showDashboard = !empty($urlToken);
$showHub = !$showDashboard && !empty($buyerEmail);
$showLogin = !$showDashboard && !$showHub;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Buyer Management Portal — ' . APP_NAME;
  require_once __DIR__ . '/includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Unified Global Navbar -->
  <?php 
  $current_page = 'edit';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-28 pb-12 relative z-10 space-y-4 sm:space-y-8">
    
    <!-- VIEW A: LOGIN SCREEN (When no token provided) -->
    <div id="loginView" class="<?php echo $showLogin ? '' : 'hidden'; ?> max-w-md mx-auto space-y-6">
      <div class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="lock" class="w-7 h-7"></i>
        </div>

        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Buyer Portal Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">Log in using your Email &amp; Secret Edit Password to update your gift website.</p>
        </div>

        <form id="buyerLoginForm" onsubmit="event.preventDefault(); handleBuyerLogin(event); return false;" class="space-y-4 text-left">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Your Email Address</label>
            <input type="email" id="loginEmail" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. rohan@example.com" required>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-[#d0c3cb]">Secret Edit Password</label>
              <button type="button" onclick="showForgotPasswordModal()" class="text-[11px] text-[#eac34a] hover:underline cursor-pointer">Forgot Password?</button>
            </div>
            <div class="relative">
              <input type="password" id="loginPassword" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl pl-4 pr-11 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Enter your edit password" required>
              <button type="button" onclick="togglePasswordVisibility('loginPassword', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer" title="Toggle password visibility">
                <i data-lucide="eye" class="w-4 h-4"></i>
              </button>
            </div>
          </div>

          <div id="loginMsg" class="hidden text-xs text-rose-400 font-semibold text-center"></div>

          <button type="submit" id="loginBtn" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
            Log In To Live Visual Editor
          </button>
        </form>
      </div>
    </div>

    <!-- FORGOT PASSWORD MODAL -->
    <div id="forgotPasswordModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/40 max-w-md w-full space-y-5 shadow-2xl relative max-h-[85vh] overflow-y-auto scrollbar-none my-auto">
        <button type="button" onclick="closeForgotPasswordModal()" class="absolute top-4 right-4 text-[#d0c3cb] hover:text-white p-1 text-lg cursor-pointer">✕</button>
        <div class="text-center space-y-1">
          <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 flex items-center justify-center mx-auto mb-2">
            <i data-lucide="key-round" class="w-6 h-6"></i>
          </div>
          <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Reset Password 🔑</h3>
          <p class="text-xs text-[#d0c3cb]">Enter your registered email to reset your account password in 10 seconds.</p>
        </div>

        <form id="forgotPassForm" onsubmit="handleRequestPasswordReset(event); return false;" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Registered Email Address</label>
            <input type="email" id="forgotPassEmail" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. rohan@example.com" required>
          </div>

          <div id="forgotPassMsg" class="hidden text-xs text-center p-3 rounded-xl"></div>

          <button type="submit" id="forgotPassBtn" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer shadow-md">
            Send Password Reset Link
          </button>
        </form>
      </div>
    </div>

    <!-- SET NEW PASSWORD MODAL (Triggered via Email Reset Link ?reset_token=xyz) -->
    <div id="setNewPasswordModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/40 max-w-md w-full space-y-5 shadow-2xl relative max-h-[85vh] overflow-y-auto scrollbar-none my-auto">
        <button type="button" onclick="closeSetNewPasswordModal()" class="absolute top-4 right-4 text-[#d0c3cb] hover:text-white p-1 text-lg cursor-pointer">✕</button>
        <div class="text-center space-y-1">
          <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 flex items-center justify-center mx-auto mb-2">
            <i data-lucide="shield-check" class="w-6 h-6"></i>
          </div>
          <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Set New Account Password 🔑</h3>
          <p class="text-xs text-[#d0c3cb]">Type your new secret edit password below to update your account access.</p>
        </div>

        <form id="setNewPassForm" onsubmit="handlePerformPasswordReset(event); return false;" class="space-y-4">
          <input type="hidden" id="newPassTokenInput" value="">
          
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">New Secret Password</label>
            <div class="relative">
              <input type="password" id="newPassInput" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl pl-4 pr-11 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Min 4 characters" required>
              <button type="button" onclick="togglePasswordVisibility('newPassInput', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer" title="Toggle password visibility">
                <i data-lucide="eye" class="w-4 h-4"></i>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Confirm New Password</label>
            <div class="relative">
              <input type="password" id="confirmNewPassInput" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl pl-4 pr-11 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Re-type new password" required>
              <button type="button" onclick="togglePasswordVisibility('confirmNewPassInput', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer" title="Toggle password visibility">
                <i data-lucide="eye" class="w-4 h-4"></i>
              </button>
            </div>
          </div>

          <div id="setNewPassMsg" class="hidden text-xs text-center p-3 rounded-xl"></div>

          <button type="submit" id="setNewPassBtn" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer shadow-md">
            Save New Password &amp; Log In
          </button>
        </form>
      </div>
    </div>

    <!-- VIEW B: PURCHASED GIFTS HUB (When logged in / managing multiple gifts) -->
    <div id="hubView" class="<?php echo $showHub ? '' : 'hidden'; ?> space-y-6">
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#4d444b]/40 pb-5">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center text-xl shrink-0 shadow-md">
              🎁
            </div>
            <div>
              <span class="text-[10px] uppercase font-extrabold tracking-[0.2em] text-[#eac34a] block">Buyer Portal Hub</span>
              <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]" id="hubTitleLabel">Your Purchased Gift Websites</h2>
              <p class="text-xs text-[#d0c3cb]">Manage all your romantic surprise pages in one single private portal.</p>
            </div>
          </div>
          <button type="button" onclick="handleBuyerLogout()" class="px-4 py-2.5 rounded-xl bg-[#151215] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 transition-all cursor-pointer shadow-md shrink-0">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
            <span>Log Out</span>
          </button>
        </div>

        <!-- Purchased Gifts Visual Cards Grid -->
        <div id="hubGiftsGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <!-- Dynamic Purchased Gift Cards -->
        </div>
      </div>
    </div>

    <!-- VIEW C: BUYER VISUAL EDITOR DASHBOARD -->
    <div id="dashboardView" class="<?php echo $showDashboard ? '' : 'hidden'; ?> space-y-3.5 sm:space-y-6">

      <!-- Active Plan Badge Banner & Share Link -->
      <div class="bg-[#221f21] p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-[#eac34a]/30 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-3 sm:gap-4">
        <div class="flex items-center gap-2.5 sm:gap-3 text-left min-w-0 flex-1">
          <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center shrink-0 shadow-md">
            <i data-lucide="sparkles" class="w-4 h-4 sm:w-6 sm:h-6"></i>
          </div>
          <div class="space-y-0.5 min-w-0 flex-1">
            <span id="activePlanBadge" class="text-[8px] sm:text-[10px] uppercase font-extrabold tracking-wider text-[#eac34a] bg-[#3b1e3b] px-2 py-0.5 rounded-full border border-[#e4b9df]/20 inline-block">
              Active Plan
            </span>
            <h2 class="text-base sm:text-2xl font-bold font-serif text-[#e8e0e3] truncate" id="dashPartnerTitle">Partner Gift Dashboard</h2>
            <p class="text-[10px] sm:text-xs text-[#d0c3cb] truncate">Update your gift contents in real-time below.</p>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-1.5 w-full md:flex md:w-auto md:items-center md:gap-2 justify-start md:justify-end shrink-0">
          <button type="button" id="backToHubBtn" onclick="showHubView()" class="hidden col-span-3 sm:col-span-1 px-2.5 py-1.5 sm:px-3.5 sm:py-2.5 rounded-xl bg-[#3b1e3b] hover:bg-[#eac34a] text-[#eac34a] hover:text-[#241a00] border border-[#eac34a]/40 font-bold text-[10px] sm:text-xs uppercase tracking-wider flex items-center justify-center gap-1 transition-all cursor-pointer shadow-md whitespace-nowrap">
            <i data-lucide="arrow-left" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
            <span>All Gifts</span>
          </button>
          <a id="viewLivePageBtn" href="#" target="_blank" class="px-2 py-1.5 sm:px-3.5 sm:py-2.5 rounded-xl bg-[#eac34a] text-[#241a00] font-extrabold text-[10px] sm:text-xs uppercase tracking-wider flex items-center justify-center gap-1 hover:bg-[#ffe088] transition-all shadow-md whitespace-nowrap">
            <span>View Live</span>
            <i data-lucide="external-link" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
          </a>
          <a id="dashWaShareBtn" href="#" target="_blank" class="px-2 py-1.5 sm:px-3.5 sm:py-2.5 rounded-xl bg-[#25D366] text-black font-extrabold text-[10px] sm:text-xs uppercase tracking-wider flex items-center justify-center gap-1 hover:bg-[#20bd5a] transition-all shadow-md whitespace-nowrap">
            <span>💬 Share</span>
          </a>
          <button type="button" onclick="handleBuyerLogout()" class="px-2 py-1.5 sm:px-3.5 sm:py-2.5 rounded-xl bg-[#221f21] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-extrabold text-[10px] sm:text-xs uppercase tracking-wider flex items-center justify-center gap-1 transition-all cursor-pointer shadow-md whitespace-nowrap">
            <i data-lucide="log-out" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
            <span>Log Out</span>
          </button>
        </div>
      </div>

      <!-- Dashboard Section Navigation Tabs -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none w-full whitespace-nowrap">
        <button type="button" onclick="switchTab('general')" id="tabBtn-general" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] shadow-[0_0_15px_rgba(234,195,74,0.3)] transition-all whitespace-nowrap shrink-0 cursor-pointer">General &amp; Music</button>
        <button type="button" onclick="switchTab('theme')" id="tabBtn-theme" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Story Milestones</button>
        <button type="button" onclick="switchTab('photos')" id="tabBtn-photos" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Photo Gallery</button>
        <button type="button" onclick="switchTab('letters')" id="tabBtn-letters" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Sealed Letters</button>
        <button type="button" onclick="switchTab('tokens')" id="tabBtn-tokens" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Love Tokens</button>
        <button type="button" onclick="switchTab('security')" id="tabBtn-security" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Security &amp; Passwords</button>
      </div>

      <!-- Main Edit Form -->
      <form id="editPageForm" onsubmit="saveDashboardChanges(event); return false;" class="bg-[#221f21] p-4 sm:p-8 rounded-2xl sm:rounded-3xl border border-[#4d444b]/50 shadow-2xl space-y-5 sm:space-y-6">
        <input type="hidden" id="activeEditToken" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" id="activeTemplateId" value="">

        <!-- TAB 1: GENERAL & MUSIC -->
        <div id="tabContent-general" class="space-y-4 text-xs">
          <div class="border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">⚙️ General &amp; Music Settings</h3>
          </div>

          <div>
            <label id="partnerNameLabel" class="block font-semibold text-[#d0c3cb] mb-1">Partner's First Name *</label>
            <input type="text" id="partnerName" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>

          <div>
            <label id="taglineQuoteLabel" class="block font-semibold text-[#d0c3cb] mb-1">Custom Romantic Quote / Tagline Banner *</label>
            <input type="text" id="taglineQuote" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Safar Khubsurat h manjil se bhi 🌹" required>
          </div>

          <!-- Visual Gift Receiver Avatar Photo Manager -->
          <div>
            <label id="partnerPhotoLabel" class="block font-semibold text-[#d0c3cb] mb-1.5">Gift Receiver / Partner Profile Photo 🖼️</label>
            <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] flex flex-col sm:flex-row items-center gap-4">
              <div id="partnerAvatarContainer" class="w-16 h-16 rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#eac34a] flex items-center justify-center font-bold text-2xl shadow-[0_0_20px_rgba(234,195,74,0.3)] shrink-0 overflow-hidden">
                <span class="w-full h-full flex items-center justify-center font-bold text-2xl text-[#eac34a] bg-[#3b1e3b] rounded-full">P</span>
              </div>
              <div class="flex-1 text-center sm:text-left space-y-2">
                <input type="file" id="dashPhotoInput" accept="image/*" onchange="handleDashPhotoSelect(this)" class="hidden">
                <input type="hidden" id="receiverPhotoUrl" value="">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                  <button type="button" onclick="document.getElementById('dashPhotoInput').click()" class="px-4 py-2 bg-[#3b1e3b] hover:bg-[#eac34a] text-[#eac34a] hover:text-[#241a00] border border-[#eac34a]/40 font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shadow-md">
                    <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                    <span>Upload / Change Photo</span>
                  </button>
                  <button type="button" onclick="removeDashPhoto()" id="removePhotoBtn" class="px-3 py-2 bg-[#221f21] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs rounded-xl transition-all cursor-pointer hidden flex items-center gap-1.5">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Remove Photo</span>
                  </button>
                </div>
                <p class="text-[10px] text-[#d0c3cb]/70">Upload a portrait photo to show at the top of the surprise page. If no photo is added, partner's initial character will be displayed.</p>
              </div>
            </div>
          </div>

          <!-- Universal Music Engine (iTunes Live Search + YouTube Link + Favorite Singer) -->
          <div class="bg-[#151215] p-5 rounded-2xl border border-[#eac34a]/30 space-y-4">
            <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-2">
              <label class="font-bold text-[#eac34a] text-xs uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="music" class="w-4 h-4 text-[#eac34a]"></i>
                <span>Background Music Engine 🎵</span>
              </label>
            </div>

            <!-- Choice Mode Radios -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label class="flex items-center gap-2 p-3 bg-[#221f21] border border-[#4d444b] rounded-xl cursor-pointer hover:border-[#eac34a]">
                <input type="radio" name="dash_music_mode" value="itunes_search" checked onchange="toggleDashMusicMode('itunes_search')" class="text-[#eac34a]">
                <div class="text-xs">
                  <strong class="block text-[#e8e0e3]">🔍 Live Song Search</strong>
                  <span class="text-[10px] text-[#d0c3cb]">Search any song / singer</span>
                </div>
              </label>

              <label class="flex items-center gap-2 p-3 bg-[#221f21] border border-[#4d444b] rounded-xl cursor-pointer hover:border-[#eac34a]">
                <input type="radio" name="dash_music_mode" value="youtube_link" onchange="toggleDashMusicMode('youtube_link')" class="text-[#eac34a]">
                <div class="text-xs">
                  <strong class="block text-[#e8e0e3]">🎥 YouTube / Shorts Link</strong>
                  <span class="text-[10px] text-[#d0c3cb]">Paste YouTube or Shorts URL</span>
                </div>
              </label>
            </div>

            <!-- iTunes Live Search Container -->
            <div id="dashItunesSearchBox" class="space-y-3">
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Search &amp; Select Song (Bollywood, Romantic, English, Punjabi) 🎶</label>
                <input type="text" id="dashItunesQueryInput" oninput="handleDashItunesSearch(this.value)" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none" placeholder="🔍 Type song name or singer e.g. Tum Hi Ho, Arijit Singh, Zara Sa, Kesariya, Taylor Swift...">
              </div>

              <!-- Selected Track Card -->
              <div id="dashSelectedTrackCard" class="bg-[#100d10] p-3 rounded-xl border border-[#eac34a]/60 flex flex-wrap items-center justify-between gap-2 sm:gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                  <img id="dashSelectedTrackImg" src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80" class="w-10 h-10 rounded-lg object-cover border border-[#4d444b] shrink-0">
                  <div class="min-w-0 flex-1">
                    <span class="block font-bold text-xs text-[#e8e0e3] truncate" id="dashSelectedTrackTitle">Tum Hi Ho</span>
                    <span class="block text-[10px] text-[#eac34a] truncate" id="dashSelectedTrackArtist">Artist: Arijit Singh</span>
                  </div>
                </div>
                <span class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 font-bold shrink-0">✓ Selected</span>
              </div>

              <!-- Live Search Results Container -->
              <div id="dashItunesResultsList" class="hidden space-y-2 max-h-60 overflow-y-auto bg-[#100d10] p-2 rounded-xl border border-[#4d444b]"></div>
            </div>

            <!-- YouTube Link Container -->
            <div id="dashYoutubeLinkBox" class="hidden space-y-3">
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Paste YouTube Video / Shorts / Audio URL 🎥</label>
                <input type="url" id="dashYoutubeUrlInput" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="https://www.youtube.com/watch?v=... or https://youtube.com/shorts/... or https://youtu.be/...">
                <p class="text-[10px] text-[#d0c3cb]/70 mt-1">Paste any public YouTube video or YouTube Shorts link. Video ID will be extracted automatically.</p>
              </div>
            </div>
            <input type="hidden" id="bgMusicUrl" value="">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Secret Hint Question (Lock Screen)</label>
              <input type="text" id="hintQuestion" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
            </div>

            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">New Hint Answer (Leave blank to keep unchanged)</label>
              <input type="text" id="hintAnswer" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Enter new secret answer">
            </div>
          </div>

          <div>
            <label id="loveNoteLabel" class="block font-semibold text-[#d0c3cb] mb-1">Short Love Note / Signature Message</label>
            <textarea id="loveNoteText" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="3"></textarea>
          </div>
        </div>

        <!-- TAB 2: DYNAMIC TEMPLATE-ISOLATED THEME SETTINGS -->
        <div id="tabContent-theme" class="hidden space-y-4 text-xs">
          <div id="themeContainer">
            <!-- Dynamically populated based on template_id -->
          </div>
        </div>

        <!-- TAB 3: SECURITY & PASSWORDS MANAGEMENT -->
        <div id="tabContent-security" class="hidden space-y-6 text-xs">
          <div class="border-b border-[#4d444b]/40 pb-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-[#eac34a]"></i>
                <span>Account Security &amp; Password Management</span>
              </h3>
              <p class="text-[11px] text-[#d0c3cb] mt-0.5">Manage your <?php echo defined('APP_NAME') ? APP_NAME : 'GiftReveal'; ?> buyer portal login password safely.</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 font-bold text-[10px] uppercase tracking-wider hidden sm:inline-block">
              🔐 Encrypted Storage
            </span>
          </div>

          <div class="bg-[#151215] p-4 sm:p-7 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6">
            <!-- Buyer Portal Login Password Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3.5 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-[#221f21] via-[#2d222d] to-[#221f21] border border-[#eac34a]/30">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center shrink-0 shadow-md">
                <i data-lucide="key-round" class="w-5 h-5 sm:w-6 sm:h-6 text-[#eac34a]"></i>
              </div>
              <div class="space-y-0.5">
                <h4 class="font-bold text-sm text-[#e8e0e3]">Update Buyer Account Password 🔑</h4>
                <p class="text-[11px] text-[#d0c3cb] leading-relaxed">Change the secret password used to log in at <code><?php echo parse_url(APP_URL, PHP_URL_HOST) ?? 'giftreveal.in'; ?>/edit.php</code>. Leave all fields blank if you don't wish to change your password.</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <!-- Current Password -->
              <div class="space-y-1.5">
                <label class="block font-semibold text-[#d0c3cb] flex items-center gap-1.5 text-xs">
                  <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                  <span>Current Account Password</span>
                </label>
                <div class="relative">
                  <input type="password" id="currentBuyerPassword" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none transition-all pr-10" placeholder="Type current password">
                  <button type="button" onclick="togglePasswordVisibility('currentBuyerPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>

              <!-- New Password -->
              <div class="space-y-1.5">
                <label class="block font-semibold text-[#d0c3cb] flex items-center gap-1.5 text-xs">
                  <i data-lucide="key" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                  <span>New Account Password</span>
                </label>
                <div class="relative">
                  <input type="password" id="newBuyerPassword" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none transition-all pr-10" placeholder="Type new password">
                  <button type="button" onclick="togglePasswordVisibility('newBuyerPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>

              <!-- Confirm New Password -->
              <div class="space-y-1.5">
                <label class="block font-semibold text-[#d0c3cb] flex items-center gap-1.5 text-xs">
                  <i data-lucide="check-check" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                  <span>Re-enter New Password</span>
                </label>
                <div class="relative">
                  <input type="password" id="confirmBuyerPassword" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none transition-all pr-10" placeholder="Re-enter new password">
                  <button type="button" onclick="togglePasswordVisibility('confirmBuyerPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Password Requirements Badge Footer -->
            <div class="p-3.5 bg-[#221f21] rounded-2xl border border-[#4d444b]/60 flex items-center justify-between text-[11px] text-[#d0c3cb]">
              <span class="flex items-center gap-1.5">
                <i data-lucide="info" class="w-4 h-4 text-[#eac34a]"></i>
                <span>Leave fields blank if keeping current password. Minimum 4 characters required.</span>
              </span>
            </div>
          </div>
        </div>

        <!-- TAB 4: SCRAPBOOK PHOTOS MANAGEMENT -->
        <div id="tabContent-photos" class="hidden space-y-5 text-xs">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-[#4d444b]/40 pb-3">
            <div>
              <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
                <i data-lucide="image" class="w-5 h-5 text-[#eac34a]"></i>
                <span>Photo Gallery Management 🖼️</span>
              </h3>
              <span class="text-[11px] font-semibold text-[#eac34a]" id="dashSelectedPhotoCount">Selected: 0 / 25 Photos</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
              <a href="javascript:void(0)" onclick="openSampleLibraryModal()" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-[#3b1e3b] text-[#e4b9df] font-bold text-xs border border-[#e4b9df]/40 hover:bg-[#4d274d] transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-md shrink-0">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>Sample Library</span>
              </a>
              <a href="javascript:void(0)" onclick="document.getElementById('dashScrapbookFileInput').click()" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-[#eac34a] text-[#241a00] font-bold text-xs hover:bg-[#ffe088] transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-md shrink-0">
                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                <span>Upload Photos</span>
              </a>
              <input type="file" id="dashScrapbookFileInput" accept="image/*" multiple class="hidden" onchange="handleDashScrapbookFiles(event)">
            </div>
          </div>

          <!-- Current Selected Uploads Grid -->
          <div class="bg-[#151215] p-4 sm:p-5 rounded-3xl border border-[#4d444b] min-h-[140px] space-y-3">
            <div id="dashScrapbookContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
              <!-- Dynamic Photo Thumbnails with Delete X Button -->
            </div>
          </div>

          <!-- Quick Pick Sample Photos Gallery -->
          <div class="space-y-3 pt-2">
            <label class="text-xs font-bold uppercase tracking-wider text-[#eac34a] flex items-center gap-1.5">
              <i data-lucide="sparkles" class="w-4 h-4"></i>
              <span>QUICK PICK SAMPLE ROMANTIC PHOTOS:</span>
            </label>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3" id="dashSamplePhotosGrid">
              <!-- Rendered by JS -->
            </div>
          </div>
        </div>

        <!-- TAB 4: SEALED LETTERS -->
        <div id="tabContent-letters" class="hidden space-y-4 text-xs">
          <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">✉️ Wax-Sealed Letters</h3>
            <button type="button" onclick="addLetterRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Sealed Letter</button>
          </div>

          <div id="editLettersList" class="space-y-3">
            <!-- Dynamic Letters -->
          </div>
        </div>

        <!-- TAB 5: LOVE TOKENS -->
        <div id="tabContent-tokens" class="hidden space-y-4 text-xs">
          <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🎟️ Redeemable Love Tokens</h3>
            <button type="button" onclick="addTokenRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Love Token</button>
          </div>

          <div id="editTokensList" class="space-y-3">
            <!-- Dynamic Tokens -->
          </div>
        </div>

        <!-- Submit Controls -->
        <div class="pt-4 border-t border-[#4d444b]/40 flex items-center justify-between">
          <div id="saveMsg" class="text-xs font-bold text-[#eac34a]"></div>
          <button type="submit" id="saveChangesBtn" class="px-8 py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
            Save All Changes
          </button>
        </div>
      </form>

    </div>

  </main>

  <!-- Unified Global Footer -->
  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <!-- Interactive Circle Crop Modal -->
  <div id="circleCropModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4 hidden">
    <div class="bg-[#221f21] border border-[#eac34a]/40 rounded-3xl p-6 max-w-md w-full text-center space-y-4 shadow-2xl relative">
      <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
        <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
          <i data-lucide="crop" class="w-4 h-4 text-[#eac34a]"></i>
          <span>Adjust &amp; Crop Partner Photo</span>
        </h3>
        <button onclick="closeCircleCropModal()" type="button" class="text-[#d0c3cb] hover:text-white text-lg font-bold">✕</button>
      </div>

      <!-- Circle Preview Container -->
      <div class="relative w-64 h-64 mx-auto overflow-hidden bg-[#151215] rounded-2xl border border-[#4d444b] flex items-center justify-center cursor-move select-none" id="cropCanvasWrapper">
        <canvas id="cropCanvas" width="256" height="256" class="touch-none"></canvas>
        <!-- Circular Overlay Mask Guide -->
        <div class="absolute inset-0 rounded-full border-4 border-[#eac34a] shadow-[0_0_0_9999px_rgba(21,18,21,0.7)] pointer-events-none"></div>
      </div>

      <!-- Controls: Zoom & Position reset -->
      <div class="space-y-3 pt-2">
        <div class="flex items-center gap-3 text-xs">
          <span class="text-[#d0c3cb] font-semibold w-12 text-right">Zoom:</span>
          <input type="range" id="cropZoomRange" min="0.5" max="3" step="0.05" value="1" oninput="updateCropCanvas()" class="w-full accent-[#eac34a]">
        </div>
        <p class="text-[10px] text-[#d0c3cb]/70">Drag photo to align partner's face inside the golden circle.</p>
      </div>

      <!-- Modal Action Buttons -->
        <button type="button" onclick="applyCircleCrop()" class="w-1/2 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:bg-[#ffe088] transition-all">Crop &amp; Apply</button>
      </div>
    </div>
  </div>

  <!-- Sample Library Picker Modal (Top-Level Fail-Safe Modal) -->
  <div id="sampleLibraryModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[9999] flex items-center justify-center p-3 sm:p-5 hidden">
    <div class="bg-[#221f21] border border-[#eac34a]/40 rounded-3xl p-4 sm:p-6 max-w-4xl w-full text-left space-y-4 shadow-2xl relative max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3 shrink-0">
        <div>
          <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
            <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
            <span>Sample Romantic Library</span>
          </h3>
          <p class="text-[11px] text-[#d0c3cb] mt-0.5">Tap any photo to add it directly to your scrapbook gallery (Up to 25 photos max).</p>
        </div>
        <a href="javascript:void(0)" onclick="closeSampleLibraryModal()" class="text-[#d0c3cb] hover:text-white text-lg font-bold p-1 cursor-pointer">✕</a>
      </div>

      <!-- Category Filter Pills Bar (Clean scrollbar-none) -->
      <div id="sampleCategoryFilters" class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-[#4d444b]/30 shrink-0 text-xs [scrollbar-width:none] [-ms-overflow-style:none]">
        <button type="button" onclick="filterSampleCategory('all')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-bold text-[11px] transition-all bg-[#eac34a] text-[#241a00] border border-[#eac34a] shadow-md cursor-pointer shrink-0" data-cat="all">All Photos ✨</button>
        <button type="button" onclick="filterSampleCategory('anniversary')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="anniversary">Anniversary 🌹</button>
        <button type="button" onclick="filterSampleCategory('birthday')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="birthday">Birthday 🎂</button>
        <button type="button" onclick="filterSampleCategory('proposal')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="proposal">Proposal 💍</button>
        <button type="button" onclick="filterSampleCategory('raksha_bandhan')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="raksha_bandhan">Rakhi</button>
        <button type="button" onclick="filterSampleCategory('long_distance')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="long_distance">Long Distance ✈️</button>
      </div>

      <!-- Scrollable Grid of Admin Sample Photos -->
      <div id="sampleModalGrid" class="sample-modal-grid p-2.5 flex-1 min-h-[300px]">
        <div class="col-span-full text-center py-10 text-[#d0c3cb] text-xs">
          <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-[#eac34a] mb-2"></i>
          Loading sample gallery photos...
        </div>
      </div>

      <div class="pt-3 border-t border-[#4d444b]/40 flex items-center justify-between shrink-0">
        <span class="text-xs text-[#eac34a] font-semibold" id="sampleModalCountLabel">Selected: 0 / 25</span>
        <a href="javascript:void(0)" onclick="closeSampleLibraryModal()" class="px-5 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-[#ffe088] transition-all shadow-md cursor-pointer">
          Done Selecting
        </a>
      </div>
    </div>
  </div>

  <script>
    const THEME_FEATURES = {
      'anniversary_reveal': {
        hasLetters: true, hasTokens: true,
        dashTitleSuffix: "'s Gift Dashboard",
        labels: {
          name: "Partner's First Name *",
          tagline: "Custom Romantic Quote / Tagline Banner *",
          note: "Short Love Note / Signature Message *",
          photo: "Gift Receiver / Partner Profile Photo 🖼️"
        }
      },
      'birthday_magic': {
        hasLetters: true, hasTokens: false,
        dashTitleSuffix: "'s Birthday Dashboard",
        labels: {
          name: "Birthday Person's Name *",
          tagline: "Custom Birthday Tagline / Motto *",
          note: "Birthday Wish / Personal Message *",
          photo: "Birthday Person Profile Photo 🖼️"
        }
      },
      'perfect_proposal': {
        hasLetters: true, hasTokens: false,
        dashTitleSuffix: "'s Gift Dashboard",
        labels: {
          name: "Partner's First Name *",
          tagline: "Custom Romantic Quote / Tagline Banner *",
          note: "Short Love Note / Signature Message *",
          photo: "Partner's Profile Photo 🖼️"
        }
      },
      'long_distance_love': {
        hasLetters: true, hasTokens: true,
        dashTitleSuffix: "'s Gift Dashboard",
        labels: {
          name: "Partner's First Name *",
          tagline: "Custom Quote / Tagline Banner *",
          note: "Short Love Note / Signature Message *",
          photo: "Partner's Profile Photo 🖼️"
        }
      },
      'raksha_bandhan_royal': {
        hasLetters: false, hasTokens: false,
        dashTitleSuffix: "'s Rakhi Dashboard",
        labels: {
          name: "Brother / Sister's First Name *",
          tagline: "Sibling Motto / Tagline Banner *",
          note: "Shagun Envelope Message / Slogan *",
          photo: "Brother / Sister's Profile Photo 🖼️"
        }
      },
      'raksha_bandhan_festive_light': {
        hasLetters: false, hasTokens: false,
        dashTitleSuffix: "'s Rakhi Dashboard",
        labels: {
          name: "Brother / Sister's First Name *",
          tagline: "Sibling Motto / Tagline Banner *",
          note: "Shagun Envelope Message / Slogan *",
          photo: "Brother / Sister's Profile Photo 🖼️"
        }
      }
    };

    function applyThemeVisibility(templateId) {
      // Use a safe fallback — never fall back to anniversary_reveal to avoid cross-contamination
      const config = THEME_FEATURES[templateId] || { hasLetters: false, hasTokens: false };
      
      const tabLetters = document.getElementById('tabBtn-letters');
      const tabTokens = document.getElementById('tabBtn-tokens');
      
      if (tabLetters) tabLetters.style.display = config.hasLetters ? 'inline-block' : 'none';
      if (tabTokens) tabTokens.style.display = config.hasTokens ? 'inline-block' : 'none';
      
      const contentLetters = document.getElementById('tabContent-letters');
      const contentTokens = document.getElementById('tabContent-tokens');
      if (!config.hasLetters && contentLetters && !contentLetters.classList.contains('hidden')) {
          switchTab('general');
      }
      if (!config.hasTokens && contentTokens && !contentTokens.classList.contains('hidden')) {
          switchTab('general');
      }
    }

    let activeToken = "<?php echo htmlspecialchars($token); ?>";

    async function handleBuyerLogin(e) {
      e.preventDefault();
      const btn = document.getElementById('loginBtn');
      const msg = document.getElementById('loginMsg');
      const email = document.getElementById('loginEmail').value;
      const pass = document.getElementById('loginPassword').value;

      btn.innerText = 'Verifying Login...';
      btn.disabled = true;
      msg.classList.add('hidden');

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/buyer_login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email, password: pass })
        });
        const data = await res.json();

        if (data.success) {
          const totalItems = (data.pages ? data.pages.length : 0) + (data.pending_orders ? data.pending_orders.length : 0);
          if (data.pending_orders && data.pending_orders.length > 0 && (!data.pages || data.pages.length === 0)) {
            window.location.href = data.pending_orders[0].redirect_url;
          } else if (data.redirect_url) {
            window.location.href = data.redirect_url;
          } else if (totalItems > 1) {
            document.getElementById('loginView').classList.add('hidden');
            document.getElementById('dashboardView').classList.add('hidden');
            renderPurchasedGiftsHub(data.pages, data.pending_orders);
            document.getElementById('hubView').classList.remove('hidden');
          } else {
            window.location.href = '<?php echo APP_URL; ?>/edit.php';
          }
        } else {
          msg.classList.remove('hidden');
          msg.innerText = data.message;
        }
      } catch (err) {
        msg.classList.remove('hidden');
        msg.innerText = 'Error: ' + err.message;
      } finally {
        btn.innerText = 'Log In To Live Visual Editor';
        btn.disabled = false;
      }
    }

    async function handleBuyerLogout() {
      try {
        await fetch('<?php echo APP_URL; ?>/api/buyer_logout.php', { method: 'POST' });
      } catch (e) {}
      window.location.href = '<?php echo APP_URL; ?>/edit.php';
    }

    let dashMusicSearchTimer = null;
    let dashCurrentMusicUrl = '';
    let dashCurrentSongTitle = '';
    let dashCurrentArtist = '';

    function toggleDashMusicMode(mode) {
      const itunesBox = document.getElementById('dashItunesSearchBox');
      const ytBox = document.getElementById('dashYoutubeLinkBox');
      const randBox = document.getElementById('dashRandomSingerBox');

      if (itunesBox) itunesBox.classList.add('hidden');
      if (ytBox) ytBox.classList.add('hidden');
      if (randBox) randBox.classList.add('hidden');

      if (mode === 'itunes_search' && itunesBox) itunesBox.classList.remove('hidden');
      if (mode === 'youtube_link' && ytBox) ytBox.classList.remove('hidden');
      if (mode === 'random_singer' && randBox) randBox.classList.remove('hidden');
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function cleanAttrStr(str) {
      if (!str) return '';
      return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function handleDashItunesSearch(query) {
      clearTimeout(dashMusicSearchTimer);
      const resultsContainer = document.getElementById('dashItunesResultsList');
      if (!query.trim()) {
        resultsContainer.classList.add('hidden');
        return;
      }

      dashMusicSearchTimer = setTimeout(async () => {
        try {
          resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-[#eac34a]">Searching iTunes Music Database...</div>';
          resultsContainer.classList.remove('hidden');

          const res = await fetch(`https://itunes.apple.com/search?term=${encodeURIComponent(query)}&entity=song&limit=8`);
          const data = await res.json();

          if (data.results && data.results.length > 0) {
            resultsContainer.innerHTML = data.results.map(item => `
              <div class="p-2.5 bg-[#151215] hover:bg-[#221f21] rounded-xl flex items-center justify-between border border-[#4d444b]/50 transition-all">
                <div class="flex items-center gap-3 overflow-hidden">
                  <img src="${item.artworkUrl60 || item.artworkUrl100}" class="w-10 h-10 rounded-lg object-cover border border-[#4d444b] shrink-0">
                  <div class="truncate">
                    <span class="block font-bold text-xs text-[#e8e0e3] truncate">${escapeHtml(item.trackName)}</span>
                    <span class="block text-[10px] text-[#d0c3cb] truncate">🎤 ${escapeHtml(item.artistName)} • ${escapeHtml(item.collectionName || '')}</span>
                  </div>
                </div>
                <button type="button" onclick="selectDashItunesTrack('${cleanAttrStr(item.previewUrl)}', '${cleanAttrStr(item.trackName)}', '${cleanAttrStr(item.artistName)}', '${cleanAttrStr(item.artworkUrl100)}')" class="px-3 py-1.5 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-[11px] rounded-lg border border-[#eac34a]/40 shrink-0 transition-all cursor-pointer">
                  + Select
                </button>
              </div>
            `).join('');
          } else {
            resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-[#d0c3cb]">No songs found. Try typing artist name or song title.</div>';
          }
        } catch (err) {
          resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-rose-400">Search error: ' + escapeHtml(err.message) + '</div>';
        }
      }, 350);
    }

    function selectDashItunesTrack(previewUrl, trackName, artistName, imgUrl) {
      dashCurrentMusicUrl = previewUrl;
      dashCurrentSongTitle = trackName;
      dashCurrentArtist = artistName;

      document.getElementById('dashSelectedTrackImg').src = imgUrl || 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80';
      document.getElementById('dashSelectedTrackTitle').innerText = trackName;
      document.getElementById('dashSelectedTrackArtist').innerText = 'Artist: ' + artistName;
      document.getElementById('bgMusicUrl').value = previewUrl;

      document.getElementById('dashItunesResultsList').classList.add('hidden');
    }

    async function loadDashboardData(token) {
      token = token || activeToken || document.getElementById('activeEditToken')?.value;
      if (!token) return;

      activeToken = token;
      const activeInput = document.getElementById('activeEditToken');
      if (activeInput) activeInput.value = token;

      document.getElementById('loginView')?.classList.add('hidden');
      document.getElementById('dashboardView')?.classList.remove('hidden');

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/edit_page.php?token=' + encodeURIComponent(token));
        const data = await res.json();

        if (data.success) {
          const p = data.page;
          document.getElementById('activeTemplateId').value = p.template_id;
          applyThemeVisibility(p.template_id);
          document.getElementById('partnerName').value = p.partner_name || '';
          document.getElementById('hintQuestion').value = p.hint_question || '';
          if (document.getElementById('secHintQuestion')) document.getElementById('secHintQuestion').value = p.hint_question || '';
          document.getElementById('loveNoteText').value = p.love_note_text || '';
          document.getElementById('taglineQuote').value = p.tagline_quote || (p.template_id === 'birthday_magic' ? 'Cheers to another year of awesome memories! 🥂' : (p.template_id === 'long_distance_love' ? 'Miles apart but connected by heart ✈️' : 'Safar Khubsurat h manjil se bhi 🌹'));
          // Bind Music Engine State from Database
          const songTitle = p.song_title || 'Tum Hi Ho';
          const songArtist = p.song_artist || 'Arijit Singh';
          const musicUrl = p.bg_music_url || '';

          dashCurrentSongTitle = songTitle;
          dashCurrentArtist = songArtist;
          dashCurrentMusicUrl = musicUrl;

          const isYt = musicUrl.includes('youtube.com') || musicUrl.includes('youtu.be');
          if (document.getElementById('bgMusicUrl')) document.getElementById('bgMusicUrl').value = musicUrl;
          if (isYt) {
            toggleDashMusicMode('youtube_link');
            const ytRadio = document.querySelector('input[name="dash_music_mode"][value="youtube_link"]');
            if (ytRadio) ytRadio.checked = true;
            document.getElementById('dashYoutubeUrlInput').value = musicUrl;
          } else {
            toggleDashMusicMode('itunes_search');
            const itunesRadio = document.querySelector('input[name="dash_music_mode"][value="itunes_search"]');
            if (itunesRadio) itunesRadio.checked = true;

            document.getElementById('dashSelectedTrackTitle').innerText = songTitle;
            document.getElementById('dashSelectedTrackArtist').innerText = 'Artist: ' + songArtist;
          }

          document.getElementById('viewLivePageBtn').href = data.share_url;
          const dashWaBtn = document.getElementById('dashWaShareBtn');
          if (dashWaBtn) {
            dashWaBtn.href = generateWhatsAppShareUrl(p.template_id, p.partner_name, data.share_url);
          }
          const schema = THEME_FEATURES[p.template_id] || { dashTitleSuffix: "'s Gift Dashboard", labels: {} };
          const defaultName = p.template_id && p.template_id.includes('raksha_bandhan') ? 'Sibling' : 'Partner';
          document.getElementById('dashPartnerTitle').innerText = (p.partner_name || defaultName) + schema.dashTitleSuffix;

          // Update Partner Photo Avatar Manager UI
          updatePartnerPhotoAvatar(p.receiver_photo, p.partner_name);

          // Render Template Isolated Theme Tab
          renderThemeContainer(p, data);

          // Render Photos
          renderPhotosList(data.media || []);

          // Render Letters
          const letters = p.letters_json ? JSON.parse(p.letters_json) : [];
          renderLettersList(letters);

          // Render Tokens
          const tokens = p.tokens_json ? JSON.parse(p.tokens_json) : [];
          renderTokensList(tokens);

          // Store buyer_pages list if returned
          if (data.buyer_pages && Array.isArray(data.buyer_pages)) {
            allBuyerPages = data.buyer_pages;
            const backBtn = document.getElementById('backToHubBtn');
            if (backBtn) {
              if (allBuyerPages.length > 1) {
                backBtn.classList.remove('hidden');
              } else {
                backBtn.classList.add('hidden');
              }
            }
          }

          lucide.createIcons();
        } else {
          const msg = document.getElementById('loginMsg');
          document.getElementById('loginView').classList.remove('hidden');
          document.getElementById('dashboardView').classList.add('hidden');
          msg.classList.remove('hidden');
          msg.innerText = data.message || 'Invalid or expired edit link token';
        }
      } catch (err) {
        console.error(err);
      }
    }

    let allBuyerPages = [];
    let allPendingOrders = [];

    function renderPurchasedGiftsHub(pages, pendingOrders) {
      allBuyerPages = pages || allBuyerPages || [];
      allPendingOrders = pendingOrders || allPendingOrders || [];
      const grid = document.getElementById('hubGiftsGrid');
      const backBtn = document.getElementById('backToHubBtn');

      const totalItems = allBuyerPages.length + allPendingOrders.length;

      if (totalItems > 1 && backBtn) {
        backBtn.classList.remove('hidden');
      } else if (backBtn) {
        backBtn.classList.add('hidden');
      }

      if (!grid) return;

      const tplMeta = {
        'anniversary_reveal': { name: 'Anniversary Reveal', icon: 'heart', color: 'from-[#3b1e3b] via-[#261626] to-[#181118]' },
        'birthday_magic': { name: 'Birthday Magic', icon: 'sparkles', color: 'from-[#1e3b30] via-[#152821] to-[#101b17]' },
        'perfect_proposal': { name: 'Perfect Proposal', icon: 'heart-handshake', color: 'from-[#3b2d1e] via-[#271d14] to-[#17130e]' },
        'long_distance_love': { name: 'Long Distance Love', icon: 'globe', color: 'from-[#1e2a3b] via-[#141b27] to-[#0e121b]' },
        'raksha_bandhan_royal': { name: 'Raksha Bandhan Royal', icon: 'crown', color: 'from-[#3b2a1a] via-[#281c12] to-[#18110b]' },
        'raksha_bandhan_festive_light': { name: 'Raksha Bandhan Festive Light', icon: 'sun', color: 'from-[#3b1e28] via-[#28141c] to-[#180b11]' }
      };

      let pendingHtml = allPendingOrders.map(po => {
        const targetUrl = po.redirect_url || ('<?php echo APP_URL; ?>/create.php?order_id=' + encodeURIComponent(po.order_id));
        return `
          <div class="relative bg-gradient-to-br from-[#3b2a1a] via-[#281d12] to-[#1a140d] p-5 sm:p-6 rounded-3xl border-2 border-[#eac34a] shadow-[0_0_30px_rgba(234,195,74,0.22)] flex flex-col justify-between gap-5 transition-all hover:shadow-[0_0_35px_rgba(234,195,74,0.3)] overflow-hidden group">
            <!-- Glowing Floating Ribbon Badge -->
            <div class="absolute top-0 right-0 bg-gradient-to-l from-[#eac34a] via-[#f7d774] to-[#eac34a] text-[#241a00] font-extrabold text-[10px] uppercase tracking-wider px-3.5 py-1.5 rounded-bl-2xl shadow-lg flex items-center gap-1.5 z-10 border-b border-l border-[#241a00]/20">
              <i data-lucide="clock" class="w-3.5 h-3.5 text-[#241a00] shrink-0"></i>
              <span>Unfinished Gift • Action Required</span>
            </div>

            <div class="space-y-3.5 pt-4">
              <div class="space-y-1">
                <h3 class="text-xl sm:text-2xl font-bold font-serif text-[#e8e0e3] group-hover:text-[#eac34a] transition-colors pr-2">
                  ${escapeHtml(po.template_name || 'Surprise Gift Card')}
                </h3>
                <p class="text-xs text-[#d0c3cb]/90 leading-relaxed">
                  ✨ Payment verified! Tap below to add partner's name, photos, and secret hint password to launch page.
                </p>
              </div>
            </div>

            <div class="pt-3 border-t border-[#eac34a]/20 space-y-2">
              <a href="${targetUrl}" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#eac34a] via-[#f7d774] to-[#cca830] hover:brightness-110 text-[#241a00] font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-md whitespace-nowrap">
                <span>Customize Gift</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
              </a>
              <div class="text-center">
                <span class="text-[10px] text-[#eac34a]/80 font-mono bg-[#151215] px-2.5 py-0.5 rounded-lg border border-[#eac34a]/20 inline-block">
                  Order #${po.order_id}
                </span>
              </div>
            </div>
          </div>
        `;
      }).join('');

      let pagesHtml = allBuyerPages.map(p => {
        const meta = tplMeta[p.template_id] || { name: 'Gift Website', icon: 'gift', color: 'from-[#221f21] to-[#151215]' };
        const partner = p.partner_name || 'Partner';
        const shareUrl = '<?php echo APP_URL; ?>/gift/' + p.url_slug;

        return `
          <div class="bg-gradient-to-b ${meta.color} p-5 sm:p-6 rounded-3xl border border-[#4d444b]/60 hover:border-[#eac34a]/60 shadow-xl flex flex-col justify-between gap-4 transition-all hover:shadow-2xl hover:scale-[1.005] group">
            <div class="space-y-3">
              <div class="flex flex-wrap items-center justify-between gap-1.5 border-b border-[#4d444b]/30 pb-2.5">
                <span class="text-[10px] uppercase font-extrabold tracking-wider text-[#eac34a] bg-[#100d10] px-2.5 py-1 rounded-full border border-[#eac34a]/30 flex items-center gap-1.5 shrink min-w-0 max-w-[65%] truncate">
                  <i data-lucide="${meta.icon}" class="w-3.5 h-3.5 text-[#eac34a] shrink-0"></i>
                  <span class="truncate">${meta.name}</span>
                </span>
                <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#a4e4b9] bg-[#1e3b20] px-2.5 py-0.5 rounded-full border border-[#a4e4b9]/30 flex items-center gap-1 shrink-0">
                  <i data-lucide="check-circle-2" class="w-3 h-3 text-[#a4e4b9]"></i> Live Page
                </span>
              </div>

              <div class="space-y-1">
                <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">
                  Surprise Page for <span class="text-[#eac34a] font-serif">${escapeHtml(partner)}</span>
                </h3>
                <p class="text-xs text-[#d0c3cb]/80 flex items-center gap-1.5 pt-0.5">
                  <span class="text-[#d0c3cb]/60 font-semibold">Page Link:</span>
                  <a href="${shareUrl}" target="_blank" class="font-mono text-[#eac34a] bg-[#100d10] px-2 py-0.5 rounded-lg border border-[#eac34a]/20 hover:border-[#eac34a] hover:underline transition-all truncate max-w-[200px]">/gift/${p.url_slug}</a>
                </p>
              </div>
            </div>

            <div class="flex items-center gap-2 pt-2.5 border-t border-[#4d444b]/30">
              <button type="button" onclick="openSelectedGiftEditor('${p.edit_token}')" class="flex-1 py-2.5 px-3.5 rounded-xl bg-gradient-to-r from-[#eac34a] via-[#f7d774] to-[#cca830] hover:brightness-110 text-[#241a00] font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-md whitespace-nowrap">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit</span>
              </button>
              <a href="${shareUrl}" target="_blank" class="py-2.5 px-3.5 rounded-xl bg-[#100d10] hover:bg-[#3b1e3b] text-[#e8e0e3] border border-[#4d444b] hover:border-[#eac34a]/60 font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shrink-0 shadow-md whitespace-nowrap">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>View</span>
              </a>
            </div>
          </div>
        `;
      }).join('');

      grid.innerHTML = pendingHtml + pagesHtml;

      if (typeof lucide === 'object') lucide.createIcons();
    }

    function showHubView() {
      const loginView = document.getElementById('loginView');
      const hubView = document.getElementById('hubView');
      const dashView = document.getElementById('dashboardView');

      if (loginView) loginView.classList.add('hidden');
      if (dashView) dashView.classList.add('hidden');
      if (hubView) hubView.classList.remove('hidden');

      if (allBuyerPages.length > 0) {
        renderPurchasedGiftsHub(allBuyerPages);
      }
    }

    async function openSelectedGiftEditor(token) {
      if (!token) return;
      activeToken = token;
      document.getElementById('activeEditToken').value = token;

      const loginView = document.getElementById('loginView');
      const hubView = document.getElementById('hubView');
      const dashView = document.getElementById('dashboardView');

      if (loginView) loginView.classList.add('hidden');
      if (hubView) hubView.classList.add('hidden');
      if (dashView) dashView.classList.remove('hidden');

      loadDashboardData(token);
    }

    function normalizeMediaUrlJs(url) {
      if (!url) return '';
      if (url.startsWith('data:image')) return url;

      const uploadIdx = url.indexOf('uploads/');
      if (uploadIdx !== -1) {
        return '<?php echo APP_URL; ?>/' + url.substring(uploadIdx);
      }
      if (url.startsWith('http://') || url.startsWith('https://')) {
        return url;
      }
      return '<?php echo APP_URL; ?>/' + url.replace(/^\/+/, '');
    }

    function updatePartnerPhotoAvatar(photoUrl, partnerName) {
      const container = document.getElementById('partnerAvatarContainer');
      const hiddenInput = document.getElementById('receiverPhotoUrl');
      const removeBtn = document.getElementById('removePhotoBtn');

      const nameChar = (partnerName || 'P').charAt(0).toUpperCase();
      const isValidPhoto = photoUrl && typeof photoUrl === 'string' && photoUrl.trim() !== '' && photoUrl !== 'null' && photoUrl !== 'undefined';

      if (isValidPhoto) {
        const cleanUrl = photoUrl.trim();
        if (hiddenInput) hiddenInput.value = cleanUrl;
        const fullUrl = normalizeMediaUrlJs(cleanUrl);

        if (container) {
          container.innerHTML = `
            <img src="${fullUrl}" alt="${nameChar}" class="w-full h-full object-cover rounded-full" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
            <span style="display:none;" class="w-full h-full flex items-center justify-center font-bold text-2xl text-[#eac34a] bg-[#3b1e3b] rounded-full">${nameChar}</span>
          `;
        }

        if (removeBtn) {
          removeBtn.classList.remove('hidden');
          removeBtn.classList.add('flex');
        }
      } else {
        if (hiddenInput) hiddenInput.value = '';
        if (container) {
          container.innerHTML = `<span class="w-full h-full flex items-center justify-center font-bold text-2xl text-[#eac34a] bg-[#3b1e3b] rounded-full">${nameChar}</span>`;
        }
        if (removeBtn) {
          removeBtn.classList.add('hidden');
          removeBtn.classList.remove('flex');
        }
      }
    }

    let cropImg = null;
    let cropScale = 1;
    let cropOffsetX = 0;
    let cropOffsetY = 0;
    let isDraggingCrop = false;
    let startDragX = 0;
    let startDragY = 0;

    async function handleDashPhotoSelect(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        try {
          const compressedDataUrl = typeof compressImage === 'function' 
            ? await compressImage(file, 1200, 1200, 0.82) 
            : await new Promise(resolve => {
                const r = new FileReader();
                r.onload = e => resolve(e.target.result);
                r.readAsDataURL(file);
              });
          
          cropImg = new Image();
          cropImg.onload = function() {
            openCircleCropModal();
          };
          cropImg.src = compressedDataUrl;
        } catch (err) {
          console.error('Image compression error:', err);
        }
      }
    }

    function openCircleCropModal() {
      if (!cropImg) return;
      cropScale = 1;
      cropOffsetX = 0;
      cropOffsetY = 0;
      document.getElementById('cropZoomRange').value = 1;
      document.getElementById('circleCropModal').classList.remove('hidden');
      setupCropCanvasEvents();
      updateCropCanvas();
      lucide.createIcons();
    }

    function closeCircleCropModal() {
      document.getElementById('circleCropModal').classList.add('hidden');
      document.getElementById('dashPhotoInput').value = '';
    }

    function updateCropCanvas() {
      const canvas = document.getElementById('cropCanvas');
      if (!canvas || !cropImg) return;
      const ctx = canvas.getContext('2d');
      const zoom = parseFloat(document.getElementById('cropZoomRange').value || 1);

      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const baseScale = Math.max(canvas.width / cropImg.width, canvas.height / cropImg.height);
      const drawWidth = cropImg.width * baseScale * zoom;
      const drawHeight = cropImg.height * baseScale * zoom;

      const drawX = (canvas.width - drawWidth) / 2 + cropOffsetX;
      const drawY = (canvas.height - drawHeight) / 2 + cropOffsetY;

      ctx.drawImage(cropImg, drawX, drawY, drawWidth, drawHeight);
    }

    function setupCropCanvasEvents() {
      const wrapper = document.getElementById('cropCanvasWrapper');
      if (!wrapper || wrapper.dataset.bound) return;
      wrapper.dataset.bound = "true";

      const startDrag = (e) => {
        isDraggingCrop = true;
        const pageX = e.touches ? e.touches[0].pageX : e.pageX;
        const pageY = e.touches ? e.touches[0].pageY : e.pageY;
        startDragX = pageX - cropOffsetX;
        startDragY = pageY - cropOffsetY;
      };

      const moveDrag = (e) => {
        if (!isDraggingCrop) return;
        const pageX = e.touches ? e.touches[0].pageX : e.pageX;
        const pageY = e.touches ? e.touches[0].pageY : e.pageY;
        cropOffsetX = pageX - startDragX;
        cropOffsetY = pageY - startDragY;
        updateCropCanvas();
      };

      const stopDrag = () => {
        isDraggingCrop = false;
      };

      wrapper.addEventListener('mousedown', startDrag);
      window.addEventListener('mousemove', moveDrag);
      window.addEventListener('mouseup', stopDrag);

      wrapper.addEventListener('touchstart', startDrag, { passive: true });
      window.addEventListener('touchmove', moveDrag, { passive: true });
      window.addEventListener('touchend', stopDrag);
    }

    function applyCircleCrop() {
      if (!cropImg) return;

      const outCanvas = document.createElement('canvas');
      outCanvas.width = 400;
      outCanvas.height = 400;
      const ctx = outCanvas.getContext('2d');

      const srcCanvas = document.getElementById('cropCanvas');
      ctx.drawImage(srcCanvas, 0, 0, 400, 400);

      // Export compressed JPEG (~35KB) to completely eliminate MySQL max_allowed_packet error!
      const croppedDataUrl = outCanvas.toDataURL('image/jpeg', 0.85);

      const partnerName = document.getElementById('partnerName').value || 'Partner';
      updatePartnerPhotoAvatar(croppedDataUrl, partnerName);
      closeCircleCropModal();
    }

    function removeDashPhoto() {
      const partnerName = document.getElementById('partnerName').value || 'Partner';
      document.getElementById('dashPhotoInput').value = '';
      updatePartnerPhotoAvatar('', partnerName);
    }

    function renderThemeContainer(p, data) {
      const templateId = p.template_id || '';
      const themeContainer = document.getElementById('themeContainer');
      const badge = document.getElementById('activePlanBadge');
      const tabBtn = document.getElementById('tabBtn-theme');
      const tabBtnLetters = document.getElementById('tabBtn-letters');
      const tabBtnTokens = document.getElementById('tabBtn-tokens');

      const nameLabel = document.getElementById('partnerNameLabel');
      const taglineLabel = document.getElementById('taglineQuoteLabel');
      const noteLabel = document.getElementById('loveNoteLabel');
      const taglineInput = document.getElementById('taglineQuote');

      const config = THEME_FEATURES[templateId] || { dashTitleSuffix: "'s Gift Dashboard", labels: {} };
      
      if (nameLabel && config.labels.name) nameLabel.innerText = config.labels.name;
      if (taglineLabel && config.labels.tagline) taglineLabel.innerText = config.labels.tagline;
      if (noteLabel && config.labels.note) noteLabel.innerText = config.labels.note;
      
      const photoLabel = document.getElementById('partnerPhotoLabel');
      if (photoLabel && config.labels.photo) photoLabel.innerText = config.labels.photo;

      if (templateId === 'birthday_magic') {
        badge.innerText = '✨ Managing: Birthday Magic Plan (Active)';
        tabBtn.innerText = 'Birthday & Reasons';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🎂 Birthday Settings &amp; Reasons</h3>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Date of Birth (Next Birthday Countdown)</label>
              <input type="date" id="partnerDob" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" value="${p.partner_dob || ''}">
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-[#4d444b]/30">
              <label class="block font-semibold text-[#d0c3cb]">Reasons I Love Celebrating You (Dynamic List)</label>
              <button type="button" onclick="addReasonRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Reason</button>
            </div>
            <div id="editReasonsList" class="space-y-2"></div>
          </div>
        `;
        renderReasonsList(data.reasons || []);

      } else if (templateId === 'perfect_proposal') {
        badge.innerText = '✨ Managing: Perfect Proposal Plan (Active)';
        tabBtn.innerText = 'Love Letter & Answer';

        const resp = data.proposal_response;
        const answerStatusHtml = resp ? `
          <div class="space-y-4 pt-1">
            <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center mx-auto shadow-lg">
              <i data-lucide="check-circle-2" class="w-7 h-7 text-[#eac34a]"></i>
            </div>
            <div class="space-y-1">
              <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#eac34a]">Partner's Answer 💕</span>
              <h4 class="text-2xl font-bold font-serif text-[#e8e0e3]">
                ${resp.response === 'yes' ? 'YES! A Thousand Times Yes 💍' : "Let's Talk 💕"}
              </h4>
              <p class="text-xs text-[#d0c3cb]/80">Responded on ${resp.responded_at_formatted || 'Recently'}</p>
            </div>
            <div class="bg-[#100d10] p-4 rounded-2xl border border-[#4d444b] text-sm font-serif italic text-[#eac34a] max-w-md mx-auto shadow-inner">
              "${resp.partner_note ? resp.partner_note : (resp.response === 'yes' ? 'YES! A thousand times YES my love! 💕' : 'Let\'s talk and celebrate together! 💕')}"
            </div>
          </div>
        ` : `
          <div class="space-y-2 py-4">
            <i data-lucide="clock" class="w-8 h-8 text-[#d0c3cb]/60 mx-auto"></i>
            <h4 class="text-sm font-bold text-[#e8e0e3]">Partner Has Not Answered Yet</h4>
            <p class="text-xs text-[#d0c3cb]/70">When your partner unlocks their surprise page and clicks "YES!" or "Let's Talk", their answer will appear here in real-time!</p>
          </div>
        `;

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">💍 Proposal Love Letter &amp; Live Answer Status</h3>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Full Proposal Love Letter Centerpiece</label>
              <textarea id="loveLetterText" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3]" rows="6">${p.love_letter_text || ''}</textarea>
            </div>
            <div class="bg-[#151215] p-5 rounded-2xl border border-[#eac34a]/40 text-center space-y-2">
              <span class="text-[10px] uppercase font-bold text-[#eac34a] block tracking-widest">LIVE PROPOSAL RESPONSE STATUS</span>
              ${answerStatusHtml}
            </div>
          </div>
        `;

      } else if (templateId === 'long_distance_love') {
        badge.innerText = '✨ Managing: Long Distance Love Plan (Active)';
        tabBtn.innerText = 'Cities & Reunion Date';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🌍 Dual Cities, Clocks &amp; Reunion Date</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Buyer City Name</label>
                <input type="text" id="buyerCity" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" placeholder="e.g. London" value="${p.buyer_city || ''}">
              </div>
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Partner City Name</label>
                <input type="text" id="partnerCity" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" placeholder="e.g. Bangalore" value="${p.partner_city || ''}">
              </div>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Next Reunion Date (Live Countdown)</label>
              <input type="date" id="reunionDate" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" value="${p.reunion_date || ''}">
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Shared Spotify / Music Playlist URL</label>
              <input type="url" id="playlistUrl" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" placeholder="https://open.spotify.com/playlist/..." value="${p.playlist_url || ''}">
            </div>
          </div>
        `;

      } else if (templateId && templateId.includes('raksha_bandhan')) {
        badge.innerText = '✨ Managing: Raksha Bandhan Special Plan (Active)';
        tabBtn.innerText = 'Sibling Promises & Vows';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">5 Sibling Promises &amp; Protection Vows</h3>
              <p class="text-[11px] text-[#d0c3cb] mt-0.5">Customize your 5 personal sibling promises displayed on the gift cards.</p>
            </div>
            <div id="editReasonsList" class="space-y-3"></div>
          </div>
        `;
        renderReasonsList(data.reasons || []);
      } else if (templateId === 'anniversary_reveal') {
        // Explicit anniversary_reveal — Story Milestones + Relationship Date
        badge.innerText = '✨ Managing: Anniversary Reveal Plan (Active)';
        tabBtn.innerText = 'Story Milestones';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">📍 Story Road Milestones</h3>
              <button type="button" onclick="addMilestoneRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Milestone</button>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Relationship Start Date (Live Together Counter)</label>
              <input type="date" id="relationshipStartDate" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" value="${p.relationship_start_date || ''}">
            </div>
            <div id="editMilestonesList" class="space-y-3"></div>
          </div>
        `;
        renderMilestonesList(data.milestones || []);
      } else {
        // Safe fallback for any unknown/future template — show nothing harmful
        badge.innerText = '✨ Managing: Gift Plan (Active)';
        tabBtn.style.display = 'none'; // Hide theme tab if no specific fields apply
        themeContainer.innerHTML = `
          <div class="p-6 text-center text-xs text-[#d0c3cb]/60">
            <p>No additional template-specific settings for this plan.</p>
          </div>
        `;
      }
    }

    const DEFAULT_RAKHI_PROMISES = [
      'Always protect you and stand by your side 🛡️',
      'Keep all your deepest secrets safe 🤫',
      'Sponsor your favorite food and treat you 🍕',
      'Never let you feel alone, no matter where I am 💖',
      'Always be your forever crime partner 🕵️‍♂️'
    ];

    function renderReasonsList(reasons) {
      const container = document.getElementById('editReasonsList');
      if (!container) return;
      
      const activeTpl = document.getElementById('activeTemplateId')?.value || '';
      const isRakhi = activeTpl.includes('raksha_bandhan');

      if (isRakhi) {
        let list = Array.isArray(reasons) ? [...reasons] : [];
        while (list.length < 5) {
          list.push(DEFAULT_RAKHI_PROMISES[list.length]);
        }
        list = list.slice(0, 5);

        container.innerHTML = list.map((r, i) => `
          <div class="space-y-1">
            <label class="block text-[11px] font-bold text-[#eac34a] uppercase tracking-wider">Promise #${i + 1}</label>
            <input type="text" class="edit-reason-item w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" value="${r}" placeholder="Promise ${i + 1} e.g. ${DEFAULT_RAKHI_PROMISES[i]}">
          </div>
        `).join('');
      } else {
        if (!reasons || reasons.length === 0) {
          reasons = ['Your contagious smile', 'The way you care for everyone', 'Our hilarious inside jokes'];
        }
        container.innerHTML = reasons.map((r, i) => `
          <div class="flex items-center gap-2">
            <input type="text" class="edit-reason-item w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2.5 text-xs text-[#e8e0e3]" value="${r}" placeholder="Reason ${i + 1}">
          </div>
        `).join('');
      }
    }

    function addReasonRow() {
      const container = document.getElementById('editReasonsList');
      if (!container) return;
      const div = document.createElement('div');
      div.className = 'flex items-center gap-2';
      div.innerHTML = `<input type="text" class="edit-reason-item w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2.5 text-xs text-[#e8e0e3]" placeholder="New reason to celebrate">`;
      container.appendChild(div);
    }

    function switchTab(tabName) {
      const activeTemplateId = document.getElementById('activeTemplateId')?.value;

      ['general', 'theme', 'photos', 'letters', 'tokens', 'security'].forEach(t => {
        const btn = document.getElementById('tabBtn-' + t);
        const content = document.getElementById('tabContent-' + t);
        if (!btn || !content) return;

        if (t === tabName) {
          btn.className = "px-4 py-2.5 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] shadow-[0_0_15px_rgba(234,195,74,0.3)] transition-all whitespace-nowrap shrink-0 cursor-pointer";
          content.classList.remove('hidden');
        } else {
          btn.className = "px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white hover:border-[#eac34a]/40 transition-all whitespace-nowrap shrink-0 cursor-pointer";
          content.classList.add('hidden');
        }
      });

      // Enforce strict hidden state for letters & tokens on non-anniversary themes
      if (activeTemplateId && activeTemplateId !== 'anniversary_reveal') {
        const tabBtnLetters = document.getElementById('tabBtn-letters');
        const tabBtnTokens = document.getElementById('tabBtn-tokens');
        if (tabBtnLetters) tabBtnLetters.classList.add('hidden');
        if (tabBtnTokens) tabBtnTokens.classList.add('hidden');
      }
    }

    function renderMilestonesList(milestones) {
      const container = document.getElementById('editMilestonesList');
      if (!container) return;
      if (milestones.length === 0) {
        milestones = [{ title: '', milestone_date: '', description: '' }];
      }

      container.innerHTML = milestones.map((m, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2 relative group">
          <input type="text" class="edit-m-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Milestone Title" value="${m.title || ''}">
          <input type="date" class="edit-m-date w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" value="${m.milestone_date || m.date || ''}">
          <input type="text" class="edit-m-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description" value="${m.description || ''}">
        </div>
      `).join('');
    }

    function addMilestoneRow() {
      const container = document.getElementById('editMilestonesList');
      if (!container) return;
      const div = document.createElement('div');
      div.className = 'bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2';
      div.innerHTML = `
        <input type="text" class="edit-m-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="New Milestone Title">
        <input type="date" class="edit-m-date w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]">
        <input type="text" class="edit-m-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description">
      `;
      container.appendChild(div);
    }

    let dashPhotosList = [];

    const SAMPLE_SCRAPBOOK_PHOTOS = [
      { url: 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=600&q=80', caption: 'Our First Date ☕' },
      { url: 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=600&q=80', caption: 'Best Friends Forever 👫' },
      { url: 'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=600&q=80', caption: 'Together Always 💑' },
      { url: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=600&q=80', caption: 'Squad Goals 🌟' },
      { url: 'https://images.unsplash.com/photo-1494774157365-9e04c6720e47?auto=format&fit=crop&w=600&q=80', caption: 'A Beautiful Smile 😊' },
      { url: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80', caption: 'Always Stunning 💫' }
    ];

    const DEFAULT_PHOTO_CAPTIONS = [
      'Our Special Smile ✨',
      'Sweet Memories 💖',
      'Unforgettable Day 🌹',
      'Together Forever 💫',
      'Laughter & Joy 😄',
      'Pure Happiness 🌸',
      'Precious Moments 💎',
      'Golden Memories 🌟',
      'Forever & Always 💓',
      'Beautiful Journey ✈️',
      'Magical Evening 🌙',
      'Endless Sunshine ☀️',
      'Charming Smile 😊',
      'Soulmates Vow 💍',
      'Heartfelt Hugs 🤗',
      'Starry Night ✨',
      'Sunset Kiss 🌅',
      'Warm Embrace 🤗',
      'Fun & Laughter 🎉',
      'Treasured Days 🏆',
      'Romantic Stroll 🚶‍♂️🚶‍♀️',
      'Candlelight Vibe 🕯️',
      'Love in the Air 💞',
      'Special Birthday 🎂',
      'Best Day Ever 🥳',
      'Lifetime Memories ♾️'
    ];

    function renderPhotosList(media) {
      if (Array.isArray(media)) {
        dashPhotosList = media.map((m, idx) => {
          const defaultCap = DEFAULT_PHOTO_CAPTIONS[idx % DEFAULT_PHOTO_CAPTIONS.length];
          if (typeof m === 'string') return { url: m, caption: defaultCap };
          return { url: m.file_path || '', caption: m.caption || defaultCap };
        });
      } else {
        dashPhotosList = [];
      }
      renderDashScrapbookPhotos();
    }

    function updateDashPhotoCaption(index, val) {
      if (dashPhotosList[index]) {
        dashPhotosList[index].caption = val;
      }
    }

    let dragSourceIdx = null;
    let touchTimer = null;
    let isTouchDragging = false;
    let touchTargetIdx = null;

    function handlePhotoDragStart(e, idx, mode) {
      dragSourceIdx = idx;
      if (e.dataTransfer) e.dataTransfer.effectAllowed = 'move';
      if (e.currentTarget) e.currentTarget.style.opacity = '0.4';
    }

    function handlePhotoDragOver(e) {
      e.preventDefault();
      if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
    }

    function handlePhotoDrop(e, targetIdx, mode) {
      e.preventDefault();
      if (dragSourceIdx === null || dragSourceIdx === targetIdx) return;
      
      let list = (mode === 'edit') ? dashPhotosList : (typeof selectedPhotoObjects !== 'undefined' ? selectedPhotoObjects : []);
      if (dragSourceIdx >= 0 && dragSourceIdx < list.length && targetIdx >= 0 && targetIdx < list.length) {
        const [movedItem] = list.splice(dragSourceIdx, 1);
        list.splice(targetIdx, 0, movedItem);
        if (mode === 'edit') {
          renderDashScrapbookPhotos();
        } else if (typeof renderPhotoPicker === 'function') {
          renderPhotoPicker();
        }
      }
    }

    function handlePhotoDragEnd(e) {
      dragSourceIdx = null;
      if (e.currentTarget) e.currentTarget.style.opacity = '1';
    }

    function handlePhotoTouchStart(e, idx, mode) {
      dragSourceIdx = idx;
      touchTargetIdx = idx;
      isTouchDragging = false;
      const elem = e.currentTarget;
      
      touchTimer = setTimeout(() => {
        isTouchDragging = true;
        if (elem) {
          elem.classList.add('ring-2', 'ring-[#eac34a]', 'scale-95');
          elem.style.opacity = '0.6';
        }
        if (navigator.vibrate) navigator.vibrate(40);
      }, 250);
    }

    function handlePhotoTouchMove(e, mode) {
      if (!isTouchDragging) {
        clearTimeout(touchTimer);
        return;
      }
      if (e.cancelable) e.preventDefault();
      const touch = e.touches[0];
      const targetElem = document.elementFromPoint(touch.clientX, touch.clientY);
      if (targetElem) {
        const card = targetElem.closest('[data-photo-index]');
        if (card && card.getAttribute('data-photo-index') !== null) {
          touchTargetIdx = parseInt(card.getAttribute('data-photo-index'), 10);
        }
      }
    }

    function handlePhotoTouchEnd(e, mode) {
      clearTimeout(touchTimer);
      if (isTouchDragging && dragSourceIdx !== null && touchTargetIdx !== null && dragSourceIdx !== touchTargetIdx) {
        let list = (mode === 'edit') ? dashPhotosList : (typeof selectedPhotoObjects !== 'undefined' ? selectedPhotoObjects : []);
        if (dragSourceIdx >= 0 && dragSourceIdx < list.length && touchTargetIdx >= 0 && touchTargetIdx < list.length) {
          const [movedItem] = list.splice(dragSourceIdx, 1);
          list.splice(touchTargetIdx, 0, movedItem);
          if (mode === 'edit') {
            renderDashScrapbookPhotos();
          } else if (typeof renderPhotoPicker === 'function') {
            renderPhotoPicker();
          }
        }
      }
      dragSourceIdx = null;
      touchTargetIdx = null;
      isTouchDragging = false;
    }

    function renderDashScrapbookPhotos() {
      const container = document.getElementById('dashScrapbookContainer');
      const countLabel = document.getElementById('dashSelectedPhotoCount');
      const sampleGrid = document.getElementById('dashSamplePhotosGrid');
      if (!container) return;

      if (countLabel) countLabel.innerText = `Selected: ${dashPhotosList.length} / 25 Photos`;

      if (dashPhotosList.length === 0) {
        container.innerHTML = `
          <div class="col-span-full py-8 text-center space-y-2">
            <i data-lucide="image" class="w-8 h-8 text-[#d0c3cb]/50 mx-auto"></i>
            <p class="text-xs text-[#d0c3cb]">No scrapbook photos uploaded yet. Click "Upload Photos" or pick from sample gallery below!</p>
          </div>
        `;
      } else {
        container.innerHTML = dashPhotosList.map((item, i) => `
          <div data-photo-index="${i}" draggable="true" 
               ondragstart="handlePhotoDragStart(event, ${i}, 'edit')" 
               ondragover="handlePhotoDragOver(event)" 
               ondrop="handlePhotoDrop(event, ${i}, 'edit')" 
               ondragend="handlePhotoDragEnd(event)"
               ontouchstart="handlePhotoTouchStart(event, ${i}, 'edit')"
               ontouchmove="handlePhotoTouchMove(event, 'edit')"
               ontouchend="handlePhotoTouchEnd(event, 'edit')"
               class="rounded-2xl overflow-hidden border border-[#4d444b] relative group bg-[#100d10] p-1.5 shadow-md hover:border-[#eac34a] transition-all flex flex-col cursor-grab active:cursor-grabbing select-none">
            <div class="relative w-full aspect-square rounded-xl overflow-hidden">
              <img src="${normalizeMediaUrlJs(item.url)}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=600&q=80'" class="w-full h-full object-cover pointer-events-none">
              <div class="absolute top-1.5 left-1.5 bg-black/70 text-[#eac34a] text-[10px] px-1.5 py-0.5 rounded-md font-mono flex items-center gap-1 backdrop-blur-sm pointer-events-none">
                <span>⠿</span>
                <span>#${i + 1}</span>
              </div>
              <button type="button" onclick="deleteDashPhoto(${i})" class="absolute top-2 right-2 bg-rose-900/90 hover:bg-rose-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shadow-lg transition-colors cursor-pointer border border-rose-400/40 z-10">
                ✕
              </button>
            </div>
            <input type="text" placeholder="✍️ Memory caption..." value="${escapeHtml(item.caption || '')}" oninput="updateDashPhotoCaption(${i}, this.value)" class="w-full bg-[#1b171b] border border-[#4d444b] rounded-lg px-2 py-1 text-[10px] text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none placeholder-[#8a7b85] mt-1.5">
          </div>
        `).join('');
      }

      if (sampleGrid) {
        fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php')
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success' && data.samples && data.samples.length > 0) {
              const top5 = data.samples.slice(0, 5);
              let html = top5.map(photo => {
                const isSel = dashPhotosList.some(p => p.url === photo.url);
                return `
                  <a href="javascript:void(0)" onclick="toggleSampleModalPhoto('${photo.url}', '${(photo.caption || 'Romantic Memory').replace(/'/g, "\\'")}')" class="aspect-square rounded-xl overflow-hidden border ${isSel ? 'border-[#eac34a] ring-2 ring-[#eac34a]/40' : 'border-[#4d444b]'} relative group cursor-pointer bg-[#100d10] hover:scale-105 transition-all block">
                    <img src="${photo.url}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=600&q=80'" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-black/70 backdrop-blur-sm text-white text-[9px] text-center py-0.5 px-1 truncate font-semibold">${photo.caption || 'Romantic Memory'}</div>
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center ${isSel ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'} transition-opacity">
                      <span class="px-2 py-1 rounded-md ${isSel ? 'bg-[#eac34a] text-[#241a00]' : 'bg-[#3b1e3b] text-[#eac34a]'} font-bold text-[10px] shadow-md">
                        ${isSel ? '✓ Added' : '+ Add'}
                      </span>
                    </div>
                  </a>
                `;
              }).join('');

              // 6th Item: View All Anchor Card
              html += `
                <a href="javascript:void(0)" onclick="openSampleLibraryModal()" class="aspect-square rounded-xl border border-[#eac34a]/60 bg-gradient-to-br from-[#3b1e3b] to-[#221f21] p-2 flex flex-col items-center justify-center text-center group cursor-pointer hover:scale-105 transition-all shadow-lg hover:border-[#eac34a]">
                  <i data-lucide="images" class="w-5 h-5 text-[#eac34a] mb-1 group-hover:scale-110 transition-transform"></i>
                  <span class="text-xs font-bold text-[#e8e0e3] group-hover:text-[#eac34a]">View All ➡️</span>
                  <span class="text-[9px] text-[#d0c3cb] mt-0.5">More Samples</span>
                </a>
              `;

              sampleGrid.innerHTML = html;
              if (typeof lucide === 'object') lucide.createIcons();
            }
          })
          .catch(() => {});
      }

      if (typeof lucide === 'object') lucide.createIcons();
    }

    function deleteDashPhoto(index) {
      dashPhotosList.splice(index, 1);
      renderDashScrapbookPhotos();
    }

    let cachedSamplePhotos = [];
    let currentSampleCategory = 'all';

    function filterSampleCategory(cat) {
      currentSampleCategory = cat;
      document.querySelectorAll('.sample-cat-pill').forEach(btn => {
        if (btn.dataset.cat === cat) {
          btn.className = 'sample-cat-pill px-3 py-1.5 rounded-full font-bold text-[11px] transition-all bg-[#eac34a] text-[#241a00] border border-[#eac34a] shadow-md cursor-pointer shrink-0';
        } else {
          btn.className = 'sample-cat-pill px-3 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0';
        }
      });
      renderSampleGrid();
    }

    function renderSampleGrid() {
      const modalGrid = document.getElementById('sampleModalGrid');
      const countLabel = document.getElementById('sampleModalCountLabel');
      if (!modalGrid) return;

      if (countLabel) countLabel.innerText = `Selected: ${dashPhotosList.length} / 25`;

      const filtered = cachedSamplePhotos.filter(s => currentSampleCategory === 'all' || s.category === currentSampleCategory);

      if (filtered.length === 0) {
        modalGrid.innerHTML = `<div class="col-span-full py-12 text-center text-xs text-[#d0c3cb]">No photos found in this category.</div>`;
        return;
      }

      modalGrid.innerHTML = filtered.map(sample => {
        const isSel = dashPhotosList.some(p => p.url === sample.url);
        return `
          <div onclick="toggleSampleModalPhoto('${sample.url}', '${(sample.caption || 'Romantic Memory').replace(/'/g, "\\'")}')" 
             class="sample-library-card ${isSel ? 'selected' : ''} group">
            <img src="${sample.url}" onerror="this.onerror=null; this.src='<?php echo APP_URL; ?>/assets/default_gallery/sample_fa6955df.webp';" class="sample-library-img">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/95 via-black/70 to-transparent text-white text-[10px] text-center pt-4 pb-1.5 px-1 truncate font-semibold z-10">
              ${sample.caption || 'Romantic Memory'}
            </div>
            ${isSel ? `
              <div class="absolute top-2 right-2 bg-[#eac34a] text-[#241a00] text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg flex items-center gap-1 z-20">
                ✓ Selected
              </div>
            ` : `
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-10">
                <span class="px-2.5 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/50 shadow-lg">+ Add Photo</span>
              </div>
            `}
          </div>
        `;
      }).join('');
    }

    async function openSampleLibraryModal() {
      const modal = document.getElementById('sampleLibraryModal');
      const countLabel = document.getElementById('sampleModalCountLabel');

      if (modal) modal.classList.remove('hidden');
      if (countLabel) countLabel.innerText = `Selected: ${dashPhotosList.length} / 25`;

      try {
        if (cachedSamplePhotos.length === 0) {
          const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php?user_mode=1');
          const data = await res.json();
          if (data.status === 'success' && data.samples.length > 0) {
            cachedSamplePhotos = data.samples;
          }
        }
        renderSampleGrid();
        if (typeof lucide === 'object') lucide.createIcons();
      } catch (err) {
        const modalGrid = document.getElementById('sampleModalGrid');
        if (modalGrid) modalGrid.innerHTML = `<div class="col-span-full py-10 text-center text-xs text-red-300">Error loading sample photos.</div>`;
      }
    }

    function toggleSampleModalPhoto(url, caption) {
      const idx = dashPhotosList.findIndex(p => p.url === url);
      if (idx >= 0) {
        dashPhotosList.splice(idx, 1);
      } else {
        if (dashPhotosList.length >= 25) {
          alert('⚠️ Maximum limit of 25 photos reached! Please remove a photo before adding more.');
          return;
        }
        dashPhotosList.push({ url: url, caption: caption || 'A Beautiful Memory' });
      }
      renderDashboardPhotos();
      renderSampleGrid();
    }

    function closeSampleLibraryModal() {
      const modal = document.getElementById('sampleLibraryModal');
      if (modal) modal.classList.add('hidden');
    }

    window.openSampleLibraryModal = openSampleLibraryModal;
    window.closeSampleLibraryModal = closeSampleLibraryModal;

    function toggleSampleModalPhoto(url, caption) {
      const idx = dashPhotosList.findIndex(p => p.url === url);
      if (idx > -1) {
        dashPhotosList.splice(idx, 1);
      } else {
        if (dashPhotosList.length >= 25) {
          alert('⚠️ Maximum limit of 25 gallery photos reached! Remove a photo to add more.');
          return;
        }
        dashPhotosList.push({ url: url, caption: caption || 'A Beautiful Memory' });
      }
      renderDashScrapbookPhotos();
      openSampleLibraryModal(); // Re-render modal selection state
    }

    function handleDashScrapbookFiles(e) {
      const files = e.target.files;
      if (!files || files.length === 0) return;

      Array.from(files).forEach(file => {
        if (dashPhotosList.length >= 25) {
          alert('⚠️ Maximum limit of 25 gallery photos reached! Please remove a photo before adding more.');
          return;
        }
        const reader = new FileReader();
        reader.onload = function(evt) {
          const tempImg = new Image();
          tempImg.onload = function() {
            if (dashPhotosList.length >= 25) {
              alert('⚠️ Maximum limit of 25 gallery photos reached!');
              return;
            }
            const canvas = document.createElement('canvas');
            const maxDim = 800;
            let w = tempImg.width;
            let h = tempImg.height;

            if (w > h && w > maxDim) {
              h = Math.round((h * maxDim) / w);
              w = maxDim;
            } else if (h > maxDim) {
              w = Math.round((w * maxDim) / h);
              h = maxDim;
            }

            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(tempImg, 0, 0, w, h);
            let compressedUrl = canvas.toDataURL('image/webp', 0.82);
            if (!compressedUrl.startsWith('data:image/webp')) {
              compressedUrl = canvas.toDataURL('image/jpeg', 0.85);
            }

            const nextCap = DEFAULT_PHOTO_CAPTIONS[dashPhotosList.length % DEFAULT_PHOTO_CAPTIONS.length];
            dashPhotosList.push({ url: compressedUrl, caption: nextCap });
            renderDashScrapbookPhotos();
          };
          tempImg.src = evt.target.result;
        };
        reader.readAsDataURL(file);
      });
      e.target.value = '';
    }

    function renderLettersList(letters) {
      const container = document.getElementById('editLettersList');
      if (letters.length === 0) {
        letters = [
          { title: 'The First Magical Spark', category: 'A Beautiful Beginning', content: 'My Dearest, I often find myself thinking back to the first moment...' },
          { title: 'Our Silent Sacred Promise', category: 'A Heartfelt Oath', content: 'Here is my little vow to you: I promise to stand by your side forever.' }
        ];
      }

      container.innerHTML = letters.map((l, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2">
          <input type="text" class="edit-l-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Letter Title" value="${l.title || ''}">
          <input type="text" class="edit-l-cat w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Category (e.g. A Heartfelt Oath)" value="${l.category || ''}">
          <textarea class="edit-l-content w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3]" rows="3" placeholder="Full Love Letter Text">${l.content || ''}</textarea>
        </div>
      `).join('');
    }

    function addLetterRow() {
      const container = document.getElementById('editLettersList');
      const div = document.createElement('div');
      div.className = 'bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2';
      div.innerHTML = `
        <input type="text" class="edit-l-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="New Letter Title">
        <input type="text" class="edit-l-cat w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Category e.g. A Quiet Memory">
        <textarea class="edit-l-content w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3]" rows="3" placeholder="Full Love Letter Text"></textarea>
      `;
      container.appendChild(div);
    }

    function renderTokensList(tokens) {
      const container = document.getElementById('editTokensList');
      if (tokens.length === 0) {
        tokens = [
          { title: '1 Free Warm Hug', description: 'Redeemable anytime for a long, tight hug when you need it most.', badge: 'Hug' },
          { title: 'Late Night Ice Cream Date', description: 'Redeemable for a midnight drive to your favorite ice cream parlor.', badge: 'Treat' }
        ];
      }

      container.innerHTML = tokens.map((t, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2">
          <input type="text" class="edit-t-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Token Title (e.g. 1 Free Warm Hug)" value="${t.title || ''}">
          <input type="text" class="edit-t-badge w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Badge e.g. Treat / Hug" value="${t.badge || ''}">
          <input type="text" class="edit-t-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description" value="${t.description || ''}">
        </div>
      `).join('');
    }

    function addTokenRow() {
      const container = document.getElementById('editTokensList');
      const div = document.createElement('div');
      div.className = 'bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2';
      div.innerHTML = `
        <input type="text" class="edit-t-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Token Title e.g. Movie Night Choice">
        <input type="text" class="edit-t-badge w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Badge e.g. Movie">
        <input type="text" class="edit-t-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description">
      `;
      container.appendChild(div);
    }

    async function saveDashboardChanges(e) {
      e.preventDefault();
      const btn = document.getElementById('saveChangesBtn');
      const msg = document.getElementById('saveMsg');

      btn.innerText = 'Saving Changes...';
      btn.disabled = true;
      msg.innerText = '';

      const letters = [];
      document.querySelectorAll('#editLettersList > div').forEach(div => {
        const title = div.querySelector('.edit-l-title')?.value;
        const cat = div.querySelector('.edit-l-cat')?.value;
        const content = div.querySelector('.edit-l-content')?.value;
        if (title) letters.push({ id: letters.length + 1, title: title, category: cat || 'Love Note', content: content || '' });
      });

      const tokens = [];
      document.querySelectorAll('#editTokensList > div').forEach(div => {
        const title = div.querySelector('.edit-t-title')?.value;
        const badge = div.querySelector('.edit-t-badge')?.value;
        const desc = div.querySelector('.edit-t-desc')?.value;
        if (title) tokens.push({ id: tokens.length + 1, title: title, badge: badge || 'Coupon', description: desc || '' });
      });

      const templateId = document.getElementById('activeTemplateId').value;
      const templateFields = {};

      if (templateId === 'birthday_magic' || templateId === 'raksha_bandhan_special' || templateId === 'raksha_bandhan_royal' || templateId === 'raksha_bandhan_festive_light') {
        const pDob = document.getElementById('partnerDob')?.value;
        const reasons = [];
        document.querySelectorAll('#editReasonsList .edit-reason-item').forEach(inp => {
          if (inp.value.trim()) reasons.push(inp.value.trim());
        });
        if (pDob) templateFields.partner_dob = pDob;
        templateFields.reasons = reasons;

      } else if (templateId === 'perfect_proposal') {
        templateFields.love_letter_text = document.getElementById('loveLetterText')?.value;

      } else if (templateId === 'long_distance_love') {
        templateFields.buyer_city = document.getElementById('buyerCity')?.value;
        templateFields.partner_city = document.getElementById('partnerCity')?.value;
        templateFields.reunion_date = document.getElementById('reunionDate')?.value;
        templateFields.playlist_url = document.getElementById('playlistUrl')?.value;

      } else {
        // anniversary_reveal
        templateFields.relationship_start_date = document.getElementById('relationshipStartDate')?.value;
        const milestones = [];
        document.querySelectorAll('#editMilestonesList > div').forEach(div => {
          const title = div.querySelector('.edit-m-title')?.value;
          const date = div.querySelector('.edit-m-date')?.value;
          const desc = div.querySelector('.edit-m-desc')?.value;
          if (title) milestones.push({ title: title, date: date || '', description: desc || '' });
        });
        templateFields.milestones = milestones;
      }

      const dashMusicMode = document.querySelector('input[name="dash_music_mode"]:checked')?.value || 'itunes_search';
      let finalBgMusicUrl = document.getElementById('bgMusicUrl').value;
      let finalSongTitle = dashCurrentSongTitle || 'Selected Song';
      let finalSongArtist = dashCurrentArtist || 'Artist';

      if (dashMusicMode === 'itunes_search') {
        finalBgMusicUrl = dashCurrentMusicUrl || finalBgMusicUrl;
      } else if (dashMusicMode === 'youtube_link') {
        finalBgMusicUrl = document.getElementById('dashYoutubeUrlInput').value.trim() || finalBgMusicUrl;
        finalSongTitle = 'Custom YouTube Song';
        finalSongArtist = 'YouTube';
      }

      templateFields.song_title = finalSongTitle;
      templateFields.song_artist = finalSongArtist;

      const currentPass = document.getElementById('currentBuyerPassword')?.value.trim() || '';
      const newPass = document.getElementById('newBuyerPassword')?.value.trim() || '';
      const confirmPass = document.getElementById('confirmBuyerPassword')?.value.trim() || '';

      if (currentPass || newPass || confirmPass) {
        if (!currentPass) {
          msg.innerText = '❌ Please enter your current account password.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
        if (!newPass) {
          msg.innerText = '❌ Please enter your new account password.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
        if (newPass !== confirmPass) {
          msg.innerText = '❌ New password and confirmation password do not match.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
        if (newPass.length < 4) {
          msg.innerText = '❌ New password must be at least 4 characters long.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
      }

      const payload = {
        token: activeToken,
        partner_name: document.getElementById('partnerName').value,
        hint_question: document.getElementById('hintQuestion').value,
        hint_answer: document.getElementById('hintAnswer').value,
        current_buyer_password: currentPass,
        new_buyer_password: newPass,
        love_note_text: document.getElementById('loveNoteText').value,
        tagline_quote: document.getElementById('taglineQuote').value,
        bg_music_url: finalBgMusicUrl,
        song_title: finalSongTitle,
        song_artist: finalSongArtist,
        receiver_photo: document.getElementById('receiverPhotoUrl').value,
        letters: letters,
        tokens: tokens,
        media_photos: dashPhotosList,
        template_fields: templateFields
      };

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/edit_page.php?token=' + encodeURIComponent(activeToken), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          msg.innerText = '✅ All changes saved successfully!';
          loadDashboardData(activeToken);
          if (newPass) {
            document.getElementById('currentBuyerPassword').value = '';
            document.getElementById('newBuyerPassword').value = '';
            document.getElementById('confirmBuyerPassword').value = '';
          }
        } else {
          msg.innerText = '❌ Error: ' + data.message;
        }
      } catch (err) {
        msg.innerText = '❌ Error: ' + err.message;
      } finally {
        btn.innerText = 'Save All Changes';
        btn.disabled = false;
      }
    }

    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4 text-[#eac34a]"></i>';
      } else {
        input.type = 'password';
        btn.innerHTML = '<i data-lucide="eye" class="w-4 h-4 text-[#d0c3cb]"></i>';
      }
      if (typeof lucide === 'object') lucide.createIcons();
    }

    let serverPages = <?php echo json_encode($serverBuyerPages); ?>;
    let serverPendingOrders = <?php echo json_encode($serverPendingOrders); ?>;

    if (serverPages.length > 0 || serverPendingOrders.length > 0) {
      allBuyerPages = serverPages;
      allPendingOrders = serverPendingOrders;
      renderPurchasedGiftsHub(serverPages, serverPendingOrders);
    }

    if (activeToken && <?php echo $showDashboard ? 'true' : 'false'; ?>) {
      loadDashboardData(activeToken);
    }

    // Smart Smooth Auto-Hiding Header Script
    (function() {
      let lastScrollY = window.scrollY;
      const header = document.getElementById('editHeader');
      const scrollThreshold = 5;

      if (!header) return;

      window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        // Always show header near top of page (0 to 60px)
        if (currentScrollY <= 60) {
          header.classList.remove('-translate-y-full');
          lastScrollY = currentScrollY;
          return;
        }

        // Ignore micro scroll jitter
        if (Math.abs(currentScrollY - lastScrollY) < scrollThreshold) {
          return;
        }

        if (currentScrollY > lastScrollY) {
          // Scrolling Down -> Smoothly Hide Header
          header.classList.add('-translate-y-full');
        } else if (currentScrollY < lastScrollY) {
          // Scrolling Up -> Smoothly Reveal Header
          header.classList.remove('-translate-y-full');
        }

        lastScrollY = currentScrollY;
      }, { passive: true });
    })();

    function showForgotPasswordModal() {
      const email = document.getElementById('loginEmail')?.value || '';
      if (email && document.getElementById('forgotPassEmail')) {
        document.getElementById('forgotPassEmail').value = email;
      }
      document.getElementById('forgotPasswordModal').classList.remove('hidden');
      if (typeof lucide === 'object') lucide.createIcons();
    }

    function closeForgotPasswordModal() {
      document.getElementById('forgotPasswordModal').classList.add('hidden');
      document.getElementById('forgotPassMsg').classList.add('hidden');
    }

    async function handleRequestPasswordReset(e) {
      e.preventDefault();
      const email = document.getElementById('forgotPassEmail').value.trim();
      const btn = document.getElementById('forgotPassBtn');
      const msg = document.getElementById('forgotPassMsg');

      if (!email) return;
      btn.innerText = 'Sending Reset Link...';
      btn.disabled = true;
      msg.classList.add('hidden');

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'request_reset', email: email })
        });
        const data = await res.json();

        msg.classList.remove('hidden');
        btn.disabled = false;
        btn.innerText = 'Send Password Reset Link';

        if (data.success) {
          msg.className = 'text-xs text-center p-3 rounded-xl bg-emerald-950/70 border border-emerald-500/40 text-emerald-300';
          msg.innerText = '✅ Password reset instructions sent to your email address!';
        } else {
          msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
          msg.innerText = '❌ ' + (data.message || 'Email not found. Please check your email address.');
        }
      } catch (err) {
        btn.disabled = false;
        btn.innerText = 'Send Password Reset Link';
        msg.classList.remove('hidden');
        msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
        msg.innerText = '❌ Network error. Please try again.';
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const resetToken = urlParams.get('reset_token');
      if (resetToken) {
        document.getElementById('newPassTokenInput').value = resetToken;
        document.getElementById('setNewPasswordModal').classList.remove('hidden');
        if (typeof lucide === 'object') lucide.createIcons();
      }
    });

    function generateWhatsAppShareUrl(templateId, partnerName, shareUrl) {
      const pName = (partnerName || '').trim();
      const nameSnippet = pName ? ` *${pName}*` : '';
      let msg = '';
      const tid = (templateId || '').toLowerCase();
      
      if (tid.includes('rakhi') || tid.includes('raksha')) {
        msg = `✨ Pyari Behen${nameSnippet}, maine tumhare liye ek special Raksha Bandhan surprise website banayi hai! 🪔🧵\n\nPerform virtual Rakhi ceremony, unroll childhood memories & unlock your gift with our childhood secret word! 🎁\n\n👉 Tap to open your gift: ${shareUrl}`;
      } else if (tid.includes('birthday')) {
        msg = `🎂 Happy Birthday${nameSnippet}! Maine tumhare liye ek special Birthday surprise website banayi hai! 🎉🎁\n\nCut the virtual cake, view our favorite moments & unlock your gifts! 🎈\n\n👉 Tap to open: ${shareUrl}`;
      } else if (tid.includes('anniversary')) {
        msg = `💕 Happy Anniversary${nameSnippet}! Maine humare is special din ke liye ek romantic surprise banaya hai! ✨🥂\n\nRelive our sweetest milestones, read sealed love letters & celebrate our journey! 💖\n\n👉 Tap to open: ${shareUrl}`;
      } else if (tid.includes('proposal') || tid.includes('propose')) {
        msg = `💍${nameSnippet}, will you marry me? Maine tumhare liye ek special proposal website banayi hai! 💖✨\n\n👉 Tap to unlock our story: ${shareUrl}`;
      } else if (tid.includes('distance')) {
        msg = `✈️ Distance means so little when you mean so much!${nameSnippet}, open this special long-distance keepsake! 💌🌍\n\n👉 Tap to open: ${shareUrl}`;
      } else {
        msg = `✨ I created a special secret surprise${nameSnippet}! 🎁\n\n👉 Tap to unlock your gift: ${shareUrl}`;
      }

      return `https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`;
    }

    function closeSetNewPasswordModal() {
      document.getElementById('setNewPasswordModal').classList.add('hidden');
    }

    async function handlePerformPasswordReset(e) {
      e.preventDefault();
      const token = document.getElementById('newPassTokenInput').value;
      const pass = document.getElementById('newPassInput').value.trim();
      const confirmPass = document.getElementById('confirmNewPassInput').value.trim();
      const btn = document.getElementById('setNewPassBtn');
      const msg = document.getElementById('setNewPassMsg');

      if (!pass || pass.length < 4) {
        msg.classList.remove('hidden');
        msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
        msg.innerText = '❌ Password must be at least 4 characters.';
        return;
      }

      if (pass !== confirmPass) {
        msg.classList.remove('hidden');
        msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
        msg.innerText = '❌ Passwords do not match. Please re-type.';
        return;
      }

      btn.innerText = 'Updating Password...';
      btn.disabled = true;

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'perform_reset', reset_token: token, new_password: pass })
        });
        const data = await res.json();

        msg.classList.remove('hidden');
        btn.disabled = false;
        btn.innerText = 'Save New Password & Log In';

        if (data.success) {
          msg.className = 'text-xs text-center p-3 rounded-xl bg-emerald-950/70 border border-emerald-500/40 text-emerald-300';
          msg.innerText = '✅ Password updated successfully! Redirecting...';
          setTimeout(() => {
            closeSetNewPasswordModal();
            window.history.replaceState({}, document.title, window.location.pathname);
          }, 1500);
        } else {
          msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
          msg.innerText = '❌ ' + (data.message || 'Invalid or expired reset link.');
        }
      } catch (err) {
        btn.disabled = false;
        btn.innerText = 'Save New Password & Log In';
        msg.classList.remove('hidden');
        msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
        msg.innerText = '❌ Connection error. Please try again.';
      }
    }
  </script>
</body>
</html>
