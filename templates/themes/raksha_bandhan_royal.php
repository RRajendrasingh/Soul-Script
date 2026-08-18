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

<!-- FLOATING MARIGOLD PETALS & STARDUST CANVAS (60 FPS LUXURY AMBIENCE) -->
<canvas id="floatingMarigoldCanvas" class="fixed inset-0 pointer-events-none z-[1] w-full h-full"></canvas>

<!-- RICH ROYAL MANDALA BACKGROUND ARTWORK LAYER -->
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

<!-- ======================================================= -->
<!-- ACT 1: AUSPICIOUS START (HERO BANNER & AARTI THALI) -->
<!-- ======================================================= -->
<section class="relative pt-24 pb-12 px-4 text-center sm:text-left z-10 w-full">
  <div class="max-w-5xl mx-auto space-y-12">
    
    <!-- Top Row: Profile & Titles -->
    <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-8 md:gap-16">
      
      <!-- Left: Double Golden Ring Photo Avatar Frame with Dynamic Rakhi & Tilak -->
      <div id="doubleGoldenRingAvatar" class="relative w-48 h-48 md:w-56 md:h-56 rounded-full bg-gradient-to-tr from-[#cca830] via-[#ffd700] to-[#b8860b] p-1.5 shadow-[0_0_60px_rgba(234,195,74,0.45)] shrink-0 group hover:scale-105 transition-transform duration-500">
        <div class="w-full h-full rounded-full border-[3px] border-[#100d10] p-1 bg-gradient-to-br from-[#2a060b] to-[#140205]">
          <div class="w-full h-full bg-[#151215] rounded-full overflow-hidden flex items-center justify-center relative shadow-inner">
            <?= $photoAvatarHtml ?>
            
            <!-- Live Tilak Mark -->
            <div id="tilakMarkOnAvatar" class="hidden absolute top-10 md:top-12 left-1/2 -translate-x-1/2 w-5 h-5 rounded-full bg-gradient-to-tr from-red-600 via-red-500 to-amber-400 shadow-[0_0_20px_#ef4444] z-30 flex items-center justify-center">
              <span class="w-2 h-2 rounded-full bg-white/90"></span>
            </div>

            <!-- Live Tied Rakhi Badge Overlay -->
            <div id="rakhiTiedOnAvatar" class="hidden absolute bottom-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-gradient-to-r from-[#2a060b] via-[#3b0811] to-[#2a060b] border border-[#eac34a] rounded-full shadow-[0_0_20px_rgba(234,195,74,0.7)] z-30 flex items-center gap-1.5">
              <span id="tiedRakhiIcon" class="text-xs">👑</span>
              <span class="text-[9px] font-extrabold text-[#ffd700] uppercase tracking-wider whitespace-nowrap">Rakhi Tied 🧵</span>
            </div>
          </div>
        </div>
        <div class="absolute -inset-4 bg-[#eac34a]/10 blur-xl rounded-full z-[-1] animate-pulse-slow"></div>
      </div>

      <!-- Right: Greetings & Titles -->
      <div class="space-y-4 w-full md:max-w-3xl text-center md:text-left">
        <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#3b1e3b]/80 border border-[#eac34a]/40 backdrop-blur-md text-[#eac34a] text-[11px] md:text-[15px] font-bold shadow-lg">
          <i data-lucide="crown" class="w-4 h-4 text-[#eac34a]"></i>
          <span><?= $taglineQuote ?></span>
        </div>

        <h1 class="text-[40px] md:text-[64px] font-bold font-handwriting text-[#e8e0e3] tracking-wide leading-[1.2] drop-shadow-md py-2">
          Happy Raksha Bandhan, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#cca830] pl-1 pr-2"><?= $partnerName ?></span><span class="ml-1">!</span> <span class="text-[26px] md:text-[40px] inline-block -translate-y-1 animate-flame">🪔</span>
        </h1>
        <p class="text-[14px] md:text-[16px] text-[#d0c3cb] max-w-lg mx-auto md:mx-0 leading-[1.618]">
          A sacred royal celebration of our eternal sibling bond, childhood mischiefs, and unbreakable promises.
        </p>
      </div>

    </div>

    <!-- WRIST RAKHI HERO CEREMONY BANNER -->
    <div id="heroWristBannerContainer" class="w-full bg-[#151215] border border-[#eac34a]/30 rounded-[2.5rem] shadow-[0_0_50px_rgba(234,195,74,0.15)] relative overflow-hidden flex flex-col md:flex-row group transition-all duration-500 hover:shadow-[0_0_60px_rgba(234,195,74,0.3)] hover:border-[#eac34a]/60">
      <div class="w-full md:w-5/12 h-56 md:h-auto relative overflow-hidden bg-gradient-to-br from-[#2a060b] to-[#140205]">
        <img src="<?= htmlspecialchars(APP_URL) ?>/assets/images/hand_rakhi_banner.png" alt="Rakhi on Wrist" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-700">
        <div class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-r from-transparent via-transparent to-[#151215]"></div>
      </div>

      <div class="w-full md:w-7/12 p-6 md:py-8 md:px-10 flex flex-col justify-center items-center md:items-start text-center md:text-left space-y-3.5 relative">
        <span class="absolute top-4 right-10 text-[#eac34a] animate-ping opacity-60">✨</span>
        <span class="absolute bottom-6 right-16 text-[#ffd700] animate-pulse opacity-70">✦</span>

        <span class="text-[10px] font-extrabold uppercase tracking-[0.25em] text-[#eac34a] bg-[#3b1e3b] px-3.5 py-1 rounded-full border border-[#eac34a]/40 shadow-inner">
          SACRED CEREMONY 🧵
        </span>

        <h3 class="text-[26px] md:text-[38px] font-extrabold font-serif text-[#eac34a] tracking-tight drop-shadow-sm leading-[1.2]">
          Virtual Rakhi Ceremony
        </h3>
        <p class="text-[14px] md:text-[15px] text-[#d0c3cb]/90 max-w-md leading-[1.618]">
          Perform the sacred rituals of Tilak, Diya Aarti, Pushpa Varsha, and tie your favorite Royal Rakhi!
        </p>
        
        <button type="button" onclick="openCeremonyModal()" class="mt-2 px-8 py-3.5 bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#cca830] text-[#241a00] font-bold text-sm uppercase tracking-widest rounded-full shadow-[0_10px_30px_rgba(234,195,74,0.4)] hover:shadow-[0_15px_40px_rgba(234,195,74,0.6)] hover:-translate-y-1 active:scale-95 transition-all cursor-pointer inline-flex items-center gap-2.5">
          <span>Tap to Start Ceremony</span>
          <i data-lucide="arrow-right" class="w-5 h-5"></i>
        </button>
      </div>
    </div>

  </div>
</section>

<!-- RAKHI CEREMONY POPUP MODAL -->
<div id="rakhiCeremonyModal" class="hidden fixed inset-0 z-[100] bg-[#000000f0] backdrop-blur-md overflow-y-auto flex items-start sm:items-center justify-center p-3 sm:p-6 opacity-0 transition-opacity duration-500">
  <button type="button" onclick="closeCeremonyModal()" class="fixed top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#3b1e3b]/90 border border-[#eac34a]/60 text-[#eac34a] flex items-center justify-center hover:bg-[#eac34a] hover:text-black transition-all shadow-[0_0_20px_rgba(234,195,74,0.4)] z-[120]">
    <i data-lucide="x" class="w-5 h-5 sm:w-6 sm:h-6"></i>
  </button>

  <div class="relative w-full max-w-2xl sm:max-w-3xl mx-auto my-auto py-6 sm:py-8">
    <div id="royalThaliContainer" class="bg-gradient-to-br from-[#3b0811] via-[#240409] to-[#140205] border-2 border-[#eac34a] backdrop-blur-xl rounded-3xl p-5 sm:p-8 text-center space-y-5 sm:space-y-6 shadow-[0_0_60px_rgba(234,195,74,0.4)] relative overflow-hidden transition-all duration-500 animate-thali-glow">
      
      <!-- Hanging Brass Temple Bell -->
      <div class="flex flex-col items-center justify-center -mt-2">
        <div class="w-0.5 h-6 bg-[#eac34a]/60"></div>
        <div id="hangingBellItem" onclick="ringHangingBell()" title="Tap to Ring Temple Bell" class="w-12 h-12 rounded-full bg-gradient-to-tr from-amber-600 via-yellow-400 to-amber-500 border-2 border-[#eac34a] p-1 shadow-[0_0_20px_rgba(234,195,74,0.6)] cursor-pointer hover:scale-110 transition-all flex items-center justify-center group z-30">
          <span id="hangingBellIcon" class="text-2xl">🔔</span>
          <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2.5 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">Ring Bell 🔔</span>
        </div>
      </div>

      <!-- Vedic Mantra Banner -->
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
        <p class="text-xs text-[#d0c3cb]/90 max-w-md mx-auto">Perform sacred rituals: Ring Bell, Apply Tilak, Light Diya Aarti, Shower Flowers, Offer Sweets &amp; Tie Royal Rakhi!</p>
      </div>

      <!-- Centerpiece Royal Aarti Thali Graphic Ring -->
      <div class="relative w-56 h-56 sm:w-64 sm:h-64 mx-auto my-4 flex items-center justify-center">
        <div id="thaliOuterRing" class="absolute inset-0 rounded-full bg-gradient-to-tr from-[#cca830] via-[#ffd700] to-[#b8860b] p-[6px] shadow-[0_0_45px_rgba(234,195,74,0.45)] transition-transform duration-1000">
          <div class="w-full h-full bg-gradient-to-br from-[#2a060b] via-[#3b0811] to-[#180306] rounded-full border-4 border-[#eac34a]/60 flex items-center justify-center relative shadow-inner overflow-hidden">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#eac34a_1px,transparent_1px)] [background-size:12px_12px]"></div>

            <!-- 1. Tilak -->
            <div id="dragRoliItem" onclick="applyRoyalTilak()" title="Apply Roli-Chandan Tilak (Step 1)" class="absolute top-4 left-5 sm:left-7 w-11 h-11 rounded-full bg-gradient-to-tr from-red-700 via-red-600 to-amber-500 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
              <span class="text-sm font-bold text-white shadow-sm">🔴</span>
              <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">1. Tilak</span>
            </div>

            <!-- 2. Diya -->
            <div id="diyaFlameContainer" onclick="lightRoyalDiya()" title="Light Diya & Aarti (Step 2)" class="absolute top-3 right-5 sm:right-7 w-11 h-11 rounded-full bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-300 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
              <span id="diyaFlameIcon" class="text-xl">🪔</span>
              <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">2. Diya</span>
            </div>

            <!-- Flowers -->
            <div onclick="triggerPushpaVarsha()" title="Shower Flowers" class="absolute bottom-4 left-6 sm:left-8 w-10 h-10 rounded-full bg-gradient-to-tr from-pink-600 via-rose-500 to-yellow-400 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
              <span class="text-base">🌸</span>
              <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">Flowers</span>
            </div>

            <!-- Sweets -->
            <div onclick="offerSweetPrasad()" title="Offer Sweet Prasad" class="absolute bottom-4 right-6 sm:right-8 w-10 h-10 rounded-full bg-gradient-to-tr from-amber-500 via-orange-400 to-yellow-300 border-2 border-[#eac34a] p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
              <span class="text-base">🍬</span>
              <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">Sweets</span>
            </div>

            <!-- 3. Rakhi -->
            <div id="dragRakhiItem" onclick="tieRoyalRakhi()" title="Tie Royal Rakhi (Step 3)" class="absolute bottom-2 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#ffe088] to-[#b8860b] border-2 border-white p-1 shadow-2xl cursor-pointer hover:scale-115 transition-all flex items-center justify-center group z-20">
              <span class="text-xl">🧵</span>
              <span class="absolute -bottom-5 text-[9px] font-extrabold text-[#eac34a] whitespace-nowrap bg-[#100d10] px-2 py-0.5 rounded-full border border-[#eac34a]/40 opacity-90">3. Rakhi</span>
            </div>

            <div class="w-14 h-14 rounded-full bg-[#1e0407] border-2 border-[#eac34a]/40 flex flex-col items-center justify-center text-center p-1 shadow-inner">
              <span class="text-lg animate-pulse">🌸</span>
              <span class="text-[7px] font-extrabold text-[#eac34a] uppercase tracking-wider">Aarti Thali</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
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
  </div>
</div>

<!-- ======================================================= -->
<!-- ACT 2: SIBLING NOSTALGIA & MEMORIES -->
<!-- ======================================================= -->

<!-- SECTION 2: SIBLING FIGHT METER & TV REMOTE RULES -->
<section class="max-w-4xl mx-auto px-4 py-8 relative z-10">
  <div class="bg-[#2a060b]/90 border-2 border-[#eac34a]/40 rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative backdrop-blur-md">
    <div class="text-center space-y-1">
      <span class="text-[10px] font-bold uppercase tracking-widest text-[#eac34a]">SIBLING HUMOR &amp; STATS 📺</span>
      <h3 class="text-xl sm:text-2xl font-bold font-serif text-[#e8e0e3]">Fight Meter &amp; Remote Rules 🤫</h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
      <div class="bg-[#180306] p-4.5 rounded-2xl border border-[#eac34a]/30 space-y-2">
        <div class="flex justify-between text-xs font-bold">
          <span class="text-[#e8e0e3]">TV Remote Sharing Ratio</span>
          <span class="text-[#eac34a]">80% <?= $partnerName ?> / 20% <?= $buyerName ?></span>
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
</section>

<!-- SECTION 3: SHAHI FARMAN UNROLLING SCROLL GALLERY (OUR CHERISHED MOMENTS) -->
<style>
  .rb-farman-section {
    position: relative;
    z-index: 10;
    overflow-x: clip;
  }
  .rb-farman-parchment {
    background: linear-gradient(to right, #cfb580 0%, #ebd7b3 2.5%, #fdfbf5 8%, #fffdf8 50%, #fdfbf5 92%, #ebd7b3 97.5%, #cfb580 100%);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.75), inset 0 0 35px rgba(160, 120, 50, 0.2);
    border-top: 4px solid #c9a658;
    border-bottom: 4px solid #c9a658;
    position: relative;
    border-radius: 1.5rem;
  }
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
  .rb-farman-rod-left { left: -10px; }
  .rb-farman-rod-right { right: -10px; }
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

  @media (max-width: 640px) {
    .rb-farman-rod-left { left: -6px; width: 14px; }
    .rb-farman-rod-right { right: -6px; width: 14px; }
    .rb-farman-rod-left::before, .rb-farman-rod-left::after,
    .rb-farman-rod-right::before, .rb-farman-rod-right::after { width: 22px; height: 14px; left: -4px; }
  }

  .rb-farman-card {
    background: #fcfbfa;
    border: 2px solid #dfc690;
    box-shadow: 0 4px 18px rgba(110, 85, 45, 0.14), inset 0 0 0 1px rgba(255, 255, 255, 0.9);
    border-radius: 1.25rem;
    padding: 0.65rem;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .rb-farman-card:hover {
    transform: translateY(-5px) scale(1.02);
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
  <div class="text-center space-y-3 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">ROYAL MEMORY SCROLL</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#fceabb] drop-shadow-md">Our Cherished Moments ✨</h2>
    <p class="text-xs text-[#d0c3cb]">Every memory photo uploaded by <?= $buyerName ?> framed inside the unrolling antique parchment scroll.</p>
    
    <!-- 3D VIRTUAL FLIPBOOK LAUNCHER BUTTON -->
    <div class="flex justify-center pt-2">
      <button type="button" onclick="openRakhiFlipbook()" class="inline-flex items-center gap-2.5 px-6 sm:px-8 py-3.5 rounded-full bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#cca830] text-[#241a00] font-bold text-xs sm:text-sm uppercase tracking-wider shadow-[0_0_25px_rgba(234,195,74,0.45)] hover:scale-105 hover:shadow-[0_0_35px_rgba(234,195,74,0.7)] transition-all cursor-pointer">
        <svg class="w-4 h-4 text-[#241a00] stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        <span>📖 Open 3D Virtual Album (Realistic Page Flip)</span>
      </button>
    </div>
  </div>

  <div class="rb-farman-parchment p-5 sm:p-10 relative">
    <div class="rb-farman-rod-left"></div>
    <div class="rb-farman-rod-right"></div>

    <!-- Floral Corner Accents -->
    <div class="absolute top-2 left-3 w-10 h-10 sm:w-14 sm:h-14 pointer-events-none opacity-80 z-10">
      <svg viewBox="0 0 100 100" fill="none">
        <path d="M15 45 C 25 25, 45 15, 65 15 C 45 30, 30 45, 15 45 Z" fill="#88a86e" opacity="0.85"/>
        <path d="M30 60 C 25 40, 40 25, 60 30 C 45 40, 40 55, 30 60 Z" fill="#e88495"/>
        <circle cx="40" cy="40" r="11" fill="#ec7085"/>
        <circle cx="40" cy="40" r="4.5" fill="#fde68a"/>
      </svg>
    </div>
    <div class="absolute top-2 right-3 w-10 h-10 sm:w-14 sm:h-14 pointer-events-none opacity-80 z-10 scale-x-[-1]">
      <svg viewBox="0 0 100 100" fill="none">
        <path d="M15 45 C 25 25, 45 15, 65 15 C 45 30, 30 45, 15 45 Z" fill="#88a86e" opacity="0.85"/>
        <path d="M30 60 C 25 40, 40 25, 60 30 C 45 40, 40 55, 30 60 Z" fill="#e88495"/>
        <circle cx="40" cy="40" r="11" fill="#ec7085"/>
        <circle cx="40" cy="40" r="4.5" fill="#fde68a"/>
      </svg>
    </div>

    <!-- Photo Cards Masonry Grid -->
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

<!-- ======================================================= -->
<!-- ACT 3: ROYAL CLIMAX & PHYSICAL KEEPSAKES -->
<!-- ======================================================= -->

<!-- SECTION 4: SHAHI TAMRAPATRA (OFFICIAL SIBLING BOND CERTIFICATE) -->
<section id="shahiTamrapatraSection" class="relative z-10 max-w-5xl mx-auto px-4 py-12 space-y-6">
  <div class="text-center space-y-2 mb-6">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">ROYAL DECREE &amp; SEAL</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#fceabb] drop-shadow-md">Shahi Tamrapatra 📜💖</h2>
    <p class="text-xs text-[#d0c3cb]">Official Sibling Bond Certificate — Sealed with sacred Rakhi threads &amp; lifelong promises!</p>
  </div>

  <div id="shahiTamrapatraContainer" class="w-full relative rounded-2xl sm:rounded-3xl shadow-[0_25px_60px_rgba(0,0,0,0.85)] border-2 border-[#d4af37] overflow-hidden bg-[#fdf9ee]">
    <svg id="shahiTamrapatraSvg" viewBox="0 0 1600 900" class="w-full h-auto block select-none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="shahiGoldHeading" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#402001"/>
          <stop offset="30%" stop-color="#7a4204"/>
          <stop offset="60%" stop-color="#aa6c12"/>
          <stop offset="100%" stop-color="#402001"/>
        </linearGradient>

        <filter id="shahiTextShadow" x="-20%" y="-20%" width="140%" height="140%">
          <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#ffffff" flood-opacity="0.95"/>
        </filter>
      </defs>

      <?php 
      $frameFilePath = __DIR__ . '/../../assets/images/shahi_master_certificate_frame.jpg';
      $frameSrc = (file_exists($frameFilePath)) 
          ? ('data:image/jpeg;base64,' . base64_encode(file_get_contents($frameFilePath))) 
          : (htmlspecialchars(APP_URL) . '/assets/images/shahi_master_certificate_frame.jpg');
      ?>
      <image href="<?= $frameSrc ?>" x="0" y="0" width="1600" height="900" preserveAspectRatio="none"/>

      <!-- Heading -->
      <text x="800" y="270" text-anchor="middle" font-family="'Cinzel Decorative', 'Cinzel', 'Playfair Display', Georgia, serif" font-size="52" font-weight="900" fill="url(#shahiGoldHeading)" filter="url(#shahiTextShadow)" letter-spacing="4">SHAHI TAMRAPATRA</text>
      <text x="800" y="315" text-anchor="middle" font-family="'Playfair Display', 'Brush Script MT', cursive" font-style="italic" font-size="28" font-weight="bold" fill="#851d2c" letter-spacing="1">Official Sibling Bond Certificate 💖</text>

      <!-- Parties Names Row with Center Heart Medallion -->
      <text x="490" y="380" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="34" font-weight="900" fill="#241402"><?= htmlspecialchars($partnerName) ?></text>
      <circle cx="800" cy="368" r="24" fill="#851d2c" stroke="#ffd700" stroke-width="2.5"/>
      <circle cx="800" cy="368" r="18" fill="#ffebee"/>
      <text x="800" y="375" text-anchor="middle" font-size="20">💖</text>
      <text x="1110" y="380" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="34" font-weight="900" fill="#241402"><?= htmlspecialchars($buyerName) ?></text>

      <!-- 2x2 Clean Balanced Sibling Promises Grid -->
      <?php 
      $vow1 = !empty($promisesList[0]) ? htmlspecialchars($promisesList[0]) : "Always Share Pizza 🍕";
      $vow2 = !empty($promisesList[1]) ? htmlspecialchars($promisesList[1]) : "Keep All Secrets 🤫";
      $vow3 = !empty($promisesList[2]) ? htmlspecialchars($promisesList[2]) : "Fight Over TV Remote 📺";
      $vow4 = !empty($promisesList[3]) ? htmlspecialchars($promisesList[3]) : "Eternal Support 💖";
      ?>
      <text x="520" y="475" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="21" font-weight="700" fill="#301802"><?= $vow1 ?></text>
      <text x="1080" y="475" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="21" font-weight="700" fill="#301802"><?= $vow2 ?></text>
      <text x="520" y="535" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="21" font-weight="700" fill="#301802"><?= $vow3 ?></text>
      <text x="1080" y="535" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="21" font-weight="700" fill="#301802"><?= $vow4 ?></text>

      <!-- Signatures & Verification ID Row -->
      <line x1="260" y1="675" x2="560" y2="675" stroke="#b89343" stroke-width="1.8" opacity="0.8"/>
      <text x="410" y="658" text-anchor="middle" font-family="'Playfair Display', 'Brush Script MT', cursive" font-style="italic" font-size="34" font-weight="bold" fill="#851d2c"><?= htmlspecialchars($partnerName) ?> ♡</text>
      <text x="410" y="700" text-anchor="middle" font-family="'Cinzel', Georgia, serif" font-size="11.5" font-weight="800" fill="#7a4d0e" letter-spacing="2">[SISTER'S SIGNATURE]</text>

      <line x1="1040" y1="675" x2="1340" y2="675" stroke="#b89343" stroke-width="1.8" opacity="0.8"/>
      <text x="1190" y="658" text-anchor="middle" font-family="'Playfair Display', 'Brush Script MT', cursive" font-style="italic" font-size="34" font-weight="bold" fill="#851d2c"><?= htmlspecialchars($buyerName) ?> ♡</text>
      <text x="1190" y="700" text-anchor="middle" font-family="'Cinzel', Georgia, serif" font-size="11.5" font-weight="800" fill="#7a4d0e" letter-spacing="2">[BROTHER'S SIGNATURE]</text>

      <?php $certId = 'SS-RB-' . date('Y') . '-' . strtoupper(substr(md5($gift['id'] ?? '8942'), 0, 4)); ?>
      <text x="1190" y="730" text-anchor="middle" font-family="'Cinzel', 'Courier New', monospace" font-size="12" font-weight="800" fill="#4a2602" letter-spacing="2">CERTIFICATE ID: <?= $certId ?></text>

      <text x="800" y="605" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="13" font-style="italic" fill="#7a5310">Issued with sacred love &bull; Raksha Bandhan <?= date('jS F Y') ?> &bull; Digital Archive Verified 🔒</text>
    </svg>
  </div>

  <!-- Download & Share Action Buttons -->
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

<!-- SECTION 5: 3D GLASSMORPHISM SIBLING PROMISE CARDS -->
<section id="siblingVowsSection" class="max-w-5xl mx-auto px-4 py-10 relative z-10 space-y-8">
  <div class="text-center space-y-2">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">BROTHER &amp; SISTER VOWS</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]"><?= count($promisesList) ?> Sacred Sibling Vows</h2>
    <div class="w-16 h-[2.5px] bg-[#eac34a] mx-auto mt-2 rounded-full shadow-[0_0_10px_#eac34a]"></div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($promisesList as $pIdx => $pText): ?>
      <div class="p-6 space-y-5 shadow-2xl relative group overflow-hidden border border-[#eac34a]/30 hover:border-[#eac34a]/80 rounded-2xl transition-all duration-500 bg-gradient-to-br from-[#2a060b]/90 via-[#151215]/95 to-[#3b1e3b]/90 backdrop-blur-xl hover:-translate-y-2 hover:shadow-[0_15px_40px_rgba(234,195,74,0.15)]">
        <div class="absolute inset-0 bg-gradient-to-b from-[#eac34a]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none z-0"></div>
        <div class="absolute -bottom-6 -right-6 text-[#eac34a]/5 text-9xl pointer-events-none z-0 rotate-12 group-hover:rotate-45 transition-transform duration-1000">❁</div>

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

<!-- SECTION 6: WAX-SEALED 3D ROYAL SHAGUN LIFAFA -->
<section class="max-w-3xl mx-auto px-4 py-10 relative z-10">
  <div class="text-center space-y-2 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">SPECIAL GIFT &amp; BLESSINGS</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Digital Shagun Envelope</h2>
    <p class="text-xs text-[#d0c3cb]">Tap the wax-sealed lifafa to reveal your gift voucher and personal letter!</p>
  </div>

  <!-- Closed Envelope UI with Royal Wax Seal -->
  <div id="shagunEnvelopeContainer" onclick="toggleShagunLifafa()" class="bg-gradient-to-br from-[#800f1c] via-[#590a13] to-[#2b0509] border-2 border-[#eac34a] rounded-3xl p-8 text-center cursor-pointer shadow-[0_0_40px_rgba(234,195,74,0.3)] hover:scale-[1.02] active:scale-95 transition-all relative overflow-hidden group">
    <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-amber-600 via-yellow-400 to-amber-500 border-2 border-white text-[#241a00] flex items-center justify-center mx-auto mb-3 text-2xl font-serif font-bold shadow-[0_0_25px_rgba(234,195,74,0.8)] group-hover:scale-110 transition-transform">
      ॐ
    </div>
    <div class="space-y-2">
      <span class="text-xs font-extrabold uppercase tracking-widest text-[#eac34a] bg-[#151215]/80 px-4 py-1 rounded-full border border-[#eac34a]/40 inline-block">ROYAL SHAGUN LIFAFA</span>
      <h3 class="text-2xl font-bold font-serif text-white">Tap to Open Envelope</h3>
      <p class="text-xs text-[#f5d77f]">Contains personal note &amp; gift voucher code</p>
    </div>
  </div>

  <!-- Opened Envelope Content -->
  <div id="shagunLetterContent" class="hidden bg-[#221f21] border-2 border-[#eac34a] rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative animate-fade-in">
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
              Surprise Amazon Cash Voucher
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
              Amazon Cash Voucher Unlocked!
            </span>
            <h4 class="text-2xl font-black font-serif text-[#a4e4b9]">₹<?= $vAmt ?> Amazon Gift Voucher</h4>
            <p class="text-xs text-[#d0c3cb]">Copy your gift code below and redeem directly on Amazon India!</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-1">
              <strong class="text-base font-mono text-white bg-[#100d10] px-5 py-2.5 rounded-xl border border-[#a4e4b9]/40 tracking-widest shadow-inner"><?= $vCode ?></strong>
              <button type="button" onclick="navigator.clipboard.writeText('<?= $vCode ?>'); alert('Voucher code copied to clipboard!'); confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-[#a4e4b9] to-[#6ee7b7] text-[#100d10] font-extrabold text-xs uppercase tracking-wider rounded-xl hover:brightness-110 transition-all cursor-pointer shadow-lg">
                Copy Code &amp; Redeem
              </button>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <!-- Amazon Affiliate Store Recommendations -->
      <?php if (!empty($rakhiAffiliateProducts)): ?>
        <div class="pt-6 border-t border-[#4d444b]/40 space-y-3">
          <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#eac34a] block text-center">
            Recommended Rakhi Gifts on Amazon (Redeem Voucher Directly Below)
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

<!-- SECTION 7: ROYAL KEEPSAKE ACTION CENTER (PRINTABLE PHOTOBOOK & WALL POSTER) -->
<section class="max-w-4xl mx-auto px-4 py-12 relative z-10">
  <div class="text-center space-y-2 mb-8">
    <div class="inline-flex items-center gap-2 bg-[#d4af37]/20 border border-[#d4af37] px-4 py-1 rounded-full text-xs font-bold text-[#fceabb] uppercase tracking-widest">
      <span>👑 PHYSICAL &bull; PRINTABLE KEEPSAKES</span>
    </div>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#fceabb] drop-shadow-md">Printable Memory Keepsakes 🖼️📖</h2>
    <p class="text-xs text-[#d0c3cb]">Turn your digital memories into 300 DPI high-definition physical treasures to print, frame, or bind!</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Keepsake Card 1: Wall Collage Poster -->
    <div class="bg-gradient-to-br from-[#2b1f13] via-[#1c150c] to-[#151008] border-2 border-[#d4af37]/70 rounded-3xl p-6 sm:p-7 shadow-[0_15px_40px_rgba(0,0,0,0.85)] flex flex-col justify-between space-y-5 relative overflow-hidden group hover:border-[#ffd700] transition-all">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-3 py-1 bg-[#d4af37]/30 border border-[#d4af37] rounded-full text-[10px] font-black uppercase tracking-wider text-[#ffd700]">FRAME READY (A4/A3)</span>
          <span class="text-2xl">🖼️</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold font-serif text-[#fceabb]">Wall Collage Poster</h3>
        <p class="text-xs text-[#d5c7bc] leading-relaxed">
          A luxury 300 DPI wall-frame keepsake featuring <?= $partnerName ?>'s portrait in a 24K gold locket, surrounded by an uncropped polaroid memory mosaic.
        </p>
      </div>

      <button type="button" id="btnWallPoster" onclick="downloadWallKeepsakePoster()" class="w-full py-3.5 bg-gradient-to-r from-[#d4af37] via-[#f7e6a6] to-[#b89343] text-[#241a00] font-extrabold text-xs sm:text-sm uppercase tracking-wider rounded-full shadow-[0_8px_25px_rgba(212,175,55,0.45)] hover:shadow-[0_12px_35px_rgba(212,175,55,0.65)] hover:-translate-y-0.5 active:scale-95 transition-all cursor-pointer inline-flex items-center justify-center gap-2">
        <i data-lucide="image" class="w-4 h-4 text-[#241a00]"></i>
        <span>Download Wall Poster (300 DPI)</span>
      </button>
    </div>

    <!-- Keepsake Card 2: Multi-Page Photobook PDF -->
    <div class="bg-gradient-to-br from-[#1a2e20] via-[#122016] to-[#0c150e] border-2 border-[#10b981]/70 rounded-3xl p-6 sm:p-7 shadow-[0_15px_40px_rgba(0,0,0,0.85)] flex flex-col justify-between space-y-5 relative overflow-hidden group hover:border-[#34d399] transition-all">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-3 py-1 bg-[#10b981]/30 border border-[#10b981] rounded-full text-[10px] font-black uppercase tracking-wider text-[#a7f3d0]">MULTI-PAGE ALBUM (PDF)</span>
          <span class="text-2xl">📖</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold font-serif text-[#a7f3d0]">Sibling Keepsake Book</h3>
        <p class="text-xs text-[#c5dbcc] leading-relaxed">
          A luxury 7-page printable storybook album with Royal Cover, Shahi Tamrapatra Certificate, chapter stories, and a dynamic QR code to relive this website anytime!
        </p>
      </div>

      <button type="button" id="btnPhotobook" onclick="downloadSiblingPhotobookPDF()" class="w-full py-3.5 bg-gradient-to-r from-[#10b981] via-[#34d399] to-[#059669] text-white font-extrabold text-xs sm:text-sm uppercase tracking-wider rounded-full shadow-[0_8px_25px_rgba(16,185,129,0.45)] hover:shadow-[0_12px_35px_rgba(16,185,129,0.65)] hover:-translate-y-0.5 active:scale-95 transition-all cursor-pointer inline-flex items-center justify-center gap-2">
        <i data-lucide="book-open" class="w-4 h-4 text-white"></i>
        <span>Download Keepsake Book (PDF)</span>
      </button>
    </div>
  </div>
</section>

<!-- Fullscreen Photo Lightbox Modal -->
<div id="rbLightboxModal" class="fixed inset-0 z-[150] bg-black/90 backdrop-blur-md hidden items-center justify-center p-4 cursor-pointer" onclick="closeLightbox()">
  <button type="button" onclick="closeLightbox()" class="absolute top-6 right-6 w-12 h-12 rounded-full bg-[#2a060b] border border-[#eac34a] text-[#eac34a] flex items-center justify-center text-xl hover:bg-[#eac34a] hover:text-black transition-all">✕</button>
  <img id="rbLightboxImg" src="" class="max-w-[90vw] max-h-[85vh] object-contain rounded-2xl border-2 border-[#eac34a] shadow-[0_0_50px_rgba(234,195,74,0.4)]" onclick="event.stopPropagation()">
</div>

<!-- Footer Bar -->
<footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
  <p class="text-xs text-[#d0c3cb]">Made with endless love by <strong class="text-[#eac34a]"><?= $buyerName ?></strong> for <strong class="text-[#eac34a]"><?= $partnerName ?></strong></p>
  <div class="flex items-center justify-center gap-3">
    <button onclick="relockGiftSession()" type="button" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer">
      <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
      <span>Lock Gift Page</span>
    </button>
  </div>
</footer>

<!-- FLOATING MARIGOLD & STARDUST CANVAS SCRIPT -->
<script>
  (function initFloatingMarigoldPetals() {
    const canvas = document.getElementById('floatingMarigoldCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let width = canvas.width = window.innerWidth;
    let height = canvas.height = window.innerHeight;

    window.addEventListener('resize', () => {
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
    });

    const petals = [];
    const petalColors = ['#f59e0b', '#fbbf24', '#d97706', '#ffd700', '#f43f5e'];

    for (let i = 0; i < 35; i++) {
      petals.push({
        x: Math.random() * width,
        y: Math.random() * height,
        r: Math.random() * 5 + 3,
        dx: Math.random() * 1.2 - 0.6,
        dy: Math.random() * 0.8 + 0.5,
        rot: Math.random() * Math.PI * 2,
        rotSpeed: Math.random() * 0.03 - 0.015,
        color: petalColors[Math.floor(Math.random() * petalColors.length)],
        opacity: Math.random() * 0.5 + 0.3
      });
    }

    function animatePetals() {
      ctx.clearRect(0, 0, width, height);

      petals.forEach(p => {
        p.x += p.dx;
        p.y += p.dy;
        p.rot += p.rotSpeed;

        if (p.y > height + 20) { p.y = -20; p.x = Math.random() * width; }
        if (p.x > width + 20) { p.x = -20; }
        if (p.x < -20) { p.x = width + 20; }

        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rot);
        ctx.fillStyle = p.color;
        ctx.globalAlpha = p.opacity;

        ctx.beginPath();
        ctx.ellipse(0, 0, p.r * 1.8, p.r, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
      });

      requestAnimationFrame(animatePetals);
    }
    animatePetals();
  })();

  function openLightbox(url) {
    const modal = document.getElementById('rbLightboxModal');
    const img = document.getElementById('rbLightboxImg');
    if (modal && img) {
      img.src = url;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
  }

  function closeLightbox() {
    const modal = document.getElementById('rbLightboxModal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }
</script>

<!-- ======================================================= -->
<!-- 3D INTERACTIVE VIRTUAL FLIPBOOK MODAL & STAGE -->
<!-- ======================================================= -->
<div id="soulscriptFlipbookModal" class="fixed inset-0 z-[999999] hidden bg-[#0d0a0d]/95 backdrop-blur-2xl flex-col items-center justify-between p-2 sm:p-4 overflow-hidden h-screen max-h-screen w-screen max-w-screen">
  
  <!-- Top Control Bar -->
  <div class="w-full max-w-4xl flex items-center justify-between gap-4 py-2.5 px-4 bg-[#221f21]/95 border border-[#eac34a]/40 rounded-2xl backdrop-blur-md shadow-2xl shrink-0 z-50 mt-1">
    <div class="flex items-center gap-2">
      <span class="w-2.5 h-2.5 rounded-full bg-[#eac34a] animate-pulse"></span>
      <span class="font-serif font-bold text-xs sm:text-sm text-[#fceabb] truncate">
        📖 <?= $partnerName ?> &amp; <?= $buyerName ?>'s 3D Virtual Album
      </span>
    </div>

    <!-- Page Controls & Counter -->
    <div class="flex items-center gap-2 sm:gap-4">
      <span id="fbPageCounter" class="font-mono text-[11px] font-bold text-[#eac34a] bg-[#151215] px-3 py-1 rounded-full border border-[#eac34a]/30">
        1 / 1
      </span>

      <!-- Sound Toggle -->
      <button id="fbSoundToggleBtn" onclick="window.__rakhiFlipbookInstance &amp;&amp; window.__rakhiFlipbookInstance.toggleSound()" class="p-2 rounded-xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition cursor-pointer" title="Toggle Sound FX">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11 5L6 9H2v6h4l5 4V5zM15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/></svg>
      </button>

      <!-- Close Modal -->
      <button onclick="closeRakhiFlipbook()" class="p-2 rounded-xl bg-red-950/80 text-red-300 border border-red-500/40 hover:bg-red-900 transition cursor-pointer" title="Close Flipbook">
        <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
  </div>

  <!-- Central 3D Flipbook Canvas Area -->
  <div class="flex-1 w-full max-w-5xl flex items-center justify-center relative my-2 overflow-hidden">
    
    <!-- Floating Previous Arrow -->
    <button onclick="window.__rakhiFlipbookInstance &amp;&amp; window.__rakhiFlipbookInstance.flipPrev()" class="absolute left-2 sm:left-4 z-30 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#eac34a]/90 text-[#241a00] border-2 border-[#151215] shadow-[0_0_20px_rgba(234,195,74,0.5)] flex items-center justify-center hover:scale-110 active:scale-95 transition cursor-pointer">
      <svg class="w-6 h-6 stroke-current stroke-3 fill-none" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>

    <!-- The 3D Book Element Container -->
    <div id="rakhiFlipbookContainer" class="shadow-[0_25px_60px_rgba(0,0,0,0.9)] rounded-xl overflow-hidden">
      
      <!-- COVER PAGE -->
      <div class="fb-page bg-gradient-to-br from-[#3b1e3b] via-[#221f21] to-[#151215] p-6 text-center border-4 border-[#eac34a] flex flex-col justify-between select-none relative">
        <div class="absolute inset-2 border border-[#eac34a]/40 pointer-events-none"></div>
        <div class="pt-6 space-y-2">
          <span class="text-[10px] font-bold tracking-[0.3em] uppercase text-[#eac34a]">ROYAL KEEPSAKE ALBUM</span>
          <h2 class="text-2xl sm:text-3xl font-bold font-serif text-[#fceabb]"><?= $partnerName ?> &amp; <?= $buyerName ?></h2>
          <p class="text-xs text-[#e4b9df] italic"><?= $taglineQuote ?></p>
        </div>
        <div class="my-auto py-6">
          <div class="w-24 h-24 sm:w-32 sm:h-32 mx-auto rounded-full border-4 border-[#eac34a] p-1 bg-[#151215] shadow-[0_0_30px_rgba(234,195,74,0.4)] overflow-hidden">
            <?= $photoAvatarHtml ?>
          </div>
        </div>
        <div class="pb-4 space-y-1">
          <span class="text-[11px] font-mono text-[#eac34a]">Swipe or Drag Corner to Flip ➔</span>
          <p class="text-[9px] text-[#d0c3cb]/60">Sealed with Royal Rakhi Mandalas</p>
        </div>
      </div>

      <!-- PAGE 2: VOWS & PROMISES -->
      <div class="fb-page bg-[#fdf9ee] text-[#3b2b1b] p-6 border-2 border-[#d4af37] flex flex-col justify-between select-none relative">
        <div class="space-y-4">
          <div class="border-b border-[#d4af37]/40 pb-3 text-center">
            <span class="text-[9px] font-bold tracking-widest text-[#a87c1c] uppercase block">ACT OF PROTECTION</span>
            <h3 class="text-lg font-bold font-serif text-[#5c3a0d]">5 Royal Sibling Promises 👑</h3>
          </div>
          <ul class="space-y-3 text-xs">
            <?php foreach ($promisesList as $idx => $promise): ?>
              <li class="flex items-start gap-2 bg-[#f4ecd8] p-2.5 rounded-xl border border-[#e8dcb8]">
                <span class="font-bold text-[#a87c1c] font-mono"><?= $idx + 1 ?>.</span>
                <span><?= htmlspecialchars($promise) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="text-center text-[10px] text-[#a87c1c] pt-2 border-t border-[#d4af37]/40">
          Lifelong Bond of Love &amp; Protection
        </div>
      </div>

      <!-- PAGES 3+: MEMORY PHOTOS -->
      <?php foreach ($media as $mIdx => $m): 
        $mUrl = htmlspecialchars(resolveMediaUrl($m['file_path'] ?? ''));
        $mCap = htmlspecialchars($m['caption'] ?? 'Cherished Memory #' . ($mIdx + 1));
      ?>
        <div class="fb-page bg-[#f7f3eb] text-[#3b2b1b] p-4 sm:p-6 border-2 border-[#ebd9b5] flex flex-col justify-between select-none relative">
          <div class="space-y-3 flex-1 flex flex-col justify-center">
            <div class="bg-white p-2 rounded-xl border border-[#d8cbb5] shadow-md aspect-4/3 overflow-hidden">
              <img src="<?= $mUrl ?>" onerror="this.onerror=null; this.src='<?= htmlspecialchars(APP_URL) ?>/assets/default_gallery/sample_fa6955df.webp';" class="w-full h-full object-cover rounded-lg">
            </div>
            <div class="text-center px-2 py-1 bg-[#ede4d3] rounded-lg border border-[#dbcfae]">
              <p class="font-serif text-xs font-bold text-[#4a341b] italic"><?= $mCap ?></p>
            </div>
          </div>
          <div class="text-center text-[9px] font-mono text-[#a87c1c] pt-2 border-t border-[#ebd9b5]">
            Memory #<?= $mIdx + 1 ?> of <?= count($media) ?>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- BACK COVER -->
      <div class="fb-page bg-gradient-to-tl from-[#3b1e3b] via-[#221f21] to-[#151215] p-6 text-center border-4 border-[#eac34a] flex flex-col justify-between select-none relative">
        <div class="absolute inset-2 border border-[#eac34a]/40 pointer-events-none"></div>
        <div class="pt-8 space-y-2">
          <span class="text-3xl">🪔</span>
          <h3 class="text-xl font-bold font-serif text-[#fceabb]">Happy Raksha Bandhan</h3>
          <p class="text-xs text-[#e4b9df]">Forever Bond of Brother &amp; Sister</p>
        </div>
        <div class="my-auto space-y-2">
          <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-tr from-amber-600 to-yellow-400 border-2 border-[#eac34a] flex items-center justify-center text-2xl shadow-[0_0_20px_#f59e0b]">
            👑
          </div>
          <p class="text-[11px] text-[#eac34a] font-serif italic">"Miles or Years, Love Stays Eternal"</p>
        </div>
        <div class="pb-4">
          <span class="text-[10px] font-mono text-[#d0c3cb]/70">Created with <?= htmlspecialchars(APP_NAME) ?></span>
        </div>
      </div>

    </div>

    <!-- Floating Next Arrow -->
    <button onclick="window.__rakhiFlipbookInstance &amp;&amp; window.__rakhiFlipbookInstance.flipNext()" class="absolute right-2 sm:right-4 z-30 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#eac34a]/90 text-[#241a00] border-2 border-[#151215] shadow-[0_0_20px_rgba(234,195,74,0.5)] flex items-center justify-center hover:scale-110 active:scale-95 transition cursor-pointer">
      <svg class="w-6 h-6 stroke-current stroke-3 fill-none" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
  </div>

  <!-- Bottom Tip -->
  <div class="py-1 text-center shrink-0">
    <p class="text-[10px] font-mono text-[#d0c3cb]/70">Tip: Drag corner or use Left/Right arrows to flip pages with sound</p>
  </div>
</div>

<!-- Load StPageFlip Browser Engine & SoulScript Flipbook Controller -->
<script src="<?= htmlspecialchars(APP_URL) ?>/assets/js/page-flip.browser.js"></script>
<script src="<?= htmlspecialchars(APP_URL) ?>/assets/js/flipbook_engine.js"></script>

<script>
  window.__rakhiFlipbookInstance = null;

  function openRakhiFlipbook() {
    if (typeof SoulScriptFlipbook === 'undefined') {
      alert('Flipbook engine loading... Please try in a moment.');
      return;
    }
    if (!window.__rakhiFlipbookInstance) {
      window.__rakhiFlipbookInstance = new SoulScriptFlipbook({
        containerId: 'rakhiFlipbookContainer',
        modalId: 'soulscriptFlipbookModal'
      });
    }
    window.__rakhiFlipbookInstance.openModal();
  }

  function closeRakhiFlipbook() {
    if (window.__rakhiFlipbookInstance) {
      window.__rakhiFlipbookInstance.closeModal();
    }
  }
</script>
