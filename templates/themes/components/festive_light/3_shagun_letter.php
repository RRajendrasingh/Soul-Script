<?php
/**
 * Component 3: Royal Shagun Letter & Envelope (Festive Light Theme)
 */
?>
<section class="max-w-4xl mx-auto px-4 py-8 relative z-10">
  <div class="bg-gradient-to-br from-[#fffdfa] via-[#fcf6f0] to-[#f9ede3] border-2 border-[#d4af37]/50 rounded-3xl p-6 sm:p-10 shadow-[0_15px_40px_rgba(212,175,55,0.12)] relative overflow-hidden space-y-6">
    
    <!-- Top Royal Stamp Badge -->
    <div class="flex items-center justify-between border-b border-[#e8d5c4] pb-4">
      <div class="flex items-center gap-2">
        <span class="w-10 h-10 rounded-full bg-[#e5534b]/10 border border-[#e5534b]/30 text-[#e5534b] flex items-center justify-center font-serif text-xl">✉️</span>
        <div>
          <span class="text-[10px] font-bold text-[#e5534b] uppercase tracking-wider block">SHAGUN LETTER</span>
          <h3 class="text-base sm:text-lg font-bold font-serif text-[#4a232f]">A Note For My Dearest Sister</h3>
        </div>
      </div>
      <span class="px-3 py-1 bg-[#d4af37]/20 border border-[#d4af37] text-[#8c6b1b] text-[10px] font-black uppercase tracking-widest rounded-full">
        SEALED WITH LOVE 💖
      </span>
    </div>

    <!-- Letter Body Paragraph -->
    <div class="space-y-4 font-serif text-[#4a232f] leading-relaxed text-sm sm:text-base bg-white/70 rounded-2xl p-6 border border-[#e8d5c4] shadow-inner">
      <p class="font-bold text-base sm:text-lg text-[#e5534b]">
        Dear <?= htmlspecialchars($partnerName) ?>,
      </p>
      
      <p class="whitespace-pre-line text-[#5c3844] italic font-sans text-sm sm:text-base leading-relaxed">
        <?= htmlspecialchars($loveNoteText ?: "No matter how far we are or how much we tease each other, you will always be my strength and greatest blessing. Wishing you a very Happy Raksha Bandhan full of laughter and joy!") ?>
      </p>

      <div class="pt-4 text-right">
        <span class="text-xs text-[#8c6b1b] font-sans font-bold block">With Endless Love & Protection,</span>
        <span class="text-base sm:text-lg font-bold text-[#4a232f] font-serif block">
          <?= htmlspecialchars($buyerName) ?> ❤️
        </span>
      </div>
    </div>

  </div>
</section>
