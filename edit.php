<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// Option 2: Password-Gated Session Login (Clean URL, token stored in session)
$token = trim($_SESSION['edit_token'] ?? $_GET['token'] ?? '');
if (!empty($_GET['token'])) {
    $_SESSION['edit_token'] = $_GET['token'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Buyer Management Portal — ' . APP_NAME;
  require_once __DIR__ . '/includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Unified Global Navbar -->
  <?php 
  $current_page = 'edit';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-12 relative z-10 space-y-8">
    
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
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-[#d0c3cb]">Secret Edit Password</label>
              <button type="button" onclick="showForgotPasswordModal()" class="text-[11px] text-[#eac34a] hover:underline cursor-pointer">Forgot Password?</button>
            </div>
            <input type="password" id="loginPassword" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Enter your edit password" required>
          </div>

          <div id="loginMsg" class="hidden text-xs text-rose-400 font-semibold text-center"></div>

          <button type="button" onclick="handleBuyerLogin(event)" id="loginBtn" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
            Log In To Live Visual Editor
          </button>
        </form>
      </div>
    </div>

    <!-- FORGOT PASSWORD MODAL -->
    <div id="forgotPasswordModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/40 max-w-md w-full space-y-5 shadow-2xl relative">
        <button type="button" onclick="closeForgotPasswordModal()" class="absolute top-4 right-4 text-[#d0c3cb] hover:text-white p-1 text-lg">✕</button>
        <div class="text-center space-y-1">
          <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 flex items-center justify-center mx-auto mb-2">
            <i data-lucide="key-round" class="w-6 h-6"></i>
          </div>
          <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Reset Password 🔑</h3>
          <p class="text-xs text-[#d0c3cb]">Enter your registered email to reset your account password in 10 seconds.</p>
        </div>

        <form id="forgotPassForm" onsubmit="handleRequestPasswordReset(event)" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Registered Email Address</label>
            <input type="email" id="forgotPassEmail" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. rohan@example.com" required>
          </div>

          <div id="forgotPassMsg" class="hidden text-xs text-center p-3 rounded-xl"></div>

          <button type="submit" id="forgotPassBtn" class="w-full py-3 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer shadow-md">
            Send Password Reset Link
          </button>
        </form>

        <div id="forgotPassOptions" class="hidden space-y-2 pt-2 border-t border-[#4d444b]/40 text-center">
          <a id="forgotWaBtn" href="#" target="_blank" class="w-full py-2.5 bg-[#25D366] text-black font-bold text-xs rounded-xl flex items-center justify-center gap-2 text-decoration-none font-bold">
            <span>💬 1-Click WhatsApp Support Reset</span>
          </a>
        </div>
      </div>
    </div>

    <!-- VIEW B: PURCHASED GIFTS HUB (When logged in / managing multiple gifts) -->
    <div id="hubView" class="hidden space-y-6">
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#4d444b]/40 pb-5">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center text-xl shrink-0 shadow-md">
              🎁
            </div>
            <div>
              <span class="text-[10px] uppercase font-extrabold tracking-[0.2em] text-[#eac34a] block">Buyer Portal Hub</span>
              <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]" id="hubTitleLabel">Your Purchased Gift Websites</h2>
              <p class="text-xs text-[#d0c3cb]">Manage all your romantic surprise pages in one single private portal.</p>
            </div>
          </div>
          <button type="button" onclick="handleBuyerLogout()" class="px-4 py-2.5 rounded-xl bg-[#151215] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 transition-all cursor-pointer shadow-md shrink-0">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
            <span>Log Out</span>
          </button>
        </div>

        <!-- Purchased Gifts Visual Cards Grid -->
        <div id="hubGiftsGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <!-- Dynamic Purchased Gift Cards -->
        </div>
      </div>
    </div>

    <!-- VIEW C: BUYER VISUAL EDITOR DASHBOARD -->
    <div id="dashboardView" class="<?php echo $token ? '' : 'hidden'; ?> space-y-6">

      <!-- Active Plan Badge Banner & Share Link -->
      <div class="bg-[#221f21] p-5 sm:p-6 rounded-3xl border border-[#eac34a]/30 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-left min-w-0 flex-1">
          <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center shrink-0 shadow-md">
            <i data-lucide="sparkles" class="w-5 h-5 sm:w-6 sm:h-6"></i>
          </div>
          <div class="space-y-0.5 min-w-0 flex-1">
            <span id="activePlanBadge" class="text-[9px] sm:text-[10px] uppercase font-extrabold tracking-wider text-[#eac34a] bg-[#3b1e3b] px-2.5 py-0.5 rounded-full border border-[#e4b9df]/20 inline-block">
              Active Plan
            </span>
            <h2 class="text-lg sm:text-2xl font-bold font-serif text-[#e8e0e3] truncate" id="dashPartnerTitle">Partner Gift Dashboard</h2>
            <p class="text-[11px] sm:text-xs text-[#d0c3cb]">Update your gift contents in real-time below.</p>
          </div>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto justify-start md:justify-end shrink-0">
          <button type="button" id="backToHubBtn" onclick="showHubView()" class="hidden px-3.5 py-2.5 rounded-xl bg-[#3b1e3b] hover:bg-[#eac34a] text-[#eac34a] hover:text-[#241a00] border border-[#eac34a]/40 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 transition-all cursor-pointer shadow-md">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>All Gifts</span>
          </button>
          <a id="viewLivePageBtn" href="#" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:bg-[#ffe088] transition-all shadow-md">
            <span>View Live</span>
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
          </a>
          <button type="button" onclick="handleBuyerLogout()" class="px-3.5 py-2.5 rounded-xl bg-[#221f21] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 transition-all cursor-pointer shadow-md">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
            <span>Log Out</span>
          </button>
        </div>
      </div>

      <!-- Dashboard Section Navigation Tabs -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none w-full whitespace-nowrap">
        <button type="button" onclick="switchTab('general')" id="tabBtn-general" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] shadow-[0_0_15px_rgba(234,195,74,0.3)] transition-all whitespace-nowrap shrink-0 cursor-pointer">General &amp; Music</button>
        <button type="button" onclick="switchTab('theme')" id="tabBtn-theme" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Story Milestones</button>
        <button type="button" onclick="switchTab('photos')" id="tabBtn-photos" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Photo Gallery</button>
        <button type="button" onclick="switchTab('letters')" id="tabBtn-letters" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Sealed Letters</button>
        <button type="button" onclick="switchTab('tokens')" id="tabBtn-tokens" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Love Tokens</button>
        <button type="button" onclick="switchTab('security')" id="tabBtn-security" class="px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white transition-all whitespace-nowrap shrink-0 cursor-pointer">Security &amp; Passwords</button>
      </div>

      <!-- Main Edit Form -->
      <form id="editPageForm" onsubmit="saveDashboardChanges(event)" class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#4d444b]/50 shadow-2xl space-y-6">
        <input type="hidden" id="activeEditToken" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" id="activeTemplateId" value="">

        <!-- TAB 1: GENERAL & MUSIC -->
        <div id="tabContent-general" class="space-y-4 text-xs">
          <div class="border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3]">⚙️ General &amp; Music Settings</h3>
          </div>

          <div>
            <label id="partnerNameLabel" class="block font-semibold text-[#d0c3cb] mb-1">Partner's First Name *</label>
            <input type="text" id="partnerName" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>

          <div>
            <label id="taglineQuoteLabel" class="block font-semibold text-[#d0c3cb] mb-1">Custom Romantic Quote / Tagline Banner *</label>
            <input type="text" id="taglineQuote" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Safar Khubsurat h manjil se bhi 🌹" required>
          </div>

          <!-- Visual Gift Receiver Avatar Photo Manager -->
          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1.5">Gift Receiver / Partner Profile Photo 🖼️</label>
            <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] flex flex-col sm:flex-row items-center gap-4">
              <div id="partnerAvatarContainer" class="w-16 h-16 rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#eac34a] flex items-center justify-center font-bold text-2xl shadow-[0_0_20px_rgba(234,195,74,0.3)] shrink-0 overflow-hidden">
                <span id="partnerAvatarFallback">A</span>
                <img id="partnerAvatarImg" src="" class="w-full h-full object-cover hidden">
              </div>
              <div class="flex-1 text-center sm:text-left space-y-2">
                <input type="file" id="dashPhotoInput" accept="image/*" onchange="handleDashPhotoSelect(this)" class="hidden">
                <input type="hidden" id="receiverPhotoUrl" value="">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                  <button type="button" onclick="document.getElementById('dashPhotoInput').click()" class="px-4 py-2 bg-[#3b1e3b] hover:bg-[#eac34a] text-[#eac34a] hover:text-[#241a00] border border-[#eac34a]/40 font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shadow-md">
                    <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                    <span>Upload / Change Photo</span>
                  </button>
                  <button type="button" onclick="removeDashPhoto()" id="removePhotoBtn" class="px-3 py-2 bg-[#221f21] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs rounded-xl transition-all cursor-pointer hidden flex items-center gap-1.5">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Remove Photo</span>
                  </button>
                </div>
                <p class="text-[10px] text-[#d0c3cb]/70">Upload a portrait photo to show at the top of the surprise page. If no photo is added, partner's initial character will be displayed.</p>
              </div>
            </div>
          </div>

          <!-- Universal Music Engine (iTunes Live Search + YouTube Link + Favorite Singer) -->
          <div class="bg-[#151215] p-5 rounded-2xl border border-[#eac34a]/30 space-y-4">
            <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-2">
              <label class="font-bold text-[#eac34a] text-xs uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="music" class="w-4 h-4 text-[#eac34a]"></i>
                <span>Background Music Engine 🎵</span>
              </label>
            </div>

            <!-- Choice Mode Radios -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label class="flex items-center gap-2 p-3 bg-[#221f21] border border-[#4d444b] rounded-xl cursor-pointer hover:border-[#eac34a]">
                <input type="radio" name="dash_music_mode" value="itunes_search" checked onchange="toggleDashMusicMode('itunes_search')" class="text-[#eac34a]">
                <div class="text-xs">
                  <strong class="block text-[#e8e0e3]">🔍 Live Song Search</strong>
                  <span class="text-[10px] text-[#d0c3cb]">Search any song / singer</span>
                </div>
              </label>

              <label class="flex items-center gap-2 p-3 bg-[#221f21] border border-[#4d444b] rounded-xl cursor-pointer hover:border-[#eac34a]">
                <input type="radio" name="dash_music_mode" value="youtube_link" onchange="toggleDashMusicMode('youtube_link')" class="text-[#eac34a]">
                <div class="text-xs">
                  <strong class="block text-[#e8e0e3]">🎥 YouTube / Shorts Link</strong>
                  <span class="text-[10px] text-[#d0c3cb]">Paste YouTube or Shorts URL</span>
                </div>
              </label>
            </div>

            <!-- iTunes Live Search Container -->
            <div id="dashItunesSearchBox" class="space-y-3">
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Search &amp; Select Song (Bollywood, Romantic, English, Punjabi) 🎶</label>
                <input type="text" id="dashItunesQueryInput" oninput="handleDashItunesSearch(this.value)" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none" placeholder="🔍 Type song name or singer e.g. Tum Hi Ho, Arijit Singh, Zara Sa, Kesariya, Taylor Swift...">
              </div>

              <!-- Selected Track Card -->
              <div id="dashSelectedTrackCard" class="bg-[#100d10] p-3 rounded-xl border border-[#eac34a]/60 flex flex-wrap items-center justify-between gap-2 sm:gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                  <img id="dashSelectedTrackImg" src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80" class="w-10 h-10 rounded-lg object-cover border border-[#4d444b] shrink-0">
                  <div class="min-w-0 flex-1">
                    <span class="block font-bold text-xs text-[#e8e0e3] truncate" id="dashSelectedTrackTitle">Tum Hi Ho</span>
                    <span class="block text-[10px] text-[#eac34a] truncate" id="dashSelectedTrackArtist">Artist: Arijit Singh</span>
                  </div>
                </div>
                <span class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 font-bold shrink-0">✓ Selected</span>
              </div>

              <!-- Live Search Results Container -->
              <div id="dashItunesResultsList" class="hidden space-y-2 max-h-60 overflow-y-auto bg-[#100d10] p-2 rounded-xl border border-[#4d444b]"></div>
            </div>

            <!-- YouTube Link Container -->
            <div id="dashYoutubeLinkBox" class="hidden space-y-3">
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Paste YouTube Video / Shorts / Audio URL 🎥</label>
                <input type="url" id="dashYoutubeUrlInput" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="https://www.youtube.com/watch?v=... or https://youtube.com/shorts/... or https://youtu.be/...">
                <p class="text-[10px] text-[#d0c3cb]/70 mt-1">Paste any public YouTube video or YouTube Shorts link. Video ID will be extracted automatically.</p>
              </div>
            </div>
            <input type="hidden" id="bgMusicUrl" value="">
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
            <label id="loveNoteLabel" class="block font-semibold text-[#d0c3cb] mb-1">Short Love Note / Signature Message</label>
            <textarea id="loveNoteText" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="3"></textarea>
          </div>
        </div>

        <!-- TAB 2: DYNAMIC TEMPLATE-ISOLATED THEME SETTINGS -->
        <div id="tabContent-theme" class="hidden space-y-4 text-xs">
          <div id="themeContainer">
            <!-- Dynamically populated based on template_id -->
          </div>
        </div>

        <!-- TAB 3: SECURITY & PASSWORDS MANAGEMENT -->
        <div id="tabContent-security" class="hidden space-y-6 text-xs">
          <div class="border-b border-[#4d444b]/40 pb-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-[#eac34a]"></i>
                <span>Account Security &amp; Password Management</span>
              </h3>
              <p class="text-[11px] text-[#d0c3cb] mt-0.5">Manage your SoulScript buyer portal login password safely.</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 font-bold text-[10px] uppercase tracking-wider hidden sm:inline-block">
              🔐 Encrypted Storage
            </span>
          </div>

          <div class="bg-[#151215] p-4 sm:p-7 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6">
            <!-- Buyer Portal Login Password Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3.5 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-[#221f21] via-[#2d222d] to-[#221f21] border border-[#eac34a]/30">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center shrink-0 shadow-md">
                <i data-lucide="key-round" class="w-5 h-5 sm:w-6 sm:h-6 text-[#eac34a]"></i>
              </div>
              <div class="space-y-0.5">
                <h4 class="font-bold text-sm text-[#e8e0e3]">Update Buyer Account Password 🔑</h4>
                <p class="text-[11px] text-[#d0c3cb] leading-relaxed">Change the secret password used to log in at <code>soulscript.in/edit</code>. Leave all fields blank if you don't wish to change your password.</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <!-- Current Password -->
              <div class="space-y-1.5">
                <label class="block font-semibold text-[#d0c3cb] flex items-center gap-1.5 text-xs">
                  <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                  <span>Current Account Password</span>
                </label>
                <div class="relative">
                  <input type="password" id="currentBuyerPassword" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none transition-all pr-10" placeholder="Type current password">
                  <button type="button" onclick="togglePasswordVisibility('currentBuyerPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>

              <!-- New Password -->
              <div class="space-y-1.5">
                <label class="block font-semibold text-[#d0c3cb] flex items-center gap-1.5 text-xs">
                  <i data-lucide="key" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                  <span>New Account Password</span>
                </label>
                <div class="relative">
                  <input type="password" id="newBuyerPassword" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none transition-all pr-10" placeholder="Type new password">
                  <button type="button" onclick="togglePasswordVisibility('newBuyerPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>

              <!-- Confirm New Password -->
              <div class="space-y-1.5">
                <label class="block font-semibold text-[#d0c3cb] flex items-center gap-1.5 text-xs">
                  <i data-lucide="check-check" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                  <span>Re-enter New Password</span>
                </label>
                <div class="relative">
                  <input type="password" id="confirmBuyerPassword" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none transition-all pr-10" placeholder="Re-enter new password">
                  <button type="button" onclick="togglePasswordVisibility('confirmBuyerPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#d0c3cb] hover:text-[#eac34a] p-1 cursor-pointer">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Password Requirements Badge Footer -->
            <div class="p-3.5 bg-[#221f21] rounded-2xl border border-[#4d444b]/60 flex items-center justify-between text-[11px] text-[#d0c3cb]">
              <span class="flex items-center gap-1.5">
                <i data-lucide="info" class="w-4 h-4 text-[#eac34a]"></i>
                <span>Leave fields blank if keeping current password. Minimum 4 characters required.</span>
              </span>
            </div>
          </div>
        </div>

        <!-- TAB 4: SCRAPBOOK PHOTOS MANAGEMENT -->
        <div id="tabContent-photos" class="hidden space-y-5 text-xs">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-[#4d444b]/40 pb-3">
            <div>
              <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
                <i data-lucide="image" class="w-5 h-5 text-[#eac34a]"></i>
                <span>Photo Gallery Management 🖼️</span>
              </h3>
              <span class="text-[11px] text-[#d0c3cb]" id="dashSelectedPhotoCount">Selected: 0 photos</span>
            </div>
            <button type="button" onclick="document.getElementById('dashScrapbookFileInput').click()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-md shrink-0">
              <i data-lucide="upload" class="w-3.5 h-3.5"></i>
              <span>Upload Photos</span>
            </button>
            <input type="file" id="dashScrapbookFileInput" accept="image/*" multiple class="hidden" onchange="handleDashScrapbookFiles(event)">
          </div>

          <!-- Current Selected Uploads Grid -->
          <div class="bg-[#151215] p-4 sm:p-5 rounded-3xl border border-[#4d444b] min-h-[140px] space-y-3">
            <div id="dashScrapbookContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
              <!-- Dynamic Photo Thumbnails with Delete X Button -->
            </div>
          </div>

          <!-- Quick Pick Sample Photos Gallery -->
          <div class="space-y-3 pt-2">
            <label class="text-xs font-bold uppercase tracking-wider text-[#eac34a] flex items-center gap-1.5">
              <i data-lucide="sparkles" class="w-4 h-4"></i>
              <span>QUICK PICK SAMPLE ROMANTIC PHOTOS:</span>
            </label>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3" id="dashSamplePhotosGrid">
              <!-- Rendered by JS -->
            </div>
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

  <!-- Interactive Circle Crop Modal -->
  <div id="circleCropModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4 hidden">
    <div class="bg-[#221f21] border border-[#eac34a]/40 rounded-3xl p-6 max-w-md w-full text-center space-y-4 shadow-2xl relative">
      <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
        <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
          <i data-lucide="crop" class="w-4 h-4 text-[#eac34a]"></i>
          <span>Adjust &amp; Crop Partner Photo</span>
        </h3>
        <button onclick="closeCircleCropModal()" type="button" class="text-[#d0c3cb] hover:text-white text-lg font-bold">✕</button>
      </div>

      <!-- Circle Preview Container -->
      <div class="relative w-64 h-64 mx-auto overflow-hidden bg-[#151215] rounded-2xl border border-[#4d444b] flex items-center justify-center cursor-move select-none" id="cropCanvasWrapper">
        <canvas id="cropCanvas" width="256" height="256" class="touch-none"></canvas>
        <!-- Circular Overlay Mask Guide -->
        <div class="absolute inset-0 rounded-full border-4 border-[#eac34a] shadow-[0_0_0_9999px_rgba(21,18,21,0.7)] pointer-events-none"></div>
      </div>

      <!-- Controls: Zoom & Position reset -->
      <div class="space-y-3 pt-2">
        <div class="flex items-center gap-3 text-xs">
          <span class="text-[#d0c3cb] font-semibold w-12 text-right">Zoom:</span>
          <input type="range" id="cropZoomRange" min="0.5" max="3" step="0.05" value="1" oninput="updateCropCanvas()" class="w-full accent-[#eac34a]">
        </div>
        <p class="text-[10px] text-[#d0c3cb]/70">Drag photo to align partner's face inside the golden circle.</p>
      </div>

      <!-- Modal Action Buttons -->
      <div class="flex items-center gap-3 pt-2">
        <button type="button" onclick="closeCircleCropModal()" class="w-1/2 py-2.5 bg-[#151215] text-[#d0c3cb] border border-[#4d444b] rounded-xl font-bold text-xs">Cancel</button>
        <button type="button" onclick="applyCircleCrop()" class="w-1/2 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:bg-[#ffe088] transition-all">Crop &amp; Apply</button>
      </div>
    </div>
  </div>

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
          } else if (data.pages && data.pages.length > 1) {
            document.getElementById('loginView').classList.add('hidden');
            document.getElementById('dashboardView').classList.add('hidden');
            renderPurchasedGiftsHub(data.pages);
            document.getElementById('hubView').classList.remove('hidden');
          } else {
            window.location.href = '<?php echo APP_URL; ?>/edit.php';
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

    async function handleBuyerLogout() {
      try {
        await fetch('<?php echo APP_URL; ?>/api/buyer_logout.php', { method: 'POST' });
      } catch (e) {}
      window.location.href = '<?php echo APP_URL; ?>/edit.php';
    }

    let dashMusicSearchTimer = null;
    let dashCurrentMusicUrl = '';
    let dashCurrentSongTitle = '';
    let dashCurrentArtist = '';

    function toggleDashMusicMode(mode) {
      const itunesBox = document.getElementById('dashItunesSearchBox');
      const ytBox = document.getElementById('dashYoutubeLinkBox');
      const randBox = document.getElementById('dashRandomSingerBox');

      if (itunesBox) itunesBox.classList.add('hidden');
      if (ytBox) ytBox.classList.add('hidden');
      if (randBox) randBox.classList.add('hidden');

      if (mode === 'itunes_search' && itunesBox) itunesBox.classList.remove('hidden');
      if (mode === 'youtube_link' && ytBox) ytBox.classList.remove('hidden');
      if (mode === 'random_singer' && randBox) randBox.classList.remove('hidden');
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

    function cleanAttrStr(str) {
      if (!str) return '';
      return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function handleDashItunesSearch(query) {
      clearTimeout(dashMusicSearchTimer);
      const resultsContainer = document.getElementById('dashItunesResultsList');
      if (!query.trim()) {
        resultsContainer.classList.add('hidden');
        return;
      }

      dashMusicSearchTimer = setTimeout(async () => {
        try {
          resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-[#eac34a]">Searching iTunes Music Database...</div>';
          resultsContainer.classList.remove('hidden');

          const res = await fetch(`https://itunes.apple.com/search?term=${encodeURIComponent(query)}&entity=song&limit=8`);
          const data = await res.json();

          if (data.results && data.results.length > 0) {
            resultsContainer.innerHTML = data.results.map(item => `
              <div class="p-2.5 bg-[#151215] hover:bg-[#221f21] rounded-xl flex items-center justify-between border border-[#4d444b]/50 transition-all">
                <div class="flex items-center gap-3 overflow-hidden">
                  <img src="${item.artworkUrl60 || item.artworkUrl100}" class="w-10 h-10 rounded-lg object-cover border border-[#4d444b] shrink-0">
                  <div class="truncate">
                    <span class="block font-bold text-xs text-[#e8e0e3] truncate">${escapeHtml(item.trackName)}</span>
                    <span class="block text-[10px] text-[#d0c3cb] truncate">🎤 ${escapeHtml(item.artistName)} • ${escapeHtml(item.collectionName || '')}</span>
                  </div>
                </div>
                <button type="button" onclick="selectDashItunesTrack('${cleanAttrStr(item.previewUrl)}', '${cleanAttrStr(item.trackName)}', '${cleanAttrStr(item.artistName)}', '${cleanAttrStr(item.artworkUrl100)}')" class="px-3 py-1.5 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-[11px] rounded-lg border border-[#eac34a]/40 shrink-0 transition-all cursor-pointer">
                  + Select
                </button>
              </div>
            `).join('');
          } else {
            resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-[#d0c3cb]">No songs found. Try typing artist name or song title.</div>';
          }
        } catch (err) {
          resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-rose-400">Search error: ' + escapeHtml(err.message) + '</div>';
        }
      }, 350);
    }

    function selectDashItunesTrack(previewUrl, trackName, artistName, imgUrl) {
      dashCurrentMusicUrl = previewUrl;
      dashCurrentSongTitle = trackName;
      dashCurrentArtist = artistName;

      document.getElementById('dashSelectedTrackImg').src = imgUrl || 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80';
      document.getElementById('dashSelectedTrackTitle').innerText = trackName;
      document.getElementById('dashSelectedTrackArtist').innerText = 'Artist: ' + artistName;
      document.getElementById('bgMusicUrl').value = previewUrl;

      document.getElementById('dashItunesResultsList').classList.add('hidden');
    }

    async function loadDashboardData(token) {
      token = token || activeToken || document.getElementById('activeEditToken')?.value;
      if (!token) return;

      activeToken = token;
      const activeInput = document.getElementById('activeEditToken');
      if (activeInput) activeInput.value = token;

      document.getElementById('loginView')?.classList.add('hidden');
      document.getElementById('dashboardView')?.classList.remove('hidden');

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/edit_page.php?token=' + encodeURIComponent(token));
        const data = await res.json();

        if (data.success) {
          const p = data.page;
          document.getElementById('activeTemplateId').value = p.template_id;
          document.getElementById('partnerName').value = p.partner_name || '';
          document.getElementById('hintQuestion').value = p.hint_question || '';
          if (document.getElementById('secHintQuestion')) document.getElementById('secHintQuestion').value = p.hint_question || '';
          document.getElementById('loveNoteText').value = p.love_note_text || '';
          document.getElementById('taglineQuote').value = p.tagline_quote || (p.template_id === 'birthday_magic' ? 'Cheers to another year of awesome memories! 🥂' : (p.template_id === 'long_distance_love' ? 'Miles apart but connected by heart ✈️' : 'Safar Khubsurat h manjil se bhi 🌹'));
          // Bind Music Engine State from Database
          const songTitle = p.song_title || 'Tum Hi Ho';
          const songArtist = p.song_artist || 'Arijit Singh';
          const musicUrl = p.bg_music_url || '';

          dashCurrentSongTitle = songTitle;
          dashCurrentArtist = songArtist;
          dashCurrentMusicUrl = musicUrl;

          const isYt = musicUrl.includes('youtube.com') || musicUrl.includes('youtu.be');
          if (isYt) {
            toggleDashMusicMode('youtube_link');
            const ytRadio = document.querySelector('input[name="dash_music_mode"][value="youtube_link"]');
            if (ytRadio) ytRadio.checked = true;
            document.getElementById('dashYoutubeUrlInput').value = musicUrl;
          } else {
            toggleDashMusicMode('itunes_search');
            const itunesRadio = document.querySelector('input[name="dash_music_mode"][value="itunes_search"]');
            if (itunesRadio) itunesRadio.checked = true;

            document.getElementById('dashSelectedTrackTitle').innerText = songTitle;
            document.getElementById('dashSelectedTrackArtist').innerText = 'Artist: ' + songArtist;
          }

          document.getElementById('viewLivePageBtn').href = data.share_url;
          document.getElementById('dashPartnerTitle').innerText = (p.partner_name || 'Partner') + "'s Gift Dashboard";

          // Update Partner Photo Avatar Manager UI
          updatePartnerPhotoAvatar(p.receiver_photo, p.partner_name);

          // Render Template Isolated Theme Tab
          renderThemeContainer(p, data);

          // Render Photos
          renderPhotosList(data.media || []);

          // Render Letters
          const letters = p.letters_json ? JSON.parse(p.letters_json) : [];
          renderLettersList(letters);

          // Render Tokens
          const tokens = p.tokens_json ? JSON.parse(p.tokens_json) : [];
          renderTokensList(tokens);

          // Store buyer_pages list if returned
          if (data.buyer_pages && Array.isArray(data.buyer_pages)) {
            allBuyerPages = data.buyer_pages;
            const backBtn = document.getElementById('backToHubBtn');
            if (backBtn) {
              if (allBuyerPages.length > 1) {
                backBtn.classList.remove('hidden');
              } else {
                backBtn.classList.add('hidden');
              }
            }
          }

          lucide.createIcons();
        } else {
          const msg = document.getElementById('loginMsg');
          document.getElementById('loginView').classList.remove('hidden');
          document.getElementById('dashboardView').classList.add('hidden');
          msg.classList.remove('hidden');
          msg.innerText = data.message || 'Invalid or expired edit link token';
        }
      } catch (err) {
        console.error(err);
      }
    }

    let allBuyerPages = [];

    function renderPurchasedGiftsHub(pages) {
      allBuyerPages = pages || [];
      const grid = document.getElementById('hubGiftsGrid');
      const backBtn = document.getElementById('backToHubBtn');

      if (allBuyerPages.length > 1 && backBtn) {
        backBtn.classList.remove('hidden');
      } else if (backBtn) {
        backBtn.classList.add('hidden');
      }

      if (!grid) return;

      const tplMeta = {
        'anniversary_reveal': { name: 'Anniversary Reveal', emoji: '🌹', color: 'from-[#3b1e3b] to-[#221f21]' },
        'birthday_magic': { name: 'Birthday Magic', emoji: '🎂', color: 'from-[#1e3b30] to-[#221f21]' },
        'perfect_proposal': { name: 'Perfect Proposal', emoji: '💍', color: 'from-[#3b2d1e] to-[#221f21]' },
        'long_distance_love': { name: 'Long Distance Love', emoji: '🌍', color: 'from-[#1e2a3b] to-[#221f21]' },
        'raksha_bandhan_special': { name: 'Raksha Bandhan Special', emoji: '🪔', color: 'from-[#3b1e22] to-[#221f21]' }
      };

      grid.innerHTML = allBuyerPages.map(p => {
        const meta = tplMeta[p.template_id] || { name: 'Gift Website', emoji: '🎁', color: 'from-[#221f21] to-[#151215]' };
        const partner = p.partner_name || 'Partner';
        const shareUrl = '<?php echo APP_URL; ?>/gift/' + p.url_slug;

        return `
          <div class="bg-gradient-to-b ${meta.color} p-5 rounded-2xl border border-[#eac34a]/30 shadow-xl flex flex-col justify-between gap-4 group hover:border-[#eac34a] transition-all">
            <div class="space-y-2.5">
              <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1.5">
                <span class="text-[10px] uppercase font-extrabold tracking-wider text-[#eac34a] bg-[#100d10] px-2.5 py-1 rounded-full border border-[#eac34a]/30 flex items-center gap-1 shrink-0">
                  <span>${meta.emoji}</span>
                  <span>${meta.name}</span>
                </span>
                <span class="text-[10px] text-[#d0c3cb]/70 font-mono truncate max-w-full">/gift/${p.url_slug}</span>
              </div>
              <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Surprise Page for <span class="text-[#eac34a]">${escapeHtml(partner)}</span></h3>
              <p class="text-xs text-[#d0c3cb]/80 truncate">Private Link: <a href="${shareUrl}" target="_blank" class="underline hover:text-[#eac34a]">${shareUrl}</a></p>
            </div>

            <div class="flex items-center gap-2 pt-2 border-t border-[#4d444b]/30">
              <button type="button" onclick="openSelectedGiftEditor('${p.edit_token}')" class="flex-1 py-2.5 px-3 rounded-xl bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-extrabold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-md">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit &amp; Manage</span>
              </button>
              <a href="${shareUrl}" target="_blank" class="py-2.5 px-3 rounded-xl bg-[#100d10] hover:bg-[#3b1e3b] text-[#e8e0e3] border border-[#4d444b] font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1 shrink-0">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>View</span>
              </a>
            </div>
          </div>
        `;
      }).join('');

      if (typeof lucide === 'object') lucide.createIcons();
    }

    function showHubView() {
      const loginView = document.getElementById('loginView');
      const hubView = document.getElementById('hubView');
      const dashView = document.getElementById('dashboardView');

      if (loginView) loginView.classList.add('hidden');
      if (dashView) dashView.classList.add('hidden');
      if (hubView) hubView.classList.remove('hidden');

      if (allBuyerPages.length > 0) {
        renderPurchasedGiftsHub(allBuyerPages);
      }
    }

    async function openSelectedGiftEditor(token) {
      if (!token) return;
      activeToken = token;
      document.getElementById('activeEditToken').value = token;

      const loginView = document.getElementById('loginView');
      const hubView = document.getElementById('hubView');
      const dashView = document.getElementById('dashboardView');

      if (loginView) loginView.classList.add('hidden');
      if (hubView) hubView.classList.add('hidden');
      if (dashView) dashView.classList.remove('hidden');

      loadDashboardData(token);
    }

    function normalizeMediaUrlJs(url) {
      if (!url) return '';
      if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:')) {
        return url;
      }
      const appUrl = '<?php echo APP_URL; ?>';
      const cleanPath = url.replace(/^\/+/, '');
      return appUrl + '/' + cleanPath;
    }

    function updatePartnerPhotoAvatar(photoUrl, partnerName) {
      const fallback = document.getElementById('partnerAvatarFallback');
      const img = document.getElementById('partnerAvatarImg');
      const removeBtn = document.getElementById('removePhotoBtn');
      const hiddenInput = document.getElementById('receiverPhotoUrl');

      const nameChar = (partnerName || 'P').charAt(0).toUpperCase();
      if (fallback) fallback.innerText = nameChar;

      const isValidPhoto = photoUrl && typeof photoUrl === 'string' && photoUrl.trim() !== '' && photoUrl !== 'null' && photoUrl !== 'undefined';

      if (isValidPhoto) {
        const cleanUrl = photoUrl.trim();
        if (hiddenInput) hiddenInput.value = cleanUrl;
        const fullUrl = normalizeMediaUrlJs(cleanUrl);
        if (img) {
          img.onerror = function() {
            this.classList.add('hidden');
            if (fallback) fallback.classList.remove('hidden');
          };
          img.onload = function() {
            this.classList.remove('hidden');
            if (fallback) fallback.classList.add('hidden');
          };

          // Unhide immediately so cached or freshly loaded images display seamlessly
          img.classList.remove('hidden');
          if (fallback) fallback.classList.add('hidden');

          img.src = fullUrl;

          if (img.complete && img.naturalWidth > 0) {
            img.classList.remove('hidden');
            if (fallback) fallback.classList.add('hidden');
          }
        }
        if (removeBtn) {
          removeBtn.classList.remove('hidden');
          removeBtn.classList.add('flex');
        }
      } else {
        if (hiddenInput) hiddenInput.value = '';
        if (img) {
          img.src = '';
          img.classList.add('hidden');
        }
        if (fallback) fallback.classList.remove('hidden');
        if (removeBtn) {
          removeBtn.classList.add('hidden');
          removeBtn.classList.remove('flex');
        }
      }
    }

    let cropImg = null;
    let cropScale = 1;
    let cropOffsetX = 0;
    let cropOffsetY = 0;
    let isDraggingCrop = false;
    let startDragX = 0;
    let startDragY = 0;

    function handleDashPhotoSelect(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
          cropImg = new Image();
          cropImg.onload = function() {
            openCircleCropModal();
          };
          cropImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    }

    function openCircleCropModal() {
      if (!cropImg) return;
      cropScale = 1;
      cropOffsetX = 0;
      cropOffsetY = 0;
      document.getElementById('cropZoomRange').value = 1;
      document.getElementById('circleCropModal').classList.remove('hidden');
      setupCropCanvasEvents();
      updateCropCanvas();
      lucide.createIcons();
    }

    function closeCircleCropModal() {
      document.getElementById('circleCropModal').classList.add('hidden');
      document.getElementById('dashPhotoInput').value = '';
    }

    function updateCropCanvas() {
      const canvas = document.getElementById('cropCanvas');
      if (!canvas || !cropImg) return;
      const ctx = canvas.getContext('2d');
      const zoom = parseFloat(document.getElementById('cropZoomRange').value || 1);

      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const baseScale = Math.max(canvas.width / cropImg.width, canvas.height / cropImg.height);
      const drawWidth = cropImg.width * baseScale * zoom;
      const drawHeight = cropImg.height * baseScale * zoom;

      const drawX = (canvas.width - drawWidth) / 2 + cropOffsetX;
      const drawY = (canvas.height - drawHeight) / 2 + cropOffsetY;

      ctx.drawImage(cropImg, drawX, drawY, drawWidth, drawHeight);
    }

    function setupCropCanvasEvents() {
      const wrapper = document.getElementById('cropCanvasWrapper');
      if (!wrapper || wrapper.dataset.bound) return;
      wrapper.dataset.bound = "true";

      const startDrag = (e) => {
        isDraggingCrop = true;
        const pageX = e.touches ? e.touches[0].pageX : e.pageX;
        const pageY = e.touches ? e.touches[0].pageY : e.pageY;
        startDragX = pageX - cropOffsetX;
        startDragY = pageY - cropOffsetY;
      };

      const moveDrag = (e) => {
        if (!isDraggingCrop) return;
        const pageX = e.touches ? e.touches[0].pageX : e.pageX;
        const pageY = e.touches ? e.touches[0].pageY : e.pageY;
        cropOffsetX = pageX - startDragX;
        cropOffsetY = pageY - startDragY;
        updateCropCanvas();
      };

      const stopDrag = () => {
        isDraggingCrop = false;
      };

      wrapper.addEventListener('mousedown', startDrag);
      window.addEventListener('mousemove', moveDrag);
      window.addEventListener('mouseup', stopDrag);

      wrapper.addEventListener('touchstart', startDrag, { passive: true });
      window.addEventListener('touchmove', moveDrag, { passive: true });
      window.addEventListener('touchend', stopDrag);
    }

    function applyCircleCrop() {
      if (!cropImg) return;

      const outCanvas = document.createElement('canvas');
      outCanvas.width = 400;
      outCanvas.height = 400;
      const ctx = outCanvas.getContext('2d');

      const srcCanvas = document.getElementById('cropCanvas');
      ctx.drawImage(srcCanvas, 0, 0, 400, 400);

      // Export compressed JPEG (~35KB) to completely eliminate MySQL max_allowed_packet error!
      const croppedDataUrl = outCanvas.toDataURL('image/jpeg', 0.85);

      const partnerName = document.getElementById('partnerName').value || 'Partner';
      updatePartnerPhotoAvatar(croppedDataUrl, partnerName);
      closeCircleCropModal();
    }

    function removeDashPhoto() {
      const partnerName = document.getElementById('partnerName').value || 'Partner';
      document.getElementById('dashPhotoInput').value = '';
      updatePartnerPhotoAvatar('', partnerName);
    }

    function renderThemeContainer(p, data) {
      const templateId = p.template_id;
      const themeContainer = document.getElementById('themeContainer');
      const badge = document.getElementById('activePlanBadge');
      const tabBtn = document.getElementById('tabBtn-theme');
      const tabBtnLetters = document.getElementById('tabBtn-letters');
      const tabBtnTokens = document.getElementById('tabBtn-tokens');

      const nameLabel = document.getElementById('partnerNameLabel');
      const taglineLabel = document.getElementById('taglineQuoteLabel');
      const noteLabel = document.getElementById('loveNoteLabel');
      const taglineInput = document.getElementById('taglineQuote');

      if (templateId === 'birthday_magic') {
        if (nameLabel) nameLabel.innerText = "Birthday Person's Name *";
        if (taglineLabel) taglineLabel.innerText = "Custom Birthday Tagline / Motto *";
        if (taglineInput) taglineInput.placeholder = "e.g. Cheers to another year of awesome memories! 🥂";
        if (noteLabel) noteLabel.innerText = "Birthday Wish / Personal Message *";
      } else if (templateId === 'long_distance_love') {
        if (nameLabel) nameLabel.innerText = "Partner's First Name *";
        if (taglineLabel) taglineLabel.innerText = "Custom Quote / Tagline Banner *";
        if (taglineInput) taglineInput.placeholder = "e.g. Miles apart but connected by heart ✈️";
        if (noteLabel) noteLabel.innerText = "Short Love Note / Signature Message *";
      } else {
        if (nameLabel) nameLabel.innerText = "Partner's First Name *";
        if (taglineLabel) taglineLabel.innerText = "Custom Romantic Quote / Tagline Banner *";
        if (taglineInput) taglineInput.placeholder = "e.g. Safar Khubsurat h manjil se bhi 🌹";
        if (noteLabel) noteLabel.innerText = "Short Love Note / Signature Message *";
      }

      // Hide Sealed Letters & Love Tokens for non-anniversary themes
      if (templateId === 'anniversary_reveal') {
        tabBtnLetters.classList.remove('hidden');
        tabBtnTokens.classList.remove('hidden');
      } else {
        tabBtnLetters.classList.add('hidden');
        tabBtnTokens.classList.add('hidden');
      }

      if (templateId === 'birthday_magic') {
        badge.innerText = '✨ Managing: Birthday Magic Plan (Active)';
        tabBtn.innerText = 'Birthday & Reasons';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🎂 Birthday Settings &amp; Reasons</h3>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Date of Birth (Next Birthday Countdown)</label>
              <input type="date" id="partnerDob" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" value="${p.partner_dob || ''}">
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-[#4d444b]/30">
              <label class="block font-semibold text-[#d0c3cb]">Reasons I Love Celebrating You (Dynamic List)</label>
              <button type="button" onclick="addReasonRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Reason</button>
            </div>
            <div id="editReasonsList" class="space-y-2"></div>
          </div>
        `;
        renderReasonsList(data.reasons || []);

      } else if (templateId === 'perfect_proposal') {
        badge.innerText = '✨ Managing: Perfect Proposal Plan (Active)';
        tabBtn.innerText = 'Love Letter & Answer';

        const resp = data.proposal_response;
        const answerStatusHtml = resp ? `
          <div class="space-y-4 pt-1">
            <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center mx-auto shadow-lg">
              <i data-lucide="check-circle-2" class="w-7 h-7 text-[#eac34a]"></i>
            </div>
            <div class="space-y-1">
              <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#eac34a]">Partner's Answer 💕</span>
              <h4 class="text-2xl font-bold font-serif text-[#e8e0e3]">
                ${resp.response === 'yes' ? 'YES! A Thousand Times Yes 💍' : "Let's Talk 💕"}
              </h4>
              <p class="text-xs text-[#d0c3cb]/80">Responded on ${resp.responded_at_formatted || 'Recently'}</p>
            </div>
            <div class="bg-[#100d10] p-4 rounded-2xl border border-[#4d444b] text-sm font-serif italic text-[#eac34a] max-w-md mx-auto shadow-inner">
              "${resp.partner_note ? resp.partner_note : (resp.response === 'yes' ? 'YES! A thousand times YES my love! 💕' : 'Let\'s talk and celebrate together! 💕')}"
            </div>
          </div>
        ` : `
          <div class="space-y-2 py-4">
            <i data-lucide="clock" class="w-8 h-8 text-[#d0c3cb]/60 mx-auto"></i>
            <h4 class="text-sm font-bold text-[#e8e0e3]">Partner Has Not Answered Yet</h4>
            <p class="text-xs text-[#d0c3cb]/70">When your partner unlocks their surprise page and clicks "YES!" or "Let's Talk", their answer will appear here in real-time!</p>
          </div>
        `;

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">💍 Proposal Love Letter &amp; Live Answer Status</h3>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Full Proposal Love Letter Centerpiece</label>
              <textarea id="loveLetterText" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3]" rows="6">${p.love_letter_text || ''}</textarea>
            </div>
            <div class="bg-[#151215] p-5 rounded-2xl border border-[#eac34a]/40 text-center space-y-2">
              <span class="text-[10px] uppercase font-bold text-[#eac34a] block tracking-widest">LIVE PROPOSAL RESPONSE STATUS</span>
              ${answerStatusHtml}
            </div>
          </div>
        `;

      } else if (templateId === 'long_distance_love') {
        badge.innerText = '✨ Managing: Long Distance Love Plan (Active)';
        tabBtn.innerText = 'Cities & Reunion Date';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🌍 Dual Cities, Clocks &amp; Reunion Date</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Buyer City Name</label>
                <input type="text" id="buyerCity" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" placeholder="e.g. London" value="${p.buyer_city || ''}">
              </div>
              <div>
                <label class="block font-semibold text-[#d0c3cb] mb-1">Partner City Name</label>
                <input type="text" id="partnerCity" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" placeholder="e.g. Bangalore" value="${p.partner_city || ''}">
              </div>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Next Reunion Date (Live Countdown)</label>
              <input type="date" id="reunionDate" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" value="${p.reunion_date || ''}">
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Shared Spotify / Music Playlist URL</label>
              <input type="url" id="playlistUrl" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" placeholder="https://open.spotify.com/playlist/..." value="${p.playlist_url || ''}">
            </div>
          </div>
        `;

      } else if (templateId === 'raksha_bandhan_special') {
        badge.innerText = '✨ Managing: Raksha Bandhan Special Plan (Active)';
        tabBtn.innerText = 'Sibling Promises & Vows';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">🪔 Sibling Promises &amp; Protection Vows</h3>
            </div>
            <div class="flex items-center justify-between pt-1">
              <label class="block font-semibold text-[#d0c3cb]">Promises &amp; Protection Vows for Sibling (Dynamic List)</label>
              <button type="button" onclick="addReasonRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Promise</button>
            </div>
            <div id="editReasonsList" class="space-y-2"></div>
          </div>
        `;
        renderReasonsList(data.reasons || []);
      } else {
        // Default: anniversary_reveal
        badge.innerText = '✨ Managing: Anniversary Reveal Plan (Active)';
        tabBtn.innerText = 'Story Milestones';

        themeContainer.innerHTML = `
          <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
              <h3 class="text-base font-bold font-serif text-[#e8e0e3]">📍 Story Road Milestones</h3>
              <button type="button" onclick="addMilestoneRow()" class="px-3 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/30 hover:bg-[#eac34a] hover:text-black transition-all">+ Add Milestone</button>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Relationship Start Date (Live Together Counter)</label>
              <input type="date" id="relationshipStartDate" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3]" value="${p.relationship_start_date || ''}">
            </div>
            <div id="editMilestonesList" class="space-y-3"></div>
          </div>
        `;
        renderMilestonesList(data.milestones || []);
      }
    }

    function renderReasonsList(reasons) {
      const container = document.getElementById('editReasonsList');
      if (!container) return;
      if (reasons.length === 0) {
        reasons = ['Your contagious smile', 'The way you care for everyone', 'Our hilarious inside jokes'];
      }
      container.innerHTML = reasons.map((r, i) => `
        <div class="flex items-center gap-2">
          <input type="text" class="edit-reason-item w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2.5 text-xs text-[#e8e0e3]" value="${r}" placeholder="Reason ${i + 1}">
        </div>
      `).join('');
    }

    function addReasonRow() {
      const container = document.getElementById('editReasonsList');
      if (!container) return;
      const div = document.createElement('div');
      div.className = 'flex items-center gap-2';
      div.innerHTML = `<input type="text" class="edit-reason-item w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2.5 text-xs text-[#e8e0e3]" placeholder="New reason to celebrate">`;
      container.appendChild(div);
    }

    function switchTab(tabName) {
      const activeTemplateId = document.getElementById('activeTemplateId')?.value;

      ['general', 'theme', 'photos', 'letters', 'tokens', 'security'].forEach(t => {
        const btn = document.getElementById('tabBtn-' + t);
        const content = document.getElementById('tabContent-' + t);
        if (!btn || !content) return;

        if (t === tabName) {
          btn.className = "px-4 py-2.5 rounded-full text-xs font-bold bg-[#eac34a] text-[#241a00] shadow-[0_0_15px_rgba(234,195,74,0.3)] transition-all whitespace-nowrap shrink-0 cursor-pointer";
          content.classList.remove('hidden');
        } else {
          btn.className = "px-4 py-2.5 rounded-full text-xs font-bold bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] hover:text-white hover:border-[#eac34a]/40 transition-all whitespace-nowrap shrink-0 cursor-pointer";
          content.classList.add('hidden');
        }
      });

      // Enforce strict hidden state for letters & tokens on non-anniversary themes
      if (activeTemplateId && activeTemplateId !== 'anniversary_reveal') {
        const tabBtnLetters = document.getElementById('tabBtn-letters');
        const tabBtnTokens = document.getElementById('tabBtn-tokens');
        if (tabBtnLetters) tabBtnLetters.classList.add('hidden');
        if (tabBtnTokens) tabBtnTokens.classList.add('hidden');
      }
    }

    function renderMilestonesList(milestones) {
      const container = document.getElementById('editMilestonesList');
      if (!container) return;
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
      if (!container) return;
      const div = document.createElement('div');
      div.className = 'bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-2';
      div.innerHTML = `
        <input type="text" class="edit-m-title w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="New Milestone Title">
        <input type="date" class="edit-m-date w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]">
        <input type="text" class="edit-m-desc w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" placeholder="Description">
      `;
      container.appendChild(div);
    }

    let dashPhotosList = [];

    const SAMPLE_SCRAPBOOK_PHOTOS = [
      { url: 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=600&q=80', caption: 'Our First Date ☕' },
      { url: 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=600&q=80', caption: 'Best Friends Forever 👫' },
      { url: 'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=600&q=80', caption: 'Together Always 💑' },
      { url: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=600&q=80', caption: 'Squad Goals 🌟' },
      { url: 'https://images.unsplash.com/photo-1494774157365-9e04c6720e47?auto=format&fit=crop&w=600&q=80', caption: 'A Beautiful Smile 😊' },
      { url: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80', caption: 'Always Stunning 💫' }
    ];

    function renderPhotosList(media) {
      if (Array.isArray(media)) {
        dashPhotosList = media.map(m => {
          if (typeof m === 'string') return { url: m, caption: 'Moments of Joy' };
          return { url: m.file_path || '', caption: m.caption || 'Moments of Joy' };
        });
      } else {
        dashPhotosList = [];
      }
      renderDashScrapbookPhotos();
    }

    function updateDashPhotoCaption(index, val) {
      if (dashPhotosList[index]) {
        dashPhotosList[index].caption = val;
      }
    }

    function renderDashScrapbookPhotos() {
      const container = document.getElementById('dashScrapbookContainer');
      const countLabel = document.getElementById('dashSelectedPhotoCount');
      const sampleGrid = document.getElementById('dashSamplePhotosGrid');
      if (!container) return;

      if (countLabel) countLabel.innerText = `Selected: ${dashPhotosList.length} photos`;

      if (dashPhotosList.length === 0) {
        container.innerHTML = `
          <div class="col-span-full py-8 text-center space-y-2">
            <i data-lucide="image" class="w-8 h-8 text-[#d0c3cb]/50 mx-auto"></i>
            <p class="text-xs text-[#d0c3cb]">No scrapbook photos uploaded yet. Click "Upload Photos" or pick from sample gallery below!</p>
          </div>
        `;
      } else {
        container.innerHTML = dashPhotosList.map((item, i) => `
          <div class="rounded-2xl overflow-hidden border border-[#4d444b] relative group bg-[#100d10] p-1.5 shadow-md hover:border-[#eac34a] transition-all flex flex-col">
            <div class="relative w-full aspect-square rounded-xl overflow-hidden">
              <img src="${normalizeMediaUrlJs(item.url)}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=600&q=80'" class="w-full h-full object-cover">
              <button type="button" onclick="deleteDashPhoto(${i})" class="absolute top-2 right-2 bg-rose-900/90 hover:bg-rose-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shadow-lg transition-colors cursor-pointer border border-rose-400/40">
                ✕
              </button>
            </div>
            <input type="text" placeholder="✍️ Memory caption..." value="${escapeHtml(item.caption || '')}" oninput="updateDashPhotoCaption(${i}, this.value)" class="w-full bg-[#1b171b] border border-[#4d444b] rounded-lg px-2 py-1 text-[10px] text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none placeholder-[#8a7b85] mt-1.5">
          </div>
        `).join('');
      }

      if (sampleGrid) {
        sampleGrid.innerHTML = SAMPLE_SCRAPBOOK_PHOTOS.map(photo => {
          const isSel = dashPhotosList.some(p => p.url === photo.url);
          return `
            <div onclick="toggleDashSamplePhoto('${photo.url}')" class="aspect-square rounded-xl overflow-hidden border ${isSel ? 'border-[#eac34a] ring-2 ring-[#eac34a]/40' : 'border-[#4d444b]'} relative group cursor-pointer bg-[#100d10] hover:scale-105 transition-all">
              <img src="${photo.url}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=600&q=80'" class="w-full h-full object-cover">
              <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[9px] text-center py-0.5 px-1 truncate">${photo.caption}</div>
              <div class="absolute inset-0 bg-black/40 flex items-center justify-center ${isSel ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'} transition-opacity">
                <span class="px-2 py-1 rounded-md ${isSel ? 'bg-[#eac34a] text-[#241a00]' : 'bg-[#3b1e3b] text-[#eac34a]'} font-bold text-[10px]">
                  ${isSel ? '✓ Added' : '+ Add'}
                </span>
              </div>
            </div>
          `;
        }).join('');
      }

      if (typeof lucide === 'object') lucide.createIcons();
    }

    function deleteDashPhoto(index) {
      dashPhotosList.splice(index, 1);
      renderDashScrapbookPhotos();
    }

    function toggleDashSamplePhoto(url) {
      const idx = dashPhotosList.findIndex(p => p.url === url);
      if (idx > -1) {
        dashPhotosList.splice(idx, 1);
      } else {
        if (dashPhotosList.length >= 10) {
          alert('⚠️ Maximum limit of 10 photos reached! Please remove a photo before adding more.');
          return;
        }
        // Use image-specific caption
        const sampleObj = SAMPLE_SCRAPBOOK_PHOTOS.find(p => p.url === url);
        dashPhotosList.push({ url: url, caption: sampleObj ? sampleObj.caption : 'A Beautiful Memory' });
      }
      renderDashScrapbookPhotos();
    }

    function handleDashScrapbookFiles(e) {
      const files = e.target.files;
      if (!files || files.length === 0) return;

      Array.from(files).forEach(file => {
        if (dashPhotosList.length >= 10) {
          alert('⚠️ Maximum limit of 10 photos reached! Please remove a photo before adding more.');
          return;
        }
        const reader = new FileReader();
        reader.onload = function(evt) {
          const tempImg = new Image();
          tempImg.onload = function() {
            if (dashPhotosList.length >= 10) {
              alert('⚠️ Maximum limit of 10 photos reached!');
              return;
            }
            const canvas = document.createElement('canvas');
            const maxDim = 800;
            let w = tempImg.width;
            let h = tempImg.height;

            if (w > h && w > maxDim) {
              h = Math.round((h * maxDim) / w);
              w = maxDim;
            } else if (h > maxDim) {
              w = Math.round((w * maxDim) / h);
              h = maxDim;
            }

            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(tempImg, 0, 0, w, h);
            const compressedUrl = canvas.toDataURL('image/jpeg', 0.85);

            dashPhotosList.push({ url: compressedUrl, caption: 'Moments of Joy' });
            renderDashScrapbookPhotos();
          };
          tempImg.src = evt.target.result;
        };
        reader.readAsDataURL(file);
      });
      e.target.value = '';
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

      const templateId = document.getElementById('activeTemplateId').value;
      const templateFields = {};

      if (templateId === 'birthday_magic' || templateId === 'raksha_bandhan_special') {
        const pDob = document.getElementById('partnerDob')?.value;
        const reasons = [];
        document.querySelectorAll('#editReasonsList .edit-reason-item').forEach(inp => {
          if (inp.value.trim()) reasons.push(inp.value.trim());
        });
        if (pDob) templateFields.partner_dob = pDob;
        templateFields.reasons = reasons;

      } else if (templateId === 'perfect_proposal') {
        templateFields.love_letter_text = document.getElementById('loveLetterText')?.value;

      } else if (templateId === 'long_distance_love') {
        templateFields.buyer_city = document.getElementById('buyerCity')?.value;
        templateFields.partner_city = document.getElementById('partnerCity')?.value;
        templateFields.reunion_date = document.getElementById('reunionDate')?.value;
        templateFields.playlist_url = document.getElementById('playlistUrl')?.value;

      } else {
        // anniversary_reveal
        templateFields.relationship_start_date = document.getElementById('relationshipStartDate')?.value;
        const milestones = [];
        document.querySelectorAll('#editMilestonesList > div').forEach(div => {
          const title = div.querySelector('.edit-m-title')?.value;
          const date = div.querySelector('.edit-m-date')?.value;
          const desc = div.querySelector('.edit-m-desc')?.value;
          if (title) milestones.push({ title: title, date: date || '', description: desc || '' });
        });
        templateFields.milestones = milestones;
      }

      const dashMusicMode = document.querySelector('input[name="dash_music_mode"]:checked')?.value || 'itunes_search';
      let finalBgMusicUrl = document.getElementById('bgMusicUrl').value;
      let finalSongTitle = dashCurrentSongTitle || 'Selected Song';
      let finalSongArtist = dashCurrentArtist || 'Artist';

      if (dashMusicMode === 'itunes_search') {
        finalBgMusicUrl = dashCurrentMusicUrl || finalBgMusicUrl;
      } else if (dashMusicMode === 'youtube_link') {
        finalBgMusicUrl = document.getElementById('dashYoutubeUrlInput').value.trim() || finalBgMusicUrl;
        finalSongTitle = 'Custom YouTube Song';
        finalSongArtist = 'YouTube';
      }

      templateFields.song_title = finalSongTitle;
      templateFields.song_artist = finalSongArtist;

      const currentPass = document.getElementById('currentBuyerPassword')?.value.trim() || '';
      const newPass = document.getElementById('newBuyerPassword')?.value.trim() || '';
      const confirmPass = document.getElementById('confirmBuyerPassword')?.value.trim() || '';

      if (currentPass || newPass || confirmPass) {
        if (!currentPass) {
          msg.innerText = '❌ Please enter your current account password.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
        if (!newPass) {
          msg.innerText = '❌ Please enter your new account password.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
        if (newPass !== confirmPass) {
          msg.innerText = '❌ New password and confirmation password do not match.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
        if (newPass.length < 4) {
          msg.innerText = '❌ New password must be at least 4 characters long.';
          btn.disabled = false;
          btn.innerText = 'Save All Changes';
          return;
        }
      }

      const payload = {
        token: activeToken,
        partner_name: document.getElementById('partnerName').value,
        hint_question: document.getElementById('hintQuestion').value,
        hint_answer: document.getElementById('hintAnswer').value,
        current_buyer_password: currentPass,
        new_buyer_password: newPass,
        love_note_text: document.getElementById('loveNoteText').value,
        tagline_quote: document.getElementById('taglineQuote').value,
        bg_music_url: finalBgMusicUrl,
        song_title: finalSongTitle,
        song_artist: finalSongArtist,
        receiver_photo: document.getElementById('receiverPhotoUrl').value,
        letters: letters,
        tokens: tokens,
        media_photos: dashPhotosList,
        template_fields: templateFields
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
          loadDashboardData(activeToken);
          if (newPass) {
            document.getElementById('currentBuyerPassword').value = '';
            document.getElementById('newBuyerPassword').value = '';
            document.getElementById('confirmBuyerPassword').value = '';
          }
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

    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4 text-[#eac34a]"></i>';
      } else {
        input.type = 'password';
        btn.innerHTML = '<i data-lucide="eye" class="w-4 h-4 text-[#d0c3cb]"></i>';
      }
      if (typeof lucide === 'object') lucide.createIcons();
    }

    if (activeToken) {
      loadDashboardData(activeToken);
    }

    // Smart Smooth Auto-Hiding Header Script
    (function() {
      let lastScrollY = window.scrollY;
      const header = document.getElementById('editHeader');
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

    function showForgotPasswordModal() {
      const email = document.getElementById('loginEmail')?.value || '';
      if (email && document.getElementById('forgotPassEmail')) {
        document.getElementById('forgotPassEmail').value = email;
      }
      document.getElementById('forgotPasswordModal').classList.remove('hidden');
      if (typeof lucide === 'object') lucide.createIcons();
    }

    function closeForgotPasswordModal() {
      document.getElementById('forgotPasswordModal').classList.add('hidden');
      document.getElementById('forgotPassMsg').classList.add('hidden');
      document.getElementById('forgotPassOptions').classList.add('hidden');
    }

    async function handleRequestPasswordReset(e) {
      e.preventDefault();
      const email = document.getElementById('forgotPassEmail').value.trim();
      const btn = document.getElementById('forgotPassBtn');
      const msg = document.getElementById('forgotPassMsg');
      const options = document.getElementById('forgotPassOptions');
      const waBtn = document.getElementById('forgotWaBtn');

      if (!email) return;
      btn.innerText = 'Sending Reset Link...';
      btn.disabled = true;
      msg.classList.add('hidden');

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'request_reset', email: email })
        });
        const data = await res.json();

        msg.classList.remove('hidden');
        btn.disabled = false;
        btn.innerText = 'Send Password Reset Link';

        if (data.success) {
          msg.className = 'text-xs text-center p-3 rounded-xl bg-emerald-950/70 border border-emerald-500/40 text-emerald-300';
          msg.innerText = '✅ Password reset instructions sent! You can also use 1-Click WhatsApp support reset below.';
          if (data.whatsapp_link && waBtn) {
            waBtn.href = data.whatsapp_link;
            options.classList.remove('hidden');
          }
        } else {
          msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
          msg.innerText = '❌ ' + (data.message || 'Email not found. Please check your email address.');
        }
      } catch (err) {
        btn.disabled = false;
        btn.innerText = 'Send Password Reset Link';
        msg.classList.remove('hidden');
        msg.className = 'text-xs text-center p-3 rounded-xl bg-rose-950/70 border border-rose-500/40 text-rose-300';
        msg.innerText = '❌ Network error. Please try again.';
      }
    }
  </script>
</body>
</html>
