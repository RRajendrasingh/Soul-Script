<?php
/**
 * Component 4: Sibling Promises Grid (Festive Light Theme) - php-app mirror
 */

$defaultVows = [
  "Always Be Your Shield" => "I promise to stand by your side in every storm of life.",
  "Share Every Laughter" => "I promise to keep multiplying our silly jokes and cheerful giggles.",
  "Never Let You Feel Alone" => "No matter where life takes us, I am always just one call away.",
  "Support Your Dreams" => "I promise to encourage and applaud your every aspiration and goal.",
  "Protect Our Precious Bond" => "I promise to cherish and protect our childhood memories forever."
];

$displayVows = [];
if (!empty($promisesList) && is_array($promisesList)) {
    foreach ($promisesList as $idx => $vowText) {
        if (!empty(trim($vowText))) {
            $displayVows[] = trim($vowText);
        }
    }
}

if (empty($displayVows)) {
    $displayVows = array_values($defaultVows);
}
?>

<section id="siblingVowsSection" class="max-w-4xl mx-auto px-4 py-8 relative z-10 space-y-6">
  <div class="text-center space-y-2">
    <div class="inline-flex items-center gap-1.5 bg-[#e5534b]/10 border border-[#e5534b]/20 px-3 py-1 rounded-full text-[11px] font-bold text-[#e5534b] uppercase tracking-wider">
      <span>🤝 SACRED VOWS & PROMISES</span>
    </div>
    <h2 class="text-2xl sm:text-3xl font-bold font-serif text-[#4a232f]">Promises from My Heart 💖</h2>
    <p class="text-xs sm:text-sm text-[#7a5c68]">Five sacred vows that bind our sibling bond forever.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php 
    $icons = ['🛡️', '😂', '📞', '🌟', '💖'];
    foreach ($displayVows as $index => $promiseText): 
      $icon = $icons[$index % count($icons)];
    ?>
      <div class="bg-white border border-[#e8d5c4] rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-[#e5534b] transition-all space-y-3 flex flex-col justify-between group">
        <div class="space-y-2">
          <div class="w-10 h-10 rounded-xl bg-[#fcf6f0] border border-[#e8d5c4] text-xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <?= $icon ?>
          </div>
          <span class="text-[10px] font-bold text-[#e5534b] uppercase tracking-wider block">PROMISE #<?= $index + 1 ?></span>
          <p class="text-xs sm:text-sm font-bold text-[#4a232f] leading-relaxed">
            "<?= htmlspecialchars($promiseText) ?>"
          </p>
        </div>
        <div class="pt-2 border-t border-[#f4e5d8] text-[10px] text-[#8c6b1b] font-bold">
          Forever Bound &amp; Sealed ✨
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
