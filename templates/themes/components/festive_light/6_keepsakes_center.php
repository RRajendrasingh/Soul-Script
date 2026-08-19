<?php
/**
 * Component 6: Printable Memory Keepsakes Center (Festive Light Theme)
 * 300 DPI Wall Poster & 7-Page PDF Photobook Download Center
 */
?>
<section class="max-w-4xl mx-auto px-4 py-8 relative z-10 space-y-6">
  <div class="text-center space-y-2">
    <div class="inline-flex items-center gap-1.5 bg-[#d4af37]/20 border border-[#d4af37] px-3 py-1 rounded-full text-[11px] font-bold text-[#8c6b1b] uppercase tracking-wider">
      <span>👑 PHYSICAL &bull; PRINTABLE KEEPSAKES</span>
    </div>
    <h2 class="text-2xl sm:text-3xl font-bold font-serif text-[#4a232f]">Printable Memory Treasures 🖼️📖</h2>
    <p class="text-xs sm:text-sm text-[#7a5c68]">Turn digital memories into 300 DPI high-definition physical treasures to print or frame!</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <!-- Keepsake Card 1: Wall Collage Poster -->
    <div class="bg-white border-2 border-[#d4af37]/60 rounded-3xl p-6 shadow-md flex flex-col justify-between space-y-4 hover:border-[#d4af37] transition-all">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-3 py-1 bg-[#d4af37]/20 border border-[#d4af37] rounded-full text-[10px] font-black uppercase tracking-wider text-[#8c6b1b]">FRAME READY (A4/A3)</span>
          <span class="text-2xl">🖼️</span>
        </div>
        <h3 class="text-xl font-bold font-serif text-[#4a232f]">Wall Collage Poster</h3>
        <p class="text-xs text-[#6b4c57] leading-relaxed">
          A luxury 300 DPI wall-frame keepsake featuring <?= htmlspecialchars($partnerName) ?>'s portrait in a 24K gold locket, surrounded by an uncropped memory mosaic.
        </p>
      </div>

      <button type="button" onclick="downloadWallKeepsakePoster()" class="w-full py-3.5 bg-gradient-to-r from-[#d4af37] via-[#f7e6a6] to-[#b89343] text-[#241a00] font-extrabold text-xs uppercase tracking-wider rounded-full shadow-md hover:shadow-lg transition-all cursor-pointer inline-flex items-center justify-center gap-2">
        <span>🖼️ Download Wall Poster (300 DPI)</span>
      </button>
    </div>

    <!-- Keepsake Card 2: Multi-Page Photobook PDF -->
    <div class="bg-white border-2 border-[#10b981]/60 rounded-3xl p-6 shadow-md flex flex-col justify-between space-y-4 hover:border-[#10b981] transition-all">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-3 py-1 bg-[#10b981]/20 border border-[#10b981] rounded-full text-[10px] font-black uppercase tracking-wider text-[#047857]">MULTI-PAGE ALBUM (PDF)</span>
          <span class="text-2xl">📖</span>
        </div>
        <h3 class="text-xl font-bold font-serif text-[#4a232f]">Sibling Keepsake Book</h3>
        <p class="text-xs text-[#6b4c57] leading-relaxed">
          A luxury 7-page printable storybook album with Royal Cover, Shahi Certificate, vows, and a dynamic QR code to relive this website anytime!
        </p>
      </div>

      <button type="button" onclick="downloadSiblingPhotobookPDF()" class="w-full py-3.5 bg-gradient-to-r from-[#10b981] via-[#34d399] to-[#059669] text-white font-extrabold text-xs uppercase tracking-wider rounded-full shadow-md hover:shadow-lg transition-all cursor-pointer inline-flex items-center justify-center gap-2">
        <span>📖 Download Keepsake Book (PDF)</span>
      </button>
    </div>
  </div>
</section>
