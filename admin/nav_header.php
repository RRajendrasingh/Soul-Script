<?php
// Reusable Admin Navigation Sub-Bar
$currentAdminScript = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-[#221f21] border border-[#eac34a]/30 p-3 sm:p-4 rounded-2xl shadow-xl mb-6 relative z-20">
  <div class="flex flex-col xl:flex-row items-center justify-between gap-4">
    <div class="flex flex-wrap items-center justify-center xl:justify-start gap-2 w-full xl:w-auto">
      <a href="index.php" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?php echo $currentAdminScript === 'index.php' ? 'bg-[#eac34a] text-[#241a00] shadow-md' : 'bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:bg-[#3b1e3b] hover:text-[#eac34a]'; ?>">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
        <span>Orders &amp; Sales</span>
      </a>
      <a href="templates.php" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?php echo $currentAdminScript === 'templates.php' ? 'bg-[#eac34a] text-[#241a00] shadow-md' : 'bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:bg-[#3b1e3b] hover:text-[#eac34a]'; ?>">
        <i data-lucide="layout-grid" class="w-4 h-4"></i>
        <span>Gift Cards Manager</span>
      </a>
      <a href="sample_gallery.php" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?php echo $currentAdminScript === 'sample_gallery.php' ? 'bg-[#eac34a] text-[#241a00] shadow-md' : 'bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:bg-[#3b1e3b] hover:text-[#eac34a]'; ?>">
        <i data-lucide="image" class="w-4 h-4"></i>
        <span>Sample Gallery</span>
      </a>
      <a href="rakhi_vouchers.php" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?php echo $currentAdminScript === 'rakhi_vouchers.php' ? 'bg-[#eac34a] text-[#241a00] shadow-md' : 'bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:bg-[#3b1e3b] hover:text-[#eac34a]'; ?>">
        <i data-lucide="gift" class="w-4 h-4"></i>
        <span>Rakhi Amazon Vouchers 🎁</span>
      </a>
      <a href="affiliate_settings.php" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?php echo $currentAdminScript === 'affiliate_settings.php' ? 'bg-[#eac34a] text-[#241a00] shadow-md' : 'bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:bg-[#3b1e3b] hover:text-[#eac34a]'; ?>">
        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
        <span>Amazon Affiliate Store 🛒</span>
      </a>
      <a href="system_reset.php" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?php echo $currentAdminScript === 'system_reset.php' ? 'bg-rose-600 text-white shadow-md' : 'bg-[#151215] text-rose-300 border border-rose-500/40 hover:bg-rose-900/40 hover:text-white'; ?>">
        <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
        <span>System Reset ⚙️</span>
      </a>
    </div>
    <div class="w-full xl:w-auto flex justify-center xl:justify-end">
      <a href="index.php?logout=1" class="px-4 py-2 rounded-xl text-xs font-bold text-rose-300 bg-rose-950/40 hover:bg-rose-900/60 border border-rose-500/40 transition-all flex items-center gap-1.5 shadow-md">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>
</nav>
