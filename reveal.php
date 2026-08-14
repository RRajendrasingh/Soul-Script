<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/media_helper.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) {
    $uriParts = explode('/gift/', $_SERVER['REQUEST_URI'] ?? '');
    if (isset($uriParts[1])) {
        $slug = explode('?', explode('/', $uriParts[1])[0])[0];
    }
}

if (!$slug) {
    header("Location: " . APP_URL);
    exit;
}

$editToken = trim($_GET['edit_token'] ?? $_GET['token'] ?? '');
$isEditMode = false;
$editPageData = null;

if ($editToken) {
    try {
        $db = getDB();
        $stmtToken = $db->prepare("
            SELECT p.page_id, p.template_id, p.url_slug, p.edit_token,
                   c.*, o.buyer_name as order_buyer_name, o.buyer_email
            FROM pages p
            JOIN page_content c ON p.page_id = c.page_id
            JOIN orders o ON p.order_id = o.order_id
            WHERE p.edit_token = ? AND LOWER(p.url_slug) = LOWER(?)
        ");
        $stmtToken->execute([$editToken, $slug]);
        $editPageData = $stmtToken->fetch();
        if ($editPageData) {
            $isEditMode = true;
        }
    } catch (Exception $e) {
        $isEditMode = false;
    }
}

$initialLockData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT p.page_id, p.template_id, p.url_slug, p.status, p.expires_at,
               c.partner_name, c.buyer_name, c.hint_question, c.receiver_photo
        FROM pages p
        JOIN page_content c ON p.page_id = c.page_id
        WHERE LOWER(p.url_slug) = LOWER(?)
    ");
    $stmt->execute([$slug]);
    $initialLockData = $stmt->fetch();
    if ($initialLockData && !empty($initialLockData['receiver_photo'])) {
        $initialLockData['receiver_photo'] = resolveMediaUrl($initialLockData['receiver_photo']);
    }
} catch (Exception $e) {
    $initialLockData = null;
}

require_once __DIR__ . '/includes/voucher_helper.php';

$rakhiVoucherStatus = null;
$rakhiAffiliateProducts = getAffiliateProducts();

if (!empty($initialLockData['page_id'])) {
    $rakhiVoucherStatus = getRakhiVoucherUnlockStatus(null, $initialLockData['page_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'A Secret Surprise For You ✨ — ' . APP_NAME;
  require_once __DIR__ . '/includes/head.php'; 
  ?>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <style>
  @keyframes spinVinyl {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .spin-vinyl {
    animation: spinVinyl 3s linear infinite;
  }
</style>

<!-- Navbar Header -->
<header id="revealHeader" class="fixed top-0 left-0 right-0 w-full z-40 bg-[#151215]/95 backdrop-blur-xl border-b border-[#4d444b]/30 shadow-md">
  <div class="max-w-[1200px] mx-auto px-3 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-2">
    <a href="<?php echo APP_URL; ?>" class="flex items-center gap-1.5 text-xs font-bold text-[#e8e0e3] hover:text-[#eac34a] transition-colors shrink-0">
      <svg class="w-4 h-4 text-[#eac34a] stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
      <span class="hidden min-[360px]:inline">SoulScript Home</span>
      <span class="inline min-[360px]:hidden">Home</span>
    </a>

    <div class="flex items-center gap-2 shrink-0">
      <!-- Mobile Compact Music Pill -->
      <button id="audioPlayBtnMobile" onclick="toggleAudioPlay()" class="px-2.5 py-1 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-[10px] uppercase flex items-center gap-1 shadow-md transition-all cursor-pointer">
        <svg class="w-3 h-3 text-[#241a00] stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        <span id="musicBtnLabel">Music</span>
      </button>

      <a href="<?php echo APP_URL; ?>/edit.php" class="px-2.5 sm:px-3.5 py-1.5 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider border border-[#eac34a]/60 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1">
        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3"/></svg>
        <span>Buyer Login</span>
      </a>
    </div>
  </div>
</header>

<!-- Floating Music Player Widget Box (Hidden on Lock Screen, shown only after unlock) -->
<div id="desktopMusicBox" class="hidden fixed bottom-6 right-6 z-50 bg-[#221f21]/95 backdrop-blur-xl border border-[#eac34a]/40 rounded-2xl p-3 items-center gap-3.5 shadow-[0_10px_30px_rgba(0,0,0,0.85)] max-w-xs sm:max-w-sm">
  <audio id="bgAudio" src="https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3" loop preload="none"></audio>
  
  <!-- Left: Partner Thumbnail Photo -->
  <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] shrink-0 shadow-md">
    <div class="w-full h-full bg-[#151215] rounded-[10px] overflow-hidden flex items-center justify-center" id="playerAvatarContainer">
      <?php if (!empty($initialLockData['receiver_photo'])): ?>
        <img id="playerReceiverPhotoImg" src="<?php echo htmlspecialchars($initialLockData['receiver_photo']); ?>" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';" alt="Partner Photo" class="w-full h-full object-cover rounded-[10px]">
        <span id="playerReceiverFallback" class="text-base font-bold font-serif text-[#eac34a] hidden"><?php echo htmlspecialchars(strtoupper(substr($initialLockData['partner_name'] ?? 'P', 0, 1))); ?></span>
      <?php else: ?>
        <span id="playerReceiverFallback" class="text-base font-bold font-serif text-[#eac34a]"><?php echo htmlspecialchars(strtoupper(substr($initialLockData['partner_name'] ?? 'P', 0, 1))); ?></span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Center: Song Title, Artist & Volume Slider (Pre-set to 50%) -->
  <div class="flex-1 min-w-0 space-y-1">
    <div>
      <span class="block font-bold text-xs text-[#e8e0e3] truncate leading-tight" id="musicBoxTitle">Tum Hi Ho</span>
      <span class="block text-[10px] text-[#eac34a] truncate mt-0.5" id="musicBoxArtist">Arijit Singh</span>
    </div>

    <div class="flex items-center gap-1.5 pt-0.5">
      <i data-lucide="volume-1" class="w-3 h-3 text-[#d0c3cb] shrink-0"></i>
      <input type="range" id="volumeSlider" min="0" max="1" step="0.01" value="0.5" oninput="changeAudioVolume(this.value)" class="w-full h-1 bg-[#4d444b] rounded-lg appearance-none cursor-pointer accent-[#eac34a]">
    </div>
  </div>

  <!-- Right: Circular Play/Pause Button -->
  <button id="audioPlayBtn" onclick="toggleAudioPlay()" aria-label="Play Pause Music" class="w-10 h-10 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] flex items-center justify-center shadow-lg transition-all shrink-0 cursor-pointer">
    <svg class="w-4 h-4 fill-[#241a00] ml-0.5" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
  </button>
</div>

  <!-- GLOBAL ROYAL MANDALA & SPARKLE DUST BACKGROUND LAYER -->
  <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
    <!-- Ambient Radial Glows -->
    <div class="absolute top-[-10%] left-[20%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[150px]"></div>
    <div class="absolute bottom-[10%] right-[10%] w-[45vw] h-[45vw] rounded-full bg-[#eac34a]/15 blur-[130px]"></div>

    <!-- Top-Left Mandala -->
    <div class="absolute -top-16 -left-16 w-64 sm:w-88 h-64 sm:h-88 opacity-35 mix-blend-screen">
      <img src="<?php echo APP_URL; ?>/assets/images/gold_mandala_corner.svg" class="w-full h-full animate-spin-slow">
    </div>

    <!-- Top-Right Mandala -->
    <div class="absolute -top-16 -right-16 w-64 sm:w-88 h-64 sm:h-88 opacity-35 mix-blend-screen">
      <img src="<?php echo APP_URL; ?>/assets/images/gold_mandala_corner.svg" class="w-full h-full rotate-90 animate-spin-slow">
    </div>

    <!-- Middle-Left Side Mandala -->
    <div class="absolute top-1/3 -left-32 w-[400px] sm:w-[500px] opacity-20 mix-blend-screen">
      <img src="<?php echo APP_URL; ?>/assets/images/gold_mandala_side.svg" class="w-full h-full">
    </div>

    <!-- Middle-Right Side Mandala -->
    <div class="absolute top-2/3 -right-32 w-[400px] sm:w-[500px] opacity-20 mix-blend-screen">
      <img src="<?php echo APP_URL; ?>/assets/images/gold_mandala_side.svg" class="w-full h-full">
    </div>

    <!-- Floating Golden Twinkling Sparkle Dust Particles -->
    <div class="absolute top-16 left-1/4 text-[#eac34a] text-xs sm:text-sm animate-ping opacity-60">✨</div>
    <div class="absolute top-40 right-1/3 text-[#ffd700] text-sm sm:text-base animate-pulse opacity-70">✦</div>
    <div class="absolute top-1/2 left-8 text-[#eac34a] text-lg animate-pulse opacity-50">✨</div>
    <div class="absolute top-2/3 right-10 text-[#ffd700] text-xs sm:text-sm animate-ping opacity-60">✧</div>
    <div class="absolute bottom-36 left-1/3 text-[#eac34a] text-sm animate-pulse opacity-70">✦</div>
    <div class="absolute bottom-16 right-1/4 text-[#ffd700] text-base animate-ping opacity-60">✨</div>
  </div>

  <!-- STEP 7: LOCK SCREEN (Exact LockScreen.tsx DOM Layout) -->
  <main id="lockScreenView" class="w-full flex flex-col items-center justify-center p-4 <?php echo $isEditMode ? 'pt-28' : 'pt-16'; ?> pb-16 relative z-10">
    <div class="max-w-md w-full bg-[#221f21]/90 border border-[#eac34a]/30 backdrop-blur-xl rounded-3xl p-6 sm:p-8 space-y-6 shadow-[0_20px_50px_rgba(0,0,0,0.8)] relative my-8">
      
      <!-- Header Icon / Receiver Avatar -->
      <div class="text-center space-y-3">
        <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[2px] mx-auto shadow-[0_0_25px_rgba(234,195,74,0.4)]">
          <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center overflow-hidden" id="lockAvatarContainer">
            <?php if (!empty($initialLockData['receiver_photo'])): ?>
              <img id="lockReceiverPhotoImg" src="<?php echo htmlspecialchars($initialLockData['receiver_photo']); ?>" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';" alt="Receiver Photo" class="w-full h-full object-cover rounded-full">
              <span id="lockReceiverFallback" class="text-2xl font-bold font-serif text-[#eac34a] hidden"><?php echo htmlspecialchars(strtoupper(substr($initialLockData['partner_name'] ?? 'P', 0, 1))); ?></span>
            <?php else: ?>
              <span id="lockReceiverFallback" class="text-2xl font-bold font-serif text-[#eac34a]"><?php echo htmlspecialchars(strtoupper(substr($initialLockData['partner_name'] ?? 'P', 0, 1))); ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div>
          <span class="text-[10px] uppercase tracking-[0.2em] text-[#eac34a] font-bold bg-[#3b1e3b] border border-[#e4b9df]/20 px-3.5 py-1 rounded-full">
            Private Surprise Reveal
          </span>
          <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3] mt-3" id="lockPartnerNameHeader">
            For <span id="lockPartnerName"><?php echo htmlspecialchars($initialLockData['partner_name'] ?? 'My Love'); ?></span> ❤️
          </h2>
          <p class="text-xs text-[#d0c3cb] mt-1 font-normal">
            A private gift prepared with love by <span class="text-[#eac34a] font-semibold" id="lockBuyerName"><?php echo htmlspecialchars($initialLockData['buyer_name'] ?? 'Someone Special'); ?></span>.
          </p>
        </div>
      </div>

      <!-- Hint Question Box -->
      <div class="bg-[#151215] p-5 rounded-2xl border border-[#4d444b] space-y-2 text-center">
        <div class="flex items-center justify-center gap-1.5 text-xs font-semibold text-[#eac34a] uppercase tracking-widest">
          <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
          <span>Hint Question</span>
        </div>
        <p class="text-sm sm:text-base text-[#e8e0e3] font-serif leading-relaxed italic" id="lockHintQuestion">
          "<?php echo htmlspecialchars(htmlspecialchars_decode($initialLockData['hint_question'] ?? 'Where did we take our very first trip together in 2022?', ENT_QUOTES), ENT_QUOTES, 'UTF-8'); ?>"
        </p>
      </div>

      <!-- Hint Form -->
      <form id="verifyForm" onsubmit="handleVerifySubmit(event); return false;" class="space-y-4 text-xs">
        <div id="lockMessage" class="hidden bg-[#3b1e3b] border border-[#e4b9df]/40 text-[#e4b9df] p-3.5 rounded-xl font-medium text-center text-xs"></div>

        <div>
          <input type="text" id="answerInput" class="w-full bg-[#151215] border border-[#4d444b] focus:border-[#eac34a] focus:ring-1 focus:ring-[#eac34a] rounded-xl px-3 sm:px-4 py-3.5 text-xs sm:text-sm text-[#e8e0e3] placeholder-[#d0c3cb]/50 placeholder:text-[10px] sm:placeholder:text-xs placeholder:font-sans placeholder:normal-case placeholder:tracking-normal text-center font-bold tracking-wider uppercase transition-all" placeholder="Enter Secret Password / Hint Answer" required autocomplete="off">
        </div>

        <button type="submit" id="unlockBtn" class="w-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-sans font-bold text-xs uppercase tracking-[0.2em] py-4 rounded-xl shadow-[0_0_20px_rgba(234,195,74,0.3)] transition-all flex items-center justify-center gap-2 cursor-pointer">
          <i data-lucide="lock" class="w-4 h-4"></i>
          <span>Unlock Surprise Page</span>
        </button>
      </form>

      <!-- Partner Recovery Hint -->
      <div class="pt-4 border-t border-[#4d444b]/30 text-center">
        <p class="text-[11px] text-[#d0c3cb]/60 italic" id="askBuyerNotice">
          Forgot the answer? Ask <span id="askBuyerName"><?php echo htmlspecialchars($initialLockData['buyer_name'] ?? 'your partner'); ?></span> for a clue.
        </p>
      </div>
    </div>
  </main>


  <!-- STEP 8: RESULT PAGE VIEW -->
  <main id="resultPageView" class="hidden min-h-screen pb-24 relative z-10">
    <div id="resultContentContainer"></div>
  </main>


  <script>
    const APP_URL = '<?php echo APP_URL; ?>';
    const currentSlug = '<?php echo htmlspecialchars($slug); ?>';
    const isEditMode = <?php echo $isEditMode ? 'true' : 'false'; ?>;
    const activeEditToken = '<?php echo htmlspecialchars($editToken); ?>';
    let lockData = null;

    let isPlaying = false;
    let isMuted = false;

    function normalizeMediaUrlJs(url) {
      if (!url) return '';
      if (url.startsWith('data:image')) return url;

      const uploadIdx = url.indexOf('uploads/');
      if (uploadIdx !== -1) {
        return '<?php echo APP_URL; ?>/' + url.substring(uploadIdx);
      }
      if (url.startsWith('http://') || url.startsWith('https://')) {
        return url;
      }
      return '<?php echo APP_URL; ?>/' + url.replace(/^\/+/, '');
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function extractYouTubeId(url) {
      if (!url || typeof url !== 'string') return null;
      const cleanUrl = url.trim();
      let match = cleanUrl.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/);
      if (match && match[1]) return match[1];
      const fallbackMatch = cleanUrl.match(/([a-zA-Z0-9_-]{11})/);
      return (fallbackMatch && cleanUrl.includes('youtu')) ? fallbackMatch[1] : null;
    }

    const PLAY_SVG = '<svg class="w-4 h-4 fill-[#241a00] ml-0.5" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>';
    const PAUSE_SVG = '<svg class="w-4 h-4 fill-[#241a00]" viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>';

    function syncMusicBtnLabel() {
      const lbl = document.getElementById('musicBtnLabel');
      if (lbl) lbl.textContent = isPlaying ? '⏸ Pause' : '▶ Music';
    }

    function showSongBadge(songTitle, artist, audioUrl, ytVideoId) {
      // Find the result content container and inject the badge at top
      const container = document.getElementById('resultContentContainer');
      if (!container) return;

      // Remove existing badge if any
      const old = document.getElementById('songForYouBadge');
      if (old) old.remove();

      const badge = document.createElement('div');
      badge.id = 'songForYouBadge';
      badge.className = 'relative z-10 mx-4 mt-6 mb-2';
      badge.innerHTML = `
        <div id="songBadgeInner" onclick="playSongFromBadge('${audioUrl || ''}', '${ytVideoId || ''}', '${(songTitle || '').replace(/'/g, '')}', '${(artist || '').replace(/'/g, '')}')"
             class="cursor-pointer group flex items-center gap-4 bg-gradient-to-r from-[#3b1e3b] to-[#221f21] border border-[#eac34a]/40 rounded-2xl p-4 shadow-[0_0_30px_rgba(234,195,74,0.15)] hover:border-[#eac34a]/80 hover:shadow-[0_0_40px_rgba(234,195,74,0.3)] transition-all duration-300 select-none">
          <!-- Animated Music Icon -->
          <div class="w-12 h-12 rounded-xl bg-[#eac34a] flex items-center justify-center shadow-lg shrink-0 group-hover:scale-110 transition-transform duration-300">
            <svg id="songBadgeIcon" class="w-6 h-6 fill-[#241a00] ml-0.5" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
          </div>
          <!-- Song Info -->
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#eac34a] mb-0.5">🎵 A Song Just For You</p>
            <p id="songBadgeTitle" class="text-sm font-bold text-[#e8e0e3] truncate">${songTitle || 'Romantic Track'}</p>
            <p id="songBadgeArtist" class="text-[10px] text-[#d0c3cb] truncate">${artist || 'Special for you'}</p>
          </div>
          <!-- Tap Hint -->
          <div id="songBadgeTapHint" class="text-[9px] text-[#eac34a]/70 font-bold uppercase tracking-wider shrink-0 text-right">
            Tap to<br>Play ▶
          </div>
        </div>
      `;
      // Insert at the very beginning of result content
      container.insertBefore(badge, container.firstChild);
    }

    function playSongFromBadge(audioUrl, ytVideoId, songTitle, artist) {
      // Update badge to playing state
      const tapHint = document.getElementById('songBadgeTapHint');
      const badgeIcon = document.getElementById('songBadgeIcon');
      const badgeInner = document.getElementById('songBadgeInner');

      if (ytVideoId) {
        // YouTube audio
        let ytContainer = document.getElementById('ytAudioIframeContainer');
        if (!ytContainer) {
          ytContainer = document.createElement('div');
          ytContainer.id = 'ytAudioIframeContainer';
          ytContainer.className = 'fixed top-0 left-0 w-1 h-1 opacity-0 pointer-events-none overflow-hidden';
          document.body.appendChild(ytContainer);
        }
        ytContainer.innerHTML = `<iframe id="ytAudioIframe" width="100" height="100" src="https://www.youtube.com/embed/${ytVideoId}?enablejsapi=1&autoplay=1&loop=1&playlist=${ytVideoId}" allow="autoplay"></iframe>`;
        isPlaying = true;
      } else {
        // Regular audio
        const audio = document.getElementById('bgAudio');
        if (audio) {
          audio.play().then(() => {
            isPlaying = true;
            const btn = document.getElementById('audioPlayBtn');
            if (btn) btn.innerHTML = PAUSE_SVG;
            syncMusicBtnLabel();
          }).catch(e => console.log('Play error:', e));
        }
      }

      // Update badge UI to show playing state
      if (badgeIcon) badgeIcon.innerHTML = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
      if (tapHint) tapHint.innerHTML = 'Now<br>Playing ♪';
      if (badgeInner) {
        badgeInner.onclick = () => toggleAudioPlay();
        badgeInner.classList.add('border-[#eac34a]/80');
      }

      // Show floating music box
      const musicBox = document.getElementById('desktopMusicBox');
      if (musicBox) {
        musicBox.classList.remove('hidden');
        musicBox.classList.add('flex');
      }

      // Update mobile top bar button
      isPlaying = true;
      syncMusicBtnLabel();
    }

    function toggleAudioPlay() {
      const audio = document.getElementById('bgAudio');
      const btn = document.getElementById('audioPlayBtn');
      const ytIframe = document.getElementById('ytAudioIframe');
      const badgeIcon = document.getElementById('songBadgeIcon');
      const tapHint = document.getElementById('songBadgeTapHint');

      if (ytIframe) {
        if (isPlaying) {
          ytIframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
          isPlaying = false;
          if (btn) btn.innerHTML = PLAY_SVG;
          if (badgeIcon) badgeIcon.innerHTML = '<polygon points="5 3 19 12 5 21 5 3"></polygon>';
          if (tapHint) tapHint.innerHTML = 'Tap to<br>Play ▶';
        } else {
          ytIframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
          isPlaying = true;
          if (btn) btn.innerHTML = PAUSE_SVG;
          if (badgeIcon) badgeIcon.innerHTML = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
          if (tapHint) tapHint.innerHTML = 'Now<br>Playing ♪';
        }
      } else if (audio) {
        if (isPlaying) {
          audio.pause();
          isPlaying = false;
          if (btn) btn.innerHTML = PLAY_SVG;
          if (badgeIcon) badgeIcon.innerHTML = '<polygon points="5 3 19 12 5 21 5 3"></polygon>';
          if (tapHint) tapHint.innerHTML = 'Tap to<br>Play ▶';
        } else {
          audio.play().then(() => {
            isPlaying = true;
            if (btn) btn.innerHTML = PAUSE_SVG;
            if (badgeIcon) badgeIcon.innerHTML = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
            if (tapHint) tapHint.innerHTML = 'Now<br>Playing ♪';
          }).catch(err => console.log('Audio playback allowed on interaction:', err));
        }
      }
      syncMusicBtnLabel();
    }

    function changeAudioVolume(val) {
      const audio = document.getElementById('bgAudio');
      if (audio) {
        audio.volume = parseFloat(val);
      }
    }

    function relockGiftSession() {
      const audio = document.getElementById('bgAudio');
      if (audio) {
        audio.pause();
        audio.currentTime = 0;
      }
      const btn = document.getElementById('audioPlayBtn');
      if (btn) btn.innerHTML = PLAY_SVG;
      isPlaying = false;
      syncMusicBtnLabel();
      const musicBox = document.getElementById('desktopMusicBox');
      if (musicBox) {
        musicBox.classList.add('hidden');
        musicBox.classList.remove('flex');
      }
      document.getElementById('resultPageView')?.classList.add('hidden');
      document.getElementById('lockScreenView')?.classList.remove('hidden');
      if (document.getElementById('answerInput')) {
        document.getElementById('answerInput').value = '';
      }
      const msg = document.getElementById('lockMessage');
      if (msg) msg.classList.add('hidden');
      loadLockMetadata();
      window.scrollTo(0, 0);
    }

    async function loadLockMetadata() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('theme') || urlParams.get('preview')) {
        try {
          const vRes = await fetch('<?php echo APP_URL; ?>/api/verify_hint.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ slug: currentSlug, answer: 'preview', preview_mode: '1' })
          });
          const vData = await vRes.json();
          if (vData.success) {
            document.getElementById('lockScreenView').classList.add('hidden');
            document.getElementById('resultPageView').classList.remove('hidden');
            renderResultPage(vData);
            return;
          }
        } catch (vErr) { /* ignore */ }
      }

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/get_page_lock.php?slug=' + encodeURIComponent(currentSlug));
        const data = await res.json();

        if (data.success) {
          lockData = data;
          document.getElementById('lockPartnerName').innerText = data.partner_name;
          document.getElementById('lockBuyerName').innerText = data.buyer_name;
          document.getElementById('askBuyerName').innerText = data.buyer_name;
          document.getElementById('lockHintQuestion').innerText = `"${data.hint_question}"`;

          const pName = data.partner_name || 'Partner';
          const pInitial = pName.charAt(0).toUpperCase();
          const avatarContainer = document.getElementById('lockAvatarContainer');
          if (avatarContainer) {
            const cleanPhoto = data.receiver_photo ? normalizeMediaUrlJs(data.receiver_photo) : '';
            if (cleanPhoto && cleanPhoto.trim() !== '') {
              avatarContainer.innerHTML = `<img id="lockReceiverPhotoImg" src="${cleanPhoto}" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\\'text-2xl font-bold font-serif text-[#eac34a]\\'>${pInitial}</span>';" alt="Receiver Photo" class="w-full h-full object-cover rounded-full">`;
            } else {
              avatarContainer.innerHTML = `<span id="lockReceiverFallback" class="text-2xl font-bold font-serif text-[#eac34a]">${pInitial}</span>`;
            }
          }

          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.get('theme') || urlParams.get('preview')) {
            try {
              const vRes = await fetch('<?php echo APP_URL; ?>/api/verify_hint.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ slug: currentSlug, answer: 'preview', preview_mode: '1' })
              });
              const vData = await vRes.json();
              if (vData.status === 'success' || vData.success) {
                document.getElementById('lockScreenView').classList.add('hidden');
                document.getElementById('resultPageView').classList.remove('hidden');
                renderResultPage(vData);
                return;
              }
            } catch (vErr) { /* ignore */ }
          }

          if (data.is_locked) {
            triggerCooldown(data.locked_until_seconds);
          }
        } else {
          document.getElementById('lockScreenView').innerHTML = `
            <div class="max-w-md w-full bg-[#221f21] border border-[#4d444b] p-8 rounded-3xl space-y-4 text-center">
              <i data-lucide="shield-alert" class="w-10 h-10 text-[#eac34a] mx-auto"></i>
              <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Page Not Found or Expired</h3>
              <p class="text-xs text-[#d0c3cb]">${data.message || 'This surprise page URL is either incorrect or has expired.'}</p>
              <a href="<?php echo APP_URL; ?>" class="inline-block px-6 py-3 rounded-full bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase">Go to SoulScript Home</a>
            </div>
          `;
          lucide.createIcons();
        }
      } catch (err) {
        document.getElementById('lockScreenView').innerHTML = `
          <div class="max-w-md w-full bg-[#221f21] border border-[#4d444b] p-8 rounded-3xl space-y-4 text-center">
            <i data-lucide="shield-alert" class="w-10 h-10 text-[#eac34a] mx-auto"></i>
            <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Page Not Found or Expired</h3>
            <p class="text-xs text-[#d0c3cb]">This surprise page link is either invalid or expired (${err.message}).</p>
            <a href="<?php echo APP_URL; ?>" class="inline-block px-6 py-3 rounded-full bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase">Go to SoulScript Home</a>
          </div>
        `;
        lucide.createIcons();
      }
    }

    // IMMEDIATELY EXECUTE LOCK METADATA LOAD ON PAGE LOAD
    document.addEventListener('DOMContentLoaded', () => {
      loadLockMetadata();
    });

    function triggerCooldown(seconds) {
      const btn = document.getElementById('unlockBtn');
      const input = document.getElementById('answerInput');
      const msg = document.getElementById('lockMessage');

      btn.disabled = true;
      input.disabled = true;
      msg.classList.remove('hidden');

      let remaining = seconds;
      const interval = setInterval(() => {
        msg.innerText = `Too many wrong guesses. Lockout engaged for ${remaining}s`;
        remaining--;
        if (remaining < 0) {
          clearInterval(interval);
          btn.disabled = false;
          input.disabled = false;
          msg.classList.add('hidden');
        }
      }, 1000);
    }

    async function handleVerifySubmit(e) {
      e.preventDefault();
      const btn = document.getElementById('unlockBtn');
      const input = document.getElementById('answerInput');
      const msg = document.getElementById('lockMessage');

      btn.innerText = 'Verifying Answer...';
      btn.disabled = true;

      try {
        const urlParams = new URLSearchParams(window.location.search);
        const res = await fetch('<?php echo APP_URL; ?>/api/verify_hint.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ slug: currentSlug, answer: input.value.trim(), preview_mode: (urlParams.get('theme') ? '1' : '0') })
        });
        const data = await res.json();

        if (data.success) {
          lockData = data;
          document.getElementById('lockScreenView').classList.add('hidden');
          document.getElementById('resultPageView').classList.remove('hidden');
          renderResultPage(data);
          if (typeof confetti === 'function') {
            confetti({ particleCount: 150, spread: 120, origin: { y: 0.5 } });
          }
        } else if (data.locked) {
          triggerCooldown(data.locked_until_seconds || 60);
          msg.classList.remove('hidden');
          msg.innerText = data.message || 'Too many wrong guesses. Please wait.';
        } else {
          msg.classList.remove('hidden');
          msg.innerText = data.message || 'Wrong answer. Please try again.';
          btn.innerText = 'Unlock This Memory 💛';
          btn.disabled = false;
        }
      } catch (err) {
        msg.classList.remove('hidden');
        msg.innerText = 'Connection error. Please try again. (' + err.message + ')';
        btn.innerText = 'Unlock This Memory 💛';
        btn.disabled = false;
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadLockMetadata();
    });

    function renderResultPage(data) {
      const container = document.getElementById('resultContentContainer');
      const templateId = data.template_id;

      const content = data.content;
      const tf = content.template_fields || {};
      const media = content.media || [];
      const letters = content.letters || [];
      const tokens = content.tokens || [];
      const reasons = tf.reasons || [];
      const rakhiVoucherStatus = data.voucher_status || null;
      const rakhiAffiliateProducts = data.affiliate_products || [];

      // SINGER HIT PLAYLIST REGISTRY
      const SINGER_PLAYLISTS = {
        'arijit singh': [
          { title: 'Tum Hi Ho', url: 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3' },
          { title: 'Kesariya', url: 'https://cdn.pixabay.com/download/audio/2022/03/15/audio_c8c8a88390.mp3?filename=romantic-piano-10708.mp3' },
          { title: 'Apna Bana Le', url: 'https://cdn.pixabay.com/download/audio/2022/10/14/audio_9939ce2674.mp3?filename=sweet-heartbeats-12400.mp3' }
        ],
        'kk': [
          { title: 'Zara Sa', url: 'https://cdn.pixabay.com/download/audio/2022/05/16/audio_db65912e7a.mp3?filename=gentle-romantic-guitar-11000.mp3' },
          { title: 'Labon Ko', url: 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3' },
          { title: 'Dil Ibadat', url: 'https://cdn.pixabay.com/download/audio/2022/03/15/audio_c8c8a88390.mp3?filename=romantic-piano-10708.mp3' }
        ],
        'atif aslam': [
          { title: 'Tera Hone Laga Hoon', url: 'https://cdn.pixabay.com/download/audio/2022/10/14/audio_9939ce2674.mp3?filename=sweet-heartbeats-12400.mp3' },
          { title: 'Jeene Laga Hoon', url: 'https://cdn.pixabay.com/download/audio/2022/05/16/audio_db65912e7a.mp3?filename=gentle-romantic-guitar-11000.mp3' }
        ],
        'shreya ghoshal': [
          { title: 'Sun Raha Hai Na Tu', url: 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3' },
          { title: 'Piyu Bole', url: 'https://cdn.pixabay.com/download/audio/2022/03/15/audio_c8c8a88390.mp3?filename=romantic-piano-10708.mp3' }
        ]
      };

      // Resolve Music Track & Titles (check both content and content.template_fields)
      let rawAudioUrl = content.bg_music_url || tf.bg_music_url || tf.song_url || tf.youtube_url || '';
      let finalAudioUrl = rawAudioUrl || 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3';
      let finalSongTitle = content.song_title || tf.song_title || '';
      let finalArtist = content.song_artist || tf.song_artist || '';

      if (rawAudioUrl === 'random_singer' || !rawAudioUrl) {
        const singerKey = (content.favorite_singers || tf.favorite_singers || 'arijit singh').toLowerCase();
        const matchedList = SINGER_PLAYLISTS[singerKey] || SINGER_PLAYLISTS['arijit singh'];
        const randomTrack = matchedList[Math.floor(Math.random() * matchedList.length)];
        finalAudioUrl = randomTrack.url;
        if (!finalSongTitle || finalSongTitle === 'Random Hit Track') {
          finalSongTitle = randomTrack.title;
        }
        if (!finalArtist) {
          finalArtist = content.favorite_singers || tf.favorite_singers || 'Arijit Singh';
        }
      }

      // Check if audio URL is a YouTube Video or Shorts URL
      const ytVideoId = extractYouTubeId(rawAudioUrl || finalAudioUrl);
      if (ytVideoId && (!finalSongTitle || finalSongTitle === 'Acoustic Sunset Love')) {
        finalSongTitle = 'YouTube Music Video';
        finalArtist = 'YouTube Audio';
      }

      if (!finalSongTitle) finalSongTitle = 'Acoustic Sunset Love';
      if (!finalArtist) finalArtist = 'Romantic Track';

      // Preload audio silently — no autoplay (blocked on mobile)
      if (!ytVideoId) {
        const audioElem = document.getElementById('bgAudio');
        if (audioElem) {
          audioElem.volume = 0.5;
          const volSlider = document.getElementById('volumeSlider');
          if (volSlider) volSlider.value = 0.5;
          audioElem.src = finalAudioUrl;
          audioElem.load(); // preload only — no play yet
        }
      }

      // Show the Song-For-You tap badge
      showSongBadge(finalSongTitle, finalArtist, finalAudioUrl, ytVideoId);

      if (!finalArtist) {
        if (content.favorite_singers && !content.favorite_singers.includes('Tony!')) {
          finalArtist = content.favorite_singers;
        } else {
          finalArtist = 'Romantic Track';
        }
      }

      // Auto-show Floating Music Player Widget on theme unlock!
      const musicBox = document.getElementById('desktopMusicBox');
      if (musicBox) {
        musicBox.classList.remove('hidden');
        musicBox.classList.add('flex');
      }

      if (document.getElementById('musicBoxTitle')) document.getElementById('musicBoxTitle').innerText = finalSongTitle;
      if (document.getElementById('musicBoxArtist')) document.getElementById('musicBoxArtist').innerText = finalArtist;

      const pName = content.partner_name || 'Partner';
      const pInitial = pName.charAt(0).toUpperCase();
      const cleanReceiverPhoto = content.receiver_photo ? normalizeMediaUrlJs(content.receiver_photo) : '';

      // Update Partner Photo Avatar inside Music Box
      const playerAvatarContainer = document.getElementById('playerAvatarContainer');
      if (playerAvatarContainer) {
        if (cleanReceiverPhoto && cleanReceiverPhoto.trim() !== '') {
          playerAvatarContainer.innerHTML = `<img id="playerReceiverPhotoImg" src="${cleanReceiverPhoto}" onerror="this.onerror=null; this.parentElement.innerHTML='<span id=\\'playerReceiverFallback\\' class=\\'text-base font-bold font-serif text-[#eac34a]\\'>${pInitial}</span>';" alt="${content.partner_name}" class="w-full h-full object-cover rounded-[10px]">`;
        } else {
          playerAvatarContainer.innerHTML = `<span id="playerReceiverFallback" class="text-base font-bold font-serif text-[#eac34a]">${pInitial}</span>`;
        }
      }

      const startDate = tf.relationship_start_date ? new Date(tf.relationship_start_date) : new Date();
      const dobStr = tf.partner_dob || '1998-11-20';

      const photoAvatarHtml = cleanReceiverPhoto && cleanReceiverPhoto.trim() !== '' ?
        `<img id="receiverPhotoImg" src="${cleanReceiverPhoto}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner\\'>${pInitial}</div>';" alt="${content.partner_name}" class="w-full h-full rounded-full object-cover border-2 border-[#151215]">` :
        `<div id="receiverPhotoImg" class="w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner">${pInitial}</div>`;

      if (data.html_content) {
        container.innerHTML = data.html_content;
        lucide.createIcons();

        // Re-initialize any specific theme scripts here if needed,
        // Since we render from PHP, we might just call init scripts if they are defined
        // We'll trust the inline onclicks and global functions for now.

      } else {
        // Fallback layout
        container.innerHTML = `
          <section class="relative pt-20 pb-16 px-4 text-center z-10">
            <div class="max-w-3xl mx-auto space-y-6">
              <h1 class="text-4xl font-extrabold font-serif text-[#e8e0e3]">${content.partner_name}</h1>
              <div class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/30">
                <p class="font-serif text-lg text-[#e8e0e3]">"${content.love_note_text}"</p>
                <p class="font-handwriting text-2xl text-[#eac34a] mt-4">— ${content.buyer_name}</p>
              </div>
            </div>
          </section>

          <!-- Footer Bar -->
          <footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
            <p class="text-xs text-[#d0c3cb]">Made with endless love by <strong class="text-[#eac34a]">${content.buyer_name}</strong> for <strong class="text-[#eac34a]">${content.partner_name}</strong></p>
            <div class="flex items-center justify-center gap-3">
              <button onclick="relockGiftSession()" type="button" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer">
                <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>Lock Gift Page 🔒</span>
              </button>
            </div>
          </footer>
        `;
        lucide.createIcons();
      }
    }

    function startLiveCounter(startDate) {
      function update() {
        const now = new Date();
        let years = now.getFullYear() - startDate.getFullYear();
        let months = now.getMonth() - startDate.getMonth();
        let days = now.getDate() - startDate.getDate();
        let hours = now.getHours() - startDate.getHours();
        let mins = now.getMinutes() - startDate.getMinutes();
        let secs = now.getSeconds() - startDate.getSeconds();

        if (secs < 0) { mins--; secs += 60; }
        if (mins < 0) { hours--; mins += 60; }
        if (hours < 0) { days--; hours += 24; }
        if (days < 0) { months--; days += 30; }
        if (months < 0) { years--; months += 12; }

        if (document.getElementById('cntY')) document.getElementById('cntY').innerText = Math.max(0, years);
        if (document.getElementById('cntM')) document.getElementById('cntM').innerText = Math.max(0, months);
        if (document.getElementById('cntD')) document.getElementById('cntD').innerText = Math.max(0, days);
        if (document.getElementById('cntH')) document.getElementById('cntH').innerText = Math.max(0, hours);
        if (document.getElementById('cntMin')) document.getElementById('cntMin').innerText = Math.max(0, mins);
        if (document.getElementById('cntSec')) document.getElementById('cntSec').innerText = Math.max(0, secs);
      }
      update();
      setInterval(update, 1000);
    }

    async function submitProposalAnswer(pageId, answer) {
      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/respond_proposal.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ page_id: pageId, response: answer })
        });
        const data = await res.json();

        if (data.success) {
          confetti({ particleCount: 180, spread: 100 });
          const resp = data.proposal_response || { response: answer, responded_at_formatted: 'Just now' };
          document.getElementById('proposalResponseSection').innerHTML = `
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center mx-auto shadow-lg">
                <i data-lucide="check-circle-2" class="w-7 h-7 text-[#eac34a]"></i>
              </div>
              <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#eac34a]">Your Answer 💕</span>
                <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">
                  ${resp.response === 'yes' ? 'YES! A Thousand Times Yes 💍' : "Let's Talk 💕"}
                </h3>
                <p class="text-xs text-[#d0c3cb]/80">Responded on ${resp.responded_at_formatted || 'Just now'}</p>
              </div>
              <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] text-sm font-serif italic text-[#eac34a] max-w-md mx-auto shadow-inner">
                "${resp.partner_note ? resp.partner_note : (resp.response === 'yes' ? 'YES! A thousand times YES my love! 💕' : 'Let\'s talk and celebrate together! 💕')}"
              </div>
              <p class="text-[11px] text-[#d0c3cb]/60 italic pt-1">Your response has been securely captured &amp; sent!</p>
            </div>
          `;
          lucide.createIcons();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Server error: ' + err.message);
      }
    }

    function startBirthdayCountdown(dobStr) {
      function update() {
        const dob = new Date(dobStr);
        const now = new Date();

        let nextDate = new Date(now.getFullYear(), dob.getMonth(), dob.getDate());
        if (nextDate.getTime() < now.getTime()) {
          nextDate = new Date(now.getFullYear() + 1, dob.getMonth(), dob.getDate());
        }

        const diff = Math.max(0, nextDate.getTime() - now.getTime());
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const mins = Math.floor((diff / 1000 / 60) % 60);
        const secs = Math.floor((diff / 1000) % 60);

        if (document.getElementById('bdayDays')) document.getElementById('bdayDays').innerText = days;
        if (document.getElementById('bdayHours')) document.getElementById('bdayHours').innerText = hours;
        if (document.getElementById('bdayMins')) document.getElementById('bdayMins').innerText = mins;
        if (document.getElementById('bdaySecs')) document.getElementById('bdaySecs').innerText = secs;
      }
      update();
      setInterval(update, 1000);
    }

    function startLongDistanceClocks(buyerTz, partnerTz) {
      function update() {
        const now = new Date();
        try {
          const bTime = now.toLocaleTimeString('en-US', { timeZone: buyerTz, hour: '2-digit', minute: '2-digit', second: '2-digit' });
          const pTime = now.toLocaleTimeString('en-US', { timeZone: partnerTz, hour: '2-digit', minute: '2-digit', second: '2-digit' });
          if (document.getElementById('buyerClock')) document.getElementById('buyerClock').innerText = bTime;
          if (document.getElementById('partnerClock')) document.getElementById('partnerClock').innerText = pTime;
        } catch (err) {
          const fallback = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
          if (document.getElementById('buyerClock')) document.getElementById('buyerClock').innerText = fallback;
          if (document.getElementById('partnerClock')) document.getElementById('partnerClock').innerText = fallback;
        }
      }
      update();
      setInterval(update, 1000);
    }

    function startReunionCountdown(reunionDateStr) {
      function update() {
        const reunion = new Date(reunionDateStr);
        const now = new Date();
        const diff = Math.max(0, reunion.getTime() - now.getTime());

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const mins = Math.floor((diff / 1000 / 60) % 60);
        const secs = Math.floor((diff / 1000) % 60);

        if (document.getElementById('reunionDays')) document.getElementById('reunionDays').innerText = days;
        if (document.getElementById('reunionHours')) document.getElementById('reunionHours').innerText = hours;
        if (document.getElementById('reunionMins')) document.getElementById('reunionMins').innerText = mins;
        if (document.getElementById('reunionSecs')) document.getElementById('reunionSecs').innerText = secs;
      }
      update();
      setInterval(update, 1000);
    }

    function switchRevealTab(tabName) {
      ['journey', 'scrapbook', 'letters', 'tokens'].forEach(t => {
        const btn = document.getElementById('revealTab-' + t);
        const content = document.getElementById('revealTabContent-' + t);
        if (t === tabName) {
          if (btn) btn.className = 'px-5 py-2.5 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] shadow-lg transition-all cursor-pointer';
          if (content) content.classList.remove('hidden');
        } else {
          if (btn) btn.className = 'px-5 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all cursor-pointer';
          if (content) content.classList.add('hidden');
        }
      });
    }

    function openLetterModal(title, category, text) {
      const modal = document.getElementById('letterModal');
      document.getElementById('letterModalTitle').innerText = title;
      document.getElementById('letterModalCat').innerText = category;
      document.getElementById('letterModalBody').innerHTML = text;

      if (modal) {
        confetti({ particleCount: 60, spread: 70, origin: { y: 0.6 } });
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    }

    function closeLetterModal() {
      const modal = document.getElementById('letterModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

    function redeemToken(idx) {
      confetti({ particleCount: 150, spread: 90 });
      const btn = document.getElementById('redeemBtn-' + idx);
      if (btn) {
        btn.className = 'w-full py-2.5 bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all';
        btn.innerHTML = '<span>REDEEMED 🎉</span>';
      }
    }

    function openLightbox(url) {
      const modal = document.getElementById('imageLightboxModal');
      const img = document.getElementById('lightboxImg');
      if (modal && img) {
        img.src = url;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    }

    function closeLightbox() {
      const modal = document.getElementById('imageLightboxModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

    let rakhiRitualProgress = { tilak: false, diya: false, rakhi: false, selectedDesign: 'gold_zardosi' };

    function playTempleBellSound() {
      try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 1.2);
        gain.gain.setValueAtTime(0.5, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.2);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + 1.2);
      } catch(e) {}
    }

    function playVedicChantSound() {
      try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();
        [220, 330, 440].forEach((freq, idx) => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.type = 'triangle';
          osc.frequency.setValueAtTime(freq, ctx.currentTime);
          gain.gain.setValueAtTime(0.15, ctx.currentTime + idx * 0.1);
          gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 2.0);
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start(ctx.currentTime + idx * 0.1);
          osc.stop(ctx.currentTime + 2.0);
        });
      } catch(e) {}
    }

    function applyRoyalTilak() {
      rakhiRitualProgress.tilak = true;
      if (navigator.vibrate) navigator.vibrate([40, 30, 40]);
      playTempleBellSound();

      const tilakMark = document.getElementById('tilakMarkOnAvatar');
      if (tilakMark) {
        tilakMark.classList.remove('hidden');
        tilakMark.classList.add('animate-tilak');
      }
      
      const btn = document.getElementById('tilakBtn');
      if (btn) btn.innerHTML = '<span class="text-base">✓ 🔴</span><span class="font-serif">Step 1: Applied</span>';
      
      const status = document.getElementById('rakhiRitualStatus');
      if (status) status.innerHTML = '✨ <strong>Roli-Chawal Kumkum Tilak</strong> applied with love &amp; Vedic blessings! Tap Step 2 to light Aarti Diya! 🪔';
      
      if (typeof confetti === 'function') {
        confetti({ particleCount: 80, spread: 75, origin: { y: 0.4 }, colors: ['#ef4444', '#f59e0b', '#ffd700'] });
      }
    }

    function lightRoyalDiya() {
      if (!rakhiRitualProgress.tilak) {
        alert('Please complete Step 1 (Apply Kumkum Tilak) first!');
        return;
      }
      rakhiRitualProgress.diya = true;
      if (navigator.vibrate) navigator.vibrate([50, 40, 50]);
      playTempleBellSound();
      playVedicChantSound();
      
      const btn = document.getElementById('diyaBtn');
      if (btn) btn.innerHTML = '<span class="text-base">✓ 🪔</span><span class="font-serif">Step 2: Lit Diya</span>';

      const thaliRing = document.getElementById('thaliOuterRing');
      if (thaliRing) {
        thaliRing.classList.add('animate-aarti');
      }

      const diyaIcon = document.getElementById('diyaFlameIcon');
      if (diyaIcon) {
        diyaIcon.classList.add('animate-flame');
      }

      const thaliContainer = document.getElementById('royalThaliContainer');
      if (thaliContainer) {
        thaliContainer.classList.add('shadow-[0_0_90px_rgba(234,195,74,0.5)]');
      }
      
      const status = document.getElementById('rakhiRitualStatus');
      if (status) status.innerHTML = '🪔 <strong>Sacred Aarti Diya flame lit!</strong> Thali rotating with temple bells. Tap Step 3 to Select &amp; Tie Royal Rakhi! 🧵';
      
      if (typeof confetti === 'function') {
        confetti({ particleCount: 100, spread: 95, origin: { y: 0.5 }, colors: ['#f59e0b', '#eab308', '#ffffff'] });
      }
    }

    function tieRoyalRakhi() {
      if (!rakhiRitualProgress.diya) {
        alert('Please complete Step 1 (Tilak) & Step 2 (Diya) first!');
        return;
      }
      openRakhiSelectorModal();
    }

    function openRakhiSelectorModal() {
      const modal = document.getElementById('rakhiSelectorModal');
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    }

    function closeRakhiSelectorModal() {
      const modal = document.getElementById('rakhiSelectorModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

    function selectRakhiDesign(designKey) {
      rakhiRitualProgress.selectedDesign = designKey;
      const options = ['gold_zardosi', 'ruby_silk', 'peacock', 'sacred_om'];
      options.forEach(opt => {
        const card = document.getElementById('rakhiOpt-' + opt);
        if (card) {
          if (opt === designKey) {
            card.className = 'rakhi-option-card bg-[#2a060b] p-3.5 rounded-2xl border-2 border-[#eac34a] cursor-pointer scale-105 transition-all shadow-xl text-center space-y-2';
          } else {
            card.className = 'rakhi-option-card bg-[#150305] p-3.5 rounded-2xl border border-[#4d444b] cursor-pointer hover:scale-105 transition-all shadow-lg text-center space-y-2';
          }
        }
      });
    }

    function ringHangingBell() {
      if (navigator.vibrate) navigator.vibrate([30, 20, 30]);
      playTempleBellSound();
      const bell = document.getElementById('hangingBellItem');
      if (bell) {
        bell.classList.remove('animate-bell-swing');
        void bell.offsetWidth;
        bell.classList.add('animate-bell-swing');
      }
      const status = document.getElementById('rakhiRitualStatus');
      if (status) status.innerHTML = '🔔 <strong>Sacred Temple Bell Rung!</strong> Auspicious resonance filled the shrine! ✨';
    }

    function triggerPushpaVarsha() {
      if (navigator.vibrate) navigator.vibrate([20, 20, 20]);
      playTempleBellSound();
      
      if (typeof confetti === 'function') {
        confetti({
          particleCount: 80,
          spread: 85,
          origin: { y: 0.3 },
          colors: ['#ef4444', '#f59e0b', '#ec4899', '#fbbf24']
        });
      }
      const status = document.getElementById('rakhiRitualStatus');
      if (status) status.innerHTML = '🌸 <strong>Pushpa Varsha (पुष्प वर्षा)!</strong> Fresh rose &amp; marigold petals showered with love! 🌹';
    }

    function offerSweetPrasad() {
      if (navigator.vibrate) navigator.vibrate([40, 30, 40]);
      playTempleBellSound();
      
      if (typeof confetti === 'function') {
        confetti({
          particleCount: 60,
          spread: 65,
          origin: { y: 0.4 },
          colors: ['#f59e0b', '#ffd700', '#ffffff']
        });
      }
      const status = document.getElementById('rakhiRitualStatus');
      if (status) status.innerHTML = '🍬 <strong>Sweet Prasad Offered!</strong> Delicious Kaju Katli &amp; Motichoor Laddu offered with love! 🥮';
    }

    function confirmTieRakhi() {
      closeRakhiSelectorModal();
      rakhiRitualProgress.rakhi = true;

      if (navigator.vibrate) navigator.vibrate([60, 40, 60, 40, 100]);
      playTempleBellSound();
      playVedicChantSound();

      const rakhiIcons = {
        'gold_zardosi': '👑 Gold Zardosi',
        'ruby_silk': '💎 Ruby Silk',
        'peacock': '🦚 Peacock Feather',
        'sacred_om': '🕉️ Sacred Om'
      };

      const rakhiEmojiMap = {
        'gold_zardosi': '👑',
        'ruby_silk': '💎',
        'peacock': '🦚',
        'sacred_om': '🕉️'
      };

      const designTitle = rakhiIcons[rakhiRitualProgress.selectedDesign] || '👑 Gold Zardosi';
      
      const btn = document.getElementById('rakhiBtn');
      if (btn) btn.innerHTML = `<span class="text-base">✓ 🧵</span><span class="font-serif">${designTitle} Tied!</span>`;

      // Visually bind Rakhi onto Brother's Wrist
      const wristNotice = document.getElementById('wristEmptyNotice');
      const tiedRakhiOverlay = document.getElementById('tiedRakhiOnWrist');
      const tiedRakhiIcon = document.getElementById('tiedRakhiIcon');

      if (wristNotice) wristNotice.classList.add('hidden');
      if (tiedRakhiOverlay) {
        tiedRakhiOverlay.classList.remove('hidden');
        if (tiedRakhiIcon) tiedRakhiIcon.textContent = rakhiEmojiMap[rakhiRitualProgress.selectedDesign] || '🧵';
      }
      
      const status = document.getElementById('rakhiRitualStatus');
      if (status) status.innerHTML = `🎉 <strong>${designTitle} Rakhi Tied on Brother's Wrist!</strong> Sacred blessings &amp; Shagun Envelope Unlocked! 💖`;
      
      if (typeof confetti === 'function') {
        // Wave 1: Golden Confetti & Rose Petals
        confetti({ particleCount: 240, spread: 110, origin: { y: 0.5 }, colors: ['#ef4444', '#f59e0b', '#ffd700', '#ffffff'] });
        setTimeout(() => confetti({ particleCount: 170, angle: 60, spread: 85, origin: { x: 0 }, colors: ['#ef4444', '#ffd700'] }), 300);
        setTimeout(() => confetti({ particleCount: 170, angle: 120, spread: 85, origin: { x: 1 }, colors: ['#f59e0b', '#ffffff'] }), 600);
      }

      // Automatically open Shagun Lifafa
      setTimeout(() => {
        toggleShagunLifafa();
        const envelope = document.getElementById('shagunLetterContent') || document.getElementById('shagunEnvelopeContainer');
        if (envelope) envelope.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 1200);
    }

    function toggleShagunLifafa() {
      const container = document.getElementById('shagunEnvelopeContainer');
      const letter = document.getElementById('shagunLetterContent');
      if (container && letter) {
        if (letter.classList.contains('hidden')) {
          container.classList.add('hidden');
          letter.classList.remove('hidden');
          if (typeof confetti === 'function') confetti({ particleCount: 100, spread: 80, origin: { y: 0.6 } });
        } else {
          letter.classList.add('hidden');
          container.classList.remove('hidden');
        }
      }
    }

    loadLockMetadata();

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function cleanAttrStr(str) {
      if (!str) return '';
      return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }
  </script>

  <!-- Interactive Rakhi Design Selection Modal -->
  <div id="rakhiSelectorModal" class="fixed inset-0 z-50 bg-[#100d10]/95 backdrop-blur-md hidden items-center justify-center p-4 overflow-y-auto" onclick="closeRakhiSelectorModal()">
    <div class="relative max-w-lg w-full bg-gradient-to-br from-[#2a060b] via-[#1a0407] to-[#100204] p-6 sm:p-8 rounded-3xl border-2 border-[#eac34a] shadow-2xl space-y-6 text-center my-auto max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
      <div class="space-y-1">
        <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#eac34a] bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#eac34a]/30 inline-block">
          👑 SACRED RAKHI SELECTION
        </span>
        <h3 class="text-2xl font-bold font-serif text-white">Choose Rakhi Style 🧵</h3>
        <p class="text-xs text-[#d0c3cb]">Select your favorite Rakhi design to tie onto your sibling's photo avatar!</p>
      </div>

      <!-- 4 Royal Rakhi Options Grid -->
      <div class="grid grid-cols-2 gap-3 text-left">
        <div onclick="selectRakhiDesign('gold_zardosi')" id="rakhiOpt-gold_zardosi" class="rakhi-option-card bg-[#2a060b] p-3.5 rounded-2xl border-2 border-[#eac34a] cursor-pointer hover:scale-105 transition-all shadow-lg text-center space-y-2">
          <span class="text-3xl block">👑</span>
          <strong class="text-xs font-serif text-[#eac34a] block">Gold Zardosi</strong>
          <span class="text-[10px] text-[#d0c3cb] block">Royal Golden Thread &amp; Beads</span>
        </div>

        <div onclick="selectRakhiDesign('ruby_silk')" id="rakhiOpt-ruby_silk" class="rakhi-option-card bg-[#150305] p-3.5 rounded-2xl border border-[#4d444b] cursor-pointer hover:scale-105 transition-all shadow-lg text-center space-y-2">
          <span class="text-3xl block">💎</span>
          <strong class="text-xs font-serif text-[#e8e0e3] block">Ruby Royal Silk</strong>
          <span class="text-[10px] text-[#d0c3cb] block">Crimson Gemstone &amp; Silk</span>
        </div>

        <div onclick="selectRakhiDesign('peacock')" id="rakhiOpt-peacock" class="rakhi-option-card bg-[#150305] p-3.5 rounded-2xl border border-[#4d444b] cursor-pointer hover:scale-105 transition-all shadow-lg text-center space-y-2">
          <span class="text-3xl block">🦚</span>
          <strong class="text-xs font-serif text-[#e8e0e3] block">Peacock Feather</strong>
          <span class="text-[10px] text-[#d0c3cb] block">Vibrant Mayur Pankh Design</span>
        </div>

        <div onclick="selectRakhiDesign('sacred_om')" id="rakhiOpt-sacred_om" class="rakhi-option-card bg-[#150305] p-3.5 rounded-2xl border border-[#4d444b] cursor-pointer hover:scale-105 transition-all shadow-lg text-center space-y-2">
          <span class="text-3xl block">🕉️</span>
          <strong class="text-xs font-serif text-[#e8e0e3] block">Sacred Om Thread</strong>
          <span class="text-[10px] text-[#d0c3cb] block">Pure Auspicious Mauli Thread</span>
        </div>
      </div>

      <button type="button" onclick="confirmTieRakhi()" class="w-full py-3.5 bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#eac34a] text-[#241a00] font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-[0_0_25px_rgba(234,195,74,0.4)] hover:brightness-110 transition-all cursor-pointer">
        Tie Selected Rakhi 🧵✨
      </button>
    </div>
  </div>

  <!-- Letter Reading Modal -->
  <div id="letterModal" class="fixed inset-0 z-50 bg-[#100d10]/90 backdrop-blur-md hidden overflow-y-auto p-4 sm:p-6 flex items-start sm:items-center justify-center py-8 sm:py-12" onclick="closeLetterModal()">
    <div class="relative max-w-xl w-full bg-[#221f21] p-6 sm:p-10 rounded-3xl border border-[#eac34a]/50 shadow-2xl space-y-6 text-center my-auto max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
      <span id="letterModalCat" class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#e4b9df]/20"></span>
      <h2 id="letterModalTitle" class="text-2xl font-bold font-serif text-[#e8e0e3]"></h2>
      
      <div class="bg-[#151215] p-6 rounded-2xl border border-[#4d444b] text-left">
        <p id="letterModalBody" class="font-serif text-sm text-[#e8e0e3] leading-relaxed italic"></p>
      </div>

      <button onclick="closeLetterModal()" class="px-6 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-full hover:bg-[#ffe088] transition-all cursor-pointer">
        Close Letter ✉️
      </button>
    </div>
  </div>

  <!-- Image Lightbox Modal -->
  <div id="imageLightboxModal" class="fixed inset-0 z-50 bg-[#100d10]/90 backdrop-blur-md hidden items-center justify-center p-4" onclick="closeLightbox()">
    <div class="relative max-w-3xl w-full max-h-[85vh] flex items-center justify-center" onclick="event.stopPropagation()">
      <button onclick="closeLightbox()" class="absolute -top-12 right-0 text-[#e8e0e3] hover:text-[#eac34a] p-2 cursor-pointer">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>
  </div>

  <!-- Smart Smooth Auto-Hiding Header Script -->
  <script>
    (function() {
      let lastScrollY = window.scrollY;
      const header = document.getElementById('revealHeader');
      const scrollThreshold = 5;

      if (!header) return;

      window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        // Always show header near top of page (0 to 60px)
        if (currentScrollY <= 60) {
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
        } else if (currentScrollY < lastScrollY) {
          // Scrolling Up -> Smoothly Reveal Header
          header.classList.remove('-translate-y-full');
        }

        lastScrollY = currentScrollY;
      }, { passive: true });
    })();
    function tieVirtualRakhi() {
      const btn = document.getElementById('tieRakhiBtn');
      if (btn) {
        btn.innerHTML = '✓ Rakhi Tied with Blessings & Love! 🪔';
        btn.className = 'px-8 py-4 rounded-full bg-emerald-600 text-white font-extrabold text-xs sm:text-sm uppercase tracking-widest shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all';
      }
      if (typeof confetti === 'function') {
        confetti({ particleCount: 120, spread: 80, origin: { y: 0.6 } });
      }
    }

    function toggleShagunLifafa() {
      const envelope = document.getElementById('shagunEnvelopeContainer');
      const letter = document.getElementById('shagunLetterContent');
      if (envelope && letter) {
        envelope.classList.toggle('hidden');
        letter.classList.toggle('hidden');
        if (typeof confetti === 'function' && !letter.classList.contains('hidden')) {
          confetti({ particleCount: 80, spread: 60, origin: { y: 0.7 } });
        }
      }
    }
  </script>
</body>
</html>
