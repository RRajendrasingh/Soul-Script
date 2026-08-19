<?php
/**
 * Component 1: Header & Hero Section (Festive Light Theme) - php-app mirror
 */
?>
<!-- Top Navigation Header -->
<header class="fixed top-0 left-0 right-0 w-full z-40 bg-[#fcf6f0]/90 backdrop-blur-md border-b border-[#e8d5c4] shadow-sm">
  <div class="max-w-5xl mx-auto px-4 h-14 sm:h-16 flex items-center justify-between gap-3">
    <!-- Brand / Logo -->
    <a href="<?= APP_URL ?>" class="flex items-center gap-2 text-sm font-bold text-[#4a232f] hover:text-[#e5534b] transition-colors shrink-0">
      <span class="w-8 h-8 rounded-full bg-[#e5534b]/10 text-[#e5534b] flex items-center justify-center font-serif text-lg">🪔</span>
      <span class="font-serif tracking-tight text-base sm:text-lg">With Love, <?= htmlspecialchars($partnerName) ?></span>
    </a>

    <!-- Header Controls -->
    <div class="flex items-center gap-2.5">
      <!-- Music Pill -->
      <button type="button" id="audioPlayBtnMobile" onclick="toggleAudioPlay()" class="px-3 py-1.5 rounded-full bg-gradient-to-r from-[#e5534b] to-[#c93d3d] text-white font-bold text-xs flex items-center gap-1.5 shadow-sm hover:shadow-md transition-all cursor-pointer">
        <span class="text-xs spin-vinyl inline-block">🎵</span>
        <span id="musicBtnLabel" class="hidden sm:inline">Music</span>
      </button>

      <!-- Share Button -->
      <button type="button" onclick="shareFestivePage()" class="p-2 sm:px-3 sm:py-1.5 rounded-full bg-[#f4e5d8] hover:bg-[#e8d5c4] text-[#4a232f] font-semibold text-xs flex items-center gap-1 transition-all cursor-pointer" title="Share Page">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 100-2.684 3 3 0 000 2.684m0 9.316a3 3 0 100-2.684 3 3 0 000 2.684"/></svg>
        <span class="hidden sm:inline">Share</span>
      </button>
    </div>
  </div>
</header>

<!-- Hero Section -->
<section class="relative pt-24 pb-10 px-4 text-center z-10 w-full max-w-4xl mx-auto">
  <div class="space-y-4">
    <!-- Top Festive Pill Badge -->
    <div class="inline-flex items-center gap-2 bg-[#e5534b]/10 border border-[#e5534b]/30 px-4 py-1.5 rounded-full text-xs font-bold text-[#e5534b] uppercase tracking-wider shadow-sm">
      <span>👑 <?= htmlspecialchars($taglineQuote ?: "World's Best Sister") ?></span>
    </div>

    <!-- Main Title -->
    <h1 class="text-3xl sm:text-5xl font-black font-serif text-[#4a232f] leading-tight tracking-tight">
      Happy Raksha Bandhan <br>
      <span class="text-[#e5534b]"><?= htmlspecialchars($partnerName) ?></span>! 💖
    </h1>

    <p class="text-sm sm:text-base text-[#6b4c57] max-w-xl mx-auto leading-relaxed">
      A celebration of eternal love, timeless childhood memories, and promises created with lots of love by <strong class="text-[#4a232f]"><?= htmlspecialchars($buyerName) ?></strong>.
    </p>

    <!-- Double Golden Ring Avatar Frame -->
    <div class="relative py-4 inline-block">
      <div class="relative w-44 h-44 sm:w-52 sm:h-52 mx-auto rounded-full p-2 bg-gradient-to-tr from-[#d4af37] via-[#f7e6a6] to-[#b89343] shadow-[0_10px_30px_rgba(229,83,75,0.25)] group">
        <div class="w-full h-full rounded-full overflow-hidden border-4 border-white shadow-inner bg-[#f9ede3]">
          <img src="<?= htmlspecialchars($cleanReceiverPhoto) ?>" alt="<?= htmlspecialchars($partnerName) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </div>

        <!-- Dynamic Live Badges on Avatar -->
        <div id="heroTilakBadge" class="<?= (!empty($_COOKIE['rakhi_tied_'.$pageId]) || !empty($rakhiVoucherStatus['unlocked'])) ? '' : 'hidden' ?> absolute top-2 right-2 bg-[#c93d3d] text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-md animate-pulse">
          ✨ Tilak Applied
        </div>
        <div id="heroRakhiBadge" class="<?= (!empty($_COOKIE['rakhi_tied_'.$pageId]) || !empty($rakhiVoucherStatus['unlocked'])) ? '' : 'hidden' ?> absolute bottom-2 left-2 bg-[#d4af37] text-[#241a00] text-[10px] font-black px-2.5 py-1 rounded-full shadow-md">
          🎗️ Rakhi Tied
        </div>
      </div>
    </div>

    <!-- Primary Action CTA Button: Tap to Tie Rakhi -->
    <div class="pt-2">
      <button type="button" onclick="openFestiveRakhiModal()" class="px-8 py-4 bg-gradient-to-r from-[#e5534b] via-[#d63a30] to-[#c93d3d] text-white font-extrabold text-sm sm:text-base uppercase tracking-wider rounded-full shadow-[0_10px_30px_rgba(229,83,75,0.4)] hover:shadow-[0_15px_35px_rgba(229,83,75,0.6)] hover:-translate-y-0.5 active:scale-95 transition-all cursor-pointer inline-flex items-center gap-3">
        <span class="text-xl">🪔</span>
        <span>Tap to Tie Virtual Rakhi</span>
        <span class="text-lg">➔</span>
      </button>
    </div>
  </div>
</section>
