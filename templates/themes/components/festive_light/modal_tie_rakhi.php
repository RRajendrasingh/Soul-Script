<?php
/**
 * Component: 100% Google Stitch Design Matching Full-Stage "Virtual Rakhi Ceremony" Modal
 * Based on Google Stitch Screen Files: FINAL_TIE_RAKHI_DESKTOP - 1 to 5 & FINAL_TIE_RAKHI_MOBILE - 1 to 5
 */
?>

<div id="festiveRakhiModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 bg-black/80 backdrop-blur-md transition-opacity duration-300">
  <div id="festiveRakhiModal" class="relative w-full max-w-5xl bg-[#fcf6f0] border-2 border-[#d4af37]/60 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
    
    <!-- Modal Header -->
    <div class="px-6 py-4 bg-[#f4e5d8]/80 border-b border-[#e8d5c4] flex items-center justify-between shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-[#d32f2f] text-white flex items-center justify-center shadow-md">
          <span class="material-symbols-outlined text-xl">celebration</span>
        </div>
        <div>
          <h3 class="text-lg font-bold font-serif text-[#4a232f]">Virtual Rakhi Ceremony</h3>
          <span id="festiveModalStepBadge" class="text-xs font-bold text-[#e5534b] uppercase tracking-wider block">Step 1 of 5</span>
        </div>
      </div>

      <!-- Close Button (Cross ✕) -->
      <button type="button" onclick="closeFestiveRakhiModal()" class="w-9 h-9 rounded-full bg-white text-[#4a232f] hover:bg-[#e5534b] hover:text-white font-bold text-lg flex items-center justify-center transition-colors shadow-sm cursor-pointer" title="Close Modal">
        ✕
      </button>
    </div>

    <!-- Modal Content Stage (Steps 1 to 5 Container) -->
    <div class="p-4 sm:p-8 overflow-y-auto flex-1 bg-[#fcf6f0] relative">

      <!-- STEP 1: PREPARE SACRED THALI & SELECT RAKHI -->
      <div id="rakhiStep1" class="space-y-6 max-w-2xl mx-auto text-center py-2">
        <div class="w-48 h-48 sm:w-56 sm:h-56 mx-auto rounded-full bg-white border-8 border-white shadow-2xl relative overflow-hidden flex items-center justify-center">
          <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZfsICxK34oixmN1AZRizpBM2bZC5BAB_XYhQLhxKaZRKgNxEv8X9v3Z4lzEedQVni4JuXg6LECezawWUPThbfyUKDAnCX14tBlz_SHV5Z0nHTlrYpNX81aS2JbA1-fREPTFZBGfA4Oin9IzGHb5PZxUinsPuL6pU81_ZnpEIrbooze4l1aomWnjr8FWAmwYUcQR92cij0amxmT3sNwf3Uq4XO2ot9yJ_JaQvk6cQiDvzzRP2Mvcj0" alt="Puja Thali" class="w-full h-full object-cover">
        </div>

        <div class="space-y-2">
          <h4 class="text-2xl sm:text-3xl font-bold font-serif text-[#4a232f]">Prepare Sacred Thali</h4>
          <p class="text-sm text-[#7a5c68]">Select your chosen Rakhi thread to perform the virtual ceremony.</p>
        </div>

        <!-- 3 Rakhi Options Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
          <button type="button" onclick="selectRakhiOption(1, this)" class="rakhi-opt-btn p-4 rounded-2xl border-2 border-[#e5534b] bg-white flex flex-col items-center gap-2 cursor-pointer shadow-md hover:shadow-lg transition-all">
            <span class="text-3xl">🏵️</span>
            <span class="text-sm font-bold text-[#4a232f]">Royal Kundan</span>
          </button>
          <button type="button" onclick="selectRakhiOption(2, this)" class="rakhi-opt-btn p-4 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-2 cursor-pointer shadow-md hover:shadow-lg transition-all">
            <span class="text-3xl">📿</span>
            <span class="text-sm font-bold text-[#4a232f]">Rudraksha</span>
          </button>
          <button type="button" onclick="selectRakhiOption(3, this)" class="rakhi-opt-btn p-4 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-2 cursor-pointer shadow-md hover:shadow-lg transition-all">
            <span class="text-3xl">✨</span>
            <span class="text-sm font-bold text-[#4a232f]">Silver Thread</span>
          </button>
        </div>
      </div>

      <!-- STEP 2: STITCH TWO-COLUMN WRIST CEREMONY (FINAL_TIE_RAKHI_DESKTOP - 1) -->
      <div id="rakhiStep2" class="hidden flex flex-col lg:flex-row gap-6 lg:gap-8 items-start justify-center">
        
        <!-- Left: Bare Wrist Interactive Canvas (55% Width) -->
        <div class="w-full lg:w-[55%] flex flex-col gap-4">
          <div id="rakhiInteractionArea" class="relative w-full aspect-video md:aspect-[4/3] rounded-2xl overflow-hidden shadow-xl border-2 border-[#d4af37]/40 bg-white touch-none">
            <img id="wristBgImage" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAKYFnHspSrA78KEP0ZJ0CfONICgStIewBATK-1s_Uv0bSQ4TGBRtnRmBqPjpRZEHyhL8DN2jT_w62j8AYTayJBAxDFp7l5IbijUlPSvRfGaVbNnDVavZt8T-S1EnKSYWoBrSe4O5hG9-INJnnJ0lMYLPCpURb8SYwvI1A7RXv6_fpw1mG8-VkfqUX9zvBJBGqCWEWp25saoTu-QGg4fSGXHiQRpXbb6gZr3UDzxF7ZEvaIZvECkwfL" alt="Wrist" class="w-full h-full object-cover select-none pointer-events-none">
            
            <!-- Target Zone Overlay -->
            <div id="rakhiTargetZone" class="absolute inset-x-8 top-1/3 bottom-10 border-2 border-dashed border-[#d32f2f]/80 bg-[#d32f2f]/10 rounded-2xl flex flex-col items-center justify-center backdrop-blur-xs transition-all pointer-events-none animate-pulse">
              <span class="material-symbols-outlined text-[#d32f2f] text-3xl">drag_click</span>
              <span class="text-xs font-bold text-white uppercase tracking-widest drop-shadow-md">Drag Rakhi Here</span>
            </div>

            <!-- Draggable Rakhi -->
            <div id="draggableRakhi" class="absolute bottom-4 left-1/2 -translate-x-1/2 w-24 h-24 cursor-grab active:cursor-grabbing z-20 touch-none" style="touch-action: none;">
              <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBrBgMcQPiJpH7ajGLFwJGVwRa8i1Cni6zU2PfdkIGO1Z52qnUapLQhEUB8IKS0u8LsO0x805E_mFTMaNxfNXiQGF5D9iEMg5ZFASInorsWjA8pMK8Lnt0I8jabUevnMHQAovnjP55h75mbDpmHRXuv5Mop7xu2TuWvXI-USFO8NdpfjFRy1d44pA0WFRFxbFRpYMqN_FHoNxjNn32vmJcW31a_RWHGrmth_-XXvYRw6uGgK-SS_5sT" alt="Rakhi" class="w-full h-full object-contain filter drop-shadow-lg select-none pointer-events-none">
            </div>
          </div>

          <button type="button" onclick="triggerRakhiTiedSuccess()" class="text-xs font-bold text-[#e5534b] hover:underline text-center cursor-pointer">
            👉 Tap here to instantly tie Rakhi
          </button>
        </div>

        <!-- Right: Control Panel & The Vow of Protection (45% Width) -->
        <div class="w-full lg:w-[45%] flex flex-col gap-6">
          <div class="bg-white rounded-2xl p-6 shadow-md border border-[#e8d5c4] space-y-4">
            <span class="text-xs font-bold text-[#e5534b] uppercase tracking-widest block">Step 2 of 3</span>
            <h4 class="text-2xl font-bold font-serif text-[#4a232f]">Tie the Rakhi</h4>
            <p class="text-sm text-gray-600 leading-relaxed">
              Select your chosen Rakhi and drag it to the designated area on the wrist to perform the virtual ceremony.
            </p>

            <div class="pt-2 border-t border-gray-100">
              <span class="text-xs font-bold text-gray-500 uppercase tracking-wide block mb-2">Selected Thread</span>
              <div class="w-24 h-24 rounded-xl border border-gray-200 bg-[#fcf6f0] p-2 flex items-center justify-center relative shadow-inner">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBrBgMcQPiJpH7ajGLFwJGVwRa8i1Cni6zU2PfdkIGO1Z52qnUapLQhEUB8IKS0u8LsO0x805E_mFTMaNxfNXiQGF5D9iEMg5ZFASInorsWjA8pMK8Lnt0I8jabUevnMHQAovnjP55h75mbDpmHRXuv5Mop7xu2TuWvXI-USFO8NdpfjFRy1d44pA0WFRFxbFRpYMqN_FHoNxjNn32vmJcW31a_RWHGrmth_-XXvYRw6uGgK-SS_5sT" alt="Selected Rakhi" class="w-full h-full object-contain">
                <div class="absolute top-1.5 right-1.5 w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center text-[10px] font-bold">✓</div>
              </div>
            </div>
          </div>

          <div class="bg-amber-50/80 border border-amber-200/80 rounded-2xl p-5 space-y-2 relative overflow-hidden shadow-sm">
            <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
              <span class="material-symbols-outlined text-amber-600">local_florist</span>
              <span>The Vow of Protection</span>
            </div>
            <p class="text-xs text-amber-900/80 leading-relaxed">
              By tying this sacred thread, you invoke the timeless promise of love, duty, and lifelong protection. The Rakhi symbolizes an unbreakable bond between siblings, transcending physical distance.
            </p>
          </div>
        </div>

      </div>

      <!-- STEP 3: STITCH CELEBRATION (FINAL_TIE_RAKHI_DESKTOP - 2) -->
      <div id="rakhiStep3" class="hidden space-y-6 text-center py-6 max-w-xl mx-auto">
        <div class="w-24 h-24 rounded-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black mx-auto flex items-center justify-center text-5xl shadow-2xl animate-bounce">
          ✨
        </div>
        <div class="space-y-2">
          <h4 class="text-3xl font-bold font-serif text-[#4a232f]">Rakhi Tied Successfully! 🎉</h4>
          <p class="text-sm text-gray-600 max-w-sm mx-auto leading-relaxed">
            The bond of protection has been renewed. A beautiful tradition continues.
          </p>
        </div>
        <button type="button" onclick="navigateFestiveStep(1)" class="py-3.5 px-10 bg-gradient-to-r from-[#d32f2f] to-[#f57c00] text-white font-bold text-sm uppercase tracking-wider rounded-full shadow-xl hover:scale-105 transition-all cursor-pointer">
          See Your Shagun 🎁
        </button>
      </div>

      <!-- STEP 4: SHAGUN ENVELOPE (FINAL_TIE_RAKHI_DESKTOP - 4) -->
      <div id="rakhiStep4" class="hidden space-y-5 text-center max-w-xl mx-auto">
        <div class="bg-white border border-[#e8d5c4] rounded-3xl p-6 space-y-4 shadow-lg">
          <span class="text-4xl block">✉️</span>
          <h4 class="text-xl font-bold font-serif text-[#4a232f]">Shagun Message &amp; Voucher</h4>
          <p class="text-sm text-[#5c3844] italic leading-relaxed">
            "<?= htmlspecialchars(mb_strimwidth($loveNoteText ?: "Wishing you a Happy Raksha Bandhan full of joy!", 0, 150, "...")) ?>"
          </p>
          <div class="p-4 bg-[#232f3e] text-white rounded-2xl text-xs space-y-2 shadow-inner">
            <span class="text-[#febd69] font-bold block text-sm">Amazon Gift Voucher Allocated</span>
            <span class="font-mono text-base text-[#febd69] font-black block">
              <?= $isUnlocked ? htmlspecialchars($voucherCode ?: 'AMZN-RAKHI-2026-X9Y') : '🔒 Code Unlocks on Rakhi Day' ?>
            </span>
          </div>
        </div>
      </div>

      <!-- STEP 5: CERTIFICATE DOWNLOAD (FINAL_TIE_RAKHI_DESKTOP - 5) -->
      <div id="rakhiStep5" class="hidden space-y-5 text-center max-w-xl mx-auto">
        <div class="bg-white border-2 border-[#d4af37] rounded-3xl p-6 space-y-4 shadow-lg">
          <span class="text-4xl block">📜</span>
          <h4 class="text-xl font-bold font-serif text-[#4a232f]">There's more to celebrate!</h4>
          <p class="text-xs text-[#7a5c68]">Your official certificate &amp; keepsake storybook are ready to download!</p>
          
          <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="button" onclick="downloadWallKeepsakePoster()" class="flex-1 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-bold text-xs uppercase rounded-full shadow cursor-pointer hover:scale-105 transition-all">
              🖼️ Wall Poster (300 DPI)
            </button>
            <button type="button" onclick="downloadSiblingPhotobookPDF()" class="flex-1 py-3 bg-emerald-500 text-white font-bold text-xs uppercase rounded-full shadow cursor-pointer hover:scale-105 transition-all">
              📖 Keepsake Book (PDF)
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Controls -->
    <div class="px-6 py-4 bg-[#f4e5d8]/80 border-t border-[#e8d5c4] flex items-center justify-between shrink-0">
      <button type="button" id="festiveModalBackBtn" onclick="navigateFestiveStep(-1)" class="px-5 py-2.5 bg-white text-[#4a232f] font-bold text-xs rounded-full hover:bg-gray-100 transition-all cursor-pointer invisible shadow-sm">
        ← Back
      </button>

      <button type="button" id="festiveModalNextBtn" onclick="navigateFestiveStep(1)" class="px-8 py-2.5 bg-gradient-to-r from-[#d32f2f] to-[#f57c00] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-md hover:opacity-90 transition-all cursor-pointer">
        NEXT ➔
      </button>
    </div>

  </div>
</div>

<script>
let currentFestiveStep = 1;
let selectedRakhiOptionId = 1;

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
}

function navigateFestiveStep(dir) {
  const nextStep = currentFestiveStep + dir;
  if (nextStep < 1 || nextStep > 5) return;

  const currentEl = document.getElementById(`rakhiStep${currentFestiveStep}`);
  if (currentEl) {
    currentEl.classList.add('hidden');
    if (currentFestiveStep === 2) {
      currentEl.classList.remove('flex');
    }
  }

  currentFestiveStep = nextStep;

  const nextEl = document.getElementById(`rakhiStep${currentFestiveStep}`);
  if (nextEl) {
    nextEl.classList.remove('hidden');
    if (currentFestiveStep === 2) {
      nextEl.classList.add('flex');
    }
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
    } else {
      nextBtn.innerText = 'NEXT ➔';
      nextBtn.onclick = function() { navigateFestiveStep(1); };
    }
  }

  if (currentFestiveStep === 2) {
    initRakhiDragLogic();
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
    initialX = currentX;
    initialY = currentY;
    active = false;

    // Check proximity to target zone
    if (dragEl && targetEl) {
      const dragRect = dragEl.getBoundingClientRect();
      const targetRect = targetEl.getBoundingClientRect();
      const dist = Math.hypot(
        (dragRect.left + dragRect.width/2) - (targetRect.left + targetRect.width/2),
        (dragRect.top + dragRect.height/2) - (targetRect.top + targetRect.height/2)
      );
      if (dist < 90) {
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
</script>
