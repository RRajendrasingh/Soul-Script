<?php
/**
 * SoulScript - Raksha Bandhan Festive Light Theme (100% Google Stitch Design Matching Edition)
 * File: templates/themes/raksha_bandhan_festive_light.php
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../includes/media_helper.php';
    require_once __DIR__ . '/../../includes/voucher_helper.php';
}

// 1. Resolve DB Data safely
$partnerName = $content['partner_name'] ?? $initialLockData['partner_name'] ?? 'Ritu';
$buyerName = $content['buyer_name'] ?? $initialLockData['buyer_name'] ?? 'Rajendra Rathore';
$taglineQuote = $content['tagline_quote'] ?? "To the world's sweetest & most protective sister!";
$loveNoteText = $content['love_note_text'] ?? "No matter how much we argue, you are the most special person in my life. Thank you for always being my second mother, mentor, and secret-keeper. Here is a little Shagun of love!";
$receiverPhoto = $content['receiver_photo'] ?? $initialLockData['receiver_photo'] ?? '';
$cleanReceiverPhoto = !empty($receiverPhoto) ? resolveMediaUrl($receiverPhoto) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuAv7Va4cDP2vPfj8FhMEG9UVDS-tAXS0-jvBqMjw79z1dlQJDpqxtSKnTqUZ3Mu5TzmLveM4Biz3yfvPOZKuqDAXDa4W_bp-MFlLUx4mJ7Ha6_rpHDAh_02Lu4gJJYTMx9YsrRVXJMu9YIFcN6mJ3Ykq4IsHzwi2l61bA2FRCR-TEdD12suP0hb2kAOuch98Ddq2SLsPW6gfChDFzvU7jz0ySrNPDhWdcz9pE8uCxdfuKggh2Vmso7O';

$promisesList = $tf['reasons'] ?? [];
$pageId = $initialLockData['page_id'] ?? $editPageData['page_id'] ?? 0;

// Resolve Audio Song
$bgMusicUrl = $content['bg_music_url'] ?? APP_URL . '/assets/audio/rakhi_theme.mp3';

// Resolve Voucher Status
if (!isset($rakhiVoucherStatus)) {
    $rakhiVoucherStatus = getRakhiVoucherUnlockStatus(null, $pageId);
}
$isUnlocked = !empty($rakhiVoucherStatus['unlocked']);
$voucherAmount = !empty($rakhiVoucherStatus['allocated_amount']) ? intval($rakhiVoucherStatus['allocated_amount']) : 500;
$voucherCode = !empty($rakhiVoucherStatus['voucher_code']) ? $rakhiVoucherStatus['voucher_code'] : '';
$unlockDateFormatted = !empty($rakhiVoucherStatus['unlock_date_formatted']) ? $rakhiVoucherStatus['unlock_date_formatted'] : '28 August 2026, 12:00 PM IST';
$secondsRemaining = !empty($rakhiVoucherStatus['seconds_remaining']) ? intval($rakhiVoucherStatus['seconds_remaining']) : 0;

// Default Vows
$defaultVows = [
  ['num' => '1', 'title' => 'Always Support', 'desc' => 'Rowing your support Heart & Hands forever.', 'icon' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>'],
  ['num' => '2', 'title' => 'Share Laughter', 'desc' => 'Sharing our endless sibling silly jokes.', 'icon' => '<circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" x2="9.01" y1="9" y2="9"></line><line x1="15" x2="15.01" y1="9" y2="9"></line>'],
  ['num' => '3', 'title' => 'Protect You', 'desc' => 'Standing strong to shield you from all worries.', 'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>'],
  ['num' => '4', 'title' => 'Be There', 'desc' => 'Linked rings of sisterly protection.', 'icon' => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>']
];

// Gallery Photos
$galleryMedia = !empty($media) && is_array($media) ? $media : [];
?>

<!-- GOOGLE STITCH EXACT HEAD STYLES & FONTS -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

<style>
:root {
  --bg-color: #fcf6f0;
  --text-maroon: #4a232f;
  --border-red: #c93d3d;
  --btn-gradient-start: #d32f2f;
  --btn-gradient-end: #f57c00;
}
.festive-stitch-body {
  background-color: var(--bg-color);
  color: var(--text-maroon);
  font-family: 'Inter', sans-serif;
}
.font-serif {
  font-family: 'Playfair Display', serif;
}
.text-maroon {
  color: var(--text-maroon);
}
.border-red-custom {
  border-color: var(--border-red);
}
.btn-gradient {
  background: linear-gradient(to right, var(--btn-gradient-start), var(--btn-gradient-end));
}
.card-shadow {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
</style>

<div class="festive-stitch-body antialiased min-h-screen relative w-full max-w-full overflow-x-hidden pt-4 sm:pt-6">

  <main class="w-full space-y-12 sm:space-y-20">

    <!-- 1. HERO SECTION -->
    <section class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-12 pt-2">
      <div class="bg-red-50/30 p-6 sm:p-12 lg:p-16 rounded-3xl grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
        <div class="space-y-4 sm:space-y-6 text-center md:text-left">
          <h1 class="text-3xl sm:text-5xl md:text-6xl font-serif font-extrabold text-maroon leading-tight tracking-tight break-words">
            Happy Raksha Bandhan<br>
            <span style="color: rgb(229, 83, 75);"><?= htmlspecialchars($partnerName) ?></span>
          </h1>
          <p class="text-gray-600 text-sm sm:text-lg leading-relaxed">
            <?= htmlspecialchars($taglineQuote) ?>
          </p>
        </div>

        <div class="relative flex justify-center md:justify-end">
          <div class="relative inline-block">
            <div class="w-48 h-48 sm:w-64 sm:h-64 md:w-[380px] md:h-[380px] rounded-full border-8 border-white shadow-xl overflow-hidden relative z-10">
              <img src="<?= htmlspecialchars($cleanReceiverPhoto) ?>" alt="<?= htmlspecialchars($partnerName) ?>" class="w-full h-full object-cover">
            </div>
            <div class="absolute inset-0 -m-3 sm:-m-6 rounded-full border-4 border-red-200 opacity-50 animate-pulse"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. SIBLING PROMISE 3D CARDS -->
    <section class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-12 space-y-8">
      <h2 class="text-2xl sm:text-3xl font-serif font-bold text-maroon flex items-center justify-center gap-3 text-center">
        <svg class="text-red-500 shrink-0" fill="none" height="28" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="28"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>
        <span>Sibling Promise 3D Cards</span>
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($defaultVows as $idx => $vow): 
          $customDesc = !empty($promisesList[$idx]) ? $promisesList[$idx] : $vow['desc'];
        ?>
          <div class="bg-white rounded-3xl p-6 sm:p-8 card-shadow border border-maroon/5 hover:shadow-xl transition-shadow space-y-6">
            <div class="flex justify-between items-start">
              <span class="text-5xl font-serif font-bold text-maroon/20"><?= $vow['num'] ?></span>
              <div class="w-12 h-12 rounded-full border border-red-200 flex items-center justify-center text-red-500 bg-red-50/30">
                <svg fill="none" height="24" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="24"><?= $vow['icon'] ?></svg>
              </div>
            </div>
            <div>
              <h3 class="text-xl font-bold text-maroon mb-2"><?= htmlspecialchars($vow['title']) ?></h3>
              <p class="text-gray-500 leading-relaxed text-sm">"<?= htmlspecialchars($customDesc) ?>"</p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 3. VIRTUAL RAKHI CEREMONY SECTION -->
    <section class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-12">
      <div class="bg-red-50/30 rounded-[32px] sm:rounded-[40px] border border-red-200 p-6 sm:p-12 text-center space-y-8 card-shadow relative">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-serif font-bold text-maroon flex items-center justify-center gap-3">
          ✨ Tie a Virtual Rakhi ✨
        </h2>

        <div class="flex justify-center items-center gap-4 sm:gap-10 md:gap-16">
          <div class="flex flex-col items-center gap-2 opacity-60">
            <svg class="text-red-500" fill="none" height="28" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="28"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>
            <span class="text-xs sm:text-sm font-medium text-gray-600">Diya</span>
          </div>

          <div class="rounded-full border-4 sm:border-8 border-white shadow-xl relative z-10 w-28 h-28 sm:w-40 sm:h-40 md:w-48 md:h-48 flex items-center justify-center bg-gray-100 overflow-hidden shrink-0">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZfsICxK34oixmN1AZRizpBM2bZC5BAB_XYhQLhxKaZRKgNxEv8X9v3Z4lzEedQVni4JuXg6LECezawWUPThbfyUKDAnCX14tBlz_SHV5Z0nHTlrYpNX81aS2JbA1-fREPTFZBGfA4Oin9IzGHb5PZxUinsPuL6pU81_ZnpEIrbooze4l1aomWnjr8FWAmwYUcQR92cij0amxmT3sNwf3Uq4XO2ot9yJ_JaQvk6cQiDvzzRP2Mvcj0" alt="Rakhi Thali" class="w-full h-full object-cover">
          </div>

          <div class="flex flex-col items-center gap-2 opacity-60">
            <svg class="text-red-500" fill="none" height="28" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="28"><rect height="4" rx="1" width="20" x="2" y="16"></rect><rect height="4" rx="1" width="16" x="4" y="12"></rect><path d="M12 12v-4"></path><path d="M8 12v-2"></path><path d="M16 12v-2"></path><path d="M12 4v4"></path></svg>
            <span class="text-xs sm:text-sm font-medium text-gray-600">Mithai</span>
          </div>
        </div>

        <button type="button" onclick="openStitchRakhiModal()" class="btn-gradient text-white font-bold py-3.5 sm:py-4 px-8 sm:px-12 rounded-full shadow-lg hover:opacity-90 transition-opacity text-base sm:text-lg inline-block cursor-pointer">
          Tap to Tie Rakhi
        </button>
      </div>
    </section>

    <!-- 4. SPLIT SECTION: ENVELOPE & ROYAL DECREE -->
    <section class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
      
      <!-- LEFT: SHAGUN ENVELOPE & REVEAL CARD -->
      <div class="space-y-6 sm:space-y-8">
        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-maroon flex items-center gap-3">
          <svg class="text-red-500 shrink-0" fill="none" height="28" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="28"><rect height="10" rx="2" width="18" x="3" y="11"></rect><circle cx="12" cy="5" r="2"></circle><path d="M12 7v4"></path><line x1="8" x2="8" y1="16" y2="16"></line><line x1="16" x2="16" y1="16"></line></svg>
          <span>Shagun Envelope ✉️</span>
        </h2>

        <div class="relative rounded-[32px] sm:rounded-[40px] overflow-hidden card-shadow min-h-[480px] bg-red-50/10 border-2 border-red-custom p-4 flex flex-col justify-center">
          
          <?php if (!$isUnlocked): ?>
            <!-- STATE 1 & 2: SCRATCH CARD OVERLAY & LOCKED TIMER -->
            <div class="relative w-full h-full min-h-[420px] rounded-3xl overflow-hidden bg-[#fefce8] p-6 space-y-6 shadow-sm border border-[#fef08a] flex flex-col justify-between">
              
              <!-- Scratch Canvas Engine Container -->
              <div id="stitchScratchContainer" class="absolute inset-0 z-20 flex flex-col items-center justify-center cursor-crosshair">
                <canvas id="stitchScratchCanvas" class="absolute inset-0 w-full h-full"></canvas>
                <div id="stitchScratchPrompt" class="relative z-30 flex flex-col items-center gap-3 pointer-events-none text-center p-4">
                  <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-lg">
                    <span class="material-symbols-outlined text-white text-5xl">card_giftcard</span>
                  </div>
                  <p class="text-white font-bold text-xl sm:text-2xl tracking-wide drop-shadow-md">Scratch to reveal Shagun 🎁</p>
                </div>
              </div>

              <!-- State 2 Content under Scratch Canvas -->
              <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 text-emerald-600">
                  <span class="material-symbols-outlined text-sm">lock_open</span>
                  <span class="text-xs font-bold tracking-widest uppercase">Envelope Locked Yet</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-serif font-bold text-gray-900">Your Shagun is Available Soon!</h3>
                <p class="text-sm text-gray-700 leading-relaxed italic">"<?= htmlspecialchars($loveNoteText) ?>"</p>
              </div>

              <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200 space-y-2">
                <p class="text-xs font-medium text-gray-600 text-center">
                  <b>Your surprise Amazon Gift Voucher unlocks on <?= htmlspecialchars($unlockDateFormatted) ?></b>
                </p>
                <div id="stitchTimerDisplay" class="text-center font-mono font-bold text-red-600 text-base">
                  Loading countdown...
                </div>
              </div>

              <button class="w-full bg-[#0d99ff] hover:bg-blue-600 text-white font-bold py-3.5 px-6 rounded-full shadow-lg transition-all flex items-center justify-center gap-2 text-base">
                <span class="material-symbols-outlined text-lg">shopping_cart</span>
                <span>Unlocks on Raksha Bandhan 🤭</span>
              </button>
            </div>

          <?php else: ?>
            <!-- STATE 3: UNLOCKED VOUCHER & AMAZON SUGGESTIONS -->
            <div class="bg-[#fefce8] rounded-3xl p-6 sm:p-8 space-y-6 shadow-sm border border-[#fef08a] h-full flex flex-col justify-between">
              <div class="space-y-3">
                <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 text-[10px] font-black uppercase rounded-full tracking-wider">
                  ✓ VOUCHER UNLOCKED
                </span>
                <h3 class="font-serif font-bold text-2xl text-maroon">A Letter for My Best Sister, <?= htmlspecialchars($partnerName) ?> 💖</h3>
                <p class="text-sm text-gray-700 leading-relaxed italic">"<?= htmlspecialchars($loveNoteText) ?>"</p>
              </div>

              <div class="bg-white rounded-2xl p-5 border border-gray-200 flex items-center justify-between gap-4 card-shadow">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 bg-red-700 rounded-xl flex items-center justify-center text-white shrink-0 shadow">
                    <span class="material-symbols-outlined text-3xl">card_giftcard</span>
                  </div>
                  <div>
                    <p class="font-bold text-base text-maroon">Amazon Gift Voucher</p>
                    <p class="text-xs text-gray-500 font-mono font-bold">Code: <span class="text-red-600"><?= htmlspecialchars($voucherCode) ?></span></p>
                  </div>
                </div>

                <button type="button" onclick="copyStitchVoucher()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase rounded-full shadow cursor-pointer">
                  Copy
                </button>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- RIGHT: ROYAL DECREE CERTIFICATE -->
      <div class="space-y-6 sm:space-y-8 bg-red-50/30 rounded-[32px] sm:rounded-[40px] p-6 sm:p-8 lg:p-12 relative overflow-hidden h-full border border-red-200 card-shadow flex flex-col justify-between text-center">
        <div class="space-y-3">
          <p class="text-xs font-sans font-bold tracking-widest text-red-500 uppercase">Royal Decree &amp; Seal</p>
          <h2 class="text-3xl sm:text-4xl font-serif font-bold text-maroon flex items-center justify-center gap-3">
            Shahi Tamrapatra 💖
          </h2>
          <p class="text-sm sm:text-base text-gray-600 leading-relaxed max-w-md mx-auto">
            Official Sibling Bond Certificate — Sealed with sacred Rakhi threads &amp; lifelong promises!
          </p>
        </div>

        <div class="rounded-2xl overflow-hidden shadow-lg border-4 border-white mx-auto w-full max-w-lg my-4">
          <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCXx9bPTk2ROO2lmeE736Caolpu-RJSi9lBODWkPogwGZjVCi1nvpsItfKzbUH-8DqK4MC_U2KHjT8JZ-jDhVun3ZJHCNW7_DeCLt8d-tRtGjpMbJzUqQqSlPQmP1ReMhxXCLna-A442FRqLgxGQkYAv18AtF2UXXjUfZs97LciRAqAKI3anS5UlA36z8F0YilwnQXNAlsyY4e-FcmnGwkJhEWFWdceTJ70H6T6YeKm9yYybbPGzTRMYO8ZfaoqTbUddQ" alt="Shahi Tamrapatra Certificate" class="w-full h-auto object-cover">
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4 w-full max-w-md mx-auto">
          <button type="button" onclick="downloadTamrapatraCertificate()" class="btn-gradient text-white font-bold py-3 px-8 rounded-full shadow-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2 flex-1 cursor-pointer">
            <span class="material-symbols-outlined text-base">download</span>
            <span>Download Certificate</span>
          </button>
          <button type="button" onclick="shareStitchWhatsApp()" class="border-2 border-red-500 text-red-500 bg-white font-bold py-3 px-8 rounded-full shadow-sm hover:bg-red-50 transition-colors flex items-center justify-center gap-2 flex-1 cursor-pointer">
            <span class="material-symbols-outlined text-base">share</span>
            <span>WhatsApp</span>
          </button>
        </div>
      </div>
    </section>

    <!-- 5. KEEPSAKES & 3D ALBUM SCROLL SECTION -->
    <section id="keepsakes" class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
      
      <!-- PRINTABLE KEEPSAKES -->
      <div class="space-y-6 sm:space-y-8">
        <div class="space-y-3">
          <div class="inline-block px-4 sm:px-5 py-1.5 rounded-full border border-red-200 text-red-500 text-[11px] sm:text-xs font-bold tracking-widest uppercase bg-red-50/50">
            👑 PHYSICAL • PRINTABLE KEEPSAKES
          </div>
          <h2 class="text-2xl sm:text-3xl md:text-4xl font-serif font-bold text-maroon leading-tight break-words">
            Printable Memory Keepsakes 🖼️ 📖
          </h2>
          <p class="text-gray-600 text-sm sm:text-base leading-relaxed max-w-lg">
            Turn your digital memories into 300 DPI high-definition physical treasures to print, frame, or bind!
          </p>
        </div>

        <div class="space-y-6">
          <div class="bg-white rounded-3xl p-6 sm:p-8 card-shadow border border-maroon/5 space-y-6">
            <div class="flex justify-between items-start">
              <span class="px-4 py-1.5 rounded-full border border-red-200 text-red-500 text-xs font-bold uppercase">
                FRAME READY (A4/A3)
              </span>
              <span class="material-symbols-outlined text-red-500 opacity-50 text-2xl">image</span>
            </div>
            <div class="space-y-2">
              <h3 class="text-xl sm:text-2xl font-serif font-bold text-maroon">Wall Collage Poster</h3>
              <p class="text-gray-500 text-sm leading-relaxed">
                A luxury 300 DPI wall-frame keepsake featuring <?= htmlspecialchars($partnerName) ?>'s portrait in a 24K gold locket, surrounded by an uncropped memory mosaic.
              </p>
            </div>
            <button type="button" onclick="downloadWallKeepsakePoster()" class="w-full sm:w-auto py-3 px-8 rounded-full btn-gradient text-white font-bold shadow-lg hover:opacity-90 flex items-center justify-center gap-2 cursor-pointer text-xs sm:text-sm">
              <span class="material-symbols-outlined text-sm">download</span>
              <span>DOWNLOAD WALL POSTER (300 DPI)</span>
            </button>
          </div>

          <div class="bg-white rounded-3xl p-6 sm:p-8 card-shadow border border-maroon/5 space-y-6">
            <div class="flex justify-between items-start">
              <span class="px-4 py-1.5 rounded-full border border-emerald-500/50 text-emerald-500 text-xs font-bold uppercase">
                MULTI-PAGE ALBUM (PDF)
              </span>
              <span class="material-symbols-outlined text-emerald-500 opacity-50 text-2xl">menu_book</span>
            </div>
            <div class="space-y-2">
              <h3 class="text-xl sm:text-2xl font-serif font-bold text-maroon">Sibling Keepsake Book</h3>
              <p class="text-gray-500 text-sm leading-relaxed">
                A luxury 7-page printable storybook album with Royal Cover, Shahi Tamrapatra Certificate, chapter stories, and a dynamic QR code to re-live this website anytime!
              </p>
            </div>
            <button type="button" onclick="downloadSiblingPhotobookPDF()" class="w-full sm:w-auto py-3 px-8 rounded-full bg-emerald-500 text-white font-bold shadow-lg hover:opacity-90 flex items-center justify-center gap-2 cursor-pointer text-xs sm:text-sm">
              <span class="material-symbols-outlined text-sm">menu_book</span>
              <span>DOWNLOAD KEEPSAKE BOOK (PDF)</span>
            </button>
          </div>
        </div>
      </div>

      <!-- ROYAL MEMORY SCROLL / 3D VIRTUAL ALBUM LAUNCHER -->
      <div class="bg-red-50/30 rounded-[32px] sm:rounded-[40px] p-6 sm:p-12 lg:p-16 text-center space-y-8 card-shadow relative overflow-hidden border border-red-200 h-full flex flex-col justify-center">
        <div class="relative z-10 space-y-4">
          <p class="text-xs font-bold tracking-[0.2em] text-yellow-600 uppercase">Royal Memory Scroll</p>
          <h2 class="text-2xl sm:text-4xl md:text-5xl font-serif font-bold flex items-center justify-center gap-3 text-maroon">
            <span>Our Cherished Moments</span>
            <span class="text-yellow-500 text-3xl">✨</span>
          </h2>
          <p class="text-sm sm:text-base leading-relaxed max-w-md mx-auto text-gray-600">
            Every memory photo uploaded by <?= htmlspecialchars($buyerName) ?> framed inside the unrolling antique parchment scroll.
          </p>
        </div>

        <button type="button" onclick="openStitchVirtualAlbumModal()" class="relative z-10 w-max mx-auto py-4 px-10 rounded-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-bold shadow-lg hover:opacity-90 flex items-center justify-center gap-2 transition-all hover:scale-105 cursor-pointer">
          <span>OPEN 3D VIRTUAL ALBUM</span>
        </button>
      </div>

    </section>

    <!-- 6. CHILDHOOD SCRAPBOOK MOSAIC WITH EXACT STAGGERED STITCH LAYOUT -->
    <section id="scrapbook" class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-12 space-y-8 sm:space-y-12">
      <div class="text-center space-y-4">
        <h2 class="text-2xl sm:text-4xl font-serif font-bold text-maroon flex items-center justify-center gap-3">
          <svg class="text-red-500 shrink-0" fill="none" height="32" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="32"><rect height="18" rx="2" ry="2" width="18" x="3" y="3"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
          <span>Our Childhood Scrapbook</span>
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto leading-relaxed text-sm sm:text-base">
          A curated archive of laughter, scraped knees, awkward haircuts, and pure joy. These captured fragments define the geography of our shared history.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        <?php 
        $defaultCaptions = ['Giggles & Mud Pies', 'Always By My Side', 'Little Hands, Big Bond', 'Growing Up Together', 'First Rakhi Memories', 'Partners in Crime'];
        $staggerClasses = ['', 'lg:mt-12', '', 'lg:-mt-10', '', 'lg:mt-12'];
        $stitchDefaultImgs = [
          'https://lh3.googleusercontent.com/aida-public/AB6AXuCNOrtcvsfdswgVbFaQS_7cryDev0cl9Ms28q69BN-fYTJtrIKW-duaIiRqkhZeNgFLwPjArYOA9IyJH-20R3E6CiW5eJ8LySG0-5YC6ZfBXDtGtlno0imtOhMn_cAtUnjBeijQjWzu8JCP1KSQITYmi06m4mOy_4_smVTQKVSwOV6X2qG7yH-00YtfrKS9kfmFSj6eivLno1QSmAuXM3FoKoACiX-4ImvGIjO5kupb_GNkALFv-_Bx',
          'https://lh3.googleusercontent.com/aida-public/AB6AXuB1W4cvzWixPF82Kr200o6tTq57eeSTvqsYQwGo0xV84XECz2DX2VGlSMzOoqobrtS2N35WIxSLvDIBZwOV7sJDy-Lj8FDwsitpCd6uUDSgGoLBMhV4Xgx385JZ2E-byxe1Y6XGX2iOoXPNhBWSlyAHrCEGGdYp21_z3_vxYjHEeHmw-4uOxpp-S1iY3JtrN9flbmXlVvdsY02hdwZDrsH9jfSlFW5ctcNzKsczrByXWHE3LCX4n8sv',
          'https://lh3.googleusercontent.com/aida-public/AB6AXuA-rxUuandJ-yKh0-NBSCcMQevrXjQdVNPHJ3Tvisal85dWtEIZj2TGWKUJjZW53DNMOzz2Eak4SDdFwZklDkd5zRAsNpqtUCp2CD5vmTWw1jofN7korPpJKNuwVTRkZhWcDed_fQn5gJRwQ4NHKvGvYPOo6TSva33ZtWkQpICoBlYl7Q7_IJlR5PALgpVJPlXgXnUPp7BqhZubH7hNphTv3gsgTmu8TBC4Ewr1teO3iftmu2b-jz1x',
          'https://lh3.googleusercontent.com/aida-public/AB6AXuCybPClPeAzCOSpUDcwR1y3MV-AFU70lwhIuE6X5EO8blYXoi8UECBQgFQRQed_Uj7XyBRF9Ns042vS1a2mplXYGwbkRam4lIjXkoLAGD5nYjA8d70kdmNQiTezC_4lrVvWZ5nE6VzGzKURzRMyNTbCMH_fq9xA7oi4amTSrHK7QkgmUqSa6SftV6PcrMBkd1FheoypWSYqNV29_4hzkpeQmUEdoCoiFqzD5cegOx2a8-bbdPf2e6Aw',
          'https://lh3.googleusercontent.com/aida-public/AB6AXuCNOrtcvsfdswgVbFaQS_7cryDev0cl9Ms28q69BN-fYTJtrIKW-duaIiRqkhZeNgFLwPjArYOA9IyJH-20R3E6CiW5eJ8LySG0-5YC6ZfBXDtGtlno0imtOhMn_cAtUnjBeijQjWzu8JCP1KSQITYmi06m4mOy_4_smVTQKVSwOV6X2qG7yH-00YtfrKS9kfmFSj6eivLno1QSmAuXM3FoKoACiX-4ImvGIjO5kupb_GNkALFv-_Bx',
          'https://lh3.googleusercontent.com/aida-public/AB6AXuCybPClPeAzCOSpUDcwR1y3MV-AFU70lwhIuE6X5EO8blYXoi8UECBQgFQRQed_Uj7XyBRF9Ns042vS1a2mplXYGwbkRam4lIjXkoLAGD5nYjA8d70kdmNQiTezC_4lrVvWZ5nE6VzGzKURzRMyNTbCMH_fq9xA7oi4amTSrHK7QkgmUqSa6SftV6PcrMBkd1FheoypWSYqNV29_4hzkpeQmUEdoCoiFqzD5cegOx2a8-bbdPf2e6Aw'
        ];
        $totalPhotos = !empty($galleryMedia) ? max(6, count($galleryMedia)) : 6;
        for ($i = 0; $i < $totalPhotos; $i++):
          $imgSrc = !empty($galleryMedia[$i]) ? (is_array($galleryMedia[$i]) ? resolveMediaUrl($galleryMedia[$i]['file_path'] ?? '') : resolveMediaUrl($galleryMedia[$i])) : $stitchDefaultImgs[$i % 6];
          $caption = !empty($galleryMedia[$i]) && is_array($galleryMedia[$i]) && !empty($galleryMedia[$i]['caption']) ? $galleryMedia[$i]['caption'] : ($defaultCaptions[$i % 6]);
        ?>
          <div class="space-y-3 group cursor-pointer <?= $staggerClasses[$i % 6] ?>" onclick="openPhotoLightbox('<?= htmlspecialchars($imgSrc) ?>')">
            <div class="rounded-2xl overflow-hidden border-8 border-white shadow-md transform group-hover:scale-[1.02] transition-transform duration-300">
              <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Childhood Memory <?= $i+1 ?>" class="w-full h-64 object-cover">
            </div>
            <p class="text-sm text-gray-500 text-center font-medium italic"><?= htmlspecialchars($caption) ?></p>
          </div>
        <?php endfor; ?>
      </div>
    </section>

    <!-- 7. SIGN-OFF & BOTTOM BANNER -->
    <section class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-12 py-12 sm:py-16 text-center border-t border-maroon/10 space-y-6">
      <div class="space-y-3">
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-maroon">Always &amp; Forever, <?= htmlspecialchars($partnerName) ?></h2>
        <p class="text-base sm:text-lg text-gray-600">May our bond stay sweet, unbreakable, and full of giggles every day of the year.</p>
      </div>
      <p class="text-sm text-gray-400">Designed with ❤️ for the Best Sister</p>
    </section>

    <section class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-12 pb-8">
      <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-[32px] sm:rounded-[40px] p-8 sm:p-12 text-center space-y-6 card-shadow">
        <div class="space-y-4">
          <h2 class="text-xl sm:text-3xl md:text-4xl font-serif font-bold text-black uppercase tracking-widest leading-tight">
            MADE WITH ENDLESS LOVE BY <?= htmlspecialchars(strtoupper($buyerName)) ?> FOR <?= htmlspecialchars(strtoupper($partnerName)) ?>
          </h2>
          <p class="text-black/80 font-medium text-sm sm:text-lg">
            This page is a digital keepsake of our bond. Lock it to preserve these memories forever.
          </p>
          <div class="pt-2">
            <button onclick="relockGiftSession()" type="button" class="px-6 py-3 rounded-full bg-black text-white hover:bg-gray-900 text-sm font-bold inline-flex items-center gap-2 transition-all cursor-pointer shadow-lg hover:scale-105">
              <span class="material-symbols-outlined text-sm text-yellow-400">lock</span>
              <span>Lock Gift Page 🔒</span>
            </button>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <footer class="w-full bg-[#fcf6f0] border-t border-maroon/10 py-6">
    <div class="max-w-[1280px] mx-auto px-6 lg:px-12 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
      <p class="text-sm text-gray-500">© 2026 SoulScript Raksha Bandhan. Celebrating Bonds of Love.</p>
      <nav class="flex gap-6 text-sm text-gray-500 uppercase tracking-wider font-medium">
        <a href="<?php echo APP_URL; ?>/terms.php" class="hover:text-red-500 transition-colors">Terms</a>
        <a href="<?php echo APP_URL; ?>/privacy.php" class="hover:text-red-500 transition-colors">Privacy</a>
      </nav>
    </div>
  </footer>

  <!-- HTML5 Audio Element -->
  <audio id="stitchBgAudio" loop preload="auto">
    <source src="<?= htmlspecialchars($bgMusicUrl) ?>" type="audio/mpeg">
  </audio>

  <!-- MODALS -->
  <?php 
  require __DIR__ . '/components/festive_light/modal_tie_rakhi.php';
  require __DIR__ . '/components/festive_light/modal_3d_album.php';
  ?>

</div>

<!-- JAVASCRIPT ENGINE WITH FULL IMMEDIATE INITIALIZATION FOR AJAX REVEAL -->
<script>
let stitchAudioPlaying = false;

function toggleFestiveAudio() {
  const audio = document.getElementById('stitchBgAudio');
  const label = document.getElementById('festiveAudioLabel');
  if (!audio) return;

  if (stitchAudioPlaying) {
    audio.pause();
    stitchAudioPlaying = false;
    if (label) label.innerText = "Play Music";
  } else {
    audio.play().then(() => {
      stitchAudioPlaying = true;
      if (label) label.innerText = "Playing 🎵";
    }).catch(e => console.log("Audio play error:", e));
  }
}

function openStitchRakhiModal() {
  const container = document.getElementById('festiveRakhiModalContainer');
  if (container) {
    container.classList.remove('hidden');
    container.style.display = 'flex';
  }
}

function closeStitchRakhiModal() {
  const container = document.getElementById('festiveRakhiModalContainer');
  if (container) {
    container.classList.add('hidden');
    container.style.display = 'none';
  }
}
function closeFestiveRakhiModal() {
  closeStitchRakhiModal();
}

function openStitchVirtualAlbumModal() {
  const container = document.getElementById('festive3DAlbumModalContainer');
  if (container) {
    container.classList.remove('hidden');
    container.style.display = 'flex';
  }
}

function closeStitchVirtualAlbumModal() {
  const container = document.getElementById('festive3DAlbumModalContainer');
  if (container) {
    container.classList.add('hidden');
    container.style.display = 'none';
  }
}
function closeFestiveVirtualAlbumModal() {
  closeStitchVirtualAlbumModal();
}

function downloadTamrapatraCertificate() {
  const certUrl = 'https://lh3.googleusercontent.com/aida-public/AB6AXuCXx9bPTk2ROO2lmeE736Caolpu-RJSi9lBODWkPogwGZjVCi1nvpsItfKzbUH-8DqK4MC_U2KHjT8JZ-jDhVun3ZJHCNW7_DeCLt8d-tRtGjpMbJzUqQqSlPQmP1ReMhxXCLna-A442FRqLgxGQkYAv18AtF2UXXjUfZs97LciRAqAKI3anS5UlA36z8F0YilwnQXNAlsyY4e-FcmnGwkJhEWFWdceTJ70H6T6YeKm9yYybbPGzTRMYO8ZfaoqTbUddQ';
  const a = document.createElement('a');
  a.href = certUrl;
  a.download = `Shahi_Tamrapatra_Certificate_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $partnerName) ?>.png`;
  a.target = '_blank';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}

function shareStitchWhatsApp() {
  const text = `📜 *Official Shahi Tamrapatra — Sibling Bond Certificate* 👑\n\nThis Royal decree certifies the eternal bond of love and protection between *<?= addslashes($buyerName) ?>* and *<?= addslashes($partnerName) ?>*!\n\nView our official certificate on SoulScript: ${window.location.href}`;
  window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
}

function copyStitchVoucher() {
  const code = '<?= htmlspecialchars($voucherCode) ?>';
  navigator.clipboard.writeText(code);
  alert('Voucher Code Copied to Clipboard! 🎉');
}

// Immediate Self-Executing Scratch Card Canvas Engine
(function initStitchScratchEngine() {
  function setupCanvas() {
    const canvas = document.getElementById('stitchScratchCanvas');
    const container = document.getElementById('stitchScratchContainer');
    const prompt = document.getElementById('stitchScratchPrompt');

    if (!canvas || !container) return;

    const ctx = canvas.getContext('2d');
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    canvas.width = container.offsetWidth || 360;
    canvas.height = container.offsetHeight || 440;

    // Create Gold Foil Gradient
    const grad = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
    grad.addColorStop(0, '#eab308');
    grad.addColorStop(0.3, '#fde047');
    grad.addColorStop(0.6, '#facc15');
    grad.addColorStop(1, '#ca8a04');

    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Realistic Foil Sparkles / Texture
    ctx.fillStyle = 'rgba(255, 255, 255, 0.35)';
    for (let i = 0; i < 180; i++) {
      ctx.beginPath();
      ctx.arc(Math.random() * canvas.width, Math.random() * canvas.height, Math.random() * 3 + 1, 0, Math.PI * 2);
      ctx.fill();
    }

    function getCoords(e) {
      const rect = canvas.getBoundingClientRect();
      const clientX = e.clientX !== undefined ? e.clientX : (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
      const clientY = e.clientY !== undefined ? e.clientY : (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
      return {
        x: clientX - rect.left,
        y: clientY - rect.top
      };
    }

    function startScratch(e) {
      isDrawing = true;
      const coords = getCoords(e);
      lastX = coords.x;
      lastY = coords.y;

      // Realistic Coin-sized Scratch Point (Radius ~16px, Width 32px)
      ctx.globalCompositeOperation = 'destination-out';
      ctx.beginPath();
      ctx.arc(coords.x, coords.y, 16, 0, Math.PI * 2);
      ctx.fill();

      // Fade out the floating helper text on first scratch so underlying content is visible
      if (prompt) {
        prompt.style.transition = 'opacity 0.4s ease';
        prompt.style.opacity = '0';
      }
    }

    function scratch(e) {
      if (!isDrawing) return;
      if (e.cancelable && e.type === 'touchmove') e.preventDefault();

      const coords = getCoords(e);

      ctx.globalCompositeOperation = 'destination-out';
      ctx.lineWidth = 32;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';

      ctx.beginPath();
      ctx.moveTo(lastX, lastY);
      ctx.lineTo(coords.x, coords.y);
      ctx.stroke();

      lastX = coords.x;
      lastY = coords.y;
    }

    function stopScratch() {
      isDrawing = false;
    }

    canvas.addEventListener('mousedown', startScratch);
    canvas.addEventListener('touchstart', startScratch, {passive: false});
    window.addEventListener('mouseup', stopScratch);
    window.addEventListener('touchend', stopScratch);
    canvas.addEventListener('mousemove', scratch);
    canvas.addEventListener('touchmove', scratch, {passive: false});
  }

  setTimeout(setupCanvas, 100);
})();

// Countdown Timer Engine
(function initStitchTimer() {
  let remainingSecs = <?= $secondsRemaining ?>;
  const timerEl = document.getElementById('stitchTimerDisplay');
  if (timerEl && remainingSecs > 0) {
    function updateTimer() {
      if (remainingSecs <= 0) {
        timerEl.innerText = "UNLOCKED! Refreshing page...";
        location.reload();
        return;
      }
      const days = Math.floor(remainingSecs / (3600 * 24));
      const hours = Math.floor((remainingSecs % (3600 * 24)) / 3600);
      const mins = Math.floor((remainingSecs % 3600) / 60);
      const secs = remainingSecs % 60;
      timerEl.innerText = `${days}d ${hours}h ${mins}m ${secs}s`;
      remainingSecs--;
    }
    updateTimer();
    setInterval(updateTimer, 1000);
  }
})();
</script>
