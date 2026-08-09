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

  <main class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-16 relative z-10 space-y-8">
    
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
        <input type="file" id="sampleFileInput" accept="image/*" class="hidden" onchange="handleSampleUpload(this.files[0])">

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

  </main>

  <script>
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

        grid.innerHTML = data.samples.map(sample => `
          <div class="bg-[#221f21] rounded-2xl border border-[#4d444b] overflow-hidden shadow-xl hover:border-[#eac34a]/60 transition group flex flex-col justify-between">
            <div class="relative aspect-square bg-black/40 overflow-hidden">
              <img src="${sample.url}" alt="${sample.caption || sample.filename}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
              <span class="absolute top-2 right-2 bg-black/70 backdrop-blur-md text-[10px] font-mono px-2 py-0.5 rounded-full text-[#eac34a]">
                ${sample.size_kb} KB
              </span>
            </div>

            <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
              <div>
                <div class="text-xs font-semibold text-[#e8e0e3] line-clamp-2" title="${sample.caption || 'No Caption'}">
                  💬 ${sample.caption || 'No Caption'}
                </div>
                <div class="text-[10px] font-mono text-[#d0c3cb]/70 truncate mt-1" title="${sample.filename}">
                  ${sample.filename}
                </div>
              </div>

              <div class="space-y-2 pt-2 border-t border-[#3d363d]">
                <button onclick="editCaption('${sample.filename}', '${(sample.caption || '').replace(/'/g, "\\'")}')" class="w-full bg-[#3b1e3b] text-xs font-semibold py-2 px-3 rounded-xl border border-[#e4b9df]/40 hover:bg-[#4d274d] transition flex items-center justify-center gap-1 text-[#e4b9df]">
                  <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Caption
                </button>

                <div class="flex items-center gap-2">
                  <button onclick="copyToClipboard('${sample.url}')" class="flex-1 bg-[#151215] text-xs font-semibold py-2 px-3 rounded-xl border border-[#4d444b] hover:border-[#eac34a] transition flex items-center justify-center gap-1 text-[#eac34a]">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i> Copy URL
                  </button>

                  <button onclick="deleteSample('${sample.filename}')" class="bg-red-950/60 text-red-300 hover:bg-red-900/80 text-xs font-semibold p-2 rounded-xl border border-red-500/40 transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        `).join('');

        if (typeof lucide !== 'undefined') lucide.createIcons();
      } catch (err) {
        grid.innerHTML = `<div class="col-span-full p-6 bg-[#3b1e3b]/50 border border-red-500/40 rounded-2xl text-center text-xs text-red-300">Network error fetching samples.</div>`;
      }
    }

    async function handleSampleUpload(file) {
      if (!file) return;

      const userCaption = prompt('Enter a romantic caption for this sample photo:', 'Together Always 💑');
      if (userCaption === null) return; // User cancelled

      const grid = document.getElementById('sampleGrid');
      grid.innerHTML = `<div class="col-span-full text-center py-12 text-[#d0c3cb] text-sm"><i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto text-[#eac34a] mb-2"></i>Compressing & Uploading WebP Sample...</div>`;
      if (typeof lucide !== 'undefined') lucide.createIcons();

      try {
        const compressedWebp = await compressImage(file, 1200, 1200, 0.82, 'image/webp');

        const formData = new FormData();
        formData.append('action', 'upload');
        formData.append('photo_data', compressedWebp);
        formData.append('caption', userCaption || 'Our Special Moments 💕');

        const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php', {
          method: 'POST',
          body: formData
        });

        const data = await res.json();
        if (data.status === 'success') {
          alert('✅ Sample WebP photo and caption uploaded successfully!');
        } else {
          alert('⚠️ Upload error: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        alert('⚠️ Upload failed: ' + err.message);
      } finally {
        fetchSamples();
      }
    }

    async function editCaption(filename, currentCaption) {
      const newCaption = prompt(`Edit caption for "${filename}":`, currentCaption);
      if (newCaption === null) return;

      const formData = new FormData();
      formData.append('action', 'update_caption');
      formData.append('filename', filename);
      formData.append('caption', newCaption);

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (data.status === 'success') {
          fetchSamples();
        } else {
          alert('⚠️ Failed updating caption: ' + data.message);
        }
      } catch (err) {
        alert('⚠️ Failed updating caption: ' + err.message);
      }
    }

    async function deleteSample(filename) {
      if (!confirm(`Are you sure you want to delete "${filename}"?`)) return;

      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('filename', filename);

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php', {
          method: 'POST',
          body: formData
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
  </script>
</body>
</html>
