<?php
// templates/themes/birthday_magic.php

$tf = $content['template_fields'] ?? [];
$media = $content['media'] ?? [];
$reasons = $tf['reasons'] ?? [];

$partnerName = htmlspecialchars($content['partner_name'] ?? 'Partner');
$buyerName = htmlspecialchars($content['buyer_name'] ?? 'Someone Special');
$taglineQuote = htmlspecialchars($content['tagline_quote'] ?? 'Cheers to another year of awesome memories! 🥂');
$loveNoteText = htmlspecialchars($content['love_note_text'] ?? 'Happy Birthday!');
$dobStr = htmlspecialchars($tf['partner_dob'] ?? '1998-11-20');

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

    <!-- Floating Tagline / Motto Banner -->
    <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#3b1e3b]/80 border border-[#e4b9df]/40 text-[#eac34a] text-xs font-bold shadow-lg backdrop-blur-md">
      <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
      <span class="font-serif italic text-sm tracking-wide">"<?= $taglineQuote ?>"</span>
    </div>

    <h1 class="text-4xl sm:text-6xl font-extrabold font-serif text-[#e8e0e3] tracking-tight leading-tight">
      Happy Birthday, <?= $partnerName ?>! 🎉
    </h1>

    <p class="text-xs sm:text-sm text-[#d0c3cb] max-w-md mx-auto leading-relaxed">
      A special celebration page created with love &amp; best wishes by <strong class="text-[#eac34a]"><?= $buyerName ?></strong>.
    </p>

    <!-- Next Birthday Countdown -->
    <div class="pt-4 max-w-xl mx-auto">
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#eac34a]/30 shadow-2xl space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-[#eac34a] flex items-center justify-center gap-1.5">
          <i data-lucide="clock" class="w-4 h-4 text-[#eac34a]"></i>
          <span>Countdown to Your Next Birthday</span>
        </span>

        <div class="grid grid-cols-4 gap-3 pt-1">
          <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
            <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdayDays">0</span>
            <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Days</span>
          </div>
          <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
            <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdayHours">0</span>
            <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Hours</span>
          </div>
          <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
            <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdayMins">0</span>
            <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Mins</span>
          </div>
          <div class="bg-[#151215] p-3.5 rounded-2xl border border-[#4d444b] text-center">
            <span class="block text-2xl font-black text-[#eac34a] font-mono" id="bdaySecs">0</span>
            <span class="text-[10px] uppercase font-bold text-[#d0c3cb]/70">Secs</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Reasons I Love Celebrating You -->
<?php if (!empty($reasons)): ?>
  <section class="max-w-3xl mx-auto px-4 py-12 relative z-10">
    <div class="text-center space-y-2 mb-12">
      <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">SPECIAL QUALITIES</span>
      <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Reasons I Love Celebrating You</h2>
      <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
    </div>

    <div class="space-y-4">
      <?php foreach ($reasons as $i => $r): ?>
        <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] hover:border-[#eac34a]/60 shadow-xl flex items-start gap-4 transition-all">
          <div class="w-8 h-8 rounded-full bg-[#3b1e3b] border border-[#eac34a]/40 text-[#eac34a] font-bold text-sm flex items-center justify-center shrink-0">
            <?= $i + 1 ?>
          </div>
          <p class="text-xs sm:text-sm text-[#e8e0e3] font-medium leading-relaxed pt-1"><?= htmlspecialchars($r) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<!-- Moments of Joy Photo Gallery -->
<?php if (!empty($media)): ?>
  <section class="max-w-5xl mx-auto px-4 py-12 relative z-10">
    <div class="text-center space-y-2 mb-12">
      <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-[#eac34a] block">PHOTO MEMORIES</span>
      <h2 class="text-3xl sm:text-4xl font-bold font-serif text-[#e8e0e3]">Moments of Joy</h2>
      <div class="w-12 h-[2px] bg-[#eac34a]/80 mx-auto mt-2"></div>
    </div>

    <div class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
      <?php foreach ($media as $m): 
        $imgUrl = htmlspecialchars(resolveMediaUrl($m['file_path'] ?? ''));
        $capText = htmlspecialchars($m['caption'] ?? 'Birthday Memory');
      ?>
        <div onclick="openLightbox('<?= $imgUrl ?>')" class="break-inside-avoid rounded-2xl overflow-hidden group cursor-pointer bg-[#151215] border border-[#4d444b] shadow-xl hover:border-[#eac34a]/70 transition-all">
          <img src="<?= $imgUrl ?>" onerror="this.onerror=null; this.style.background='#221f21'; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23221f21%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23eac34a%22 font-size=%2228%22%3E📷%3C/text%3E%3C/svg%3E'" alt="Moments of joy" class="w-full h-auto object-contain block group-hover:scale-[1.02] transition-transform duration-500">
          <div class="p-3 bg-[#221f21] border-t border-[#4d444b]/40 text-left">
            <span class="text-[11px] font-bold text-[#eac34a] block"><?= $capText ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<!-- Birthday Note Card -->
<section class="max-w-2xl mx-auto px-4 py-12 relative z-10">
  <div class="bg-[#221f21] p-8 sm:p-10 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center relative overflow-hidden">
    <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
      <i data-lucide="feather" class="w-6 h-6"></i>
    </div>

    <div class="space-y-4">
      <h3 class="text-2xl font-bold font-serif text-[#e8e0e3]">Your Birthday Wish</h3>
      <p class="text-sm sm:text-base font-serif text-[#d0c3cb] italic leading-relaxed whitespace-pre-line">
        "<?= $loveNoteText ?>"
      </p>
    </div>

    <div class="pt-4 border-t border-[#4d444b]/50">
      <span class="text-xs uppercase tracking-widest text-[#eac34a] font-bold block">With Love &amp; Best Wishes,</span>
      <span class="text-xl font-bold font-serif text-[#e8e0e3] mt-1 block"><?= $buyerName ?></span>
    </div>
  </div>
</section>

<!-- Footer Bar -->
<footer class="mt-20 pt-8 pb-12 border-t border-[#4d444b]/40 text-center relative z-10 space-y-4">
  <p class="text-xs text-[#d0c3cb]">Made with love by <strong class="text-[#eac34a]"><?= $buyerName ?></strong> for <strong class="text-[#eac34a]"><?= $partnerName ?></strong></p>
  <div class="flex items-center justify-center gap-3">
    <button onclick="relockGiftSession()" type="button" class="px-4 py-2 rounded-full border border-[#4d444b] bg-[#151215] text-[#d0c3cb] hover:border-[#eac34a] text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer">
      <i data-lucide="lock" class="w-3.5 h-3.5 text-[#eac34a]"></i>
      <span>Lock Gift Page 🔒</span>
    </button>
  </div>
</footer>

<script>
  if (typeof startBirthdayCountdown === 'function') {
    startBirthdayCountdown('<?= $dobStr ?>');
  }
</script>
