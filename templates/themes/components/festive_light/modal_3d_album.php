<?php
/**
 * Component: 3D Interactive Virtual Memory Album Modal Dialog (Festive Light Theme)
 */
?>

<div id="festive3DAlbumModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-0 sm:p-6 bg-black/85 backdrop-blur-md transition-opacity duration-300">
  <div class="relative w-full h-full sm:h-auto sm:max-w-4xl sm:max-h-[92vh] bg-[#fffdfa] border-0 sm:border-2 border-[#d4af37]/60 rounded-none sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col">
    
    <!-- Modal Header -->
    <div class="px-5 py-4 bg-[#fcf6f0] border-b border-[#e8d5c4] flex items-center justify-between shrink-0 shadow-sm z-10">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-full bg-[#d4af37]/20 text-[#934b00] flex items-center justify-center text-lg border border-[#d4af37]/40 shadow-inner">
          📖
        </div>
        <div>
          <h3 class="text-sm sm:text-base font-bold font-serif text-[#4a232f]">3D Childhood Memory Album</h3>
          <span class="text-[10px] font-bold text-[#e5534b] uppercase tracking-wider block">Interactive Virtual Photobook</span>
        </div>
      </div>

      <button type="button" onclick="closeStitchVirtualAlbumModal()" class="w-9 h-9 rounded-full bg-[#f4e5d8] text-[#4a232f] hover:bg-[#e5534b] hover:text-white font-bold text-lg flex items-center justify-center transition-colors cursor-pointer shadow-sm" title="Close Album">
        ✕
      </button>
    </div>

    <!-- Album Interactive Flipbook Stage -->
    <div class="flex-1 bg-[#151012] p-3 sm:p-8 flex items-center justify-center relative overflow-y-auto">
      <div id="festiveFlipbookStage" class="w-full max-w-2xl bg-white rounded-2xl sm:rounded-3xl shadow-2xl p-4 sm:p-6 flex flex-col justify-between relative border-2 sm:border-4 border-[#d4af37]/50 space-y-4 my-auto">
        
        <!-- Active Page Content -->
        <div id="festiveFlipbookPageContent" class="flex-1 flex flex-col items-center justify-center text-center space-y-3">
          <?php 
          $firstImg = !empty($galleryMedia[0]) ? (is_array($galleryMedia[0]) ? resolveMediaUrl($galleryMedia[0]['file_path'] ?? '') : resolveMediaUrl($galleryMedia[0])) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCNOrtcvsfdswgVbFaQS_7cryDev0cl9Ms28q69BN-fYTJtrIKW-duaIiRqkhZeNgFLwPjArYOA9IyJH-20R3E6CiW5eJ8LySG0-5YC6ZfBXDtGtlno0imtOhMn_cAtUnjBeijQjWzu8JCP1KSQITYmi06m4mOy_4_smVTQKVSwOV6X2qG7yH-00YtfrKS9kfmFSj6eivLno1QSmAuXM3FoKoACiX-4ImvGIjO5kupb_GNkALFv-_Bx';
          ?>
          <div class="w-full aspect-[4/3] rounded-xl sm:rounded-2xl overflow-hidden bg-[#fcf6f0] border-2 border-[#e8d5c4] shadow-inner relative group">
            <img id="festiveFlipbookActiveImg" src="<?= htmlspecialchars($firstImg) ?>" alt="Album Page" class="w-full h-full object-cover">
          </div>
          <p id="festiveFlipbookCaption" class="text-xs sm:text-base font-serif italic text-[#4a232f] font-bold px-2">
            "Unforgettable childhood memories with <?= htmlspecialchars($partnerName) ?>"
          </p>
        </div>

        <!-- Flipbook Page Navigation Bar -->
        <div class="pt-3 border-t border-[#e8d5c4] flex items-center justify-between text-xs font-bold text-[#4a232f]">
          <button type="button" onclick="navigateFlipbookPage(-1)" class="px-4 py-2.5 bg-[#fcf6f0] border border-[#e8d5c4] rounded-full hover:bg-[#e5534b] hover:text-white hover:border-[#e5534b] transition-all cursor-pointer shadow-sm active:scale-95">
            ← Previous Page
          </button>
          
          <span id="flipbookPageIndicator" class="text-xs font-bold font-mono text-[#e5534b] bg-red-50 px-3 py-1 rounded-full border border-red-200">Page 1 of <?= max(1, count($galleryMedia)) ?></span>
          
          <button type="button" onclick="navigateFlipbookPage(1)" class="px-4 py-2.5 bg-[#fcf6f0] border border-[#e8d5c4] rounded-full hover:bg-[#e5534b] hover:text-white hover:border-[#e5534b] transition-all cursor-pointer shadow-sm active:scale-95">
            Next Page →
          </button>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
let currentAlbumPageIndex = 0;
const albumMediaData = <?php 
$slides = [];
if (!empty($galleryMedia)) {
  foreach ($galleryMedia as $m) {
    $slides[] = [
      'url' => is_array($m) ? resolveMediaUrl($m['file_path'] ?? '') : resolveMediaUrl($m),
      'caption' => is_array($m) ? ($m['caption'] ?? 'Unforgettable childhood memory') : 'Unforgettable childhood memory'
    ];
  }
} else {
  $slides = [
    ['url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCNOrtcvsfdswgVbFaQS_7cryDev0cl9Ms28q69BN-fYTJtrIKW-duaIiRqkhZeNgFLwPjArYOA9IyJH-20R3E6CiW5eJ8LySG0-5YC6ZfBXDtGtlno0imtOhMn_cAtUnjBeijQjWzu8JCP1KSQITYmi06m4mOy_4_smVTQKVSwOV6X2qG7yH-00YtfrKS9kfmFSj6eivLno1QSmAuXM3FoKoACiX-4ImvGIjO5kupb_GNkALFv-_Bx', 'caption' => 'Giggles & Mud Pies'],
    ['url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB1W4cvzWixPF82Kr200o6tTq57eeSTvqsYQwGo0xV84XECz2DX2VGlSMzOoqobrtS2N35WIxSLvDIBZwOV7sJDy-Lj8FDwsitpCd6uUDSgGoLBMhV4Xgx385JZ2E-byxe1Y6XGX2iOoXPNhBWSlyAHrCEGGdYp21_z3_vxYjHEeHmw-4uOxpp-S1iY3JtrN9flbmXlVvdsY02hdwZDrsH9jfSlFW5ctcNzKsczrByXWHE3LCX4n8sv', 'caption' => 'Always By My Side'],
    ['url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA-rxUuandJ-yKh0-NBSCcMQevrXjQdVNPHJ3Tvisal85dWtEIZj2TGWKUJjZW53DNMOzz2Eak4SDdFwZklDkd5zRAsNpqtUCp2CD5vmTWw1jofN7korPpJKNuwVTRkZhWcDed_fQn5gJRwQ4NHKvGvYPOo6TSva33ZtWkQpICoBlYl7Q7_IJlR5PALgpVJPlXgXnUPp7BqhZubH7hNphTv3gsgTmu8TBC4Ewr1teO3iftmu2b-jz1x', 'caption' => 'Little Hands, Big Bond']
  ];
}
echo json_encode($slides); 
?>;

function navigateFlipbookPage(dir) {
  if (!albumMediaData || albumMediaData.length === 0) return;
  currentAlbumPageIndex += dir;
  if (currentAlbumPageIndex < 0) currentAlbumPageIndex = albumMediaData.length - 1;
  if (currentAlbumPageIndex >= albumMediaData.length) currentAlbumPageIndex = 0;

  const img = document.getElementById('festiveFlipbookActiveImg');
  const caption = document.getElementById('festiveFlipbookCaption');
  const indicator = document.getElementById('flipbookPageIndicator');

  if (img) img.src = albumMediaData[currentAlbumPageIndex].url;
  if (caption) caption.innerText = `"${albumMediaData[currentAlbumPageIndex].caption}"`;
  if (indicator) indicator.innerText = `Page ${currentAlbumPageIndex + 1} of ${albumMediaData.length}`;
}

function openStitchVirtualAlbumModal() {
  const container = document.getElementById('festive3DAlbumModalContainer');
  const musicBox = document.getElementById('desktopMusicBox');
  if (container) {
    if (container.parentElement !== document.body) {
      document.body.appendChild(container);
    }
    container.classList.remove('hidden');
    container.style.display = 'flex';
  }
  if (musicBox) {
    musicBox.style.display = 'none';
  }
  document.body.style.overflow = 'hidden';
}
function openFestiveVirtualAlbumModal() {
  openStitchVirtualAlbumModal();
}

function closeStitchVirtualAlbumModal() {
  const container = document.getElementById('festive3DAlbumModalContainer');
  const musicBox = document.getElementById('desktopMusicBox');
  if (container) {
    container.classList.add('hidden');
    container.style.display = 'none';
  }
  if (musicBox) {
    musicBox.style.display = 'flex';
  }
  document.body.style.overflow = '';
}
function closeFestiveVirtualAlbumModal() {
  closeStitchVirtualAlbumModal();
}

// Auto-portal to body on document ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    const c = document.getElementById('festive3DAlbumModalContainer');
    if (c && c.parentElement !== document.body) document.body.appendChild(c);
  });
} else {
  const c = document.getElementById('festive3DAlbumModalContainer');
  if (c && c.parentElement !== document.body) document.body.appendChild(c);
}
</script>
