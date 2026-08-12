<?php
// SoulScript — Unified Reusable Head Component
// Ensures consistent meta tags, high-performance font preloading (zero FOUT),
// Tailwind CSS configuration, design system tokens, and Lucide icons.

$pageTitle = $pageTitle ?? (defined('APP_NAME') ? APP_NAME . ' — Romantic Surprise Websites' : 'SoulScript');
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="googlebot" content="noindex, nofollow">
<title><?php echo htmlspecialchars($pageTitle); ?></title>

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/favicon.svg">
<link rel="shortcut icon" type="image/svg+xml" href="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/favicon.svg">
<link rel="apple-touch-icon" href="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/favicon.svg">

<!-- Google Fonts Preconnect & Preload (Eliminates FOUT / Font Jump) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Caveat:wght@600;700&display=block">
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Caveat:wght@600;700&display=block" rel="stylesheet">

<!-- Tailwind CSS CDN & Design System Font Family Mapping -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          serif: ['"Bodoni Moda"', 'serif'],
          sans: ['Montserrat', 'sans-serif'],
          handwriting: ['Caveat', 'cursive'],
        }
      }
    }
  }
</script>

<!-- Global CSS Design Tokens & Layout Rules (With Cache-Busting) -->
<link rel="stylesheet" href="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/css/main.css?v=<?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.5'; ?>">

<!-- Client-Side Image Compressor & Lucide Icons -->
<script src="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/js/compressor.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<style>
  html, body {
    overflow-x: hidden !important;
    width: 100% !important;
    max-width: 100vw !important;
    position: relative;
  }

  @keyframes aartiRotate {
    0% { transform: rotate(0deg) scale(1); }
    25% { transform: rotate(90deg) scale(1.03); }
    50% { transform: rotate(180deg) scale(1); }
    75% { transform: rotate(270deg) scale(1.03); }
    100% { transform: rotate(360deg) scale(1); }
  }
  .animate-aarti {
    animation: aartiRotate 6s linear infinite;
  }

  @keyframes flameFlicker {
    0%, 100% { transform: scale(1) translateY(0); opacity: 1; filter: drop-shadow(0 0 10px #f59e0b); }
    25% { transform: scale(1.15) translateY(-2px); opacity: 0.9; filter: drop-shadow(0 0 18px #ef4444); }
    50% { transform: scale(0.95) translateY(1px); opacity: 1; filter: drop-shadow(0 0 12px #f59e0b); }
    75% { transform: scale(1.2) translateY(-1px); opacity: 0.95; filter: drop-shadow(0 0 20px #eab308); }
  }
  .animate-flame {
    animation: flameFlicker 1.2s ease-in-out infinite;
  }

  @keyframes tilakGlow {
    0%, 100% { transform: translate(-50%, 0) scale(1); box-shadow: 0 0 12px #ef4444; }
    50% { transform: translate(-50%, 0) scale(1.3); box-shadow: 0 0 22px #eab308; }
  }
  .animate-tilak {
    animation: tilakGlow 2s ease-in-out infinite;
  }
</style>
