<?php
// Unified Global Reusable Header Component
$current_page = $current_page ?? 'home';
$isAdminPage = $isAdminPage ?? false;
?>
<!-- Navbar (Unified Smart Auto-Hiding Header) -->
<header id="mainHeader" class="fixed top-0 left-0 right-0 w-full z-50 bg-[#151215]/95 backdrop-blur-xl border-b border-[#4d444b]/30 shadow-[0_4px_30px_rgba(0,0,0,0.5)] transition-transform duration-300 ease-in-out transform translate-y-0">
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-3">
    <!-- Brand Logo -->
    <a href="<?php echo APP_URL; ?>" class="flex items-center gap-2.5 text-left group shrink-0">
      <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] shadow-[0_0_15px_rgba(234,195,74,0.3)] group-hover:scale-105 transition-transform duration-300">
        <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
          <i data-lucide="heart" class="w-4 h-4 text-[#eac34a] fill-[#eac34a]/30 group-hover:fill-[#eac34a] transition-colors"></i>
        </div>
      </div>
      <div class="flex flex-col">
        <span class="text-xl sm:text-2xl font-bold tracking-wide text-[#e8e0e3] font-serif group-hover:text-[#eac34a] transition-colors leading-none">
          <?php echo defined('APP_NAME') ? APP_NAME : 'GiftReveal'; ?>
        </span>
        <span class="hidden sm:block text-[9px] uppercase tracking-[0.2em] text-[#eac34a] font-semibold mt-0.5 font-sans">
          <?php echo $isAdminPage ? 'Admin Control Panel' : 'Romantic Surprise Websites'; ?>
        </span>
      </div>
    </a>

    <!-- Center Navigation (Desktop) -->
    <nav class="hidden md:flex items-center gap-8 font-sans text-xs uppercase tracking-[0.15em] font-semibold">
      <a href="<?php echo APP_URL; ?>" class="<?php echo $current_page === 'home' ? 'text-[#eac34a] border-b-2 border-[#eac34a] font-bold' : 'text-[#d0c3cb] border-b-2 border-transparent hover:text-[#e4b9df]'; ?> py-1 transition-colors">
        Home
      </a>
      <a href="<?php echo APP_URL; ?>/#gallery" class="text-[#d0c3cb] border-b-2 border-transparent hover:text-[#e4b9df] transition-colors py-1">
        Templates &amp; Pricing
      </a>
      <a href="<?php echo APP_URL; ?>/#gallery" class="text-[#eac34a] hover:text-[#ffe088] flex items-center gap-1.5 transition-all py-1 border-b-2 border-transparent hover:border-[#eac34a]">
        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#eac34a]"></i>
        <span>Live Demo</span>
      </a>
    </nav>

    <!-- Right Action Controls (Desktop) -->
    <div class="hidden md:flex items-center gap-3 shrink-0">
      <?php require __DIR__ . '/user_avatar_nav.php'; ?>
    </div>

    <!-- Mobile Controls (Hamburger Menu Button & Quick CTA / Avatar) -->
    <div class="flex md:hidden items-center gap-2">
      <?php 
      $isMobileBuyerSession = !empty($_SESSION['buyer_token']) || !empty($_SESSION['edit_token']) || !empty($_SESSION['buyer_page_id']) || !empty($_SESSION['buyer_email']);
      $mobileBuyerName = !empty($_SESSION['buyer_name']) ? trim($_SESSION['buyer_name']) : 'Buyer';
      $mobileBuyerInitial = strtoupper(substr($mobileBuyerName, 0, 1));
      $mobileBuyerSlug = !empty($_SESSION['buyer_slug']) ? $_SESSION['buyer_slug'] : '';
      ?>
      <?php if ($isMobileBuyerSession): ?>
        <a href="<?php echo APP_URL; ?>/edit.php" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/60 shadow-[0_0_12px_rgba(234,195,74,0.2)]">
          <div class="w-5 h-5 rounded-full bg-gradient-to-tr from-[#eac34a] to-[#d4af37] text-[#151215] flex items-center justify-center font-bold text-[10px] shadow-inner">
            <?php echo htmlspecialchars($mobileBuyerInitial); ?>
          </div>
          <span class="text-[11px] font-bold font-serif max-w-[70px] truncate text-[#e8e0e3]"><?php echo htmlspecialchars($mobileBuyerName); ?></span>
        </a>
      <?php else: ?>
        <a href="<?php echo APP_URL; ?>/#gallery" class="px-3 py-1.5 rounded-full bg-[#eac34a] text-[#241a00] text-[11px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-md">
          <i data-lucide="gift" class="w-3 h-3"></i>
          <span>Create</span>
        </a>
      <?php endif; ?>

      <button onclick="toggleMobileNavMenu()" id="hamburgerBtn" aria-label="Toggle Menu" class="p-2 rounded-xl bg-[#221f21] border border-[#4d444b] text-[#e8e0e3] hover:text-[#eac34a] hover:border-[#eac34a] transition-all cursor-pointer">
        <i data-lucide="menu" id="hamburgerIcon" class="w-5 h-5"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Drawer Overlay Menu -->
  <div id="mobileDrawerMenu" class="hidden md:hidden bg-[#151215]/98 border-b border-[#4d444b]/50 px-4 py-5 space-y-4 shadow-2xl backdrop-blur-2xl transition-all duration-300">
    <?php if ($isMobileBuyerSession): ?>
      <div class="p-3.5 rounded-2xl bg-[#221f21] border border-[#eac34a]/40 flex items-center justify-between gap-3 shadow-lg">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#eac34a] to-[#d4af37] text-[#151215] flex items-center justify-center font-bold text-sm shadow-inner shrink-0">
            <?php echo htmlspecialchars($mobileBuyerInitial); ?>
          </div>
          <div class="flex flex-col text-left min-w-0 truncate">
            <span class="text-xs font-bold font-serif text-[#e8e0e3] truncate"><?php echo htmlspecialchars($mobileBuyerName); ?></span>
            <span class="text-[9px] text-[#eac34a] font-semibold uppercase tracking-wider">Buyer Portal Active</span>
          </div>
        </div>
        <a href="<?php echo APP_URL; ?>/edit.php?logout=1" class="text-rose-400 hover:text-rose-300 px-2.5 py-1.5 bg-rose-950/30 hover:bg-rose-950/60 rounded-xl text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 border border-rose-500/20 shrink-0">
          <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
          <span>Logout</span>
        </a>
      </div>
    <?php endif; ?>

    <nav class="flex flex-col space-y-2.5 font-sans text-xs uppercase tracking-wider font-semibold">
      <a href="<?php echo APP_URL; ?>" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#3b1e3b]/60 text-[#eac34a] font-bold border border-[#eac34a]/30 flex items-center gap-2.5">
        <i data-lucide="home" class="w-4 h-4"></i>
        <span>Home</span>
      </a>
      <a href="<?php echo APP_URL; ?>/#gallery" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] flex items-center gap-2.5">
        <i data-lucide="layout" class="w-4 h-4"></i>
        <span>Templates &amp; Pricing</span>
      </a>
      <a href="<?php echo APP_URL; ?>/#gallery" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#221f21] text-[#eac34a] border border-[#4d444b] flex items-center gap-2.5">
        <i data-lucide="sparkles" class="w-4 h-4"></i>
        <span>Live Demo</span>
      </a>
      <?php if ($isMobileBuyerSession): ?>
        <a href="<?php echo APP_URL; ?>/edit.php" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/60 font-bold flex items-center gap-2.5">
          <i data-lucide="edit-3" class="w-4 h-4"></i>
          <span>Manage &amp; Edit My Surprise</span>
        </a>
        <?php if ($mobileBuyerSlug): ?>
          <a href="<?php echo APP_URL; ?>/gift/<?php echo urlencode($mobileBuyerSlug); ?>" target="_blank" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#221f21] text-[#a4e4b9] border border-[#a4e4b9]/40 font-bold flex items-center gap-2.5">
            <i data-lucide="external-link" class="w-4 h-4"></i>
            <span>View Live Gift Link ↗</span>
          </a>
        <?php endif; ?>
      <?php else: ?>
        <a href="<?php echo APP_URL; ?>/edit.php" onclick="toggleMobileNavMenu()" class="px-4 py-3 rounded-xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/60 font-bold flex items-center gap-2.5">
          <i data-lucide="key-round" class="w-4 h-4"></i>
          <span>Buyer Login Portal</span>
        </a>
      <?php endif; ?>
    </nav>

    <div class="pt-2 border-t border-[#4d444b]/40">
      <a href="<?php echo APP_URL; ?>/#gallery" onclick="toggleMobileNavMenu()" class="w-full py-3 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] rounded-xl font-bold text-xs uppercase tracking-wider shadow-lg flex items-center justify-center gap-2">
        <i data-lucide="gift" class="w-4 h-4"></i>
        <span>Create Personalized Surprise</span>
      </a>
    </div>
  </div>
</header>

<!-- Smart Unified Header JavaScript -->
<script>
  function toggleMobileNavMenu() {
    const menu = document.getElementById('mobileDrawerMenu');
    const icon = document.getElementById('hamburgerIcon');
    if (menu) {
      menu.classList.toggle('hidden');
      const isOpen = !menu.classList.contains('hidden');
      if (icon) {
        icon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
        if (typeof lucide === 'object') lucide.createIcons();
      }
    }
  }

  function initHeaderIcons() {
    if (typeof lucide === 'object' && typeof lucide.createIcons === 'function') {
      lucide.createIcons();
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeaderIcons);
  } else {
    initHeaderIcons();
  }
  setTimeout(initHeaderIcons, 100);
  setTimeout(initHeaderIcons, 500);

  (function() {
    let lastScrollY = window.scrollY;
    const header = document.getElementById('mainHeader');
    const scrollThreshold = 5;

    if (!header) return;

    window.addEventListener('scroll', () => {
      const currentScrollY = window.scrollY;
      const mobileDrawer = document.getElementById('mobileDrawerMenu');
      const isMobileMenuOpen = mobileDrawer && !mobileDrawer.classList.contains('hidden');

      if (currentScrollY <= 60 || isMobileMenuOpen) {
        header.classList.remove('-translate-y-full');
        lastScrollY = currentScrollY;
        return;
      }

      if (Math.abs(currentScrollY - lastScrollY) < scrollThreshold) {
        return;
      }

      if (currentScrollY > lastScrollY) {
        header.classList.add('-translate-y-full');
      } else if (currentScrollY < lastScrollY) {
        header.classList.remove('-translate-y-full');
      }

      lastScrollY = currentScrollY;
    }, { passive: true });
  })();
</script>
