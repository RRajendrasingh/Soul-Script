<?php
/**
 * Component 2: Dynamic Gift Reveal Card (Festive Light Theme)
 * Handles State 1 (Scratch Card) -> State 2 (Locked Timer) -> State 3 (Unlocked Voucher + Brand Suggestions)
 */

$isUnlocked = !empty($rakhiVoucherStatus['unlocked']);
$voucherAmount = !empty($rakhiVoucherStatus['allocated_amount']) ? intval($rakhiVoucherStatus['allocated_amount']) : 500;
$voucherCode = !empty($rakhiVoucherStatus['voucher_code']) ? $rakhiVoucherStatus['voucher_code'] : '';
$unlockDateFormatted = !empty($rakhiVoucherStatus['unlock_date_formatted']) ? $rakhiVoucherStatus['unlock_date_formatted'] : '28 August 2026, 12:00 PM IST';
$secondsRemaining = !empty($rakhiVoucherStatus['seconds_remaining']) ? intval($rakhiVoucherStatus['seconds_remaining']) : 0;
?>

<section id="giftRevealSection" class="max-w-4xl mx-auto px-4 py-8 relative z-10">
  <div class="bg-white border-2 border-[#e5534b]/30 rounded-3xl p-6 sm:p-8 shadow-[0_15px_40px_rgba(74,35,47,0.08)] relative overflow-hidden space-y-6">
    
    <!-- Section Title & Subtitle -->
    <div class="text-center space-y-1">
      <div class="inline-flex items-center gap-1.5 bg-[#e5534b]/10 border border-[#e5534b]/20 px-3 py-1 rounded-full text-[11px] font-bold text-[#e5534b] uppercase tracking-wider">
        <span>🎁 SHAGUN GIFT CARD & VOUCHER</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-bold font-serif text-[#4a232f]">Rakhi Special Surprise Gift 💳✨</h2>
      <p class="text-xs sm:text-sm text-[#7a5c68]">
        A special Raksha Bandhan gift allocated for <?= htmlspecialchars($partnerName) ?> by <?= htmlspecialchars($buyerName) ?>!
      </p>
    </div>

    <!-- STATE CONTAINER (Dynamically Toggled via JS / Server State) -->
    <div id="giftCardStateContainer" class="max-w-md mx-auto relative min-h-[220px]">

      <?php if ($isUnlocked): ?>
        <!-- ========================================== -->
        <!-- STATE 3: UNLOCKED VOUCHER CODE & COPY BUTTON -->
        <!-- ========================================== -->
        <div id="giftState3Unlocked" class="bg-gradient-to-br from-[#232f3e] to-[#131921] rounded-2xl p-6 text-white shadow-xl space-y-4 border border-[#febd69]/40 relative">
          <div class="flex items-center justify-between">
            <span class="text-xl font-bold text-[#febd69] tracking-wider">amazon.in</span>
            <span class="px-2.5 py-1 bg-[#febd69]/20 border border-[#febd69] text-[#febd69] font-black text-xs rounded-full">
              ₹<?= number_format($voucherAmount) ?> GIFT CARD
            </span>
          </div>

          <div class="space-y-1 py-2">
            <span class="text-[10px] text-gray-400 uppercase tracking-widest block font-bold">Voucher Claim Code</span>
            <div class="flex items-center gap-2">
              <input type="text" readonly id="voucherCodeInput" value="<?= htmlspecialchars($voucherCode ?: 'AMZN-RAKHI-2026-X9Y') ?>" class="w-full bg-[#1e2732] border border-gray-600 rounded-lg px-3 py-2 text-sm sm:text-base font-mono font-bold text-[#febd69] text-center tracking-widest focus:outline-none">
              <button type="button" onclick="copyVoucherCode()" class="px-4 py-2 bg-[#febd69] hover:bg-[#f3a847] text-[#111] font-extrabold text-xs uppercase rounded-lg shadow transition-all cursor-pointer shrink-0">
                Copy
              </button>
            </div>
          </div>

          <div class="text-[11px] text-gray-300 flex items-center justify-between pt-1 border-t border-gray-700">
            <span>Valid on Amazon.in Shopping & Pay</span>
            <span class="text-[#febd69] font-bold">Unlocked ✅</span>
          </div>
        </div>

      <?php else: ?>
        <!-- ======================================================== -->
        <!-- STATE 1 & STATE 2: SCRATCH CARD OR LOCKED COUNTDOWN TIMER -->
        <!-- ======================================================== -->
        
        <!-- Inner Card Layout -->
        <div class="bg-gradient-to-br from-[#232f3e] via-[#1a232e] to-[#131921] rounded-2xl p-6 text-white shadow-xl space-y-4 border border-[#febd69]/30 relative overflow-hidden">
          <div class="flex items-center justify-between">
            <span class="text-xl font-bold text-[#febd69] tracking-wider">amazon.in</span>
            <span class="px-2.5 py-1 bg-[#febd69]/20 border border-[#febd69] text-[#febd69] font-black text-xs rounded-full">
              ₹<?= number_format($voucherAmount) ?> GIFT CARD
            </span>
          </div>

          <!-- Blurred / Hidden Voucher Code Area -->
          <div class="space-y-1 py-2 relative">
            <span class="text-[10px] text-gray-400 uppercase tracking-widest block font-bold">Voucher Claim Code</span>
            <div class="relative bg-[#1e2732] border border-gray-600 rounded-lg p-3 text-center overflow-hidden">
              <span class="font-mono font-bold text-gray-400 text-sm blur-md select-none">XXXX-XXXX-XXXX-XXXX</span>
              <div class="absolute inset-0 bg-[#1e2732]/90 flex items-center justify-center gap-1.5 text-xs text-[#febd69] font-bold">
                <span>🔒 Locked Code</span>
              </div>
            </div>
          </div>

          <!-- Countdown Timer Display -->
          <div class="bg-[#19212c] rounded-xl p-3 text-center space-y-1 border border-gray-700">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Unlocks On Raksha Bandhan Day</span>
            <div id="giftCountdownTimer" class="font-mono font-extrabold text-sm sm:text-base text-[#febd69] tracking-widest">
              Calculating Time...
            </div>
            <span class="text-[9px] text-gray-400 block"><?= htmlspecialchars($unlockDateFormatted) ?></span>
          </div>
        </div>

        <!-- Interactive Scratch Overlay Layer (State 1 -> State 2 Transition) -->
        <div id="scratchCardOverlay" class="absolute inset-0 z-20 rounded-2xl overflow-hidden cursor-crosshair transition-opacity duration-500 shadow-2xl">
          <canvas id="scratchCanvas" class="w-full h-full block rounded-2xl"></canvas>
          <div id="scratchInstructionPill" class="absolute bottom-3 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-[#4a232f]/90 text-white rounded-full text-[11px] font-bold tracking-wider shadow-lg pointer-events-none flex items-center gap-1.5 animate-bounce">
            <span>✨ Scratch with finger or mouse</span>
          </div>
        </div>

      <?php endif; ?>

    </div>

    <!-- STATE 3 BRAND SUGGESTIONS GRID (Shows when unlocked or below gift card) -->
    <div id="brandSuggestionsGrid" class="pt-4 border-t border-[#e8d5c4] space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-[#4a232f] flex items-center gap-1.5">
          <span>🛍️ Recommended Ways to Spend Your Gift Voucher:</span>
        </h3>
        <span class="text-[10px] font-bold text-[#e5534b] uppercase">Amazon India Store</span>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php foreach ($rakhiAffiliateProducts as $prod): ?>
          <a href="<?= htmlspecialchars($prod['affiliate_url']) ?>" target="_blank" rel="noopener noreferrer" class="group bg-[#fcf6f0] border border-[#e8d5c4] rounded-2xl p-3 hover:border-[#e5534b] transition-all flex flex-col justify-between space-y-2">
            <div class="aspect-square rounded-xl overflow-hidden bg-white p-1">
              <img src="<?= htmlspecialchars($prod['image_url']) ?>" alt="<?= htmlspecialchars($prod['title']) ?>" class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
            </div>
            <div class="space-y-1">
              <span class="text-[9px] font-bold text-[#e5534b] uppercase block"><?= htmlspecialchars($prod['category'] ?? 'Gift') ?></span>
              <h4 class="text-[11px] font-bold text-[#4a232f] line-clamp-2 leading-snug group-hover:text-[#e5534b] transition-colors">
                <?= htmlspecialchars($prod['title']) ?>
              </h4>
              <span class="text-xs font-black text-[#4a232f] block"><?= htmlspecialchars($prod['price_text']) ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>
