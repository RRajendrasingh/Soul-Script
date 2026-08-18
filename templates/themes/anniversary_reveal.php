<?php
// templates/themes/anniversary_reveal.php

// Prepare variables that were previously in JS
$tf = $content['template_fields'] ?? [];
$media = $content['media'] ?? [];
$letters = $content['letters'] ?? [];
$tokens = $content['tokens'] ?? [];

$partnerName = htmlspecialchars($content['partner_name'] ?? 'Partner');
$buyerName = htmlspecialchars($content['buyer_name'] ?? 'Someone Special');
$taglineQuote = htmlspecialchars($content['tagline_quote'] ?? 'Safar Khubsurat h manjil se bhi 🌹');
$loveNoteText = htmlspecialchars($content['love_note_text'] ?? 'Happy Anniversary my love!');
$startDateStr = htmlspecialchars($tf['relationship_start_date'] ?? date('Y-m-d'));

$pInitial = strtoupper(substr($content['partner_name'] ?? 'P', 0, 1));
$cleanReceiverPhoto = !empty($content['receiver_photo']) ? resolveMediaUrl($content['receiver_photo']) : '';

if ($cleanReceiverPhoto) {
    $photoAvatarHtml = '<img id="receiverPhotoImg" src="'.htmlspecialchars($cleanReceiverPhoto).'" onerror="this.onerror=null; this.parentElement.innerHTML=\'<div class=\\\'w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner\\\'>'.$pInitial.'</div>\';" alt="'.$partnerName.'" class="w-full h-full rounded-full object-cover border-2 border-[#151215]">';
} else {
    $photoAvatarHtml = '<div id="receiverPhotoImg" class="w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner">'.$pInitial.'</div>';
}
?>

<!-- Hero Header & Custom Quote Section -->
<section class="relative pt-20 pb-8 px-4 text-center z-10">
  <div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Circular Gift Receiver Avatar Frame -->
    <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto group mb-2">
      <div class="w-full h-full rounded-full p-1 bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] shadow-[0_0_30px_rgba(234,195,74,0.4)] transition-transform duration-300 group-hover:scale-105">
        <?= $photoAvatarHtml ?>
      </div>
      <?php if (!empty($isEditMode)): ?>
        <button onclick="triggerReceiverPhotoUpload()" class="absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center text-white text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer border-2 border-[#eac34a]">
          <i data-lucide="camera" class="w-4 h-4 text-[#eac34a] mb-0.5"></i>
          <span>Change Photo</span>
        </button>
      <?php endif; ?>
    </div>

    <!-- Floating Romantic Quote Banner -->
    <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#3b1e3b]/80 border border-[#e4b9df]/40 text-[#eac34a] text-xs font-bold shadow-lg backdrop-blur-md">
      <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
      <span class="font-serif italic text-sm tracking-wide">"<?= $taglineQuote ?>"</span>
    </div>

    <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3] tracking-tight leading-tight">
      <?= $partnerName ?>, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#e4b9df]">Forever Cherished</span>
    </h1>

    <p class="text-xs sm:text-sm text-[#d0c3cb] max-w-md mx-auto italic font-serif leading-relaxed">
      "You are my today, my tomorrow, and all of my beautiful memories in between."
    </p>
  </div>
</section>

<!-- SECTION 1: LIVE COUNTER -->
<section class="max-w-2xl mx-auto px-4 py-6 relative z-10">
  <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 shadow-2xl text-center space-y-3">
    <span class="text-xs font-bold uppercase tracking-widest text-[#eac34a] flex items-center justify-center gap-1.5">
      <i data-lucide="clock" class="w-4 h-4 text-[#eac34a]"></i>
      <span>Counting Every Second Together</span>
    </span>

    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 pt-2">
      <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntY">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Years</span></div>
      <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntM">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Months</span></div>
      <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntD">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Days</span></div>
      <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntH">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Hours</span></div>
      <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntMin">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Mins</span></div>
      <div class="bg-[#151215] p-3 rounded-2xl border border-[#4d444b]"><span class="text-2xl font-bold font-serif text-[#eac34a] block" id="cntSec">0</span><span class="text-[10px] uppercase text-[#d0c3cb]">Secs</span></div>
    </div>
  </div>
</section>

<!-- SECTION 2: LOVE NOTE CENTERPIECE -->
<section class="max-w-3xl mx-auto px-4 py-6 relative z-10">
  <div class="bg-[#221f21] p-8 sm:p-12 rounded-3xl border border-[#eac34a]/40 shadow-2xl text-center space-y-4">
    <i data-lucide="feather" class="w-8 h-8 text-[#eac34a] mx-auto"></i>
    <p class="font-serif text-lg sm:text-xl italic text-[#e8e0e3] leading-relaxed">
      "<?= $loveNoteText ?>"
    </p>
    <p class="font-handwriting text-3xl text-[#eac34a] pt-4">— Forever yours, <?= $buyerName ?></p>
  </div>
</section>

<!-- SECTION 3: OUR PRECIOUS MILESTONES TIMELINE -->
<section class="max-w-3xl mx-auto px-4 py-8 relative z-10">
  <div class="text-center space-y-2 mb-12">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">OUR LOVE ROAD</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Our Precious Milestones</h2>
    <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
  </div>

  <div class="relative border-l-2 border-[#eac34a]/40 ml-4 sm:ml-32 space-y-8">
    <?php foreach ($tf['milestones'] ?? [] as $m): 
      $dateObj = strtotime($m['date'] ?? $m['milestone_date'] ?? '2022-08-15');
      $formattedDate = date('d M Y', $dateObj);
    ?>
      <div class="relative pl-6 sm:pl-8 group">
        <div class="absolute -left-[9px] top-2 w-4 h-4 rounded-full bg-[#eac34a] border-4 border-[#151215] shadow-md group-hover:scale-125 transition-transform"></div>
        <div class="sm:absolute sm:-left-32 sm:top-1 text-xs font-bold text-[#eac34a] mb-1 sm:mb-0 sm:w-24 sm:text-right font-mono tracking-wider">
          <?= $formattedDate ?>
        </div>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] hover:border-[#eac34a]/60 shadow-xl transition-all space-y-1.5">
          <h3 class="text-base font-bold font-serif text-[#e8e0e3]"><?= htmlspecialchars($m['title'] ?? '') ?></h3>
          <p class="text-xs text-[#d0c3cb] leading-relaxed font-sans"><?= htmlspecialchars($m['description'] ?? '') ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SECTION 4: PHOTO SCRAPBOOK GALLERY -->
<section class="max-w-5xl mx-auto px-4 py-12 relative z-10 space-y-8">
  <div class="text-center space-y-2 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">PHOTO MEMORIES</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Our Photo Scrapbook</h2>
    <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
  </div>

  <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
    <?php foreach ($media as $m): 
      $imgUrl = htmlspecialchars(resolveMediaUrl($m['file_path'] ?? ''));
      $capText = htmlspecialchars($m['caption'] ?? 'Sweet Moments');
    ?>
      <div onclick="openLightbox('<?= $imgUrl ?>')" class="break-inside-avoid rounded-2xl overflow-hidden border border-[#4d444b] group relative cursor-pointer hover:border-[#eac34a]/70 transition-all bg-[#151215] shadow-xl">
        <img src="<?= $imgUrl ?>" onerror="this.onerror=null; this.style.background='#221f21'; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23221f21%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23eac34a%22 font-size=%2228%22%3E📷%3C/text%3E%3C/svg%3E'" class="w-full h-auto object-contain block group-hover:scale-[1.02] transition-transform duration-500">
        <div class="p-3 bg-[#221f21] border-t border-[#4d444b]/40 text-left">
          <span class="text-[11px] font-bold text-[#eac34a] block"><?= $capText ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SECTION 5: SEALED LETTERS -->
<section class="max-w-4xl mx-auto px-4 py-12 relative z-10 space-y-8">
  <div class="text-center space-y-2 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">ENCHANTED LETTER JAR</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Wax-Sealed Love Letters</h2>
    <p class="text-xs text-[#d0c3cb]">Tap any envelope below to break the wax seal and open the letter.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <?php 
    $displayLetters = count($letters) > 0 ? $letters : [
      ['title' => 'The First Magical Spark', 'category' => 'A Beautiful Beginning', 'content' => 'My Dearest... I often find myself thinking back to the very first moment our paths crossed.'],
      ['title' => 'Our Silent Sacred Promise', 'category' => 'A Heartfelt Oath', 'content' => 'Here is my little vow to you...']
    ];
    foreach ($displayLetters as $idx => $l): 
      $escTitle = addslashes($l['title'] ?? '');
      $escCategory = addslashes($l['category'] ?? 'Love Note');
      $escContent = addslashes(nl2br($l['content'] ?? ''));
    ?>
      <div onclick="openLetterModal('<?= htmlspecialchars($escTitle) ?>', '<?= htmlspecialchars($escCategory) ?>', '<?= htmlspecialchars($escContent) ?>')" class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 hover:border-[#eac34a] shadow-2xl space-y-4 cursor-pointer group transition-all">
        <div class="flex items-center justify-between">
          <span class="text-[10px] uppercase tracking-widest text-[#eac34a] font-bold bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#e4b9df]/20"><?= htmlspecialchars($l['category'] ?? 'LOVE LETTER') ?></span>
          <span class="text-xs text-rose-400 font-bold flex items-center gap-1">
            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
            <span>SEALED</span>
          </span>
        </div>
        <h3 class="text-xl font-bold font-serif text-[#e8e0e3] group-hover:text-[#eac34a] transition-colors"><?= htmlspecialchars($l['title'] ?? '') ?></h3>
        <p class="text-xs text-[#d0c3cb] line-clamp-3 leading-relaxed font-serif italic">"<?= htmlspecialchars($l['content'] ?? '') ?>"</p>
        <div class="pt-2 text-center">
          <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider group-hover:bg-[#ffe088] shadow-md transition-all">
            <span>Break Wax Seal &amp; Read</span>
            <i data-lucide="mail-open" class="w-3.5 h-3.5"></i>
          </span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SECTION 6: LOVE TOKENS & REDEEMABLE COUPONS -->
<section class="max-w-4xl mx-auto px-4 py-12 relative z-10 space-y-8">
  <div class="text-center space-y-2 mb-8">
    <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">REDEEMABLE COUPONS</span>
    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Romantic Love Tokens</h2>
    <p class="text-xs text-[#d0c3cb]">Tap any coupon below anytime to redeem it with <?= $buyerName ?>.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <?php 
    $displayTokens = count($tokens) > 0 ? $tokens : [
      ['title' => '1 Free Warm Hug', 'badge' => 'Hug', 'description' => 'Redeemable anytime.'],
      ['title' => 'Late Night Ice Cream', 'badge' => 'Treat', 'description' => 'Midnight drive.'],
      ['title' => 'Movie Night Choice', 'badge' => 'Movie', 'description' => 'You pick the movie!']
    ];
    foreach ($displayTokens as $idx => $t): 
    ?>
      <div id="tokenCard-<?= $idx ?>" class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 shadow-2xl text-center space-y-4 relative overflow-hidden">
        <span class="text-[10px] uppercase font-bold text-[#eac34a] bg-[#3b1e3b] px-3 py-1 rounded-full border border-[#e4b9df]/20"><?= htmlspecialchars($t['badge'] ?? 'COUPON') ?></span>
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]"><?= htmlspecialchars($t['title'] ?? '') ?></h3>
        <p class="text-xs text-[#d0c3cb] leading-relaxed"><?= htmlspecialchars($t['description'] ?? '') ?></p>
        <button onclick="redeemToken(<?= $idx ?>)" id="redeemBtn-<?= $idx ?>" class="w-full py-2.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5">
          <i data-lucide="ticket" class="w-4 h-4"></i>
          <span>Redeem Coupon ♥</span>
        </button>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Footer Bar -->
<footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
  <p class="text-xs text-[#d0c3cb]">Made with endless love by <strong class="text-[#eac34a]"><?= $buyerName ?></strong> for <strong class="text-[#eac34a]"><?= $partnerName ?></strong></p>
  <div class="flex items-center justify-center gap-3">
    <button onclick="relockGiftSession()" type="button" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer">
      <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
      <span>Lock Gift Page</span>
    </button>
  </div>
</footer>

<script>
  if (typeof startLiveCounter === 'function') {
    startLiveCounter(new Date('<?= $startDateStr ?>'));
  }
</script>
