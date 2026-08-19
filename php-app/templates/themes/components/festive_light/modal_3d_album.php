<?php
/**
 * Component: 3D Interactive Virtual Memory Album Modal Dialog (Festive Light Theme) - php-app mirror
 */
?>

<div id="festive3DAlbumModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/75 backdrop-blur-md transition-opacity duration-300">
  <div class="relative w-full max-w-4xl bg-[#fffdfa] border-2 border-[#d4af37]/60 rounded-3xl shadow-2xl overflow-hidden flex flex-col h-[85vh] max-h-[750px]">
    
    <!-- Modal Header -->
    <div class="px-6 py-4 bg-[#fcf6f0] border-b border-[#e8d5c4] flex items-center justify-between shrink-0">
      <div class="flex items-center gap-2">
        <span class="text-2xl">📖</span>
        <div>
          <h3 class="text-base font-bold font-serif text-[#4a232f]">3D Childhood Memory Album</h3>
          <span class="text-[10px] font-bold text-[#e5534b] uppercase tracking-wider block">Interactive Virtual Photobook</span>
        </div>
      </div>

      <button type="button" onclick="closeFestiveVirtualAlbumModal()" class="w-8 h-8 rounded-full bg-[#f4e5d8] text-[#4a232f] hover:bg-[#e5534b] hover:text-white font-bold text-base flex items-center justify-center transition-colors cursor-pointer" title="Close Album">
        ✕
      </button>
    </div>

    <!-- Album Interactive Flipbook Stage -->
    <div class="flex-1 bg-[#151012] p-4 sm:p-8 flex items-center justify-center relative overflow-hidden">
      <div id="festiveFlipbookStage" class="w-full max-w-2xl aspect-[4/3] bg-white rounded-2xl shadow-2xl p-4 sm:p-6 flex flex-col justify-between relative border-4 border-[#d4af37]/40">
        
        <!-- Active Page Content -->
        <div id="festiveFlipbookPageContent" class="h-full flex flex-col items-center justify-center text-center space-y-3">
          <?php if (!empty($galleryMedia[0])): 
            $firstImg = is_array($galleryMedia[0]) ? resolveMediaUrl($galleryMedia[0]['file_path'] ?? '') : resolveMediaUrl($galleryMedia[0]);
          ?>
            <div class="w-full h-4/5 rounded-xl overflow-hidden bg-[#fcf6f0] border border-[#e8d5c4]">
              <img id="festiveFlipbookActiveImg" src="<?= htmlspecialchars($firstImg) ?>" alt="Album Page" class="w-full h-full object-cover">
            </div>
            <p id="festiveFlipbookCaption" class="text-xs sm:text-sm font-serif italic text-[#4a232f]">
              "Unforgettable childhood memories with <?= htmlspecialchars($partnerName) ?>"
            </p>
          <?php else: ?>
            <div class="text-center py-12 text-gray-500 text-xs">
              📖 Upload photos in dashboard to fill this 3D Memory Album!
            </div>
          <?php endif; ?>
        </div>

        <!-- Flipbook Page Navigation Bar -->
        <div class="pt-3 border-t border-[#e8d5c4] flex items-center justify-between text-xs font-bold text-[#4a232f]">
          <button type="button" onclick="navigateFlipbookPage(-1)" class="px-4 py-2 bg-[#fcf6f0] border border-[#e8d5c4] rounded-full hover:bg-[#e5534b] hover:text-white transition-all cursor-pointer">
            ← Previous Page
          </button>
          
          <span id="flipbookPageIndicator" class="text-[11px] font-mono text-[#e5534b]">Page 1 of <?= max(1, count($galleryMedia)) ?></span>
          
          <button type="button" onclick="navigateFlipbookPage(1)" class="px-4 py-2 bg-[#fcf6f0] border border-[#e8d5c4] rounded-full hover:bg-[#e5534b] hover:text-white transition-all cursor-pointer">
            Next Page →
          </button>
        </div>

      </div>
    </div>

  </div>
</div>
