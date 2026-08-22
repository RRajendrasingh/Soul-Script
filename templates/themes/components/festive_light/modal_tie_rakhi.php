<?php
/**
 * Component: 100% Google Stitch Design Matching Full-Stage "Virtual Rakhi Ceremony" Modal
 */
?>

<div id="festiveRakhiModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-0 sm:p-6 bg-black/85 backdrop-blur-md transition-opacity duration-300">
  <div id="festiveRakhiModal" class="relative w-full h-full sm:h-auto sm:max-w-3xl sm:max-h-[94vh] bg-[#fcf6f0] border-0 sm:border-2 border-[#d4af37]/60 rounded-none sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col">
    
    <!-- Modal Header Bar -->
    <div class="px-5 py-3.5 bg-[#f4e5d8]/90 border-b border-[#e8d5c4] flex items-center justify-between shrink-0 shadow-sm z-10">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-full bg-[#d32f2f] text-white flex items-center justify-center shadow-md text-base font-bold">
          🪔
        </div>
        <div>
          <h3 class="text-sm sm:text-base font-bold font-serif text-[#4a232f]">Virtual Rakhi Ceremony</h3>
          <span id="festiveModalStepBadge" class="text-[10px] font-bold text-[#e5534b] uppercase tracking-wider block">Step 1 of 5</span>
        </div>
      </div>

      <!-- Close Button (Cross ✕) -->
      <button type="button" onclick="closeFestiveRakhiModal()" class="w-8 h-8 rounded-full bg-white text-[#4a232f] hover:bg-[#e5534b] hover:text-white font-bold text-base flex items-center justify-center transition-colors shadow-sm cursor-pointer" title="Close Modal">
        ✕
      </button>
    </div>

    <!-- Modal Content Stage (Steps 1 to 5 Container) -->
    <div class="p-4 sm:p-6 overflow-y-auto flex-1 bg-[#fcf6f0] relative">

      <!-- STEP 1: PREPARE SACRED THALI & SELECT RAKHI -->
      <div id="rakhiStep1" class="space-y-5 max-w-xl mx-auto text-center py-2">
        <?php 
        $heroPhoto = !empty($receiver_photo) ? resolveMediaUrl($receiver_photo) : (!empty($galleryMedia[0]) ? (is_array($galleryMedia[0]) ? resolveMediaUrl($galleryMedia[0]['file_path'] ?? '') : resolveMediaUrl($galleryMedia[0])) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCZfsICxK34oixmN1AZRizpBM2bZC5BAB_XYhQLhxKaZRKgNxEv8X9v3Z4lzEedQVni4JuXg6LECezawWUPThbfyUKDAnCX14tBlz_SHV5Z0nHTlrYpNX81aS2JbA1-fREPTFZBGfA4Oin9IzGHb5PZxUinsPuL6pU81_ZnpEIrbooze4l1aomWnjr8FWAmwYUcQR92cij0amxmT3sNwf3Uq4XO2ot9yJ_JaQvk6cQiDvzzRP2Mvcj0');
        ?>
        <div class="w-40 h-40 sm:w-48 sm:h-48 mx-auto rounded-full bg-white border-4 border-[#d4af37]/60 shadow-2xl relative overflow-hidden flex items-center justify-center p-1">
          <img src="<?= htmlspecialchars($heroPhoto) ?>" alt="Sibling Memory" class="w-full h-full object-cover rounded-full">
        </div>

        <div class="space-y-1">
          <h4 class="text-2xl sm:text-3xl font-bold font-serif text-[#4a232f]">Prepare Sacred Thali</h4>
          <p class="text-xs sm:text-sm text-[#7a5c68]">Select your chosen Rakhi thread to perform the virtual ceremony.</p>
        </div>

        <!-- 3 Rakhi Options Grid -->
        <div class="grid grid-cols-3 gap-3 pt-2">
          <button type="button" onclick="selectRakhiOption(1, this)" class="rakhi-opt-btn p-3 rounded-2xl border-2 border-[#e5534b] bg-white flex flex-col items-center gap-1.5 cursor-pointer shadow-sm hover:shadow-md transition-all active:scale-95">
            <span class="text-2xl sm:text-3xl">🏵️</span>
            <span class="text-xs font-bold text-[#4a232f]">Royal Kundan</span>
          </button>
          <button type="button" onclick="selectRakhiOption(2, this)" class="rakhi-opt-btn p-3 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-1.5 cursor-pointer shadow-sm hover:shadow-md transition-all active:scale-95">
            <span class="text-2xl sm:text-3xl">📿</span>
            <span class="text-xs font-bold text-[#4a232f]">Rudraksha</span>
          </button>
          <button type="button" onclick="selectRakhiOption(3, this)" class="rakhi-opt-btn p-3 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-1.5 cursor-pointer shadow-sm hover:shadow-md transition-all active:scale-95">
            <span class="text-2xl sm:text-3xl">✨</span>
            <span class="text-xs font-bold text-[#4a232f]">Silver Thread</span>
          </button>
        </div>
      </div>

      <!-- STEP 2: STITCH CEREMONY READY TO TIE (BARE WRIST + DRAG TARGET) -->
      <div id="rakhiStep2" class="hidden flex-col items-center space-y-4 max-w-xl mx-auto py-1">
        <div class="text-center space-y-0.5">
          <h4 class="text-xl sm:text-2xl font-bold font-serif text-[#4a232f]">Ceremony: Ready to Tie</h4>
          <p class="text-xs text-[#7a5c68]">Select a Rakhi and tie it to celebrate</p>
        </div>

        <!-- Bare Wrist Interactive Stage -->
        <div id="rakhiInteractionArea" class="relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-2 sm:border-4 border-[#d4af37]/40 bg-white touch-none">
          <!-- 100% Bare Wrist Image (Without any Rakhi) -->
          <img id="wristBgImage" src="https://lh3.googleusercontent.com/aida/AP1WRLusJMjZjDNpL3tdMMUcxKwtBHZB1m0ulhgHVwD_ZG1RiPZHPkFWvQoviBGySth2IRJP7U8_ZRh173Q5rGPCoj-rQdxS98HK8HPLgOlTLKwzQAhDI0_xc9qhBja4ry7qP8YfM4XASkHMHYXXaPwtRdt4kXdoK3LZq_y_0LhAk3SPlD-kg9VH2_C68AFubCsBZD0A7RPNdjgemarba2C-3vuMw7lto7kP_MhlKWD5TQgZah-7MmGkrvn-oNw" alt="Brother's Bare Wrist" class="w-full h-full object-cover select-none pointer-events-none">
          
          <!-- Dashed Target Zone Overlay -->
          <div id="rakhiTargetZone" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-20 border-2 border-dashed border-white/90 bg-black/20 rounded-2xl flex flex-col items-center justify-center backdrop-blur-xs transition-all pointer-events-none">
            <span class="text-[11px] font-bold text-white uppercase tracking-widest drop-shadow-md">Drag Rakhi Here</span>
          </div>

          <!-- Draggable Rakhi Thread -->
          <div id="draggableRakhi" class="absolute bottom-3 left-1/2 -translate-x-1/2 w-28 h-28 cursor-grab active:cursor-grabbing z-20 touch-none" style="touch-action: none;">
            <img id="draggableRakhiImg" src="https://lh3.googleusercontent.com/aida/AP1WRLvbnd-5weRLOAwdfA-O5tILrD1hX7niryxFWKAlZF9ArzpuKowuWj2utypXaVQ4dWHgV1LFWoevzqQlZbJe5YKZaaqOuordVByiZMf50Aq3hu55KzCryJbWIfxjOQwKofotmSg9UYTdNB2vv7hlIweT2fJuGE6piTSeX1vXMHny0MU6CGc1obbTH5Fixj6WZXIhuDaxfT3IhM5zHSyPawlN4WX5Mgbu8HgW8GdJj2LzLztHDIqZRRuleXA" alt="Rakhi" class="w-full h-full object-contain filter drop-shadow-xl select-none pointer-events-none">
          </div>
        </div>

        <!-- Bottom Sheet Action Bar -->
        <div class="w-full bg-white rounded-2xl p-3 sm:p-4 border border-[#e8d5c4] shadow-sm flex items-center justify-between gap-3">
          <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-[#fcf6f0] border border-[#e8d5c4] p-1 flex items-center justify-center shrink-0">
              <span id="selectedRakhiIcon" class="text-xl">🏵️</span>
            </div>
            <div class="truncate">
              <span class="text-[10px] font-bold text-[#e5534b] uppercase block tracking-wider">Ready to Tie</span>
              <span id="selectedRakhiName" class="text-xs sm:text-sm font-bold text-[#4a232f] block truncate">Royal Kundan Rakhi</span>
            </div>
          </div>

          <button type="button" onclick="triggerRakhiTiedSuccess()" class="px-5 py-2.5 bg-gradient-to-r from-[#d32f2f] to-[#f57c00] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0 flex items-center gap-1">
            <span>Tie Now 🧵</span>
          </button>
        </div>
      </div>

      <!-- STEP 3: RAKHI TIED SUCCESSFULLY (TIED WRIST + SEE YOUR GIFT) -->
      <div id="rakhiStep3" class="hidden flex-col items-center space-y-4 max-w-xl mx-auto py-1 text-center">
        <div class="space-y-0.5">
          <h4 class="text-2xl sm:text-3xl font-black font-serif text-[#4a232f]">Rakhi Tied Successfully!</h4>
          <p class="text-xs sm:text-sm text-[#7a5c68]">A bond of love, protection, and joy.</p>
        </div>

        <!-- Tied Wrist Image Container -->
        <div class="relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-2 sm:border-4 border-[#d4af37]/60 bg-white">
          <img src="https://lh3.googleusercontent.com/aida/AEtjO1V9fsGQla1yqqkKaOKFChivC6eDTa-cQ8oIlYdIBLFJEKnfgk4jIIloFOR-WWEueSaUggxUCWvAsCoVYN2NSwulJpcT_YMIIchS796mQCVkv643jHSQplN1cAsMlDHDg7-oXqdejXr9-_izXQG_gNVVqpKlMzFQwRTrGa8iLfFX2nJY3yHCcyNNNcEusroXtgSMNkvIwNKATz3RZyBsnU75OreOfCnZ9xvweQ5AYbnStlot4vvdsGL4Dgs" alt="Rakhi Tied Successfully" class="w-full h-full object-cover">
          
          <div class="absolute top-3 right-3 px-3 py-1 bg-[#1f4e27]/90 border border-[#52b76b] text-[#98ecaa] font-bold text-[10px] uppercase rounded-full shadow-lg flex items-center gap-1 backdrop-blur-sm">
            <span>✓</span> <span>Tied with Love</span>
          </div>
        </div>

        <!-- Prominent "SEE YOUR GIFT" Button -->
        <div class="w-full pt-1">
          <button type="button" onclick="navigateFestiveStep(1)" class="w-full py-4 px-8 bg-gradient-to-r from-[#e57800] via-[#f59e0b] to-[#d97706] text-white font-extrabold text-sm sm:text-base uppercase tracking-wider rounded-2xl shadow-xl hover:shadow-2xl hover:scale-[1.02] active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-2">
            <span>SEE YOUR GIFT 🎁</span>
          </button>
        </div>
      </div>

      <!-- STEP 4: ROYAL SHAGUN LIFAFA & SCRATCH CARD -->
      <div id="rakhiStep4" class="hidden flex-col space-y-4 max-w-2xl mx-auto py-1">
        <div class="text-center space-y-0.5">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#ffdcc5] text-[#934b00] text-[10px] font-bold uppercase tracking-wider">
            <span>🧧</span> <span>Royal Shagun Lifafa</span>
          </span>
          <h4 class="text-xl sm:text-2xl font-bold font-serif text-[#4a232f]">Your Shagun Letter &amp; Gift Voucher</h4>
        </div>

        <!-- Two Column Card: Note & Scratch Card -->
        <div class="flex flex-col md:flex-row bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl border border-[#e8d5c4]">
          <!-- Left Column: Shagun Letter Quote -->
          <div class="flex-1 p-5 sm:p-6 bg-white border-b md:border-b-0 md:border-r border-[#e8d5c4] flex flex-col justify-between space-y-4">
            <div>
              <span class="text-3xl text-[#d4af37] block mb-2 opacity-75">❝</span>
              <p class="text-sm sm:text-base text-[#4a232f] italic font-serif leading-relaxed">
                "<?= htmlspecialchars($loveNoteText ?: "mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🧧") ?>"
              </p>
            </div>
            <div class="text-right border-t border-[#f4e5d8] pt-2">
              <span class="text-xs font-bold text-[#934b00] uppercase tracking-wider block">— With lots of love,</span>
              <span class="text-sm font-bold font-serif text-[#4a232f]"><?= htmlspecialchars($buyerName ?: 'Brother') ?></span>
            </div>
          </div>

          <!-- Right Column: Interactive Gold Scratch Card -->
          <div class="flex-1 p-5 sm:p-6 bg-[#fcf6f0] flex flex-col items-center text-center justify-center space-y-3">
            <div class="text-center">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[#e5534b] block">Amazon Gift Voucher</span>
              <h5 class="text-base font-bold font-serif text-[#4a232f]">Scratch to Reveal Code 🪙</h5>
            </div>

            <!-- Scratch Card Area -->
            <div class="relative w-full max-w-[260px] h-32 rounded-2xl overflow-hidden shadow-inner border-2 border-[#d4af37] bg-[#1a0f0a] flex flex-col items-center justify-center select-none">
              <!-- Revealed Content Behind Scratch Canvas -->
              <div class="p-3 text-center space-y-1">
                <span class="text-[10px] font-bold text-[#eac34a] uppercase block">₹500 Amazon Voucher</span>
                <strong class="text-base font-mono font-black text-white block tracking-widest bg-black/50 px-3 py-1 rounded-lg border border-[#eac34a]/40 select-all">
                  <?= htmlspecialchars($voucherCode ?: 'AMZ-RAKHI-2026') ?>
                </strong>
                <span class="text-[9px] text-gray-300 block">Redeem directly on Amazon</span>
              </div>

              <!-- HTML5 Scratch Canvas Layer -->
              <canvas id="modalScratchCanvas" class="absolute inset-0 w-full h-full cursor-pointer z-10 touch-none" style="touch-action: none;"></canvas>
            </div>

            <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($voucherCode ?: 'AMZ-RAKHI-2026') ?>'); alert('Voucher Code Copied! 🎉'); confetti({ particleCount: 80, spread: 60 });" class="px-4 py-1.5 bg-[#e5534b] hover:bg-[#d32f2f] text-white text-xs font-bold uppercase rounded-full shadow-sm transition-all cursor-pointer">
              Copy Voucher Code 📋
            </button>
          </div>
        </div>
      </div>

      <!-- STEP 5: THERE'S MORE TO CELEBRATE (KEEPSAKES & FINISH) -->
      <div id="rakhiStep5" class="hidden space-y-5 text-center max-w-xl mx-auto py-2">
        <div class="w-20 h-20 mx-auto rounded-full bg-[#ffdcc5] text-[#934b00] flex items-center justify-center text-3xl shadow-xl relative">
          🎁
          <div class="absolute -top-1 -right-1 w-7 h-7 bg-[#0061a5] text-white rounded-full flex items-center justify-center text-xs font-bold shadow-md">★</div>
        </div>

        <div class="space-y-1">
          <h4 class="text-2xl sm:text-3xl font-bold font-serif text-[#4a232f]">There's more to celebrate!</h4>
          <p class="text-xs sm:text-sm text-[#7a5c68] max-w-md mx-auto leading-relaxed">
            Download your high-definition Printable Keepsakes or explore the complete childhood memory gallery!
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
          <button type="button" onclick="downloadWallKeepsakePoster()" class="py-3 px-5 bg-gradient-to-r from-[#d4af37] via-[#f7e6a6] to-[#b89343] text-[#241a00] font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow hover:scale-105 transition-all cursor-pointer flex items-center justify-center gap-2">
            <span>🖼️</span> <span>Wall Poster (300 DPI)</span>
          </button>
          <button type="button" onclick="downloadSiblingPhotobookPDF()" class="py-3 px-5 bg-gradient-to-r from-[#10b981] via-[#34d399] to-[#059669] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow hover:scale-105 transition-all cursor-pointer flex items-center justify-center gap-2">
            <span>📖</span> <span>Keepsake Book (PDF)</span>
          </button>
        </div>

        <div class="pt-2">
          <button type="button" onclick="closeFestiveRakhiModal()" class="w-full py-3.5 px-6 bg-[#934b00] hover:bg-[#7a3e00] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-md hover:shadow-lg transition-all cursor-pointer">
            Explore Full Gift Experience ➔
          </button>
        </div>
      </div>

    </div>

    <!-- Modal Footer Controls (Sticky Bottom with Clean Elevation) -->
    <div class="sticky bottom-0 z-20 px-4 sm:px-6 py-3.5 bg-[#f4e5d8] border-t border-[#e8d5c4] flex items-center justify-between shrink-0 shadow-[0_-4px_15px_rgba(0,0,0,0.06)]">
      <button type="button" id="festiveModalBackBtn" onclick="navigateFestiveStep(-1)" class="px-5 py-2.5 bg-white text-[#4a232f] font-bold text-xs rounded-full hover:bg-gray-100 transition-all cursor-pointer invisible shadow-sm">
        ← Back
      </button>

      <button type="button" id="festiveModalNextBtn" onclick="navigateFestiveStep(1)" class="px-8 py-2.5 bg-gradient-to-r from-[#d32f2f] to-[#f57c00] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-md hover:opacity-90 active:scale-95 transition-all cursor-pointer">
        NEXT ➔
      </button>
    </div>

  </div>
</div>

<script>
let currentFestiveStep = 1;
let selectedRakhiOptionId = 1;

const rakhiImages = {
  1: {
    name: 'Royal Kundan Rakhi',
    icon: '🏵️',
    img: 'https://lh3.googleusercontent.com/aida/AP1WRLvbnd-5weRLOAwdfA-O5tILrD1hX7niryxFWKAlZF9ArzpuKowuWj2utypXaVQ4dWHgV1LFWoevzqQlZbJe5YKZaaqOuordVByiZMf50Aq3hu55KzCryJbWIfxjOQwKofotmSg9UYTdNB2vv7hlIweT2fJuGE6piTSeX1vXMHny0MU6CGc1obbTH5Fixj6WZXIhuDaxfT3IhM5zHSyPawlN4WX5Mgbu8HgW8GdJj2LzLztHDIqZRRuleXA'
  },
  2: {
    name: 'Rudraksha Sacred Rakhi',
    icon: '📿',
    img: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDwAuD61c82cyWNX0PFa77UJrvtXb2o2mLIuK8K8FvXCRuYInKBWruke4m9l7Gxxh984tDgFxHPVsqt_Vwd3qTes7nZ9F5-53YY8EazEK1ydgpWVh4et5zgTy3SyCvD5BfMHag1TaSj2QVzNGnsWG4HsTxX_r9CdC6-TnjGNmeNMCETqb6Vh10Qqkzo3smXC2m-Y5Ui0WqvaGyRf-vLnTc2tFj6yZlQM2Z0F7_pntD9vNkJmi3PkrjH'
  },
  3: {
    name: 'Silver Thread Zari Rakhi',
    icon: '✨',
    img: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAdJgvnpt9efEREwzgTGxPH8MPQ5AWE0t4mqfy-UhaDL-arn2W1sEI1ND2UOlcFMlEXpeog2gXEuJMN9mQudEErF0Mxvx1eUO17QBpvGtTyDHcJBLnJOr11V3pjEm3aLuZaUr7ucec0xi9sy-j4vzY7eGrpFjBUpRTcBGTPnwtWSk7wFKxGFfREfhaQEQ5mi9OBNsmf8qGSHlW-wB7hhVnd77ika9jsTD5Z0FT1B7OQtcCsRvTnJhFU'
  }
};

function selectRakhiOption(optId, btnEl) {
  selectedRakhiOptionId = optId;
  document.querySelectorAll('.rakhi-opt-btn').forEach(btn => {
    btn.classList.remove('border-[#e5534b]');
    btn.classList.add('border-transparent');
  });
  if (btnEl) {
    btnEl.classList.remove('border-transparent');
    btnEl.classList.add('border-[#e5534b]');
  }

  const rData = rakhiImages[optId] || rakhiImages[1];
  const dragImg = document.getElementById('draggableRakhiImg');
  const nameEl = document.getElementById('selectedRakhiName');
  const iconEl = document.getElementById('selectedRakhiIcon');

  if (dragImg) dragImg.src = rData.img;
  if (nameEl) nameEl.innerText = rData.name;
  if (iconEl) iconEl.innerText = rData.icon;
}

function navigateFestiveStep(dir) {
  const nextStep = currentFestiveStep + dir;
  if (nextStep < 1 || nextStep > 5) return;

  const currentEl = document.getElementById(`rakhiStep${currentFestiveStep}`);
  if (currentEl) {
    currentEl.classList.add('hidden');
    currentEl.classList.remove('flex');
  }

  currentFestiveStep = nextStep;

  const nextEl = document.getElementById(`rakhiStep${currentFestiveStep}`);
  if (nextEl) {
    nextEl.classList.remove('hidden');
    nextEl.classList.add('flex');
  }

  const badge = document.getElementById('festiveModalStepBadge');
  if (badge) badge.innerText = `Step ${currentFestiveStep} of 5`;

  const backBtn = document.getElementById('festiveModalBackBtn');
  if (backBtn) {
    if (currentFestiveStep > 1) {
      backBtn.classList.remove('invisible');
    } else {
      backBtn.classList.add('invisible');
    }
  }

  const nextBtn = document.getElementById('festiveModalNextBtn');
  if (nextBtn) {
    if (currentFestiveStep === 5) {
      nextBtn.innerText = 'FINISH ✓';
      nextBtn.onclick = function() { closeFestiveRakhiModal(); };
    } else if (currentFestiveStep === 3) {
      nextBtn.innerText = 'SEE YOUR GIFT 🎁';
      nextBtn.onclick = function() { navigateFestiveStep(1); };
    } else {
      nextBtn.innerText = 'NEXT ➔';
      nextBtn.onclick = function() { navigateFestiveStep(1); };
    }
  }

  if (currentFestiveStep === 2) {
    initRakhiDragLogic();
  } else if (currentFestiveStep === 3) {
    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
    if (typeof playTempleBellSound === 'function') playTempleBellSound();
  } else if (currentFestiveStep === 4) {
    initModalScratchCanvas();
  }
}

function triggerRakhiTiedSuccess() {
  navigateFestiveStep(1);
}

function initRakhiDragLogic() {
  const dragEl = document.getElementById('draggableRakhi');
  const targetEl = document.getElementById('rakhiTargetZone');
  const container = document.getElementById('rakhiInteractionArea');

  if (!dragEl || !container) return;

  let active = false;
  let currentX, currentY, initialX, initialY;
  let xOffset = 0, yOffset = 0;

  function dragStart(e) {
    if (e.type === "touchstart") {
      initialX = e.touches[0].clientX - xOffset;
      initialY = e.touches[0].clientY - yOffset;
    } else {
      initialX = e.clientX - xOffset;
      initialY = e.clientY - yOffset;
    }
    if (e.target === dragEl || dragEl.contains(e.target)) {
      active = true;
    }
  }

  function dragEnd() {
    if (!active) return;
    initialX = currentX;
    initialY = currentY;
    active = false;

    if (dragEl && targetEl) {
      const dragRect = dragEl.getBoundingClientRect();
      const targetRect = targetEl.getBoundingClientRect();
      const dist = Math.hypot(
        (dragRect.left + dragRect.width/2) - (targetRect.left + targetRect.width/2),
        (dragRect.top + dragRect.height/2) - (targetRect.top + targetRect.height/2)
      );
      if (dist < 110) {
        triggerRakhiTiedSuccess();
      }
    }
  }

  function drag(e) {
    if (active) {
      e.preventDefault();
      if (e.type === "touchmove") {
        currentX = e.touches[0].clientX - initialX;
        currentY = e.touches[0].clientY - initialY;
      } else {
        currentX = e.clientX - initialX;
        currentY = e.clientY - initialY;
      }
      xOffset = currentX;
      yOffset = currentY;
      dragEl.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;
    }
  }

  container.addEventListener("touchstart", dragStart, {passive: false});
  container.addEventListener("touchend", dragEnd, {passive: false});
  container.addEventListener("touchmove", drag, {passive: false});
  container.addEventListener("mousedown", dragStart, false);
  window.addEventListener("mouseup", dragEnd, false);
  container.addEventListener("mousemove", drag, false);
}

function initModalScratchCanvas() {
  const canvas = document.getElementById('modalScratchCanvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const rect = canvas.getBoundingClientRect();
  canvas.width = rect.width || 260;
  canvas.height = rect.height || 128;

  // Fill gold scratch foil
  const grad = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
  grad.addColorStop(0, '#d4af37');
  grad.addColorStop(0.5, '#f7e6a6');
  grad.addColorStop(1, '#b89343');
  ctx.fillStyle = grad;
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  ctx.fillStyle = '#241a00';
  ctx.font = 'bold 12px Montserrat, sans-serif';
  ctx.textAlign = 'center';
  ctx.fillText('✨ Scratch Here to Reveal 🪙', canvas.width / 2, canvas.height / 2 + 4);

  let isScratching = false;

  function scratch(e) {
    if (!isScratching) return;
    e.preventDefault();
    const cRect = canvas.getBoundingClientRect();
    const x = (e.touches ? e.touches[0].clientX : e.clientX) - cRect.left;
    const y = (e.touches ? e.touches[0].clientY : e.clientY) - cRect.top;

    ctx.globalCompositeOperation = 'destination-out';
    ctx.beginPath();
    ctx.arc(x, y, 16, 0, Math.PI * 2, false);
    ctx.fill();
  }

  canvas.addEventListener('mousedown', () => isScratching = true);
  window.addEventListener('mouseup', () => isScratching = false);
  canvas.addEventListener('mousemove', scratch);

  canvas.addEventListener('touchstart', (e) => { isScratching = true; scratch(e); }, {passive: false});
  window.addEventListener('touchend', () => isScratching = false);
  canvas.addEventListener('touchmove', scratch, {passive: false});
}

function openFestiveRakhiModal() {
  const container = document.getElementById('festiveRakhiModalContainer');
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

function closeFestiveRakhiModal() {
  const container = document.getElementById('festiveRakhiModalContainer');
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

// Auto-portal to body on document ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    const c = document.getElementById('festiveRakhiModalContainer');
    if (c && c.parentElement !== document.body) document.body.appendChild(c);
  });
} else {
  const c = document.getElementById('festiveRakhiModalContainer');
  if (c && c.parentElement !== document.body) document.body.appendChild(c);
}
</script>

