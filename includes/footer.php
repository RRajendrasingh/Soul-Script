<?php
// SoulScript — Unified Reusable Footer Component
?>
<footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
  <div class="max-w-[1200px] mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#d0c3cb]">
    <div class="flex items-center gap-2">
      <div class="w-6 h-6 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center border border-[#eac34a]/30">
        <i data-lucide="heart" class="w-3.5 h-3.5 fill-[#eac34a]/40"></i>
      </div>
      <span class="font-bold text-[#e8e0e3] font-serif"><?php echo defined('APP_NAME') ? APP_NAME : 'SoulScript'; ?></span>
      <span class="text-[#d0c3cb]/50">— Crafted for unforgettable romantic moments</span>
    </div>

    <div class="flex items-center gap-6 text-[11px] font-semibold text-[#d0c3cb]/80">
      <a href="<?php echo APP_URL; ?>" class="hover:text-[#eac34a] transition-colors">Home</a>
      <a href="<?php echo APP_URL; ?>/#gallery" class="hover:text-[#eac34a] transition-colors">Templates &amp; Pricing</a>
      <a href="<?php echo APP_URL; ?>/edit.php" class="hover:text-[#eac34a] transition-colors">Buyer Portal</a>
    </div>

    <p class="text-[10px] text-[#d0c3cb]/60 font-mono">
      &copy; <?php echo date('Y'); ?> SoulScript. All rights reserved.
    </p>
  </div>
</footer>
