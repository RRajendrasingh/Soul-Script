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
  $pageTitle = 'Terms & Conditions — ' . APP_NAME . ' | User Agreement';
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
  $current_page = 'terms';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-36 pb-20 relative z-10 space-y-10">
    
    <!-- Hero Header -->
    <div class="text-center space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#3b1e3b]/60 border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
        <span>Legal Agreement</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">
        Terms &amp; Conditions
      </h1>
      <p class="text-xs sm:text-sm text-[#d0c3cb]">
        Last Updated: August 2026 | Effective immediately for all visitors and buyers.
      </p>
    </div>

    <!-- Content Document -->
    <div class="bg-[#221f21]/90 rounded-3xl border border-[#4d444b]/40 p-6 sm:p-10 shadow-2xl backdrop-blur-xl space-y-8 text-xs sm:text-sm leading-relaxed text-[#d0c3cb]">
      
      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">1. Overview &amp; Acceptance</h2>
        <p>
          Welcome to <strong class="text-[#e8e0e3]"><?php echo APP_NAME; ?></strong>, accessible at <a href="<?php echo APP_URL; ?>" class="text-[#eac34a] underline"><?php echo APP_URL; ?></a>. By accessing or using our platform, purchasing custom web templates, or generating personalized surprise reveal web links, you agree to be bound by these Terms and Conditions. If you disagree with any part of these terms, please do not use our services.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">2. Nature of Service (Digital Products)</h2>
        <p>
          SoulScript provides interactive, custom digital web experiences (surprise reveal websites for anniversaries, birthdays, proposals, Raksha Bandhan, and romantic milestones).
        </p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li>All products sold on this platform are <strong>strictly digital web pages and services</strong>.</li>
          <li>No physical goods, CDs, or printed cards are shipped physically to the buyer's home address.</li>
          <li>Upon successful payment confirmation and form submission, unique password-locked web URLs and buyer dashboard access are delivered immediately.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">3. Account, Passwords &amp; Security</h2>
        <p>
          When personalizing a surprise page, you may set private hint answers or buyer portal passwords. You are entirely responsible for maintaining the confidentiality of your account credentials and links. SoulScript is not liable for unauthorized access resulting from the user sharing links, passwords, or secret hints.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">4. Content Guidelines &amp; User Conduct</h2>
        <p>
          When uploading photos, custom notes, audio, or anniversary messages, you warrant that you own or possess valid permissions for all submitted media. You agree not to upload any media that is:
        </p>
        <ul class="list-disc list-inside space-y-1 pl-2 text-[#d0c3cb]/90">
          <li>Defamatory, obscene, unlawful, abusive, or infringing on third-party privacy.</li>
          <li>Malicious software, corrupted payloads, or attempts to disrupt service functionality.</li>
        </ul>
        <p>
          SoulScript reserves the right to remove any content or deactivate links that violate these safety standards.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">5. Pricing, Payments &amp; Currency</h2>
        <p>
          All prices for templates and addons are clearly displayed in <strong>Indian Rupees (INR / ₹)</strong>. Payments are processed securely via verified third-party payment gateways (such as Razorpay). SoulScript does not store raw credit/debit card numbers or UPI PINs on its servers.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">6. Hosting &amp; Service Availability</h2>
        <p>
          While we strive for 99.9% uptime and persistent dual-storage backups of your customized surprises, SoulScript shall not be held liable for temporary downtimes caused by internet infrastructure outages, third-party hosting maintenance, or force majeure events.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">7. Modifications to Terms</h2>
        <p>
          SoulScript reserves the right to modify these Terms at any time. Any changes will be posted on this page with an updated timestamp. Continued use of the platform constitutes acceptance of revised terms.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-serif font-bold text-[#eac34a]">8. Governing Law &amp; Dispute Resolution</h2>
        <p>
          These Terms and any dispute arising from the use of this website shall be governed by and construed in accordance with the laws of India, subject to the exclusive jurisdiction of the courts in India.
        </p>
      </section>

      <section class="space-y-2 pt-4 border-t border-[#4d444b]/40 text-xs">
        <p>
          If you have any questions regarding these Terms, please contact our support team at <a href="mailto:support@digitalyogi24.com" class="text-[#eac34a] underline">support@digitalyogi24.com</a> or via our <a href="<?php echo APP_URL; ?>/contact.php" class="text-[#eac34a] underline">Contact Us</a> page.
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
