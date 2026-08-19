<?php
/**
 * Component: 5-Step Interactive "Tap to Tie Rakhi" Modal Dialog (100% Stitch Design Matching)
 */
?>

<div id="festiveRakhiModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 bg-black/70 backdrop-blur-sm transition-opacity duration-300">
  <div id="festiveRakhiModal" class="relative w-full max-w-[460px] bg-[#fcf6f0] border-2 border-[#d4af37]/50 rounded-[32px] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
    
    <!-- Modal Header -->
    <div class="px-6 py-4 bg-[#f4e5d8]/60 border-b border-[#e8d5c4] flex items-center justify-between shrink-0">
      <div class="flex items-center gap-2.5">
        <span class="text-2xl">🪔</span>
        <div>
          <h3 class="text-base font-bold font-serif text-[#4a232f]">Virtual Rakhi Ceremony</h3>
          <span id="festiveModalStepBadge" class="text-[10px] font-bold text-[#e5534b] uppercase tracking-wider block">Step 1 of 5</span>
        </div>
      </div>

      <!-- Close Button (Cross ✕) -->
      <button type="button" onclick="closeFestiveRakhiModal()" class="w-8 h-8 rounded-full bg-white text-[#4a232f] hover:bg-[#e5534b] hover:text-white font-bold text-base flex items-center justify-center transition-colors shadow-sm cursor-pointer" title="Close Modal">
        ✕
      </button>
    </div>

    <!-- Modal Content Stage (Steps 1 to 5 Container) -->
    <div class="p-6 overflow-y-auto flex-1 space-y-5">

      <!-- STEP 1: CHOOSE RAKHI ON THALI -->
      <div id="rakhiStep1" class="space-y-5 text-center">
        <div class="w-48 h-48 mx-auto rounded-full bg-white border-4 border-[#d4af37]/50 p-2 shadow-xl relative overflow-hidden flex items-center justify-center">
          <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZfsICxK34oixmN1AZRizpBM2bZC5BAB_XYhQLhxKaZRKgNxEv8X9v3Z4lzEedQVni4JuXg6LECezawWUPThbfyUKDAnCX14tBlz_SHV5Z0nHTlrYpNX81aS2JbA1-fREPTFZBGfA4Oin9IzGHb5PZxUinsPuL6pU81_ZnpEIrbooze4l1aomWnjr8FWAmwYUcQR92cij0amxmT3sNwf3Uq4XO2ot9yJ_JaQvk6cQiDvzzRP2Mvcj0" alt="Puja Thali" class="w-full h-full object-cover rounded-full">
        </div>
        <div class="space-y-1">
          <h4 class="text-xl font-bold font-serif text-[#4a232f]">Prepare Sacred Thali</h4>
          <p class="text-xs text-[#7a5c68]">Select a special Rakhi thread for your brother to begin.</p>
        </div>

        <!-- 3 Rakhi Options Grid -->
        <div class="grid grid-cols-3 gap-3 pt-2">
          <button type="button" onclick="selectRakhiOption(1, this)" class="rakhi-opt-btn p-3 rounded-2xl border-2 border-[#e5534b] bg-white flex flex-col items-center gap-1.5 cursor-pointer shadow-sm hover:shadow-md transition-all">
            <span class="text-2xl">🏵️</span>
            <span class="text-[11px] font-bold text-[#4a232f]">Royal Kundan</span>
          </button>
          <button type="button" onclick="selectRakhiOption(2, this)" class="rakhi-opt-btn p-3 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-1.5 cursor-pointer shadow-sm hover:shadow-md transition-all">
            <span class="text-2xl">📿</span>
            <span class="text-[11px] font-bold text-[#4a232f]">Rudraksha</span>
          </button>
          <button type="button" onclick="selectRakhiOption(3, this)" class="rakhi-opt-btn p-3 rounded-2xl border-2 border-transparent hover:border-[#d4af37] bg-white flex flex-col items-center gap-1.5 cursor-pointer shadow-sm hover:shadow-md transition-all">
            <span class="text-2xl">✨</span>
            <span class="text-[11px] font-bold text-[#4a232f]">Silver Thread</span>
          </button>
        </div>
      </div>

      <!-- STEP 2: DRAG & DROP RAKHI TO WRIST -->
      <div id="rakhiStep2" class="hidden space-y-4 text-center">
        <div class="space-y-1">
          <h4 class="text-xl font-bold font-serif text-[#4a232f]">Tie Rakhi on Wrist</h4>
          <p class="text-xs text-[#7a5c68]">Drag the Rakhi thread upwards onto brother's wrist or tap below!</p>
        </div>

        <div id="rakhiInteractionArea" class="relative w-full aspect-square max-w-[280px] mx-auto rounded-3xl bg-white border-2 border-[#d4af37]/40 overflow-hidden shadow-xl touch-none">
          <!-- Wrist Background Image -->
          <img id="wristBgImage" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAeUqO2t1o0sPZLFq_QHL05QMQiy-hGG27aSMXTMcJgF9UtH9PydzEAcoQvjVf7j6EQ8qN0baAB3AXk-wCpKqT_rnYHR4QgQUZOoxUYqf1nNsrNt5FXSFyBjmkgXyHm5ee7FqIKvYsY2bt4tb8y3OjjRIj82i5qQgn_17oeC7dZVvSlckOUFoW_wNPtbKmov8ta0VlxxyeJeIB507DxsErD7CVlz90EvF3xdO06rwHv_9dFeiwAFE8i" alt="Wrist" class="w-full h-full object-cover select-none pointer-events-none">
          
          <!-- Target Zone -->
          <div id="rakhiTargetZone" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-16 border-2 border-dashed border-[#e5534b]/60 rounded-full opacity-60 pointer-events-none animate-pulse"></div>

          <!-- Draggable Rakhi -->
          <div id="draggableRakhi" class="absolute bottom-4 left-1/2 -translate-x-1/2 w-20 h-20 cursor-grab active:cursor-grabbing z-20 touch-none" style="touch-action: none;">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBrBgMcQPiJpH7ajGLFwJGVwRa8i1Cni6zU2PfdkIGO1Z52qnUapLQhEUB8IKS0u8LsO0x805E_mFTMaNxfNXiQGF5D9iEMg5ZFASInorsWjA8pMK8Lnt0I8jabUevnMHQAovnjP55h75mbDpmHRXuv5Mop7xu2TuWvXI-USFO8NdpfjFRy1d44pA0WFRFxbFRpYMqN_FHoNxjNn32vmJcW31a_RWHGrmth_-XXvYRw6uGgK-SS_5sT" alt="Rakhi" class="w-full h-full object-contain filter drop-shadow-lg select-none pointer-events-none">
          </div>
        </div>

        <button type="button" onclick="triggerRakhiTiedSuccess()" class="text-xs font-bold text-[#e5534b] hover:underline cursor-pointer">
          👉 Tap here to instantly tie Rakhi
        </button>
      </div>

      <!-- STEP 3: SUCCESS & CONFETTI CELEBRATION -->
      <div id="rakhiStep3" class="hidden space-y-4 text-center py-4">
        <div class="w-20 h-20 rounded-full bg-emerald-50 border-2 border-emerald-500 text-emerald-600 mx-auto flex items-center justify-center text-4xl shadow-xl animate-bounce">
          ✓
        </div>
        <div class="space-y-1">
          <h4 class="text-2xl font-bold font-serif text-[#4a232f]">Rakhi Tied Successfully! 🎉</h4>
          <p class="text-xs text-[#7a5c68]">The bond of eternal love and protection is sealed forever.</p>
        </div>
      </div>

      <!-- STEP 4: SHAGUN LETTER & VOUCHER REVEAL -->
      <div id="rakhiStep4" class="hidden space-y-4 text-center">
        <div class="bg-white border border-[#e8d5c4] rounded-2xl p-5 space-y-3 shadow-md">
          <span class="text-3xl block">✉️</span>
          <h4 class="text-lg font-bold font-serif text-[#4a232f]">Shagun Message &amp; Voucher</h4>
          <p class="text-xs text-[#5c3844] italic leading-relaxed">
            "<?= htmlspecialchars(mb_strimwidth($loveNoteText ?: "Wishing you a Happy Raksha Bandhan full of joy!", 0, 120, "...")) ?>"
          </p>
          <div class="p-3.5 bg-[#232f3e] text-white rounded-xl text-xs space-y-1 shadow-inner">
            <span class="text-[#febd69] font-bold block">Amazon Gift Voucher Allocated</span>
            <span class="font-mono text-sm text-[#febd69] font-black">
              <?= $isUnlocked ? htmlspecialchars($voucherCode ?: 'AMZN-RAKHI-2026-X9Y') : '🔒 Code Unlocks on Rakhi Day' ?>
            </span>
          </div>
        </div>
      </div>

      <!-- STEP 5: OFFICIAL CERTIFICATE & KEEPSAKES -->
      <div id="rakhiStep5" class="hidden space-y-4 text-center">
        <div class="bg-white border-2 border-[#d4af37] rounded-2xl p-5 space-y-3 shadow-md">
          <span class="text-3xl block">📜</span>
          <h4 class="text-lg font-bold font-serif text-[#4a232f]">Shahi Tamrapatra Certificate</h4>
          <p class="text-xs text-[#7a5c68]">Your official certificate of protection is ready to download &amp; share!</p>
          
          <div class="flex flex-col gap-2.5 pt-2">
            <button type="button" onclick="downloadWallKeepsakePoster()" class="w-full py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-bold text-xs uppercase rounded-xl shadow cursor-pointer">
              🖼️ Download Wall Poster (300 DPI)
            </button>
            <button type="button" onclick="downloadSiblingPhotobookPDF()" class="w-full py-3 bg-emerald-500 text-white font-bold text-xs uppercase rounded-xl shadow cursor-pointer">
              📖 Download Keepsake Book (PDF)
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Controls -->
    <div class="px-6 py-4 bg-[#f4e5d8]/60 border-t border-[#e8d5c4] flex items-center justify-between shrink-0">
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

  document.getElementById(`rakhiStep${currentFestiveStep}`)?.classList.add('hidden');
  currentFestiveStep = nextStep;
  document.getElementById(`rakhiStep${currentFestiveStep}`)?.classList.remove('hidden');

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
      if (dist < 80) {
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
