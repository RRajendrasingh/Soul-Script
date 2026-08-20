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
  $pageTitle = 'Cancellation & Refund Policy — ' . APP_NAME . ' | Customer Satisfaction';
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
  $current_page = 'refund';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-36 pb-20 relative z-10 space-y-10">
    
    <!-- Hero Header -->
    <div class="text-center space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#3b1e3b]/60 border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
        <span>Customer Satisfaction</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">
        Cancellation &amp; Refund Policy
      </h1>
      <p class="text-xs sm:text-sm text-[#d0c3cb]">
        Last Updated: August 2026 | Transparent and fair guidelines for our digital personalized services.
      </p>
    </div>

    <!-- Content Document -->
    <div class="bg-[#221f21]/90 rounded-3xl border border-[#4d444b]/40 p-6 sm:p-10 shadow-2xl backdrop-blur-xl space-y-8 text-xs sm:text-sm leading-relaxed text-[#d0c3cb]">
      
      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">1. Digital Customized Services</h2>
        <p>
          At <strong class="text-[#e8e0e3]"><?php echo APP_NAME; ?></strong>, all our products are custom-crafted, personalized digital web experiences. Because each surprise reveal page is provisioned, hosted, and customized specifically for your relationship memories with instant digital delivery, standard return of physical items does not apply.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">2. Pre-Purchase Live Demos</h2>
        <p>
          To ensure 100% satisfaction before you make any payment, we provide interactive <strong>Live Demos</strong> and complete template previews on our homepage (<a href="<?php echo APP_URL; ?>/#gallery" class="text-[#eac34a] underline"><?php echo APP_URL; ?>/#gallery</a>). Customers are encouraged to explore the animations, themes, and layouts before purchasing.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">3. Refund Eligibility &amp; Criteria</h2>
        <p>We provide full or partial refunds under the following specific circumstances:</p>
        <ul class="list-disc list-inside space-y-1.5 pl-2 text-[#d0c3cb]/90">
          <li><strong>Duplicate Charges</strong>: In the event of an accidental double charge or technical billing duplicate, the surplus transaction will be refunded in full.</li>
          <li><strong>Payment Deducted but Order Not Created</strong>: If money was debited from your bank account/card but the order failed to generate due to a network glitch, our automated system or support team will reconcile and issue a complete refund.</li>
          <li><strong>Unresolvable Technical Failure</strong>: If our platform fails to generate your customized website and our technical support team cannot resolve the issue within 24 hours of receiving your support ticket.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">4. Non-Refundable Scenarios</h2>
        <p>Refunds will not be granted under the following conditions:</p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li>Change of mind after the surprise page has been successfully personalized, published, and viewed.</li>
          <li>Incorrect user-entered data (e.g. misspelled names or dates) — customers can edit and correct all details anytime for free via the <a href="<?php echo APP_URL; ?>/edit.php" class="text-[#eac34a] underline">Buyer Portal</a>.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">5. Refund Processing Timelines</h2>
        <p>
          Once an approved refund is initiated by our team:
        </p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li>The refund is processed back to the <strong>original source of payment</strong> (UPI ID, credit/debit card, or bank account).</li>
          <li>Refunds typically reflect in your bank account within <strong>5 to 7 business days</strong> as per standard banking network cycles in India.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">6. How to Request a Refund</h2>
        <p>
          To request a refund, please send an email to <a href="mailto:support@giftreveal.in" class="text-[#eac34a] underline">support@giftreveal.in</a> with:
        </p>
        <ol class="list-decimal list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li>Your <strong>Order ID</strong> (e.g. <code>ord_...</code>) or registered email address.</li>
          <li>Payment transaction screenshot or gateway reference ID.</li>
          <li>Brief explanation of the issue encountered.</li>
        </ol>
        <p class="text-xs text-[#d0c3cb]/80 mt-2">
          Our team reviews and responds to all refund requests within 24 hours.
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
