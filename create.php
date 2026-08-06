<?php
require_once __DIR__ . '/config/db.php';

$order_id = $_GET['order_id'] ?? '';
$order = null;
$error = null;

if ($order_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        $error = "Order not found. Please start from the template gallery.";
    } else if ($order['payment_status'] !== 'paid') {
        $error = "Payment required: The partner details form is only unlocked for paid orders.";
    }
} else {
    $error = "Missing order ID. Please select a template and complete checkout first.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Personalize Surprise — <?php echo APP_NAME; ?></title>
  
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
  <script src="<?php echo APP_URL; ?>/assets/js/compressor.js"></script>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>  <!-- Unified Global Navbar -->
  <?php 
  $current_page = 'create';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-12 relative z-10 space-y-8">

  <?php if ($error): ?>
    <div class="bg-[#221f21] rounded-3xl border border-[#eac34a]/30 p-8 text-center space-y-4 shadow-2xl">
      <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
        <i data-lucide="lock" class="w-6 h-6"></i>
      </div>
      <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Payment Verification Required</h3>
      <p class="text-xs text-[#d0c3cb]"><?php echo htmlspecialchars($error); ?></p>
      <a href="<?php echo APP_URL; ?>/#gallery" class="inline-block px-6 py-2.5 rounded-full bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider shadow-md hover:bg-[#ffe088]">
        Browse Templates &amp; Complete Order
      </a>
    </div>
  <?php else: ?>

    <!-- Order Header Pill -->
    <div class="flex items-center justify-between text-xs font-semibold text-[#d0c3cb]">
      <a href="<?php echo APP_URL; ?>" class="flex items-center gap-1 hover:text-[#eac34a] transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Templates</span>
      </a>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#3b1e3b] border border-[#e4b9df]/20 text-[#e4b9df]">
        <i data-lucide="shield-check" class="w-4 h-4 text-[#eac34a]"></i>
        <span>Order #<?php echo substr($order['order_id'], -8); ?> (Paid ₹<?php echo (int)$order['amount_paid']; ?>)</span>
      </div>
    </div>

    <!-- Title Box -->
    <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/30 shadow-2xl space-y-2">
      <span class="text-[10px] uppercase tracking-[0.2em] text-[#eac34a] font-bold">Personalization Form</span>
      <h1 class="text-3xl font-bold font-serif text-[#e8e0e3]">Build Your Surprise Reveal</h1>
      <p class="text-xs text-[#d0c3cb]">Fill in the details below to generate your unlisted private reveal page. You can edit these anytime later using your Email &amp; Password.</p>
    </div>

    <!-- Success Modal Card (Matches 5th SS Exact DOM Layout) -->
    <div id="successCard" class="hidden max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/60 shadow-[0_20px_50px_rgba(0,0,0,0.9)] text-center space-y-6">
      
      <!-- Glowing Heart Header Badge -->
      <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] mx-auto shadow-[0_0_25px_rgba(234,195,74,0.4)]">
        <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
          <i data-lucide="heart" class="w-7 h-7 text-[#eac34a] fill-[#eac34a]"></i>
        </div>
      </div>

      <div>
        <span class="text-[10px] uppercase tracking-[0.2em] text-[#eac34a] font-bold bg-[#3b1e3b] px-3.5 py-1 rounded-full border border-[#e4b9df]/20">
          ✨ SURPRISE PAGE GENERATED &amp; HASHED!
        </span>
        <h2 class="text-2xl font-bold font-serif text-[#e8e0e3] mt-3">Your Private Gift Link is Ready</h2>
        <p class="text-xs text-[#d0c3cb] mt-1 font-normal">
          Your custom surprise page is protected with your hint password gate and active for 12 months.
        </p>
      </div>
      
      <!-- Private Link Card -->
      <div class="bg-[#151215] p-5 rounded-2xl border border-[#4d444b] text-left space-y-2">
        <label class="text-[10px] uppercase font-bold text-[#eac34a] tracking-widest block">PRIVATE SUBDOMAIN LINK</label>
        <div class="flex gap-2">
          <input type="text" id="shareUrlInput" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs font-mono font-bold text-[#e8e0e3]" readonly>
          <button onclick="copyToClipboard('shareUrlInput')" class="px-4 py-2.5 bg-[#3b1e3b] border border-[#eac34a]/40 text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-xs rounded-xl uppercase shrink-0 transition-all">Copy</button>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <!-- Bright Green WhatsApp Button -->
        <a id="whatsappShareBtn" href="#" target="_blank" class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
          <i data-lucide="share-2" class="w-4 h-4"></i>
          <span>SHARE SURPRISE LINK ON WHATSAPP</span>
        </a>

        <!-- Yellow Open & Test Button -->
        <a id="previewBtn" href="#" target="_blank" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
          <i data-lucide="external-link" class="w-4 h-4"></i>
          <span>OPEN &amp; TEST LOCK SCREEN (HINT GATE)</span>
        </a>

        <!-- Bottom Action Links -->
        <div class="flex items-center justify-between pt-2 text-xs font-semibold text-[#d0c3cb]">
          <button type="button" onclick="showQrModal()" class="hover:text-[#eac34a] flex items-center gap-1.5">
            <i data-lucide="qr-code" class="w-4 h-4 text-[#eac34a]"></i>
            <span>Show Phone QR Code</span>
          </button>
          <a href="<?php echo APP_URL; ?>" class="hover:text-[#eac34a]">Done &amp; Return Home</a>
        </div>
      </div>
    </div>

    <!-- Main Creation Form -->
    <form id="createPageForm" class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#4d444b]/50 shadow-2xl space-y-6" onsubmit="handleFormSubmit(event)">
      <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
      <input type="hidden" name="template_id" value="<?php echo htmlspecialchars($order['template_id']); ?>">

      <div class="border-b border-[#4d444b]/40 pb-4">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">1. General Details</h3>
      </div>

      <div class="space-y-4 text-xs">
        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Partner's First Name *</label>
          <input type="text" name="partner_name" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Ananya" value="Ananya" required>
        </div>

        <!-- Gift Receiver Avatar Profile Photo Upload -->
        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1.5">Gift Receiver / Partner Profile Photo 🖼️ (Optional)</label>
          <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] flex flex-col sm:flex-row items-center gap-4">
            <div id="partnerAvatarContainer" class="w-16 h-16 rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#eac34a] flex items-center justify-center font-bold text-2xl shadow-[0_0_20px_rgba(234,195,74,0.3)] shrink-0 overflow-hidden">
              <span id="partnerAvatarFallback">A</span>
              <img id="partnerAvatarImg" src="" class="w-full h-full object-cover hidden">
            </div>
            <div class="flex-1 text-center sm:text-left space-y-2">
              <input type="file" id="receiverPhotoInput" accept="image/*" onchange="handleReceiverPhotoUpload(this)" class="hidden">
              <input type="hidden" name="receiver_photo" id="receiverPhotoData" value="">
              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <button type="button" onclick="document.getElementById('receiverPhotoInput').click()" class="px-4 py-2 bg-[#3b1e3b] hover:bg-[#eac34a] text-[#eac34a] hover:text-[#241a00] border border-[#eac34a]/40 font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shadow-md">
                  <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                  <span>Upload / Crop Photo</span>
                </button>
                <button type="button" onclick="removeCreatePhoto()" id="removeCreatePhotoBtn" class="px-3 py-2 bg-[#221f21] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs rounded-xl transition-all cursor-pointer hidden flex items-center gap-1.5">
                  <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                  <span>Remove Photo</span>
                </button>
              </div>
              <p class="text-[10px] text-[#d0c3cb]/70">Upload a portrait photo to show at the top of the surprise page. If no photo is added, partner's initial character will be displayed.</p>
            </div>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block font-semibold text-[#d0c3cb]">Custom Romantic Quote / Tagline Banner *</label>
            <button type="button" onclick="setPresetQuote('Safar Khubsurat h manjil se bhi 🌹')" class="text-[10px] text-[#eac34a] hover:underline font-bold">✨ Use "Safar Khubsurat..." Preset</button>
          </div>
          <input type="text" id="taglineQuoteInput" name="tagline_quote" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Safar Khubsurat h manjil se bhi 🌹" value="Safar Khubsurat h manjil se bhi 🌹" required>
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
              <input type="radio" name="music_mode" value="itunes_search" checked onchange="toggleMusicMode('itunes_search')" class="text-[#eac34a]">
              <div class="text-xs">
                <strong class="block text-[#e8e0e3]">🔍 Live Song Search</strong>
                <span class="text-[10px] text-[#d0c3cb]">Search any song / singer</span>
              </div>
            </label>

            <label class="flex items-center gap-2 p-3 bg-[#221f21] border border-[#4d444b] rounded-xl cursor-pointer hover:border-[#eac34a]">
              <input type="radio" name="music_mode" value="youtube_link" onchange="toggleMusicMode('youtube_link')" class="text-[#eac34a]">
              <div class="text-xs">
                <strong class="block text-[#e8e0e3]">🎥 YouTube Link</strong>
                <span class="text-[10px] text-[#d0c3cb]">Paste YouTube URL</span>
              </div>
            </label>
          </div>

          <!-- iTunes Live Search Container -->
          <div id="itunesSearchBox" class="space-y-3">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Search &amp; Select Song (Bollywood, Romantic, English, Punjabi) 🎶</label>
              <input type="text" id="itunesQueryInput" oninput="handleItunesSearch(this.value)" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none" placeholder="🔍 Type song name or singer e.g. Tum Hi Ho, Arijit Singh, Zara Sa, Kesariya, Taylor Swift...">
            </div>

            <!-- Selected Track Card -->
            <div id="selectedTrackCard" class="bg-[#100d10] p-3 rounded-xl border border-[#eac34a]/60 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img id="selectedTrackImg" src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80" class="w-10 h-10 rounded-lg object-cover border border-[#4d444b]">
                <div>
                  <span class="block font-bold text-xs text-[#e8e0e3]" id="selectedTrackTitle">Tum Hi Ho</span>
                  <span class="block text-[10px] text-[#eac34a]" id="selectedTrackArtist">Artist: Arijit Singh</span>
                </div>
              </div>
              <span class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 font-bold">✓ Selected</span>
            </div>

            <!-- Live Search Results Container -->
            <div id="itunesResultsList" class="hidden space-y-2 max-h-60 overflow-y-auto bg-[#100d10] p-2 rounded-xl border border-[#4d444b]"></div>
          </div>

          <!-- YouTube Link Container -->
          <div id="youtubeLinkBox" class="hidden space-y-3">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Paste YouTube Video / Audio URL 🎥</label>
              <input type="url" id="youtubeUrlInput" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/...">
              <p class="text-[10px] text-[#d0c3cb]/70 mt-1">Paste any public YouTube video link for your partner's favorite song.</p>
            </div>
          </div>
        </div>
        </div>

        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Short Love Note / Signature Message</label>
          <textarea name="love_note_text" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="3" placeholder="e.g. Ananya, every second with you feels like home. Happy Anniversary!">Every single day spent with you has been a beautiful gift. Happy Anniversary my love!</textarea>
        </div>
      </div>

      <div class="border-b border-[#4d444b]/40 pb-4 pt-4">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">2. Template Specific Fields</h3>
      </div>

      <?php if ($order['template_id'] === 'anniversary_reveal'): ?>
        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Relationship Start Date * (For live time counter)</label>
            <input type="date" name="relationship_start_date" value="2022-08-15" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>

          <div class="flex items-center justify-between pt-2">
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider">"Our Story" Milestones (3 to 6 Memories) *</label>
            <button type="button" onclick="addMilestoneCard()" class="px-3.5 py-1.5 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1">
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
              <span>Add Milestone</span>
            </button>
          </div>

          <div id="milestonesContainer" class="space-y-4">
            <!-- Dynamic Milestone Cards -->
          </div>
        </div>

      <?php elseif ($order['template_id'] === 'birthday_magic'): ?>
        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Partner's Date of Birth * (For next birthday countdown)</label>
            <input type="date" name="partner_dob" value="1998-11-20" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>

          <label class="block font-semibold text-[#d0c3cb] pt-2">Reasons I Love Celebrating You (3 to 5 entries)</label>
          <div class="space-y-2">
            <input type="text" name="reasons[]" value="Your infectious laugh that brightens any room" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Reason 1 e.g. Your infectious laugh that brightens any room" required>
            <input type="text" name="reasons[]" value="How you always remember to bring me chai on rainy afternoons" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Reason 2 e.g. How you always remember to bring me chai" required>
            <input type="text" name="reasons[]" value="Your kind, selfless and compassionate heart" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Reason 3 e.g. Your kind and compassionate heart" required>
          </div>
        </div>

      <?php elseif ($order['template_id'] === 'perfect_proposal'): ?>
        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Full Emotional Love Letter * (Centerpiece of your proposal)</label>
            <textarea name="love_letter_text" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="8" placeholder="Dearest Priya, I remember the exact moment I realized I wanted to spend the rest of my life with you..." required>Dearest Priya,

From the very first afternoon we shared a quiet coffee together under the soft rain, I knew in my heart that you were different. Your smile has a way of making the rest of the world fade into quiet background noise.

Through every milestone, every late-night conversation, and every spontaneous trip, you have been my favorite part of every day. With you, home is no longer a place—it's a feeling, and that feeling is wherever you are.

Today, I want to ask you the most important question of my life. Will you take my hand and walk through forever together with me?</textarea>
          </div>
        </div>

      <?php elseif ($order['template_id'] === 'long_distance_love'): ?>
        <div class="space-y-4 text-xs">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Your City *</label>
              <input type="text" name="buyer_city" value="Mumbai" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" placeholder="e.g. Mumbai" required>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Partner's City *</label>
              <input type="text" name="partner_city" value="London" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" placeholder="e.g. London" required>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Next Reunion Date &amp; Time * (For live countdown)</label>
            <input type="datetime-local" name="reunion_date" value="2026-09-15T18:00" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" required>
          </div>

          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Shared Song or Spotify Playlist Link (Optional)</label>
            <input type="url" name="playlist_url" value="https://open.spotify.com/track/0VjIjW4GlUZAMYd2vXMi3b" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" placeholder="https://open.spotify.com/track/...">
          </div>
        </div>
      <?php endif; ?>

      <!-- 3. Couple Photos Section (Matches 4th SS Exact DOM Layout) -->
      <div class="space-y-4 pt-4 border-t border-[#4d444b]/40">
        <div class="flex items-center justify-between">
          <div>
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider">COUPLE PHOTOS (SELECT 3-10 PHOTOS) *</label>
            <span class="text-[11px] text-[#d0c3cb]" id="selectedPhotoCount">Selected: 3/10 photos</span>
          </div>
          <button type="button" onclick="triggerFileInput()" class="px-4 py-2 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1.5">
            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
            <span>Upload Photos</span>
          </button>
          <input type="file" id="photoFileInput" accept="image/*" multiple class="hidden" onchange="handleFileSelect(event)">
        </div>

        <!-- Selected Uploads Grid -->
        <div class="bg-[#151215] p-4 rounded-3xl border border-[#4d444b] min-h-[120px]">
          <div id="photoPreviewContainer" class="flex flex-wrap gap-3">
            <!-- Dynamic Upload Thumbnails with X -->
          </div>
        </div>

        <!-- Quick Pick Sample Photos Gallery -->
        <div class="space-y-2 pt-2">
          <label class="text-[11px] font-bold uppercase tracking-wider text-[#eac34a] flex items-center gap-1">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            <span>QUICK PICK SAMPLE PHOTOS:</span>
          </label>
          <div class="grid grid-cols-3 sm:grid-cols-6 gap-3" id="samplePhotosGrid">
            <!-- Rendered by JS -->
          </div>
        </div>
      <div class="border-b border-[#4d444b]/40 pb-4 pt-4">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">4. Hint Security Gate &amp; Secret Password Setup</h3>
      </div>

      <div class="space-y-4 text-xs">
        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Secret Hint Question * (Asked to recipient on unlock screen)</label>
          <input type="text" id="hintQuestionInput" name="hint_question" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Where did we take our very first trip together in 2022?" value="Where did we take our very first trip together in 2022?" required>
          
          <div class="flex flex-wrap gap-2 mt-2">
            <span class="text-[10px] text-[#eac34a] font-bold self-center">✨ Preset Hints:</span>
            <button type="button" onclick="setHintPreset('Where did we take our very first trip together in 2022?', 'Shimla')" class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 hover:bg-[#eac34a] hover:text-[#241a00] transition-colors">"First Trip Location?"</button>
            <button type="button" onclick="setHintPreset('What is the nickname I call you when we are alone?', 'Piku')" class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 hover:bg-[#eac34a] hover:text-[#241a00] transition-colors">"My Secret Nickname?"</button>
            <button type="button" onclick="setHintPreset('Where was our first date cafe?', 'Starbucks')" class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 hover:bg-[#eac34a] hover:text-[#241a00] transition-colors">"First Date Cafe?"</button>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Secret Hint Answer * (Case-insensitive unlock key)</label>
          <input type="text" id="hintAnswerInput" name="hint_answer" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Shimla" value="Shimla" required>
        </div>

        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Custom Link Slug Path (Optional)</label>
          <input type="text" name="custom_slug" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. ananya-rohan (auto-generated if left blank)">
        </div>
      </div>

      <button type="submit" id="submitPageBtn" class="w-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-sans font-bold text-xs uppercase tracking-[0.2em] py-4 rounded-full shadow-[0_0_25px_rgba(234,195,74,0.3)] transition-all cursor-pointer">
        Generate &amp; Publish Secret Reveal Page →
      </button>
    </form>

  <?php endif; ?>

  </main>

  <!-- Footer -->
  <footer class="bg-[#100d10] text-[#d0c3cb] border-t border-[#4d444b]/30 relative z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <div class="space-y-4 md:col-span-1">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px]">
              <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
                <i data-lucide="heart" class="w-3.5 h-3.5 text-[#eac34a] fill-[#eac34a]/30"></i>
              </div>
            </div>
            <span class="text-xl font-bold font-serif text-[#e8e0e3]">SoulScript</span>
          </div>
          <p class="text-xs text-[#d0c3cb]/80 leading-relaxed font-light">
            Crafting personalized, private digital surprise pages for life's most meaningful romantic moments.
          </p>
        </div>

        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Templates</h4>
          <ul class="space-y-2 text-xs text-[#d0c3cb]/80">
            <li><a href="<?php echo APP_URL; ?>/#gallery" class="hover:text-[#eac34a]">Anniversary Reveal (₹499)</a></li>
            <li><a href="<?php echo APP_URL; ?>/#gallery" class="hover:text-[#eac34a]">Birthday Magic (₹399)</a></li>
            <li><a href="<?php echo APP_URL; ?>/#gallery" class="hover:text-[#eac34a]">Perfect Proposal (₹599)</a></li>
            <li><a href="<?php echo APP_URL; ?>/#gallery" class="hover:text-[#eac34a]">Long Distance Love (₹449)</a></li>
          </ul>
        </div>

        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Privacy &amp; Guarantee</h4>
          <ul class="space-y-2 text-xs text-[#d0c3cb]/80">
            <li class="flex items-center gap-2"><i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i><span>Hint Password Security Gate</span></li>
            <li class="flex items-center gap-2"><i data-lucide="eye-off" class="w-3.5 h-3.5 text-[#e4b9df]"></i><span>Search Engine Excluded</span></li>
            <li class="flex items-center gap-2"><i data-lucide="shield" class="w-3.5 h-3.5 text-[#eac34a]"></i><span>Razorpay Secured Payments</span></li>
          </ul>
        </div>

        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Need Support?</h4>
          <p class="text-xs text-[#d0c3cb]/80 leading-relaxed">support@soulscript.in</p>
          <p class="text-[11px] text-[#d0c3cb]/50">Includes 12-Month validity guarantee for all generated pages.</p>
        </div>
      </div>
    </div>
  </footer>

  <script>
    lucide.createIcons();

    let cropImg = null;
    let cropScale = 1;
    let cropOffsetX = 0;
    let cropOffsetY = 0;
    let isDraggingCrop = false;
    let startDragX = 0;
    let startDragY = 0;

    function handleReceiverPhotoUpload(input) {
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
      document.getElementById('receiverPhotoInput').value = '';
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

      // Export compressed JPEG (~35KB) to prevent MySQL packet errors
      const croppedDataUrl = outCanvas.toDataURL('image/jpeg', 0.85);

      const partnerName = document.querySelector('input[name="partner_name"]')?.value || 'Partner';
      updatePartnerPhotoAvatar(croppedDataUrl, partnerName);
      closeCircleCropModal();
    }

    function updatePartnerPhotoAvatar(photoUrl, partnerName) {
      const fallback = document.getElementById('partnerAvatarFallback');
      const img = document.getElementById('partnerAvatarImg');
      const removeBtn = document.getElementById('removeCreatePhotoBtn');
      const hiddenInput = document.getElementById('receiverPhotoData');

      const nameChar = (partnerName || 'P').charAt(0).toUpperCase();
      if (fallback) fallback.innerText = nameChar;

      if (photoUrl && photoUrl.trim() !== '') {
        if (hiddenInput) hiddenInput.value = photoUrl;
        if (img) {
          img.src = photoUrl;
          img.classList.remove('hidden');
        }
        if (fallback) fallback.classList.add('hidden');
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

    function removeCreatePhoto() {
      const partnerName = document.querySelector('input[name="partner_name"]')?.value || 'Partner';
      document.getElementById('receiverPhotoInput').value = '';
      updatePartnerPhotoAvatar('', partnerName);
    }

    // Sample Photos Registry (Exact match with 4th SS)
    const SAMPLE_PHOTOS = [
      'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80'
    ];

    let selectedPhotoUrls = [SAMPLE_PHOTOS[0], SAMPLE_PHOTOS[1], SAMPLE_PHOTOS[2]];

    // Initialize Milestones & Sample Photos on DOM load
    document.addEventListener('DOMContentLoaded', () => {
      initDefaultMilestones();
      renderPhotoPicker();
    });

    // --- DYNAMIC MILESTONES MANAGER (Matches 1st & 3rd SS) ---
    function initDefaultMilestones() {
      const container = document.getElementById('milestonesContainer');
      if (!container) return;

      container.innerHTML = '';
      const defaults = [
        { title: 'The Day We First Met', date: '2022-08-15', desc: 'Met at the cozy coffee shop on a rainy afternoon.' },
        { title: 'Our First Snow Trip', date: '2022-12-25', desc: 'Watched the winter snowfall under the pines.' },
        { title: 'One Year Celebration', date: '2023-08-15', desc: '365 days of non-stop laughter and memories.' }
      ];

      defaults.forEach((m, idx) => addMilestoneCard(m.title, m.date, m.desc));
    }

    function addMilestoneCard(title = '', date = '', desc = '') {
      const container = document.getElementById('milestonesContainer');
      if (!container) return;

      const idx = container.children.length + 1;
      const card = document.createElement('div');
      card.className = 'bg-[#151215] p-5 rounded-2xl border border-[#4d444b] space-y-3 relative group';

      card.innerHTML = `
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-[#eac34a]">Milestone #${idx}</span>
          <button type="button" onclick="removeMilestoneCard(this)" class="text-[#d0c3cb] hover:text-rose-400 p-1 transition-colors">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <input type="date" name="milestone_date[]" class="bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" value="${date}" required>
          <input type="text" name="milestone_title[]" class="bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Title e.g. First Trip Together" value="${title}" required>
        </div>
        <textarea name="milestone_desc[]" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="2" placeholder="Short description...">${desc}</textarea>
      `;

      container.appendChild(card);
      renumberMilestones();
      lucide.createIcons();
    }

    function removeMilestoneCard(btn) {
      const container = document.getElementById('milestonesContainer');
      if (container.children.length <= 1) {
        alert('Please keep at least 1 milestone!');
        return;
      }
      btn.closest('.group').remove();
      renumberMilestones();
    }

    function renumberMilestones() {
      const container = document.getElementById('milestonesContainer');
      Array.from(container.children).forEach((card, idx) => {
        const titleSpan = card.querySelector('span');
        if (titleSpan) titleSpan.innerText = `Milestone #${idx + 1}`;
      });
    }

    // --- PHOTO PICKER MANAGER (Matches 4th SS Exact UI Layout) ---
    function triggerFileInput() {
      document.getElementById('photoFileInput').click();
    }

    async function handleFileSelect(e) {
      const files = Array.from(e.target.files);
      for (let file of files) {
        try {
          const compressed = await compressImage(file, 1600, 1600, 0.82);
          selectedPhotoUrls.push(compressed);
        } catch (err) {
          console.error(err);
        }
      }
      renderPhotoPicker();
    }

    function removePhoto(idx) {
      if (selectedPhotoUrls.length <= 1) {
        alert('Please keep at least 1 photo!');
        return;
      }
      selectedPhotoUrls.splice(idx, 1);
      renderPhotoPicker();
    }

    function toggleSamplePhoto(url) {
      const idx = selectedPhotoUrls.indexOf(url);
      if (idx > -1) {
        if (selectedPhotoUrls.length <= 1) {
          alert('Please keep at least 1 photo!');
          return;
        }
        selectedPhotoUrls.splice(idx, 1);
      } else {
        selectedPhotoUrls.push(url);
      }
      renderPhotoPicker();
    }

    function renderPhotoPicker() {
      const countElem = document.getElementById('selectedPhotoCount');
      if (countElem) countElem.innerText = `Selected: ${selectedPhotoUrls.length}/10 photos`;

      // Render Selected Uploads
      const prevContainer = document.getElementById('photoPreviewContainer');
      if (prevContainer) {
        prevContainer.innerHTML = selectedPhotoUrls.map((url, idx) => `
          <div class="relative w-20 h-20 rounded-2xl overflow-hidden border-2 border-[#eac34a]/60 group bg-[#100d10] shrink-0">
            <img src="${url}" class="w-full h-full object-cover">
            <button type="button" onclick="removePhoto(${idx})" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-black/80 text-white flex items-center justify-center text-xs font-bold hover:bg-rose-600 transition-colors">
              ✕
            </button>
          </div>
        `).join('');
      }

      // Render Quick Pick Sample Gallery with Golden Checkmark
      const sampleGrid = document.getElementById('samplePhotosGrid');
      if (sampleGrid) {
        sampleGrid.innerHTML = SAMPLE_PHOTOS.map((url, idx) => {
          const isSelected = selectedPhotoUrls.includes(url);
          return `
            <div onclick="toggleSamplePhoto('${url}')" class="relative aspect-square rounded-2xl overflow-hidden border-2 cursor-pointer transition-all ${isSelected ? 'border-[#eac34a] shadow-[0_0_15px_rgba(234,195,74,0.4)] scale-95' : 'border-[#4d444b] opacity-60 hover:opacity-100'}">
              <img src="${url}" class="w-full h-full object-cover">
              ${isSelected ? `
                <div class="absolute inset-0 bg-[#eac34a]/20 flex items-center justify-center">
                  <div class="w-6 h-6 rounded-full bg-[#eac34a] text-[#241a00] flex items-center justify-center text-xs font-extrabold shadow-md">✓</div>
                </div>
              ` : ''}
            </div>
          `;
        }).join('');
      }
    }

    let currentSelectedMusicUrl = 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3';
    let currentSelectedSongTitle = 'Tum Hi Ho';
    let currentSelectedArtist = 'Arijit Singh';
    let searchDebounceTimer = null;

    function setPresetQuote(quoteText) {
      const input = document.getElementById('taglineQuoteInput');
      if (input) input.value = quoteText;
    }

    function setHintPreset(question, answer) {
      const qInput = document.getElementById('hintQuestionInput');
      const aInput = document.getElementById('hintAnswerInput');
      if (qInput) qInput.value = question;
      if (aInput) aInput.value = answer;
    }

    function toggleMusicMode(mode) {
      const itunesBox = document.getElementById('itunesSearchBox');
      const ytBox = document.getElementById('youtubeLinkBox');
      const randBox = document.getElementById('randomSingerBox');

      if (itunesBox) itunesBox.classList.add('hidden');
      if (ytBox) ytBox.classList.add('hidden');
      if (randBox) randBox.classList.add('hidden');

      if (mode === 'itunes_search' && itunesBox) itunesBox.classList.remove('hidden');
      if (mode === 'youtube_link' && ytBox) ytBox.classList.remove('hidden');
      if (mode === 'random_singer' && randBox) randBox.classList.remove('hidden');
    }

    function cleanAttrStr(str) {
      if (!str) return '';
      return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function handleItunesSearch(query) {
      clearTimeout(searchDebounceTimer);
      const resultsContainer = document.getElementById('itunesResultsList');
      if (!query.trim()) {
        resultsContainer.classList.add('hidden');
        return;
      }

      searchDebounceTimer = setTimeout(async () => {
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
                <button type="button" onclick="selectItunesTrack('${cleanAttrStr(item.previewUrl)}', '${cleanAttrStr(item.trackName)}', '${cleanAttrStr(item.artistName)}', '${cleanAttrStr(item.artworkUrl100)}')" class="px-3 py-1.5 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-[11px] rounded-lg border border-[#eac34a]/40 shrink-0 transition-all cursor-pointer">
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

    function selectItunesTrack(previewUrl, trackName, artistName, imgUrl) {
      currentSelectedMusicUrl = previewUrl;
      currentSelectedSongTitle = trackName;
      currentSelectedArtist = artistName;

      document.getElementById('selectedTrackImg').src = imgUrl || 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80';
      document.getElementById('selectedTrackTitle').innerText = trackName;
      document.getElementById('selectedTrackArtist').innerText = 'Artist: ' + artistName;

      document.getElementById('itunesResultsList').classList.add('hidden');
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

    async function handleFormSubmit(e) {
      e.preventDefault();
      const btn = document.getElementById('submitPageBtn');
      btn.innerText = 'Creating Secret Page...';
      btn.disabled = true;

      const form = document.getElementById('createPageForm');
      const formData = new FormData(form);

      const milestones = [];
      const mTitles = formData.getAll('milestone_title[]');
      const mDates = formData.getAll('milestone_date[]');
      const mDescs = formData.getAll('milestone_desc[]');
      for (let i = 0; i < mTitles.length; i++) {
        if (mTitles[i]) {
          milestones.push({ title: mTitles[i], date: mDates[i] || '', description: mDescs[i] || '' });
        }
      }

      const reasons = formData.getAll('reasons[]').filter(r => r.trim() !== '');

      const musicMode = formData.get('music_mode') || 'itunes_search';
      let bgMusicUrl = '';
      let songTitle = '';
      let favoriteSingers = '';

      if (musicMode === 'itunes_search') {
        bgMusicUrl = currentSelectedMusicUrl;
        songTitle = currentSelectedSongTitle;
        favoriteSingers = currentSelectedArtist;
      } else if (musicMode === 'youtube_link') {
        const ytInput = document.getElementById('youtubeUrlInput');
        bgMusicUrl = ytInput ? ytInput.value.trim() : '';
        songTitle = 'YouTube Favorite Song';
        favoriteSingers = 'YouTube Track';
      } else {
        favoriteSingers = document.getElementById('favoriteSingerChoice').value || 'Arijit Singh';
        bgMusicUrl = 'random_singer';
        songTitle = 'Random Hit Track';
      }

      const payload = {
        order_id: formData.get('order_id'),
        partner_name: formData.get('partner_name'),
        tagline_quote: formData.get('tagline_quote'),
        favorite_singers: favoriteSingers,
        bg_music_url: bgMusicUrl,
        song_title: songTitle,
        love_note_text: formData.get('love_note_text'),
        hint_question: formData.get('hint_question'),
        hint_answer: formData.get('hint_answer'),
        custom_slug: formData.get('custom_slug'),
        photos: selectedPhotoUrls,
        template_fields: {
          relationship_start_date: formData.get('relationship_start_date') || null,
          partner_dob: formData.get('partner_dob') || null,
          love_letter_text: formData.get('love_letter_text') || null,
          buyer_city: formData.get('buyer_city') || null,
          partner_city: formData.get('partner_city') || null,
          reunion_date: formData.get('reunion_date') || null,
          playlist_url: formData.get('playlist_url') || null,
          milestones: milestones,
          reasons: reasons
        }
      };

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/create_page.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          form.classList.add('hidden');
          const successCard = document.getElementById('successCard');
          successCard.classList.remove('hidden');
          
          document.getElementById('shareUrlInput').value = data.share_url;
          document.getElementById('previewBtn').href = data.share_url;

          // WhatsApp Share Link
          const waText = encodeURIComponent(`I created a secret romantic surprise for you! ❤️ Open your gift link here: ${data.share_url}`);
          document.getElementById('whatsappShareBtn').href = `https://api.whatsapp.com/send?text=${waText}`;

          // Set QR image URL
          const qrImg = document.getElementById('qrCodeImg');
          if (qrImg) qrImg.src = `https://quickchart.io/qr?text=${encodeURIComponent(data.share_url)}&size=300`;

          window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Server error: ' + err.message);
      } finally {
        btn.innerText = 'Generate & Publish Secret Reveal Page →';
        btn.disabled = false;
      }
    }

    function copyToClipboard(inputId) {
      const input = document.getElementById(inputId);
      input.select();
      document.execCommand('copy');
      alert('Link copied to clipboard!');
    }

    function showQrModal() {
      const modal = document.getElementById('qrModal');
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    }

    function closeQrModal() {
      const modal = document.getElementById('qrModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

    function handleReceiverPhotoUpload(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('receiverPhotoPreview').src = e.target.result;
          document.getElementById('receiverPhotoData').value = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    }
  </script>

  <!-- Mobile QR Code Modal -->
  <div id="qrModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4" onclick="closeQrModal()">
    <div class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/50 max-w-sm w-full text-center space-y-4 shadow-2xl relative" onclick="event.stopPropagation()">
      <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Mobile QR Code</h3>
      <p class="text-xs text-[#d0c3cb]">Scan this QR code with any phone camera to open the gift link.</p>
      
      <div class="bg-white p-4 rounded-2xl inline-block shadow-lg mx-auto">
        <img id="qrCodeImg" src="" alt="Gift Page QR Code" class="w-48 h-48 object-contain mx-auto">
      </div>

      <div>
        <button onclick="closeQrModal()" class="px-6 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase rounded-full hover:bg-[#ffe088] transition-all cursor-pointer">
          Done &amp; Close
        </button>
      </div>
    </div>
  </div>

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

  <!-- Smart Smooth Auto-Hiding Header Script -->
  <script>
    (function() {
      let lastScrollY = window.scrollY;
      const header = document.getElementById('createHeader');
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
  </script>
</body>
</html>
