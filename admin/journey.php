<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$loginError = '';
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: " . APP_URL . "/admin/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_user'], $_POST['admin_pass'])) {
    if ($_POST['admin_user'] === ADMIN_USER && $_POST['admin_pass'] === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $loginError = 'Invalid admin username or password.';
    }
}

$isLoggedIn = !empty($_SESSION['admin_logged_in']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Our Journey & Master Production Roadmap — ' . APP_NAME;
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-20 relative z-10 space-y-10">

    <?php if (!$isLoggedIn): ?>
      <!-- LOGIN SCREEN -->
      <div class="max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="compass" class="w-7 h-7"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Admin Vault Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">Our Journey &amp; Master Production Roadmap</p>
        </div>
        <?php if ($loginError): ?>
          <div class="p-3 bg-rose-900/40 border border-rose-500/40 text-rose-300 rounded-xl text-xs font-semibold">
            <?php echo htmlspecialchars($loginError); ?>
          </div>
        <?php endif; ?>
        <form method="POST" class="space-y-4 text-left">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Username</label>
            <input type="text" name="admin_user" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Password</label>
            <input type="password" name="admin_pass" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#eac34a] to-[#d4af37] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:brightness-110 transition-all cursor-pointer">
            Unlock Journey Access
          </button>
        </form>
      </div>

    <?php else: ?>
      <?php require_once __DIR__ . '/nav_header.php'; ?>

      <!-- HERO BANNER -->
      <div class="bg-gradient-to-r from-[#2a172a] via-[#221f21] to-[#1c161f] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/30 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 text-[10px] uppercase font-extrabold tracking-widest">
            <i data-lucide="sparkles" class="w-3 h-3"></i>
            <span>Google Antigravity &times; SoulScript Architecture</span>
          </div>
          <h1 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3] leading-tight">
            Our Journey &amp; Master Production Roadmap 🚀
          </h1>
          <p class="text-xs sm:text-sm text-[#d0c3cb] leading-relaxed">
            The complete history of everything engineered from Day 1, paired with our live pre-launch checklist for the Raksha Bandhan 2026 public launch.
          </p>
        </div>
        <div class="grid grid-cols-2 gap-3 shrink-0 w-full md:w-auto">
          <div class="bg-[#151215]/80 p-4 rounded-2xl border border-[#eac34a]/20 text-center">
            <span class="text-[10px] text-[#d0c3cb]/70 uppercase font-bold block">Engineered Pillars</span>
            <span class="text-2xl font-black font-serif text-[#eac34a]">8 Pillars</span>
          </div>
          <div class="bg-[#151215]/80 p-4 rounded-2xl border border-[#a4e4b9]/20 text-center">
            <span class="text-[10px] text-[#d0c3cb]/70 uppercase font-bold block">Launch Ready</span>
            <span class="text-2xl font-black font-serif text-[#a4e4b9]">85%</span>
          </div>
        </div>
      </div>

      <!-- SECTION 1: THE JOURNEY (WHAT WE HAVE BUILT) -->
      <section class="space-y-6">
        <div class="flex items-center gap-3 border-b border-[#4d444b]/40 pb-3">
          <div class="w-8 h-8 rounded-xl bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center border border-[#eac34a]/30">
            <i data-lucide="milestone" class="w-4 h-4"></i>
          </div>
          <div>
            <h2 class="text-xl font-bold font-serif text-[#e8e0e3]">Chapter 1: The Complete SoulScript Journey (Built Together)</h2>
            <p class="text-xs text-[#d0c3cb]">From the very first prompt to the royal multi-experience platform we have today.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <!-- Pillar 1 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span>1. Core Multi-Theme Surprise Engine</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Engineered 5 dynamic celebration themes: Anniversary Timeline, Birthday Magic, Perfect Proposal (Interactive Yes/No), Long-Distance Love, and Raksha Bandhan Royal Luxury.
            </p>
          </div>

          <!-- Pillar 2 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="lock" class="w-4 h-4"></i>
                <span>2. Security, Cryptography &amp; QR Codes</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Secret password hint hashing (SHA-256 with salt), 64-character Buyer Edit Tokens, dynamic printable QR codes, and tamper-proof session gates.
            </p>
          </div>

          <!-- Pillar 3 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="hard-drive" class="w-4 h-4"></i>
                <span>3. Dual-Storage &amp; Auto-Healing Media Vault</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Dual-storage persistence on Hostinger (`uploads_persistent`), client/server WebP image compression, and PHP auto-healing protecting all customer photos from Git resets.
            </p>
          </div>

          <!-- Pillar 4 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="gift" class="w-4 h-4"></i>
                <span>4. Raksha Bandhan 3-Act Royal Experience</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Act 1: Sibling Nostalgia &amp; Remote Fight Meter; Act 2: 3D Shagun Lifafa with Scratch to Reveal &amp; Shahi Tamrapatra; Act 3: Amazon Cash Voucher + Sister Avatar Badge.
            </p>
          </div>

          <!-- Pillar 5 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>5. A4 Landscape Photobook PDF &amp; Poster</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Built 300 DPI high-resolution canvas compositing engine that generates a 6-page printable A4 landscape sibling photobook and a royal framed wall poster with QR codes.
            </p>
          </div>

          <!-- Pillar 6 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="ticket" class="w-4 h-4"></i>
                <span>6. Pure Lottery Draw &amp; Single-Table Vault</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Simplified vault allocation into a 100% pure random lottery draw (`ORDER BY RAND()`) and merged 3 separate breakdown tables into 1 clean unified master table.
            </p>
          </div>

          <!-- Pillar 7 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                <span>7. Sample Gallery &amp; Dynamic Pricing</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Created dedicated sample showcase gallery (`/sample_gallery.php`), live preview links, and admin control for dynamic template pricing.
            </p>
          </div>

          <!-- Pillar 8 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2 hover:border-[#eac34a]/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a] flex items-center gap-1.5">
                <i data-lucide="shield-alert" class="w-4 h-4"></i>
                <span>8. Amazon Affiliate Store &amp; Safe Reset Tool</span>
              </span>
              <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2 py-0.5 rounded-full font-bold">Completed</span>
            </div>
            <p class="text-[11px] text-[#d0c3cb] leading-relaxed">
              Added curated Amazon gift recommendation cards with affiliate tracking, and built a 1-click safe database reset tool that protects official demos and personal orders.
            </p>
          </div>

        </div>
      </section>

      <!-- SECTION 2: MASTER PENDING TASK CHECKLIST -->
      <section class="space-y-6">
        <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-950/60 text-rose-400 flex items-center justify-center border border-rose-500/30">
              <i data-lucide="list-checks" class="w-4 h-4"></i>
            </div>
            <div>
              <h2 class="text-xl font-bold font-serif text-[#e8e0e3]">Chapter 2: Master Pending Action Items (Pre-Launch Checklist)</h2>
              <p class="text-xs text-[#d0c3cb]">All remaining tasks to execute before the public festive marketing launch.</p>
            </div>
          </div>
          <span class="text-xs font-bold text-[#eac34a] bg-[#151215] px-3 py-1 rounded-full border border-[#eac34a]/30">
            9 Key Action Items
          </span>
        </div>

        <div class="space-y-4">

          <!-- Task 1 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">1</span>
                <span>🌐 Custom Domain Purchase &amp; Global Migration</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#2a1f0a] px-2.5 py-0.5 rounded-full border border-[#eac34a]/40 self-start sm:self-auto">Priority: High</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Purchase brand domain (e.g. <code>soulscript.in</code> / <code>soulscript.com</code>). In codebase, update <code>APP_URL</code> in <code>config/config.php</code>, Razorpay webhook callback URLs, OpenGraph WhatsApp preview meta tags, and QR code base links.
            </p>
          </div>

          <!-- Task 2 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">2</span>
                <span>✨ AI-Emoji &amp; Modern Aesthetic Content Polish</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#a4e4b9] bg-[#1e3b20] px-2.5 py-0.5 rounded-full border border-[#a4e4b9]/40 self-start sm:self-auto">Priority: Medium</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Review landing page, order creation steps, and surprise reveal cards to infuse modern emojis (✨ 🎁 👑 💖 🌸 📜 🪔 💫 🍫) and high-conversion copy.
            </p>
          </div>

          <!-- Task 3 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">3</span>
                <span>📱 Mobile Viewport &amp; Screen Overflow Fixes</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#2a1f0a] px-2.5 py-0.5 rounded-full border border-[#eac34a]/40 self-start sm:self-auto">Priority: High</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Audit small screens (320px–390px iPhone/Android) to ensure zero horizontal text clipping, perfectly scaled fight meter sliders, and responsive padding.
            </p>
          </div>

          <!-- Task 4 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">4</span>
                <span>🎁 Amazon India Gift Cards Bulk Purchase Guide</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#2a1f0a] px-2.5 py-0.5 rounded-full border border-[#eac34a]/40 self-start sm:self-auto">Priority: High</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Step-by-step guide for purchasing instant Amazon India e-Gift cards in batches (₹100, ₹150, ₹250, ₹500, ₹2000) and uploading CSV to <code>/admin/rakhi_vouchers.php</code>.
            </p>
          </div>

          <!-- Task 5 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">5</span>
                <span>🛒 Amazon Affiliate Curated Product Catalog</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#a4e4b9] bg-[#1e3b20] px-2.5 py-0.5 rounded-full border border-[#a4e4b9]/40 self-start sm:self-auto">Priority: Medium</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Populate <code>/admin/affiliate_settings.php</code> with top-selling Rakhi gifts (Cadbury hampers, luxury dry fruits, smartwatches, personalized keepsakes) using your Amazon Associate Tag.
            </p>
          </div>

          <!-- Task 6 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">6</span>
                <span>💳 Razorpay Live Mode Key Migration</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#2a1f0a] px-2.5 py-0.5 rounded-full border border-[#eac34a]/40 self-start sm:self-auto">Priority: High</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Set <code>RAZORPAY_KEY_ID</code> and <code>RAZORPAY_KEY_SECRET</code> to live mode credentials in <code>config/config.env.php</code> on Hostinger server.
            </p>
          </div>

          <!-- Task 7 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">7</span>
                <span>🔒 Lock / Hide System Reset Tool on Live Launch</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#2a1f0a] px-2.5 py-0.5 rounded-full border border-[#eac34a]/40 self-start sm:self-auto">Priority: High</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> After completing the 10-friends test and final wipe, remove the reset navigation button or require master password confirmation to eliminate accidental data loss.
            </p>
          </div>

          <!-- Task 8 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">8</span>
                <span>📄 Photobook PDF &amp; Poster Typography Polish</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#a4e4b9] bg-[#1e3b20] px-2.5 py-0.5 rounded-full border border-[#a4e4b9]/40 self-start sm:self-auto">Priority: Medium</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Enlarge sibling names in royal calligraphy font, optimize signature line spacing, and format sibling vows into clean cards.
            </p>
          </div>

          <!-- Task 9 -->
          <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b]/40 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <span class="text-sm font-bold text-[#e8e0e3] flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center text-xs font-bold shrink-0">9</span>
                <span>🎬 Aug 21 AI Viral Video Ad Generation</span>
              </span>
              <span class="text-[10px] uppercase font-bold text-[#a4e4b9] bg-[#1e3b20] px-2.5 py-0.5 rounded-full border border-[#a4e4b9]/40 self-start sm:self-auto">Priority: Medium</span>
            </div>
            <p class="text-xs text-[#d0c3cb] leading-relaxed">
              <strong>Action:</strong> Generate 3 emotional TV-commercial style video ads using Google Veo 2 / Luma Dream Machine + ElevenLabs Hindi voiceover + CapCut for Instagram Reels &amp; YouTube Shorts.
            </p>
          </div>

        </div>
      </section>

    <?php endif; ?>

  </main>
</body>
</html>
