<?php
/**
 * SoulScript - Raksha Bandhan Festive Light Theme (Stitch Edition)
 * File: templates/themes/raksha_bandhan_festive_light.php
 * Isolated component-driven architecture with zero side-effects on live production.
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../includes/media_helper.php';
    require_once __DIR__ . '/../../includes/voucher_helper.php';
}

// 1. Resolve DB Data safely
$partnerName = $content['partner_name'] ?? $initialLockData['partner_name'] ?? 'Sister';
$buyerName = $content['buyer_name'] ?? $initialLockData['buyer_name'] ?? 'Brother';
$taglineQuote = $content['tagline_quote'] ?? "World's Best Sister 👑";
$loveNoteText = $content['love_note_text'] ?? '';
$receiverPhoto = $content['receiver_photo'] ?? $initialLockData['receiver_photo'] ?? '';
$cleanReceiverPhoto = !empty($receiverPhoto) ? resolveMediaUrl($receiverPhoto) : APP_URL . '/assets/default_gallery/partner_avatar.png';

$promisesList = $tf['reasons'] ?? [];
$pageId = $initialLockData['page_id'] ?? $editPageData['page_id'] ?? 0;

// Resolve Audio Song
$bgMusicUrl = $content['bg_music_url'] ?? APP_URL . '/assets/audio/rakhi_theme.mp3';
$songTitle = $content['song_title'] ?? 'Phoolon Ka Taaron Ka';
$songArtist = $content['song_artist'] ?? 'Kishore Kumar';

// Resolve Voucher Status
if (!isset($rakhiVoucherStatus)) {
    $rakhiVoucherStatus = getRakhiVoucherUnlockStatus(null, $pageId);
}
if (!isset($rakhiAffiliateProducts)) {
    $rakhiAffiliateProducts = getAffiliateProducts();
}
?>

<!-- FESTIVE LIGHT THEME WRAPPER -->
<div id="festiveLightThemeWrapper" class="bg-[#fcf6f0] text-[#4a232f] font-sans min-h-screen relative overflow-x-hidden">

  <?php
  // Render Modular Sub-Components
  require __DIR__ . '/components/festive_light/1_header_hero.php';
  require __DIR__ . '/components/festive_light/2_gift_reveal_card.php';
  require __DIR__ . '/components/festive_light/3_shagun_letter.php';
  require __DIR__ . '/components/festive_light/4_sibling_promises.php';
  require __DIR__ . '/components/festive_light/5_scrapbook_gallery.php';
  require __DIR__ . '/components/festive_light/6_keepsakes_center.php';
  require __DIR__ . '/components/festive_light/modal_tie_rakhi.php';
  require __DIR__ . '/components/festive_light/modal_3d_album.php';
  ?>

  <!-- HTML5 Audio Element for Music Engine -->
  <audio id="festiveBgAudio" loop preload="auto">
    <source src="<?= htmlspecialchars($bgMusicUrl) ?>" type="audio/mpeg">
  </audio>

</div>

<!-- LIGHT THEME JAVASCRIPT ENGINE -->
<script>
let festiveModalCurrentStep = 1;
let festiveAudioPlaying = false;
let festiveMediaList = <?= json_encode(array_values(array_map(function($m) {
    return is_array($m) ? resolveMediaUrl($m['file_path'] ?? '') : resolveMediaUrl($m);
}, $galleryMedia ?? []))) ?>;
let festiveFlipbookCurrentIdx = 0;

// Audio Controls
function toggleAudioPlay() {
  const audio = document.getElementById('festiveBgAudio');
  const btnLabel = document.getElementById('musicBtnLabel');
  if (!audio) return;

  if (festiveAudioPlaying) {
    audio.pause();
    festiveAudioPlaying = false;
    if (btnLabel) btnLabel.innerText = "Play Music";
  } else {
    audio.play().then(() => {
      festiveAudioPlaying = true;
      if (btnLabel) btnLabel.innerText = "Playing 🎵";
    }).catch(e => console.log("Audio autoplay prevented:", e));
  }
}

// 5-Step Rakhi Modal Controls
function openFestiveRakhiModal() {
  const container = document.getElementById('festiveRakhiModalContainer');
  if (container) {
    container.classList.remove('hidden');
    navigateFestiveStep(0);
  }
}

function closeFestiveRakhiModal() {
  const container = document.getElementById('festiveRakhiModalContainer');
  if (container) container.classList.add('hidden');
}

function navigateFestiveStep(delta) {
  festiveModalCurrentStep = Math.max(1, Math.min(5, festiveModalCurrentStep + delta));
  
  for (let i = 1; i <= 5; i++) {
    const el = document.getElementById('rakhiStep' + i);
    if (el) el.classList.toggle('hidden', i !== festiveModalCurrentStep);
  }

  const badge = document.getElementById('festiveModalStepBadge');
  if (badge) badge.innerText = `Step ${festiveModalCurrentStep} of 5`;

  const backBtn = document.getElementById('festiveModalBackBtn');
  if (backBtn) backBtn.style.visibility = (festiveModalCurrentStep > 1) ? 'visible' : 'hidden';

  const nextBtn = document.getElementById('festiveModalNextBtn');
  if (nextBtn) {
    if (festiveModalCurrentStep === 5) {
      nextBtn.innerText = "Done ✓";
      nextBtn.onclick = closeFestiveRakhiModal;
    } else {
      nextBtn.innerText = "Next ➔";
      nextBtn.onclick = function() { navigateFestiveStep(1); };
    }
  }

  if (festiveModalCurrentStep === 3) {
    triggerConfettiBurst();
    document.getElementById('heroTilakBadge')?.classList.remove('hidden');
    document.getElementById('heroRakhiBadge')?.classList.remove('hidden');
  }
}

function selectRakhiOption(optId, btn) {
  document.querySelectorAll('.rakhi-opt-btn').forEach(b => {
    b.classList.remove('border-[#e5534b]');
    b.classList.add('border-transparent');
  });
  if (btn) {
    btn.classList.remove('border-transparent');
    btn.classList.add('border-[#e5534b]');
  }
}

function triggerRakhiTiedSuccess() {
  navigateFestiveStep(1);
}

function triggerConfettiBurst() {
  if (typeof confetti === 'function') {
    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
  }
}

// 3D Virtual Album Modal Controls
function openFestiveVirtualAlbumModal() {
  const container = document.getElementById('festive3DAlbumModalContainer');
  if (container) container.classList.remove('hidden');
}

function closeFestiveVirtualAlbumModal() {
  const container = document.getElementById('festive3DAlbumModalContainer');
  if (container) container.classList.add('hidden');
}

function navigateFlipbookPage(delta) {
  if (!festiveMediaList || festiveMediaList.length === 0) return;
  festiveFlipbookCurrentIdx = (festiveFlipbookCurrentIdx + delta + festiveMediaList.length) % festiveMediaList.length;
  
  const imgEl = document.getElementById('festiveFlipbookActiveImg');
  if (imgEl) imgEl.src = festiveMediaList[festiveFlipbookCurrentIdx];

  const indicator = document.getElementById('flipbookPageIndicator');
  if (indicator) indicator.innerText = `Page ${festiveFlipbookCurrentIdx + 1} of ${festiveMediaList.length}`;
}

// Copy Voucher Code Handler
function copyVoucherCode() {
  const input = document.getElementById('voucherCodeInput');
  if (input) {
    input.select();
    navigator.clipboard.writeText(input.value);
    alert('Voucher Code Copied to Clipboard! 🎉');
  }
}

// Share Page Handler
function shareFestivePage() {
  if (navigator.share) {
    navigator.share({
      title: 'Happy Raksha Bandhan!',
      text: 'Check out this secret Rakhi surprise page created for <?= htmlspecialchars($partnerName) ?>!',
      url: window.location.href
    }).catch(e => console.log(e));
  } else {
    navigator.clipboard.writeText(window.location.href);
    alert('Link Copied to Clipboard!');
  }
}

// Interactive Real-Time Touch/Mouse Scratch Card Engine (State 1 -> State 2)
document.addEventListener('DOMContentLoaded', function() {
  const canvas = document.getElementById('scratchCanvas');
  const overlay = document.getElementById('scratchCardOverlay');
  if (!canvas || !overlay) return;

  const ctx = canvas.getContext('2d');
  const width = overlay.clientWidth || 360;
  const height = overlay.clientHeight || 220;
  canvas.width = width;
  canvas.height = height;

  // Draw Luxury Silver Metallic Pattern
  const grad = ctx.createLinearGradient(0, 0, width, height);
  grad.addColorStop(0, '#d1d5db');
  grad.addColorStop(0.5, '#e5e7eb');
  grad.addColorStop(1, '#9ca3af');
  ctx.fillStyle = grad;
  ctx.fillRect(0, 0, width, height);

  ctx.fillStyle = '#4b5563';
  ctx.font = 'bold 14px sans-serif';
  ctx.textAlign = 'center';
  ctx.fillText('✨ Scratch Here to Reveal Gift ✨', width / 2, height / 2);

  let isScratching = false;

  function scratch(e) {
    if (!isScratching) return;
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX || e.touches[0].clientX) - rect.left;
    const y = (e.clientY || e.touches[0].clientY) - rect.top;

    ctx.globalCompositeOperation = 'destination-out';
    ctx.beginPath();
    ctx.arc(x, y, 22, 0, Math.PI * 2);
    ctx.fill();
  }

  canvas.addEventListener('mousedown', () => isScratching = true);
  canvas.addEventListener('mouseup', () => isScratching = false);
  canvas.addEventListener('mousemove', scratch);

  canvas.addEventListener('touchstart', (e) => { isScratching = true; scratch(e); }, {passive: true});
  canvas.addEventListener('touchend', () => isScratching = false);
  canvas.addEventListener('touchmove', scratch, {passive: true});

  // Countdown Timer Engine
  let remainingSeconds = <?= $secondsRemaining ?>;
  const timerEl = document.getElementById('giftCountdownTimer');

  if (timerEl && remainingSeconds > 0) {
    function updateCountdown() {
      if (remainingSeconds <= 0) {
        timerEl.innerText = "UNLOCKED! Refreshing page...";
        location.reload();
        return;
      }
      const days = Math.floor(remainingSeconds / (3600 * 24));
      const hours = Math.floor((remainingSeconds % (3600 * 24)) / 3600);
      const minutes = Math.floor((remainingSeconds % 3600) / 60);
      const secs = remainingSeconds % 60;
      
      timerEl.innerText = `${days}d ${hours}h ${minutes}m ${secs}s`;
      remainingSeconds--;
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
  }
});
</script>
