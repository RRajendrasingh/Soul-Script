<?php
/**
 * Component: 100% Google Stitch Design Matching Full-Stage "Virtual Rakhi Ceremony" Modal
 * Exactly matches original Stitch files:
 * FINAL_TIE_RAKHI_DESKTOP - 1 (Prepare Thali / Wrist Drag Stage)
 * FINAL_TIE_RAKHI_DESKTOP - 2 (Rakhi Tied Successfully & See Your Shagun)
 * FINAL_TIE_RAKHI_DESKTOP - 3 (Royal Shagun Lifafa - Tap to Open Envelope)
 * FINAL_TIE_RAKHI_DESKTOP - 4 (Two-Column Letter Quote & Amazon Cash Voucher Countdown)
 * FINAL_TIE_RAKHI_DESKTOP - 5 (There's More to Celebrate / Downloads & Gift Gallery)
 */
?>

<div id="festiveRakhiModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 bg-black/80 backdrop-blur-md transition-opacity duration-300">
  <div id="festiveRakhiModal" class="relative w-full max-w-4xl bg-[#fcf6f0] border-2 border-[#d4af37]/60 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
    
    <!-- Modal Header Bar -->
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
            <img id="wristBgImage" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAeUqO2t1o0sPZLFq_QHL05QMQiy-hGG27aSMXTMcJgF9UtH9PydzEAcoQvjVf7j6EQ8qN0baAB3AXk-wCpKqT_rnYHR4QgQUZOoxUYqf1nNsrNt5FXSFyBjmkgXyHm5ee7FqIKvYsY2bt4tb8y3OjjRIj82i5qQgn_17oeC7dZVvSlckOUFoW_wNPtbKmov8ta0VlxxyeJeIB507DxsErD7CVlz90EvF3xdO06rwHv_9dFeiwAFE8i" alt="Wrist" class="w-full h-full object-cover select-none pointer-events-none">
            
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

      <!-- STEP 3: STITCH ROYAL SHAGUN LIFAFA - TAP TO OPEN ENVELOPE (FINAL_TIE_RAKHI_DESKTOP - 3) -->
      <div id="rakhiStep3" class="hidden space-y-6 text-center py-4 max-w-md mx-auto">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#f4e5d8] border border-[#d4af37]/40 text-[#934b00] shadow-sm">
          <span class="material-symbols-outlined text-base">redeem</span>
          <span class="text-xs font-bold uppercase tracking-widest">Royal Shagun Lifafa</span>
        </div>

        <!-- Envelope Icon Container -->
        <div class="relative w-40 h-40 mx-auto flex items-center justify-center cursor-pointer group" onclick="navigateFestiveStep(1)">
          <div class="absolute inset-0 bg-[#e57800]/20 rounded-full blur-2xl group-hover:bg-[#e57800]/40 transition-colors"></div>
          <svg class="w-32 h-32 text-[#d32f2f] relative z-10 transform transition-transform group-hover:scale-105 drop-shadow-xl" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM20 18H4V8L12 13L20 8V18Z"></path>
            <path class="text-[#d4af37]" d="M12 15C11.8 15 11.6 14.9 11.4 14.8C9.5 13.1 8 11.8 8 10.2C8 9 9 8 10.2 8C10.9 8 11.5 8.3 12 8.8C12.5 8.3 13.1 8 13.8 8C15 8 16 9 16 10.2C16 11.8 14.5 13.1 12.6 14.8C12.4 14.9 12.2 15 12 15Z"></path>
          </svg>
        </div>

        <div class="space-y-2">
          <h4 class="text-3xl font-bold font-serif text-[#4a232f]">Tap to Open Envelope</h4>
          <p class="text-xs text-[#7a5c68]">Contains personal note &amp; gift voucher code</p>
        </div>

        <button type="button" onclick="navigateFestiveStep(1)" class="w-full py-3.5 px-8 bg-gradient-to-r from-[#934b00] to-[#e57800] text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-lg hover:shadow-xl transition-all cursor-pointer">
          OPEN NOW ➔
        </button>
      </div>

      <!-- STEP 4: STITCH TWO-COLUMN LETTER & AMZON CASH VOUCHER (FINAL_TIE_RAKHI_DESKTOP - 4) -->
      <div id="rakhiStep4" class="hidden flex flex-col md:flex-row bg-white rounded-3xl overflow-hidden shadow-xl border border-[#e8d5c4] max-w-3xl mx-auto">
        <!-- Left Column: Shagun Letter Quote -->
        <div class="flex-1 p-6 sm:p-8 bg-white border-b md:border-b-0 md:border-r border-[#e8d5c4] flex flex-col justify-center relative">
          <span class="material-symbols-outlined text-4xl text-[#d4af37] mb-3 opacity-60">format_quote</span>
          <p class="text-lg sm:text-xl text-[#4a232f] italic font-serif leading-relaxed">
            "<?= htmlspecialchars($loveNoteText ?: "mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🧧") ?>"
          </p>
          <div class="mt-4 flex justify-end">
            <span class="text-xs font-bold text-[#934b00] uppercase tracking-wider">— From <?= htmlspecialchars($senderName ?: 'Brother') ?></span>
          </div>
        </div>

        <!-- Right Column: Surprise Amazon Cash Voucher Countdown -->
        <div class="flex-1 p-6 sm:p-8 bg-[#fcf6f0] flex flex-col items-center text-center justify-center space-y-4">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#ffdcc5] text-[#934b00] text-[11px] font-bold uppercase tracking-wider">
            <span>🎁</span>
            <span>Surprise Amazon Cash Voucher</span>
          </div>

          <h4 class="text-xl sm:text-2xl font-bold font-serif text-[#4a232f] leading-tight">
            Your Secret Rakhi Cash Voucher Unlocks Soon! ⏳
          </h4>

          <p class="text-xs text-[#7a5c68]">
            Your Brother has hidden a surprise Amazon Gift Voucher inside this card! It unlocks automatically on the auspicious day.
          </p>

          <div class="w-full bg-white rounded-2xl border border-[#e8d5c4] p-4 flex items-center justify-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-full bg-[#ffdcc5] text-[#934b00] flex items-center justify-center font-bold">
              ⏰
            </div>
            <div class="text-left">
              <span class="text-[10px] font-bold text-gray-500 uppercase block tracking-wider">Unlocks on</span>
              <span class="text-sm font-bold text-[#934b00] block">28 August 2026</span>
              <span class="text-[11px] text-gray-600 block">12:00 PM IST</span>
            </div>
          </div>
        </div>
      </div>

      <!-- STEP 5: STITCH THERE'S MORE TO CELEBRATE (FINAL_TIE_RAKHI_DESKTOP - 5) -->
      <div id="rakhiStep5" class="hidden space-y-6 text-center max-w-2xl mx-auto py-4">
        <div class="w-24 h-24 mx-auto rounded-full bg-[#ffdcc5] text-[#934b00] flex items-center justify-center text-4xl shadow-xl relative">
          🎁
          <div class="absolute -top-1 -right-1 w-8 h-8 bg-[#0061a5] text-white rounded-full flex items-center justify-center text-sm font-bold shadow-md">★</div>
        </div>

        <div class="space-y-2">
          <h4 class="text-3xl font-bold font-serif text-[#4a232f]">There's more to celebrate!</h4>
          <p class="text-sm text-[#7a5c68] max-w-md mx-auto leading-relaxed">
            The festivities continue. Download your official 300 DPI Certificate &amp; Keepsake Photobook or discover our curated gift gallery!
          </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center pt-2">
          <button type="button" onclick="downloadWallKeepsakePoster()" class="py-3 px-6 bg-[#d4af37] text-[#241a00] font-bold text-xs uppercase rounded-full shadow cursor-pointer hover:scale-105 transition-all">
            🖼️ Wall Poster (300 DPI)
          </button>
          <button type="button" onclick="downloadSiblingPhotobookPDF()" class="py-3 px-6 bg-[#10b981] text-white font-bold text-xs uppercase rounded-full shadow cursor-pointer hover:scale-105 transition-all">
            📖 Keepsake Book (PDF)
          </button>
          <button type="button" onclick="closeFestiveRakhiModal()" class="py-3 px-6 bg-[#934b00] text-white font-bold text-xs uppercase rounded-full shadow cursor-pointer hover:scale-105 transition-all">
            Explore Gift Gallery ➔
          </button>
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
    if (currentFestiveStep === 2 || currentFestiveStep === 4) {
      currentEl.classList.remove('flex');
    }
  }

  currentFestiveStep = nextStep;

  const nextEl = document.getElementById(`rakhiStep${currentFestiveStep}`);
  if (nextEl) {
    nextEl.classList.remove('hidden');
    if (currentFestiveStep === 2 || currentFestiveStep === 4) {
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
      nextBtn.innerText = 'OPEN NOW ➔';
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
