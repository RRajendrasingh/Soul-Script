<?php
/**
 * GiftReveal — Smart Context-Aware Dual-Session Avatar Navigation Component
 * Supports: Guest, Buyer, Master Admin, and Dual-Session (Admin + Buyer) modes.
 */

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$isAdminSession = !empty($_SESSION['admin_logged_in']);
$isBuyerSession = !empty($_SESSION['buyer_token']) || !empty($_SESSION['buyer_page_id']);

$buyerName = !empty($_SESSION['buyer_name']) ? trim($_SESSION['buyer_name']) : 'Buyer';
$buyerInitial = strtoupper(substr($buyerName, 0, 1));
$buyerSlug = !empty($_SESSION['buyer_slug']) ? $_SESSION['buyer_slug'] : '';
$buyerPageId = !empty($_SESSION['buyer_page_id']) ? $_SESSION['buyer_page_id'] : '';

$currentAppUrl = defined('APP_URL') ? APP_URL : '';
$isCurrentlyAdminPage = !empty($isAdminPage);
?>

<!-- SMART USER AVATAR NAVIGATION PILL -->
<div class="relative inline-block text-left" id="userNavDropdownContainer">
  <?php if ($isAdminSession && $isBuyerSession): ?>
    <!-- DUAL SESSION (Admin + Buyer) -->
    <button type="button" onclick="toggleUserDropdown(event)" id="userNavDropdownBtn" class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#3b1e3b] hover:bg-[#4d274d] text-[#eac34a] border border-[#eac34a]/60 shadow-[0_0_15px_rgba(234,195,74,0.2)] transition-all cursor-pointer">
      <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-[#eac34a] to-[#d4af37] text-[#151215] flex items-center justify-center font-bold text-xs shadow-inner">
        👑
      </div>
      <span class="text-xs font-bold font-serif tracking-wide hidden sm:inline text-[#e8e0e3]">
        Admin (<span class="text-[#eac34a]"><?php echo htmlspecialchars($buyerName); ?></span>)
      </span>
      <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#eac34a] transition-transform duration-200" id="userNavChevron"></i>
    </button>

    <!-- DUAL DROPDOWN MENU -->
    <div id="userNavMenu" class="hidden absolute right-0 mt-2 w-72 rounded-2xl bg-[#1b171b] border border-[#4d444b] shadow-[0_10px_40px_rgba(0,0,0,0.8)] backdrop-blur-2xl p-2 z-50 text-left divide-y divide-[#4d444b]/40 animate-in fade-in duration-150">
      <!-- Admin Section -->
      <div class="p-2.5">
        <div class="flex items-center gap-2 text-[#eac34a] text-xs font-bold uppercase tracking-wider mb-1">
          <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Master Admin Control
        </div>
        <a href="<?php echo $currentAppUrl; ?>/admin/index.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
          <i data-lucide="layout-dashboard" class="w-4 h-4 text-[#eac34a]"></i> Admin Dashboard
        </a>
      </div>

      <!-- Buyer Section -->
      <div class="p-2.5">
        <div class="flex items-center gap-2 text-[#e4b9df] text-xs font-bold uppercase tracking-wider mb-1">
          <i data-lucide="gift" class="w-3.5 h-3.5"></i> Buyer: <?php echo htmlspecialchars($buyerName); ?>
        </div>
        <a href="<?php echo $currentAppUrl; ?>/edit.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
          <i data-lucide="edit-3" class="w-4 h-4 text-[#e4b9df]"></i> Edit My Surprise
        </a>
        <?php if ($buyerSlug): ?>
          <a href="<?php echo $currentAppUrl; ?>/gift/<?php echo urlencode($buyerSlug); ?>" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
            <i data-lucide="external-link" class="w-4 h-4 text-[#a4e4b9]"></i> View Live Gift Link ↗
          </a>
        <?php endif; ?>
      </div>

      <!-- Logout Section -->
      <div class="p-2.5 space-y-1">
        <a href="<?php echo $currentAppUrl; ?>/edit.php?logout=1" class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-300 hover:bg-rose-950/40 transition-all">
          <i data-lucide="log-out" class="w-3.5 h-3.5 text-rose-400"></i> Logout Buyer Session
        </a>
        <a href="<?php echo $currentAppUrl; ?>/admin/index.php?logout=1" class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-300 hover:bg-rose-950/40 transition-all">
          <i data-lucide="shield-off" class="w-3.5 h-3.5 text-rose-400"></i> Logout Admin Session
        </a>
      </div>
    </div>

  <?php elseif ($isAdminSession): ?>
    <!-- ADMIN ONLY SESSION -->
    <button type="button" onclick="toggleUserDropdown(event)" id="userNavDropdownBtn" class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#3b1e3b] hover:bg-[#4d274d] text-[#eac34a] border border-[#eac34a]/60 shadow-[0_0_15px_rgba(234,195,74,0.2)] transition-all cursor-pointer">
      <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-[#eac34a] to-[#d4af37] text-[#151215] flex items-center justify-center font-bold text-xs shadow-inner">
        👑
      </div>
      <span class="text-xs font-bold font-serif tracking-wide hidden sm:inline text-[#e8e0e3]">
        Master Admin
      </span>
      <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#eac34a] transition-transform duration-200" id="userNavChevron"></i>
    </button>

    <!-- ADMIN DROPDOWN MENU -->
    <div id="userNavMenu" class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-[#1b171b] border border-[#4d444b] shadow-[0_10px_40px_rgba(0,0,0,0.8)] backdrop-blur-2xl p-2 z-50 text-left divide-y divide-[#4d444b]/40 animate-in fade-in duration-150">
      <div class="p-2.5">
        <span class="text-[10px] uppercase font-bold tracking-widest text-[#eac34a] block">Authenticated SuperAdmin</span>
        <span class="text-xs font-serif font-bold text-[#e8e0e3]">GiftReveal Suite</span>
      </div>

      <div class="p-2 space-y-1">
        <a href="<?php echo $currentAppUrl; ?>/admin/index.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
          <i data-lucide="layout-dashboard" class="w-4 h-4 text-[#eac34a]"></i> Orders &amp; Sales
        </a>
        <a href="<?php echo $currentAppUrl; ?>/admin/templates.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
          <i data-lucide="layout-grid" class="w-4 h-4 text-[#eac34a]"></i> Gift Cards Manager
        </a>
        <a href="<?php echo $currentAppUrl; ?>/admin/messages.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
          <i data-lucide="mail" class="w-4 h-4 text-[#eac34a]"></i> Customer Inquiries
        </a>
        <a href="<?php echo $currentAppUrl; ?>/admin/journey.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
          <i data-lucide="compass" class="w-4 h-4 text-[#eac34a]"></i> Master Roadmap 🚀
        </a>
        <a href="<?php echo $currentAppUrl; ?>" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#d0c3cb] hover:bg-[#3b1e3b] hover:text-white transition-all">
          <i data-lucide="globe" class="w-4 h-4 text-[#a4e4b9]"></i> View Customer Website
        </a>
      </div>

      <div class="p-2">
        <a href="<?php echo $currentAppUrl; ?>/admin/index.php?logout=1" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-rose-300 hover:bg-rose-950/50 hover:text-rose-200 transition-all">
          <i data-lucide="log-out" class="w-4 h-4 text-rose-400"></i> Secure Logout
        </a>
      </div>
    </div>

  <?php elseif ($isBuyerSession): ?>
    <!-- BUYER ONLY SESSION -->
    <button type="button" onclick="toggleUserDropdown(event)" id="userNavDropdownBtn" class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#3b1e3b] hover:bg-[#4d274d] text-[#eac34a] border border-[#eac34a]/60 shadow-[0_0_15px_rgba(234,195,74,0.2)] transition-all cursor-pointer">
      <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-[#eac34a] to-[#d4af37] text-[#151215] flex items-center justify-center font-bold text-xs shadow-inner">
        <?php echo htmlspecialchars($buyerInitial); ?>
      </div>
      <span class="text-xs font-bold font-serif tracking-wide hidden sm:inline text-[#e8e0e3]">
        <?php echo htmlspecialchars($buyerName); ?>
      </span>
      <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#eac34a] transition-transform duration-200" id="userNavChevron"></i>
    </button>

    <!-- BUYER DROPDOWN MENU -->
    <div id="userNavMenu" class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-[#1b171b] border border-[#4d444b] shadow-[0_10px_40px_rgba(0,0,0,0.8)] backdrop-blur-2xl p-2 z-50 text-left divide-y divide-[#4d444b]/40 animate-in fade-in duration-150">
      <div class="p-2.5">
        <span class="text-[10px] uppercase font-bold tracking-widest text-[#eac34a] block">Authenticated Buyer</span>
        <span class="text-xs font-serif font-bold text-[#e8e0e3]"><?php echo htmlspecialchars($buyerName); ?></span>
      </div>

      <div class="p-2 space-y-1">
        <a href="<?php echo $currentAppUrl; ?>/edit.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
          <i data-lucide="edit-3" class="w-4 h-4 text-[#eac34a]"></i> Edit Surprise Details
        </a>
        <?php if ($buyerSlug): ?>
          <a href="<?php echo $currentAppUrl; ?>/gift/<?php echo urlencode($buyerSlug); ?>" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#e8e0e3] hover:bg-[#3b1e3b] hover:text-[#eac34a] transition-all">
            <i data-lucide="external-link" class="w-4 h-4 text-[#a4e4b9]"></i> View Live Gift Link ↗
          </a>
        <?php endif; ?>
      </div>

      <div class="p-2">
        <a href="<?php echo $currentAppUrl; ?>/edit.php?logout=1" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-rose-300 hover:bg-rose-950/50 hover:text-rose-200 transition-all">
          <i data-lucide="log-out" class="w-4 h-4 text-rose-400"></i> Logout from Portal
        </a>
      </div>
    </div>

  <?php else: ?>
    <!-- GUEST VISITOR (Not logged in) -->
    <div class="flex items-center gap-2.5">
      <a href="<?php echo $currentAppUrl; ?>/edit.php" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 border border-[#eac34a]/60 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] shadow-[0_0_15px_rgba(234,195,74,0.2)] transition-all">
        <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
        <span>Buyer Login</span>
      </a>
      <a href="<?php echo $currentAppUrl; ?>/#gallery" class="px-5 py-2 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] text-xs font-bold uppercase tracking-[0.15em] shadow-[0_0_20px_rgba(234,195,74,0.3)] hover:shadow-[0_0_30px_rgba(234,195,74,0.5)] hover:scale-105 transition-all duration-300 flex items-center gap-2">
        <i data-lucide="gift" class="w-3.5 h-3.5"></i>
        <span>Create Surprise</span>
      </a>
    </div>
  <?php endif; ?>
</div>

<script>
function toggleUserDropdown(e) {
  if (e) {
    e.stopPropagation();
    e.preventDefault();
  }
  const menu = document.getElementById('userNavMenu');
  const chevron = document.getElementById('userNavChevron');
  if (menu) {
    const isHidden = menu.classList.contains('hidden');
    if (isHidden) {
      menu.classList.remove('hidden');
      if (chevron) chevron.style.transform = 'rotate(180deg)';
      if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
      }
    } else {
      menu.classList.add('hidden');
      if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
  }
}

document.addEventListener('click', function(e) {
  const container = document.getElementById('userNavDropdownContainer');
  const menu = document.getElementById('userNavMenu');
  const chevron = document.getElementById('userNavChevron');
  if (container && menu && !container.contains(e.target)) {
    menu.classList.add('hidden');
    if (chevron) chevron.style.transform = 'rotate(0deg)';
  }
});
</script>
