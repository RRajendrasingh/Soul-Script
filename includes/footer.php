<?php
// SoulScript — Unified Reusable Footer Component
// Comprehensive multi-column footer meeting Razorpay merchant compliance standards
?>
<footer class="mt-24 pt-12 pb-10 border-t border-[#4d444b]/40 bg-[#120f12]/80 backdrop-blur-md relative z-10 text-[#d0c3cb]">
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
    
    <!-- Top Grid: Brand & Link Sections -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">
      
      <!-- Brand & Mission Column -->
      <div class="space-y-4 sm:col-span-2 lg:col-span-1">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1px] shadow-[0_0_12px_rgba(234,195,74,0.3)]">
            <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
              <i data-lucide="heart" class="w-4 h-4 text-[#eac34a] fill-[#eac34a]/30"></i>
            </div>
          </div>
          <span class="text-xl font-bold font-serif text-[#e8e0e3] tracking-wide">
            <?php echo defined('APP_NAME') ? APP_NAME : 'SoulScript'; ?>
          </span>
        </div>
        <p class="text-xs text-[#d0c3cb]/80 leading-relaxed">
          Crafting personalized, interactive, and password-protected surprise reveal websites for anniversaries, birthdays, proposals, and festive celebrations.
        </p>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#3b1e3b]/50 border border-[#eac34a]/30 text-[10px] text-[#eac34a] font-semibold">
          <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
          <span>100% Secure &amp; Instant Delivery</span>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="space-y-3.5">
        <h4 class="text-xs font-serif font-bold text-[#eac34a] uppercase tracking-wider">Explore</h4>
        <ul class="space-y-2 text-xs">
          <li>
            <a href="<?php echo APP_URL; ?>" class="hover:text-[#eac34a] transition-colors flex items-center gap-1.5">
              <span>Home</span>
            </a>
          </li>
          <li>
            <a href="<?php echo APP_URL; ?>/#gallery" class="hover:text-[#eac34a] transition-colors flex items-center gap-1.5">
              <span>Templates &amp; Pricing</span>
            </a>
          </li>
          <li>
            <a href="<?php echo APP_URL; ?>/#gallery" class="hover:text-[#eac34a] transition-colors flex items-center gap-1.5">
              <span>Live Demos</span>
            </a>
          </li>
          <li>
            <a href="<?php echo APP_URL; ?>/about.php" class="hover:text-[#eac34a] transition-colors flex items-center gap-1.5">
              <span>About Us</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Customer Support & Account -->
      <div class="space-y-3.5">
        <h4 class="text-xs font-serif font-bold text-[#eac34a] uppercase tracking-wider">Account &amp; Help</h4>
        <ul class="space-y-2 text-xs">
          <li>
            <a href="<?php echo APP_URL; ?>/edit.php" class="hover:text-[#eac34a] transition-colors flex items-center gap-1.5">
              <span>Buyer Portal / Edit Page</span>
            </a>
          </li>
          <li>
            <a href="<?php echo APP_URL; ?>/contact.php" class="hover:text-[#eac34a] transition-colors flex items-center gap-1.5">
              <span>Contact Support</span>
            </a>
          </li>
          <li>
            <a href="mailto:support@digitalyogi24.com" class="hover:text-[#eac34a] transition-colors flex items-center gap-1.5 text-[#d0c3cb]/70">
              <i data-lucide="mail" class="w-3 h-3 text-[#eac34a]"></i>
              <span>support@digitalyogi24.com</span>
            </a>
          </li>
          <li class="text-[11px] text-[#d0c3cb]/60">
            Mon – Sat: 10:00 AM – 7:00 PM IST
          </li>
        </ul>
      </div>

      <!-- Legal & Policies (Mandatory for Payment Gateways) -->
      <div class="space-y-3.5">
        <h4 class="text-xs font-serif font-bold text-[#eac34a] uppercase tracking-wider">Legal &amp; Policies</h4>
        <ul class="space-y-2 text-xs">
          <li>
            <a href="<?php echo APP_URL; ?>/terms.php" class="hover:text-[#eac34a] transition-colors">
              Terms &amp; Conditions
            </a>
          </li>
          <li>
            <a href="<?php echo APP_URL; ?>/privacy.php" class="hover:text-[#eac34a] transition-colors">
              Privacy Policy
            </a>
          </li>
          <li>
            <a href="<?php echo APP_URL; ?>/refund-policy.php" class="hover:text-[#eac34a] transition-colors">
              Cancellation &amp; Refund Policy
            </a>
          </li>
          <li>
            <a href="<?php echo APP_URL; ?>/shipping-policy.php" class="hover:text-[#eac34a] transition-colors">
              Shipping &amp; Delivery Policy
            </a>
          </li>
        </ul>
      </div>

    </div>

    <!-- Bottom Bar -->
    <div class="pt-8 border-t border-[#4d444b]/30 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#d0c3cb]/70">
      <p class="text-[11px]">
        &copy; <?php echo date('Y'); ?> <span class="text-[#e8e0e3] font-semibold">SoulScript</span>. All rights reserved.
      </p>
      
      <div class="flex items-center gap-4 text-[10px] uppercase tracking-wider text-[#d0c3cb]/50">
        <span>Instant Digital Delivery</span>
        <span>•</span>
        <span>Encrypted Payments</span>
        <span>•</span>
        <span>Made with ❤️ in India</span>
      </div>
    </div>

  </div>
</footer>
