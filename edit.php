<?php
require_once __DIR__ . '/config/db.php';

$token = trim($_GET['token'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buyer Management Portal — <?php echo APP_NAME; ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            serif: ['"Bodoni Moda"', 'serif'],
            sans: ['Montserrat', 'sans-serif'],
          }
        }
      }
    }
  </script>
  
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Navbar -->
  <header class="sticky top-0 z-50 bg-[#151215]/90 backdrop-blur-xl border-b border-[#4d444b]/30 shadow-[0_4px_30px_rgba(0,0,0,0.5)]">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      <a href="<?php echo APP_URL; ?>" class="flex items-center gap-3 text-left group">
        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] shadow-[0_0_15px_rgba(234,195,74,0.3)] group-hover:scale-105 transition-transform duration-300">
          <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
            <i data-lucide="heart" class="w-4 h-4 text-[#eac34a] fill-[#eac34a]/30 group-hover:fill-[#eac34a] transition-colors"></i>
          </div>
        </div>
        <div class="flex flex-col">
          <span class="text-2xl font-bold tracking-wide text-[#e8e0e3] font-serif group-hover:text-[#eac34a] transition-colors">
            SoulScript
          </span>
          <span class="text-[9px] uppercase tracking-[0.2em] text-[#eac34a] font-semibold -mt-1 font-sans">
            Buyer Management Portal
          </span>
        </div>
      </a>

      <div class="flex items-center gap-3">
        <a href="<?php echo APP_URL; ?>/" class="px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider border border-[#4d444b] bg-[#221f21] text-[#d0c3cb] hover:border-[#eac34a]/50 hover:text-[#e8e0e3] transition-all">
          Home
        </a>
      </div>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10 space-y-8">
    
    <!-- VIEW A: LOGIN SCREEN (When no token provided) -->
    <div id="loginView" class="<?php echo $token ? 'hidden' : ''; ?> max-w-md mx-auto space-y-6">
      <div class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="lock" class="w-7 h-7"></i>
        </div>

        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Buyer Portal Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">Log in using your Email &amp; Secret Edit Password to update your gift website.</p>
        </div>

        <form id="buyerLoginForm" onsubmit="event.preventDefault(); handleBuyerLogin(event);" class="space-y-4 text-left">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Your Email Address</label>
            <input type="email" id="loginEmail" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. rohan@example.com" required>
          </div>

          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Secret Edit Password</label>
            <input type="password" id="loginPassword" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Enter your edit password" required>
          </div>

          <div id="loginMsg" class="hidden text-xs text-rose-400 font-semibold text-center"></div>

          <button type="button" onclick="handleBuyerLogin(event)" id="loginBtn" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
            Log In To Live Visual Editor
          </button>
        </form>
      </div>
    </div>

    <!-- VIEW B: BUYER DASHBOARD (When logged in / token active) -->
    <div id="dashboardView" class="<?php echo $token ? '' : 'hidden'; ?> space-y-6">
      
      <!-- Dashboard Header -->
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/30 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="space-y-1 text-center sm:text-left">
          <span class="text-[10px] uppercase tracking-[0.2em] text-[#eac34a] font-bold bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#e4b9df]/20">
            🔑 Buyer Manage Dashboard
          </span>
          <h1 class="text-2xl font-bold font-serif text-[#e8e0e3]">Edit Your Surprise Reveal Page</h1>
          <p class="text-xs text-[#d0c3cb]">Update quotes, background music, story milestones, photos, letters, and tokens anytime.</p>
        </div>

        <div class="flex gap-2">
          <a id="viewLivePageBtn" href="#" target="_blank" class="px-4 py-2.5 rounded-xl bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:bg-[#ffe088] transition-all">
            <span>View Live Gift</span>
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
          </a>
        </div>
      </div>

      <!-- Dashboard Section Navigation Tabs -->
      <div class="flex items-center justify-center gap-2 overflow-x-auto pb-2">
        <button onclick="switchTab('general')" id="tabBtn-general" class="px-4 py-2 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] transition-all">⚙️ General &amp; Music</button>
        <button onclick="switchTab('milestones')" id="tabBtn-milestones" class="px-4 py-2 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all">📍 Story Road</button>
        <button onclick="switchTab('photos')" id="tabBtn-photos" class="px-4 py-2 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all">🖼️ Scrapbook</button>
        <button onclick="switchTab('letters')" id="tabBtn-letters" class="px-4 py-2 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all">✉️ Sealed Letters</button>
        <button onclick="switchTab('tokens')" id="tabBtn-tokens" class="px-4 py-2 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all">🎟️ Love Tokens</button>
      </div>

      <!-- Main Edit Form -->
      <form id="editPageForm" onsubmit="saveDashboardChanges(event)" class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#4d444b]/50 shadow-2xl space-y-6">
        <input type="hidden" id="activeEditToken" value="<?php echo htmlspecialchars($token); ?>">

        <!-- TAB 1: GENERAL & MUSIC -->
        <div id="tabContent-general" class="space-y-4 text-xs">
          <div class="border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">⚙️ General &amp; Music Settings</h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Partner's First Name</label>
              <input type="text" id="partnerName" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
            </div>

            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Partner's Favorite Singers 🎙️</label>
              <input type="text" id="favoriteSingers" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Arijit Singh & KK">
            </div>
          </div>

          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Custom Romantic Quote / Tagline Banner *</label>
            <input type="text" id="taglineQuote" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Safar Khubsurat h manjil se bhi 🌹" required>
          </div>

          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Background Audio MP3 URL 🎵</label>
            <input type="url" id="bgMusicUrl" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="https://example.com/song.mp3">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Secret Hint Question (Lock Screen)</label>
              <input type="text" id="hintQuestion" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
            </div>

            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">New Hint Answer (Leave blank to keep unchanged)</label>
              <input type="text" id="hintAnswer" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Enter new secret answer">
            </div>
          </div>

          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Short Love Note / Signature Message</label>
            <textarea id="loveNoteText" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="3"></textarea>
          </div>
        </div>

        <!-- TAB 2: STORY MILESTONES -->
        <div id="tabContent-milestones" class="hidden space-y-4 text-xs">
          <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">📍 Story Road Milestones</h3>
            <button type="button" onclick="addMilestoneRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Milestone</button>
          </div>

          <div id="editMilestonesList" class="space-y-3">
            <!-- Dynamic Rows -->
          </div>
        </div>

        <!-- TAB 3: SCRAPBOOK PHOTOS -->
        <div id="tabContent-photos" class="hidden space-y-4 text-xs">
          <div class="border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🖼️ Photo Scrapbook</h3>
          </div>

          <div id="editPhotosList" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <!-- Dynamic Photos -->
          </div>
        </div>

        <!-- TAB 4: SEALED LETTERS -->
        <div id="tabContent-letters" class="hidden space-y-4 text-xs">
          <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">✉️ Wax-Sealed Letters</h3>
            <button type="button" onclick="addLetterRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Sealed Letter</button>
          </div>

          <div id="editLettersList" class="space-y-3">
            <!-- Dynamic Letters -->
          </div>
        </div>

        <!-- TAB 5: LOVE TOKENS -->
        <div id="tabContent-tokens" class="hidden space-y-4 text-xs">
          <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🎟️ Redeemable Love Tokens</h3>
            <button type="button" onclick="addTokenRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Love Token</button>
          </div>

          <div id="editTokensList" class="space-y-3">
            <!-- Dynamic Tokens -->
          </div>
        </div>

        <!-- Submit Controls -->
        <div class="pt-4 border-t border-[#4d444b]/40 flex items-center justify-between">
          <div id="saveMsg" class="text-xs font-bold text-[#eac34a]"></div>
          <button type="submit" id="saveChangesBtn" class="px-8 py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
            Save All Changes
          </button>
        </div>
      </form>

    </div>

  </main>

  <script>
    let activeToken = "<?php echo htmlspecialchars($token); ?>";

    async function handleBuyerLogin(e) {
      e.preventDefault();
      const btn = document.getElementById('loginBtn');
      const msg = document.getElementById('loginMsg');
      const email = document.getElementById('loginEmail').value;
      const pass = document.getElementById('loginPassword').value;

      btn.innerText = 'Verifying Login...';
      btn.disabled = true;
      msg.classList.add('hidden');

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/buyer_login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email, password: pass })
        });
        const data = await res.json();

        if (data.success) {
          if (data.redirect_url) {
            window.location.href = data.redirect_url;
          } else {
            window.location.href = `<?php echo APP_URL; ?>/gift/${data.url_slug}?edit_token=${data.edit_token}`;
          }
        } else {
          msg.classList.remove('hidden');
          msg.innerText = data.message;
        }
      } catch (err) {
        msg.classList.remove('hidden');
        msg.innerText = 'Error: ' + err.message;
      } finally {
        btn.innerText = 'Log In To Live Visual Editor';
        btn.disabled = false;
      }
    }

    async function loadDashboardData(token) {
      if (!token) return;

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/edit_page.php?token=' + encodeURIComponent(token));
        const data = await res.json();

        if (data.success) {
          window.location.href = `<?php echo APP_URL; ?>/gift/${data.page.url_slug}?edit_token=${token}`;
        }

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/edit_page.php?token=' + encodeURIComponent(token));
        const data = await res.json();

        if (data.success) {
          const p = data.page;
          document.getElementById('partnerName').value = p.partner_name || '';
          document.getElementById('hintQuestion').value = p.hint_question || '';
          document.getElementById('loveNoteText').value = p.love_note_text || '';
          document.getElementById('taglineQuote').value = p.tagline_quote || 'Safar Khubsurat h manjil se bhi 🌹';
          document.getElementById('favoriteSingers').value = p.favorite_singers || 'Arijit Singh & KK';
          document.getElementById('bgMusicUrl').value = p.bg_music_url || '';

          document.getElementById('viewLivePageBtn').href = data.share_url;

          // Render Milestones
          renderMilestonesList(data.milestones || []);

          // Render Photos
          renderPhotosList(data.media || []);

          // Render Letters
          const letters = p.letters_json ? JSON.parse(p.letters_json) : [];
          renderLettersList(letters);

          // Render Tokens
          const tokens = p.tokens_json ? JSON.parse(p.tokens_json) : [];
          renderTokensList(tokens);

          lucide.createIcons();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        console.error(err);
      }
    }

    function switchTab(tabName) {
      ['general', 'milestones', 'photos', 'letters', 'tokens'].forEach(t => {
        const btn = document.getElementById('tabBtn-' + t);
        const content = document.getElementById('tabContent-' + t);
        if (t === tabName) {
          btn.className = 'px-4 py-2 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] transition-all';
          content.classList.remove('hidden');
        } else {
          btn.className = 'px-4 py-2 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all';
          content.classList.add('hidden');
        }
      });
    }

    function renderMilestonesList(milestones) {
      const container = document.getElementById('editMilestonesList');
      if (milestones.length === 0) {
        milestones = [{ title: '', milestone_date: '', description: '' }];
      }

      container.innerHTML = milestones.map((m, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2 relative group">
          <input type="text" class="edit-m-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Milestone Title" value="${m.title || ''}">
          <input type="date" class="edit-m-date w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" value="${m.milestone_date || m.date || ''}">
          <input type="text" class="edit-m-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description" value="${m.description || ''}">
        </div>
      `).join('');
    }

    function addMilestoneRow() {
      const container = document.getElementById('editMilestonesList');
      const div = document.createElement('div');
      div.className = 'bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2';
      div.innerHTML = `
        <input type="text" class="edit-m-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="New Milestone Title">
        <input type="date" class="edit-m-date w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]">
        <input type="text" class="edit-m-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description">
      `;
      container.appendChild(div);
    }

    function renderPhotosList(media) {
      const container = document.getElementById('editPhotosList');
      container.innerHTML = media.map(m => `
        <div class="aspect-square rounded-2xl overflow-hidden border border-[#4d444b] relative group bg-[#151215]">
          <img src="${m.file_path}" class="w-full h-full object-cover">
        </div>
      `).join('');
    }

    function renderLettersList(letters) {
      const container = document.getElementById('editLettersList');
      if (letters.length === 0) {
        letters = [
          { title: 'The First Magical Spark', category: 'A Beautiful Beginning', content: 'My Dearest, I often find myself thinking back to the first moment...' },
          { title: 'Our Silent Sacred Promise', category: 'A Heartfelt Oath', content: 'Here is my little vow to you: I promise to stand by your side forever.' }
        ];
      }

      container.innerHTML = letters.map((l, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2">
          <input type="text" class="edit-l-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Letter Title" value="${l.title || ''}">
          <input type="text" class="edit-l-cat w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Category (e.g. A Heartfelt Oath)" value="${l.category || ''}">
          <textarea class="edit-l-content w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3]" rows="3" placeholder="Full Love Letter Text">${l.content || ''}</textarea>
        </div>
      `).join('');
    }

    function addLetterRow() {
      const container = document.getElementById('editLettersList');
      const div = document.createElement('div');
      div.className = 'bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2';
      div.innerHTML = `
        <input type="text" class="edit-l-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="New Letter Title">
        <input type="text" class="edit-l-cat w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Category e.g. A Quiet Memory">
        <textarea class="edit-l-content w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3]" rows="3" placeholder="Full Love Letter Text"></textarea>
      `;
      container.appendChild(div);
    }

    function renderTokensList(tokens) {
      const container = document.getElementById('editTokensList');
      if (tokens.length === 0) {
        tokens = [
          { title: '1 Free Warm Hug', description: 'Redeemable anytime for a long, tight hug when you need it most.', badge: 'Hug' },
          { title: 'Late Night Ice Cream Date', description: 'Redeemable for a midnight drive to your favorite ice cream parlor.', badge: 'Treat' }
        ];
      }

      container.innerHTML = tokens.map((t, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2">
          <input type="text" class="edit-t-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Token Title (e.g. 1 Free Warm Hug)" value="${t.title || ''}">
          <input type="text" class="edit-t-badge w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Badge e.g. Treat / Hug" value="${t.badge || ''}">
          <input type="text" class="edit-t-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description" value="${t.description || ''}">
        </div>
      `).join('');
    }

    function addTokenRow() {
      const container = document.getElementById('editTokensList');
      const div = document.createElement('div');
      div.className = 'bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2';
      div.innerHTML = `
        <input type="text" class="edit-t-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Token Title e.g. Movie Night Choice">
        <input type="text" class="edit-t-badge w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Badge e.g. Movie">
        <input type="text" class="edit-t-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description">
      `;
      container.appendChild(div);
    }

    async function saveDashboardChanges(e) {
      e.preventDefault();
      const btn = document.getElementById('saveChangesBtn');
      const msg = document.getElementById('saveMsg');

      btn.innerText = 'Saving Changes...';
      btn.disabled = true;
      msg.innerText = '';

      const milestones = [];
      document.querySelectorAll('#editMilestonesList > div').forEach(div => {
        const title = div.querySelector('.edit-m-title')?.value;
        const date = div.querySelector('.edit-m-date')?.value;
        const desc = div.querySelector('.edit-m-desc')?.value;
        if (title) milestones.push({ title: title, date: date || '', description: desc || '' });
      });

      const letters = [];
      document.querySelectorAll('#editLettersList > div').forEach(div => {
        const title = div.querySelector('.edit-l-title')?.value;
        const cat = div.querySelector('.edit-l-cat')?.value;
        const content = div.querySelector('.edit-l-content')?.value;
        if (title) letters.push({ id: letters.length + 1, title: title, category: cat || 'Love Note', content: content || '' });
      });

      const tokens = [];
      document.querySelectorAll('#editTokensList > div').forEach(div => {
        const title = div.querySelector('.edit-t-title')?.value;
        const badge = div.querySelector('.edit-t-badge')?.value;
        const desc = div.querySelector('.edit-t-desc')?.value;
        if (title) tokens.push({ id: tokens.length + 1, title: title, badge: badge || 'Coupon', description: desc || '' });
      });

      const payload = {
        token: activeToken,
        partner_name: document.getElementById('partnerName').value,
        hint_question: document.getElementById('hintQuestion').value,
        hint_answer: document.getElementById('hintAnswer').value,
        love_note_text: document.getElementById('loveNoteText').value,
        tagline_quote: document.getElementById('taglineQuote').value,
        favorite_singers: document.getElementById('favoriteSingers').value,
        bg_music_url: document.getElementById('bgMusicUrl').value,
        letters: letters,
        tokens: tokens,
        template_fields: {
          milestones: milestones
        }
      };

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/edit_page.php?token=' + encodeURIComponent(activeToken), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          msg.innerText = '✅ All changes saved successfully!';
        } else {
          msg.innerText = '❌ Error: ' + data.message;
        }
      } catch (err) {
        msg.innerText = '❌ Error: ' + err.message;
      } finally {
        btn.innerText = 'Save All Changes';
        btn.disabled = false;
      }
    }

    if (activeToken) {
      loadDashboardData(activeToken);
    }
  </script>
</body>
</html>
