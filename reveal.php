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

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[20%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[150px]"></div>
    <div class="absolute bottom-[10%] right-[10%] w-[45vw] h-[45vw] rounded-full bg-[#eac34a]/10 blur-[130px]"></div>
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
      <form id="verifyForm" onsubmit="handleVerifySubmit(event)" class="space-y-4 text-xs">
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
      if (!url) return null;
      let match = url.match(/\/shorts\/([a-zA-Z0-9_-]+)/);
      if (match && match[1]) return match[1];
      match = url.match(/[?&]v=([a-zA-Z0-9_-]+)/);
      if (match && match[1]) return match[1];
      match = url.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
      if (match && match[1]) return match[1];
      match = url.match(/\/embed\/([a-zA-Z0-9_-]+)/);
      if (match && match[1]) return match[1];
      return null;
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
    loadLockMetadata();

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
        const res = await fetch('<?php echo APP_URL; ?>/api/verify_hint.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ slug: currentSlug, answer: input.value.trim() })
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

      // Resolve Music Track & Titles
      let finalAudioUrl = content.bg_music_url || 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3';
      let finalSongTitle = content.song_title || tf.song_title || '';
      let finalArtist = content.song_artist || '';

      if (finalAudioUrl === 'random_singer' || !finalAudioUrl) {
        const singerKey = (content.favorite_singers || 'arijit singh').toLowerCase();
        const matchedList = SINGER_PLAYLISTS[singerKey] || SINGER_PLAYLISTS['arijit singh'];
        const randomTrack = matchedList[Math.floor(Math.random() * matchedList.length)];
        finalAudioUrl = randomTrack.url;
        if (!finalSongTitle || finalSongTitle === 'Random Hit Track') {
          finalSongTitle = randomTrack.title;
        }
        if (!finalArtist) {
          finalArtist = content.favorite_singers || 'Arijit Singh';
        }
      }

      if (!finalSongTitle) finalSongTitle = 'Acoustic Sunset Love';
      if (!finalArtist) finalArtist = 'Romantic Track';

      // Check if bg_music_url is a YouTube Video or Shorts URL
      const ytVideoId = extractYouTubeId(content.bg_music_url);

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

      // Music box stays hidden until user taps Song badge

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

      if (templateId === 'anniversary_reveal') {

        let html = `
        <!-- Hero Header & Custom Quote Section -->
        <section class="relative pt-20 pb-8 px-4 text-center z-10">
          <div class="max-w-4xl mx-auto space-y-6">
            
            <!-- Circular Gift Receiver Avatar Frame -->
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto group mb-2">
              <div class="w-full h-full rounded-full p-1 bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] shadow-[0_0_30px_rgba(234,195,74,0.4)] transition-transform duration-300 group-hover:scale-105">
                ${photoAvatarHtml}
              </div>
              ${isEditMode ? `
                <button onclick="triggerReceiverPhotoUpload()" class="absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center text-white text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer border-2 border-[#eac34a]">
                  <i data-lucide="camera" class="w-4 h-4 text-[#eac34a] mb-0.5"></i>
                  <span>Change Photo</span>
                </button>
              ` : ''}
            </div>

            <!-- Floating Romantic Quote Banner -->
            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#3b1e3b]/80 border border-[#e4b9df]/40 text-[#eac34a] text-xs font-bold shadow-lg backdrop-blur-md">
              <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
              <span class="font-serif italic text-sm tracking-wide">"${content.tagline_quote || 'Safar Khubsurat h manjil se bhi 🌹'}"</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3] tracking-tight leading-tight">
              ${content.partner_name}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#e4b9df]">Forever Cherished</span>
            </h1>

            <p class="text-xs sm:text-sm text-[#d0c3cb] max-w-md mx-auto italic font-serif leading-relaxed">
              "You are my today, my tomorrow, and all of my beautiful memories in between."
            </p>
          </div>
        </section>

        <!-- SECTION 1: LIVE COUNTER -->
        <section class="max-w-2xl mx-auto px-4 py-6 relative z-10">
          <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 shadow-2xl text-center space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-[#eac34a] flex items-center justify-center gap-1.5">
              <i data-lucide="clock" class="w-4 h-4 text-[#eac34a]"></i>
              <span>Counting Every Second Together</span>
            </span>

            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 pt-2">
              <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntY">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Years</span></div>
              <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntM">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Months</span></div>
              <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntD">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Days</span></div>
              <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntH">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Hours</span></div>
              <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntMin">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Mins</span></div>
              <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntSec">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Secs</span></div>
            </div>
          </div>
        </section>

        <!-- SECTION 2: LOVE NOTE CENTERPIECE -->
        <section class="max-w-3xl mx-auto px-4 py-6 relative z-10">
          <div class="bg-[#221f21] p-8 sm:p-12 rounded-3xl border border-[#eac34a]/40 shadow-2xl text-center space-y-4">
            <i data-lucide="feather" class="w-8 h-8 text-[#eac34a] mx-auto"></i>
            <p class="font-serif text-lg sm:text-xl italic text-[#e8e0e3] leading-relaxed">
              "${content.love_note_text || 'Happy Anniversary my love!'}"
            </p>
            <p class="font-handwriting text-3xl text-[#eac34a] pt-4">— Forever yours, ${content.buyer_name}</p>
          </div>
        </section>

        <!-- SECTION 3: OUR PRECIOUS MILESTONES TIMELINE -->
        <section class="max-w-3xl mx-auto px-4 py-8 relative z-10">
          <div class="text-center space-y-2 mb-12">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">OUR LOVE ROAD</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Our Precious Milestones</h2>
            <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
          </div>

          <div class="relative border-l-2 border-[#eac34a]/40 ml-4 sm:ml-32 space-y-8">
            ${(tf.milestones || []).map(m => {
              const dateObj = new Date(m.date || m.milestone_date || '2022-08-15');
              const formattedDate = dateObj.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
              return `
                <div class="relative pl-6 sm:pl-8 group">
                  <div class="absolute -left-[9px] top-2 w-4 h-4 rounded-full bg-[#eac34a] border-4 border-[#151215] shadow-md group-hover:scale-125 transition-transform"></div>
                  <div class="sm:absolute sm:-left-32 sm:top-1 text-xs font-bold text-[#eac34a] mb-1 sm:mb-0 sm:w-24 sm:text-right font-mono tracking-wider">
                    ${formattedDate}
                  </div>
                  <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] hover:border-[#eac34a]/60 shadow-xl transition-all space-y-1.5">
                    <h3 class="text-base font-bold font-serif text-[#e8e0e3]">${m.title}</h3>
                    <p class="text-xs text-[#d0c3cb] leading-relaxed font-sans">${m.description}</p>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
        </section>

        <!-- SECTION 4: PHOTO SCRAPBOOK GALLERY -->
        <section class="max-w-5xl mx-auto px-4 py-12 relative z-10 space-y-8">
          <div class="text-center space-y-2 mb-8">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">PHOTO MEMORIES</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Our Photo Scrapbook</h2>
            <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
          </div>

          <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
            ${media.map(m => {
              const imgUrl = normalizeMediaUrlJs(m.file_path);
              const capText = escapeHtml(m.caption || 'Sweet Moments');
              return `
                <div onclick="openLightbox('${imgUrl}')" class="break-inside-avoid rounded-2xl overflow-hidden border border-[#4d444b] group relative cursor-pointer hover:border-[#eac34a]/70 transition-all bg-[#151215] shadow-xl">
                  <img src="${imgUrl}" onerror="this.onerror=null; this.style.background='#221f21'; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23221f21%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23eac34a%22 font-size=%2228%22%3E📷%3C/text%3E%3C/svg%3E'" class="w-full h-auto object-contain block group-hover:scale-[1.02] transition-transform duration-500">
                  <div class="p-3 bg-[#221f21] border-t border-[#4d444b]/40 text-left">
                    <span class="text-[11px] font-bold text-[#eac34a] block">${capText}</span>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
        </section>

        <!-- SECTION 5: SEALED LETTERS -->
        <section class="max-w-4xl mx-auto px-4 py-12 relative z-10 space-y-8">
          <div class="text-center space-y-2 mb-8">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">ENCHANTED LETTER JAR</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Wax-Sealed Love Letters 💌</h2>
            <p class="text-xs text-[#d0c3cb]">Tap any envelope below to break the wax seal and open the letter.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            ${(letters.length > 0 ? letters : [
              { title: 'The First Magical Spark', category: 'A Beautiful Beginning', content: 'My Dearest Ananya, I often find myself thinking back to the very first moment our paths crossed. Before I met you, the days carried a quiet rhythm, but your laughter turned everything into music.' },
              { title: 'Our Silent Sacred Promise', category: 'A Heartfelt Oath', content: 'Here is my little vow to you: I promise to stand by your side through stormy afternoon skies and calm golden mornings forever. You are my home.' }
            ]).map((l, idx) => `
              <div onclick="openLetterModal('${l.title.replace(/'/g, "\\'")}', '${(l.category || 'Love Note').replace(/'/g, "\\'")}', '${l.content.replace(/'/g, "\\'").replace(/\n/g, "<br>")}')" class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 hover:border-[#eac34a] shadow-2xl space-y-4 cursor-pointer group transition-all">
                <div class="flex items-center justify-between">
                  <span class="text-[10px] uppercase tracking-widest text-[#eac34a] font-bold bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#e4b9df]/20">${l.category || 'LOVE LETTER'}</span>
                  <span class="text-xs text-rose-400 font-bold flex items-center gap-1">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    <span>SEALED</span>
                  </span>
                </div>
                <h3 class="text-xl font-bold font-serif text-[#e8e0e3] group-hover:text-[#eac34a] transition-colors">${l.title}</h3>
                <p class="text-xs text-[#d0c3cb] line-clamp-3 leading-relaxed font-serif italic">"${l.content}"</p>
                <div class="pt-2 text-center">
                  <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider group-hover:bg-[#ffe088] shadow-md transition-all">
                    <span>Break Wax Seal &amp; Read</span>
                    <i data-lucide="mail-open" class="w-3.5 h-3.5"></i>
                  </span>
                </div>
              </div>
            `).join('')}
          </div>
        </section>

        <!-- SECTION 6: LOVE TOKENS & REDEEMABLE COUPONS -->
        <section class="max-w-4xl mx-auto px-4 py-12 relative z-10 space-y-8">
          <div class="text-center space-y-2 mb-8">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">REDEEMABLE COUPONS</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Romantic Love Tokens 🎟️</h2>
            <p class="text-xs text-[#d0c3cb]">Tap any coupon below anytime to redeem it with ${content.buyer_name}.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            ${(tokens.length > 0 ? tokens : [
              { id: 1, title: '1 Free Warm Hug', badge: 'Hug', description: 'Redeemable anytime for a long, tight hug when you need it most.' },
              { id: 2, title: 'Late Night Ice Cream Date', badge: 'Treat', description: 'Redeemable for a midnight drive to your favorite ice cream parlor.' },
              { id: 3, title: 'Movie Night Choice', badge: 'Movie', description: 'You pick the movie, I make the popcorn and no complaints!' }
            ]).map((t, idx) => `
              <div id="tokenCard-${idx}" class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 shadow-2xl text-center space-y-4 relative overflow-hidden">
                <span class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#e4b9df]/20">${t.badge || 'COUPON'}</span>
                <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">${t.title}</h3>
                <p class="text-xs text-[#d0c3cb] leading-relaxed">${t.description}</p>
                <button onclick="redeemToken(${idx})" id="redeemBtn-${idx}" class="w-full py-2.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5">
                  <i data-lucide="ticket" class="w-4 h-4"></i>
                  <span>Redeem Coupon ♥</span>
                </button>
              </div>
            `).join('')}
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

        container.innerHTML = html;
        lucide.createIcons();
        startLiveCounter(startDate);

      } else if (templateId === 'perfect_proposal') {
        const existingResp = data.proposal_response;

        html = `
          <section class="relative pt-20 pb-16 px-4 text-center z-10">
            <div class="max-w-4xl mx-auto space-y-6">
              <!-- Circular Gift Receiver Avatar Frame -->
              <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto group mb-2">
                <div class="w-full h-full rounded-full p-1 bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] shadow-[0_0_30px_rgba(234,195,74,0.4)] transition-transform duration-300 group-hover:scale-105">
                  ${photoAvatarHtml}
                </div>
                ${isEditMode ? `
                  <button onclick="triggerReceiverPhotoUpload()" class="absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center text-white text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer border-2 border-[#eac34a]">
                    <i data-lucide="camera" class="w-4 h-4 text-[#eac34a] mb-0.5"></i>
                    <span>Change Photo</span>
                  </button>
                ` : ''}
              </div>

              <!-- Floating Romantic Quote Banner -->
              <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#3b1e3b]/80 border border-[#e4b9df]/40 text-[#eac34a] text-xs font-bold shadow-lg backdrop-blur-md">
                <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
                <span class="font-serif italic text-sm tracking-wide">"${content.tagline_quote || 'Safar Khubsurat h manjil se bhi 🌹'}"</span>
              </div>
              <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3]">
                Will You Marry Me, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#e4b9df]">${content.partner_name}</span>?
              </h1>
            </div>
          </section>

          <!-- Love Letter Centerpiece -->
          <section class="max-w-3xl mx-auto px-4 py-8 relative z-10">
            <div class="bg-[#221f21] p-8 sm:p-12 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6">
              <p class="font-serif text-base sm:text-lg text-[#e8e0e3] leading-relaxed whitespace-pre-line">
                ${tf.love_letter_text || content.love_note_text}
              </p>
              <p class="font-handwriting text-3xl text-[#eac34a] text-right pt-4">— Forever yours, ${content.buyer_name}</p>
            </div>
          </section>

          <!-- Photo Gallery -->
          <section class="max-w-4xl mx-auto px-4 py-12 relative z-10 space-y-8">
            <h2 class="text-2xl font-bold font-serif text-center text-[#e8e0e3]">Captured Memories</h2>
            <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
              ${media.map(m => {
                const imgUrl = normalizeMediaUrlJs(m.file_path);
                const capText = escapeHtml(m.caption || 'Precious Moments');
                return `
                  <div onclick="openLightbox('${imgUrl}')" class="break-inside-avoid rounded-2xl overflow-hidden border border-[#4d444b] group relative cursor-pointer hover:border-[#eac34a]/70 transition-all bg-[#151215] shadow-xl">
                    <img src="${imgUrl}" onerror="this.onerror=null; this.style.background='#221f21'; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23221f21%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23eac34a%22 font-size=%2228%22%3E📷%3C/text%3E%3C/svg%3E'" class="w-full h-auto object-contain block group-hover:scale-[1.02] transition-transform duration-500">
                    <div class="p-3 bg-[#221f21] border-t border-[#4d444b]/40 text-left">
                      <span class="text-[11px] font-bold text-[#eac34a] block">${capText}</span>
                    </div>
                  </div>
                `;
              }).join('')}
            </div>
          </section>

          <!-- Response Capture Buttons -->
          <section class="max-w-xl mx-auto px-4 py-8 relative z-10">
            <div id="proposalResponseSection" class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 text-center space-y-6">
              ${existingResp ? `
                <div class="space-y-4">
                  <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center mx-auto shadow-lg">
                    <i data-lucide="check-circle-2" class="w-7 h-7 text-[#eac34a]"></i>
                  </div>
                  <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#eac34a]">Your Answer 💕</span>
                    <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">
                      ${existingResp.response === 'yes' ? 'YES! A Thousand Times Yes 💍' : "Let's Talk 💕"}
                    </h3>
                    <p class="text-xs text-[#d0c3cb]/80">Responded on ${existingResp.responded_at_formatted || 'Recently'}</p>
                  </div>
                  <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] text-sm font-serif italic text-[#eac34a] max-w-md mx-auto shadow-inner">
                    "${existingResp.partner_note ? existingResp.partner_note : (existingResp.response === 'yes' ? 'YES! A thousand times YES my love! 💕' : 'Let\'s talk and celebrate together! 💕')}"
                  </div>
                  <p class="text-[11px] text-[#d0c3cb]/60 italic pt-1">${content.buyer_name} has been notified!</p>
                </div>
              ` : `
                <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Your Answer</h3>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                  <button onclick="submitProposalAnswer('${data.page_id}', 'yes')" class="px-8 py-4 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-full hover:bg-[#ffe088] shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="heart" class="w-4 h-4 fill-current"></i>
                    <span>YES! A Thousand Times Yes</span>
                  </button>
                  <button onclick="submitProposalAnswer('${data.page_id}', 'lets_talk')" class="px-6 py-4 bg-[#151215] text-[#e8e0e3] border border-[#4d444b] font-bold text-xs uppercase tracking-wider rounded-full hover:border-[#eac34a] flex items-center justify-center gap-2 cursor-pointer">
                    <span>Let's Talk &amp; Celebrate</span>
                  </button>
                </div>
              `}
            </div>
          </section>
        `;

        container.innerHTML = html;
        lucide.createIcons();

      } else if (templateId === 'birthday_magic') {
        const dobStr = tf.partner_dob || '1998-11-20';
        const reasons = tf.reasons || [];

        html = `
          <section class="relative pt-20 pb-16 px-4 text-center z-10">
            <div class="max-w-4xl mx-auto space-y-6">
              <!-- Circular Gift Receiver Avatar Frame -->
              <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto group mb-2">
                <div class="w-full h-full rounded-full p-1 bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] shadow-[0_0_30px_rgba(234,195,74,0.4)] transition-transform duration-300 group-hover:scale-105">
                  ${photoAvatarHtml}
                </div>
                ${isEditMode ? `
                  <button onclick="triggerReceiverPhotoUpload()" class="absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center text-white text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer border-2 border-[#eac34a]">
                    <i data-lucide="camera" class="w-4 h-4 text-[#eac34a] mb-0.5"></i>
                    <span>Change Photo</span>
                  </button>
                ` : ''}
              </div>

              <!-- Floating Tagline / Motto Banner -->
              <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#3b1e3b]/80 border border-[#e4b9df]/40 text-[#eac34a] text-xs font-bold shadow-lg backdrop-blur-md">
                <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
                <span class="font-serif italic text-sm tracking-wide">"${content.tagline_quote || 'Cheers to another year of awesome memories! 🥂'}"</span>
              </div>

              <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3] tracking-tight leading-tight">
                Happy Birthday, ${content.partner_name}! 🎉
              </h1>

              <p class="text-xs sm:text-sm text-[#d0c3cb] max-w-md mx-auto leading-relaxed">
                A special celebration page created with love &amp; best wishes by <strong class="text-[#eac34a]">${content.buyer_name}</strong>.
              </p>

              <!-- Next Birthday Countdown -->
              <div class="pt-4 max-w-xl mx-auto">
                <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 shadow-2xl space-y-3">
                  <span class="text-xs font-bold uppercase tracking-widest text-[#eac34a] flex items-center justify-center gap-1.5">
                    <i data-lucide="clock" class="w-4 h-4 text-[#eac34a]"></i>
                    <span>Countdown to Your Next Birthday</span>
                  </span>

                  <div class="grid grid-cols-4 gap-3 pt-1">
                    <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
                      <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdayDays">0</span>
                      <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Days</span>
                    </div>
                    <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
                      <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdayHours">0</span>
                      <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Hours</span>
                    </div>
                    <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
                      <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdayMins">0</span>
                      <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Mins</span>
                    </div>
                    <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
                      <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdaySecs">0</span>
                      <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Secs</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Reasons I Love Celebrating You -->
          ${reasons.length > 0 ? `
            <section class="max-w-3xl mx-auto px-4 py-12 relative z-10">
              <div class="text-center space-y-2 mb-12">
                <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">SPECIAL QUALITIES</span>
                <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Reasons I Love Celebrating You</h2>
                <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
              </div>

              <div class="space-y-4">
                ${reasons.map((r, i) => `
                  <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] hover:border-[#eac34a]/60 shadow-xl flex items-start gap-4 transition-all">
                    <div class="w-8 h-8 rounded-full bg-[#3b1e3b] border border-[#eac34a]/40 text-[#eac34a] font-bold text-sm flex items-center justify-center shrink-0">
                      ${i + 1}
                    </div>
                    <p class="text-xs sm:text-sm text-[#e8e0e3] font-medium leading-relaxed pt-1">${r}</p>
                  </div>
                `).join('')}
              </div>
            </section>
          ` : ''}

          <!-- Moments of Joy Photo Gallery -->
          ${media.length > 0 ? `
            <section class="max-w-5xl mx-auto px-4 py-12 relative z-10">
              <div class="text-center space-y-2 mb-12">
                <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">PHOTO MEMORIES</span>
                <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Moments of Joy</h2>
                <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
              </div>

              <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
                ${media.map(m => {
                  const imgUrl = normalizeMediaUrlJs(m.file_path);
                  const capText = escapeHtml(m.caption || 'Birthday Memory');
                  return `
                    <div onclick="openLightbox('${imgUrl}')" class="break-inside-avoid rounded-2xl overflow-hidden group cursor-pointer bg-[#151215] border border-[#4d444b] shadow-xl hover:border-[#eac34a]/70 transition-all">
                      <img src="${imgUrl}" onerror="this.onerror=null; this.style.background='#221f21'; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23221f21%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23eac34a%22 font-size=%2228%22%3E📷%3C/text%3E%3C/svg%3E'" alt="Moments of joy" class="w-full h-auto object-contain block group-hover:scale-[1.02] transition-transform duration-500">
                      <div class="p-3 bg-[#221f21] border-t border-[#4d444b]/40 text-left">
                        <span class="text-[11px] font-bold text-[#eac34a] block">${capText}</span>
                      </div>
                    </div>
                  `;
                }).join('')}
              </div>
            </section>
          ` : ''}

          <!-- Birthday Note Card -->
          <section class="max-w-2xl mx-auto px-4 py-12 relative z-10">
            <div class="bg-[#221f21] p-8 sm:p-10 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center relative overflow-hidden">
              <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
                <i data-lucide="feather" class="w-6 h-6"></i>
              </div>

              <div class="space-y-4">
                <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">Your Birthday Wish</h3>
                <p class="text-sm sm:text-base font-serif text-[#d0c3cb] italic leading-relaxed whitespace-pre-line">
                  "${content.love_note_text || 'Happy Birthday!'}"
                </p>
              </div>

              <div class="pt-4 border-t border-[#4d444b]/50">
                <span class="text-xs uppercase tracking-widest text-[#eac34a] font-bold block">With Love &amp; Best Wishes,</span>
                <span class="text-xl font-bold font-serif text-[#e8e0e3] mt-1 block">${content.buyer_name}</span>
              </div>
            </div>
          </section>

          <!-- Footer Bar -->
          <footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
            <p class="text-xs text-[#d0c3cb]">Made with love by <strong class="text-[#eac34a]">${content.buyer_name}</strong> for <strong class="text-[#eac34a]">${content.partner_name}</strong></p>
            <div class="flex items-center justify-center gap-3">
              <button onclick="relockGiftSession()" type="button" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer">
                <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>Lock Gift Page 🔒</span>
              </button>
            </div>
          </footer>
        `;

        container.innerHTML = html;
        lucide.createIcons();
        startBirthdayCountdown(dobStr);

      } else if (templateId === 'long_distance_love') {
        const buyerCity = tf.buyer_city || 'London';
        const buyerTz = tf.buyer_timezone || 'Europe/London';
        const partnerCity = tf.partner_city || 'Bangalore';
        const partnerTz = tf.partner_timezone || 'Asia/Kolkata';
        const reunionDateStr = tf.reunion_date || '2026-12-25';
        const playlistUrl = tf.playlist_url;

        html = `
          <section class="relative pt-20 pb-16 px-4 text-center z-10">
            <div class="max-w-4xl mx-auto space-y-6">
              <!-- Circular Gift Receiver Avatar Frame -->
              <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto group mb-2">
                <div class="w-full h-full rounded-full p-1 bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] shadow-[0_0_30px_rgba(234,195,74,0.4)] transition-transform duration-300 group-hover:scale-105">
                  ${photoAvatarHtml}
                </div>
                ${isEditMode ? `
                  <button onclick="triggerReceiverPhotoUpload()" class="absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center text-white text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer border-2 border-[#eac34a]">
                    <i data-lucide="camera" class="w-4 h-4 text-[#eac34a] mb-0.5"></i>
                    <span>Change Photo</span>
                  </button>
                ` : ''}
              </div>

              <!-- Floating Tagline / Quote Banner -->
              <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#3b1e3b]/80 border border-[#e4b9df]/40 text-[#eac34a] text-xs font-bold shadow-lg backdrop-blur-md">
                <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
                <span class="font-serif italic text-sm tracking-wide">"${content.tagline_quote || 'Miles apart but connected by heart ✈️'}"</span>
              </div>

              <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3] tracking-tight leading-tight">
                ${buyerCity} <span class="text-[#eac34a]">✈️</span> ${partnerCity}
              </h1>

              <p class="text-xs sm:text-sm text-[#d0c3cb] max-w-md mx-auto font-medium leading-relaxed">
                A real-time bridge connecting <strong class="text-[#eac34a]">${content.buyer_name}</strong> in ${buyerCity} and <strong class="text-[#eac34a]">${content.partner_name}</strong> in ${partnerCity}.
              </p>

              <!-- Dual Live City Clocks -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto pt-6">
                <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 space-y-2 shadow-2xl">
                  <span class="text-[11px] uppercase font-bold text-[#eac34a] block tracking-wider">
                    ${buyerCity} Time (${content.buyer_name})
                  </span>
                  <span class="text-3xl font-black font-mono text-[#e8e0e3] block" id="buyerClock">--:--</span>
                </div>

                <div class="bg-[#221f21] p-6 rounded-3xl border border-[#e4b9df]/30 space-y-2 shadow-2xl">
                  <span class="text-[11px] uppercase font-bold text-[#e4b9df] block tracking-wider">
                    ${partnerCity} Time (${content.partner_name})
                  </span>
                  <span class="text-3xl font-black font-mono text-[#e8e0e3] block" id="partnerClock">--:--</span>
                </div>
              </div>
            </div>
          </section>

          <!-- Live Reunion Countdown -->
          <section class="max-w-3xl mx-auto px-4 py-8 relative z-10">
            <div class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl text-center space-y-4">
              <div class="flex items-center justify-center gap-2 text-[#eac34a] text-xs font-bold uppercase tracking-widest">
                <i data-lucide="plane" class="w-4 h-4 text-[#eac34a]"></i>
                <span>Next Reunion Countdown</span>
              </div>

              <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">Counting Down Until We Meet Again</h3>

              <div class="grid grid-cols-4 gap-3 max-w-xl mx-auto pt-2">
                <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] text-center">
                  <span class="block text-2xl font-black text-[#eac34a] font-mono" id="reunionDays">0</span>
                  <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Days</span>
                </div>
                <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] text-center">
                  <span class="block text-2xl font-black text-[#eac34a] font-mono" id="reunionHours">0</span>
                  <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Hours</span>
                </div>
                <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] text-center">
                  <span class="block text-2xl font-black text-[#eac34a] font-mono" id="reunionMins">0</span>
                  <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Mins</span>
                </div>
                <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] text-center">
                  <span class="block text-2xl font-black text-[#eac34a] font-mono" id="reunionSecs">0</span>
                  <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Secs</span>
                </div>
              </div>
            </div>
          </section>

          <!-- Playlist Link Card -->
          ${playlistUrl ? `
            <section class="max-w-2xl mx-auto px-4 py-4 relative z-10">
              <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 flex items-center justify-center shrink-0">
                    <i data-lucide="music" class="w-5 h-5"></i>
                  </div>
                  <div>
                    <h4 class="font-bold text-xs text-[#e8e0e3]">Our Shared Long-Distance Playlist</h4>
                    <p class="text-[11px] text-[#d0c3cb]">Songs that keep us close across the distance</p>
                  </div>
                </div>

                <a href="${playlistUrl}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-xl bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs shrink-0 flex items-center gap-1.5 transition-colors cursor-pointer">
                  <span>Listen</span>
                  <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                </a>
              </div>
            </section>
          ` : ''}

          <!-- Our Journey Across Miles Gallery -->
          ${media.length > 0 ? `
            <section class="max-w-4xl mx-auto px-4 py-12 relative z-10">
              <div class="text-center space-y-2 mb-12">
                <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">OUR MEMORIES</span>
                <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Love Across Miles</h2>
                <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
              </div>

              <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
                ${media.map(m => {
                  const imgUrl = normalizeMediaUrlJs(m.file_path);
                  const capText = escapeHtml(m.caption || 'Distance Memory');
                  return `
                    <div onclick="openLightbox('${imgUrl}')" class="break-inside-avoid rounded-2xl overflow-hidden group cursor-pointer bg-[#151215] border border-[#4d444b] shadow-xl hover:border-[#eac34a]/70 transition-all">
                      <img src="${imgUrl}" onerror="this.onerror=null; this.style.background='#221f21'; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23221f21%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23eac34a%22 font-size=%2228%22%3E📷%3C/text%3E%3C/svg%3E'" alt="Distance memory" class="w-full h-auto object-contain block group-hover:scale-[1.02] transition-transform duration-500">
                      <div class="p-3 bg-[#221f21] border-t border-[#4d444b]/40 text-left">
                        <span class="text-[11px] font-bold text-[#eac34a] block">${capText}</span>
                      </div>
                    </div>
                  `;
                }).join('')}
              </div>
            </section>
          ` : ''}

          <!-- Love Note Card -->
          <section class="max-w-2xl mx-auto px-4 py-12 relative z-10">
            <div class="bg-[#221f21] p-8 sm:p-10 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center relative overflow-hidden">
              <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
                <i data-lucide="feather" class="w-6 h-6"></i>
              </div>

              <div class="space-y-4">
                <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">Until I Hold You Again</h3>
                <p class="text-sm sm:text-base font-serif text-[#d0c3cb] italic leading-relaxed whitespace-pre-line">
                  "${content.love_note_text || 'Thinking of you always.'}"
                </p>
              </div>

              <div class="pt-4 border-t border-[#4d444b]/50">
                <span class="text-xs uppercase tracking-widest text-[#eac34a] font-bold block">Always Yours,</span>
                <span class="text-xl font-bold font-serif text-[#e8e0e3] mt-1 block">${content.buyer_name}</span>
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

        container.innerHTML = html;
        lucide.createIcons();
        startLongDistanceClocks(buyerTz, partnerTz);
        startReunionCountdown(reunionDateStr);

      } else if (templateId === 'raksha_bandhan_special') {

        const promisesList = (reasons && reasons.length > 0) ? reasons : [
          "Always protect you and stand by your side 🛡️",
          "Keep all your deepest secrets safe 🤫",
          "Sponsor your favorite food and treat you 🍕",
          "Never let you feel alone, no matter where I am 💖",
          "Always be your forever crime partner 🕵️‍♂️"
        ];

        let voucherCode = '';
        if (tokens && tokens.length > 0) {
          voucherCode = tokens[0].shagun_voucher_code || tokens[0].code || '';
        }

        html = `
        <!-- SECTION 1: HERO HEADER & TAGLINE BANNER -->
        <section class="relative pt-20 pb-8 px-4 text-center z-10">
          <div class="max-w-4xl mx-auto space-y-6">
            <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[3px] mx-auto shadow-[0_0_35px_rgba(234,195,74,0.4)]">
              <div class="w-full h-full bg-[#151215] rounded-full overflow-hidden flex items-center justify-center">
                ${photoAvatarHtml}
              </div>
            </div>

            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#3b1e3b]/80 border border-[#eac34a]/40 backdrop-blur-md text-[#eac34a] text-xs sm:text-sm font-bold shadow-lg">
              <i data-lucide="flame" class="w-4 h-4 text-[#eac34a]"></i>
              <span>${content.tagline_quote || "World's Best Sister 👑"}</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3] tracking-tight leading-tight">
              Happy Raksha Bandhan, <span class="text-[#eac34a]">${content.partner_name}</span>! 🪔
            </h1>
            <p class="text-xs sm:text-base text-[#d0c3cb] max-w-xl mx-auto">
              A digital celebration of our unbreakable bond, childhood memories, and sacred vows.
            </p>
          </div>
        </section>

        <!-- SECTION 2: VIRTUAL RAKHI TYING CEREMONY -->
        <section class="max-w-3xl mx-auto px-4 py-8 relative z-10">
          <div class="bg-[#221f21]/90 border border-[#eac34a]/40 backdrop-blur-xl rounded-3xl p-6 sm:p-8 text-center space-y-6 shadow-[0_20px_50px_rgba(0,0,0,0.8)]">
            <div class="w-16 h-16 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center mx-auto shadow-lg">
              <i data-lucide="sparkles" class="w-8 h-8 text-[#eac34a]"></i>
            </div>
            <div>
              <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block mb-1">SACRED THREAD OF LOVE</span>
              <h2 class="text-2xl sm:text-3xl font-bold font-serif text-[#e8e0e3]">Virtual Rakhi Ceremony 🧵</h2>
              <p class="text-xs text-[#d0c3cb] max-w-md mx-auto mt-2">Tap the golden thread below to complete the virtual Rakhi ritual with confetti and blessings!</p>
            </div>
            <div>
              <button onclick="tieVirtualRakhi()" id="tieRakhiBtn" type="button" class="px-8 py-4 rounded-full bg-gradient-to-r from-[#eac34a] via-[#e4b9df] to-[#cca830] text-[#241a00] font-extrabold text-xs sm:text-sm uppercase tracking-widest shadow-[0_0_30px_rgba(234,195,74,0.4)] hover:scale-105 transition-all cursor-pointer">
                Tie Virtual Rakhi 🧵
              </button>
            </div>
          </div>
        </section>

        <!-- SECTION 3: 5 SIBLING PROMISES FLIP CARDS -->
        <section class="max-w-5xl mx-auto px-4 py-10 relative z-10 space-y-8">
          <div class="text-center space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">BROTHER &amp; SISTER VOWS</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">5 Sibling Promises 🛡️</h2>
            <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            ${promisesList.map((pText, pIdx) => `
              <div class="bg-[#221f21] border border-[#4d444b] rounded-2xl p-6 space-y-4 hover:border-[#eac34a]/60 transition-all shadow-xl relative group">
                <div class="flex items-center justify-between">
                  <span class="w-8 h-8 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs flex items-center justify-center border border-[#eac34a]/30">#${pIdx + 1}</span>
                  <i data-lucide="shield-check" class="w-5 h-5 text-[#eac34a]"></i>
                </div>
                <p class="text-sm font-serif text-[#e8e0e3] leading-relaxed">
                  "${pText}"
                </p>
                <div class="text-[10px] text-[#eac34a] uppercase tracking-wider font-semibold">Promise to ${content.partner_name}</div>
              </div>
            `).join('')}
          </div>
        </section>

        <!-- SECTION 4: ALWAYS-VISIBLE DIGITAL SHAGUN ENVELOPE -->
        <section class="max-w-3xl mx-auto px-4 py-10 relative z-10">
          <div class="text-center space-y-2 mb-8">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">SPECIAL GIFT &amp; BLESSINGS</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Digital Shagun Envelope 🧧</h2>
            <p class="text-xs text-[#d0c3cb]">Tap the traditional Indian Shagun Lifafa below to open your message and gift voucher!</p>
          </div>

          <!-- Closed Envelope UI -->
          <div id="shagunEnvelopeContainer" onclick="toggleShagunLifafa()" class="bg-gradient-to-br from-[#800f1c] via-[#590a13] to-[#2b0509] border-2 border-[#eac34a] rounded-3xl p-8 text-center cursor-pointer shadow-[0_0_40px_rgba(234,195,74,0.3)] hover:scale-[1.02] transition-all relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-[#eac34a]/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="w-20 h-20 rounded-full bg-[#eac34a] text-[#241a00] flex items-center justify-center mx-auto shadow-[0_0_25px_rgba(234,195,74,0.6)] group-hover:rotate-12 transition-transform">
              <i data-lucide="mail-open" class="w-10 h-10 text-[#241a00]"></i>
            </div>
            <div class="space-y-2 mt-4">
              <span class="text-xs font-extrabold uppercase tracking-widest text-[#eac34a] bg-[#151215]/80 px-4 py-1 rounded-full border border-[#eac34a]/40 inline-block">ROYAL SHAGUN LIFAFA</span>
              <h3 class="text-2xl font-bold font-serif text-white">Tap to Open Envelope 🧧</h3>
              <p class="text-xs text-[#f5d77f]">Contains your personal message &amp; special gift code</p>
            </div>
          </div>

          <!-- Opened Envelope Content (Hidden by default, shown on click) -->
          <div id="shagunLetterContent" class="hidden bg-[#221f21] border-2 border-[#eac34a] rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-[#4d444b]/60 pb-4">
              <span class="text-xs font-bold text-[#eac34a] uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="heart" class="w-4 h-4 text-[#eac34a]"></i>
                <span>Shagun Letter from ${content.buyer_name}</span>
              </span>
              <button type="button" onclick="toggleShagunLifafa()" class="text-xs text-[#d0c3cb] hover:text-[#eac34a] font-bold">Close ✕</button>
            </div>

            <div class="space-y-4">
              <p class="font-serif text-base sm:text-lg text-[#e8e0e3] leading-relaxed italic bg-[#151215] p-5 rounded-2xl border border-[#4d444b]">
                "${content.love_note_text || "Choti / Didi, mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🧧 (Aur haan, TV remote mera hi रहेगा! 😄)"}"
              </p>

              ${voucherCode ? `
                <div class="p-4 bg-gradient-to-r from-[#eac34a]/20 via-[#e4b9df]/20 to-[#eac34a]/20 border-2 border-[#eac34a] rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-lg">
                  <div class="flex items-center gap-3 text-left">
                    <div class="w-10 h-10 rounded-xl bg-[#eac34a] text-[#241a00] flex items-center justify-center shrink-0 font-bold">🎁</div>
                    <div>
                      <span class="block text-[10px] uppercase font-bold text-[#eac34a] tracking-wider">Gift Voucher Code</span>
                      <strong class="text-base font-mono text-white tracking-widest">${voucherCode}</strong>
                    </div>
                  </div>
                  <button type="button" onclick="navigator.clipboard.writeText('${voucherCode}'); alert('Voucher code copied to clipboard!');" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider transition-colors shadow-md cursor-pointer">
                    Copy Code
                  </button>
                </div>
              ` : ''}
            </div>

            <div class="text-right pt-2">
              <span class="text-xs text-[#d0c3cb]">With lots of love,</span>
              <span class="block text-lg font-bold font-serif text-[#eac34a]">${content.buyer_name}</span>
            </div>
          </div>
        </section>

        <!-- SECTION 5: CHILDHOOD MEMORIES SCRAPBOOK -->
        <section class="max-w-5xl mx-auto px-4 py-12 relative z-10 space-y-8">
          <div class="text-center space-y-2 mb-8">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">TV REMOTE FIGHTS TO BEST FRIENDS</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Childhood Memories 📸</h2>
            <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
          </div>

          <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
            ${media.map(m => {
              const imgUrl = normalizeMediaUrlJs(m.file_path);
              const capText = escapeHtml(m.caption || 'Childhood Memory');
              return `
                <div onclick="openLightbox('${imgUrl}')" class="break-inside-avoid rounded-2xl overflow-hidden border border-[#4d444b] group relative cursor-pointer hover:border-[#eac34a]/70 transition-all bg-[#151215] shadow-xl">
                  <img src="${imgUrl}" onerror="this.onerror=null; this.style.background='#221f21'; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23221f21%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23eac34a%22 font-size=%2228%22%3E📷%3C/text%3E%3C/svg%3E'" class="w-full h-auto object-contain block group-hover:scale-[1.02] transition-transform duration-500">
                  <div class="p-3 bg-[#221f21] border-t border-[#4d444b]/40 text-left">
                    <span class="text-[11px] font-bold text-[#eac34a] block">${capText}</span>
                  </div>
                </div>
              `;
            }).join('')}
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

        container.innerHTML = html;
        lucide.createIcons();

      } else {
        // Fallback layout
        html = `
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
        container.innerHTML = html;
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
