<?php
/**
 * SoulScript - Admin Gift Cards & Templates Manager UI
 * Full CRUD, Custom Sequence Re-ordering (Move Up/Down), Active Status Toggles,
 * Cover Photo WebP Uploads, and Auto Payment Link Generation
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/media_helper.php';

session_start();
$isAdminLoggedIn = !empty($_SESSION['admin_logged_in']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gift Cards & Templates Manager — SoulScript Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #120f12; color: #e8e0e3; }
    .font-serif { font-family: 'Cinzel', serif; }
  </style>
</head>
<body class="min-h-screen pb-16">

  <!-- Header Banner (Mobile Responsive) -->
  <header class="bg-[#1b171b]/95 border-b border-[#3b1e3b] sticky top-0 z-40 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-4 py-3.5 sm:py-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
      <div class="flex items-center gap-3">
        <a href="index.php" class="p-2 rounded-xl bg-[#241c24] text-[#eac34a] hover:bg-[#3b1e3b] transition-all flex items-center justify-center border border-[#eac34a]/20 shrink-0">
          <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div class="min-w-0">
          <h1 class="text-base sm:text-xl font-bold font-serif text-[#e8e0e3] flex items-center gap-2 truncate">
            <i data-lucide="layout-grid" class="w-4 h-4 sm:w-5 sm:h-5 text-[#eac34a] shrink-0"></i>
            <span class="truncate">Gift Cards Manager</span>
          </h1>
          <p class="text-[11px] text-[#b8a7b3] leading-tight truncate sm:whitespace-normal">Manage gift templates, prices, cover photos &amp; live URLs</p>
        </div>
      </div>
      <button onclick="openTemplateModal()" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#eac34a] text-[#241a00] font-bold text-xs hover:bg-[#ffe088] transition-all shadow-lg flex items-center justify-center gap-1.5 shrink-0 cursor-pointer">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span>Add New Gift Card</span>
      </button>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">
    <?php require_once __DIR__ . '/nav_header.php'; ?>

    <!-- Stats & Guidelines Alert -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-[#1b171b] border border-[#3b1e3b] rounded-2xl p-4 flex items-center justify-between">
        <div>
          <span class="text-xs text-[#b8a7b3] uppercase tracking-wider block">Total Active Cards</span>
          <span class="text-2xl font-bold font-serif text-[#eac34a]" id="statActiveCards">0</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-[#eac34a]/10 border border-[#eac34a]/30 flex items-center justify-center text-[#eac34a]">
          <i data-lucide="sparkles" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-[#1b171b] border border-[#3b1e3b] rounded-2xl p-4 flex items-center justify-between">
        <div>
          <span class="text-xs text-[#b8a7b3] uppercase tracking-wider block">Total Templates</span>
          <span class="text-2xl font-bold font-serif text-[#e4b9df]" id="statTotalCards">0</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-[#e4b9df]/10 border border-[#e4b9df]/30 flex items-center justify-center text-[#e4b9df]">
          <i data-lucide="gift" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-[#1b171b] border border-[#eac34a]/30 rounded-2xl p-4 flex items-center gap-3">
        <i data-lucide="shield-check" class="w-8 h-8 text-[#eac34a] shrink-0"></i>
        <div class="text-[11px] text-[#d0c3cb]">
          <strong class="text-[#eac34a] block">Persistent Image Backup Active</strong>
          All cover photos uploaded here are automatically protected from Git deployment wipes.
        </div>
      </div>
    </div>

    <!-- Admin Quick Reference & Code Fallbacks Guide -->
    <div class="bg-[#1b171b] border border-[#eac34a]/30 rounded-2xl p-5 shadow-xl space-y-4">
      <div class="flex items-center justify-between border-b border-[#3b1e3b] pb-3">
        <div class="flex items-center gap-2">
          <i data-lucide="book-open" class="w-5 h-5 text-[#eac34a]"></i>
          <h3 class="text-sm font-bold font-serif text-[#e8e0e3] uppercase tracking-wider">💡 Admin Quick Reference &amp; Code Fallbacks Guide</h3>
        </div>
        <button type="button" onclick="document.getElementById('adminGuideContent').classList.toggle('hidden')" class="text-xs text-[#eac34a] hover:underline font-semibold cursor-pointer">
          Toggle Guide ↕️
        </button>
      </div>

      <div id="adminGuideContent" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-[#d0c3cb]">
        <!-- Guide Item 1 -->
        <div class="bg-[#151215] p-3.5 rounded-xl border border-[#3b1e3b] space-y-1.5">
          <div class="font-bold text-[#eac34a] flex items-center gap-1.5">
            <i data-lucide="link" class="w-3.5 h-3.5"></i>
            <span>1. Dynamic Demo URLs &amp; Passwords</span>
          </div>
          <p class="text-[11px] leading-relaxed">
            Changing the <strong>Demo URL</strong> or <strong>Demo Password</strong> in any gift card below instantly updates the live website sample buttons on <code class="text-[#e4b9df] bg-[#221f21] px-1 py-0.5 rounded">index.php</code>!
          </p>
        </div>

        <!-- Guide Item 2 -->
        <div class="bg-[#151215] p-3.5 rounded-xl border border-[#3b1e3b] space-y-1.5">
          <div class="font-bold text-[#e4b9df] flex items-center gap-1.5">
            <i data-lucide="file-code" class="w-3.5 h-3.5"></i>
            <span>2. Hardcoded Code Fallbacks (For VS Code)</span>
          </div>
          <ul class="text-[11px] space-y-1 font-mono text-[#b8a7b3]">
            <li>• <strong class="text-[#e8e0e3]">index.php:L228</strong> &rarr; <code class="text-[#eac34a]">$defaultDemos</code> array</li>
            <li>• <strong class="text-[#e8e0e3]">index.php:L145</strong> &rarr; Hero Banner Demo buttons</li>
            <li>• <strong class="text-[#e8e0e3]">includes/header.php:L34</strong> &rarr; Navbar Live Demo link</li>
          </ul>
        </div>

        <!-- Guide Item 3 -->
        <div class="bg-[#151215] p-3.5 rounded-xl border border-[#3b1e3b] space-y-1.5">
          <div class="font-bold text-[#eac34a] flex items-center gap-1.5">
            <i data-lucide="image" class="w-3.5 h-3.5"></i>
            <span>3. Cover Photo Upload Specs</span>
          </div>
          <p class="text-[11px] leading-relaxed">
            Recommended size: <strong>800 x 600 px</strong> or <strong>800 x 800 px</strong>. Max size: 5 MB. Automatically converted to WebP and backed up to outer persistent storage <code class="text-[#eac34a] bg-[#221f21] px-1 py-0.5 rounded">uploads_persistent/default_gallery/</code> (100% safe from Git wipes).
          </p>
        </div>

        <!-- Guide Item 4 -->
        <div class="bg-[#151215] p-3.5 rounded-xl border border-[#3b1e3b] space-y-1.5">
          <div class="font-bold text-[#e4b9df] flex items-center gap-1.5">
            <i data-lucide="zap" class="w-3.5 h-3.5"></i>
            <span>4. Auto-Generated Personalization Links</span>
          </div>
          <p class="text-[11px] leading-relaxed">
            Typing a title like <em>"Diwali Surprise"</em> auto-generates slug <code class="text-[#e4b9df] bg-[#221f21] px-1 py-0.5 rounded">diwali_surprise</code> and personalization URL <code class="text-[#eac34a] bg-[#221f21] px-1 py-0.5 rounded">create.php?template=diwali_surprise</code>.
          </p>
        </div>

        <!-- Guide Item 5 -->
        <div class="bg-[#151215] p-3.5 rounded-xl border border-[#3b1e3b] space-y-1.5 md:col-span-2">
          <div class="font-bold text-[#eac34a] flex items-center gap-1.5">
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
            <span>5. Admin Free Demo Creation (0 ₹ Razorpay Bypass)</span>
          </div>
          <p class="text-[11px] leading-relaxed">
            As the logged-in Website Owner, whenever you create a new surprise demo site via <code class="text-[#e4b9df] bg-[#221f21] px-1 py-0.5 rounded">create.php</code>, Razorpay is <strong>100% skipped automatically</strong>! You get instant FREE active orders with 0 ₹ payment.
          </p>
        </div>
      </div>
    </div>

    <!-- Cards Grid -->
    <div id="templatesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div class="col-span-full text-center py-16 text-[#b8a7b3] text-sm">
        <i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto text-[#eac34a] mb-2"></i>
        Loading gift card templates...
      </div>
    </div>

  </main>

  <!-- Add / Edit Template Modal -->
  <div id="templateModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[9999] flex items-center justify-center p-4 hidden">
    <div class="bg-[#221f21] border border-[#eac34a]/40 rounded-3xl p-5 sm:p-6 max-w-xl w-full text-left space-y-4 shadow-2xl relative max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between border-b border-[#3b1e3b] pb-3 shrink-0">
        <div>
          <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2" id="modalTitle">
            <i data-lucide="gift" class="w-4 h-4 text-[#eac34a]"></i>
            <span>Add New Gift Card</span>
          </h3>
          <p class="text-[11px] text-[#b8a7b3]">Personalization link will be automatically generated from Card Title!</p>
        </div>
        <button onclick="closeTemplateModal()" type="button" class="text-[#b8a7b3] hover:text-white text-lg font-bold p-1 cursor-pointer">✕</button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <form id="templateForm" onsubmit="handleSaveTemplate(event); return false;" class="space-y-4 overflow-y-auto pr-1 flex-1">
        <input type="hidden" id="formTemplateId" value="">
        <input type="hidden" id="formExistingImageUrl" value="">

        <!-- Cover Photo Upload Field with UI Note -->
        <div>
          <label class="block text-xs font-bold text-[#eac34a] uppercase tracking-wider mb-1">
            Cover Photo Image (WebP Auto-Convert) *
          </label>
          <div class="flex items-center gap-3">
            <div id="coverPreviewContainer" class="w-20 h-20 rounded-xl overflow-hidden border border-[#4d444b] bg-[#100d10] shrink-0 flex items-center justify-center relative">
              <img id="coverPreviewImg" src="" class="w-full h-full object-cover hidden">
              <i id="coverPreviewIcon" data-lucide="image" class="w-6 h-6 text-[#b8a7b3]"></i>
            </div>
            <div class="flex-1 space-y-1.5">
              <input type="file" id="coverFileInput" accept="image/*" onchange="handleCoverFileSelect(event)" class="block w-full text-xs text-[#b8a7b3] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#3b1e3b] file:text-[#e4b9df] hover:file:bg-[#4d274d] cursor-pointer">
              <!-- Clear UI Dimension Note -->
              <p class="text-[10px] text-[#eac34a]/90 font-mono leading-tight">
                💡 <strong>Recommended Size:</strong> 800 x 600 px (4:3 aspect ratio) or 800 x 800 px. Max file size: 5 MB. Automatically converted to optimized WebP.
              </p>
            </div>
          </div>
        </div>

        <!-- Card Title (Heading) -->
        <div>
          <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Card Title (Heading) *</label>
          <input type="text" id="formName" required oninput="updateAutoSlugPreview(this.value)" placeholder="e.g. Raksha Bandhan Special 🪔" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
          <!-- Auto-Generated Slug Preview -->
          <p class="text-[10px] text-[#b8a7b3] mt-1 font-mono">
            Auto-Generated Create Link: <span id="autoSlugPreview" class="text-[#eac34a] font-bold"><?php echo APP_URL; ?>/create.php?template=...</span>
          </p>
        </div>

        <!-- Subtitle / Tagline -->
        <div>
          <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Subtitle / Short Tagline *</label>
          <input type="text" id="formTagline" required placeholder="e.g. Celebrate the timeless bond of brother and sister" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
        </div>

        <!-- Description & Features -->
        <div>
          <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Full Description &amp; Feature List *</label>
          <textarea id="formDescription" required rows="3" placeholder="Describe included features (e.g. Interactive Rakhi tying, 5 promise cards, childhood memory scrapbook...)" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none"></textarea>
        </div>

        <!-- Pricing & Badge Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Price (INR ₹) *</label>
            <input type="number" id="formPriceInr" required min="0" step="1" value="449" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Badge Tag Label</label>
            <input type="text" id="formBadge" placeholder="e.g. Festival Special 🪔" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
          </div>
        </div>

        <!-- Button Text -->
        <div>
          <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Button Text *</label>
          <input type="text" id="formButtonText" value="Personalize This Gift 🎁" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
        </div>

        <!-- Demo URL & Demo Password Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Demo Live URL</label>
            <input type="url" id="formDemoUrl" placeholder="https://digitalyogi24.com/gift/manvi-testing" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[#e8e0e3] mb-1">Demo Unlock Password</label>
            <input type="text" id="formDemoPassword" placeholder="e.g. 1234" class="w-full bg-[#171317] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
          </div>
        </div>

        <!-- Active Status Toggle -->
        <div class="flex items-center justify-between p-3 bg-[#171317] border border-[#4d444b] rounded-xl">
          <div>
            <span class="text-xs font-bold text-[#e8e0e3] block">Display Status</span>
            <span class="text-[10px] text-[#b8a7b3]">Active cards will appear on Homepage and Creation Wizard</span>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="formActiveStatus" checked class="sr-only peer">
            <div class="w-11 h-6 bg-[#3b1e3b] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#eac34a]"></div>
          </label>
        </div>

        <div class="pt-3 border-t border-[#3b1e3b] flex items-center justify-end gap-3 shrink-0">
          <button type="button" onclick="closeTemplateModal()" class="px-4 py-2 bg-[#171317] text-[#b8a7b3] border border-[#4d444b] rounded-xl font-bold text-xs">Cancel</button>
          <button type="submit" id="saveSubmitBtn" class="px-6 py-2 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-[#ffe088] transition-all shadow-md">
            Save Gift Card
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let globalTemplatesList = [];
    let uploadedCoverBase64 = '';

    document.addEventListener('DOMContentLoaded', () => {
      loadAdminTemplates();
    });

    async function loadAdminTemplates() {
      const container = document.getElementById('templatesContainer');
      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/admin_templates.php');
        const data = await res.json();

        if (data.status === 'success' && data.templates) {
          globalTemplatesList = data.templates;

          const activeCount = globalTemplatesList.filter(t => t.active === 1).length;
          document.getElementById('statActiveCards').innerText = activeCount;
          document.getElementById('statTotalCards').innerText = globalTemplatesList.length;

          renderTemplatesGrid();
        } else {
          container.innerHTML = `<div class="col-span-full py-10 text-center text-xs text-[#b8a7b3]">No gift templates found.</div>`;
        }
      } catch (err) {
        container.innerHTML = `<div class="col-span-full py-10 text-center text-xs text-rose-400">Error loading templates: ${err.message}</div>`;
      }
    }

    function renderTemplatesGrid() {
      const container = document.getElementById('templatesContainer');
      if (!container) return;

      if (globalTemplatesList.length === 0) {
        container.innerHTML = `<div class="col-span-full py-10 text-center text-xs text-[#b8a7b3]">No templates defined. Click "+ Add New Gift Card" to create one.</div>`;
        return;
      }

      container.innerHTML = globalTemplatesList.map((item, idx) => `
        <div class="bg-[#1b171b] border ${item.active ? 'border-[#3b1e3b] hover:border-[#eac34a]' : 'border-rose-900/40 opacity-70'} rounded-3xl p-4 shadow-xl flex flex-col justify-between transition-all relative group">
          
          <!-- Top Cover Image with Badges -->
          <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-[#100d10] mb-3">
            <img src="${item.preview_image_url}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=600&q=80'" class="w-full h-full object-cover">
            
            <div class="absolute top-2 left-2 flex flex-col gap-1 items-start">
              ${item.badge ? `<span class="bg-[#eac34a] text-[#241a00] text-[10px] font-bold px-2 py-0.5 rounded-md shadow-md">${escapeHtml(item.badge)}</span>` : ''}
              <span class="bg-black/75 backdrop-blur-sm text-white text-[9px] font-mono px-2 py-0.5 rounded-md border border-white/10">ID: ${item.template_id}</span>
            </div>

            <div class="absolute top-2 right-2 flex items-center gap-1.5">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold shadow-md ${item.active ? 'bg-emerald-500 text-white' : 'bg-rose-900 text-rose-200'}">
                ${item.active ? '✓ Active' : '✕ Inactive'}
              </span>
            </div>

            <div class="absolute bottom-2 left-2 right-2 bg-black/80 backdrop-blur-md p-2 rounded-xl border border-white/10 flex items-center justify-between">
              <span class="text-xs font-bold text-[#eac34a]">₹${item.price_inr}</span>
              <span class="text-[10px] text-[#b8a7b3] font-mono">Pos: #${idx + 1}</span>
            </div>
          </div>

          <!-- Body Text -->
          <div class="space-y-2 mb-4 flex-1">
            <h3 class="text-base font-bold font-serif text-[#e8e0e3] leading-snug">${escapeHtml(item.name)}</h3>
            <p class="text-xs font-semibold text-[#eac34a]">${escapeHtml(item.tagline)}</p>
            <p class="text-[11px] text-[#b8a7b3] line-clamp-3 leading-relaxed">${escapeHtml(item.description)}</p>
          </div>

          <!-- Live Create Link Box -->
          <div class="bg-[#120f12] border border-[#3b1e3b] rounded-xl p-2.5 mb-4 space-y-1.5">
            <div class="flex items-center justify-between">
              <span class="text-[10px] text-[#eac34a] font-bold uppercase tracking-wider flex items-center gap-1">
                <i data-lucide="link" class="w-3 h-3"></i> 100% Working Create URL
              </span>
              <button type="button" onclick="copyToClipboard('${item.create_url}')" class="text-[9px] bg-[#3b1e3b] text-[#e4b9df] px-2 py-0.5 rounded-md hover:bg-[#4d274d] cursor-pointer">Copy</button>
            </div>
            <a href="${item.create_url}" target="_blank" class="text-[10px] text-[#d0c3cb] font-mono block truncate hover:text-[#eac34a] underline">
              ${item.create_url}
            </a>
          </div>

          <!-- Card Action Buttons Grid -->
          <div class="pt-3 border-t border-[#3b1e3b] flex flex-col gap-2">
            <!-- Sequence Controls -->
            <div class="flex items-center justify-between gap-1 bg-[#171317] p-1.5 rounded-xl border border-[#4d444b]">
              <span class="text-[10px] text-[#b8a7b3] font-bold px-1">Order Sequence:</span>
              <div class="flex items-center gap-1">
                <button type="button" onclick="moveTemplateSequence('${item.template_id}', -1)" ${idx === 0 ? 'disabled class="opacity-30 cursor-not-allowed px-2 py-1 bg-[#241c24] text-white text-[10px] font-bold rounded-lg"' : 'class="px-2.5 py-1 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#4d274d] text-[10px] font-bold rounded-lg cursor-pointer"'}>
                  ⬆️ Up
                </button>
                <button type="button" onclick="moveTemplateSequence('${item.template_id}', 1)" ${idx === globalTemplatesList.length - 1 ? 'disabled class="opacity-30 cursor-not-allowed px-2 py-1 bg-[#241c24] text-white text-[10px] font-bold rounded-lg"' : 'class="px-2.5 py-1 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#4d274d] text-[10px] font-bold rounded-lg cursor-pointer"'}>
                  ⬇️ Down
                </button>
              </div>
            </div>

            <!-- Management Buttons -->
            <div class="flex items-center gap-2">
              <button type="button" onclick="toggleTemplateStatus('${item.template_id}')" class="flex-1 py-2 rounded-xl text-[10px] font-bold border transition-all cursor-pointer ${item.active ? 'bg-amber-950/40 text-amber-300 border-amber-500/40 hover:bg-amber-900/60' : 'bg-emerald-950/40 text-emerald-300 border-emerald-500/40 hover:bg-emerald-900/60'}">
                ${item.active ? '⏸️ Hide Card' : '▶️ Activate Card'}
              </button>
              <button type="button" onclick="openTemplateModal('${item.template_id}')" class="px-3 py-2 rounded-xl bg-[#3b1e3b] text-[#e4b9df] font-bold text-[10px] border border-[#e4b9df]/30 hover:bg-[#4d274d] transition-all cursor-pointer">
                ✏️ Edit
              </button>
              <button type="button" onclick="deleteTemplate('${item.template_id}')" class="px-2.5 py-2 rounded-xl bg-rose-950/60 text-rose-300 font-bold text-[10px] border border-rose-500/30 hover:bg-rose-900/80 transition-all cursor-pointer">
                🗑️
              </button>
            </div>
          </div>

        </div>
      `).join('');

      if (typeof lucide === 'object') lucide.createIcons();
    }

    function updateAutoSlugPreview(val) {
      const preview = document.getElementById('autoSlugPreview');
      if (!preview) return;
      const slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
      preview.innerText = '<?php echo APP_URL; ?>/create.php?template=' + (slug || '...');
    }

    function openTemplateModal(templateId = null) {
      const modal = document.getElementById('templateModal');
      const modalTitle = document.getElementById('modalTitle');
      const form = document.getElementById('templateForm');

      form.reset();
      uploadedCoverBase64 = '';
      document.getElementById('coverPreviewImg').classList.add('hidden');
      document.getElementById('coverPreviewIcon').classList.remove('hidden');

      if (templateId) {
        const t = globalTemplatesList.find(item => item.template_id === templateId);
        if (t) {
          modalTitle.innerHTML = `<i data-lucide="edit" class="w-4 h-4 text-[#eac34a]"></i><span>Edit Gift Card (${t.name})</span>`;
          document.getElementById('formTemplateId').value = t.template_id;
          document.getElementById('formName').value = t.name;
          document.getElementById('formTagline').value = t.tagline;
          document.getElementById('formDescription').value = t.description;
          document.getElementById('formPriceInr').value = t.price_inr;
          document.getElementById('formBadge').value = t.badge || '';
          document.getElementById('formButtonText').value = t.button_text || 'Personalize This Gift 🎁';
          document.getElementById('formDemoUrl').value = t.demo_url || '';
          document.getElementById('formDemoPassword').value = t.demo_password || '';
          document.getElementById('formActiveStatus').checked = (t.active === 1);
          document.getElementById('formExistingImageUrl').value = t.preview_image_url || '';

          if (t.preview_image_url) {
            document.getElementById('coverPreviewImg').src = t.preview_image_url;
            document.getElementById('coverPreviewImg').classList.remove('hidden');
            document.getElementById('coverPreviewIcon').classList.add('hidden');
          }
          updateAutoSlugPreview(t.name);
        }
      } else {
        modalTitle.innerHTML = `<i data-lucide="plus-circle" class="w-4 h-4 text-[#eac34a]"></i><span>Add New Gift Card</span>`;
        document.getElementById('formTemplateId').value = '';
        document.getElementById('formExistingImageUrl').value = '';
        updateAutoSlugPreview('');
      }

      if (modal) modal.classList.remove('hidden');
      if (typeof lucide === 'object') lucide.createIcons();
    }

    function closeTemplateModal() {
      const modal = document.getElementById('templateModal');
      if (modal) modal.classList.add('hidden');
    }

    function handleCoverFileSelect(e) {
      const file = e.target.files[0];
      if (!file) return;

      if (file.size > 5 * 1024 * 1024) {
        alert('⚠️ Maximum file size is 5 MB! Please select a smaller photo.');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(evt) {
        uploadedCoverBase64 = evt.target.result;
        document.getElementById('coverPreviewImg').src = uploadedCoverBase64;
        document.getElementById('coverPreviewImg').classList.remove('hidden');
        document.getElementById('coverPreviewIcon').classList.add('hidden');
      };
      reader.readAsDataURL(file);
    }

    async function handleSaveTemplate(e) {
      e.preventDefault();
      const btn = document.getElementById('saveSubmitBtn');
      btn.innerText = 'Saving...';
      btn.disabled = true;

      const payload = {
        action: 'save',
        template_id: document.getElementById('formTemplateId').value,
        name: document.getElementById('formName').value,
        tagline: document.getElementById('formTagline').value,
        description: document.getElementById('formDescription').value,
        price_inr: parseFloat(document.getElementById('formPriceInr').value),
        badge: document.getElementById('formBadge').value,
        button_text: document.getElementById('formButtonText').value,
        demo_url: document.getElementById('formDemoUrl').value,
        demo_password: document.getElementById('formDemoPassword').value,
        active: document.getElementById('formActiveStatus').checked ? 1 : 0,
        cover_photo: uploadedCoverBase64,
        existing_image_url: document.getElementById('formExistingImageUrl').value
      };

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/admin_templates.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.status === 'success') {
          closeTemplateModal();
          loadAdminTemplates();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Server Error: ' + err.message);
      } finally {
        btn.innerText = 'Save Gift Card';
        btn.disabled = false;
      }
    }

    async function moveTemplateSequence(templateId, direction) {
      const idx = globalTemplatesList.findIndex(t => t.template_id === templateId);
      if (idx === -1) return;

      const targetIdx = idx + direction;
      if (targetIdx < 0 || targetIdx >= globalTemplatesList.length) return;

      // Swap in memory instantly
      const temp = globalTemplatesList[idx];
      globalTemplatesList[idx] = globalTemplatesList[targetIdx];
      globalTemplatesList[targetIdx] = temp;

      // Re-render UI grid immediately so button clicks feel instantaneous
      renderTemplatesGrid();

      const sequence = globalTemplatesList.map(t => t.template_id);

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/admin_templates.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'reorder', sequence: sequence })
        });
        const data = await res.json();
        if (data.status !== 'success') {
          loadAdminTemplates();
        }
      } catch (err) {
        loadAdminTemplates();
      }
    }

    async function toggleTemplateStatus(templateId) {
      try {
        await fetch('<?php echo APP_URL; ?>/api/admin_templates.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'toggle_status', template_id: templateId })
        });
        loadAdminTemplates();
      } catch (err) {}
    }

    async function deleteTemplate(templateId) {
      if (!confirm(`Are you sure you want to delete template "${templateId}"?`)) return;
      try {
        await fetch('<?php echo APP_URL; ?>/api/admin_templates.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'delete', template_id: templateId })
        });
        loadAdminTemplates();
      } catch (err) {}
    }

    function copyToClipboard(text) {
      navigator.clipboard.writeText(text);
      alert('📋 Personalize / Payment URL copied to clipboard:\n' + text);
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
  </script>
</body>
</html>
