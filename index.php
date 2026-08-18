<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/media_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = APP_NAME . ' — Rakhi Gift 2026 | Personalized Digital Surprise with Amazon Voucher';
  require_once __DIR__ . '/includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans relative overflow-x-hidden min-h-screen">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <div class="absolute top-0 left-0 w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-0 right-0 w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Unified Global Navbar -->
  <?php 
  $current_page = 'home';
  require_once __DIR__ . '/includes/header.php'; 

  // Fetch primary featured template for dynamic hero mockup (Position #1)
  $primaryTemplate = null;
  try {
      $dbHero = getDB();
      $stmtHero = $dbHero->query("SELECT * FROM templates WHERE active = 1 ORDER BY sort_order ASC, template_id ASC LIMIT 1");
      $primaryTemplate = $stmtHero->fetch();
  } catch (Exception $exH) {}

  $heroDemoUrl = !empty($primaryTemplate['demo_url']) ? $primaryTemplate['demo_url'] : (APP_URL . '/#gallery');
  $heroCoverImg = !empty($primaryTemplate['preview_image_url']) ? resolveMediaUrl($primaryTemplate['preview_image_url']) : 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&q=80&w=800';
  $heroTemplateName = !empty($primaryTemplate['name']) ? htmlspecialchars($primaryTemplate['name']) : 'Romantic Special 👑';
  $heroTagline = !empty($primaryTemplate['tagline']) ? htmlspecialchars($primaryTemplate['tagline']) : 'A Night To Remember';
  $heroPassword = !empty($primaryTemplate['demo_password']) ? htmlspecialchars($primaryTemplate['demo_password']) : '';
  ?>

  <!-- Hero Section -->
  <section class="relative min-h-[85vh] w-full flex items-center justify-center pt-24 pb-16 md:pt-32 md:pb-24 z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col lg:flex-row items-center gap-16">
      
      <!-- Left Column -->
      <div class="w-full lg:w-1/2 flex flex-col items-start text-left gap-8">
        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-[#3b1e3b]/60 border border-[#e4b9df]/20 backdrop-blur-md shadow-sm">
          <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a] animate-pulse"></i>
          <span class="font-sans text-xs font-semibold text-[#eac34a] tracking-[0.2em] uppercase">
            🪔 Rakhi 2026 Special — August 28
          </span>
        </div>

        <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-extrabold text-[#e8e0e3] leading-[1.15] tracking-tight">
          Send Behen a Rakhi Gift
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#e4b9df]">
            She'll Never Forget.
          </span>
        </h1>

        <p class="font-sans text-xs sm:text-base text-[#d0c3cb] max-w-lg leading-relaxed font-normal">
          A personalized surprise website — unlocked only by Behen with a childhood secret — packed with your photos, messages, and an Amazon Gift Voucher. Delivered instantly on WhatsApp. ₹499 onwards.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 w-full sm:w-auto pt-2">
          <a href="#gallery" class="group relative px-6 sm:px-8 py-3.5 sm:py-4 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] rounded-full font-sans text-xs font-bold uppercase tracking-[0.15em] sm:tracking-[0.2em] shadow-[0_0_25px_rgba(234,195,74,0.3)] hover:shadow-[0_0_35px_rgba(234,195,74,0.6)] transition-all duration-500 flex items-center justify-center gap-2.5 cursor-pointer">
            <i data-lucide="gift" class="w-4 h-4 shrink-0"></i>
            <span class="font-bold tracking-wider">🪔 SEND RAKHI SURPRISE</span>
            <i data-lucide="arrow-right" class="w-4 h-4 shrink-0 group-hover:translate-x-1 transition-transform"></i>
          </a>

          <a href="#gallery" class="group flex items-center justify-center gap-3 px-6 py-3.5 sm:py-4 rounded-full border border-[#4d444b] hover:border-[#eac34a]/60 text-[#d0c3cb] hover:text-[#e8e0e3] transition-all duration-300 font-sans text-xs uppercase tracking-wider font-semibold cursor-pointer whitespace-nowrap">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-[#eac34a]/40 flex items-center justify-center group-hover:border-[#eac34a] group-hover:bg-[#eac34a]/10 transition-all shrink-0">
              <i data-lucide="play" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-[#eac34a] fill-[#eac34a]"></i>
            </div>
            <span class="whitespace-nowrap font-bold tracking-wider">EXPLORE LIVE SAMPLES</span>
          </a>
        </div>

        <!-- Trust Highlights -->
        <div class="pt-6 flex flex-wrap items-center gap-6 text-xs text-[#d0c3cb]/80 border-t border-[#4d444b]/30 w-full">
          <div class="flex items-center gap-2">
            <i data-lucide="gift" class="w-4 h-4 text-[#eac34a]"></i>
            <span>Amazon Voucher Included</span>
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4 text-[#e4b9df]"></i>
            <span>Razorpay Secured Payments</span>
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="send" class="w-4 h-4 text-[#eac34a]"></i>
            <span>WhatsApp Instant Delivery</span>
          </div>
        </div>
      </div>

      <!-- Right Column: Dynamic Phone Mockup Frame (Position #1 Sync) -->
      <div class="w-full lg:w-1/2 flex justify-center lg:justify-end relative overflow-hidden py-4">
        <!-- Spinning Background Outline Circles -->
        <div class="absolute top-1/4 left-4 sm:-left-8 w-64 h-64 rounded-full border border-[#eac34a]/20 animate-spin z-10 pointer-events-none" style="animation-duration: 20s;"></div>
        <div class="absolute bottom-10 right-4 sm:-right-8 w-48 h-48 rounded-full border border-[#e4b9df]/20 animate-spin z-10 pointer-events-none" style="animation-duration: 25s; animation-direction: reverse;"></div>

        <!-- Phone Container -->
        <div class="relative w-[300px] sm:w-[330px] h-[620px] bg-[#100d10] rounded-[3rem] border-[6px] border-[#2d292b] shadow-[0_30px_60px_rgba(0,0,0,0.8)] overflow-hidden transform rotate-[-2deg] hover:rotate-0 hover:scale-105 transition-all duration-700 ease-out z-20">
          <div class="w-full h-full relative bg-[#151215] flex flex-col justify-between p-6 text-center">
            <div class="absolute top-0 inset-x-0 h-6 flex justify-center z-50">
              <div class="w-1/3 h-4 bg-[#100d10] rounded-b-xl"></div>
            </div>

            <div class="pt-8 flex flex-col items-center gap-4">
              <div class="w-14 h-14 rounded-full bg-[#3b1e3b] border border-[#eac34a]/40 flex items-center justify-center text-[#eac34a] shadow-[0_0_20px_rgba(234,195,74,0.2)]">
                <i data-lucide="gift" class="w-7 h-7 text-[#eac34a]"></i>
              </div>
              <div class="px-2 space-y-1">
                <span class="font-serif text-xl font-bold text-[#e8e0e3] block truncate leading-tight"><?php echo $heroTemplateName; ?></span>
                <span class="font-sans text-[11px] text-[#eac34a] font-semibold tracking-wider uppercase block truncate"><?php echo $heroTagline; ?></span>
              </div>
            </div>

            <div class="relative aspect-4/3 rounded-2xl overflow-hidden border border-[#eac34a]/30 shadow-md bg-[#100d10]">
              <img src="<?php echo $heroCoverImg; ?>" alt="<?php echo $heroTemplateName; ?>" class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500">
              <div class="absolute inset-0 bg-gradient-to-t from-[#151215] via-transparent to-transparent"></div>
            </div>

            <div class="space-y-3 pb-4">
              <div class="w-full bg-[#221f21] border border-[#4d444b] rounded-xl py-3 px-4 text-xs font-mono text-[#d0c3cb]/80 flex items-center justify-center gap-1.5 shadow-inner">
                <i data-lucide="key-round" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span><?php echo !empty($heroPassword) ? ('Hint Key: ' . $heroPassword) : 'Enter secret hint...'; ?></span>
              </div>
              <a href="<?php echo $heroDemoUrl; ?>" <?php echo (strpos($heroDemoUrl, 'http') === 0) ? 'target="_blank"' : ''; ?> class="w-full bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#cca830] text-[#241a00] font-sans text-xs font-bold uppercase tracking-wider py-3 rounded-xl shadow-lg flex items-center justify-center gap-2 hover:brightness-110 transition-all cursor-pointer">
                <i data-lucide="eye" class="w-3.5 h-3.5 text-[#241a00]"></i>
                <span>Explore Live Sample ↗</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Interactive Features & Trust Highlights Banner -->
  <section class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-20">
    <div class="bg-[#221f21] border border-[#eac34a]/30 rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-[0_20px_40px_rgba(0,0,0,0.5)]">
      <div class="space-y-2 text-center md:text-left">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#3b1e3b] text-[#e4b9df] text-xs font-semibold uppercase tracking-wider">
          <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
          <span>✨ Why 500+ Brothers Chose SoulScript</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold font-serif text-[#e8e0e3]">
          Not Just a Link — A Full Rakhi Surprise Experience
        </h3>
        <p class="text-xs sm:text-sm text-[#d0c3cb]">
          Photos, music, Amazon voucher, and a secret only Behen can unlock. Explore all templates below — each has a real live demo!
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3 shrink-0">
        <a href="#gallery" class="px-6 py-3 rounded-full bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#cca830] text-[#241a00] font-bold text-xs uppercase tracking-wider shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
          <i data-lucide="layout-grid" class="w-4 h-4 text-[#241a00]"></i>
          <span>Explore All 5 Templates ↗</span>
        </a>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="py-20 bg-[#1e1b1d] border-y border-[#4d444b]/30 relative z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center space-y-3 mb-16">
        <span class="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-[#eac34a]">Simple 3-Step Rakhi Gift Creation</span>
        <h2 class="text-3xl font-bold font-serif text-[#e8e0e3]">How SoulScript Works</h2>
        <p class="text-sm text-[#d0c3cb] max-w-xl mx-auto">
          Build a personalized Rakhi surprise in under 5 minutes from your phone or laptop — and share on WhatsApp instantly.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Step 1 -->
        <div class="relative bg-[#221f21] p-8 rounded-2xl border border-[#4d444b]/50 space-y-4 hover:border-[#eac34a]/40 transition-all duration-300 shadow-xl group">
          <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] text-[#e4b9df] font-serif font-bold text-xl flex items-center justify-center border border-[#e4b9df]/30 group-hover:scale-110 transition-transform">
            1
          </div>
          <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Choose Occasion Template</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Pick from Raksha Bandhan Royal, Anniversary Reveal, Birthday Magic, Perfect Proposal, or Long Distance Love. Pay securely via Razorpay.
          </p>
        </div>

        <!-- Step 2 -->
        <div class="relative bg-[#221f21] p-8 rounded-2xl border border-[#4d444b]/50 space-y-4 hover:border-[#eac34a]/40 transition-all duration-300 shadow-xl group">
          <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-serif font-bold text-xl flex items-center justify-center border border-[#eac34a]/30 group-hover:scale-110 transition-transform">
            2
          </div>
          <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Add Memories &amp; Hint Key</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Upload childhood photos, write sibling promises, add an Amazon Gift Voucher code, and set a secret hint question only Behen can answer!
          </p>
        </div>

        <!-- Step 3 -->
        <div class="relative bg-[#221f21] p-8 rounded-2xl border border-[#4d444b]/50 space-y-4 hover:border-[#eac34a]/40 transition-all duration-300 shadow-xl group">
          <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] text-[#ffe088] font-serif font-bold text-xl flex items-center justify-center border border-[#ffe088]/30 group-hover:scale-110 transition-transform">
            3
          </div>
          <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Share on WhatsApp 🎁</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            You get a private link instantly. Send to Behen via WhatsApp — she clicks, enters the childhood answer, and your entire Rakhi surprise unlocks with confetti!
          </p>
        </div>
      </div>
    </div>
  </section>
  </section>

  <!-- Templates & Pricing Section -->
  <section id="gallery" class="py-24 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center space-y-4 max-w-3xl mx-auto mb-14">
      <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#e4b9df]/20 text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="gift" class="w-3.5 h-3.5 text-[#eac34a]"></i>
        <span>🎁 Rakhi 2026 + Year-Round Occasions</span>
      </div>
      <h2 class="text-3xl sm:text-5xl font-bold font-serif text-[#e8e0e3] tracking-tight">
        Surprise Gift Templates &amp; Pricing
      </h2>
      <p class="text-xs sm:text-sm text-[#d0c3cb] leading-relaxed">
        Each template is a complete personalized experience — with photos, music, Amazon vouchers, countdown timers, and a secret unlock only the recipient can open.
      </p>
      <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-3"></div>
    </div>
      <?php
    $activeTemplates = [];
    try {
        $dbTpl = getDB();
        $stmtTpl = $dbTpl->query("SELECT * FROM templates WHERE active = 1 ORDER BY sort_order ASC, template_id ASC");
        $activeTemplates = $stmtTpl->fetchAll();
    } catch (Exception $exT) {}

    $templateSpecs = [
        'raksha_bandhan_royal' => [
            'collected' => ['Brother/Sister Name & Motto', '3-Step Ritual (Tilak, Diya, Rakhi)', '5 Sibling Promises / Vows', 'Amazon Gift Voucher Code & Shagun Photos'],
            'features'  => ['3-Step Tilak & Diya Ceremony', 'Amazon Voucher Gift Card Display', '3D Glassmorphism Vow Cards', 'Shahi Farman Antique Scroll & Sibling Promises']
        ],
        'anniversary_reveal' => [
            'collected' => ['Relationship Start Date', '3-6 Timeline Milestones', 'Personalized Love Note', '5-10 Photo Gallery'],
            'features'  => ['Live Time Counter', 'Vertical Story Timeline', 'Captured Moments Gallery', 'Signed Love Note Card']
        ],
        'birthday_magic' => [
            'collected' => ['Partner Date of Birth', '3-5 Reasons to Celebrate', 'Personalized Note', '5-10 Photo Gallery'],
            'features'  => ['Next Birthday Countdown', 'Confetti Animations', 'Interactive Reasons List', 'Festive Header Banner']
        ],
        'perfect_proposal' => [
            'collected' => ['Heartfelt Proposal Letter', 'Proposal Location & Date', 'Custom Yes Response Message', '5-10 Photo Gallery'],
            'features'  => ['Interactive Proposal Buttons', 'Instant Response Email Alert', 'Floating Heart Animations', 'Grand Proposal Card']
        ],
        'long_distance_love' => [
            'collected' => ['Buyer & Partner Cities', 'Next Reunion Date & Time', 'Shared Playlist URL', '5-10 Photo Gallery'],
            'features'  => ['Dual City Clocks', 'Live Reunion Countdown', 'Music Player Widget', 'Our Journey Gallery']
        ]
    ];
    ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
      <?php foreach ($activeTemplates as $tIdx => $t): 
        $tid = $t['template_id'];
        $tName = htmlspecialchars($t['name']);
        $tTagline = htmlspecialchars($t['tagline']);
        $tDesc = htmlspecialchars($t['description']);
        $tPrice = (float)$t['price_inr'];
        $tBadge = htmlspecialchars($t['badge'] ?? '');
        $tCover = resolveMediaUrl($t['preview_image_url']);
        $tCreateUrl = APP_URL . '/create.php?template=' . urlencode($tid);

        $tDemoUrl = !empty($t['demo_url']) ? $t['demo_url'] : '';
        $tDemoPass = !empty($t['demo_password']) ? $t['demo_password'] : '';

        // Determine concise single-line button text exactly matching Screenshot 1
        $tBtnLabel = 'CUSTOMIZE (₹' . number_format($tPrice, 0) . ')';

        $spec = $templateSpecs[$tid] ?? [
            'collected' => ['Personalized Names & Motto', 'Custom Occasion Details', 'Personalized Note / Envelope', '5-10 Photo Memory Gallery'],
            'features'  => ['Interactive Lock Gate & Confetti', 'Customized Countdown Timer', '3D Gift Envelope / Letter', 'Background Soundtrack & Photos']
        ];

        $isFeatured = ($tIdx === 0);
      ?>
      <div class="bg-[#221f21] rounded-3xl border <?php echo $isFeatured ? 'border-2 border-[#eac34a] shadow-[0_0_35px_rgba(234,195,74,0.3)]' : 'border-[#4d444b]/50 hover:border-[#eac34a]/40 shadow-2xl'; ?> transition-all duration-300 flex flex-col overflow-hidden group">
        
        <!-- Header Image Box (h-64 Height with Gradient & Badges) -->
        <div class="relative h-64 bg-[#100d10] overflow-hidden">
          <img src="<?php echo $tCover; ?>" alt="<?php echo $tName; ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-95">
          <div class="absolute inset-0 bg-gradient-to-t from-[#221f21] via-transparent to-transparent"></div>
          
          <?php if (!empty($tBadge)): ?>
            <div class="absolute top-4 left-4 bg-gradient-to-r from-[#eac34a] via-[#e4b9df] to-[#cca830] text-[#241a00] text-xs font-extrabold px-3.5 py-1.5 rounded-full uppercase tracking-wider shadow-lg flex items-center gap-1.5">
              <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#241a00]"></i>
              <span><?php echo $tBadge; ?></span>
            </div>
          <?php endif; ?>

          <div class="absolute top-4 right-4 bg-[#eac34a] text-[#241a00] font-extrabold text-sm sm:text-base px-3.5 py-1 rounded-xl shadow-md">
            ₹<?php echo number_format($tPrice, 0); ?>
          </div>

          <div class="absolute bottom-4 left-4 right-4 text-[#e8e0e3] space-y-0.5">
            <h3 class="text-2xl font-bold font-serif leading-snug"><?php echo $tName; ?></h3>
            <p class="text-xs text-[#eac34a] font-medium"><?php echo $tTagline; ?></p>
          </div>
        </div>

        <!-- Body Area -->
        <div class="p-6 sm:p-8 flex-1 flex flex-col justify-between space-y-6">
          <p class="text-xs text-[#d0c3cb] leading-relaxed font-normal">
            <?php echo $tDesc; ?>
          </p>

          <!-- Inner 2-Column Info Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-[#151215] p-5 rounded-2xl border border-[#4d444b]">
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>COLLECTED FIELDS:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <?php foreach ($spec['collected'] as $cItem): ?>
                  <li class="flex items-start gap-1.5"><span class="text-[#eac34a">•</span><span><?php echo htmlspecialchars($cItem); ?></span></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#e4b9df]"></i>
                <span>RESULT FEATURES:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <?php foreach ($spec['features'] as $fItem): ?>
                  <li class="flex items-start gap-1.5"><span class="text-[#e4b9df">•</span><span><?php echo htmlspecialchars($fItem); ?></span></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

          <!-- Action Buttons Area -->
          <div class="space-y-3 pt-1">
            <div class="flex flex-col sm:flex-row items-center gap-3">
              <a href="<?php echo $tCreateUrl; ?>" class="w-full sm:flex-1 py-3.5 px-6 rounded-full bg-gradient-to-r from-[#eac34a] via-[#e4b9df] to-[#cca830] hover:brightness-110 text-[#241a00] font-bold text-xs uppercase tracking-wider transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 no-underline text-center shrink-0 whitespace-nowrap">
                <i data-lucide="gift" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap"><?php echo $tBtnLabel; ?></span>
              </a>

              <?php if (!empty($tDemoUrl)): ?>
                <a href="<?php echo $tDemoUrl; ?>" target="_blank" class="w-full sm:w-auto px-5 py-3.5 rounded-full bg-[#151215] hover:bg-[#3b1e3b] text-[#e8e0e3] font-semibold text-xs border border-[#4d444b] hover:border-[#eac34a]/60 transition-all flex items-center justify-center gap-1.5 shrink-0 no-underline whitespace-nowrap">
                  <i data-lucide="eye" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
                  <span class="whitespace-nowrap">Live Sample</span>
                </a>
              <?php endif; ?>
            </div>

            <?php if (!empty($tDemoPass)): ?>
              <div class="w-full text-center text-[11px] text-[#e4b9df] font-medium bg-[#151215] py-2 px-3 rounded-xl border border-[#4d444b]/60 flex items-center justify-center gap-1.5">
                <i data-lucide="key-round" class="w-3.5 h-3.5 text-[#eac34a] shrink-0"></i>
                <span>Demo Password: <strong class="text-[#eac34a] font-mono tracking-wider"><?php echo htmlspecialchars($tDemoPass); ?></strong></span>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Security & Privacy Section -->
  <section class="py-20 bg-[#100d10] text-[#e8e0e3] border-t border-[#4d444b]/30 relative z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#3b1e3b] border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-wider">
            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
            <span>Encrypted Hint Password Protection</span>
          </div>

          <h2 class="text-3xl sm:text-4xl font-bold font-serif leading-tight">
            Your Love Story Stays 100% Private &amp; Protected
          </h2>

          <p class="text-xs sm:text-sm text-[#d0c3cb] leading-relaxed">
            We safeguard your personal love letters, photos, and memories with enterprise-grade security protocols:
          </p>

          <div class="space-y-4 text-xs text-[#d0c3cb]">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center shrink-0 border border-[#eac34a]/30">
                <i data-lucide="key-round" class="w-4 h-4"></i>
              </div>
              <div>
                <strong class="text-[#e8e0e3] block font-semibold text-sm">Hashed Hint Verification</strong>
                Your answer is stored as an encrypted SHA-256 hash. Surprise content is blocked until your partner passes the hint test.
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-[#3b1e3b] text-[#e4b9df] flex items-center justify-center shrink-0 border border-[#e4b9df]/30">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
              </div>
              <div>
                <strong class="text-[#e8e0e3] block font-semibold text-sm">Cooldown Brute-Force Guard</strong>
                A 60-second lockout automatically engages after 5 incorrect password attempts to stop guessing.
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-[#3b1e3b] text-[#ffe088] flex items-center justify-center shrink-0 border border-[#ffe088]/30">
                <i data-lucide="share-2" class="w-4 h-4"></i>
              </div>
              <div>
                <strong class="text-[#e8e0e3] block font-semibold text-sm">Noindex Search Protection</strong>
                All generated pages include <code class="bg-[#221f21] text-[#eac34a] px-1.5 py-0.5 rounded">noindex, nofollow</code> headers so search engines will never crawl your memory page.
              </div>
            </div>
          </div>
        </div>

        <!-- Lock Screen Mini Preview Card -->
        <div class="bg-[#221f21] border border-[#eac34a]/30 rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative overflow-hidden">
          <div class="flex items-center justify-between pb-4 border-b border-[#4d444b]/50">
            <div class="flex items-center gap-2 text-xs font-mono text-[#d0c3cb]">
              <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
              <span>soulscript.in/gift/your-private-link</span>
            </div>
            <span class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] border border-[#e4b9df]/30 px-2 py-0.5 rounded font-mono">Protected</span>
          </div>

          <div class="text-center space-y-3 py-4">
            <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30 shadow-[0_0_15px_rgba(234,195,74,0.2)]">
              <i data-lucide="key-round" class="w-7 h-7"></i>
            </div>
            <h4 class="text-xl font-bold font-serif text-[#e8e0e3]">Hint Security Gate</h4>
            <p class="text-xs text-[#d0c3cb]">"Where did we take our very first trip together in 2022?"</p>
          </div>

          <div class="space-y-3">
            <div class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#d0c3cb]/60 font-mono text-center">
              Enter memory answer...
            </div>
            <div class="w-full bg-[#eac34a] text-[#241a00] font-sans font-bold text-xs uppercase tracking-widest py-3 rounded-full text-center shadow-md">
              Unlock The Surprise ❤️
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-20 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center space-y-3 mb-16">
      <span class="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-[#eac34a]">Loved By Couples & Siblings</span>
      <h2 class="text-3xl font-bold font-serif text-[#e8e0e3]">Real Stories. Real Surprises.</h2>
      <p class="text-sm text-[#d0c3cb]">Surprising siblings & partners across Mumbai, Delhi, Bangalore, Jaipur, and NRI cities worldwide.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "My husband was speechless on our 2nd anniversary! When he unlocked the link with our secret trip answer and saw the live time counter and timeline of our photos, he literally had tears in his eyes."
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Rohan &amp; Ananya</span>
          <span class="text-[#eac34a] text-[11px]">Anniversary Reveal</span>
        </div>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "I used the Perfect Proposal template for Priya in Jaipur. When she clicked 'YES ❤️', I got an instant response while she was smiling at me! Worth every rupee."
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Aman &amp; Priya</span>
          <span class="text-[#eac34a] text-[11px]">Perfect Proposal</span>
        </div>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "Living in London while my girlfriend is in Bangalore is tough. The Long Distance Love template with dual city clocks and reunion countdown made us feel so connected."
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Vikram &amp; Sneha</span>
          <span class="text-[#eac34a] text-[11px]">Long Distance Love</span>
        </div>
      </div>

      <!-- Rakhi Testimonial 4 -->
      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "Didi rone lagi jab usne link unlock kiya! Usne kaha yeh toh ab tak ka sabse best Rakhi gift hai. The Amazon voucher was the cherry on top — she could shop whatever she wanted!"
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Aryan S.</span>
          <span class="text-[#eac34a] text-[11px]">Raksha Bandhan Royal</span>
        </div>
      </div>

      <!-- Rakhi Testimonial 5 — NRI -->
      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "Meri behen Dubai mein rehti hai. Maine SoulScript link WhatsApp pe bheja. Usne raat ko unlock kiya aur call karke rona shuru kar diya. Distance khatam ho gaya uss moment mein!"
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Sahil K.</span>
          <span class="text-[#eac34a] text-[11px]">Raksha Bandhan Royal · NRI</span>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="py-20 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center space-y-3 mb-12">
      <span class="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-[#eac34a]">💬 Common Questions</span>
      <h2 class="text-3xl font-bold font-serif text-[#e8e0e3]">Everything You Need to Know</h2>
      <p class="text-sm text-[#d0c3cb]">Quick answers before you create your surprise.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-4xl mx-auto">

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 space-y-2">
        <h4 class="font-bold text-[#e8e0e3] text-sm flex items-center gap-2">
          <i data-lucide="gift" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
          Amazon Voucher kaise add karte hain?
        </h4>
        <p class="text-xs text-[#d0c3cb] leading-relaxed">Gift create karne ke baad, apne Buyer Portal mein login karein aur Amazon Gift Card code paste karein. Behen ko link unlock karne par code dikhega.</p>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 space-y-2">
        <h4 class="font-bold text-[#e8e0e3] text-sm flex items-center gap-2">
          <i data-lucide="send" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
          Kya Behen ka WhatsApp number chahiye?
        </h4>
        <p class="text-xs text-[#d0c3cb] leading-relaxed">Nahi. Aapko sirf ek private link milta hai — use WhatsApp, Instagram DM, ya Email — kisi bhi platform se bhej sakte hain.</p>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 space-y-2">
        <h4 class="font-bold text-[#e8e0e3] text-sm flex items-center gap-2">
          <i data-lucide="lock" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
          Kya koi bhi link dekh sakta hai?
        </h4>
        <p class="text-xs text-[#d0c3cb] leading-relaxed">Nahi. Hamare hint lock se sirf wahi open kar sakta hai jo secret childhood question ka answer jaanta ho. Complete privacy guaranteed.</p>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 space-y-2">
        <h4 class="font-bold text-[#e8e0e3] text-sm flex items-center gap-2">
          <i data-lucide="clock" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
          Kitne time mein ready ho jaata hai?
        </h4>
        <p class="text-xs text-[#d0c3cb] leading-relaxed">5–10 minutes mein aapka gift link ready ho jaata hai. Payment ke turant baad form milta hai — fill karo aur instantly share karo.</p>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 space-y-2">
        <h4 class="font-bold text-[#e8e0e3] text-sm flex items-center gap-2">
          <i data-lucide="pencil" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
          Kya baad mein edit kar sakte hain?
        </h4>
        <p class="text-xs text-[#d0c3cb] leading-relaxed">Haan! Buyer Portal se kabhi bhi content update kar sakte hain — Amazon code, photos, music, messages — sabkuch. Page 12 mahine tak live rehta hai.</p>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 space-y-2">
        <h4 class="font-bold text-[#e8e0e3] text-sm flex items-center gap-2">
          <i data-lucide="shield-check" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
          Payment safe hai?
        </h4>
        <p class="text-xs text-[#d0c3cb] leading-relaxed">100% safe — Razorpay se payment hoti hai (India ka #1 payment gateway). UPI, Card, NetBanking — sab options available hain.</p>
      </div>

    </div>
  </section>

  <!-- Buyer Login CTA Banner -->
  <section class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-12 z-10 relative">
    <div class="bg-gradient-to-r from-[#3b1e3b] via-[#221f21] to-[#3b1e3b] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-6">
      <div class="space-y-2 text-center sm:text-left">
        <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-[#eac34a] bg-[#151215] px-3 py-1 rounded-full border border-[#eac34a]/30">🔑 Already Bought A Gift Website?</span>
        <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">Log In To Edit &amp; Update Your Surprise Page</h3>
        <p class="text-xs text-[#d0c3cb]">Update your Amazon voucher code, background music, photos, sibling promises, or any details anytime using your Email &amp; Secret Password.</p>
      </div>
      <a href="<?php echo APP_URL; ?>/edit.php" class="px-8 py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-full shadow-xl transition-all shrink-0 flex items-center gap-2">
        <i data-lucide="key-round" class="w-4 h-4"></i>
        <span>Buyer Login Portal →</span>
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-[#100d10] border-t border-[#4d444b]/30 pt-12 pb-8 relative z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        
        <!-- Brand Column -->
        <div class="space-y-4 md:col-span-1">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] shadow-[0_0_15px_rgba(234,195,74,0.3)]">
              <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
                <i data-lucide="heart" class="w-3.5 h-3.5 text-[#eac34a] fill-[#eac34a]/30"></i>
              </div>
            </div>
            <span class="text-xl font-bold font-serif text-[#e8e0e3] tracking-tight">SoulScript</span>
          </div>
          <p class="text-xs text-[#d0c3cb]/80 leading-relaxed font-light">
            Crafting personalized digital surprises for Rakhi, anniversaries, birthdays, proposals & long-distance love.
          </p>
          <div class="flex items-center gap-2 text-[11px] text-[#eac34a] bg-[#3b1e3b] border border-[#e4b9df]/20 px-3 py-1.5 rounded-full w-fit">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
            <span>100% Private Link-Only Pages</span>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Templates</h4>
          <ul class="space-y-2 text-xs text-[#d0c3cb]/80">
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">🪔 Raksha Bandhan Royal (₹499)</a></li>
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Anniversary Reveal (₹499)</a></li>
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Birthday Magic (₹399)</a></li>
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Perfect Proposal (₹599)</a></li>
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Long Distance Love (₹449)</a></li>
          </ul>
        </div>

        <!-- Security & Privacy -->
        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Privacy &amp; Guarantee</h4>
          <ul class="space-y-2 text-xs text-[#d0c3cb]/80">
            <li class="flex items-center gap-2">
              <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
              <span>Hint Password Security Gate</span>
            </li>
            <li class="flex items-center gap-2">
              <i data-lucide="eye-off" class="w-3.5 h-3.5 text-[#e4b9df]"></i>
              <span>Search Engine Excluded</span>
            </li>
            <li class="flex items-center gap-2">
              <i data-lucide="shield" class="w-3.5 h-3.5 text-[#eac34a]"></i>
              <span>Razorpay Secured Payments</span>
            </li>
          </ul>
        </div>

        <!-- Support -->
        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Need Support?</h4>
          <p class="text-xs text-[#d0c3cb]/80 leading-relaxed">
            Questions about creating or delivering your surprise link?
          </p>
          <p class="text-xs text-[#eac34a] font-medium font-mono">support@soulscript.in</p>
          <p class="text-[11px] text-[#d0c3cb]/50">Includes 12-Month validity guarantee for all generated pages.</p>
        </div>
      </div>

    </div>

    <!-- Full-Width Dedicated Bottom Footer Bar Strip -->
    <div class="mt-12 py-6 border-t border-[#4d444b]/40 bg-[#0d0a0d]">
      <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#d0c3cb]/70 gap-4">
        <span>© 2026 SoulScript. All rights reserved. Made with ❤️ for couples everywhere.</span>
        <a href="<?php echo APP_URL; ?>/admin/index.php" class="text-[11px] text-[#d0c3cb]/50 hover:text-[#eac34a] transition-colors flex items-center gap-1 font-mono">
          <span>Admin Portal</span>
          <i data-lucide="lock" class="w-3 h-3 text-[#eac34a]/70"></i>
        </a>
      </div>
    </div>
  </footer>

  <!-- Checkout Modal -->
  <div id="checkoutModal" class="modal-overlay fixed inset-0 bg-black/85 backdrop-blur-md z-50 hidden items-center justify-center p-4 sm:p-6">
    <div class="modal-container bg-[#191518] border border-[#eac34a]/30 rounded-3xl max-w-2xl sm:max-w-[720px] w-full p-6 sm:p-8 relative shadow-2xl">
      <button class="modal-close text-[#d0c3cb] hover:text-white text-2xl absolute top-4 right-5 p-1 cursor-pointer transition-colors z-10" onclick="closeCheckout()">&times;</button>
      <h2 class="font-serif text-2xl text-[#e8e0e3] font-bold mb-0.5 pr-6" id="modalTemplateTitle">Checkout</h2>
      <p class="text-xs text-[#d0c3cb] mb-6">Enter your details to unlock your partner personalization form.</p>
      
      <form id="checkoutForm" onsubmit="handleCheckoutSubmit(event); return false;">
        <input type="hidden" id="selectedTemplateId" value="">
        
        <div id="checkoutErrorMsg" class="hidden mb-4 p-3 bg-[#3b1e3b] border border-[#e4b9df]/40 text-[#e4b9df] rounded-xl text-xs font-semibold text-center"></div>
        <div id="loggedInNotice" class="hidden mb-4 p-3 bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9] rounded-xl text-xs font-semibold text-center flex items-center justify-center gap-2"></div>

        <!-- 2-Column Responsive Grid on Desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <!-- Column 1: Full Name -->
          <div class="form-group">
            <label class="form-label text-xs font-semibold text-[#d0c3cb] block mb-1">Your Full Name (Buyer) *</label>
            <input type="text" id="buyerName" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Rohan Sharma" minlength="2" maxlength="60" required>
          </div>

          <!-- Column 2: WhatsApp Number -->
          <div class="form-group">
            <label class="form-label text-xs font-semibold text-[#d0c3cb] block mb-1">WhatsApp Mobile Number *</label>
            <div class="flex rounded-xl overflow-hidden border border-[#4d444b] focus-within:border-[#eac34a] bg-[#100d10] transition-colors">
              <div class="bg-[#221f21] text-[#eac34a] font-mono text-xs font-bold px-3 flex items-center border-r border-[#4d444b] gap-1 shrink-0 select-none">
                <span>IN</span>
                <span>+91</span>
              </div>
              <input type="tel" id="buyerPhone" class="w-full bg-transparent px-3 py-2.5 text-sm text-[#e8e0e3] focus:outline-none font-mono tracking-wider placeholder-[#d0c3cb]/40" placeholder="9876543210" pattern="^[6-9]\d{9}$" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
            </div>
            <span class="text-[10px] text-[#d0c3cb]/60 mt-1 block">Enter 10-digit mobile number</span>
          </div>

          <!-- Column 1: Email Address -->
          <div class="form-group">
            <label class="form-label text-xs font-semibold text-[#d0c3cb] block mb-1">Email Address *</label>
            <input type="email" id="buyerEmail" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="rohan@example.com" required>
          </div>

          <!-- Column 2: Secret Edit Password -->
          <div class="form-group" id="buyerPasswordGroup">
            <div class="flex items-center justify-between mb-1">
              <label class="form-label text-xs font-semibold text-[#d0c3cb]">Secret Edit Password *</label>
              <span class="text-[10px] text-[#eac34a] font-bold" id="passStrengthBadge">Min 6 chars</span>
            </div>
            <div class="relative">
              <input type="password" id="buyerPassword" minlength="6" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 pr-10 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none font-mono" placeholder="••••••••" oninput="checkPasswordStrength(this.value)" required>
              <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-2.5 text-[#d0c3cb] hover:text-[#eac34a] transition-colors">
                <i data-lucide="eye" id="passEyeIcon" class="w-4 h-4"></i>
              </button>
            </div>
            <span class="text-[10px] text-[#d0c3cb]/70 mt-1 block">🔒 Used to log into your Buyer Portal</span>
          </div>
        </div>

        <!-- Total Amount & Payment CTA Button (Exact Reference Layout) -->
        <div class="p-4 sm:p-5 bg-[#100d10] border border-[#4d444b] rounded-2xl flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-4">
          <div>
            <span class="text-[11px] uppercase font-extrabold text-[#d0c3cb]/70 tracking-wider block mb-0.5">Total Investment</span>
            <span class="font-serif text-3xl font-extrabold text-[#eac34a]" id="modalPrice">₹499</span>
          </div>
          <button type="button" disabled class="px-6 py-3.5 bg-[#eac34a] text-[#241a00] font-sans text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-lg flex items-center justify-center gap-2 cursor-not-allowed opacity-80 shrink-0 whitespace-nowrap" id="checkoutBtn">
            <span>Proceed to Pay &amp; Personalize</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </button>
        </div>

        <button type="button" onclick="simulateDevPayment()" class="w-full bg-transparent text-[#eac34a] border border-[#eac34a]/40 font-sans text-[11px] font-semibold py-2.5 rounded-xl hover:border-[#eac34a] transition-all cursor-pointer">
          ⚡ Test Mode: Instant Skip Payment &amp; Personalize
        </button>
      </form>
    </div>
  </div>

  <script>
    lucide.createIcons();

    let currentTemplateId = '';
    let currentPrice = 0;

    function checkPasswordStrength(pass) {
      const badge = document.getElementById('passStrengthBadge');
      if (!badge) return;
      if (pass.length < 6) {
        badge.innerText = 'Weak (Min 6 chars)';
        badge.className = 'text-[10px] text-rose-400 font-bold';
      } else if (pass.length < 10) {
        badge.innerText = 'Good Password ✓';
        badge.className = 'text-[10px] text-amber-400 font-bold';
      } else {
        badge.innerText = 'Strong Password 💪';
        badge.className = 'text-[10px] text-emerald-400 font-bold';
      }
    }

    function togglePasswordVisibility() {
      const passInput = document.getElementById('buyerPassword');
      const eyeIcon = document.getElementById('passEyeIcon');
      if (passInput.type === 'password') {
        passInput.type = 'text';
        eyeIcon.setAttribute('data-lucide', 'eye-off');
      } else {
        passInput.type = 'password';
        eyeIcon.setAttribute('data-lucide', 'eye');
      }
      lucide.createIcons();
    }

    let isLoggedInBuyerSession = false;

    async function checkActiveBuyerSession() {
      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/buyer_session.php', { credentials: 'same-origin' });
        const data = await res.json();
        const passGroup = document.getElementById('buyerPasswordGroup');
        const passInput = document.getElementById('buyerPassword');
        const noticeBox = document.getElementById('loggedInNotice');

        if (data.logged_in && data.buyer_email) {
          isLoggedInBuyerSession = true;
          if (data.buyer_name) document.getElementById('buyerName').value = data.buyer_name;
          if (data.buyer_phone) document.getElementById('buyerPhone').value = data.buyer_phone;
          if (data.buyer_email) document.getElementById('buyerEmail').value = data.buyer_email;

          passGroup.classList.add('hidden');
          passInput.removeAttribute('required');
          passInput.value = 'LOGGED_IN_SESSION';

          noticeBox.innerHTML = `<i data-lucide="user-check" class="w-4 h-4"></i> <span>Logged in as <strong>${escapeHtml(data.buyer_email)}</strong> (Buying a New Gift)</span>`;
          noticeBox.classList.remove('hidden');
          if (typeof lucide === 'object') lucide.createIcons();
        } else {
          isLoggedInBuyerSession = false;
          passGroup.classList.remove('hidden');
          passInput.setAttribute('required', 'true');
          noticeBox.classList.add('hidden');
        }
      } catch (err) {
        console.log('Session check error:', err);
      }
    }

    function openCheckout(templateId, name, price) {
      currentTemplateId = templateId;
      currentPrice = price;
      document.getElementById('selectedTemplateId').value = templateId;
      document.getElementById('modalTemplateTitle').innerText = name;
      document.getElementById('modalPrice').innerText = '₹' + price;
      const modal = document.getElementById('checkoutModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      checkActiveBuyerSession();
    }

    function selectTemplate(templateId, name, price) {
      openCheckout(templateId, name, price);
    }

    function closeCheckout() {
      const modal = document.getElementById('checkoutModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    async function handleCheckoutSubmit(e) {
      e.preventDefault();
      const errBox = document.getElementById('checkoutErrorMsg');
      errBox.classList.add('hidden');

      const rawPhone = document.getElementById('buyerPhone').value.trim();
      const cleanPhone = rawPhone.replace(/[^0-9]/g, '');

      if (!/^[6-9]\d{9}$/.test(cleanPhone)) {
        errBox.innerText = 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.';
        errBox.classList.remove('hidden');
        return;
      }

      const buyerPassword = document.getElementById('buyerPassword').value;
      if (!isLoggedInBuyerSession && buyerPassword.length < 6) {
        errBox.innerText = 'Please create a Secret Edit Password with at least 6 characters.';
        errBox.classList.remove('hidden');
        return;
      }

      const fullPhone = '+91' + cleanPhone;

      const btn = document.getElementById('checkoutBtn');
      btn.innerText = 'Creating Order...';
      btn.disabled = true;

      const payload = {
        buyer_name: document.getElementById('buyerName').value,
        buyer_phone: fullPhone,
        buyer_email: document.getElementById('buyerEmail').value,
        buyer_password: buyerPassword,
        template_id: currentTemplateId
      };

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/create_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          const options = {
            key: data.razorpay_key_id,
            amount: currentPrice * 100,
            currency: 'INR',
            name: 'SoulScript',
            description: 'Surprise Reveal Page Order',
            order_id: data.order.razorpay_order_id,
            handler: async function (response) {
              await fetch('<?php echo APP_URL; ?>/api/webhook_razorpay.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  order_id: data.order.order_id,
                  razorpay_payment_id: response.razorpay_payment_id,
                  status: 'paid'
                })
              });
              window.location.href = '<?php echo APP_URL; ?>/create.php?order_id=' + data.order.order_id;
            },
            prefill: {
              name: payload.buyer_name,
              email: payload.buyer_email,
              contact: payload.buyer_phone
            },
            theme: { color: '#eac34a' }
          };

          const rzp = new Razorpay(options);
          rzp.open();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Server error: ' + err.message);
      } finally {
        btn.innerText = 'Proceed to Pay & Personalize →';
        btn.disabled = false;
      }
    }

    async function simulateDevPayment() {
      const rawPhone = document.getElementById('buyerPhone').value.trim() || '9876543210';
      const cleanPhone = rawPhone.replace(/[^0-9]/g, '');
      const fullPhone = '+91' + (cleanPhone.length === 10 ? cleanPhone : '9876543210');

      const buyerPass = document.getElementById('buyerPassword').value.trim() || '123456';
      const payload = {
        buyer_name: document.getElementById('buyerName').value || 'Test Buyer',
        buyer_phone: fullPhone,
        buyer_email: document.getElementById('buyerEmail').value || 'test@example.com',
        buyer_password: buyerPass,
        template_id: currentTemplateId || 'anniversary_reveal'
      };

      const res = await fetch('<?php echo APP_URL; ?>/api/create_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data.success) {
        await fetch('<?php echo APP_URL; ?>/api/webhook_razorpay.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            order_id: data.order.order_id,
            razorpay_payment_id: 'sim_pay_' + Date.now(),
            status: 'paid'
          })
        });

        window.location.href = '<?php echo APP_URL; ?>/create.php?order_id=' + data.order.order_id;
      }
    }

    function toggleMobileNavMenu() {
      const menu = document.getElementById('mobileDrawerMenu');
      const icon = document.getElementById('hamburgerIcon');
      if (menu) {
        menu.classList.toggle('hidden');
        const isOpen = !menu.classList.contains('hidden');
        if (icon) {
          icon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
          if (typeof lucide === 'object') lucide.createIcons();
        }
      }
    }

    // Smart Smooth Auto-Hiding Header on Scroll (Mobile & Desktop)
    (function() {
      let lastScrollY = window.scrollY;
      const header = document.getElementById('mainHeader');
      const scrollThreshold = 8;

      if (!header) return;

      window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;
        const mobileDrawer = document.getElementById('mobileDrawerMenu');
        const isMobileMenuOpen = mobileDrawer && !mobileDrawer.classList.contains('hidden');

        // Always show header at top of page or when mobile drawer menu is open
        if (currentScrollY <= 60 || isMobileMenuOpen) {
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
        } else {
          // Scrolling Up -> Smoothly Reveal Header
          header.classList.remove('-translate-y-full');
        }

        lastScrollY = currentScrollY;
      }, { passive: true });
    })();
  </script>
</body>
</html>
