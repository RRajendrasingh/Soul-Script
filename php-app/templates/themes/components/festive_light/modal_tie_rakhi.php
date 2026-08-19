<?php
/**
 * Component: 5-Step Interactive "Tap to Tie Rakhi" Modal Dialog (Festive Light Theme) - php-app mirror
 */
?>

<div id="festiveRakhiModalContainer" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-300">
  <div id="festiveRakhiModal" class="relative w-full max-w-[440px] bg-[#fffdfa] border-2 border-[#e5534b]/40 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
    
    <!-- Modal Header -->
    <div class="px-5 py-4 bg-[#fcf6f0] border-b border-[#e8d5c4] flex items-center justify-between shrink-0">
      <div class="flex items-center gap-2">
        <span class="text-xl">🪔</span>
        <div>
          <h3 class="text-sm font-bold font-serif text-[#4a232f]">Virtual Rakhi Ceremony</h3>
          <span id="festiveModalStepBadge" class="text-[10px] font-bold text-[#e5534b] uppercase tracking-wider block">Step 1 of 5</span>
        </div>
      </div>

      <!-- Close Button (Cross ✕) -->
      <button type="button" onclick="closeFestiveRakhiModal()" class="w-8 h-8 rounded-full bg-[#f4e5d8] text-[#4a232f] hover:bg-[#e5534b] hover:text-white font-bold text-base flex items-center justify-center transition-colors cursor-pointer" title="Close Modal">
        ✕
      </button>
    </div>

    <!-- Modal Content Stage (Steps 1 to 5 Container) -->
    <div class="p-5 overflow-y-auto flex-1 space-y-4">

      <!-- STEP 1: CHOOSE RAKHI ON THALI -->
      <div id="rakhiStep1" class="space-y-4 text-center">
        <div class="w-44 h-44 mx-auto rounded-full bg-[#fcf6f0] border-4 border-[#d4af37]/40 p-2 shadow-inner relative overflow-hidden">
          <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2O0UUMotKODBIkqNEhea8j8pOpDOQrX4zypkSNf-d_qW1vbuowzyWKz2QFNt0Rr6gJC6hwhNPXD34RdszgjwyoOWu__tmKeep2ubQNCCe_y_0zuS0RLnSDHyMNWo7_DQUoJkkPYUQS1gfRvE4ZLXQCytMoYF2K0JhPccdolz7LFpQeRDJ7QrZ1qyKxscQT4SCnKcnkj0PB0JBhw4T_zK6rfr_HJrZE0pMb5JkavqvjRPfROe38YTU" alt="Puja Thali" class="w-full h-full object-cover rounded-full">
        </div>
        <div>
          <h4 class="text-base font-bold font-serif text-[#4a232f]">Prepare Sacred Thali</h4>
          <p class="text-xs text-[#7a5c68]">Select a special Rakhi thread for your brother to begin.</p>
        </div>

        <!-- 3 Rakhi Options Grid -->
        <div class="grid grid-cols-3 gap-3 pt-2">
          <button type="button" onclick="selectRakhiOption(1, this)" class="rakhi-opt-btn p-2 rounded-2xl border-2 border-[#e5534b] bg-[#fcf6f0] flex flex-col items-center gap-1 cursor-pointer transition-all">
            <span class="text-2xl">🏵️</span>
            <span class="text-[10px] font-bold text-[#4a232f]">Royal Kundan</span>
          </button>
          <button type="button" onclick="selectRakhiOption(2, this)" class="rakhi-opt-btn p-2 rounded-2xl border-2 border-transparent hover:border-[#e8d5c4] bg-[#fcf6f0] flex flex-col items-center gap-1 cursor-pointer transition-all">
            <span class="text-2xl">📿</span>
            <span class="text-[10px] font-bold text-[#4a232f]">Rudraksha</span>
          </button>
          <button type="button" onclick="selectRakhiOption(3, this)" class="rakhi-opt-btn p-2 rounded-2xl border-2 border-transparent hover:border-[#e8d5c4] bg-[#fcf6f0] flex flex-col items-center gap-1 cursor-pointer transition-all">
            <span class="text-2xl">✨</span>
            <span class="text-[10px] font-bold text-[#4a232f]">Silver Thread</span>
          </button>
        </div>
      </div>

      <!-- STEP 2: DRAG & DROP RAKHI TO WRIST -->
      <div id="rakhiStep2" class="hidden space-y-4 text-center">
        <div>
          <h4 class="text-base font-bold font-serif text-[#4a232f]">Tie Rakhi on Wrist</h4>
          <p class="text-xs text-[#7a5c68]">Drag the Rakhi thread upwards onto brother's wrist or tap below!</p>
        </div>

        <div id="rakhiInteractionArea" class="relative w-full aspect-square max-w-[280px] mx-auto rounded-3xl bg-[#fcf6f0] border-2 border-[#e8d5c4] overflow-hidden shadow-inner touch-none">
          <!-- Wrist Background Image -->
          <img id="wristBgImage" src="https://lh3.googleusercontent.com/aida/AP1WRLusJMjZjDNpL3tdMMUcxKwtBHZB1m0ulhgHVwD_ZG1RiPZHPkFWvQoviBGySth2IRJP7U8_ZRh173Q5rGPCoj-rQdxS98HK8HPLgOlTLKwzQAhDI0_xc9qhBja4ry7qP8YfM4XASkHMHYXXaPwtRdt4kXdoK3LZq_y_0LhAk3SPlD-kg9VH2_C68AFubCsBZD0A7RPNdjgemarba2C-3vuMw7lto7kP_MhlKWD5TQgZah-7MmGkrvn-oNw" alt="Wrist" class="w-full h-full object-cover select-none pointer-events-none">
          
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
        <div class="w-20 h-20 rounded-full bg-[#10b981]/10 border-2 border-[#10b981] text-[#10b981] mx-auto flex items-center justify-center text-4xl shadow-lg animate-bounce">
          ✓
        </div>
        <div class="space-y-1">
          <h4 class="text-xl font-bold font-serif text-[#4a232f]">Rakhi Tied Successfully! 🎉</h4>
          <p class="text-xs text-[#7a5c68]">The bond of eternal love and protection is sealed forever.</p>
        </div>
      </div>

      <!-- STEP 4: SHAGUN LETTER & VOUCHER REVEAL -->
      <div id="rakhiStep4" class="hidden space-y-4 text-center">
        <div class="bg-gradient-to-br from-[#fcf6f0] to-[#f4e5d8] border border-[#e8d5c4] rounded-2xl p-4 space-y-3">
          <span class="text-2xl block">✉️</span>
          <h4 class="text-base font-bold font-serif text-[#4a232f]">Shagun Message &amp; Voucher</h4>
          <p class="text-xs text-[#5c3844] italic">
            "<?= htmlspecialchars(mb_strimwidth($loveNoteText ?: "Wishing you a Happy Raksha Bandhan full of joy!", 0, 120, "...")) ?>"
          </p>
          <div class="p-3 bg-[#232f3e] text-white rounded-xl text-xs space-y-1">
            <span class="text-[#febd69] font-bold block">Amazon Gift Voucher Allocated</span>
            <span class="font-mono text-sm text-[#febd69] font-black">
              <?= $isUnlocked ? htmlspecialchars($voucherCode ?: 'AMZN-RAKHI-2026-X9Y') : '🔒 Code Unlocks on Rakhi Day' ?>
            </span>
          </div>
        </div>
      </div>

      <!-- STEP 5: OFFICIAL CERTIFICATE & KEEPSAKES -->
      <div id="rakhiStep5" class="hidden space-y-4 text-center">
        <div class="bg-gradient-to-br from-[#fffdfa] to-[#f9ede3] border-2 border-[#d4af37] rounded-2xl p-4 space-y-3">
          <span class="text-2xl block">📜</span>
          <h4 class="text-base font-bold font-serif text-[#4a232f]">Shahi Tamrapatra Certificate</h4>
          <p class="text-xs text-[#7a5c68]">Your official certificate of protection is ready to download &amp; share!</p>
          
          <div class="flex flex-col gap-2 pt-2">
            <button type="button" onclick="downloadWallKeepsakePoster()" class="w-full py-2.5 bg-[#d4af37] text-[#241a00] font-bold text-xs uppercase rounded-xl shadow">
              🖼️ Download Wall Poster (300 DPI)
            </button>
            <button type="button" onclick="downloadSiblingPhotobookPDF()" class="w-full py-2.5 bg-[#10b981] text-white font-bold text-xs uppercase rounded-xl shadow">
              📖 Download Keepsake Book (PDF)
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Controls -->
    <div class="px-5 py-3.5 bg-[#fcf6f0] border-t border-[#e8d5c4] flex items-center justify-between shrink-0">
      <button type="button" id="festiveModalBackBtn" onclick="navigateFestiveStep(-1)" class="px-4 py-2 bg-[#f4e5d8] text-[#4a232f] font-bold text-xs rounded-full hover:bg-[#e8d5c4] transition-all cursor-pointer invisible">
        ← Back
      </button>

      <button type="button" id="festiveModalNextBtn" onclick="navigateFestiveStep(1)" class="px-6 py-2 bg-[#e5534b] text-white font-bold text-xs uppercase rounded-full shadow hover:bg-[#c93d3d] transition-all cursor-pointer">
        Next ➔
      </button>
    </div>

  </div>
</div>
