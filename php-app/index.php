<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo APP_NAME; ?> — Romantic Surprise Websites</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Caveat:wght@600;700&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS CDN -->
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
  
  <!-- Lucide Icons & Razorpay -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    html, body {
      overflow-x: hidden !important;
      width: 100% !important;
      max-width: 100vw !important;
      position: relative;
    }
  </style>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans relative overflow-x-hidden min-h-screen">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <div class="absolute top-0 left-0 w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-0 right-0 w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Navbar -->
  <header class="sticky top-0 z-50 bg-[#151215]/95 backdrop-blur-xl border-b border-[#4d444b]/30 shadow-[0_4px_30px_rgba(0,0,0,0.5)]">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-3">
      <!-- Brand Logo -->
      <a href="<?php echo APP_URL; ?>" class="flex items-center gap-2.5 text-left group shrink-0">
        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] shadow-[0_0_15px_rgba(234,195,74,0.3)] group-hover:scale-105 transition-transform duration-300">
          <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
            <i data-lucide="heart" class="w-4 h-4 text-[#eac34a] fill-[#eac34a]/30 group-hover:fill-[#eac34a] transition-colors"></i>
          </div>
        </div>
        <div class="flex flex-col">
          <span class="text-xl sm:text-2xl font-bold tracking-wide text-[#e8e0e3] font-serif group-hover:text-[#eac34a] transition-colors leading-none">
            SoulScript
          </span>
          <span class="hidden sm:block text-[9px] uppercase tracking-[0.2em] text-[#eac34a] font-semibold mt-0.5 font-sans">
            Romantic Surprise Websites
          </span>
        </div>
      </a>

      <!-- Center Navigation (Desktop) -->
      <nav class="hidden md:flex items-center gap-8 font-sans text-xs uppercase tracking-[0.15em] font-semibold">
        <a href="<?php echo APP_URL; ?>" class="text-[#eac34a] border-b-2 border-[#eac34a] py-1 font-bold">
          Home
        </a>
        <a href="#gallery" class="text-[#d0c3cb] border-b-2 border-transparent hover:text-[#e4b9df] transition-colors py-1">
          Templates &amp; Pricing
        </a>
        <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" class="text-[#eac34a] hover:text-[#ffe088] flex items-center gap-1.5 transition-all py-1 border-b-2 border-transparent hover:border-[#eac34a]">
          <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
          <span>Live Demo</span>
        </a>
      </nav>

      <!-- Right Action Controls (Desktop) -->
      <div class="hidden md:flex items-center gap-3 shrink-0">
        <a href="<?php echo APP_URL; ?>/edit.php" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 border border-[#eac34a]/60 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] shadow-[0_0_15px_rgba(234,195,74,0.2)] transition-all">
          <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
          <span>Buyer Login</span>
        </a>

        <a href="#gallery" class="px-5 py-2 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] text-xs font-bold uppercase tracking-[0.15em] shadow-[0_0_20px_rgba(234,195,74,0.3)] hover:shadow-[0_0_30px_rgba(234,195,74,0.5)] hover:scale-105 transition-all duration-300 flex items-center gap-2">
          <i data-lucide="gift" class="w-3.5 h-3.5"></i>
          <span>Create Surprise</span>
        </a>
      </div>

      <!-- Mobile Controls (Hamburger Menu Button & Quick CTA) -->
      <div class="flex md:hidden items-center gap-2">
        <a href="#gallery" class="px-3 py-1.5 rounded-full bg-[#eac34a] text-[#241a00] text-[11px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-md">
          <i data-lucide="gift" class="w-3 h-3"></i>
          <span>Create</span>
        </a>

        <button onclick="toggleMobileNavMenu()" id="hamburgerBtn" aria-label="Toggle Menu" class="p-2 rounded-xl bg-[#221f21] border border-[#4d444b] text-[#e8e0e3] hover:text-[#eac34a] hover:border-[#eac34a] transition-all cursor-pointer">
          <i data-lucide="menu" id="hamburgerIcon" class="w-5 h-5"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer Overlay Menu -->
    <div id="mobileDrawerMenu" class="hidden md:hidden bg-[#151215]/98 border-b border-[#4d444b]/50 px-4 py-5 space-y-4 shadow-2xl backdrop-blur-2xl transition-all duration-300">
      <nav class="flex flex-col space-y-2.5 font-sans text-xs uppercase tracking-wider font-semibold">
        <a href="<?php echo APP_URL; ?>" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#3b1e3b]/60 text-[#eac34a] font-bold border border-[#eac34a]/30 flex items-center gap-2.5">
          <i data-lucide="home" class="w-4 h-4"></i>
          <span>Home</span>
        </a>
        <a href="#gallery" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] flex items-center gap-2.5">
          <i data-lucide="layout" class="w-4 h-4"></i>
          <span>Templates &amp; Pricing</span>
        </a>
        <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#221f21] text-[#eac34a] border border-[#4d444b] flex items-center gap-2.5">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          <span>Live Demo</span>
        </a>
        <a href="<?php echo APP_URL; ?>/edit.php" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/60 font-bold flex items-center gap-2.5">
          <i data-lucide="key-round" class="w-4 h-4"></i>
          <span>Buyer Login Portal</span>
        </a>
      </nav>

      <div class="pt-2 border-t border-[#4d444b]/40">
        <a href="#gallery" onclick="toggleMobileNavMenu()" class="w-full py-3 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] rounded-xl font-bold text-xs uppercase tracking-wider shadow-lg flex items-center justify-center gap-2">
          <i data-lucide="gift" class="w-4 h-4"></i>
          <span>Create Personalized Surprise</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="relative min-h-[85vh] w-full flex items-center justify-center pt-24 pb-16 md:pt-32 md:pb-24 z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col lg:flex-row items-center gap-16">
      
      <!-- Left Column -->
      <div class="w-full lg:w-1/2 flex flex-col items-start text-left gap-8">
        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-[#3b1e3b]/60 border border-[#e4b9df]/20 backdrop-blur-md shadow-sm">
          <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a] animate-pulse"></i>
          <span class="font-sans text-xs font-semibold text-[#eac34a] tracking-[0.2em] uppercase">
            The Ultimate Reveal
          </span>
        </div>

        <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-extrabold text-[#e8e0e3] leading-[1.15] tracking-tight">
          Turn Your Memories Into a 
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#e4b9df]">
            Digital Surprise.
          </span>
        </h1>

        <p class="font-sans text-xs sm:text-base text-[#d0c3cb] max-w-lg leading-relaxed font-normal">
          Craft breathtaking, personalized website experiences that build anticipation and deliver unforgettable romantic moments. Perfect for anniversaries, birthdays, proposals, or long-distance love.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 w-full sm:w-auto pt-2">
          <a href="#gallery" class="group relative px-6 sm:px-8 py-3.5 sm:py-4 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] rounded-full font-sans text-xs font-bold uppercase tracking-[0.15em] sm:tracking-[0.2em] shadow-[0_0_25px_rgba(234,195,74,0.3)] hover:shadow-[0_0_35px_rgba(234,195,74,0.6)] transition-all duration-500 flex items-center justify-center gap-2.5 cursor-pointer">
            <i data-lucide="gift" class="w-4 h-4 shrink-0"></i>
            <span class="font-bold tracking-wider">CHOOSE YOUR SURPRISE</span>
            <i data-lucide="arrow-right" class="w-4 h-4 shrink-0 group-hover:translate-x-1 transition-transform"></i>
          </a>

          <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" class="group flex items-center justify-center gap-3 px-6 py-3.5 sm:py-4 rounded-full border border-[#4d444b] hover:border-[#eac34a]/60 text-[#d0c3cb] hover:text-[#e8e0e3] transition-all duration-300 font-sans text-xs uppercase tracking-wider font-semibold cursor-pointer whitespace-nowrap">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-[#eac34a]/40 flex items-center justify-center group-hover:border-[#eac34a] group-hover:bg-[#eac34a]/10 transition-all shrink-0">
              <i data-lucide="play" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-[#eac34a] fill-[#eac34a]"></i>
            </div>
            <span class="whitespace-nowrap font-bold tracking-wider">TRY LIVE DEMO</span>
          </a>
        </div>

        <!-- Trust Highlights -->
        <div class="pt-6 flex flex-wrap items-center gap-6 text-xs text-[#d0c3cb]/80 border-t border-[#4d444b]/30 w-full">
          <div class="flex items-center gap-2">
            <i data-lucide="lock" class="w-4 h-4 text-[#eac34a]"></i>
            <span>Hint Password Gate</span>
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4 text-[#e4b9df]"></i>
            <span>Razorpay Secured</span>
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4 text-[#eac34a]"></i>
            <span>12 Months Live</span>
          </div>
        </div>
      </div>

      <!-- Right Column: Phone Mockup Frame & Background Spinning Circles -->
      <div class="w-full lg:w-1/2 flex justify-center lg:justify-end relative overflow-hidden py-4">
        <!-- Spinning Background Outline Circles -->
        <div class="absolute top-1/4 left-4 sm:-left-8 w-64 h-64 rounded-full border border-[#eac34a]/20 animate-spin z-10 pointer-events-none" style="animation-duration: 20s;"></div>
        <div class="absolute bottom-10 right-4 sm:-right-8 w-48 h-48 rounded-full border border-[#e4b9df]/20 animate-spin z-10 pointer-events-none" style="animation-duration: 25s; animation-direction: reverse;"></div>

        <!-- Phone Container -->
        <div class="relative w-[300px] sm:w-[330px] h-[620px] bg-[#100d10] rounded-[3rem] border-[6px] border-[#2d292b] shadow-[0_30px_60px_rgba(0,0,0,0.8)] overflow-hidden transform rotate-[-2deg] hover:rotate-0 hover:scale-105 transition-all duration-700 ease-out z-20">
          <div class="w-full h-full relative bg-[#151215] flex flex-col justify-between p-6 text-center">
            <div class="absolute top-0 inset-x-0 h-6 flex justify-center z-50">
              <div class="w-1/3 h-4 bg-[#100d10] rounded-b-xl"></div>
            </div>

            <div class="pt-8 flex flex-col items-center gap-4">
              <div class="w-14 h-14 rounded-full bg-[#3b1e3b] border border-[#eac34a]/40 flex items-center justify-center text-[#eac34a] shadow-[0_0_20px_rgba(234,195,74,0.2)]">
                <i data-lucide="heart" class="w-7 h-7 fill-[#eac34a]/30"></i>
              </div>
              <div>
                <span class="font-serif text-2xl text-[#e8e0e3] font-bold block">For Sarah</span>
                <span class="font-sans text-xs text-[#eac34a] tracking-widest uppercase mt-1 block">A Night To Remember</span>
              </div>
            </div>

            <div class="relative aspect-4/3 rounded-2xl overflow-hidden border border-[#eac34a]/20 shadow-md">
              <img src="https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&q=80&w=800" alt="Preview" class="w-full h-full object-cover opacity-80">
              <div class="absolute inset-0 bg-gradient-to-t from-[#151215] via-transparent to-transparent"></div>
            </div>

            <div class="space-y-3 pb-4">
              <div class="w-full bg-[#221f21] border border-[#4d444b] rounded-xl py-3 px-4 text-xs font-mono text-[#d0c3cb]/60">
                Enter the memory...
              </div>
              <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" class="w-full bg-[#eac34a] text-[#241a00] font-sans text-xs font-bold uppercase tracking-wider py-3 rounded-xl shadow-md flex items-center justify-center gap-2 hover:bg-[#ffe088] transition-all">
                <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                <span>Unlock Memory</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Interactive Sample Demos Banner -->
  <section class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-20">
    <div class="bg-[#221f21] border border-[#eac34a]/30 rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-[0_20px_40px_rgba(0,0,0,0.5)]">
      <div class="space-y-2 text-center md:text-left">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#3b1e3b] text-[#e4b9df] text-xs font-semibold uppercase tracking-wider">
          <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
          <span>Interactive Sample Demos Ready</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold font-serif text-[#e8e0e3]">
          Experience how your partner will unlock their surprise
        </h3>
        <p class="text-xs sm:text-sm text-[#d0c3cb]">
          Test our live password hint gate! Try the sample pages for Anniversary Reveal and Perfect Proposal right now.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3 shrink-0">
        <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" class="px-5 py-2.5 rounded-full bg-[#151215] text-[#eac34a] hover:bg-[#3b1e3b] font-semibold text-xs border border-[#eac34a]/40 shadow-sm transition-all flex items-center gap-1.5">
          <i data-lucide="heart" class="w-4 h-4 fill-[#eac34a]"></i>
          <span>Anniversary Demo (Hint: shimla)</span>
        </a>

        <a href="<?php echo APP_URL; ?>/gift/priya-aman" target="_blank" class="px-5 py-2.5 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          <span>Proposal Demo (Hint: paris)</span>
        </a>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="py-20 bg-[#1e1b1d] border-y border-[#4d444b]/30 relative z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center space-y-3 mb-16">
        <span class="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-[#eac34a]">Simple 3-Step Creation</span>
        <h2 class="text-3xl font-bold font-serif text-[#e8e0e3]">How SoulScript Works</h2>
        <p class="text-sm text-[#d0c3cb] max-w-xl mx-auto">
          Build a personalized, password-protected digital gift in under 5 minutes from your phone or laptop.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Step 1 -->
        <div class="relative bg-[#221f21] p-8 rounded-2xl border border-[#4d444b]/50 space-y-4 hover:border-[#eac34a]/40 transition-all duration-300 shadow-xl group">
          <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] text-[#e4b9df] font-serif font-bold text-xl flex items-center justify-center border border-[#e4b9df]/30 group-hover:scale-110 transition-transform">
            1
          </div>
          <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Choose Occasion Template</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Pick from Anniversary Reveal (₹499), Birthday Magic (₹399), Perfect Proposal (₹599), or Long Distance Love (₹449). Pay securely via Razorpay.
          </p>
        </div>

        <!-- Step 2 -->
        <div class="relative bg-[#221f21] p-8 rounded-2xl border border-[#4d444b]/50 space-y-4 hover:border-[#eac34a]/40 transition-all duration-300 shadow-xl group">
          <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-serif font-bold text-xl flex items-center justify-center border border-[#eac34a]/30 group-hover:scale-110 transition-transform">
            2
          </div>
          <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Add Memories &amp; Hint Key</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Upload your favorite photos, milestone dates, love letters, and a soundtrack. Create a secret hint question that only your partner knows how to answer!
          </p>
        </div>

        <!-- Step 3 -->
        <div class="relative bg-[#221f21] p-8 rounded-2xl border border-[#4d444b]/50 space-y-4 hover:border-[#eac34a]/40 transition-all duration-300 shadow-xl group">
          <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] text-[#ffe088] font-serif font-bold text-xl flex items-center justify-center border border-[#ffe088]/30 group-hover:scale-110 transition-transform">
            3
          </div>
          <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Share Private Link</h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed">
            Receive your custom link (`soulscript.in/gift/ananya-rohan`). Send it via WhatsApp or Instagram. Your partner unlocks it to reveal the surprise!
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Templates & Pricing Section (Exact TemplateGallery.tsx DOM Layout) -->
  <section id="gallery" class="py-24 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center space-y-4 max-w-3xl mx-auto mb-14">
      <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#e4b9df]/20 text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="gift" class="w-3.5 h-3.5 text-[#eac34a]"></i>
        <span>Select Your Occasion</span>
      </div>
      <h2 class="text-3xl sm:text-5xl font-bold font-serif text-[#e8e0e3] tracking-tight">
        Surprise Gift Templates &amp; Pricing
      </h2>
      <p class="text-xs sm:text-sm text-[#d0c3cb] leading-relaxed">
        Every template is engineered with custom per-occasion logic, interactive countdowns, tailored forms, and responsive visual layouts.
      </p>
      <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-3"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      
      <!-- Card 1: Anniversary Reveal -->
      <div class="bg-[#221f21] rounded-3xl border border-[#4d444b]/50 hover:border-[#eac34a]/40 shadow-2xl transition-all duration-300 flex flex-col overflow-hidden group">
        <div class="relative h-64 bg-[#100d10] overflow-hidden">
          <img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=800&q=80" alt="Anniversary Reveal" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-90">
          <div class="absolute inset-0 bg-gradient-to-t from-[#221f21] via-transparent to-transparent"></div>
          <div class="absolute top-4 left-4 bg-[#151215]/80 backdrop-blur-md text-[#e4b9df] border border-[#e4b9df]/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            Most Popular
          </div>
          <div class="absolute top-4 right-4 bg-[#eac34a] text-[#241a00] font-extrabold text-base px-4 py-1 rounded-xl shadow-md">
            ₹499
          </div>
          <div class="absolute bottom-4 left-4 right-4 text-[#e8e0e3] space-y-1">
            <h3 class="text-2xl font-bold font-serif">Anniversary Reveal</h3>
            <p class="text-xs text-[#eac34a] font-medium">Celebrate Your Journey Together</p>
          </div>
        </div>
        <div class="p-6 sm:p-8 flex-1 flex flex-col justify-between space-y-6">
          <p class="text-xs text-[#d0c3cb] leading-relaxed font-normal">
            A timeless, elegant narrative celebrating your relationship milestone. Features a chronological memory timeline, live "together for" time counter, and signed love note.
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-[#151215] p-5 rounded-2xl border border-[#4d444b]">
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>Collected Fields:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Relationship Start Date</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>3-6 Timeline Milestones</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Personalized Love Note</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>5-10 Photo Gallery</span></li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#e4b9df]"></i>
                <span>Result Features:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Live Time Counter</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Vertical Story Timeline</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Captured Moments Gallery</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Signed Love Note Card</span></li>
              </ul>
            </div>
          </div>
          <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
            <button onclick="openCheckout('anniversary_reveal', 'Anniversary Reveal', 499)" class="w-full sm:flex-1 py-3.5 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-[0.15em] shadow-[0_0_20px_rgba(234,195,74,0.3)] transition-all flex items-center justify-center gap-2 cursor-pointer">
              <i data-lucide="gift" class="w-4 h-4"></i>
              <span>Customize (₹499)</span>
            </button>
            <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" class="w-full sm:w-auto px-5 py-3.5 rounded-full bg-[#151215] hover:bg-[#3b1e3b] text-[#e8e0e3] font-semibold text-xs border border-[#4d444b] transition-all flex items-center justify-center gap-1.5 shrink-0">
              <i data-lucide="eye" class="w-4 h-4 text-[#eac34a]"></i>
              <span>Live Sample</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 2: Birthday Magic -->
      <div class="bg-[#221f21] rounded-3xl border border-[#4d444b]/50 hover:border-[#eac34a]/40 shadow-2xl transition-all duration-300 flex flex-col overflow-hidden group">
        <div class="relative h-64 bg-[#100d10] overflow-hidden">
          <img src="https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=800&q=80" alt="Birthday Magic" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-90">
          <div class="absolute inset-0 bg-gradient-to-t from-[#221f21] via-transparent to-transparent"></div>
          <div class="absolute top-4 left-4 bg-[#151215]/80 backdrop-blur-md text-[#e4b9df] border border-[#e4b9df]/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            Festive Joy
          </div>
          <div class="absolute top-4 right-4 bg-[#eac34a] text-[#241a00] font-extrabold text-base px-4 py-1 rounded-xl shadow-md">
            ₹399
          </div>
          <div class="absolute bottom-4 left-4 right-4 text-[#e8e0e3] space-y-1">
            <h3 class="text-2xl font-bold font-serif">Birthday Magic</h3>
            <p class="text-xs text-[#eac34a] font-medium">Celebrate Their Special Day</p>
          </div>
        </div>
        <div class="p-6 sm:p-8 flex-1 flex flex-col justify-between space-y-6">
          <p class="text-xs text-[#d0c3cb] leading-relaxed font-normal">
            Vibrant and joyous celebration page. Unveil reasons why you love them, celebratory confetti animations, auto-calculated next birthday countdown, and photo memories.
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-[#151215] p-5 rounded-2xl border border-[#4d444b]">
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>Collected Fields:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Partner Date of Birth</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>3-5 Reasons to Celebrate</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Personalized Note</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>5-10 Photo Gallery</span></li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#e4b9df]"></i>
                <span>Result Features:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Next Birthday Countdown</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Confetti Animations</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Interactive Reasons List</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Festive Header Banner</span></li>
              </ul>
            </div>
          </div>
          <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
            <button onclick="openCheckout('birthday_magic', 'Birthday Magic', 399)" class="w-full sm:flex-1 py-3.5 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-[0.15em] shadow-[0_0_20px_rgba(234,195,74,0.3)] transition-all flex items-center justify-center gap-2 cursor-pointer">
              <i data-lucide="gift" class="w-4 h-4"></i>
              <span>Customize (₹399)</span>
            </button>
            <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" class="w-full sm:w-auto px-5 py-3.5 rounded-full bg-[#151215] hover:bg-[#3b1e3b] text-[#e8e0e3] font-semibold text-xs border border-[#4d444b] transition-all flex items-center justify-center gap-1.5 shrink-0">
              <i data-lucide="eye" class="w-4 h-4 text-[#eac34a]"></i>
              <span>Live Sample</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 3: Perfect Proposal -->
      <div class="bg-[#221f21] rounded-3xl border border-[#4d444b]/50 hover:border-[#eac34a]/40 shadow-2xl transition-all duration-300 flex flex-col overflow-hidden group">
        <div class="relative h-64 bg-[#100d10] overflow-hidden">
          <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=800&q=80" alt="Perfect Proposal" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-90">
          <div class="absolute inset-0 bg-gradient-to-t from-[#221f21] via-transparent to-transparent"></div>
          <div class="absolute top-4 left-4 bg-[#3b2d12]/90 backdrop-blur-md text-[#eac34a] border border-[#eac34a]/60 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            Premium
          </div>
          <div class="absolute top-4 right-4 bg-[#eac34a] text-[#241a00] font-extrabold text-base px-4 py-1 rounded-xl shadow-md">
            ₹599
          </div>
          <div class="absolute bottom-4 left-4 right-4 text-[#e8e0e3] space-y-1">
            <h3 class="text-2xl font-bold font-serif">Perfect Proposal</h3>
            <p class="text-xs text-[#eac34a] font-medium">Build Suspense To The Ultimate Question</p>
          </div>
        </div>
        <div class="p-6 sm:p-8 flex-1 flex flex-col justify-between space-y-6">
          <p class="text-xs text-[#d0c3cb] leading-relaxed font-normal">
            Build suspense to the ultimate question. Includes full emotional love letter centerpiece, captured memories gallery, and interactive response capture (YES 💍 or Let's Talk 💬).
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-[#151215] p-5 rounded-2xl border border-[#4d444b]">
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>Collected Fields:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Full Emotional Love Letter</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Secret Password Q&amp;A</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>5-10 Photo Gallery</span></li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#e4b9df]"></i>
                <span>Result Features:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>"Will You Marry Me?" Header</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Love Letter Centerpiece</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Interactive YES! 💍 Response</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Real-Time Buyer Notification</span></li>
              </ul>
            </div>
          </div>
          <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
            <button onclick="openCheckout('perfect_proposal', 'Perfect Proposal', 599)" class="w-full sm:flex-1 py-3.5 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-[0.15em] shadow-[0_0_20px_rgba(234,195,74,0.3)] transition-all flex items-center justify-center gap-2 cursor-pointer">
              <i data-lucide="gift" class="w-4 h-4"></i>
              <span>Customize (₹599)</span>
            </button>
            <a href="<?php echo APP_URL; ?>/gift/priya-aman" target="_blank" class="w-full sm:w-auto px-5 py-3.5 rounded-full bg-[#151215] hover:bg-[#3b1e3b] text-[#e8e0e3] font-semibold text-xs border border-[#4d444b] transition-all flex items-center justify-center gap-1.5 shrink-0">
              <i data-lucide="eye" class="w-4 h-4 text-[#eac34a]"></i>
              <span>Live Sample</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 4: Long Distance Love -->
      <div class="bg-[#221f21] rounded-3xl border border-[#4d444b]/50 hover:border-[#eac34a]/40 shadow-2xl transition-all duration-300 flex flex-col overflow-hidden group">
        <div class="relative h-64 bg-[#100d10] overflow-hidden">
          <img src="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80" alt="Long Distance Love" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-90">
          <div class="absolute inset-0 bg-gradient-to-t from-[#221f21] via-transparent to-transparent"></div>
          <div class="absolute top-4 left-4 bg-[#151215]/80 backdrop-blur-md text-[#e4b9df] border border-[#e4b9df]/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            Across Distance
          </div>
          <div class="absolute top-4 right-4 bg-[#eac34a] text-[#241a00] font-extrabold text-base px-4 py-1 rounded-xl shadow-md">
            ₹449
          </div>
          <div class="absolute bottom-4 left-4 right-4 text-[#e8e0e3] space-y-1">
            <h3 class="text-2xl font-bold font-serif">Long Distance Love</h3>
            <p class="text-xs text-[#eac34a] font-medium">Bridge Miles With Shared Memories</p>
          </div>
        </div>
        <div class="p-6 sm:p-8 flex-1 flex flex-col justify-between space-y-6">
          <p class="text-xs text-[#d0c3cb] leading-relaxed font-normal">
            Bridge miles with shared memories. Features side-by-side dual city clocks, live reunion countdown timer, shared soundtrack link, and memory gallery.
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-[#151215] p-5 rounded-2xl border border-[#4d444b]">
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[#eac34a]"></i>
                <span>Collected Fields:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Buyer &amp; Partner Cities</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Next Reunion Date &amp; Time</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>Shared Playlist URL</span></li>
                <li class="flex items-start gap-1"><span class="text-[#eac34a">•</span><span>5-10 Photo Gallery</span></li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold text-[#e8e0e3] text-xs mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#e4b9df]"></i>
                <span>Result Features:</span>
              </h4>
              <ul class="space-y-1.5 text-[11px] text-[#d0c3cb]">
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Dual City Clocks</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Live Reunion Countdown</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Music Player Widget</span></li>
                <li class="flex items-start gap-1"><span class="text-[#e4b9df">•</span><span>Our Journey Gallery</span></li>
              </ul>
            </div>
          </div>
          <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
            <button onclick="openCheckout('long_distance_love', 'Long Distance Love', 449)" class="w-full sm:flex-1 py-3.5 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-[0.15em] shadow-[0_0_20px_rgba(234,195,74,0.3)] transition-all flex items-center justify-center gap-2 cursor-pointer">
              <i data-lucide="gift" class="w-4 h-4"></i>
              <span>Customize (₹449)</span>
            </button>
            <a href="<?php echo APP_URL; ?>/gift/ananya-rohan" target="_blank" class="w-full sm:w-auto px-5 py-3.5 rounded-full bg-[#151215] hover:bg-[#3b1e3b] text-[#e8e0e3] font-semibold text-xs border border-[#4d444b] transition-all flex items-center justify-center gap-1.5 shrink-0">
              <i data-lucide="eye" class="w-4 h-4 text-[#eac34a]"></i>
              <span>Live Sample</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- Security & Privacy Section -->
  <section class="py-20 bg-[#100d10] text-[#e8e0e3] border-t border-[#4d444b]/30 relative z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#3b1e3b] border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-wider">
            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
            <span>Encrypted Hint Password Protection</span>
          </div>

          <h2 class="text-3xl sm:text-4xl font-bold font-serif leading-tight">
            Your Love Story Stays 100% Private &amp; Protected
          </h2>

          <p class="text-xs sm:text-sm text-[#d0c3cb] leading-relaxed">
            We safeguard your personal love letters, photos, and memories with enterprise-grade security protocols:
          </p>

          <div class="space-y-4 text-xs text-[#d0c3cb]">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center shrink-0 border border-[#eac34a]/30">
                <i data-lucide="key-round" class="w-4 h-4"></i>
              </div>
              <div>
                <strong class="text-[#e8e0e3] block font-semibold text-sm">Hashed Hint Verification</strong>
                Your answer is stored as an encrypted SHA-256 hash. Surprise content is blocked until your partner passes the hint test.
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-[#3b1e3b] text-[#e4b9df] flex items-center justify-center shrink-0 border border-[#e4b9df]/30">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
              </div>
              <div>
                <strong class="text-[#e8e0e3] block font-semibold text-sm">Cooldown Brute-Force Guard</strong>
                A 60-second lockout automatically engages after 5 incorrect password attempts to stop guessing.
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-[#3b1e3b] text-[#ffe088] flex items-center justify-center shrink-0 border border-[#ffe088]/30">
                <i data-lucide="share-2" class="w-4 h-4"></i>
              </div>
              <div>
                <strong class="text-[#e8e0e3] block font-semibold text-sm">Noindex Search Protection</strong>
                All generated pages include <code class="bg-[#221f21] text-[#eac34a] px-1.5 py-0.5 rounded">noindex, nofollow</code> headers so search engines will never crawl your memory page.
              </div>
            </div>
          </div>
        </div>

        <!-- Lock Screen Mini Preview Card -->
        <div class="bg-[#221f21] border border-[#eac34a]/30 rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative overflow-hidden">
          <div class="flex items-center justify-between pb-4 border-b border-[#4d444b]/50">
            <div class="flex items-center gap-2 text-xs font-mono text-[#d0c3cb]">
              <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
              <span>soulscript.in/gift/ananya-rohan</span>
            </div>
            <span class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] border border-[#e4b9df]/30 px-2 py-0.5 rounded font-mono">Protected</span>
          </div>

          <div class="text-center space-y-3 py-4">
            <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30 shadow-[0_0_15px_rgba(234,195,74,0.2)]">
              <i data-lucide="key-round" class="w-7 h-7"></i>
            </div>
            <h4 class="text-xl font-bold font-serif text-[#e8e0e3]">Hint Security Gate</h4>
            <p class="text-xs text-[#d0c3cb]">"Where did we take our very first trip together in 2022?"</p>
          </div>

          <div class="space-y-3">
            <div class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#d0c3cb]/60 font-mono text-center">
              Enter memory answer...
            </div>
            <div class="w-full bg-[#eac34a] text-[#241a00] font-sans font-bold text-xs uppercase tracking-widest py-3 rounded-full text-center shadow-md">
              Unlock The Surprise ❤️
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-20 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center space-y-3 mb-16">
      <span class="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-[#eac34a]">Loved By Couples</span>
      <h2 class="text-3xl font-bold font-serif text-[#e8e0e3]">Real Romantic Moments</h2>
      <p class="text-sm text-[#d0c3cb]">Surprising partners across Mumbai, Delhi, Bangalore, Jaipur, and worldwide.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "My husband was speechless on our 2nd anniversary! When he unlocked the link with our secret trip answer and saw the live time counter and timeline of our photos, he literally had tears in his eyes."
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Rohan &amp; Ananya</span>
          <span class="text-[#eac34a] text-[11px]">Anniversary Reveal</span>
        </div>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "I used the Perfect Proposal template for Priya in Jaipur. When she clicked 'YES ❤️', I got an instant response while she was smiling at me! Worth every rupee."
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Aman &amp; Priya</span>
          <span class="text-[#eac34a] text-[11px]">Perfect Proposal</span>
        </div>
      </div>

      <div class="bg-[#221f21] p-6 rounded-2xl border border-[#4d444b]/50 shadow-xl space-y-4">
        <div class="flex items-center gap-1 text-[#eac34a]">
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
          <i data-lucide="star" class="w-4 h-4 fill-[#eac34a]"></i>
        </div>
        <p class="text-xs text-[#d0c3cb] leading-relaxed italic">
          "Living in London while my girlfriend is in Bangalore is tough. The Long Distance Love template with dual city clocks and reunion countdown made us feel so connected."
        </p>
        <div class="pt-3 border-t border-[#4d444b]/30 flex items-center justify-between text-xs">
          <span class="font-bold text-[#e8e0e3]">Vikram &amp; Sneha</span>
          <span class="text-[#eac34a] text-[11px]">Long Distance Love</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Buyer Login CTA Banner -->
  <section class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-12 z-10 relative">
    <div class="bg-gradient-to-r from-[#3b1e3b] via-[#221f21] to-[#3b1e3b] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-6">
      <div class="space-y-2 text-center sm:text-left">
        <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-[#eac34a] bg-[#151215] px-3 py-1 rounded-full border border-[#eac34a]/30">🔑 Already Bought A Gift Website?</span>
        <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">Log In To Edit &amp; Update Your Surprise Page</h3>
        <p class="text-xs text-[#d0c3cb]">Update your quote, background music, favorite singers, photo scrapbook, or milestone dates anytime using your Email &amp; Secret Password.</p>
      </div>
      <a href="<?php echo APP_URL; ?>/edit.php" class="px-8 py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-full shadow-xl transition-all shrink-0 flex items-center gap-2">
        <i data-lucide="key-round" class="w-4 h-4"></i>
        <span>Buyer Login Portal →</span>
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-[#100d10] border-t border-[#4d444b]/30 py-16 relative z-10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        
        <!-- Brand Column -->
        <div class="space-y-4 md:col-span-1">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] shadow-[0_0_15px_rgba(234,195,74,0.3)]">
              <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
                <i data-lucide="heart" class="w-3.5 h-3.5 text-[#eac34a] fill-[#eac34a]/30"></i>
              </div>
            </div>
            <span class="text-xl font-bold font-serif text-[#e8e0e3] tracking-tight">SoulScript</span>
          </div>
          <p class="text-xs text-[#d0c3cb]/80 leading-relaxed font-light">
            Crafting personalized, private digital surprise pages for life's most meaningful romantic moments.
          </p>
          <div class="flex items-center gap-2 text-[11px] text-[#eac34a] bg-[#3b1e3b] border border-[#e4b9df]/20 px-3 py-1.5 rounded-full w-fit">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
            <span>100% Private Link-Only Pages</span>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Templates</h4>
          <ul class="space-y-2 text-xs text-[#d0c3cb]/80">
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Anniversary Reveal (₹499)</a></li>
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Birthday Magic (₹399)</a></li>
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Perfect Proposal (₹599)</a></li>
            <li><a href="#gallery" class="hover:text-[#eac34a] transition-colors">Long Distance Love (₹449)</a></li>
          </ul>
        </div>

        <!-- Security & Privacy -->
        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Privacy &amp; Guarantee</h4>
          <ul class="space-y-2 text-xs text-[#d0c3cb]/80">
            <li class="flex items-center gap-2">
              <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
              <span>Hint Password Security Gate</span>
            </li>
            <li class="flex items-center gap-2">
              <i data-lucide="eye-off" class="w-3.5 h-3.5 text-[#e4b9df]"></i>
              <span>Search Engine Excluded</span>
            </li>
            <li class="flex items-center gap-2">
              <i data-lucide="shield" class="w-3.5 h-3.5 text-[#eac34a]"></i>
              <span>Razorpay Secured Payments</span>
            </li>
          </ul>
        </div>

        <!-- Support -->
        <div class="space-y-3">
          <h4 class="text-xs font-semibold text-[#e8e0e3] uppercase tracking-widest font-mono">Need Support?</h4>
          <p class="text-xs text-[#d0c3cb]/80 leading-relaxed">
            Questions about creating or delivering your surprise link?
          </p>
          <p class="text-xs text-[#eac34a] font-medium font-mono">support@soulscript.in</p>
          <p class="text-[11px] text-[#d0c3cb]/50">Includes 12-Month validity guarantee for all generated pages.</p>
        </div>
      </div>

      <div class="pt-8 border-t border-[#4d444b]/30 flex flex-col sm:flex-row items-center justify-between text-xs text-[#d0c3cb]/60 gap-4">
        <span>© 2026 SoulScript. All rights reserved. Made with ❤️ for couples everywhere.</span>
        <a href="<?php echo APP_URL; ?>/admin/index.php" class="text-[11px] text-[#d0c3cb]/40 hover:text-[#eac34a] transition-colors">Admin Portal</a>
      </div>
    </div>
  </footer>

  <!-- Checkout Modal -->
  <div id="checkoutModal" class="modal-overlay fixed inset-0 bg-black/85 backdrop-blur-md z-50 hidden overflow-y-auto p-4 sm:p-6 flex items-start sm:items-center justify-center py-8 sm:py-12">
    <div class="modal-container bg-[#191518] border border-[#eac34a]/40 rounded-3xl max-w-lg w-full p-6 sm:p-8 relative my-auto max-h-[90vh] overflow-y-auto shadow-2xl">
      <button class="modal-close text-[#d0c3cb] hover:text-white text-2xl absolute top-4 right-4 p-1 cursor-pointer transition-colors z-10" onclick="closeCheckout()">&times;</button>
      <h2 class="font-serif text-2xl text-[#e8e0e3] font-bold mb-1 pr-6" id="modalTemplateTitle">Checkout</h2>
      <p class="text-xs text-[#d0c3cb] mb-5">Enter your details to unlock your partner personalization form.</p>
      
      <form id="checkoutForm" onsubmit="handleCheckoutSubmit(event)">
        <input type="hidden" id="selectedTemplateId" value="">
        
        <div id="checkoutErrorMsg" class="hidden mb-4 p-3 bg-[#3b1e3b] border border-[#e4b9df]/40 text-[#e4b9df] rounded-xl text-xs font-semibold text-center"></div>

        <div class="form-group mb-4">
          <label class="form-label text-xs font-semibold text-[#d0c3cb] block mb-1">Your Full Name (Buyer) *</label>
          <input type="text" id="buyerName" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Rohan Sharma" minlength="2" maxlength="60" required>
        </div>

        <div class="form-group mb-4">
          <label class="form-label text-xs font-semibold text-[#d0c3cb] block mb-1">WhatsApp Mobile Number *</label>
          <div class="flex rounded-xl overflow-hidden border border-[#4d444b] focus-within:border-[#eac34a] bg-[#100d10] transition-colors">
            <div class="bg-[#221f21] text-[#eac34a] font-mono text-xs font-bold px-3.5 flex items-center border-r border-[#4d444b] gap-1.5 shrink-0 select-none">
              <span>🇮🇳</span>
              <span>+91</span>
            </div>
            <input type="tel" id="buyerPhone" class="w-full bg-transparent px-3 py-3 text-sm text-[#e8e0e3] focus:outline-none font-mono tracking-wider placeholder-[#d0c3cb]/40" placeholder="9876543210" pattern="^[6-9]\d{9}$" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
          </div>
          <span class="text-[10px] text-[#d0c3cb]/60 mt-1 block">Enter 10-digit mobile number (starts with 6, 7, 8, or 9)</span>
        </div>

        <div class="form-group mb-4">
          <label class="form-label text-xs font-semibold text-[#d0c3cb] block mb-1">Email Address *</label>
          <input type="email" id="buyerEmail" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="rohan@example.com" required>
        </div>

        <div class="form-group mb-4">
          <div class="flex items-center justify-between mb-1">
            <label class="form-label text-xs font-semibold text-[#d0c3cb]">Create Secret Edit Password *</label>
            <span class="text-[10px] text-[#eac34a] font-bold" id="passStrengthBadge">Min 6 chars</span>
          </div>
          <div class="relative">
            <input type="password" id="buyerPassword" minlength="6" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 pr-10 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none font-mono" placeholder="••••••••" oninput="checkPasswordStrength(this.value)" required>
            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-3 text-[#d0c3cb] hover:text-[#eac34a] transition-colors">
              <i data-lucide="eye" id="passEyeIcon" class="w-4 h-4"></i>
            </button>
          </div>
          <span class="text-[10px] text-[#d0c3cb]/70 mt-1.5 block leading-relaxed">
            🔒 You will use this Secret Password to log into your Buyer Portal at <strong class="text-[#eac34a]">soulscript.in/edit</strong> anytime.
          </span>
        </div>

        <div class="my-6 p-4 bg-[#100d10] border border-[#4d444b] rounded-xl flex justify-between items-center">
          <span class="text-xs text-[#d0c3cb]">Total Amount</span>
          <span class="font-serif text-xl font-extrabold text-[#eac34a]" id="modalPrice">₹499</span>
        </div>

        <button type="submit" class="w-full bg-[#eac34a] text-[#241a00] font-sans text-xs font-bold uppercase tracking-wider py-3.5 rounded-full hover:bg-[#ffe088] transition-all cursor-pointer shadow-lg" id="checkoutBtn">
          Proceed to Pay &amp; Personalize →
        </button>

        <button type="button" onclick="simulateDevPayment()" class="w-full mt-3 bg-transparent text-[#eac34a] border border-[#eac34a]/40 font-sans text-xs font-semibold py-2.5 rounded-full hover:border-[#eac34a] transition-all cursor-pointer">
          ⚡ Test Mode: Instant Skip Payment &amp; Personalize
        </button>
      </form>
    </div>
  </div>

  <script>
    lucide.createIcons();

    let currentTemplateId = '';
    let currentPrice = 0;

    function checkPasswordStrength(pass) {
      const badge = document.getElementById('passStrengthBadge');
      if (!badge) return;
      if (pass.length < 6) {
        badge.innerText = 'Weak (Min 6 chars)';
        badge.className = 'text-[10px] text-rose-400 font-bold';
      } else if (pass.length < 10) {
        badge.innerText = 'Good Password ✓';
        badge.className = 'text-[10px] text-amber-400 font-bold';
      } else {
        badge.innerText = 'Strong Password 💪';
        badge.className = 'text-[10px] text-emerald-400 font-bold';
      }
    }

    function togglePasswordVisibility() {
      const passInput = document.getElementById('buyerPassword');
      const eyeIcon = document.getElementById('passEyeIcon');
      if (passInput.type === 'password') {
        passInput.type = 'text';
        eyeIcon.setAttribute('data-lucide', 'eye-off');
      } else {
        passInput.type = 'password';
        eyeIcon.setAttribute('data-lucide', 'eye');
      }
      lucide.createIcons();
    }

    function openCheckout(templateId, name, price) {
      currentTemplateId = templateId;
      currentPrice = price;
      document.getElementById('selectedTemplateId').value = templateId;
      document.getElementById('modalTemplateTitle').innerText = name;
      document.getElementById('modalPrice').innerText = '₹' + price;
      const modal = document.getElementById('checkoutModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeCheckout() {
      const modal = document.getElementById('checkoutModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    async function handleCheckoutSubmit(e) {
      e.preventDefault();
      const errBox = document.getElementById('checkoutErrorMsg');
      errBox.classList.add('hidden');

      const rawPhone = document.getElementById('buyerPhone').value.trim();
      const cleanPhone = rawPhone.replace(/[^0-9]/g, '');

      if (!/^[6-9]\d{9}$/.test(cleanPhone)) {
        errBox.innerText = 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.';
        errBox.classList.remove('hidden');
        return;
      }

      const buyerPassword = document.getElementById('buyerPassword').value;
      if (buyerPassword.length < 6) {
        errBox.innerText = 'Please create a Secret Edit Password with at least 6 characters.';
        errBox.classList.remove('hidden');
        return;
      }

      const fullPhone = '+91' + cleanPhone;

      const btn = document.getElementById('checkoutBtn');
      btn.innerText = 'Creating Order...';
      btn.disabled = true;

      const payload = {
        buyer_name: document.getElementById('buyerName').value,
        buyer_phone: fullPhone,
        buyer_email: document.getElementById('buyerEmail').value,
        buyer_password: buyerPassword,
        template_id: currentTemplateId
      };

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/create_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          const options = {
            key: data.razorpay_key_id,
            amount: currentPrice * 100,
            currency: 'INR',
            name: 'SoulScript',
            description: 'Surprise Reveal Page Order',
            order_id: data.order.razorpay_order_id,
            handler: async function (response) {
              await fetch('<?php echo APP_URL; ?>/api/webhook_razorpay.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  order_id: data.order.order_id,
                  razorpay_payment_id: response.razorpay_payment_id,
                  status: 'paid'
                })
              });
              window.location.href = '<?php echo APP_URL; ?>/create.php?order_id=' + data.order.order_id;
            },
            prefill: {
              name: payload.buyer_name,
              email: payload.buyer_email,
              contact: payload.buyer_phone
            },
            theme: { color: '#eac34a' }
          };

          const rzp = new Razorpay(options);
          rzp.open();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Server error: ' + err.message);
      } finally {
        btn.innerText = 'Proceed to Pay & Personalize →';
        btn.disabled = false;
      }
    }

    async function simulateDevPayment() {
      const rawPhone = document.getElementById('buyerPhone').value.trim() || '9876543210';
      const cleanPhone = rawPhone.replace(/[^0-9]/g, '');
      const fullPhone = '+91' + (cleanPhone.length === 10 ? cleanPhone : '9876543210');

      const payload = {
        buyer_name: document.getElementById('buyerName').value || 'Test Buyer',
        buyer_phone: fullPhone,
        buyer_email: document.getElementById('buyerEmail').value || 'test@example.com',
        template_id: currentTemplateId || 'anniversary_reveal'
      };

      const res = await fetch('<?php echo APP_URL; ?>/api/create_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data.success) {
        await fetch('<?php echo APP_URL; ?>/api/webhook_razorpay.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            order_id: data.order.order_id,
            razorpay_payment_id: 'sim_pay_' + Date.now(),
            status: 'paid'
          })
        });

        window.location.href = '<?php echo APP_URL; ?>/create.php?order_id=' + data.order.order_id;
      }
    }

    function toggleMobileNavMenu() {
      const menu = document.getElementById('mobileDrawerMenu');
      const icon = document.getElementById('hamburgerIcon');
      if (menu) {
        menu.classList.toggle('hidden');
        const isOpen = !menu.classList.contains('hidden');
        if (icon) {
          icon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
          if (typeof lucide === 'object') lucide.createIcons();
        }
      }
    }
  </script>
</body>
</html>
