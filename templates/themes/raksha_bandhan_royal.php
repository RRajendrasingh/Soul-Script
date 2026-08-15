<?php
// templates/themes/raksha_bandhan_royal.php

$tf = $content['template_fields'] ?? [];
$media = $content['media'] ?? [];
$reasons = $tf['reasons'] ?? [];
$tokens = $data['tokens'] ?? [];

$partnerName = htmlspecialchars($content['partner_name'] ?? 'Di');
$buyerName = htmlspecialchars($content['buyer_name'] ?? 'Someone Special');
$taglineQuote = htmlspecialchars($content['tagline_quote'] ?? "World's Best Sister 👑");
$loveNoteText = htmlspecialchars($content['love_note_text'] ?? "Choti / Didi, mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🧧 (Aur haan, TV remote mera hi रहेगा! 😄)");

$rakhiVoucherStatus = $data['rakhi_voucher_status'] ?? null;
$rakhiAffiliateProducts = $data['rakhi_affiliate_products'] ?? [];

$promisesList = (!empty($reasons)) ? $reasons : [
    "Always protect you & stand by your side 🛡️",
    "Keep all your deepest secrets safe 🤫",
    "Sponsor your favorite food & treats 🍕",
    "Never let you feel alone, wherever I am 💖",
    "Always be your forever crime partner 🕵️‍♂️"
];

$voucherCode = '';
if (!empty($tokens)) {
    $voucherCode = $tokens[0]['shagun_voucher_code'] ?? $tokens[0]['code'] ?? '';
}

$pInitial = strtoupper(substr($content['partner_name'] ?? 'P', 0, 1));
$cleanReceiverPhoto = !empty($content['receiver_photo']) ? resolveMediaUrl($content['receiver_photo']) : '';

if ($cleanReceiverPhoto) {
    $photoAvatarHtml = '<img id="receiverPhotoImg" src="'.htmlspecialchars($cleanReceiverPhoto).'" onerror="this.onerror=null; this.parentElement.innerHTML=\'<div class=\\\'w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner\\\'>'.$pInitial.'</div>\';" alt="'.$partnerName.'" class="w-full h-full rounded-full object-cover border-2 border-[#151215] z-10">';
} else {
    $photoAvatarHtml = '<div id="receiverPhotoImg" class="w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner z-10">'.$pInitial.'</div>';
}
?>

<!-- RICH ROYAL MANDALA BACKGROUND ARTWORK LAYER (TOP, MIDDLE & BOTTOM) -->
<div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
  <!-- Top-Left Mandala -->
  <div class="absolute -top-16 -left-16 sm:-top-24 sm:-left-24 w-48 h-48 sm:w-80 sm:h-80 opacity-[0.08] sm:opacity-20 mix-blend-screen">
    <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/gold_mandala_corner.svg" class="w-full h-full animate-spin-slow">
  </div>

  <!-- Top-Right Mandala -->
  <div class="absolute -top-16 -right-16 sm:-top-24 sm:-right-24 w-48 h-48 sm:w-80 sm:h-80 opacity-[0.08] sm:opacity-20 mix-blend-screen">
    <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/gold_mandala_corner.svg" class="w-full h-full rotate-90 animate-spin-slow">
  </div>

  <!-- Middle-Left Side Mandala -->
  <div class="absolute top-1/3 -left-20 sm:-left-36 w-64 sm:w-[500px] opacity-[0.08] sm:opacity-[0.25] mix-blend-screen animate-pulse-slow">
    <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/custom_mandala.svg" class="w-full h-full animate-spin-slow">
  </div>

  <!-- Middle-Right Side Mandala -->
  <div class="absolute top-2/3 -right-20 sm:-right-36 w-64 sm:w-[500px] opacity-[0.08] sm:opacity-[0.25] mix-blend-screen animate-pulse-slow">
    <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/custom_mandala.svg" class="w-full h-full animate-spin-slow">
  </div>

  <!-- Bottom-Left Mandala -->
  <div class="absolute -bottom-16 -left-16 sm:-bottom-24 sm:-left-24 w-56 h-56 sm:w-96 sm:h-96 opacity-[0.08] sm:opacity-20 mix-blend-screen">
    <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/gold_mandala_corner.svg" class="w-full h-full rotate-270">
  </div>

  <!-- Bottom-Right Mandala -->
  <div class="absolute -bottom-16 -right-16 sm:-bottom-24 sm:-right-24 w-56 h-56 sm:w-96 sm:h-96 opacity-[0.08] sm:opacity-20 mix-blend-screen">
    <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/gold_mandala_corner.svg" class="w-full h-full rotate-180">
  </div>

  <!-- Floating Golden Twinkling Sparkle Dust Particles -->
  <div class="absolute top-12 left-1/4 text-[#eac34a] text-xs sm:text-sm animate-ping opacity-60">✨</div>
  <div class="absolute top-36 right-1/3 text-[#ffd700] text-sm sm:text-base animate-pulse opacity-70">✦</div>
  <div class="absolute top-1/2 left-10 text-[#eac34a] text-lg animate-pulse opacity-50">✨</div>
  <div class="absolute top-2/3 right-12 text-[#ffd700] text-xs sm:text-sm animate-ping opacity-60">✧</div>
  <div class="absolute bottom-40 left-1/3 text-[#eac34a] text-sm animate-pulse opacity-70">✦</div>
  <div class="absolute bottom-20 right-1/4 text-[#ffd700] text-base animate-ping opacity-60">✨</div>
</div>

<!-- TOP-RIGHT HANGING BRASS & TERRACOTTA DIYAS ON CHAINS -->
<div class="absolute top-0 right-4 sm:right-12 z-20 pointer-events-none flex gap-4 sm:gap-6">
  <div class="flex flex-col items-center animate-bell-swing">
    <div class="w-0.5 h-16 sm:h-24 bg-gradient-to-b from-[#eac34a] to-[#cca830]"></div>
    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-tr from-amber-600 via-yellow-400 to-amber-500 border-2 border-[#eac34a] p-1 shadow-[0_0_20px_#f59e0b] flex items-center justify-center -mt-1">
      <span class="text-base sm:text-lg animate-flame">🪔</span>
    </div>
  </div>
  <div class="flex flex-col items-center animate-bell-swing" style="animation-delay: 0.5s;">
    <div class="w-0.5 h-24 sm:h-36 bg-gradient-to-b from-[#eac34a] to-[#cca830]"></div>
    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-gradient-to-tr from-rose-600 via-pink-400 to-amber-400 border-2 border-[#eac34a] p-1 shadow-[0_0_15px_#ec4899] flex items-center justify-center -mt-1">
      <span class="text-sm sm:text-base animate-flame">🪔</span>
    </div>
  </div>
</div>

<!-- SECTION 1: HERO HEADER & DOUBLE GOLDEN RING AVATAR -->
<section class="relative pt-24 pb-12 px-4 text-center sm:text-left z-10 w-full">
  <div class="max-w-5xl mx-auto space-y-12">
    
    <!-- Top Row: Profile & Titles -->
    <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-8 md:gap-16">
      
      <!-- Left: Double Golden Ring Photo Avatar Frame -->
      <div id="doubleGoldenRingAvatar" class="relative w-48 h-48 md:w-56 md:h-56 rounded-full bg-gradient-to-tr from-[#cca830] via-[#ffd700] to-[#b8860b] p-1.5 shadow-[0_0_60px_rgba(234,195,74,0.45)] shrink-0 group hover:scale-105 transition-transform duration-500">
        <div class="w-full h-full rounded-full border-[3px] border-[#100d10] p-1 bg-gradient-to-br from-[#2a060b] to-[#140205]">
          <div class="w-full h-full bg-[#151215] rounded-full overflow-hidden flex items-center justify-center relative shadow-inner">
            <?= $photoAvatarHtml ?>
            <div id="tilakMarkOnAvatar" class="hidden absolute top-10 md:top-12 left-1/2 -translate-x-1/2 w-5 h-5 rounded-full bg-gradient-to-tr from-red-600 via-red-500 to-amber-400 shadow-[0_0_20px_#ef4444] z-30 flex items-center justify-center">
              <span class="w-2 h-2 rounded-full bg-white/90"></span>
            </div>
          </div>
        </div>
        <!-- Decorative Glow Base -->
        <div class="absolute -inset-4 bg-[#eac34a]/10 blur-xl rounded-full z-[-1] animate-pulse-slow"></div>
      </div>

      <!-- Right: Greetings & Titles -->
      <div class="space-y-4 w-full md:max-w-3xl text-center md:text-left">
        <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#3b1e3b]/80 border border-[#eac34a]/40 backdrop-blur-md text-[#eac34a] text-[10px] md:text-[16px] font-bold shadow-lg">
          <i data-lucide="crown" class="w-4 h-4 text-[#eac34a]"></i>
          <span><?= $taglineQuote ?></span>
        </div>

        <!-- 42px (Mobile) and 68px (Desktop) are exact Golden Ratio steps from 16px -->
        <h1 class="text-[42px] md:text-[68px] font-bold font-handwriting text-[#e8e0e3] tracking-wide leading-[1.2] drop-shadow-md py-2">
          Happy Raksha Bandhan, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#cca830] pl-1 pr-2"><?= $partnerName ?></span><span class="ml-1">!</span> <span class="text-[26px] md:text-[42px] inline-block -translate-y-1 animate-flame">🪔</span>
        </h1>
        <!-- 16px is the base size, 1.618 is the golden line-height -->
        <p class="text-[14px] md:text-[16px] text-[#d0c3cb] max-w-lg mx-auto md:mx-0 leading-[1.618]">
          A digital celebration of our unbreakable bond, childhood memories, and sacred vows.
        </p>
      </div>

    </div>

    <!-- Bottom Row: PHOTOREALISTIC WRIST RAKHI HERO BANNER -->
    <div id="heroWristBannerContainer" class="w-full bg-[#151215] border border-[#eac34a]/30 rounded-[2.5rem] shadow-[0_0_50px_rgba(234,195,74,0.15)] relative overflow-hidden flex flex-col md:flex-row group transition-all duration-500 hover:shadow-[0_0_60px_rgba(234,195,74,0.3)] hover:border-[#eac34a]/60">
      
      <!-- Left side: Photorealistic Image (Brother's Wrist with Rakhi) -->
      <div class="w-full md:w-5/12 h-56 md:h-auto relative overflow-hidden bg-gradient-to-br from-[#2a060b] to-[#140205]">
        <!-- Fallback/User Image - Set to absolute cover -->
        <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/hand_rakhi_banner.png" alt="Rakhi on Wrist" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-700">
        
        <!-- Gradient Overlay to blend with right side/bottom -->
        <div class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-r from-transparent via-transparent to-[#151215]"></div>
      </div>

      <!-- Right side: Content & Action Button -->
      <div class="w-full md:w-7/12 p-6 md:py-6 md:px-8 flex flex-col justify-center items-center md:items-start text-center md:text-left space-y-3 relative">
        <!-- Floating Sparkles -->
        <span class="absolute top-4 right-10 text-[#eac34a] animate-ping opacity-60">✨</span>
        <span class="absolute bottom-6 right-16 text-[#ffd700] animate-pulse opacity-70">✦</span>

        <!-- 26px (Mobile) and 42px (Desktop) -->
        <h3 class="text-[26px] md:text-[42px] font-extrabold font-serif text-[#eac34a] tracking-tight drop-shadow-sm leading-[1.2]">
          Virtual Rakhi Ceremony
        </h3>
        <p class="text-[14px] md:text-[16px] text-[#d0c3cb]/90 max-w-sm leading-[1.618]">
          Animated golden Rakhi tying ceremony. Complete the sacred rituals of Tilak, Aarti, and Shagun.
        </p>
        
        <!-- Action Button: Opens the Modal -->
        <button type="button" onclick="openCeremonyModal()" class="mt-2 px-8 py-3 bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#cca830] text-[#241a00] font-bold text-sm uppercase tracking-widest rounded-full shadow-[0_10px_30px_rgba(234,195,74,0.4)] hover:shadow-[0_15px_40px_rgba(234,195,74,0.6)] hover:-translate-y-1 active:scale-95 transition-all cursor-pointer inline-flex items-center gap-2.5">
          <span>Tap to Tie Rakhi</span>
          <i data-lucide="arrow-right" class="w-5 h-5"></i>
        </button>
      </div>

    </div>

  </div>
</section>

<!-- RAKHI CEREMONY POPUP MODAL -->
<div id="rakhiCeremonyModal" class="hidden fixed inset-0 z-[100] bg-[#000000f0] backdrop-blur-md overflow-y-auto flex items-start sm:items-center justify-center p-3 sm:p-6 opacity-0 transition-opacity duration-500">
  
  <!-- Close Button -->
  <button type="button" onclick="closeCeremonyModal()" class="fixed top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#3b1e3b]/90 border border-[#eac34a]/60 text-[#eac34a] flex items-center justify-center hover:bg-[#eac34a] hover:text-black transition-all shadow-[0_0_20px_rgba(234,195,74,0.4)] z-[120]">
    <i data-lucide="x" class="w-5 h-5 sm:w-6 sm:h-6"></i>
  </button>

  <div class="relative w-full max-w-2xl sm:max-w-3xl mx-auto my-auto py-6 sm:py-8">
    <div id="royalThaliContainer" class="bg-gradient-to-br from-[#3b0811] via-[#240409] to-[#140205] border-2 border-[#eac34a] backdrop-blur-xl rounded-3xl p-4 sm:p-8 text-center space-y-4 sm:space-y-6 shadow-[0_0_60px_rgba(234,195,74,0.4)] relative overflow-hidden transition-all duration-500 animate-thali-glow">
    
    <!-- Hanging Brass Temple Bell -->
    <div class="flex flex-col items-center justify-center -mt-2">
      <div class="w-0.5 h-6 bg-[#eac34a]/60"></div>
      <div id="hangingBellItem" onclick="ringHangingBell()" title="Tap to Ring Temple Bell (घंटी बजाएं)" class="w-12 h-12 rounded-full bg-gradient-to-tr from-amber-600 via-yellow-400 to-amber-500 border-2 border-[#eac34a] p-1 shadow-[0_0_20px_rgba(234,195,74,0.6)] cursor-pointer hover:scale-110 transition-all flex items-center justify-center group z-30">
        <span id="hangingBellIcon" class="text-2xl">🔔</span>
        <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2.5 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">Ring Bell 🔔</span>
      </div>
    </div>

    <div class="flex items-center justify-center gap-2">
      <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.25em] text-[#eac34a] bg-[#3b1e3b] px-4 py-1.5 rounded-full border border-[#eac34a]/40 shadow-inner flex items-center gap-1.5">
        <span>✨</span> <span>VIRTUAL RAKHI SHRINE &amp; SACRED RITUALS</span> <span>✨</span>
      </span>
    </div>

    <!-- Vedic Raksha Sutra Mantra Audio Banner -->
    <div class="bg-[#1a0307] p-3.5 rounded-2xl border border-[#eac34a]/40 max-w-lg mx-auto shadow-inner space-y-1.5">
      <div class="flex items-center justify-between text-left">
        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#eac34a] flex items-center gap-1">
          <span>🕉️</span> <span>SACRED RAKSHA SUTRA MANTRA</span>
        </span>
        <button type="button" onclick="playVedicChantSound(); playTempleBellSound();" class="text-[10px] font-bold text-[#eac34a] bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all cursor-pointer flex items-center gap-1">
          <span>▶️</span> <span>Play Chants &amp; Bells</span>
        </button>
      </div>
      <p class="font-serif text-xs text-[#f7d070] italic tracking-wide">
        "ॐ येन बद्धो बली राजा दानवेन्द्रो महाबलः। तेन त्वामभि बध्नामि रक्षे मा चल मा चल॥"
      </p>
    </div>

    <div class="space-y-1">
      <h2 class="text-2xl sm:text-3xl font-bold font-serif text-[#e8e0e3]">Virtual Rakhi Ceremony 🧵</h2>
      <p class="text-xs text-[#d0c3cb]/90 max-w-md mx-auto">Perform sacred rituals: Ring Bell, Apply Roli-Chandan Tilak, Light Diya Aarti, Shower Flowers, Offer Sweets, and Tie Royal Rakhi!</p>
    </div>

    <!-- Centerpiece Royal Aarti Thali Graphic Ring with Drag Handles -->
    <div class="relative w-56 h-56 sm:w-64 sm:h-64 mx-auto my-4 flex items-center justify-center">
      <!-- Outer Golden Carved Thali Plate -->
      <div id="thaliOuterRing" class="absolute inset-0 rounded-full bg-gradient-to-tr from-[#cca830] via-[#ffd700] to-[#b8860b] p-[6px] shadow-[0_0_45px_rgba(234,195,74,0.45)] transition-transform duration-1000">
        <div class="w-full h-full bg-gradient-to-br from-[#2a060b] via-[#3b0811] to-[#180306] rounded-full border-4 border-[#eac34a]/60 flex items-center justify-center relative shadow-inner overflow-hidden">
          
          <!-- Decorative Marigold Flower Petals Accents -->
          <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#eac34a_1px,transparent_1px)] [background-size:12px_12px]"></div>

          <!-- Roli-Chawal Bowl (Kumkum) Left -->
          <div id="dragRoliItem" draggable="true" onclick="applyRoyalTilak()" title="Apply Roli-Chandan Tilak (Step 1)" class="absolute top-4 left-5 sm:left-7 w-11 h-11 rounded-full bg-gradient-to-tr from-red-700 via-red-600 to-amber-500 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
            <span class="text-sm font-bold text-white shadow-sm">🔴</span>
            <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">1. Tilak</span>
          </div>

          <!-- Brass Aarti Diya Flame Top/Center -->
          <div id="diyaFlameContainer" draggable="true" onclick="lightRoyalDiya()" title="Light Diya & Aarti (Step 2)" class="absolute top-3 right-5 sm:right-7 w-11 h-11 rounded-full bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-300 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
            <span id="diyaFlameIcon" class="text-xl">🪔</span>
            <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">2. Diya</span>
          </div>

          <!-- Flower Shower Basket Bottom Left -->
          <div onclick="triggerPushpaVarsha()" title="Shower Flowers (पुष्प वर्षा)" class="absolute bottom-4 left-6 sm:left-8 w-10 h-10 rounded-full bg-gradient-to-tr from-pink-600 via-rose-500 to-yellow-400 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
            <span class="text-base">🌸</span>
            <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">Flowers</span>
          </div>

          <!-- Sweet Prasad Offering Bottom Right -->
          <div onclick="offerSweetPrasad()" title="Offer Sweet Prasad (मिठाई)" class="absolute bottom-4 right-6 sm:right-8 w-10 h-10 rounded-full bg-gradient-to-tr from-amber-500 via-orange-400 to-yellow-300 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
            <span class="text-base">🍬</span>
            <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">Sweets</span>
          </div>

          <!-- Royal Silk Rakhi Thread Center Bottom -->
          <div id="dragRakhiItem" draggable="true" onclick="tieRoyalRakhi()" title="Tie Royal Rakhi (Step 3)" class="absolute bottom-2 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#ffe088] to-[#b8860b] border-2 border-white p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
            <span class="text-xl">🧵</span>
            <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">3. Rakhi</span>
          </div>

          <!-- Center Flower Emblem -->
          <div class="w-14 h-14 rounded-full bg-[#1e0407] border-2 border-[#eac34a]/40 flex flex-col items-center justify-center text-center p-1 shadow-inner">
            <span class="text-lg animate-pulse">🌸</span>
            <span class="text-[7px] font-extrabold text-[#eac34a] uppercase tracking-wider">Aarti Thali</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Ritual Action Buttons -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 max-w-2xl mx-auto pt-2">
      <button type="button" onclick="applyRoyalTilak()" id="tilakBtn" class="px-3 py-3 rounded-2xl bg-[#2a060b] hover:bg-[#eac34a] text-[#f7d070] hover:text-[#241a00] border-2 border-[#eac34a]/50 font-bold text-xs flex flex-col items-center gap-1 transition-all cursor-pointer shadow-lg hover:scale-105">
        <span class="text-lg">🔴</span>
        <span class="font-serif text-[11px]">Step 1: Tilak</span>
      </button>

      <button type="button" onclick="lightRoyalDiya()" id="diyaBtn" class="px-3 py-3 rounded-2xl bg-[#2a060b] hover:bg-[#eac34a] text-[#f7d070] hover:text-[#241a00] border-2 border-[#eac34a]/50 font-bold text-xs flex flex-col items-center gap-1 transition-all cursor-pointer shadow-lg hover:scale-105">
        <span class="text-lg" id="diyaIconSpan">🪔</span>
        <span class="font-serif text-[11px]">Step 2: Light Diya</span>
      </button>

      <button type="button" onclick="triggerPushpaVarsha(); offerSweetPrasad();" class="px-3 py-3 rounded-2xl bg-[#2a060b] hover:bg-[#eac34a] text-[#f7d070] hover:text-[#241a00] border-2 border-[#eac34a]/50 font-bold text-xs flex flex-col items-center gap-1 transition-all cursor-pointer shadow-lg hover:scale-105">
        <span class="text-lg">🌸🍬</span>
        <span class="font-serif text-[11px]">Flowers &amp; Sweets</span>
      </button>

      <button type="button" onclick="tieRoyalRakhi()" id="rakhiBtn" class="px-3 py-3 rounded-2xl bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#eac34a] text-[#241a00] font-extrabold text-xs flex flex-col items-center gap-1 shadow-[0_0_25px_rgba(234,195,74,0.4)] hover:scale-105 transition-all cursor-pointer">
        <span class="text-lg">🧵</span>
        <span class="font-serif text-[11px]">Step 3: Tie Rakhi</span>
      </button>
    </div>

    <div id="rakhiRitualStatus" class="text-xs font-bold text-[#eac34a] min-h-[24px] pt-1 tracking-wide">
      Ring Temple Bell 🔔 or tap Step 1 to apply Kumkum-Chandan Tilak! 🔴
    </div>
  </div>
</section>

<!-- SECTION 3: SIBLING FIGHT METER & TV REMOTE RULES -->
<section class="max-w-4xl mx-auto px-4 py-8 relative z-10">
  <div class="bg-[#2a060b]/90 border-2 border-[#eac34a]/40 rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative">
    <div class="text-center space-y-1">
      <span class="text-[10px] font-bold uppercase tracking-widest text-[#eac34a]">SIBLING HUMOR &amp; STATS 📺</span>
      <h3 class="text-xl sm:text-2xl font-bold font-serif text-[#e8e0e3]">Fight Meter &amp; Remote Rules 🤫</h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
      <div class="bg-[#180306] p-4.5 rounded-2xl border border-[#eac34a]/30 space-y-2">
        <div class="flex justify-between text-xs font-bold">
          <span class="text-[#e8e0e3]">TV Remote Sharing Ratio</span>
          <span class="text-[#eac34a]">80% Sister / 20% Brother</span>
        </div>
        <div class="w-full h-3 bg-black/60 rounded-full overflow-hidden p-0.5 border border-[#eac34a]/30">
          <div class="h-full bg-gradient-to-r from-[#eac34a] to-[#ff4d6d] rounded-full w-[80%]"></div>
        </div>
      </div>

      <div class="bg-[#180306] p-4.5 rounded-2xl border border-[#eac34a]/30 space-y-2">
        <div class="flex justify-between text-xs font-bold">
          <span class="text-[#e8e0e3]">Secret Keeper Rating</span>
          <span class="text-[#eac34a]">100% Top Secret 🏆</span>
        </div>
        <div class="w-full h-3 bg-black/60 rounded-full overflow-hidden p-0.5 border border-[#eac34a]/30">
          <div class="h-full bg-gradient-to-r from-emerald-500 to-[#eac34a] rounded-full w-[100%]"></div>
        </div>
      </div>
    </div>
  </div>
    </div>
  </div>
</div>

<!-- SECTION 4: 3D GLASSMORPHISM SIBLING PROMISE CARDS -->
<section id="siblingVowsSection" class="max-w-5xl mx-auto px-4 py-10 relative z-10 space-y-8">
  <div class="text-center space-y-2">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">BROTHER &amp; SISTER VOWS</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]"><?= count($promisesList) ?> Sacred Sibling Vows 🛡️</h2>
    <div class="w-16 h-[2.5px] bg-[#eac34a] mx-auto mt-2 rounded-full shadow-[0_0_10px_#eac34a]"></div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($promisesList as $pIdx => $pText): ?>
      <!-- Premium Glassmorphism Promise Card -->
      <div class="p-6 space-y-5 shadow-2xl relative group overflow-hidden border border-[#eac34a]/30 hover:border-[#eac34a]/80 rounded-2xl transition-all duration-500 bg-gradient-to-br from-[#2a060b]/90 via-[#151215]/95 to-[#3b1e3b]/90 backdrop-blur-xl hover:-translate-y-2 hover:shadow-[0_15px_40px_rgba(234,195,74,0.15)]">
        
        <!-- Subtle Glow Overlay on Hover -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#eac34a]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none z-0"></div>
        
        <!-- Subtle Mandala/Floral Watermark -->
        <div class="absolute -bottom-6 -right-6 text-[#eac34a]/5 text-9xl pointer-events-none z-0 rotate-12 group-hover:rotate-45 transition-transform duration-1000">
          ❁
        </div>

        <div class="relative z-10 flex items-center justify-between">
          <span class="w-10 h-10 rounded-full bg-[#100d10] border border-[#eac34a]/50 text-[#eac34a] font-bold text-xs flex items-center justify-center font-serif shadow-lg group-hover:bg-[#eac34a] group-hover:text-[#100d10] transition-colors duration-300">#<?= $pIdx + 1 ?></span>
          <i data-lucide="shield-check" class="w-6 h-6 text-[#eac34a]/60 group-hover:text-[#eac34a] transition-colors"></i>
        </div>
        <p class="relative z-10 text-base font-serif text-[#e8e0e3] leading-relaxed italic pt-2">
          "<?= htmlspecialchars($pText) ?>"
        </p>
        <div class="relative z-10 flex items-center gap-2 pt-4 border-t border-[#eac34a]/10 mt-auto">
          <span class="w-1.5 h-1.5 rounded-full bg-[#eac34a] animate-pulse"></span>
          <span class="text-[10px] text-[#eac34a] uppercase tracking-widest font-extrabold opacity-80">SACRED VOW TO <?= $partnerName ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SECTION 4.5: SHAHI TAMRAPATRA (OFFICIAL ROYAL SIBLING BOND CERTIFICATE) -->
<!-- 100% Scoped Styles for Shahi Tamrapatra - Zero Global Impact -->
<style>
  .shahi-tamrapatra-wrapper {
    position: relative;
    z-index: 10;
  }
  .shahi-tamrapatra-sheet {
    background: radial-gradient(circle at 50% 50%, #fffef9 0%, #faf3e1 65%, #f1dfb8 100%);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8), inset 0 0 50px rgba(180, 140, 60, 0.22);
    border: 3px solid #d4af37;
    position: relative;
    border-radius: 1.5rem;
    overflow: hidden;
  }
  .tamrapatra-inner-frame {
    border: 2px solid #b89343;
    position: relative;
    border-radius: 1rem;
    background: rgba(255, 253, 245, 0.65);
  }
  .tamrapatra-title-gold {
    font-family: 'Cinzel Decorative', 'Cinzel', 'Playfair Display', Georgia, serif;
    background: linear-gradient(180deg, #8a5a0c 0%, #b8860b 35%, #e6cb7a 65%, #7a4e08 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: drop-shadow(0 2px 4px rgba(184, 134, 11, 0.2));
  }
  .tamrapatra-cursive-sig {
    font-family: 'Playfair Display', 'Brush Script MT', 'Alex Brush', cursive;
    font-style: italic;
  }
  .shahi-red-wax-seal {
    width: 68px;
    height: 68px;
    background: radial-gradient(circle at 35% 35%, #ef3b4c 0%, #a80f21 50%, #59050e 100%);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5), inset 0 2px 4px rgba(255, 255, 255, 0.4), inset 0 -4px 6px rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    border: 2px solid #ff7a85;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  .shahi-red-wax-seal::before {
    content: "";
    position: absolute;
    inset: 4px;
    border: 1.5px dashed rgba(255, 220, 100, 0.6);
    border-radius: 50%;
  }
</style>

<section id="shahiTamrapatraSection" class="shahi-tamrapatra-wrapper max-w-4xl mx-auto px-4 py-10 space-y-6">
  <!-- Section Intro -->
  <div class="text-center space-y-2 mb-6">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">ROYAL DECREE &amp; SEAL</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#fceabb] drop-shadow-md">Shahi Tamrapatra 📜</h2>
    <p class="text-xs text-[#d0c3cb]">The official royal certificate certifying the eternal bond between brother and sister.</p>
  </div>

  <!-- Certificate Canvas Document (Rendered for 4K Export) -->
  <div id="shahiTamrapatraDocument" class="shahi-tamrapatra-sheet p-4 sm:p-8 md:p-10 relative">
    
    <!-- Ornate Vector Gold Filigree Corners Matching Mockup -->
    <!-- Top Left Corner -->
    <div class="absolute top-2 left-2 w-16 h-16 sm:w-24 sm:h-24 pointer-events-none opacity-85 z-10">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5 5 L5 45 C5 30 15 20 30 15 C 45 10 30 5 5 5 Z" fill="#b89343" opacity="0.9"/>
        <path d="M5 5 C 20 20 25 40 40 40 C 35 25 25 15 5 5 Z" fill="#d4af37"/>
        <path d="M15 15 C 30 30 45 35 60 20 C 40 15 25 25 15 15 Z" fill="#8a5a0c" opacity="0.6"/>
        <circle cx="20" cy="20" r="4" fill="#ffd700"/>
      </svg>
    </div>
    <!-- Top Right Corner -->
    <div class="absolute top-2 right-2 w-16 h-16 sm:w-24 sm:h-24 pointer-events-none opacity-85 z-10 scale-x-[-1]">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5 5 L5 45 C5 30 15 20 30 15 C 45 10 30 5 5 5 Z" fill="#b89343" opacity="0.9"/>
        <path d="M5 5 C 20 20 25 40 40 40 C 35 25 25 15 5 5 Z" fill="#d4af37"/>
        <path d="M15 15 C 30 30 45 35 60 20 C 40 15 25 25 15 15 Z" fill="#8a5a0c" opacity="0.6"/>
        <circle cx="20" cy="20" r="4" fill="#ffd700"/>
      </svg>
    </div>
    <!-- Bottom Left Corner -->
    <div class="absolute bottom-2 left-2 w-16 h-16 sm:w-24 sm:h-24 pointer-events-none opacity-85 z-10 scale-y-[-1]">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5 5 L5 45 C5 30 15 20 30 15 C 45 10 30 5 5 5 Z" fill="#b89343" opacity="0.9"/>
        <path d="M5 5 C 20 20 25 40 40 40 C 35 25 25 15 5 5 Z" fill="#d4af37"/>
        <path d="M15 15 C 30 30 45 35 60 20 C 40 15 25 25 15 15 Z" fill="#8a5a0c" opacity="0.6"/>
        <circle cx="20" cy="20" r="4" fill="#ffd700"/>
      </svg>
    </div>
    <!-- Bottom Right Corner -->
    <div class="absolute bottom-2 right-2 w-16 h-16 sm:w-24 sm:h-24 pointer-events-none opacity-85 z-10 scale-[-1]">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5 5 L5 45 C5 30 15 20 30 15 C 45 10 30 5 5 5 Z" fill="#b89343" opacity="0.9"/>
        <path d="M5 5 C 20 20 25 40 40 40 C 35 25 25 15 5 5 Z" fill="#d4af37"/>
        <path d="M15 15 C 30 30 45 35 60 20 C 40 15 25 25 15 15 Z" fill="#8a5a0c" opacity="0.6"/>
        <circle cx="20" cy="20" r="4" fill="#ffd700"/>
      </svg>
    </div>

    <!-- Inner Certificate Border Frame -->
    <div class="tamrapatra-inner-frame p-6 sm:p-10 text-center space-y-6 relative z-10">
      
      <!-- Royal Crown & Heraldic Emblem -->
      <div class="flex flex-col items-center justify-center space-y-1">
        <div class="w-14 h-10 text-[#b89343] flex items-center justify-center">
          <svg viewBox="0 0 100 60" fill="#b89343" class="w-full h-full drop-shadow-sm">
            <path d="M50 5 L62 25 L85 10 L75 45 L25 45 L15 10 L38 25 Z M25 48 L75 48 L72 55 L28 55 Z"/>
            <circle cx="50" cy="5" r="3" fill="#ffd700"/>
            <circle cx="15" cy="10" r="2.5" fill="#ffd700"/>
            <circle cx="85" cy="10" r="2.5" fill="#ffd700"/>
          </svg>
        </div>
        <h3 class="tamrapatra-title-gold text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-wider uppercase">
          SHAHI TAMRAPATRA
        </h3>
        <span class="text-[10px] sm:text-xs font-serif uppercase tracking-[0.25em] text-[#6b4d1b] font-bold block pt-0.5">
          CERTIFICATE OF ETERNAL SIBLING BOND
        </span>
      </div>

      <!-- Preamble text -->
      <p class="font-serif italic text-xs sm:text-sm text-[#4a3410] max-w-xl mx-auto leading-relaxed">
        This Royal decree certifies the sacred and eternal bond of love, protection, and cherished companionship between:
      </p>

      <!-- Parties Names Block (Left: Brother, Right: Sister) -->
      <div class="flex flex-row items-center justify-around gap-4 max-w-lg mx-auto py-2 border-y border-[#d4af37]/40 bg-[#faf4e3]/60 rounded-xl px-4">
        <div class="text-center space-y-0.5 flex-1">
          <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-[#7a5310] block">RESPONSIBLE BROTHER</span>
          <strong class="font-serif text-base sm:text-xl text-[#2a1705] font-extrabold block truncate"><?= $buyerName ?></strong>
        </div>
        <div class="w-8 h-8 rounded-full bg-[#ebd7b3] border border-[#b89343] flex items-center justify-center font-serif font-bold text-xs text-[#6b4d1b] shrink-0 shadow-inner">
          &amp;
        </div>
        <div class="text-center space-y-0.5 flex-1">
          <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-[#7a5310] block">BELOVED SISTER</span>
          <strong class="font-serif text-base sm:text-xl text-[#2a1705] font-extrabold block truncate"><?= $partnerName ?></strong>
        </div>
      </div>

      <!-- Formal Treaty / Vow Decree -->
      <p class="font-serif text-xs sm:text-[13.5px] text-[#3d2b0c] leading-relaxed max-w-2xl mx-auto px-2">
        We solemnly acknowledge the vow of lifelong protection (Raksha) and unconditional love (Bandhan). In testimony of this unbreakable kinship, both parties exchange eternal affection, joy, and shared memories. May this sacred knot bestow boundless prosperity, happiness, and divinity upon their lives.
      </p>

      <div class="text-[11px] sm:text-xs font-serif font-bold text-[#6b4d1b] italic">
        Dated: <?= date('jS F Y') ?>
      </div>

      <!-- Signatures & Royal Red Wax Seal Centerpiece -->
      <div class="pt-2 flex items-center justify-between gap-2 max-w-xl mx-auto border-t border-[#d4af37]/30">
        
        <!-- Left: Brother Signature -->
        <div class="text-center flex-1">
          <div class="tamrapatra-cursive-sig text-base sm:text-2xl text-[#6b4d1b] font-bold pb-1 border-b border-[#a88232]/50">
            <?= $buyerName ?>
          </div>
          <span class="text-[8.5px] sm:text-[10px] font-serif uppercase tracking-widest text-[#7a5310] block pt-1 font-semibold">
            [<?= $buyerName ?>'s Signature]
          </span>
        </div>

        <!-- Center: 3D Crimson Red Wax Seal -->
        <div class="px-2 shrink-0 flex flex-col items-center">
          <div class="shahi-red-wax-seal">
            <span class="text-xs text-[#ffe088]">👑</span>
            <span class="text-[8px] font-black uppercase text-[#fff2c4] tracking-widest leading-none mt-0.5">SHAHI</span>
          </div>
        </div>

        <!-- Right: Sister Signature -->
        <div class="text-center flex-1">
          <div class="tamrapatra-cursive-sig text-base sm:text-2xl text-[#6b4d1b] font-bold pb-1 border-b border-[#a88232]/50">
            <?= $partnerName ?>
          </div>
          <span class="text-[8.5px] sm:text-[10px] font-serif uppercase tracking-widest text-[#7a5310] block pt-1 font-semibold">
            [<?= $partnerName ?>'s Signature]
          </span>
        </div>

      </div>

      <!-- Scribe footnote -->
      <div class="text-[9px] sm:text-[10px] font-serif text-[#8a6526] italic pt-1">
        Signed and sealed under the Royal Scribe Authority of SoulScript &bull; Lifetime Sacred Bond
      </div>

    </div>
  </div>

  <!-- Download & Share Action Buttons (Full-width touch-friendly on Mobile) -->
  <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
    <button type="button" id="downloadCertBtn" onclick="downloadShahiTamrapatra()" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-[#d4af37] via-[#f7e6a6] to-[#b89343] text-[#241a00] font-extrabold text-xs sm:text-sm uppercase tracking-wider rounded-full shadow-[0_8px_25px_rgba(212,175,55,0.45)] hover:shadow-[0_12px_35px_rgba(212,175,55,0.65)] hover:-translate-y-0.5 active:scale-95 transition-all cursor-pointer inline-flex items-center justify-center gap-2">
      <i data-lucide="download" class="w-4 h-4 text-[#241a00]"></i>
      <span>Download Official Certificate (4K HD)</span>
    </button>

    <button type="button" onclick="shareShahiTamrapatraWhatsApp()" class="w-full sm:w-auto px-6 py-3.5 bg-[#1f4e27] hover:bg-[#276332] text-[#98ecaa] border border-[#52b76b]/50 font-bold text-xs sm:text-sm rounded-full shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all cursor-pointer inline-flex items-center justify-center gap-2">
      <i data-lucide="share-2" class="w-4 h-4 text-[#98ecaa]"></i>
      <span>Share Certificate on WhatsApp 📲</span>
    </button>
  </div>
</section>

<script>
  // 4K Ultra-HD Client-Side Download Function
  function downloadShahiTamrapatra() {
    const btn = document.getElementById('downloadCertBtn');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Generating 4K Certificate...';
    btn.disabled = true;

    const loadHtml2Canvas = () => {
      return new Promise((resolve, reject) => {
        if (window.html2canvas) return resolve();
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
      });
    };

    loadHtml2Canvas().then(() => {
      const element = document.getElementById('shahiTamrapatraDocument');
      html2canvas(element, {
        scale: 3, // 3x Ultra-HD 300 DPI supersampling!
        useCORS: true,
        backgroundColor: '#faf3e1',
        logging: false
      }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'Shahi_Tamrapatra_Certificate_<?= preg_replace('/[^a-zA-Z0-9_-]/', '', $buyerName) ?>_<?= preg_replace('/[^a-zA-Z0-9_-]/', '', $partnerName) ?>.png';
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
        btn.innerHTML = originalContent;
        btn.disabled = false;
        if (window.lucide) lucide.createIcons();
        if (window.confetti) {
          confetti({ particleCount: 80, spread: 60, origin: { y: 0.7 } });
        }
      }).catch(err => {
        console.error(err);
        btn.innerHTML = originalContent;
        btn.disabled = false;
        if (window.lucide) lucide.createIcons();
      });
    }).catch(err => {
      console.error(err);
      btn.innerHTML = originalContent;
      btn.disabled = false;
      if (window.lucide) lucide.createIcons();
    });
  }

  function shareShahiTamrapatraWhatsApp() {
    const text = `📜 *Official Shahi Tamrapatra — Sibling Bond Certificate* 👑\n\nThis Royal decree certifies the eternal bond of love and protection between *<?= $buyerName ?>* and *<?= $partnerName ?>*!\n\nView our official certificate on SoulScript: ${window.location.href}`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
  }
</script>

<!-- SECTION 5: WAX-SEALED ROYAL SHAGUN LIFAFA -->
<section class="max-w-3xl mx-auto px-4 py-10 relative z-10">
  <div class="text-center space-y-2 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">SPECIAL GIFT &amp; BLESSINGS</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Digital Shagun Envelope 🧧</h2>
    <p class="text-xs text-[#d0c3cb]">Tap the wax-sealed lifafa to reveal your gift voucher and personal letter!</p>
  </div>

  <!-- Closed Envelope UI -->
  <div id="shagunEnvelopeContainer" onclick="toggleShagunLifafa()" class="bg-gradient-to-br from-[#800f1c] via-[#590a13] to-[#2b0509] border-2 border-[#eac34a] rounded-3xl p-8 text-center cursor-pointer shadow-[0_0_40px_rgba(234,195,74,0.3)] hover:scale-[1.02] transition-all relative overflow-hidden group">
    <div class="wax-seal-badge w-14 h-14 rounded-full text-[#eac34a] flex items-center justify-center mx-auto mb-3 text-xl font-serif font-bold">
      ॐ
    </div>
    <div class="space-y-2">
      <span class="text-xs font-extrabold uppercase tracking-widest text-[#eac34a] bg-[#151215]/80 px-4 py-1 rounded-full border border-[#eac34a]/40 inline-block">ROYAL SHAGUN LIFAFA</span>
      <h3 class="text-2xl font-bold font-serif text-white">Tap to Open Envelope 🧧</h3>
      <p class="text-xs text-[#f5d77f]">Contains personal note &amp; gift voucher code</p>
    </div>
  </div>

  <!-- Opened Envelope Content -->
  <div id="shagunLetterContent" class="hidden bg-[#221f21] border-2 border-[#eac34a] rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative">
    <div class="flex items-center justify-between border-b border-[#4d444b]/60 pb-4">
      <span class="text-xs font-bold text-[#eac34a] uppercase tracking-wider flex items-center gap-1.5">
        <i data-lucide="heart" class="w-4 h-4 text-[#eac34a]"></i>
        <span>Shagun Letter from <?= $buyerName ?></span>
      </span>
      <button type="button" onclick="toggleShagunLifafa()" class="text-xs text-[#d0c3cb] hover:text-[#eac34a] font-bold">Close ✕</button>
    </div>

    <div class="space-y-4">
      <p class="font-serif text-base sm:text-lg text-[#e8e0e3] leading-relaxed italic bg-[#151215] p-5 rounded-2xl border border-[#4d444b]">
        "<?= $loveNoteText ?>"
      </p>

      <!-- Dynamic Rakhi Voucher Unlock Box -->
      <?php if (!empty($rakhiVoucherStatus)): ?>
        <?php if (empty($rakhiVoucherStatus['unlocked'])): ?>
          <div class="p-5 bg-gradient-to-br from-[#3b2a1a] via-[#281d12] to-[#1a140d] border-2 border-[#eac34a] rounded-2xl text-center space-y-3 shadow-xl relative overflow-hidden">
            <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#eac34a] bg-[#100d10] px-3 py-1 rounded-full border border-[#eac34a]/30 inline-block">
              🎁 Surprise Amazon Cash Voucher
            </span>
            <h4 class="text-lg font-bold font-serif text-white">Your Secret Rakhi Cash Voucher Unlocks Soon! ⏳</h4>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              Your Brother/Sister has hidden a surprise Amazon Gift Voucher inside this card! It unlocks automatically on <strong><?= htmlspecialchars($rakhiVoucherStatus['unlock_date_formatted'] ?? '28 August 2026, 12:00 PM IST') ?></strong>.
            </p>
            <div class="inline-flex items-center gap-2 text-xs font-mono text-[#eac34a] bg-[#100d10] py-2 px-4 rounded-xl border border-[#eac34a]/30 font-extrabold shadow-inner">
              <i data-lucide="clock" class="w-4 h-4 text-[#eac34a] animate-pulse"></i>
              <span>Unlocks on <?= htmlspecialchars($rakhiVoucherStatus['unlock_date_formatted'] ?? '28 Aug 2026, 12:00 PM IST') ?></span>
            </div>
          </div>
        <?php else: 
          $vAmt = htmlspecialchars($rakhiVoucherStatus['allocated_amount'] ?? 100);
          $vCode = !empty($rakhiVoucherStatus['voucher_code']) ? htmlspecialchars($rakhiVoucherStatus['voucher_code']) : ($voucherCode && $voucherCode !== 'AMZ-RAKHI-9876' ? htmlspecialchars($voucherCode) : 'AMZ-RAKHI-2026-CLAIM');
        ?>
          <div class="p-5 bg-gradient-to-br from-[#1e3b20] via-[#152821] to-[#101b17] border-2 border-[#a4e4b9] rounded-2xl text-center space-y-3.5 shadow-2xl relative overflow-hidden">
            <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#a4e4b9] bg-[#100d10] px-3 py-1 rounded-full border border-[#a4e4b9]/30 inline-block">
              🎉 Amazon Cash Voucher Unlocked!
            </span>
            <h4 class="text-2xl font-black font-serif text-[#a4e4b9]">₹<?= $vAmt ?> Amazon Gift Voucher</h4>
            <p class="text-xs text-[#d0c3cb]">Copy your gift code below and redeem directly on Amazon India!</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-1">
              <strong class="text-base font-mono text-white bg-[#100d10] px-5 py-2.5 rounded-xl border border-[#a4e4b9]/40 tracking-widest shadow-inner"><?= $vCode ?></strong>
              <button type="button" onclick="navigator.clipboard.writeText('<?= $vCode ?>'); alert('Voucher code copied to clipboard!'); confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-[#a4e4b9] to-[#6ee7b7] text-[#100d10] font-extrabold text-xs uppercase tracking-wider rounded-xl hover:brightness-110 transition-all cursor-pointer shadow-lg">
                Copy Code &amp; Redeem 📋
              </button>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <!-- Amazon Affiliate Store Recommendations -->
      <?php if (!empty($rakhiAffiliateProducts)): ?>
        <div class="pt-6 border-t border-[#4d444b]/40 space-y-3">
          <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#eac34a] block text-center">
            🛍️ Recommended Rakhi Gifts on Amazon (Redeem Voucher Directly Below)
          </span>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php foreach ($rakhiAffiliateProducts as $prod): ?>
              <a href="<?= htmlspecialchars($prod['affiliate_url'] ?? '#') ?>" target="_blank" class="bg-[#100d10] p-3 rounded-2xl border border-[#4d444b]/40 hover:border-[#eac34a] transition-all group flex flex-col justify-between space-y-2 text-left">
                <div class="w-full aspect-square rounded-xl overflow-hidden bg-black/40">
                  <img src="<?= htmlspecialchars($prod['image_url'] ?? '') ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="space-y-1">
                  <span class="text-[9px] font-bold text-[#eac34a] block truncate"><?= htmlspecialchars($prod['category'] ?? 'Amazon Gift') ?></span>
                  <h5 class="text-xs font-bold text-[#e8e0e3] line-clamp-2 leading-snug"><?= htmlspecialchars($prod['title'] ?? '') ?></h5>
                  <span class="text-xs font-extrabold text-[#a4e4b9] block"><?= htmlspecialchars($prod['price_text'] ?? '') ?></span>
                </div>
                <div class="w-full py-1.5 bg-[#eac34a] text-[#241a00] font-extrabold text-[10px] uppercase tracking-wider rounded-lg text-center group-hover:bg-[#ffe088] transition-colors">
                  Buy on Amazon ↗
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="text-right pt-2">
      <span class="text-xs text-[#d0c3cb]">With lots of love,</span>
      <span class="block text-lg font-bold font-serif text-[#eac34a]"><?= $buyerName ?></span>
    </div>
  </div>
</section>

<!-- SECTION 6: SHAHI FARMAN UNROLLING SCROLL GALLERY (ALL PHOTOS) -->
<!-- SCOPED CSS FOR RAKHI PARCHMENT SCROLL GALLERY (100% Isolated - Zero Global CSS Leak) -->
<style>
  .rb-farman-section {
    position: relative;
    z-index: 10;
  }
  .rb-farman-parchment {
    background: linear-gradient(to right, #cfb580 0%, #ebd7b3 2.5%, #fdfbf5 8%, #fffdf8 50%, #fdfbf5 92%, #ebd7b3 97.5%, #cfb580 100%);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.75), inset 0 0 35px rgba(160, 120, 50, 0.2);
    border-top: 4px solid #c9a658;
    border-bottom: 4px solid #c9a658;
    position: relative;
    border-radius: 1.5rem;
  }
  /* Golden Roller Rods Left & Right */
  .rb-farman-rod-left, .rb-farman-rod-right {
    position: absolute;
    top: -24px;
    bottom: -24px;
    width: 22px;
    background: linear-gradient(to right, #523b13 0%, #b89343 35%, #f7e6a6 50%, #b89343 75%, #3d2b0c 100%);
    border-radius: 11px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.8), inset 0 2px 4px rgba(255,255,255,0.5);
    z-index: 25;
  }
  .rb-farman-rod-left { left: -12px; }
  .rb-farman-rod-right { right: -12px; }
  .rb-farman-rod-left::before, .rb-farman-rod-left::after,
  .rb-farman-rod-right::before, .rb-farman-rod-right::after {
    content: "";
    position: absolute;
    width: 32px;
    height: 18px;
    left: -5px;
    background: radial-gradient(ellipse at center, #fff2c4 0%, #d4af37 50%, #523b13 100%);
    border-radius: 9px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.6);
    border: 1px solid rgba(255,255,255,0.3);
  }
  .rb-farman-rod-left::before, .rb-farman-rod-right::before { top: -12px; }
  .rb-farman-rod-left::after, .rb-farman-rod-right::after { bottom: -12px; }

  /* Photo Cards Matching User Mockup Colors */
  .rb-farman-card {
    background: #fcfbfa;
    border: 2px solid #dfc690;
    box-shadow: 0 4px 18px rgba(110, 85, 45, 0.14), inset 0 0 0 1px rgba(255, 255, 255, 0.9);
    border-radius: 1.25rem;
    padding: 0.65rem;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .rb-farman-card:hover {
    transform: translateY(-5px);
    border-color: #b89343;
    box-shadow: 0 12px 28px rgba(110, 85, 45, 0.25);
  }
  .rb-farman-img-box {
    border-radius: 0.85rem;
    overflow: hidden;
    border: 1px solid #ebd9b5;
    background-color: #f7f3eb;
    position: relative;
  }
  .rb-farman-caption {
    color: #3b2b1b;
    font-family: 'Cinzel', 'Playfair Display', Georgia, serif;
    font-size: 0.78rem;
    font-weight: 700;
    text-align: center;
    margin-top: 0.55rem;
    line-height: 1.3;
    letter-spacing: 0.01em;
  }
</style>

<section class="rb-farman-section max-w-5xl mx-auto px-4 py-12 space-y-8">
  <!-- Section Title -->
  <div class="text-center space-y-2 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">ROYAL MEMORY SCROLL</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#fceabb] drop-shadow-md">Our Cherished Moments ✨</h2>
    <p class="text-xs text-[#d0c3cb]">Every memory photo uploaded by <?= $buyerName ?> framed inside the unrolling antique parchment scroll.</p>
  </div>

  <!-- Antique Parchment Scroll Container -->
  <div class="rb-farman-parchment p-5 sm:p-10 relative">
    <!-- Decorative Wooden Scroll Handles -->
    <div class="rb-farman-rod-left"></div>
    <div class="rb-farman-rod-right"></div>

    <!-- Corner Watercolor Floral Ornaments -->
    <!-- Top-Left Floral -->
    <div class="absolute top-2 left-3 w-10 h-10 sm:w-14 sm:h-14 pointer-events-none opacity-80 z-10">
      <svg viewBox="0 0 100 100" fill="none">
        <path d="M15 45 C 25 25, 45 15, 65 15 C 45 30, 30 45, 15 45 Z" fill="#88a86e" opacity="0.85"/>
        <path d="M30 60 C 25 40, 40 25, 60 30 C 45 40, 40 55, 30 60 Z" fill="#e88495"/>
        <circle cx="40" cy="40" r="11" fill="#ec7085"/>
        <circle cx="40" cy="40" r="4.5" fill="#fde68a"/>
      </svg>
    </div>
    <!-- Top-Right Floral -->
    <div class="absolute top-2 right-3 w-10 h-10 sm:w-14 sm:h-14 pointer-events-none opacity-80 z-10 scale-x-[-1]">
      <svg viewBox="0 0 100 100" fill="none">
        <path d="M15 45 C 25 25, 45 15, 65 15 C 45 30, 30 45, 15 45 Z" fill="#88a86e" opacity="0.85"/>
        <path d="M30 60 C 25 40, 40 25, 60 30 C 45 40, 40 55, 30 60 Z" fill="#e88495"/>
        <circle cx="40" cy="40" r="11" fill="#ec7085"/>
        <circle cx="40" cy="40" r="4.5" fill="#fde68a"/>
      </svg>
    </div>
    <!-- Bottom-Left Floral -->
    <div class="absolute bottom-2 left-3 w-10 h-10 sm:w-14 sm:h-14 pointer-events-none opacity-80 z-10 scale-y-[-1]">
      <svg viewBox="0 0 100 100" fill="none">
        <path d="M15 45 C 25 25, 45 15, 65 15 C 45 30, 30 45, 15 45 Z" fill="#88a86e" opacity="0.85"/>
        <path d="M30 60 C 25 40, 40 25, 60 30 C 45 40, 40 55, 30 60 Z" fill="#e88495"/>
        <circle cx="40" cy="40" r="11" fill="#ec7085"/>
        <circle cx="40" cy="40" r="4.5" fill="#fde68a"/>
      </svg>
    </div>
    <!-- Bottom-Right Floral -->
    <div class="absolute bottom-2 right-3 w-10 h-10 sm:w-14 sm:h-14 pointer-events-none opacity-80 z-10 scale-[-1]">
      <svg viewBox="0 0 100 100" fill="none">
        <path d="M15 45 C 25 25, 45 15, 65 15 C 45 30, 30 45, 15 45 Z" fill="#88a86e" opacity="0.85"/>
        <path d="M30 60 C 25 40, 40 25, 60 30 C 45 40, 40 55, 30 60 Z" fill="#e88495"/>
        <circle cx="40" cy="40" r="11" fill="#ec7085"/>
        <circle cx="40" cy="40" r="4.5" fill="#fde68a"/>
      </svg>
    </div>

    <!-- Photo Cards Masonry Grid (Pinterest Style with Mockup Colors) -->
    <div class="columns-2 sm:columns-3 gap-4 sm:gap-6 space-y-4 sm:space-y-6 relative z-20">
      <?php foreach ($media as $m): 
        $imgUrl = htmlspecialchars(resolveMediaUrl($m['file_path'] ?? ''));
        $capText = htmlspecialchars($m['caption'] ?? 'Cherished Memory');
      ?>
        <div onclick="openLightbox('<?= $imgUrl ?>')" class="break-inside-avoid rb-farman-card group cursor-pointer flex flex-col">
          <div class="rb-farman-img-box">
            <img loading="lazy" src="<?= $imgUrl ?>" onerror="this.onerror=null; this.src='<?= htmlspecialchars(APP_URL) ?>/assets/default_gallery/sample_fa6955df.webp';" class="w-full h-auto object-cover group-hover:scale-[1.04] transition-transform duration-500">
          </div>
          <div class="px-1">
            <p class="rb-farman-caption truncate" title="<?= $capText ?>"><?= $capText ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Footer Bar -->
<footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
  <p class="text-xs text-[#d0c3cb]">Made with endless love by <strong class="text-[#eac34a]"><?= $buyerName ?></strong> for <strong class="text-[#eac34a]"><?= $partnerName ?></strong></p>
  <div class="flex items-center justify-center gap-3">
    <button onclick="relockGiftSession()" type="button" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer">
      <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
      <span>Lock Gift Page 🔒</span>
    </button>
  </div>
</footer>
