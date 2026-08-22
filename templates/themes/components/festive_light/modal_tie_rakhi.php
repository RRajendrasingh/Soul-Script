<?php
/**
 * Component: 100% Google Stitch Design Matching Full-Stage "Virtual Rakhi Ceremony" Modal
 */
?>

<div id="festiveRakhiModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-0 sm:p-6 bg-black/85 backdrop-blur-md transition-opacity duration-300">
  <div id="festiveRakhiModal" class="relative w-full h-full sm:h-auto sm:max-w-2xl sm:max-h-[94vh] bg-[#fcf6f0] border-0 sm:border-2 border-[#d4af37]/60 rounded-none sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col">
    
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
      <div id="rakhiStep1" class="flex flex-col items-center text-center space-y-4 max-w-md mx-auto py-1 w-full">
        <?php 
        $heroPhoto = !empty($receiverPhoto) ? resolveMediaUrl($receiverPhoto) : (!empty($cleanReceiverPhoto) ? $cleanReceiverPhoto : (!empty($galleryMedia[0]) ? (is_array($galleryMedia[0]) ? resolveMediaUrl($galleryMedia[0]['file_path'] ?? '') : resolveMediaUrl($galleryMedia[0])) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCZfsICxK34oixmN1AZRizpBM2bZC5BAB_XYhQLhxKaZRKgNxEv8X9v3Z4lzEedQVni4JuXg6LECezawWUPThbfyUKDAnCX14tBlz_SHV5Z0nHTlrYpNX81aS2JbA1-fREPTFZBGfA4Oin9IzGHb5PZxUinsPuL6pU81_ZnpEIrbooze4l1aomWnjr8FWAmwYUcQR92cij0amxmT3sNwf3Uq4XO2ot9yJ_JaQvk6cQiDvzzRP2Mvcj0'));
        ?>
        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full bg-white border-4 border-[#d4af37]/60 shadow-xl relative overflow-hidden flex items-center justify-center p-1 shrink-0">
          <img src="<?= htmlspecialchars($heroPhoto) ?>" alt="Sibling Memory" class="w-full h-full object-cover rounded-full">
        </div>

        <div class="space-y-0.5">
          <h4 class="text-xl sm:text-2xl font-bold font-serif text-[#4a232f]">Prepare Sacred Thali</h4>
          <p class="text-xs text-[#7a5c68]">Select your chosen Rakhi thread to perform the virtual ceremony.</p>
        </div>

        <!-- 3 Rakhi Options Grid -->
        <div class="grid grid-cols-3 gap-2.5 w-full pt-1">
          <button type="button" onclick="selectRakhiOption(1, this)" class="rakhi-opt-btn p-2.5 rounded-2xl border-2 border-[#e5534b] bg-white flex flex-col items-center gap-1 cursor-pointer shadow-sm hover:shadow-md transition-all active:scale-95">
            <span class="text-2xl sm:text-3xl">🏵️</span>
            <span class="text-[11px] sm:text-xs font-bold text-[#4a232f] leading-tight">Royal Kundan</span>
          </button>
          <button type="button" onclick="selectRakhiOption(2, this)" class="rakhi-opt-btn p-2.5 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-1 cursor-pointer shadow-sm hover:shadow-md transition-all active:scale-95">
            <span class="text-2xl sm:text-3xl">📿</span>
            <span class="text-[11px] sm:text-xs font-bold text-[#4a232f] leading-tight">Rudraksha</span>
          </button>
          <button type="button" onclick="selectRakhiOption(3, this)" class="rakhi-opt-btn p-2.5 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-1 cursor-pointer shadow-sm hover:shadow-md transition-all active:scale-95">
            <span class="text-2xl sm:text-3xl">✨</span>
            <span class="text-[11px] sm:text-xs font-bold text-[#4a232f] leading-tight">Silver Thread</span>
          </button>
        </div>
      </div>

      <!-- STEP 2: STITCH CEREMONY READY TO TIE (BARE WRIST + BOTTOM TRAY DRAGGABLE) -->
      <div id="rakhiStep2" class="hidden flex-col items-center space-y-3.5 max-w-md mx-auto py-1 w-full">
        <div class="text-center space-y-0.5">
          <h4 class="text-xl sm:text-2xl font-bold font-serif text-[#4a232f]">Ceremony: Ready to Tie</h4>
          <p class="text-xs text-[#7a5c68]">Drag the Rakhi upwards onto the wrist to tie</p>
        </div>

        <!-- Bare Wrist Interactive Stage -->
        <div id="rakhiInteractionArea" class="relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl border-2 sm:border-4 border-[#d4af37]/60 bg-[#fff9f5] touch-none">
          <!-- 100% Bare Wrist Image (Stitch Exact Asset) -->
          <img id="wristBgImage" src="<?= APP_URL ?>/assets/images/rakhi_wrist_bare.jpg" alt="Brother's Bare Wrist" class="absolute inset-0 w-full h-full object-cover select-none pointer-events-none" loading="eager" decoding="sync">
          
          <!-- Auspicious Target Zone Overlay -->
          <div id="rakhiTargetZone" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-24 border-2 border-dashed border-[#d4af37] bg-white/40 rounded-2xl flex flex-col items-center justify-center backdrop-blur-[2px] transition-all pointer-events-none shadow-inner">
            <span class="text-[11px] font-black text-[#78350f] uppercase tracking-widest drop-shadow-sm flex items-center gap-1">
              <span>🧵</span> <span>Drop Rakhi Here</span>
            </span>
            <span class="text-[9px] font-bold text-[#b45309] mt-0.5 animate-pulse">⬆️ Drag from below to tie</span>
          </div>
        </div>

        <!-- Bottom Tray: Draggable Rakhi Thread & Action Button -->
        <div class="w-full bg-white rounded-2xl p-3 sm:p-4 border border-[#e8d5c4] shadow-sm flex flex-col gap-2.5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span id="selectedRakhiIcon" class="text-xl">🏵️</span>
              <span id="selectedRakhiName" class="text-xs sm:text-sm font-bold text-[#4a232f]">Royal Kundan Rakhi</span>
            </div>
            <button type="button" onclick="performAutoTieAnimation()" class="px-4 py-1.5 bg-gradient-to-r from-[#d32f2f] to-[#f57c00] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow hover:opacity-90 active:scale-95 transition-all cursor-pointer flex items-center gap-1">
              <span>Tie Now</span> <span>🧵</span>
            </button>
          </div>

          <!-- Bottom Draggable Tray Container (100% Fixed/Stationary Box) -->
          <div id="draggableRakhiTray" class="w-full h-20 bg-[#fcf6f0] border-2 border-dashed border-[#d4af37]/60 rounded-xl flex items-center justify-center relative overflow-visible select-none touch-none" style="touch-action: none;">
            <!-- Only the Rakhi itself is draggable / transformed -->
            <div id="draggableRakhi" class="w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing select-none relative z-30 transition-transform" style="touch-action: none;">
              <!-- SVG / Pure Clip-Art Content -->
              <div id="rakhiVectorContainer" class="w-48 h-16 flex items-center justify-center pointer-events-none select-none"></div>
            </div>
          </div>
          <span class="text-[10px] text-gray-500 text-center font-medium block">👆 Grab the Rakhi sticker and drag upwards to the wrist</span>
        </div>
      </div>

      <!-- STEP 3: RAKHI TIED SUCCESSFULLY (TIED WRIST + SEE YOUR GIFT) -->
      <div id="rakhiStep3" class="hidden flex-col items-center space-y-4 max-w-md mx-auto py-1 text-center w-full">
        <div class="space-y-0.5">
          <h4 class="text-2xl sm:text-3xl font-black font-serif text-[#4a232f]">Rakhi Tied Successfully!</h4>
          <p class="text-xs text-[#7a5c68]">A sacred bond of love, protection, and lifelong joy.</p>
        </div>

        <!-- Tied Wrist Image Container (Exact Stitch Design) -->
        <div class="relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl border-2 sm:border-4 border-[#d4af37]/60 bg-[#fff9f5]">
          <img id="tiedWristBgImage" src="<?= APP_URL ?>/assets/images/rakhi_wrist_tied.jpg" alt="Rakhi Tied Successfully on Wrist" class="absolute inset-0 w-full h-full object-cover select-none" loading="eager" decoding="sync">
          
          <div class="absolute top-3 right-3 px-3 py-1 bg-[#1f4e27]/90 border border-[#52b76b] text-[#98ecaa] font-bold text-[10px] uppercase rounded-full shadow-lg flex items-center gap-1 backdrop-blur-sm">
            <span>✓</span> <span>Tied with Love</span>
          </div>
        </div>

        <!-- Prominent "SEE YOUR GIFT" Button -->
        <div class="w-full pt-1">
          <button type="button" onclick="navigateFestiveStep(1)" class="w-full py-3.5 px-8 bg-gradient-to-r from-[#e57800] via-[#f59e0b] to-[#d97706] text-white font-extrabold text-sm sm:text-base uppercase tracking-wider rounded-2xl shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-2">
            <span>SEE YOUR SHAGUN 🎁</span>
          </button>
        </div>
      </div>

      <!-- STEP 4: ROYAL SHAGUN LIFAFA LETTER & BLESSING -->
      <div id="rakhiStep4" class="hidden flex-col space-y-3.5 max-w-md mx-auto py-1 w-full">
        <div class="text-center space-y-0.5">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#ffdcc5] text-[#934b00] text-[10px] font-bold uppercase tracking-wider">
            <span>🧧</span> <span>Royal Shagun Lifafa</span>
          </span>
          <h4 class="text-lg sm:text-xl font-bold font-serif text-[#4a232f]">Your Shagun Letter &amp; Blessings</h4>
        </div>

        <!-- Shagun Envelope Letter Card -->
        <div class="w-full bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg border border-[#e8d5c4] p-5 sm:p-6 flex flex-col justify-between space-y-4">
          <div class="space-y-2">
            <div class="flex items-center justify-between border-b border-[#f4e5d8] pb-2">
              <span class="text-xs font-bold uppercase tracking-wider text-[#934b00]">Shagun Sandesh 💌</span>
              <span class="text-xs text-gray-500">Raksha Bandhan 2026</span>
            </div>
            <span class="text-3xl text-[#d4af37] block opacity-75 leading-none">❝</span>
            <p class="text-sm sm:text-base text-[#4a232f] italic font-serif leading-relaxed px-1">
              "<?= htmlspecialchars($loveNoteText ?: "Gunnu, mera saara pyaar aur dher saare aashirwaad iss shagun mein h! 🎁") ?>"
            </p>
          </div>

          <!-- Shagun Aashirwaad Coin Box -->
          <div class="w-full bg-[#fcf6f0] border border-[#d4af37]/50 rounded-2xl p-4 flex items-center gap-3.5 shadow-inner">
            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#d4af37] via-[#f7e6a6] to-[#b89343] text-[#241a00] font-black text-2xl flex items-center justify-center shadow-md shrink-0">
              ₹
            </div>
            <div class="text-left">
              <span class="text-[10px] font-bold text-[#e5534b] uppercase block tracking-wider">Shagun &amp; Aashirwaad</span>
              <span class="text-xs font-bold text-[#4a232f] block">Forever Love, Care &amp; Lifelong Protection</span>
            </div>
          </div>

          <div class="text-right border-t border-[#f4e5d8] pt-2">
            <span class="text-[10px] font-bold text-[#934b00] uppercase tracking-wider block">— With lots of love,</span>
            <span class="text-sm font-bold font-serif text-[#4a232f]"><?= htmlspecialchars($buyerName ?: 'Brother') ?></span>
          </div>
        </div>
      </div>

      <!-- STEP 5: THERE'S MORE TO CELEBRATE (KEEPSAKES & FINISH) -->
      <div id="rakhiStep5" class="hidden flex-col items-center space-y-3.5 max-w-md mx-auto py-1 text-center w-full">
        <div class="w-14 h-14 mx-auto rounded-full bg-[#ffdcc5] text-[#934b00] flex items-center justify-center text-2xl shadow-md relative">
          🎁
          <div class="absolute -top-1 -right-1 w-5 h-5 bg-[#0061a5] text-white rounded-full flex items-center justify-center text-[9px] font-bold shadow-sm">★</div>
        </div>

        <div class="space-y-1">
          <h4 class="text-xl sm:text-2xl font-bold font-serif text-[#4a232f]">There's more to celebrate!</h4>
          <p class="text-xs text-[#7a5c68] max-w-xs mx-auto leading-relaxed">
            Download your high-definition Printable Keepsakes or explore the complete childhood memory gallery!
          </p>
        </div>

        <div class="w-full flex flex-col gap-2 pt-1">
          <button type="button" onclick="downloadWallKeepsakePoster()" class="w-full py-3 px-4 bg-gradient-to-r from-[#d4af37] via-[#f7e6a6] to-[#b89343] text-[#241a00] font-extrabold text-xs uppercase tracking-wider rounded-xl shadow hover:scale-[1.02] transition-all cursor-pointer flex items-center justify-center gap-2">
            <span>🖼️</span> <span>Wall Poster (300 DPI)</span>
          </button>
          <button type="button" onclick="downloadSiblingPhotobookPDF()" class="w-full py-3 px-4 bg-gradient-to-r from-[#10b981] via-[#34d399] to-[#059669] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow hover:scale-[1.02] transition-all cursor-pointer flex items-center justify-center gap-2">
            <span>📖</span> <span>Keepsake Book (PDF)</span>
          </button>
          <button type="button" onclick="closeFestiveRakhiModal()" class="w-full py-3 px-4 bg-[#934b00] hover:bg-[#7a3e00] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow hover:shadow-md transition-all cursor-pointer mt-1">
            Explore Full Gift Experience ➔
          </button>
        </div>
      </div>

    </div>

    <!-- Modal Footer Controls (Sticky Bottom with Clean Elevation) -->
    <div class="sticky bottom-0 z-20 px-4 sm:px-6 py-3.5 bg-[#f4e5d8] border-t border-[#e8d5c4] flex items-center justify-between shrink-0 shadow-[0_-4px_15px_rgba(0,0,0,0.06)]">
      <button type="button" id="festiveModalBackBtn" onclick="navigateFestiveStep(-1)" class="px-5 py-2 bg-white text-[#4a232f] font-bold text-xs rounded-full hover:bg-gray-100 transition-all cursor-pointer invisible shadow-sm">
        ← Back
      </button>

      <button type="button" id="festiveModalNextBtn" onclick="navigateFestiveStep(1)" class="px-7 py-2 bg-gradient-to-r from-[#d32f2f] to-[#f57c00] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-md hover:opacity-90 active:scale-95 transition-all cursor-pointer">
        NEXT ➔
      </button>
    </div>

  </div>
</div>

<script>
let currentFestiveStep = 1;
let selectedRakhiOptionId = 1;

// 100% Transparent High-Definition Vector Rakhi SVG Templates
const rakhiVectors = {
  1: {
    name: 'Royal Kundan Rakhi',
    icon: '🏵️',
    svg: `<svg viewBox="0 0 240 60" class="w-full h-full filter drop-shadow-md select-none pointer-events-none">
      <!-- Braided Red & Gold Silk Thread -->
      <path d="M0 30 Q 30 25, 60 30 T 120 30" stroke="#d32f2f" stroke-width="4" fill="none" stroke-linecap="round"/>
      <path d="M0 30 Q 30 35, 60 30 T 120 30" stroke="#f59e0b" stroke-width="2" fill="none" stroke-dasharray="4,2"/>
      <path d="M120 30 Q 150 25, 180 30 T 240 30" stroke="#d32f2f" stroke-width="4" fill="none" stroke-linecap="round"/>
      <path d="M120 30 Q 150 35, 180 30 T 240 30" stroke="#f59e0b" stroke-width="2" fill="none" stroke-dasharray="4,2"/>
      <!-- Kundan Centerpiece -->
      <circle cx="120" cy="30" r="22" fill="#b91c1c" stroke="#d4af37" stroke-width="3"/>
      <circle cx="120" cy="30" r="16" fill="#fbbf24" stroke="#78350f" stroke-width="1.5"/>
      <circle cx="120" cy="30" r="8" fill="#dc2626"/>
      <circle cx="120" cy="30" r="4" fill="#ffffff" opacity="0.9"/>
      <!-- Kundan Petals -->
      <circle cx="120" cy="10" r="4" fill="#fbbf24" stroke="#d4af37" stroke-width="1"/>
      <circle cx="120" cy="50" r="4" fill="#fbbf24" stroke="#d4af37" stroke-width="1"/>
      <circle cx="100" cy="30" r="4" fill="#fbbf24" stroke="#d4af37" stroke-width="1"/>
      <circle cx="140" cy="30" r="4" fill="#fbbf24" stroke="#d4af37" stroke-width="1"/>
    </svg>`
  },
  2: {
    name: 'Rudraksha Sacred Rakhi',
    icon: '📿',
    svg: `<svg viewBox="0 0 240 60" class="w-full h-full filter drop-shadow-md select-none pointer-events-none">
      <!-- Sacred Holy Moli Thread -->
      <path d="M0 30 L 90 30" stroke="#ea580c" stroke-width="4" fill="none" stroke-linecap="round"/>
      <path d="M0 30 L 90 30" stroke="#facc15" stroke-width="2" fill="none" stroke-dasharray="6,3"/>
      <path d="M150 30 L 240 30" stroke="#ea580c" stroke-width="4" fill="none" stroke-linecap="round"/>
      <path d="M150 30 L 240 30" stroke="#facc15" stroke-width="2" fill="none" stroke-dasharray="6,3"/>
      <!-- Gold Bead Accents -->
      <circle cx="95" cy="30" r="6" fill="#eab308" stroke="#713f12" stroke-width="1"/>
      <circle cx="145" cy="30" r="6" fill="#eab308" stroke="#713f12" stroke-width="1"/>
      <!-- Sacred Rudraksha Bead with Textures -->
      <circle cx="120" cy="30" r="18" fill="#78350f" stroke="#451a03" stroke-width="2"/>
      <path d="M112 18 Q 120 30 112 42" stroke="#451a03" stroke-width="2" fill="none"/>
      <path d="M120 12 L 120 48" stroke="#451a03" stroke-width="2" fill="none"/>
      <path d="M128 18 Q 120 30 128 42" stroke="#451a03" stroke-width="2" fill="none"/>
      <!-- Auspicious Red Dot -->
      <circle cx="120" cy="30" r="3" fill="#dc2626"/>
    </svg>`
  },
  3: {
    name: 'Silver Thread Zari Rakhi',
    icon: '✨',
    svg: `<svg viewBox="0 0 240 60" class="w-full h-full filter drop-shadow-md select-none pointer-events-none">
      <!-- Metallic Silver & Blue Braided Thread -->
      <path d="M0 30 Q 30 26, 60 30 T 120 30" stroke="#94a3b8" stroke-width="4" fill="none" stroke-linecap="round"/>
      <path d="M0 30 Q 30 34, 60 30 T 120 30" stroke="#0284c7" stroke-width="2" fill="none" stroke-dasharray="5,2"/>
      <path d="M120 30 Q 150 26, 180 30 T 240 30" stroke="#94a3b8" stroke-width="4" fill="none" stroke-linecap="round"/>
      <path d="M120 30 Q 150 34, 180 30 T 240 30" stroke="#0284c7" stroke-width="2" fill="none" stroke-dasharray="5,2"/>
      <!-- Silver Starburst Crystal Centerpiece -->
      <circle cx="120" cy="30" r="20" fill="#f8fafc" stroke="#38bdf8" stroke-width="2"/>
      <!-- Diamond Star -->
      <polygon points="120,14 125,25 136,30 125,35 120,46 115,35 104,30 115,25" fill="#0284c7" stroke="#0369a1" stroke-width="1"/>
      <circle cx="120" cy="30" r="5" fill="#ffffff"/>
    </svg>`
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

  const rData = rakhiVectors[optId] || rakhiVectors[1];
  const container = document.getElementById('rakhiVectorContainer');
  const nameEl = document.getElementById('selectedRakhiName');
  const iconEl = document.getElementById('selectedRakhiIcon');

  if (container) container.innerHTML = rData.svg;
  if (nameEl) nameEl.innerText = rData.name;
  if (iconEl) iconEl.innerText = rData.icon;
}

function navigateFestiveStep(dir) {
  const nextStep = currentFestiveStep + dir;
  if (nextStep < 1 || nextStep > 5) return;

  const currentEl = document.getElementById(`rakhiStep${currentFestiveStep}`);
  if (currentEl) {
    currentEl.classList.add('hidden');
  }

  currentFestiveStep = nextStep;

  const nextEl = document.getElementById(`rakhiStep${currentFestiveStep}`);
  if (nextEl) {
    nextEl.classList.remove('hidden');
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
      nextBtn.innerText = 'SEE YOUR SHAGUN 🎁';
      nextBtn.onclick = function() { navigateFestiveStep(1); };
    } else if (currentFestiveStep === 2) {
      nextBtn.innerText = 'TIE RAKHI 🧵';
      nextBtn.onclick = function() { performAutoTieAnimation(); };
    } else {
      nextBtn.innerText = 'NEXT ➔';
      nextBtn.onclick = function() { navigateFestiveStep(1); };
    }
  }

  if (currentFestiveStep === 2) {
    selectRakhiOption(selectedRakhiOptionId);
    initRakhiDragLogic();
  } else if (currentFestiveStep === 3) {
    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
    if (typeof playTempleBellSound === 'function') playTempleBellSound();
    
    // Save completion state in browser memory
    try {
      const pId = '<?= addslashes($initialLockData['page_id'] ?? 'rakhi_festive') ?>';
      localStorage.setItem('rakhi_ceremony_completed_' + pId, 'true');
      updateCeremonyCompletedUI();
    } catch(e) {}
  }
}

let isTyingAnimating = false;

function performAutoTieAnimation() {
  if (isTyingAnimating) return;
  isTyingAnimating = true;

  const draggableRakhi = document.getElementById('draggableRakhi');
  const targetEl = document.getElementById('rakhiTargetZone');
  
  if (draggableRakhi && targetEl) {
    const targetRect = targetEl.getBoundingClientRect();
    const rakhiRect = draggableRakhi.getBoundingClientRect();
    const deltaY = targetRect.top + (targetRect.height / 2) - (rakhiRect.top + (rakhiRect.height / 2));
    const deltaX = targetRect.left + (targetRect.width / 2) - (rakhiRect.left + (rakhiRect.width / 2));

    draggableRakhi.style.transition = 'transform 0.55s cubic-bezier(0.34, 1.56, 0.64, 1)';
    draggableRakhi.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0) scale(1.15)`;

    setTimeout(() => {
      triggerRakhiTiedSuccess();
      isTyingAnimating = false;
      draggableRakhi.style.transition = '';
      draggableRakhi.style.transform = 'translate3d(0, 0, 0)';
    }, 600);
  } else {
    triggerRakhiTiedSuccess();
    isTyingAnimating = false;
  }
}

function triggerRakhiTiedSuccess() {
  navigateFestiveStep(1);
}

function initRakhiDragLogic() {
  const draggableRakhi = document.getElementById('draggableRakhi');
  const targetEl = document.getElementById('rakhiTargetZone');
  const tray = document.getElementById('draggableRakhiTray');

  if (!draggableRakhi || !targetEl || !tray) return;

  let active = false;
  let currentX = 0, currentY = 0, initialX = 0, initialY = 0;

  function dragStart(e) {
    if (isTyingAnimating) return;
    draggableRakhi.style.transition = 'none';
    if (e.type === "touchstart") {
      initialX = e.touches[0].clientX;
      initialY = e.touches[0].clientY;
    } else {
      initialX = e.clientX;
      initialY = e.clientY;
    }
    active = true;
    draggableRakhi.style.zIndex = '50';
  }

  function dragEnd() {
    if (!active) return;
    active = false;

    // Check if dragged upwards towards target zone
    if (currentY < -60) {
      performAutoTieAnimation();
    } else {
      // Spring back smoothly into the fixed tray
      draggableRakhi.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
      draggableRakhi.style.transform = 'translate3d(0, 0, 0)';
      currentX = 0;
      currentY = 0;
    }
  }

  function drag(e) {
    if (!active) return;
    e.preventDefault();
    if (e.type === "touchmove") {
      currentX = e.touches[0].clientX - initialX;
      currentY = e.touches[0].clientY - initialY;
    } else {
      currentX = e.clientX - initialX;
      currentY = e.clientY - initialY;
    }
    // Allow free upward drag and slight horizontal drift
    currentY = Math.min(10, currentY);
    draggableRakhi.style.transform = `translate3d(${currentX}px, ${currentY}px, 0) scale(1.08)`;
  }

  draggableRakhi.addEventListener("touchstart", dragStart, {passive: false});
  window.addEventListener("touchend", dragEnd, {passive: false});
  window.addEventListener("touchmove", drag, {passive: false});
  draggableRakhi.addEventListener("mousedown", dragStart, false);
  window.addEventListener("mouseup", dragEnd, false);
  window.addEventListener("mousemove", drag, false);
}

function openFestiveRakhiModal(targetStep = 1) {
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

  // Navigate to target step if specified
  if (targetStep !== currentFestiveStep) {
    const diff = targetStep - currentFestiveStep;
    navigateFestiveStep(diff);
  }
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

function updateCeremonyCompletedUI() {
  try {
    const pId = '<?= addslashes($initialLockData['page_id'] ?? 'rakhi_festive') ?>';
    const isCompleted = localStorage.getItem('rakhi_ceremony_completed_' + pId) === 'true';
    const heroBtn = document.getElementById('heroCeremonyBtn');
    const heroBadge = document.getElementById('heroCeremonyBadge');
    
    if (isCompleted && heroBtn) {
      heroBtn.innerHTML = '<span>View Shagun &amp; Keepsakes 🎁</span>';
      heroBtn.onclick = function() { openFestiveRakhiModal(4); };
    }
  } catch(e) {}
}

window.openFestiveRakhiModal = openFestiveRakhiModal;
window.closeFestiveRakhiModal = closeFestiveRakhiModal;
window.navigateFestiveStep = navigateFestiveStep;
window.triggerRakhiTiedSuccess = triggerRakhiTiedSuccess;
window.performAutoTieAnimation = performAutoTieAnimation;
window.selectRakhiOption = selectRakhiOption;

// Auto-portal to body & check completion on ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    const c = document.getElementById('festiveRakhiModalContainer');
    if (c && c.parentElement !== document.body) document.body.appendChild(c);
    selectRakhiOption(1);
    updateCeremonyCompletedUI();
  });
} else {
  const c = document.getElementById('festiveRakhiModalContainer');
  if (c && c.parentElement !== document.body) document.body.appendChild(c);
  selectRakhiOption(1);
  updateCeremonyCompletedUI();
}
</script>


