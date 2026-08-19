<?php
/**
 * Component 5: Childhood Scrapbook Photo Mosaic & 3D Album Launcher (Festive Light Theme)
 */

$galleryMedia = !empty($media) && is_array($media) ? $media : [];
?>

<section class="max-w-4xl mx-auto px-4 py-8 relative z-10 space-y-6">
  <div class="text-center space-y-2">
    <div class="inline-flex items-center gap-1.5 bg-[#e5534b]/10 border border-[#e5534b]/20 px-3 py-1 rounded-full text-[11px] font-bold text-[#e5534b] uppercase tracking-wider">
      <span>📸 MEMORY MOSAIC</span>
    </div>
    <h2 class="text-2xl sm:text-3xl font-bold font-serif text-[#4a232f]">Our Childhood Scrapbook 📖</h2>
    <p class="text-xs sm:text-sm text-[#7a5c68]">Unforgettable moments filled with smiles, fights, and pure love.</p>
  </div>

  <?php if (!empty($galleryMedia)): ?>
    <!-- 2-Column Responsive Masonry Mosaic -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
      <?php foreach ($galleryMedia as $mIdx => $mObj): 
        $imgSrc = is_array($mObj) ? resolveMediaUrl($mObj['file_path'] ?? '') : resolveMediaUrl($mObj);
        $caption = is_array($mObj) ? ($mObj['caption'] ?? '') : '';
        if (!$imgSrc) continue;
      ?>
        <div class="bg-white border border-[#e8d5c4] rounded-2xl p-2 shadow-sm hover:shadow-md hover:border-[#e5534b] transition-all group overflow-hidden">
          <div class="aspect-square rounded-xl overflow-hidden bg-[#fcf6f0]">
            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Memory <?= $mIdx+1 ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 cursor-pointer" onclick="openPhotoLightbox('<?= htmlspecialchars($imgSrc) ?>')">
          </div>
          <?php if (!empty($caption)): ?>
            <p class="text-[11px] font-serif italic text-[#4a232f] text-center pt-2 px-1 line-clamp-1">
              "<?= htmlspecialchars($caption) ?>"
            </p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <!-- Default Placeholder Mosaic -->
    <div class="bg-white border border-[#e8d5c4] rounded-2xl p-8 text-center text-xs text-[#7a5c68]">
      📸 Photo memories will appear here once uploaded by brother!
    </div>
  <?php endif; ?>

  <!-- 3D Virtual Album Launcher Banner -->
  <div class="bg-gradient-to-r from-[#4a232f] to-[#2b131b] rounded-2xl p-6 text-white text-center sm:text-left sm:flex items-center justify-between gap-4 shadow-lg">
    <div class="space-y-1 mb-4 sm:mb-0">
      <span class="px-2.5 py-0.5 bg-[#d4af37]/30 border border-[#d4af37] text-[#ffd700] text-[10px] font-black uppercase rounded-full tracking-wider">
        ✨ 3D INTERACTIVE FLIPBOOK
      </span>
      <h3 class="text-lg sm:text-xl font-bold font-serif text-[#fceabb]">Open 3D Virtual Memory Album 📖</h3>
      <p class="text-xs text-[#d5c7bc]">Turn digital pages like a real luxury memory book album!</p>
    </div>

    <button type="button" onclick="openFestiveVirtualAlbumModal()" class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-[#d4af37] via-[#f7e6a6] to-[#b89343] text-[#241a00] font-extrabold text-xs sm:text-sm uppercase tracking-wider rounded-full shadow-md hover:shadow-lg transition-all cursor-pointer inline-flex items-center justify-center gap-2">
      <span>📖 Open 3D Album</span>
    </button>
  </div>
</section>
