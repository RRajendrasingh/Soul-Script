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
  $pageTitle = 'About Us — ' . APP_NAME . ' | Romantic Surprise Websites';
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
  $current_page = 'about';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-36 pb-20 relative z-10 space-y-12">
    
    <!-- Hero Header -->
    <div class="text-center space-y-4">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#3b1e3b]/60 border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
        <span>Our Mission &amp; Craft</span>
      </div>
      <h1 class="text-3xl sm:text-5xl font-bold font-serif text-[#e8e0e3] tracking-tight">
        Turning Memories Into <span class="bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#e4b9df] bg-clip-text text-transparent">Digital Keepsakes</span>
      </h1>
      <p class="text-sm sm:text-base text-[#d0c3cb] max-w-2xl mx-auto leading-relaxed">
        SoulScript empowers you to create deeply personal, password-locked, interactive surprise reveal websites for anniversaries, birthdays, proposals, and festive celebrations.
      </p>
    </div>

    <!-- Story Card -->
    <div class="bg-[#221f21]/90 rounded-3xl border border-[#4d444b]/40 p-6 sm:p-10 shadow-2xl backdrop-blur-xl space-y-8">
      <div class="space-y-4">
        <h2 class="text-2xl font-serif font-bold text-[#eac34a] flex items-center gap-3">
          <i data-lucide="heart" class="w-6 h-6 text-[#eac34a]"></i>
          <span>The SoulScript Story</span>
        </h2>
        <p class="text-sm sm:text-base text-[#d0c3cb]/90 leading-relaxed">
          In a world dominated by fleeting text messages and disposable greeting cards, genuine personal expressions often get lost. We created SoulScript to give relationships a permanent, breathtaking digital home.
        </p>
        <p class="text-sm sm:text-base text-[#d0c3cb]/90 leading-relaxed">
          Whether you are celebrating years of companionship, asking the most important question of your life, or sending love across long distances, SoulScript blends cinematic storytelling, interactive touchpoints, personalized music, and interactive photo scrapbooks into a timeless digital reveal experience.
        </p>
      </div>

      <!-- Feature Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-[#4d444b]/40">
        <div class="p-5 rounded-2xl bg-[#1b181b] border border-[#4d444b]/30 space-y-2.5">
          <div class="w-10 h-10 rounded-xl bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center border border-[#eac34a]/30">
            <i data-lucide="shield-check" class="w-5 h-5"></i>
          </div>
          <h3 class="font-serif font-bold text-[#e8e0e3] text-base">Private &amp; Secure</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Every page is protected with a private hint question or password, ensuring only your special someone unlocks your surprise.
          </p>
        </div>

        <div class="p-5 rounded-2xl bg-[#1b181b] border border-[#4d444b]/30 space-y-2.5">
          <div class="w-10 h-10 rounded-xl bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center border border-[#eac34a]/30">
            <i data-lucide="zap" class="w-5 h-5"></i>
          </div>
          <h3 class="font-serif font-bold text-[#e8e0e3] text-base">Instant Delivery</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Your customized surprise website is ready within seconds of personalization, accessible on any smartphone, tablet, or PC worldwide.
          </p>
        </div>

        <div class="p-5 rounded-2xl bg-[#1b181b] border border-[#4d444b]/30 space-y-2.5">
          <div class="w-10 h-10 rounded-xl bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center border border-[#eac34a]/30">
            <i data-lucide="sparkles" class="w-5 h-5"></i>
          </div>
          <h3 class="font-serif font-bold text-[#e8e0e3] text-base">Cinematic Designs</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Designed by luxury experience designers with interactive flower showers, candle glows, memory carousels, and atmospheric sound.
          </p>
        </div>
      </div>

      <!-- Trust Badge / Contact Callout -->
      <div class="p-6 rounded-2xl bg-gradient-to-r from-[#3b1e3b]/80 to-[#241a00]/50 border border-[#eac34a]/40 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
          <h4 class="font-serif font-bold text-[#ffe088] text-base">Ready to craft your unforgettable reveal?</h4>
          <p class="text-xs text-[#d0c3cb]">Explore our handcrafted templates and personalize your story today.</p>
        </div>
        <a href="<?php echo APP_URL; ?>/#gallery" class="px-6 py-2.5 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition-all shrink-0">
          Browse Templates
        </a>
      </div>
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
