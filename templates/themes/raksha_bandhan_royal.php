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
<div id="rakhiCeremonyModal" class="hidden fixed inset-0 z-[100] bg-[#000000e6] backdrop-blur-md overflow-y-auto flex items-center justify-center p-4 sm:p-6 opacity-0 transition-opacity duration-500">
  
  <!-- Close Button -->
  <button type="button" onclick="closeCeremonyModal()" class="absolute top-6 right-6 sm:top-8 sm:right-8 w-12 h-12 rounded-full bg-[#3b1e3b]/80 border border-[#eac34a]/40 text-[#eac34a] flex items-center justify-center hover:bg-[#eac34a] hover:text-black transition-all shadow-[0_0_20px_rgba(234,195,74,0.3)] z-[110]">
    <i data-lucide="x" class="w-6 h-6"></i>
  </button>

  <div class="relative w-full max-w-3xl mx-auto my-12">
    <div id="royalThaliContainer" class="bg-gradient-to-br from-[#3b0811] via-[#240409] to-[#140205] border-2 border-[#eac34a] backdrop-blur-xl rounded-3xl p-6 sm:p-8 text-center space-y-6 shadow-[0_0_60px_rgba(234,195,74,0.4)] relative overflow-hidden transition-all duration-500 animate-thali-glow">
    
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
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">5 Sibling Promises 🛡️</h2>
    <div class="w-16 h-[2.5px] bg-[#eac34a] mx-auto mt-2 rounded-full shadow-[0_0_10px_#eac34a]"></div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($promisesList as $pIdx => $pText): ?>
      <div class="glass-vow-card p-6 space-y-4 shadow-2xl relative group overflow-hidden border-2 border-[#eac34a]/40 hover:border-[#eac34a] transition-all">
        <div class="flex items-center justify-between">
          <span class="wax-seal-badge w-10 h-10 rounded-full text-[#eac34a] font-bold text-xs flex items-center justify-center font-serif shadow-lg">#<?= $pIdx + 1 ?></span>
          <i data-lucide="shield-check" class="w-6 h-6 text-[#eac34a]"></i>
        </div>
        <p class="text-sm font-serif text-[#e8e0e3] leading-relaxed italic pt-1">
          "<?= htmlspecialchars($pText) ?>"
        </p>
        <div class="flex items-center gap-1.5 pt-2 border-t border-[#eac34a]/20">
          <span class="w-2 h-2 rounded-full bg-[#eac34a]"></span>
          <span class="text-[10px] text-[#eac34a] uppercase tracking-wider font-bold">SACRED VOW TO <?= $partnerName ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

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
<section class="max-w-5xl mx-auto px-4 py-12 relative z-10 space-y-8">
  <div class="text-center space-y-2 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">ROYAL MEMORY SCROLL</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Shahi Farman Scrapbook 📜</h2>
    <p class="text-xs text-[#d0c3cb]">Every memory photo uploaded by <?= $buyerName ?> framed inside the unrolling antique parchment scroll.</p>
  </div>

  <!-- Antique Parchment Scroll Container -->
  <div class="shahi-farman-scroll rounded-3xl p-6 sm:p-10 relative">
    <div class="shahi-farman-handle-left"></div>
    <div class="shahi-farman-handle-right"></div>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
      <?php foreach ($media as $m): 
        $imgUrl = htmlspecialchars(resolveMediaUrl($m['file_path'] ?? ''));
        $capText = htmlspecialchars($m['caption'] ?? 'Cherished Memory');
      ?>
        <div onclick="openLightbox('<?= $imgUrl ?>')" class="bg-[#1c1715] p-2 sm:p-3 rounded-2xl border border-[#eac34a]/30 group cursor-pointer hover:border-[#eac34a] transition-all shadow-xl flex flex-col justify-between">
          <div class="w-full aspect-square rounded-xl overflow-hidden bg-black/50 relative">
            <img src="<?= $imgUrl ?>" onerror="this.onerror=null; this.src='<?= htmlspecialchars(APP_URL) ?>/assets/default_gallery/sample_fa6955df.webp';" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          </div>
          <div class="pt-2 text-center">
            <span class="text-[10px] sm:text-xs font-serif text-[#eac34a] block truncate font-semibold"><?= $capText ?></span>
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
