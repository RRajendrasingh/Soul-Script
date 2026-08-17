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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
<!-- Mobile Compact Floating Music Pill (Native CSS: Shown on Mobile < 640px, Hidden on Desktop >= 640px) -->
<button id="mobileMusicMiniBtn" onclick="toggleMobileMusicDrawer()" aria-label="Open Music Player" class="flex sm:hidden fixed bottom-4 right-4 z-[90] w-12 h-12 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#ffe088] to-[#cca830] text-[#241a00] border-2 border-[#151215] shadow-[0_0_20px_rgba(234,195,74,0.6)] items-center justify-center cursor-pointer transition-all hover:scale-105 active:scale-95">
  <span id="mobileMiniMusicIcon" class="text-lg">🎵</span>
</button>

<!-- Floating Music Player Widget Box (Native CSS: Hidden on Mobile < 640px until tapped, Flex on Desktop >= 640px) -->
<div id="desktopMusicBox" class="hidden sm:flex fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:bottom-6 z-50 bg-[#221f21]/95 backdrop-blur-xl border border-[#eac34a]/40 rounded-2xl p-3 items-center gap-3.5 shadow-[0_10px_35px_rgba(0,0,0,0.9)] max-w-none sm:max-w-sm transition-all duration-300">
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

  <!-- Right Actions: Play/Pause Button & Mobile Minimize Arrow -->
  <div class="flex items-center gap-2 shrink-0">
    <button id="audioPlayBtn" onclick="toggleAudioPlay()" aria-label="Play Pause Music" class="w-10 h-10 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] flex items-center justify-center shadow-lg transition-all shrink-0 cursor-pointer">
      <svg class="w-4 h-4 fill-[#241a00] ml-0.5" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
    </button>
    <button onclick="collapseMobileMusicDrawer()" class="sm:hidden p-1.5 text-[#d0c3cb] hover:text-[#eac34a] transition cursor-pointer" title="Minimize Music Player">
      <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
  </div>
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

      // Auto-show Floating Music Controls on theme unlock: Mini Pill on Mobile (< 640px), Full Widget on Desktop (>= 640px)
      const musicBox = document.getElementById('desktopMusicBox');
      const mobileMiniBtn = document.getElementById('mobileMusicMiniBtn');
      if (window.innerWidth < 640) {
        if (mobileMiniBtn) {
          mobileMiniBtn.className = mobileMiniBtn.className.replace(/\bhidden\b/g, '').trim();
          mobileMiniBtn.style.display = 'flex';
        }
        if (musicBox) {
          musicBox.style.display = 'none';
        }
      } else {
        if (musicBox) {
          musicBox.className = musicBox.className.replace(/\bhidden\b/g, '').trim();
          musicBox.style.display = 'flex';
        }
      }

      if (document.getElementById('musicBoxTitle')) document.getElementById('musicBoxTitle').innerText = songTitle;

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

    function toggleMobileMusicDrawer() {
      const box = document.getElementById('desktopMusicBox');
      const miniBtn = document.getElementById('mobileMusicMiniBtn');
      if (!box) return;
      const isHidden = box.style.display === 'none' || box.classList.contains('hidden') || getComputedStyle(box).display === 'none';
      if (isHidden) {
        box.className = box.className.replace(/\bhidden\b/g, '').trim();
        box.style.display = 'flex';
        if (miniBtn) {
          miniBtn.style.display = 'none';
        }
      } else {
        collapseMobileMusicDrawer();
      }
    }

    function collapseMobileMusicDrawer() {
      const box = document.getElementById('desktopMusicBox');
      const miniBtn = document.getElementById('mobileMusicMiniBtn');
      if (!box) return;
      box.style.display = 'none';
      if (miniBtn && window.innerWidth < 640) {
        miniBtn.className = miniBtn.className.replace(/\bhidden\b/g, '').trim();
        miniBtn.style.display = 'flex';
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

        // Re-execute scripts inside injected HTML so template functions execute
        Array.from(container.querySelectorAll('script')).forEach(oldScript => {
          const newScript = document.createElement('script');
          Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
          newScript.appendChild(document.createTextNode(oldScript.innerHTML));
          oldScript.parentNode.replaceChild(newScript, oldScript);
        });

        // Initialize Global Keepsake Data Variables
        window.__giftMedia = (media || []).map(m => ({
          url: normalizeMediaUrlJs(m.file_path || m.url),
          caption: m.caption || 'Cherished Memory'
        }));
        window.__partnerPhoto = cleanReceiverPhoto || '<?php echo APP_URL; ?>/assets/default_gallery/sample_fa6955df.webp';
        window.__partnerName = content.partner_name || 'Sister';
        window.__buyerName = content.buyer_name || 'Brother';
        window.__giftSlug = currentSlug;
        window.__appUrl = APP_URL;
        window.__loveNote = content.love_note_text || content.love_letter_text || 'Thank you for being the most wonderful sibling in the universe!';
        window.__certId = 'SS-RB-' + new Date().getFullYear() + '-' + (data.page_id ? data.page_id.toString().padStart(4, '0') : '8942');

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

    function openCeremonyModal() {
      const modal = document.getElementById('rakhiCeremonyModal');
      if(modal) {
        modal.classList.remove('hidden');
        // trigger reflow
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeCeremonyModal() {
      const modal = document.getElementById('rakhiCeremonyModal');
      if(modal) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        setTimeout(() => {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        }, 500);
      }
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

      // Visually bind Rakhi onto Brother's Wrist & Avatar
      const wristNotice = document.getElementById('wristEmptyNotice');
      const tiedRakhiOverlay = document.getElementById('tiedRakhiOnWrist');
      const tiedRakhiIcon = document.getElementById('tiedRakhiIcon');
      const tiedOnAvatar = document.getElementById('rakhiTiedOnAvatar');

      if (wristNotice) wristNotice.classList.add('hidden');
      if (tiedRakhiOverlay) {
        tiedRakhiOverlay.classList.remove('hidden');
        if (tiedRakhiIcon) tiedRakhiIcon.textContent = rakhiEmojiMap[rakhiRitualProgress.selectedDesign] || '🧵';
      }
      if (tiedOnAvatar) {
        tiedOnAvatar.classList.remove('hidden');
        if (tiedRakhiIcon) tiedRakhiIcon.textContent = rakhiEmojiMap[rakhiRitualProgress.selectedDesign] || '👑';
      }
      
      const status = document.getElementById('rakhiRitualStatus');
      if (status) status.innerHTML = `🎉 <strong>${designTitle} Rakhi Tied!</strong> Sacred blessings &amp; Shagun Envelope Unlocked! 💖`;
      
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

    function downloadShahiTamrapatra() {
      const btn = document.getElementById('downloadCertBtn');
      const originalContent = btn ? btn.innerHTML : '';
      if (btn) {
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Generating 4K Certificate...';
        btn.disabled = true;
      }

      const svg = document.getElementById('shahiTamrapatraSvg');
      if (!svg) {
        if (btn) {
          btn.innerHTML = originalContent;
          btn.disabled = false;
        }
        return;
      }

      // Clone SVG and prepare for pure high-density rasterization (16:9 4K UHD 3840x2160)
      const svgClone = svg.cloneNode(true);
      svgClone.setAttribute('width', '3840');
      svgClone.setAttribute('height', '2160');

      const svgString = new XMLSerializer().serializeToString(svgClone);
      const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
      const URL = window.URL || window.webkitURL || window;
      const blobURL = URL.createObjectURL(svgBlob);

      const img = new Image();
      img.onload = function() {
        const canvas = document.createElement('canvas');
        // 4K Ultra-HD Master Resolution (3840 x 2160)
        canvas.width = 3840;
        canvas.height = 2160;
        const ctx = canvas.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        URL.revokeObjectURL(blobURL);

        const link = document.createElement('a');
        const buyer = '<?= preg_replace('/[^a-zA-Z0-9_-]/', '', $buyerName ?? 'Brother') ?>';
        const partner = '<?= preg_replace('/[^a-zA-Z0-9_-]/', '', $partnerName ?? 'Sister') ?>';
        link.download = `Shahi_Tamrapatra_Certificate_${buyer}_${partner}.png`;
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();

        if (btn) {
          btn.innerHTML = originalContent;
          btn.disabled = false;
        }
        if (window.lucide) lucide.createIcons();
        if (typeof confetti === 'function') {
          confetti({ particleCount: 120, spread: 80, origin: { y: 0.6 } });
        }
      };

      img.onerror = function(err) {
        console.error('SVG Render Error:', err);
        URL.revokeObjectURL(blobURL);
        if (btn) {
          btn.innerHTML = originalContent;
          btn.disabled = false;
        }
        if (window.lucide) lucide.createIcons();
        alert('Could not generate 4K image directly. Please try again.');
      };

      img.src = blobURL;
    }

    function shareShahiTamrapatraWhatsApp() {
      const text = `📜 *Official Shahi Tamrapatra — Sibling Bond Certificate* 👑\n\nThis Royal decree certifies the eternal bond of love and protection between *<?= addslashes($buyerName ?? '') ?>* and *<?= addslashes($partnerName ?? '') ?>*!\n\nView our official certificate on SoulScript: ${window.location.href}`;
      window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
    }

    // Helper: Safely load image as HTMLImageElement with CORS handling
    function loadImgAsync(src) {
      return new Promise((resolve) => {
        if (!src) return resolve(null);
        const img = new Image();
        img.crossOrigin = "anonymous";
        img.onload = () => resolve(img);
        img.onerror = () => {
          // Fallback retry without crossOrigin if local/data URL
          const img2 = new Image();
          img2.onload = () => resolve(img2);
          img2.onerror = () => resolve(null);
          img2.src = src;
        };
        img.src = src;
      });
    }

    // ==========================================
    // 1. WALL COLLAGE POSTER GENERATOR (300 DPI A4/A3)
    // ==========================================
    async function downloadWallKeepsakePoster() {
      const btn = document.getElementById('btnWallPoster');
      const origText = btn ? btn.innerHTML : '';
      if (btn) {
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Generating 300 DPI Wall Poster...';
        btn.disabled = true;
      }

      try {
        const partnerName = window.__partnerName || 'Sister';
        const buyerName = window.__buyerName || 'Brother';
        const partnerPhotoUrl = window.__partnerPhoto || '<?= APP_URL ?>/assets/default_gallery/sample_fa6955df.webp';
        const mediaList = (window.__giftMedia && window.__giftMedia.length > 0) 
          ? window.__giftMedia 
          : [{ url: partnerPhotoUrl, caption: 'Cherished Memory' }];

        // Canvas 2480 x 3508 (A4 300 DPI Print Standard)
        const canvas = document.createElement('canvas');
        canvas.width = 2480;
        canvas.height = 3508;
        const ctx = canvas.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';

        // 1. Base Ivory Linen Canvas
        const bgGrad = ctx.createLinearGradient(0, 0, 0, canvas.height);
        bgGrad.addColorStop(0, '#fdfbf7');
        bgGrad.addColorStop(0.5, '#f7f1e3');
        bgGrad.addColorStop(1, '#fdfbf7');
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 2. Ornate Outer 24K Gold & Crimson Border
        ctx.lineWidth = 14;
        ctx.strokeStyle = '#d4af37';
        ctx.strokeRect(60, 60, canvas.width - 120, canvas.height - 120);

        ctx.lineWidth = 4;
        ctx.strokeStyle = '#851d2c';
        ctx.strokeRect(85, 85, canvas.width - 170, canvas.height - 170);

        // Corner Filigrees
        const drawCornerFiligree = (cx, cy, flipX, flipY) => {
          ctx.save();
          ctx.translate(cx, cy);
          ctx.scale(flipX ? -1 : 1, flipY ? -1 : 1);
          ctx.fillStyle = '#b89343';
          ctx.beginPath();
          ctx.arc(0, 0, 24, 0, Math.PI * 2);
          ctx.fill();
          ctx.fillStyle = '#851d2c';
          ctx.beginPath();
          ctx.arc(0, 0, 10, 0, Math.PI * 2);
          ctx.fill();
          ctx.restore();
        };
        drawCornerFiligree(85, 85, false, false);
        drawCornerFiligree(canvas.width - 85, 85, true, false);
        drawCornerFiligree(85, canvas.height - 85, false, true);
        drawCornerFiligree(canvas.width - 85, canvas.height - 85, true, true);

        // 3. Top Rakhi Crest & Header
        ctx.textAlign = 'center';
        ctx.fillStyle = '#851d2c';
        ctx.font = 'bold 36px serif';
        ctx.fillText('👑  ✦  SOULSCRIPT ROYAL ARCHIVE  ✦  👑', canvas.width / 2, 175);

        ctx.fillStyle = '#4a2602';
        ctx.font = '900 68px "Cinzel Decorative", "Cinzel", "Playfair Display", Georgia, serif';
        ctx.fillText('The Sacred Sibling Journey', canvas.width / 2, 260);

        ctx.fillStyle = '#7a4204';
        ctx.font = 'italic bold 42px "Playfair Display", Georgia, serif';
        ctx.fillText(`${partnerName} & ${buyerName} — Cherished Lifetime Memories`, canvas.width / 2, 325);

        // 4. Center Oval 24K Gold Locket (Sister's Portrait)
        const locketX = canvas.width / 2;
        const locketY = 1680;
        const locketRx = 330;
        const locketRy = 420;

        // Outer Gold Locket Glow & Frame
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(locketX, locketY, locketRx + 32, locketRy + 32, 0, 0, Math.PI * 2);
        ctx.fillStyle = '#d4af37';
        ctx.shadowColor = 'rgba(184, 147, 67, 0.45)';
        ctx.shadowBlur = 40;
        ctx.fill();
        ctx.restore();

        ctx.beginPath();
        ctx.ellipse(locketX, locketY, locketRx + 16, locketRy + 16, 0, 0, Math.PI * 2);
        ctx.fillStyle = '#851d2c';
        ctx.fill();

        // Load & Draw Avatar inside Locket
        const avatarImg = await loadImgAsync(partnerPhotoUrl);
        if (avatarImg) {
          ctx.save();
          ctx.beginPath();
          ctx.ellipse(locketX, locketY, locketRx, locketRy, 0, 0, Math.PI * 2);
          ctx.clip();
          ctx.drawImage(avatarImg, locketX - locketRx, locketY - locketRy, locketRx * 2, locketRy * 2);
          ctx.restore();
        }

        // Locket Crown & Ribbon
        ctx.fillStyle = '#d4af37';
        ctx.beginPath();
        ctx.arc(locketX, locketY - locketRy - 20, 26, 0, Math.PI * 2);
        ctx.fill();

        // 5. Surrounding Uncropped Polaroid Mosaic Grid
        const polaroidSlots = [
          { x: 420, y: 560, w: 320, h: 360, rot: -0.04 },
          { x: 960, y: 520, w: 320, h: 360, rot: 0.02 },
          { x: 1520, y: 520, w: 320, h: 360, rot: -0.02 },
          { x: 2060, y: 560, w: 320, h: 360, rot: 0.04 },
          { x: 380, y: 1040, w: 330, h: 370, rot: 0.03 },
          { x: 920, y: 980, w: 330, h: 370, rot: -0.03 },
          { x: 1560, y: 980, w: 330, h: 370, rot: 0.03 },
          { x: 2100, y: 1040, w: 330, h: 370, rot: -0.03 },
          { x: 360, y: 1540, w: 330, h: 380, rot: -0.04 },
          { x: 2120, y: 1540, w: 330, h: 380, rot: 0.04 },
          { x: 380, y: 2040, w: 330, h: 370, rot: 0.03 },
          { x: 920, y: 2100, w: 330, h: 370, rot: -0.03 },
          { x: 1560, y: 2100, w: 330, h: 370, rot: 0.03 },
          { x: 2100, y: 2040, w: 330, h: 370, rot: -0.03 },
          { x: 420, y: 2540, w: 320, h: 360, rot: -0.03 },
          { x: 960, y: 2580, w: 320, h: 360, rot: 0.02 },
          { x: 1520, y: 2580, w: 320, h: 360, rot: -0.02 },
          { x: 2060, y: 2540, w: 320, h: 360, rot: 0.03 }
        ];

        for (let i = 0; i < polaroidSlots.length; i++) {
          const slot = polaroidSlots[i];
          const mediaItem = mediaList[i % mediaList.length];
          const photoImg = await loadImgAsync(mediaItem.url);

          ctx.save();
          ctx.translate(slot.x, slot.y);
          ctx.rotate(slot.rot);

          ctx.shadowColor = 'rgba(0, 0, 0, 0.18)';
          ctx.shadowBlur = 18;
          ctx.shadowOffsetX = 4;
          ctx.shadowOffsetY = 8;
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(-slot.w / 2, -slot.h / 2, slot.w, slot.h);

          ctx.shadowColor = 'transparent';
          ctx.lineWidth = 1.5;
          ctx.strokeStyle = '#e0c99a';
          ctx.strokeRect(-slot.w / 2 + 6, -slot.h / 2 + 6, slot.w - 12, slot.h - 12);

          const imgAreaW = slot.w - 24;
          const imgAreaH = slot.h - 70;
          const imgAreaX = -slot.w / 2 + 12;
          const imgAreaY = -slot.h / 2 + 12;

          ctx.fillStyle = '#f8f5ee';
          ctx.fillRect(imgAreaX, imgAreaY, imgAreaW, imgAreaH);

          if (photoImg) {
            const aspect = photoImg.width / photoImg.height;
            let drawW = imgAreaW;
            let drawH = imgAreaW / aspect;
            if (drawH > imgAreaH) {
              drawH = imgAreaH;
              drawW = imgAreaH * aspect;
            }
            const drawX = imgAreaX + (imgAreaW - drawW) / 2;
            const drawY = imgAreaY + (imgAreaH - drawH) / 2;
            ctx.drawImage(photoImg, drawX, drawY, drawW, drawH);
          }

          ctx.fillStyle = '#d4af37';
          const cornerSize = 14;
          ctx.fillRect(imgAreaX, imgAreaY, cornerSize, 3);
          ctx.fillRect(imgAreaX, imgAreaY, 3, cornerSize);
          ctx.fillRect(imgAreaX + imgAreaW - cornerSize, imgAreaY, cornerSize, 3);
          ctx.fillRect(imgAreaX + imgAreaW - 3, imgAreaY, 3, cornerSize);

          ctx.fillStyle = '#3a2414';
          ctx.font = '600 15px "Playfair Display", Georgia, serif';
          ctx.textAlign = 'center';
          const capText = (mediaItem.caption && mediaItem.caption.length > 28) 
            ? mediaItem.caption.substring(0, 26) + '...' 
            : (mediaItem.caption || 'Cherished Memory');
          ctx.fillText(capText, 0, slot.h / 2 - 18);

          ctx.restore();
        }

        ctx.textAlign = 'center';
        const plaqueY = 3020;
        ctx.fillStyle = '#d4af37';
        ctx.beginPath();
        ctx.roundRect(canvas.width / 2 - 450, plaqueY, 900, 75, 18);
        ctx.fill();

        ctx.fillStyle = '#241402';
        ctx.font = 'bold 28px "Cinzel", Georgia, serif';
        ctx.fillText('✦  SEALED WITH ETERNAL LOVE • RAKSHA BANDHAN 2026  ✦', canvas.width / 2, plaqueY + 48);

        ctx.fillStyle = '#851d2c';
        ctx.font = 'italic bold 44px "Playfair Display", cursive';
        ctx.fillText(`${partnerName} ♡`, canvas.width / 2 - 320, plaqueY + 180);
        ctx.fillText(`${buyerName} ♡`, canvas.width / 2 + 320, plaqueY + 180);

        ctx.fillStyle = '#7a4204';
        ctx.font = '800 18px "Cinzel", Georgia, serif';
        ctx.fillText("[SISTER'S SIGNATURE]", canvas.width / 2 - 320, plaqueY + 220);
        ctx.fillText("[BROTHER'S SIGNATURE]", canvas.width / 2 + 320, plaqueY + 220);

        ctx.fillStyle = '#7a5310';
        ctx.font = 'italic 20px "Playfair Display", Georgia, serif';
        ctx.fillText(`Protected by SoulScript Royal Archive • Verification ID: ${window.__certId || 'SS-RB-2026'} • 300 DPI Museum Edition`, canvas.width / 2, plaqueY + 310);

        const link = document.createElement('a');
        link.download = `Wall_Collage_Poster_${buyerName}_${partnerName}.png`;
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();

        if (btn) {
          btn.innerHTML = origText;
          btn.disabled = false;
        }
        if (typeof confetti === 'function') {
          confetti({ particleCount: 150, spread: 85, origin: { y: 0.6 } });
        }
      } catch (err) {
        console.error('Wall Poster Generation Error:', err);
        if (btn) {
          btn.innerHTML = origText;
          btn.disabled = false;
        }
        alert('Could not generate 300 DPI Wall Poster. Please try again.');
      }
    }

    // Helper: Convert HTMLImageElement to Clean Base64 Data URL (100% Reliable for jsPDF)
    function imgToDataUrl(img, type = 'image/jpeg', quality = 0.92) {
      if (!img) return null;
      try {
        const canvas = document.createElement('canvas');
        canvas.width = img.naturalWidth || img.width || 800;
        canvas.height = img.naturalHeight || img.height || 600;
        const ctx = canvas.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL(type, quality);
      } catch (e) {
        return null;
      }
    }

    // Helper: Render SVG to Data URL via Canvas
    function renderSvgToDataUrl(svgElement, width = 1600, height = 900) {
      if (!svgElement) return Promise.resolve(null);
      return new Promise((resolve) => {
        try {
          const svgClone = svgElement.cloneNode(true);
          svgClone.setAttribute('width', width.toString());
          svgClone.setAttribute('height', height.toString());
          const svgString = new XMLSerializer().serializeToString(svgClone);
          const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
          const URL = window.URL || window.webkitURL || window;
          const blobURL = URL.createObjectURL(svgBlob);

          const img = new Image();
          img.onload = function() {
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            URL.revokeObjectURL(blobURL);
            resolve(canvas.toDataURL('image/png'));
          };
          img.onerror = function() {
            URL.revokeObjectURL(blobURL);
            resolve(null);
          };
          img.src = blobURL;
        } catch (err) {
          resolve(null);
        }
      });
    }

    // ==========================================
    // 2. MULTI-PAGE KEEPSAKE PHOTOBOOK (PDF) - HIGH-RES CANVAS ENGINE
    // ==========================================
    async function downloadSiblingPhotobookPDF() {
      const btn = document.getElementById('btnPhotobook');
      const origText = btn ? btn.innerHTML : '';
      if (btn) {
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Compiling Luxury Photobook PDF...';
        btn.disabled = true;
      }

      try {
        if (!window.jspdf || !window.jspdf.jsPDF) {
          throw new Error('jsPDF library not loaded');
        }

        const partnerName = window.__partnerName || 'Sister';
        const buyerName = window.__buyerName || 'Brother';
        const partnerPhotoUrl = window.__partnerPhoto || '<?= APP_URL ?>/assets/default_gallery/sample_fa6955df.webp';
        const mediaList = (window.__giftMedia && window.__giftMedia.length > 0) ? window.__giftMedia : [];
        const loveNote = window.__loveNote || 'Thank you for being the most wonderful sibling in the universe!';
        const giftUrl = window.location.href;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
          orientation: 'landscape',
          unit: 'mm',
          format: 'a4'
        });

        const C_WIDTH = 2480;
        const C_HEIGHT = 1754;

        // Helper: Create a fresh High-Res Canvas Page
        const createPageCanvas = () => {
          const cvs = document.createElement('canvas');
          cvs.width = C_WIDTH;
          cvs.height = C_HEIGHT;
          const c = cvs.getContext('2d');
          c.imageSmoothingEnabled = true;
          c.imageSmoothingQuality = 'high';

          // Base Ivory Linen Background
          const grad = c.createLinearGradient(0, 0, C_WIDTH, C_HEIGHT);
          grad.addColorStop(0, '#fdfbf7');
          grad.addColorStop(0.5, '#f7f1e3');
          grad.addColorStop(1, '#fdfbf7');
          c.fillStyle = grad;
          c.fillRect(0, 0, C_WIDTH, C_HEIGHT);

          // Ornate 24K Gold & Crimson Border
          c.lineWidth = 10;
          c.strokeStyle = '#d4af37';
          c.strokeRect(50, 50, C_WIDTH - 100, C_HEIGHT - 100);

          c.lineWidth = 3;
          c.strokeStyle = '#851d2c';
          c.strokeRect(68, 68, C_WIDTH - 136, C_HEIGHT - 136);

          // Corner Filigrees
          const drawCorner = (cx, cy) => {
            c.fillStyle = '#b89343';
            c.beginPath();
            c.arc(cx, cy, 18, 0, Math.PI * 2);
            c.fill();
            c.fillStyle = '#851d2c';
            c.beginPath();
            c.arc(cx, cy, 8, 0, Math.PI * 2);
            c.fill();
          };
          drawCorner(68, 68);
          drawCorner(C_WIDTH - 68, 68);
          drawCorner(68, C_HEIGHT - 68);
          drawCorner(C_WIDTH - 68, C_HEIGHT - 68);

          return { cvs, c };
        };

        // ----------------------------------------------------
        // PAGE 1: ROYAL COVER
        // ----------------------------------------------------
        const page1 = createPageCanvas();
        const c1 = page1.c;

        c1.textAlign = 'center';
        c1.fillStyle = '#851d2c';
        c1.font = 'bold 36px "Cinzel", serif';
        c1.fillText('👑  SOULSCRIPT ROYAL KEEPSAKE ARCHIVE  👑', C_WIDTH / 2, 160);

        c1.fillStyle = '#402001';
        c1.font = '900 68px "Cinzel Decorative", "Playfair Display", Georgia, serif';
        c1.fillText('THE SACRED SIBLING STORY', C_WIDTH / 2, 250);

        c1.fillStyle = '#7a4204';
        c1.font = 'italic bold 34px "Playfair Display", Georgia, serif';
        c1.fillText('A Lifetime of Love, Laughter & Unbreakable Promises', C_WIDTH / 2, 315);

        // Center Large 24K Gold Oval Medallion Frame
        const p1AvatarImg = await loadImgAsync(partnerPhotoUrl);
        const locketX = C_WIDTH / 2;
        const locketY = 820;
        const locketRx = 340;
        const locketRy = 440;

        c1.save();
        c1.beginPath();
        c1.ellipse(locketX, locketY, locketRx + 32, locketRy + 32, 0, 0, Math.PI * 2);
        c1.fillStyle = '#d4af37';
        c1.shadowColor = 'rgba(184, 147, 67, 0.45)';
        c1.shadowBlur = 40;
        c1.fill();
        c1.restore();

        c1.beginPath();
        c1.ellipse(locketX, locketY, locketRx + 16, locketRy + 16, 0, 0, Math.PI * 2);
        c1.fillStyle = '#851d2c';
        c1.fill();

        if (p1AvatarImg) {
          c1.save();
          c1.beginPath();
          c1.ellipse(locketX, locketY, locketRx, locketRy, 0, 0, Math.PI * 2);
          c1.clip();
          c1.drawImage(p1AvatarImg, locketX - locketRx, locketY - locketRy, locketRx * 2, locketRy * 2);
          c1.restore();
        }

        c1.fillStyle = '#851d2c';
        c1.font = 'italic bold 64px "Playfair Display", cursive';
        c1.fillText(`${partnerName}  &  ${buyerName}`, C_WIDTH / 2, 1380);

        c1.fillStyle = '#7a5310';
        c1.font = '800 24px "Cinzel", Georgia, serif';
        c1.fillText('Issued on Raksha Bandhan 2026 • Official Digital Keepsake Archive', C_WIDTH / 2, 1460);

        doc.addImage(page1.cvs.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 297, 210);

        // ----------------------------------------------------
        // PAGE 2: SHAHI TAMRAPATRA CERTIFICATE (CANVAS EMBEDDED)
        // ----------------------------------------------------
        doc.addPage();
        const page2 = createPageCanvas();
        const c2 = page2.c;

        const certSvg = document.getElementById('shahiTamrapatraSvg');
        if (certSvg) {
          const certDataUrl = await renderSvgToDataUrl(certSvg, 2200, 1238);
          if (certDataUrl) {
            const certImg = await loadImgAsync(certDataUrl);
            if (certImg) {
              c2.drawImage(certImg, 140, 258, 2200, 1238);
            }
          }
        }
        doc.addImage(page2.cvs.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 297, 210);

        // ----------------------------------------------------
        // CHAPTER PAGES: SPREADS OF USER SCRAPBOOK PHOTOS (Large, beautiful, uncropped)
        // ----------------------------------------------------
        const chapterMeta = [
          { title: 'Chapter 1: Sweet Childhood Shenanigans 📺', quote: 'Fighting over the TV remote & hiding secret chocolate wrappers! 🍫' },
          { title: 'Chapter 2: Unfiltered Laughter & Secrets 🤫', quote: 'Crime partners in every mischief, sharing secrets the world will never know! ✨' },
          { title: 'Chapter 3: Lifelong Protection & Sacred Bond 💖', quote: 'Always standing as an unbreakable shield through every milestone of life! 🪔' },
          { title: 'Chapter 4: Precious Milestones & Adventures 🌸', quote: 'Celebrating irreplaceable memories etched in our hearts forever! 🌟' }
        ];

        const photosPerPage = 4;
        const totalPhotos = mediaList.length;
        const totalChapterPages = Math.max(1, Math.ceil(totalPhotos / photosPerPage));

        for (let chIdx = 0; chIdx < totalChapterPages; chIdx++) {
          doc.addPage();
          const pageObj = createPageCanvas();
          const c = pageObj.c;
          const meta = chapterMeta[chIdx % chapterMeta.length];

          // Chapter Header Banner
          c.textAlign = 'center';
          c.fillStyle = '#851d2c';
          c.font = 'bold 46px "Cinzel", serif';
          c.fillText(meta.title, C_WIDTH / 2, 150);

          c.fillStyle = '#7a4204';
          c.font = 'italic bold 26px "Playfair Display", Georgia, serif';
          c.fillText(`"${meta.quote}"`, C_WIDTH / 2, 205);

          // 4 Large Proportional Polaroid Slots
          const slots = [
            { x: 160, y: 260, w: 1040, h: 660, rot: -0.015 },
            { x: 1280, y: 260, w: 1040, h: 660, rot: 0.015 },
            { x: 160, y: 960, w: 1040, h: 660, rot: 0.015 },
            { x: 1280, y: 960, w: 1040, h: 660, rot: -0.015 }
          ];

          const startIdx = chIdx * photosPerPage;
          for (let p = 0; p < photosPerPage; p++) {
            const photoIdx = startIdx + p;
            if (photoIdx >= totalPhotos && totalPhotos > 0) break;

            const slot = slots[p];
            const mediaItem = mediaList[photoIdx % (totalPhotos || 1)] || { url: partnerPhotoUrl, caption: 'Cherished Memory' };
            const pImg = await loadImgAsync(mediaItem.url);

            c.save();
            c.translate(slot.x + slot.w / 2, slot.y + slot.h / 2);
            c.rotate(slot.rot);

            // Polaroid Card Matting with Soft Shadow
            c.shadowColor = 'rgba(0, 0, 0, 0.16)';
            c.shadowBlur = 24;
            c.shadowOffsetX = 4;
            c.shadowOffsetY = 10;
            c.fillStyle = '#ffffff';
            c.fillRect(-slot.w / 2, -slot.h / 2, slot.w, slot.h);

            // Gold Border Trim
            c.shadowColor = 'transparent';
            c.lineWidth = 2;
            c.strokeStyle = '#e0c99a';
            c.strokeRect(-slot.w / 2 + 10, -slot.h / 2 + 10, slot.w - 20, slot.h - 20);

            // Photo Bounding Box (Uncropped Proportional Fit)
            const imgAreaW = slot.w - 40;
            const imgAreaH = slot.h - 100;
            const imgAreaX = -slot.w / 2 + 20;
            const imgAreaY = -slot.h / 2 + 20;

            c.fillStyle = '#f8f5ee';
            c.fillRect(imgAreaX, imgAreaY, imgAreaW, imgAreaH);

            if (pImg) {
              const aspect = (pImg.naturalWidth || pImg.width || 800) / (pImg.naturalHeight || pImg.height || 600);
              let drawW = imgAreaW;
              let drawH = imgAreaW / aspect;
              if (drawH > imgAreaH) {
                drawH = imgAreaH;
                drawW = imgAreaH * aspect;
              }
              const drawX = imgAreaX + (imgAreaW - drawW) / 2;
              const drawY = imgAreaY + (imgAreaH - drawH) / 2;
              c.drawImage(pImg, drawX, drawY, drawW, drawH);
            }

            // Gold Photo Corners
            c.fillStyle = '#d4af37';
            const cLen = 22;
            c.fillRect(imgAreaX, imgAreaY, cLen, 5);
            c.fillRect(imgAreaX, imgAreaY, 5, cLen);
            c.fillRect(imgAreaX + imgAreaW - cLen, imgAreaY, cLen, 5);
            c.fillRect(imgAreaX + imgAreaW - 5, imgAreaY, 5, cLen);

            // Caption Text
            c.fillStyle = '#3a2414';
            c.font = '600 24px "Playfair Display", Georgia, serif';
            c.textAlign = 'center';
            const capText = (mediaItem.caption && mediaItem.caption.length > 40) 
              ? mediaItem.caption.substring(0, 38) + '...' 
              : (mediaItem.caption || 'Cherished Memory');
            c.fillText(capText, 0, slot.h / 2 - 25);

            c.restore();
          }

          doc.addImage(pageObj.cvs.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 297, 210);
        }

        // ----------------------------------------------------
        // FINAL PAGE: ROYAL SHAGUN LETTER & DIGITAL QR PORTAL
        // ----------------------------------------------------
        doc.addPage();
        const lastPage = createPageCanvas();
        const cEnd = lastPage.c;

        cEnd.textAlign = 'center';
        cEnd.fillStyle = '#851d2c';
        cEnd.font = 'bold 46px "Cinzel", serif';
        cEnd.fillText('👑  THE ROYAL SHAGUN LETTER  👑', C_WIDTH / 2, 160);

        // Letter Box
        const letterBoxW = C_WIDTH - 360;
        const letterBoxH = 680;
        const letterBoxX = 180;
        const letterBoxY = 220;

        cEnd.fillStyle = '#ffffff';
        cEnd.strokeStyle = '#d4af37';
        cEnd.lineWidth = 4;
        cEnd.beginPath();
        cEnd.roundRect(letterBoxX, letterBoxY, letterBoxW, letterBoxH, 24);
        cEnd.fill();
        cEnd.stroke();

        cEnd.fillStyle = '#3a200a';
        cEnd.font = 'italic 34px "Playfair Display", Georgia, serif';
        cEnd.textAlign = 'center';

        const words = loveNote.split(' ');
        let line = '';
        let lineY = letterBoxY + 120;
        for (let n = 0; n < words.length; n++) {
          const testLine = line + words[n] + ' ';
          const metrics = cEnd.measureText(testLine);
          if (metrics.width > letterBoxW - 140 && n > 0) {
            cEnd.fillText(line, C_WIDTH / 2, lineY);
            line = words[n] + ' ';
            lineY += 56;
          } else {
            line = testLine;
          }
        }
        cEnd.fillText(line, C_WIDTH / 2, lineY);

        cEnd.fillStyle = '#851d2c';
        cEnd.font = 'italic bold 42px "Playfair Display", cursive';
        cEnd.fillText(`— With eternal love & lifelong protection, ${buyerName}`, letterBoxX + letterBoxW - 400, letterBoxY + letterBoxH - 60);

        // Dynamic QR Code Portal
        const qrContainer = document.createElement('div');
        new QRCode(qrContainer, { text: giftUrl, width: 340, height: 340, correctLevel: QRCode.CorrectLevel.H });
        await new Promise(r => setTimeout(r, 400));
        const qrCanvas = qrContainer.querySelector('canvas');
        if (qrCanvas) {
          cEnd.fillStyle = '#ffffff';
          cEnd.fillRect(C_WIDTH / 2 - 150, 960, 300, 300);
          cEnd.strokeStyle = '#d4af37';
          cEnd.lineWidth = 4;
          cEnd.strokeRect(C_WIDTH / 2 - 150, 960, 300, 300);
          cEnd.drawImage(qrCanvas, C_WIDTH / 2 - 135, 975, 270, 270);
        }

        cEnd.fillStyle = '#7a4204';
        cEnd.font = 'bold 30px "Cinzel", Georgia, serif';
        cEnd.fillText('SCAN TO RELIVE YOUR DIGITAL CELEBRATION', C_WIDTH / 2, 1340);

        cEnd.font = 'italic 24px "Playfair Display", Georgia, serif';
        cEnd.fillText('Scan with any smartphone camera to play music & experience the interactive rituals anytime at SoulScript.', C_WIDTH / 2, 1400);

        doc.addImage(lastPage.cvs.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 297, 210);

        doc.save(`Sibling_Memory_Book_${buyerName}_${partnerName}.pdf`);

        if (btn) {
          btn.innerHTML = origText;
          btn.disabled = false;
        }
        if (typeof confetti === 'function') {
          confetti({ particleCount: 180, spread: 90, origin: { y: 0.6 } });
        }
      } catch (err) {
        console.error('Photobook PDF Generation Error:', err);
        if (btn) {
          btn.innerHTML = origText;
          btn.disabled = false;
        }
        alert('Could not generate Photobook PDF. Please try again.');
      }
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
