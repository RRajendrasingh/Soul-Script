<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Shipping & Delivery Policy — ' . APP_NAME . ' | Instant Digital Delivery';
  require_once __DIR__ . '/includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden selection:bg-[#eac34a]/30 selection:text-[#ffe088]">

  <!-- Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Header -->
  <?php 
  $current_page = 'shipping';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-36 pb-20 relative z-10 space-y-10">
    
    <!-- Hero Header -->
    <div class="text-center space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#3b1e3b]/60 border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="zap" class="w-3.5 h-3.5"></i>
        <span>Instant Digital Fulfillment</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">
        Shipping &amp; Delivery Policy
      </h1>
      <p class="text-xs sm:text-sm text-[#d0c3cb]">
        Last Updated: August 2026 | 100% Online, Instantaneous Electronic Fulfillment.
      </p>
    </div>

    <!-- Content Document -->
    <div class="bg-[#221f21]/90 rounded-3xl border border-[#4d444b]/40 p-6 sm:p-10 shadow-2xl backdrop-blur-xl space-y-8 text-xs sm:text-sm leading-relaxed text-[#d0c3cb]">
      
      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">1. Digital Delivery Model (No Physical Shipping)</h2>
        <p>
          <strong class="text-[#e8e0e3]"><?php echo APP_NAME; ?></strong> operates exclusively as a digital web experience platform. All items sold on <a href="<?php echo APP_URL; ?>" class="text-[#eac34a] underline"><?php echo APP_URL; ?></a> are <strong>digital services and personalized interactive web pages</strong>.
        </p>
        <p>
          We do NOT dispatch physical packages, letters, DVDs, or tangible items to physical postal addresses. Therefore, there are no physical shipping charges, courier delays, or customs duties associated with any purchases on our platform.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">2. Delivery Timeline &amp; Access Method</h2>
        <p>
          Delivery of your purchased template and personalization portal occurs <strong>instantly (within 0 to 10 minutes)</strong> upon successful completion of your online payment:
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
          <div class="p-4 rounded-xl bg-[#1b181b] border border-[#4d444b]/30 space-y-1">
            <span class="block text-xs font-bold text-[#ffe088]">Step 1: Instant Redirect</span>
            <p class="text-[11px] text-[#d0c3cb]">You are immediately redirected to the personalizer form upon payment.</p>
          </div>
          <div class="p-4 rounded-xl bg-[#1b181b] border border-[#4d444b]/30 space-y-1">
            <span class="block text-xs font-bold text-[#ffe088]">Step 2: Instant URL Link</span>
            <p class="text-[11px] text-[#d0c3cb]">Your unique surprise reveal link is generated immediately upon form submit.</p>
          </div>
          <div class="p-4 rounded-xl bg-[#1b181b] border border-[#4d444b]/30 space-y-1">
            <span class="block text-xs font-bold text-[#ffe088]">Step 3: Portal Access</span>
            <p class="text-[11px] text-[#d0c3cb]">Your dashboard at Buyer Portal remains 24/7 accessible to edit or view.</p>
          </div>
        </div>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">3. Delivery Confirmation &amp; Email Receipts</h2>
        <p>
          Upon payment capture, an automated order confirmation is generated. Customers also receive direct access to their orders using their registered email on the <a href="<?php echo APP_URL; ?>/edit.php" class="text-[#eac34a] underline">Buyer Portal</a>.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">4. Delivery Issues &amp; Support Assistance</h2>
        <p>
          If you experience any delay in receiving your confirmation or accessing your personalization form after a successful charge, please follow these steps:
        </p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li>Check your spam/junk folder for confirmation emails.</li>
          <li>Log into the <a href="<?php echo APP_URL; ?>/edit.php" class="text-[#eac34a] underline">Buyer Portal</a> using your checkout email to see all active orders.</li>
          <li>If you still require assistance, email our 24/7 technical team at <a href="mailto:support@digitalyogi24.com" class="text-[#eac34a] underline">support@digitalyogi24.com</a> with your transaction reference number. We will manually resolve access within 2 hours.</li>
        </ul>
      </section>

      <section class="space-y-2 pt-4 border-t border-[#4d444b]/40 text-xs">
        <p>
          For more details, please visit our <a href="<?php echo APP_URL; ?>/contact.php" class="text-[#eac34a] underline">Contact Us</a> page or email <a href="mailto:support@digitalyogi24.com" class="text-[#eac34a] underline">support@digitalyogi24.com</a>.
        </p>
      </section>

    </div>

  </main>

  <!-- Footer -->
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
  
  <script>
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  </script>
</body>
</html>
