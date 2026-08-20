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
  $pageTitle = 'Privacy Policy — ' . APP_NAME . ' | Data Protection & Media Privacy';
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
  $current_page = 'privacy';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-36 pb-20 relative z-10 space-y-10">
    
    <!-- Hero Header -->
    <div class="text-center space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#3b1e3b]/60 border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
        <span>Privacy &amp; Security</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">
        Privacy Policy
      </h1>
      <p class="text-xs sm:text-sm text-[#d0c3cb]">
        Last Updated: August 2026 | We take your personal memories and data security seriously.
      </p>
    </div>

    <!-- Content Document -->
    <div class="bg-[#221f21]/90 rounded-3xl border border-[#4d444b]/40 p-6 sm:p-10 shadow-2xl backdrop-blur-xl space-y-8 text-xs sm:text-sm leading-relaxed text-[#d0c3cb]">
      
      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">1. Commitment to Privacy</h2>
        <p>
          At <strong class="text-[#e8e0e3]"><?php echo APP_NAME; ?></strong> (<a href="<?php echo APP_URL; ?>" class="text-[#eac34a] underline"><?php echo APP_URL; ?></a>), we understand that romantic surprise websites contain deeply personal moments, photos, and messages. We are firmly committed to safeguarding the privacy and confidentiality of our visitors and customers.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">2. Information We Collect</h2>
        <p>We collect only the essential information necessary to deliver and customize your surprise reveals:</p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li><strong>Buyer Contact Information</strong>: Name, email address, and phone number (for order delivery, receipts, and account authentication).</li>
          <li><strong>Customization Content</strong>: Partner name, relationship milestone dates, love notes, letters, secret hint questions, and uploaded scrapbook photos / avatars.</li>
          <li><strong>Payment Information</strong>: Transaction identifiers and payment status provided by our payment gateway partners. <em>We never see or store your credit card, debit card, or UPI banking credentials.</em></li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">3. How We Use &amp; Protect Your Uploaded Photos &amp; Memories</h2>
        <p>
          Your uploaded photos and scrapbook memories are processed with optimized web compression and stored in secure, persistent cloud directories solely for rendering your personalized reveal pages.
        </p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li><strong>Zero Commercial Use</strong>: We will NEVER sell, license, publish, or publicly distribute your personal photos or romantic letters.</li>
          <li><strong>Password &amp; Hint Lock</strong>: All surprise reveal pages remain strictly accessible only to those possessing the exact URL and correct secret hint or password.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">4. Data Sharing with Third Parties</h2>
        <p>
          We do not sell, trade, or rent personal identification information to third parties. We share limited technical data only with trusted infrastructure providers required to operate our service:
        </p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li><strong>Payment Processors</strong>: Secure RBI-compliant payment gateways (such as Razorpay) for transaction execution.</li>
          <li><strong>Transactional Email Services</strong>: To deliver order receipts and page access links directly to your mailbox.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">5. Cookies &amp; Session Data</h2>
        <p>
          We use minimal session cookies strictly required for user authentication (e.g. remembering your Buyer Portal login session when editing your surprise page). We do not use intrusive cross-site tracking cookies.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">6. Data Retention &amp; User Deletion Requests</h2>
        <p>
          Your customized surprise websites are preserved indefinitely so you and your partner can revisit them on future anniversaries. However, if you wish to delete your customized page, uploaded photos, or account data permanently, you may email us at <a href="mailto:support@giftreveal.in" class="text-[#eac34a] underline">support@giftreveal.in</a> and we will erase your media within 48 hours.
        </p>
      </section>

      <section class="space-y-2 pt-4 border-t border-[#4d444b]/40 text-xs">
        <p>
          For any privacy inquiries or grievance redressal, please write to our Privacy Officer at <a href="mailto:support@giftreveal.in" class="text-[#eac34a] underline">support@giftreveal.in</a>.
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
