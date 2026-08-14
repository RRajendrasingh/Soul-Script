<?php
// templates/themes/perfect_proposal.php

$tf = $content['template_fields'] ?? [];
$media = $content['media'] ?? [];
$existingResp = $data['proposal_response'] ?? null;

$partnerName = htmlspecialchars($content['partner_name'] ?? 'Partner');
$buyerName = htmlspecialchars($content['buyer_name'] ?? 'Someone Special');
$taglineQuote = htmlspecialchars($content['tagline_quote'] ?? 'Safar Khubsurat h manjil se bhi 🌹');
$loveLetterText = htmlspecialchars($tf['love_letter_text'] ?? $content['love_note_text'] ?? '');

$pInitial = strtoupper(substr($content['partner_name'] ?? 'P', 0, 1));
$cleanReceiverPhoto = !empty($content['receiver_photo']) ? resolveMediaUrl($content['receiver_photo']) : '';

if ($cleanReceiverPhoto) {
    $photoAvatarHtml = '<img id="receiverPhotoImg" src="'.htmlspecialchars($cleanReceiverPhoto).'" onerror="this.onerror=null; this.parentElement.innerHTML=\'<div class=\\\'w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner\\\'>'.$pInitial.'</div>\';" alt="'.$partnerName.'" class="w-full h-full rounded-full object-cover border-2 border-[#151215]">';
} else {
    $photoAvatarHtml = '<div id="receiverPhotoImg" class="w-full h-full rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#151215] flex items-center justify-center font-bold text-3xl sm:text-4xl font-serif shadow-inner">'.$pInitial.'</div>';
}
?>

<section class="relative pt-20 pb-16 px-4 text-center z-10">
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
    <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3]">
      Will You Marry Me, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#eac34a] via-[#ffd700] to-[#e4b9df]"><?= $partnerName ?></span>?
    </h1>
  </div>
</section>

<!-- Love Letter Centerpiece -->
<section class="max-w-3xl mx-auto px-4 py-8 relative z-10">
  <div class="bg-[#221f21] p-8 sm:p-12 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6">
    <p class="font-serif text-base sm:text-lg text-[#e8e0e3] leading-relaxed whitespace-pre-line">
      <?= $loveLetterText ?>
    </p>
    <p class="font-handwriting text-3xl text-[#eac34a] text-right pt-4">— Forever yours, <?= $buyerName ?></p>
  </div>
</section>

<!-- Photo Gallery -->
<section class="max-w-4xl mx-auto px-4 py-12 relative z-10 space-y-8">
  <h2 class="text-2xl font-bold font-serif text-center text-[#e8e0e3]">Captured Memories</h2>
  <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
    <?php foreach ($media as $m): 
      $imgUrl = htmlspecialchars(resolveMediaUrl($m['file_path'] ?? ''));
      $capText = htmlspecialchars($m['caption'] ?? 'Precious Moments');
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

<!-- Response Capture Buttons -->
<section class="max-w-xl mx-auto px-4 py-8 relative z-10">
  <div id="proposalResponseSection" class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 text-center space-y-6">
    <?php if ($existingResp): ?>
      <div class="space-y-4">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 flex items-center justify-center mx-auto shadow-lg">
          <i data-lucide="check-circle-2" class="w-7 h-7 text-[#eac34a]"></i>
        </div>
        <div class="space-y-1">
          <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#eac34a]">Your Answer 💕</span>
          <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">
            <?= $existingResp['response'] === 'yes' ? 'YES! A Thousand Times Yes 💍' : "Let's Talk 💕" ?>
          </h3>
          <p class="text-xs text-[#d0c3cb]/80">Responded on <?= htmlspecialchars($existingResp['responded_at_formatted'] ?? 'Recently') ?></p>
        </div>
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] text-sm font-serif italic text-[#eac34a] max-w-md mx-auto shadow-inner">
          "<?= htmlspecialchars($existingResp['partner_note'] ?? ($existingResp['response'] === 'yes' ? 'YES! A thousand times YES my love! 💕' : 'Let\'s talk and celebrate together! 💕')) ?>"
        </div>
        <p class="text-[11px] text-[#d0c3cb]/60 italic pt-1"><?= $buyerName ?> has been notified!</p>
      </div>
    <?php else: ?>
      <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Your Answer</h3>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="submitProposalAnswer('<?= htmlspecialchars($data['page_id'] ?? '') ?>', 'yes')" class="px-8 py-4 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-full hover:bg-[#ffe088] shadow-lg flex items-center justify-center gap-2 cursor-pointer">
          <i data-lucide="heart" class="w-4 h-4 fill-current"></i>
          <span>YES! A Thousand Times Yes</span>
        </button>
        <button onclick="submitProposalAnswer('<?= htmlspecialchars($data['page_id'] ?? '') ?>', 'lets_talk')" class="px-6 py-4 bg-[#151215] text-[#e8e0e3] border border-[#4d444b] font-bold text-xs uppercase tracking-wider rounded-full hover:border-[#eac34a] flex items-center justify-center gap-2 cursor-pointer">
          <span>Let's Talk &amp; Celebrate</span>
        </button>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Footer Bar -->
<footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
  <p class="text-xs text-[#d0c3cb]">Made with endless love by <strong class="text-[#eac34a]"><?= $buyerName ?></strong> for <strong class="text-[#eac34a]"><?= $partnerName ?></strong></p>
  <div class="flex items-center justify-center gap-3">
    <button onclick="relockGiftSession()" type="button" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer">
      <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
      <span>Lock Gift Page 🔒</span>
    </button>
  </div>
</footer>
