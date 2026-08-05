<?php
require_once __DIR__ . '/config/db.php';

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

$editToken = trim($_GET['edit_token'] ?? '');
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
               c.partner_name, c.buyer_name, c.hint_question
        FROM pages p
        JOIN page_content c ON p.page_id = c.page_id
        WHERE LOWER(p.url_slug) = LOWER(?)
    ");
    $stmt->execute([$slug]);
    $initialLockData = $stmt->fetch();
} catch (Exception $e) {
    $initialLockData = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>A Secret Surprise For You ✨ — <?php echo APP_NAME; ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Caveat:wght@600;700&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            serif: ['"Bodoni Moda"', 'serif'],
            sans: ['Montserrat', 'sans-serif'],
            handwriting: ['Caveat', 'cursive'],
          }
        }
      }
    }
  </script>
  
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

<?php if ($isEditMode): ?>
<!-- TOP STICKY BUYER LIVE EDIT BAR -->
<div class="bg-gradient-to-r from-[#3b1e3b] via-[#221f21] to-[#3b1e3b] border-b border-[#eac34a]/60 px-4 py-3 sticky top-0 z-50 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-2xl text-xs">
  <div class="flex items-center gap-2">
    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
    <strong class="text-[#e8e0e3] font-bold">✏️ BUYER LIVE VISUAL EDITOR — Click any section's ✏️ icon to edit &amp; preview live!</strong>
  </div>
  <div class="flex items-center gap-3">
    <button onclick="openEditModal('security')" class="px-3.5 py-1.5 rounded-full bg-[#3b1e3b] border border-[#eac34a]/60 text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-[11px] transition-all flex items-center gap-1 cursor-pointer">
      <i data-lucide="key-round" class="w-3 h-3"></i>
      <span>Edit Passwords &amp; Security</span>
    </button>
    <a href="<?php echo APP_URL; ?>/gift/<?php echo htmlspecialchars($slug); ?>" target="_blank" class="px-3.5 py-1.5 rounded-full bg-[#151215] border border-[#eac34a]/40 text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-[11px] transition-all flex items-center gap-1">
      <i data-lucide="eye" class="w-3 h-3"></i>
      <span>View As Partner</span>
    </a>
    <a href="<?php echo APP_URL; ?>/edit.php" class="px-3.5 py-1.5 rounded-full bg-rose-950/80 border border-rose-500/40 text-rose-300 hover:bg-rose-600 hover:text-white font-bold text-[11px] transition-all">
      🔒 Logout / Exit Edit
    </a>
  </div>
</div>
<?php endif; ?>

  <!-- Top-Left Navigation Pill -->
  <style>
  @keyframes spinVinyl {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .spin-vinyl {
    animation: spinVinyl 3s linear infinite;
  }
</style>

<!-- Navbar -->
<header class="sticky top-0 z-40 bg-[#151215]/90 backdrop-blur-xl border-b border-[#4d444b]/30">
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <a href="<?php echo APP_URL; ?>" class="flex items-center gap-2 text-xs font-bold text-[#e8e0e3] hover:text-[#eac34a] transition-colors">
      <i data-lucide="arrow-left" class="w-4 h-4 text-[#eac34a]"></i>
      <span>SoulScript Home</span>
    </a>

    <div class="flex items-center gap-3">
      <?php if ($isEditMode): ?>
        <span class="px-3.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40">
          ✏️ Edit Mode Active
        </span>
      <?php else: ?>
        <a href="<?php echo APP_URL; ?>/edit.php" class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-[#eac34a]/60 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1.5">
          <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
          <span>Buyer Login</span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Floating Audio Player Box -->
<div class="fixed top-20 right-4 z-50 bg-[#221f21]/95 backdrop-blur-xl border border-[#eac34a]/40 rounded-2xl p-2.5 flex items-center gap-3 shadow-2xl">
  <audio id="bgAudio" src="https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3" loop preload="none"></audio>
  
  <!-- Vinyl Disc Graphic -->
  <div id="vinylDisc" class="w-9 h-9 rounded-full bg-black border-2 border-[#eac34a] flex items-center justify-center relative shadow-lg shrink-0">
    <div class="w-3 h-3 rounded-full bg-[#151215] border border-[#eac34a]"></div>
  </div>

  <button id="audioPlayBtn" onclick="toggleAudioPlay()" class="w-9 h-9 rounded-xl bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] flex items-center justify-center shadow-md transition-all shrink-0 cursor-pointer">
    <i data-lucide="play" class="w-4 h-4 fill-current ml-0.5"></i>
  </button>
  <div class="hidden sm:block text-[11px] pr-3 border-r border-[#4d444b]">
    <span class="block font-bold text-[#e8e0e3] line-clamp-1" id="musicBoxTitle">Tum Hi Ho</span>
    <span class="block text-[#eac34a] text-[10px] font-medium" id="musicBoxSinger">🎙️ Artist: Arijit Singh</span>
  </div>
  <button id="audioMuteBtn" onclick="toggleAudioMute()" class="p-1.5 text-[#d0c3cb] hover:text-white transition-colors cursor-pointer">
    <i data-lucide="volume-2" class="w-4 h-4 text-[#eac34a]"></i>
  </button>
</div>

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[20%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[150px]"></div>
    <div class="absolute bottom-[10%] right-[10%] w-[45vw] h-[45vw] rounded-full bg-[#eac34a]/10 blur-[130px]"></div>
  </div>

  <!-- STEP 7: LOCK SCREEN (Exact LockScreen.tsx DOM Layout) -->
  <main id="lockScreenView" class="min-h-screen flex flex-col items-center justify-center p-4 relative z-10">
    <div class="max-w-md w-full bg-[#221f21]/90 border border-[#eac34a]/30 backdrop-blur-xl rounded-3xl p-6 sm:p-8 space-y-6 shadow-[0_20px_50px_rgba(0,0,0,0.8)] relative my-8">
      
      <!-- Header Icon -->
      <div class="text-center space-y-3">
        <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] mx-auto shadow-[0_0_20px_rgba(234,195,74,0.3)]">
          <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
            <i data-lucide="key-round" class="w-7 h-7 text-[#eac34a]"></i>
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
          "<?php echo htmlspecialchars($initialLockData['hint_question'] ?? 'Where did we take our very first trip together in 2022?'); ?>"
        </p>
      </div>

      <!-- Hint Form -->
      <form id="verifyForm" onsubmit="handleVerifySubmit(event)" class="space-y-4 text-xs">
        <div id="lockMessage" class="hidden bg-[#3b1e3b] border border-[#e4b9df]/40 text-[#e4b9df] p-3.5 rounded-xl font-medium text-center text-xs"></div>

        <div>
          <label class="block text-[11px] uppercase tracking-wider text-[#d0c3cb] font-semibold mb-2 text-center">
            Enter Secret Password / Hint Answer
          </label>
          <input type="text" id="answerInput" class="w-full bg-[#151215] border border-[#4d444b] focus:border-[#eac34a] focus:ring-1 focus:ring-[#eac34a] rounded-xl px-4 py-3.5 text-sm text-[#e8e0e3] placeholder-[#d0c3cb]/40 font-mono text-center font-bold tracking-wider uppercase transition-all" placeholder="e.g. SHIMLA" required autocomplete="off">
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
    let lockData = null;

    let isPlaying = false;
    let isMuted = false;

    function toggleAudioPlay() {
      const audio = document.getElementById('bgAudio');
      const btn = document.getElementById('audioPlayBtn');
      if (isPlaying) {
        audio.pause();
        isPlaying = false;
        btn.innerHTML = '<i data-lucide="play" class="w-4 h-4 fill-white ml-0.5"></i>';
      } else {
        audio.play().then(() => {
          isPlaying = true;
          btn.innerHTML = '<i data-lucide="pause" class="w-4 h-4 fill-white"></i>';
        }).catch(err => console.log('Audio playback allowed on interaction:', err));
      }
      lucide.createIcons();
    }

    function toggleAudioMute() {
      const audio = document.getElementById('bgAudio');
      const btn = document.getElementById('audioMuteBtn');
      isMuted = !isMuted;
      audio.muted = isMuted;
      btn.innerHTML = isMuted ? '<i data-lucide="volume-x" class="w-4 h-4 text-rose-400"></i>' : '<i data-lucide="volume-2" class="w-4 h-4 text-[#eac34a]"></i>';
      lucide.createIcons();
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

    // AUTO-BYPASS LOCK SCREEN FOR BUYER EDIT MODE
    document.addEventListener('DOMContentLoaded', async () => {
      const isEditMode = <?php echo $isEditMode ? 'true' : 'false'; ?>;
      const editToken  = '<?php echo htmlspecialchars($editToken); ?>';
      if (isEditMode && editToken) {
        try {
          const res = await fetch('<?php echo APP_URL; ?>/api/verify_hint.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ slug: currentSlug, bypass_edit_token: editToken })
          });
          const data = await res.json();
          if (data.success) {
            lockData = data;
            document.getElementById('lockScreenView').classList.add('hidden');
            document.getElementById('resultPageView').classList.remove('hidden');
            renderResultPage(data);
          }
        } catch (err) {
          console.warn('Edit mode auto-bypass error:', err);
        }
      }
    });

    function renderResultPage(data) {
      const container = document.getElementById('resultContentContainer');
      const templateId = data.template_id;

      const content = data.content;
      const tf = content.template_fields || {};
      const media = content.media || [];
      const letters = content.letters || [];
      const tokens = content.tokens || [];

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

      // Resolve Music Track & Singer Badge
      let finalAudioUrl = content.bg_music_url || 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3';
      let finalSongTitle = tf.song_title || 'Acoustic Sunset Love';
      let singerName = content.favorite_singers || 'Arijit Singh & KK';

      if (finalAudioUrl === 'random_singer' || !finalAudioUrl || finalAudioUrl.includes('pixabay')) {
        const key = (singerName || 'arijit singh').toLowerCase();
        let matchedList = SINGER_PLAYLISTS['arijit singh'];
        for (const sKey in SINGER_PLAYLISTS) {
          if (key.includes(sKey)) {
            matchedList = SINGER_PLAYLISTS[sKey];
            break;
          }
        }
        const randomTrack = matchedList[Math.floor(Math.random() * matchedList.length)];
        finalAudioUrl = randomTrack.url;
        finalSongTitle = randomTrack.title + ' (Random Hit)';
      }

      // Update Floating Music Box Audio Source
      const audioElem = document.getElementById('bgAudio');
      if (audioElem) {
        audioElem.src = finalAudioUrl;
        audioElem.play().catch(e => console.log('Auto-play ready on user tap:', e));
      }
      if (document.getElementById('musicBoxTitle')) document.getElementById('musicBoxTitle').innerText = finalSongTitle;
      if (document.getElementById('musicBoxSinger')) document.getElementById('musicBoxSinger').innerText = '🎙️ ' + singerName;

      const startDate = tf.relationship_start_date ? new Date(tf.relationship_start_date) : new Date();
      const dobStr = tf.partner_dob || '1998-11-20';

      if (templateId === 'anniversary_reveal') {

        let html = `
        <!-- Custom Quote Badge & Hero Section -->
        <section class="relative pt-20 pb-12 px-4 text-center z-10">
          <div class="max-w-4xl mx-auto space-y-6">
            
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

            <!-- 4-Tabbed View Switcher -->
            <div class="pt-4 flex items-center justify-center gap-2 flex-wrap">
              <button onclick="switchRevealTab('journey')" id="revealTab-journey" class="px-5 py-2.5 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] shadow-lg transition-all cursor-pointer">📖 Our Journey</button>
              <button onclick="switchRevealTab('scrapbook')" id="revealTab-scrapbook" class="px-5 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all cursor-pointer">🖼️ Scrapbook</button>
              <button onclick="switchRevealTab('letters')" id="revealTab-letters" class="px-5 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all cursor-pointer">✉️ Sealed Letters (${letters.length || 2})</button>
              <button onclick="switchRevealTab('tokens')" id="revealTab-tokens" class="px-5 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all cursor-pointer">🎟️ Love Tokens (${tokens.length || 3})</button>
            </div>
          </div>
        </section>

        <!-- TAB 1: OUR JOURNEY -->
        <div id="revealTabContent-journey" class="space-y-12">
          
          <!-- Live Counter -->
          <section class="max-w-2xl mx-auto px-4 relative z-10">
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

          <!-- Love Note Centerpiece -->
          <section class="max-w-3xl mx-auto px-4 relative z-10">
            <div class="bg-[#221f21] p-8 sm:p-12 rounded-3xl border border-[#eac34a]/40 shadow-2xl text-center space-y-4">
              <i data-lucide="feather" class="w-8 h-8 text-[#eac34a] mx-auto"></i>
              <p class="font-serif text-lg sm:text-xl italic text-[#e8e0e3] leading-relaxed">
                "${content.love_note_text || 'Happy Anniversary my love!'}"
              </p>
              <p class="font-handwriting text-3xl text-[#eac34a] pt-4">— Forever yours, ${content.buyer_name}</p>
            </div>
          </section>

          <!-- Vertical Timeline Road -->
          <section class="max-w-3xl mx-auto px-4 relative z-10">
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

        </div>

        <!-- TAB 2: SCRAPBOOK -->
        <div id="revealTabContent-scrapbook" class="hidden max-w-5xl mx-auto px-4 relative z-10 space-y-8">
          <div class="text-center space-y-2 mb-8">
            <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">PHOTO MEMORIES</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Our Photo Scrapbook</h2>
            <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            ${media.map(m => `
              <div onclick="openLightbox('${m.file_path}')" class="aspect-4/5 rounded-2xl overflow-hidden border border-[#4d444b] group relative cursor-pointer hover:border-[#eac34a]/70 transition-all bg-[#221f21] shadow-xl">
                <img src="${m.file_path}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute bottom-0 inset-x-0 p-3 bg-gradient-to-t from-black/80 to-transparent text-left">
                  <span class="text-[10px] uppercase font-bold text-[#eac34a]">${m.caption || 'Sweet Moments'}</span>
                </div>
              </div>
            `).join('')}
          </div>
        </div>

        <!-- TAB 3: SEALED LETTERS -->
        <div id="revealTabContent-letters" class="hidden max-w-4xl mx-auto px-4 relative z-10 space-y-8">
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
        </div>

        <!-- TAB 4: LOVE TOKENS -->
        <div id="revealTabContent-tokens" class="hidden max-w-4xl mx-auto px-4 relative z-10 space-y-8">
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
        </div>

        <!-- Footer Bar -->
        <footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
          <p class="text-xs text-[#d0c3cb]">Made with endless love by <strong class="text-[#eac34a]">${content.buyer_name}</strong> for <strong class="text-[#eac34a]">${content.partner_name}</strong></p>
          <div class="flex items-center justify-center gap-3">
            <a href="${APP_URL}/edit" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all">
              <i data-lucide="edit" class="w-3.5 h-3.5 text-[#eac34a]"></i>
              <span>Unlock Editor Access 🔑</span>
            </a>
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
              <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#3b1e3b] border border-[#e4b9df]/30 text-[#e4b9df] text-xs font-semibold shadow-md">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span class="uppercase tracking-widest text-[11px]">A Special Question</span>
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
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
              ${media.map(m => `
                <div onclick="openLightbox('${m.file_path}')" class="aspect-4/5 rounded-2xl overflow-hidden border border-[#4d444b] group relative cursor-pointer hover:border-[#eac34a]/70 transition-all">
                  <img src="${m.file_path}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
              `).join('')}
            </div>
          </section>

          <!-- Response Capture Buttons -->
          <section class="max-w-xl mx-auto px-4 py-8 relative z-10">
            <div id="proposalResponseSection" class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 text-center space-y-6">
              ${existingResp ? `
                <i data-lucide="heart" class="w-12 h-12 text-[#eac34a] fill-[#eac34a] mx-auto"></i>
                <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">
                  You Answered: "${existingResp.response.toUpperCase() === 'YES' ? 'YES! 💍' : 'Let\'s Talk 💬'}"
                </h3>
                <p class="text-xs text-[#d0c3cb]">${content.buyer_name} has been notified!</p>
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
              <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#3b1e3b] border border-[#e4b9df]/30 text-[#eac34a] text-xs font-semibold shadow-md">
                <i data-lucide="cake" class="w-4 h-4 text-[#eac34a]"></i>
                <span class="uppercase tracking-widest text-[11px]">It's Time To Celebrate!</span>
              </div>

              <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3] tracking-tight leading-tight">
                Happy Birthday, ${content.partner_name}! 🎉
              </h1>

              <p class="text-xs sm:text-sm text-[#d0c3cb] max-w-md mx-auto leading-relaxed">
                A special celebration page created with endless love by <strong class="text-[#eac34a]">${content.buyer_name}</strong>.
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

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                ${media.map(m => `
                  <div onclick="openLightbox('${m.file_path}')" class="relative aspect-square rounded-2xl overflow-hidden group cursor-pointer bg-[#221f21] border border-[#4d444b] shadow-xl hover:border-[#eac34a]/70 transition-all">
                    <img src="${m.file_path}" alt="Moments of joy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 group-hover:opacity-100">
                  </div>
                `).join('')}
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
                <span class="text-xs uppercase tracking-widest text-[#eac34a] font-bold block">With Endless Love,</span>
                <span class="text-xl font-bold font-serif text-[#e8e0e3] mt-1 block">${content.buyer_name}</span>
              </div>
            </div>
          </section>
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
              <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#3b1e3b] border border-[#e4b9df]/30 text-[#eac34a] text-xs font-semibold shadow-md">
                <i data-lucide="globe" class="w-4 h-4 text-[#eac34a]"></i>
                <span class="uppercase tracking-widest text-[11px]">Miles Apart, Joined At Heart</span>
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

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                ${media.map(m => `
                  <div onclick="openLightbox('${m.file_path}')" class="relative aspect-square rounded-2xl overflow-hidden group cursor-pointer bg-[#221f21] border border-[#4d444b] shadow-xl hover:border-[#eac34a]/70 transition-all">
                    <img src="${m.file_path}" alt="Distance memory" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 group-hover:opacity-100">
                  </div>
                `).join('')}
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
        `;

        container.innerHTML = html;
        lucide.createIcons();
        startLongDistanceClocks(buyerTz, partnerTz);
        startReunionCountdown(reunionDateStr);

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
          document.getElementById('proposalResponseSection').innerHTML = `
            <i data-lucide="heart" class="w-12 h-12 text-[#eac34a] fill-[#eac34a] mx-auto"></i>
            <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">
              You Answered: "${answer.toUpperCase() === 'YES' ? 'YES! 💍' : 'Let\'s Talk 💬'}"
            </h3>
            <p class="text-xs text-[#d0c3cb]">${data.message}</p>
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

    // IN-PLACE LIVE VISUAL EDITOR ENGINE
    const isEditMode = <?php echo $isEditMode ? 'true' : 'false'; ?>;
    const activeEditToken = '<?php echo htmlspecialchars($editToken); ?>';

    function openLiveSectionEditor(section) {
      const modal = document.getElementById('liveEditorModal');
      const titleElem = document.getElementById('liveEditorTitle');
      const bodyElem = document.getElementById('liveEditorBody');

      if (!modal || !lockData) return;

      const content = lockData.content || {};
      const tf = content.template_fields || {};

      let html = '';

      if (section === 'quote') {
        titleElem.innerText = '✏️ Edit Tagline Quote & Note';
        html = `
          <div class="space-y-4 text-xs text-left">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Partner Name</label>
              <input type="text" id="livePartnerName" value="${escapeHtml(content.partner_name || '')}" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]">
            </div>

            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Tagline Quote Banner 🌹</label>
              <input type="text" id="liveTaglineQuote" value="${escapeHtml(content.tagline_quote || '')}" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]">
              
              <div class="pt-2 flex flex-wrap gap-1.5">
                <button type="button" onclick="document.getElementById('liveTaglineQuote').value='Safar Khubsurat h manjil se bhi 🌹'" class="px-2 py-1 bg-[#3b1e3b] text-[#eac34a] text-[10px] rounded-lg">Preset 1</button>
                <button type="button" onclick="document.getElementById('liveTaglineQuote').value='In your smile, I see something more beautiful than stars ✨'" class="px-2 py-1 bg-[#3b1e3b] text-[#eac34a] text-[10px] rounded-lg">Preset 2</button>
                <button type="button" onclick="document.getElementById('liveTaglineQuote').value='You are my favorite notification 💖'" class="px-2 py-1 bg-[#3b1e3b] text-[#eac34a] text-[10px] rounded-lg">Preset 3</button>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Love Note Message</label>
              <textarea id="liveLoveNoteText" rows="3" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3]">${escapeHtml(content.love_note_text || '')}</textarea>
            </div>
          </div>
        `;
      } else if (section === 'music') {
        titleElem.innerText = '🎵 Universal Live Song Search & Music';
        html = `
          <div class="space-y-4 text-xs text-left">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Search iTunes Song Database 🔍</label>
              <input type="text" oninput="handleItunesLiveSearch(this.value)" placeholder="Type song title or artist (e.g. Arijit Singh - Tum Hi Ho)..." class="w-full bg-[#151215] border border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]">
              <div id="liveItunesResults" class="hidden mt-2 max-h-48 overflow-y-auto space-y-1 bg-[#151215] border border-[#4d444b] p-2 rounded-xl"></div>
            </div>

            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Or YouTube Song URL 🎥</label>
              <input type="url" id="liveYoutubeUrl" value="${content.bg_music_url && content.bg_music_url.includes('youtube') ? escapeHtml(content.bg_music_url) : ''}" placeholder="https://www.youtube.com/watch?v=..." class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]">
            </div>

            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Selected Singer / Artist Name</label>
              <input type="text" id="liveFavoriteSingers" value="${escapeHtml(content.favorite_singers || 'Arijit Singh & KK')}" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]">
            </div>
          </div>
        `;
      } else if (section === 'security') {
        titleElem.innerText = '🔒 Edit Buyer Password & Hint Lock';
        html = `
          <div class="space-y-4 text-xs text-left">
            <div class="p-3 bg-[#3b1e3b]/60 border border-[#eac34a]/30 rounded-xl space-y-1">
              <span class="font-bold text-[#eac34a] block">🔑 Buyer Account Password</span>
              <p class="text-[10px] text-[#d0c3cb]">Password used to log in at soulscript.in/edit</p>
              <input type="password" id="liveBuyerPassword" placeholder="Enter new secret edit password (optional)" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]">
            </div>

            <div class="p-3 bg-[#151215] border border-[#4d444b] rounded-xl space-y-3">
              <span class="font-bold text-[#e8e0e3] block">🔐 Recipient Hint Question &amp; Secret Answer</span>
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Hint Question (Shown on lock screen)</label>
                <input type="text" id="liveHintQuestion" value="${escapeHtml(content.hint_question || '')}" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]">
              </div>
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Hint Secret Answer (Used to unlock)</label>
                <input type="text" id="liveHintAnswer" placeholder="Type new secret answer (leave blank to keep current)" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]">
              </div>
            </div>
          </div>
        `;
      } else {
        titleElem.innerText = '✏️ Live Section Customization';
        html = `<div class="text-xs text-[#d0c3cb]">Update content live. Click save below.</div>`;
      }

      bodyElem.innerHTML = html;
      document.getElementById('liveEditorSaveBtn').onclick = () => saveLiveSectionChanges(section);

      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeLiveEditorModal() {
      const modal = document.getElementById('liveEditorModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

    let liveItunesTimer = null;
    let liveChosenMusicUrl = '';
    let liveChosenSongTitle = '';
    let liveChosenArtist = '';

    function handleItunesLiveSearch(query) {
      clearTimeout(liveItunesTimer);
      const container = document.getElementById('liveItunesResults');
      if (!query.trim()) {
        container.classList.add('hidden');
        return;
      }

      liveItunesTimer = setTimeout(async () => {
        try {
          container.innerHTML = '<div class="p-2 text-center text-xs text-[#eac34a]">Searching iTunes...</div>';
          container.classList.remove('hidden');

          const res = await fetch(`https://itunes.apple.com/search?term=${encodeURIComponent(query)}&entity=song&limit=6`);
          const data = await res.json();

          if (data.results && data.results.length > 0) {
            container.innerHTML = data.results.map(item => `
              <div class="p-2 bg-[#221f21] hover:bg-[#3b1e3b] rounded-lg flex items-center justify-between transition-all">
                <div class="flex items-center gap-2 overflow-hidden">
                  <img src="${item.artworkUrl60 || item.artworkUrl100}" class="w-8 h-8 rounded-md object-cover">
                  <div class="truncate">
                    <span class="block font-bold text-xs text-[#e8e0e3] truncate">${escapeHtml(item.trackName)}</span>
                    <span class="block text-[10px] text-[#d0c3cb] truncate">${escapeHtml(item.artistName)}</span>
                  </div>
                </div>
                <button type="button" onclick="selectLiveItunesSong('${escapeHtml(item.previewUrl)}', '${escapeHtml(item.trackName)}', '${escapeHtml(item.artistName)}')" class="px-2.5 py-1 bg-[#eac34a] text-[#241a00] font-bold text-[10px] rounded-md">
                  + Select
                </button>
              </div>
            `).join('');
          } else {
            container.innerHTML = '<div class="p-2 text-center text-xs text-[#d0c3cb]">No songs found</div>';
          }
        } catch (err) {
          container.innerHTML = '<div class="p-2 text-center text-xs text-rose-400">Search error</div>';
        }
      }, 350);
    }

    function selectLiveItunesSong(url, title, artist) {
      liveChosenMusicUrl = url;
      liveChosenSongTitle = title;
      liveChosenArtist = artist;
      document.getElementById('liveFavoriteSingers').value = artist;
      document.getElementById('liveItunesResults').classList.add('hidden');
      alert(`Selected "${title}" by ${artist}! Click Save to update live.`);
    }

    async function saveLiveSectionChanges(section) {
      const btn = document.getElementById('liveEditorSaveBtn');
      btn.innerText = 'Saving Live...';
      btn.disabled = true;

      const payload = { token: activeEditToken };

      if (section === 'quote') {
        payload.partner_name = document.getElementById('livePartnerName').value.trim();
        payload.tagline_quote = document.getElementById('liveTaglineQuote').value.trim();
        payload.love_note_text = document.getElementById('liveLoveNoteText').value.trim();
      } else if (section === 'music') {
        const ytUrl = document.getElementById('liveYoutubeUrl').value.trim();
        payload.favorite_singers = document.getElementById('liveFavoriteSingers').value.trim();
        payload.bg_music_url = ytUrl || liveChosenMusicUrl || lockData.content.bg_music_url;
        payload.template_fields = { song_title: liveChosenSongTitle || lockData.content.template_fields.song_title };
      } else if (section === 'security') {
        const pass = document.getElementById('liveBuyerPassword').value.trim();
        const hintQ = document.getElementById('liveHintQuestion').value.trim();
        const hintA = document.getElementById('liveHintAnswer').value.trim();
        payload.hint_question = hintQ;
        if (hintA) payload.hint_answer = hintA;
        if (pass) payload.buyer_password = pass;
      }

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/edit_page.php?token=' + encodeURIComponent(activeEditToken), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          confetti({ particleCount: 100, spread: 80, origin: { y: 0.6 } });
          closeLiveEditorModal();
          // Reload fresh lock data
          const lockRes = await fetch('<?php echo APP_URL; ?>/api/get_page_lock.php?slug=' + encodeURIComponent(currentSlug));
          const lockJson = await lockRes.json();
          if (lockJson.success) {
            lockData = lockJson;
            renderSurpriseContent(lockData);
          }
        } else {
          alert('Save Error: ' + data.message);
        }
      } catch (err) {
        alert('Server Error: ' + err.message);
      } finally {
        btn.innerText = 'Save & Apply Live ✨';
        btn.disabled = false;
      }
    }
  </script>

  <!-- In-Place Live Visual Editor Modal -->
  <div id="liveEditorModal" class="fixed inset-0 z-50 bg-[#100d10]/90 backdrop-blur-md hidden items-center justify-center p-4" onclick="closeLiveEditorModal()">
    <div class="relative max-w-lg w-full bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/60 shadow-2xl space-y-6 text-center" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between border-b border-[#4d444b] pb-3">
        <h3 id="liveEditorTitle" class="text-lg font-bold font-serif text-[#e8e0e3]"></h3>
        <button onclick="closeLiveEditorModal()" class="text-[#d0c3cb] hover:text-white p-1">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <div id="liveEditorBody"></div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button onclick="closeLiveEditorModal()" class="px-4 py-2 bg-[#151215] text-[#d0c3cb] font-bold text-xs rounded-xl border border-[#4d444b]">
          Cancel
        </button>
        <button id="liveEditorSaveBtn" class="px-6 py-2.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
          Save &amp; Apply Live ✨
        </button>
      </div>
    </div>
  </div>

  <!-- Letter Reading Modal -->
  <div id="letterModal" class="fixed inset-0 z-50 bg-[#100d10]/90 backdrop-blur-md hidden items-center justify-center p-4" onclick="closeLetterModal()">
    <div class="relative max-w-xl w-full bg-[#221f21] p-8 sm:p-10 rounded-3xl border border-[#eac34a]/50 shadow-2xl space-y-6 text-center" onclick="event.stopPropagation()">
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
      <img id="lightboxImg" src="" alt="Enlarged view" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl border border-[#eac34a]/30">
      <button onclick="closeLightbox()" class="absolute -top-12 right-0 text-[#e8e0e3] hover:text-[#eac34a] p-2 cursor-pointer">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>
  </div>
</body>
</html>
