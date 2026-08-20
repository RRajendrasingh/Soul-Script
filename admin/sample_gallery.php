<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header("Location: " . APP_URL . "/admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Sample Gallery Manager — Admin ' . APP_NAME;
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
  <script src="<?php echo APP_URL; ?>/assets/js/compressor.js"></script>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Global Header -->
  <?php 
  $current_page = 'admin';
  $isAdminPage = true;
  require_once __DIR__ . '/../includes/header.php'; 
  ?>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-20 relative z-10 space-y-8">
    <?php require_once __DIR__ . '/nav_header.php'; ?>
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#221f21]/80 backdrop-blur-md p-6 rounded-3xl border border-[#4d444b] shadow-2xl">
      <div>
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-[#eac34a] mb-1">
          <i data-lucide="shield-check" class="w-4 h-4"></i> Admin Panel
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold font-serif text-[#e8e0e3]">Default Sample Gallery Manager</h1>
        <p class="text-xs sm:text-sm text-[#d0c3cb] mt-1">Upload and manage self-hosted WebP sample images and captions for template defaults and sample picker.</p>
      </div>

      <div class="flex items-center gap-3 w-full sm:w-auto">
        <label for="sampleFileInput" class="flex-1 sm:flex-none cursor-pointer bg-gradient-to-r from-[#eac34a] to-[#d8ad2e] text-[#151215] font-semibold text-xs sm:text-sm px-5 py-3 rounded-2xl hover:opacity-90 transition flex items-center justify-center gap-2 shadow-lg">
          <i data-lucide="upload-cloud" class="w-4 h-4"></i> Upload Sample Photo
        </label>
        <input type="file" id="sampleFileInput" accept="image/*" class="hidden" onclick="this.value=''" onchange="handleSampleUpload(this.files[0])">

        <a href="<?php echo APP_URL; ?>/admin/index.php" class="bg-[#3b1e3b] text-[#e8e0e3] border border-[#e4b9df]/40 font-semibold text-xs sm:text-sm px-4 py-3 rounded-2xl hover:bg-[#4d274d] transition flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
        </a>
      </div>
    </div>

    <!-- Stats Summary Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] border border-[#e4b9df]/40 flex items-center justify-center text-[#eac34a]">
          <i data-lucide="image" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]" id="totalCountDisplay">0</div>
          <div class="text-xs text-[#d0c3cb]">Active Sample Photos</div>
        </div>
      </div>

      <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] border border-[#e4b9df]/40 flex items-center justify-center text-[#eac34a]">
          <i data-lucide="message-square-quote" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]">Caption Enabled</div>
          <div class="text-xs text-[#d0c3cb]">Custom Romantic Captions</div>
        </div>
      </div>

      <div class="bg-[#221f21] p-5 rounded-2xl border border-[#4d444b] flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[#3b1e3b] border border-[#e4b9df]/40 flex items-center justify-center text-[#eac34a]">
          <i data-lucide="globe" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-2xl font-bold font-serif text-[#e8e0e3]">Self-Hosted</div>
          <div class="text-xs text-[#d0c3cb]">Zero Unsplash Dependency</div>
        </div>
      </div>
    </div>

    <!-- Sample Photos Responsive Grid -->
    <div id="sampleGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div class="col-span-full text-center py-12 text-[#d0c3cb] text-sm">
        <i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto text-[#eac34a] mb-2"></i>
        Loading self-hosted sample assets...
      </div>
    </div>

    <!-- Universal Admin Footer -->
    <?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
  </main>

  <!-- Admin Sample Upload / Edit Modal -->
  <div id="sampleEditModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[9999] flex items-center justify-center p-4 hidden">
    <div class="bg-[#221f21] border border-[#eac34a]/40 rounded-3xl p-6 max-w-md w-full text-left space-y-4 shadow-2xl relative">
      <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
        <h3 id="modalTitle" class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
          <i data-lucide="edit-3" class="w-4 h-4 text-[#eac34a]"></i>
          <span>Sample Photo Metadata</span>
        </h3>
        <button type="button" onclick="closeSampleModal()" class="text-[#d0c3cb] hover:text-white text-lg font-bold p-1 cursor-pointer">✕</button>
      </div>

      <form id="sampleMetaForm" onsubmit="submitSampleMeta(event); return false;" class="space-y-4">
        <input type="hidden" id="modalFilename" value="">

        <div id="fileUploadGroup" class="space-y-1">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#eac34a]">Selected Photo File</label>
          <div id="previewFileName" class="text-xs text-[#d0c3cb] truncate bg-[#151215] p-2.5 rounded-xl border border-[#4d444b]">No file selected</div>
        </div>

        <div class="space-y-1">
          <label for="modalCaption" class="block text-xs font-bold uppercase tracking-wider text-[#eac34a]">Romantic Caption</label>
          <input type="text" id="modalCaption" required placeholder="e.g. Together Always 💑" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
        </div>

        <div class="space-y-1">
          <label for="modalCategory" class="block text-xs font-bold uppercase tracking-wider text-[#eac34a]">Occasion Category Tag</label>
          <select id="modalCategory" required class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-3 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none cursor-pointer">
            <option value="anniversary">Anniversary 🌹</option>
            <option value="birthday">Birthday 🎂</option>
            <option value="proposal">Proposal 💍</option>
            <option value="raksha_bandhan">Raksha Bandhan 🪔</option>
            <option value="long_distance">Long Distance ✈️</option>
          </select>
          <p class="text-[10px] text-[#d0c3cb] mt-1">This category determines which filter tab this photo appears in on create.php & edit.php.</p>
        </div>

        <div class="pt-2 flex items-center justify-end gap-3 border-t border-[#4d444b]/40">
          <button type="button" onclick="closeSampleModal()" class="px-4 py-2 bg-[#3b1e3b] text-[#e8e0e3] font-bold text-xs rounded-xl hover:bg-[#4d274d] transition cursor-pointer">Cancel</button>
          <button type="submit" id="modalSubmitBtn" class="px-5 py-2 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-[#ffe088] transition shadow-md cursor-pointer">Save Metadata</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let currentUploadFile = null;

    const catLabels = {
      'anniversary': 'Anniversary 🌹',
      'birthday': 'Birthday 🎂',
      'proposal': 'Proposal 💍',
      'raksha_bandhan': 'Rakhi 🪔',
      'long_distance': 'Long Distance ✈️'
    };

    document.addEventListener('DOMContentLoaded', () => {
      if (typeof lucide !== 'undefined') lucide.createIcons();
      fetchSamples();
    });

    async function fetchSamples() {
      const grid = document.getElementById('sampleGrid');
      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php');
        const data = await res.json();

        if (data.status !== 'success') {
          grid.innerHTML = `<div class="col-span-full p-6 bg-[#3b1e3b]/50 border border-red-500/40 rounded-2xl text-center text-xs text-red-300">Error loading sample assets.</div>`;
          return;
        }

        document.getElementById('totalCountDisplay').textContent = data.total_samples || 0;

        if (data.samples.length === 0) {
          grid.innerHTML = `<div class="col-span-full p-12 text-center bg-[#221f21] rounded-3xl border border-[#4d444b] text-sm text-[#d0c3cb]">No default sample photos found. Click "Upload Sample Photo" to add one!</div>`;
          return;
        }

        grid.innerHTML = data.samples.map(sample => {
          const catLabel = catLabels[sample.category] || 'Anniversary 🌹';
          const isCover = sample.is_template_cover;
          return `
            <div class="bg-[#221f21] rounded-2xl border ${isCover ? 'border-amber-500/40' : 'border-[#4d444b]'} overflow-hidden shadow-xl hover:border-[#eac34a]/60 transition group flex flex-col justify-between">
              <div class="relative aspect-square bg-black/40 overflow-hidden">
                <img src="${sample.url}" alt="${sample.caption || sample.filename}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                <span class="absolute top-2 right-2 bg-black/70 backdrop-blur-md text-[10px] font-mono px-2 py-0.5 rounded-full text-[#eac34a]">
                  ${sample.size_kb} KB
                </span>
                ${isCover ? `
                  <span class="absolute top-2 left-2 bg-amber-950/90 border border-amber-500/50 text-amber-300 text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md">
                    🎴 Template Cover Photo
                  </span>
                ` : ''}
              </div>

              <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                <div>
                  <div class="flex items-center gap-1.5 mb-1.5">
                    <span class="px-2 py-0.5 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 text-[10px] font-bold">
                      ${catLabel}
                    </span>
                  </div>
                  <div class="text-xs font-semibold text-[#e8e0e3] line-clamp-2" title="${sample.caption || 'No Caption'}">
                    💬 ${sample.caption || 'No Caption'}
                  </div>
                  <div class="text-[10px] font-mono text-[#d0c3cb]/70 truncate mt-1" title="${sample.filename}">
                    ${sample.filename}
                  </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-[#3d363d]">
                  <button onclick="openEditModal('${sample.filename}', '${(sample.caption || '').replace(/'/g, "\\'")}', '${sample.category || 'anniversary'}')" class="w-full bg-[#3b1e3b] text-xs font-semibold py-2 px-3 rounded-xl border border-[#e4b9df]/40 hover:bg-[#4d274d] transition flex items-center justify-center gap-1 text-[#e4b9df]">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Tag & Caption
                  </button>

                  <div class="flex items-center gap-2">
                    <button onclick="copyToClipboard('${sample.url}')" class="flex-1 bg-[#151215] text-xs font-semibold py-2 px-3 rounded-xl border border-[#4d444b] hover:border-[#eac34a] transition flex items-center justify-center gap-1 text-[#eac34a]">
                      <i data-lucide="copy" class="w-3.5 h-3.5"></i> Copy URL
                    </button>

                    ${!isCover ? `
                      <button onclick="deleteSample('${sample.filename}')" class="bg-red-950/60 text-red-300 hover:bg-red-900/80 text-xs font-semibold p-2 rounded-xl border border-red-500/40 transition">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    ` : `
                      <span title="Template Cover Photos cannot be deleted from sample gallery" class="opacity-40 cursor-not-allowed bg-gray-900 text-gray-500 text-xs font-semibold p-2 rounded-xl border border-gray-700">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                      </span>
                    `}
                  </div>
                </div>
              </div>
            </div>
          `;
        }).join('');

        if (typeof lucide !== 'undefined') lucide.createIcons();
      } catch (err) {
        grid.innerHTML = `<div class="col-span-full p-6 bg-[#3b1e3b]/50 border border-red-500/40 rounded-2xl text-center text-xs text-red-300">Network error fetching samples.</div>`;
      }
    }

    function handleSampleUpload(file) {
      if (!file) return;
      currentUploadFile = file;
      document.getElementById('modalFilename').value = '';
      document.getElementById('fileUploadGroup').classList.remove('hidden');
      document.getElementById('previewFileName').textContent = file.name + ' (' + roundKb(file.size) + ' KB)';
      document.getElementById('modalTitle').innerHTML = '<i data-lucide="upload-cloud" class="w-4 h-4 text-[#eac34a]"></i> <span>Upload Sample Photo</span>';
      document.getElementById('modalCaption').value = 'Together Always 💑';
      document.getElementById('modalCategory').value = 'anniversary';
      document.getElementById('modalSubmitBtn').textContent = 'Upload & Save Tag';
      document.getElementById('sampleEditModal').classList.remove('hidden');
      if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openEditModal(filename, caption, category) {
      currentUploadFile = null;
      document.getElementById('modalFilename').value = filename;
      document.getElementById('fileUploadGroup').classList.add('hidden');
      document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit-3" class="w-4 h-4 text-[#eac34a]"></i> <span>Edit Photo Tag & Caption</span>';
      document.getElementById('modalCaption').value = caption || '';
      document.getElementById('modalCategory').value = category || 'anniversary';
      document.getElementById('modalSubmitBtn').textContent = 'Save Metadata';
      document.getElementById('sampleEditModal').classList.remove('hidden');
      if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeSampleModal() {
      document.getElementById('sampleEditModal').classList.add('hidden');
      currentUploadFile = null;
      const inp = document.getElementById('sampleFileInput');
      if (inp) inp.value = '';
    }

    async function submitSampleMeta(e) {
      e.preventDefault();
      const filename = document.getElementById('modalFilename').value;
      const caption = document.getElementById('modalCaption').value;
      const category = document.getElementById('modalCategory').value;
      const btn = document.getElementById('modalSubmitBtn');

      btn.disabled = true;
      btn.textContent = 'Saving...';

      try {
        if (currentUploadFile) {
          // UPLOAD NEW FILE
          const compressedWebp = await compressImage(currentUploadFile, 1200, 1200, 0.82, 'image/webp');
          const formData = new FormData();
          formData.append('action', 'upload');
          formData.append('photo_data', compressedWebp);
          formData.append('caption', caption || 'Our Special Moments 💕');
          formData.append('category', category || 'anniversary');

          const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php', {
            method: 'POST', body: formData
          });
          const data = await res.json();
          if (data.status === 'success') {
            closeSampleModal();
            fetchSamples();
          } else {
            alert('⚠️ Upload error: ' + (data.message || 'Unknown error'));
          }
        } else {
          // UPDATE EXISTING FILE TAG & CAPTION
          const formData = new FormData();
          formData.append('action', 'update_caption');
          formData.append('filename', filename);
          formData.append('caption', caption);
          formData.append('category', category);

          const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php', {
            method: 'POST', body: formData
          });
          const data = await res.json();
          if (data.status === 'success') {
            closeSampleModal();
            fetchSamples();
          } else {
            alert('⚠️ Update error: ' + data.message);
          }
        }
      } catch (err) {
        alert('⚠️ Operation failed: ' + err.message);
      } finally {
        btn.disabled = false;
      }
    }

    async function deleteSample(filename) {
      if (!confirm(`Are you sure you want to delete "${filename}"?`)) return;

      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('filename', filename);

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php', {
          method: 'POST', body: formData
        });
        const data = await res.json();
        if (data.status === 'success') {
          fetchSamples();
        } else {
          alert('⚠️ Delete failed: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        alert('⚠️ Delete failed: ' + err.message);
      }
    }

    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        alert('📋 Copied URL to clipboard!');
      });
    }

    function roundKb(bytes) {
      if (!bytes || isNaN(bytes)) return '0.0';
      return (bytes / 1024).toFixed(1);
    }
  </script>
</body>
</html>
