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
<link rel="stylesheet" href="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/css/main.css?v=<?php echo time(); ?>_grid_vertical">

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
    25% { transform: rotate(90deg) scale(1.02); }
    50% { transform: rotate(180deg) scale(1); }
    75% { transform: rotate(270deg) scale(1.02); }
    100% { transform: rotate(360deg) scale(1); }
  }
  .animate-aarti {
    animation: aartiRotate 8s linear infinite;
  }

  @keyframes flameFlicker {
    0%, 100% { transform: scale(1) translateY(0); filter: drop-shadow(0 0 12px #f59e0b) drop-shadow(0 0 25px #ef4444); }
    25% { transform: scale(1.2) translateY(-3px) rotate(-2deg); filter: drop-shadow(0 0 20px #ef4444) drop-shadow(0 0 35px #f59e0b); }
    50% { transform: scale(0.9) translateY(1px) rotate(2deg); filter: drop-shadow(0 0 15px #f59e0b); }
    75% { transform: scale(1.25) translateY(-2px); filter: drop-shadow(0 0 25px #eab308) drop-shadow(0 0 40px #f97316); }
  }
  .animate-flame {
    animation: flameFlicker 1.1s ease-in-out infinite;
  }

  @keyframes tilakGlow {
    0%, 100% { transform: translate(-50%, 0) scale(1); box-shadow: 0 0 15px #ef4444, 0 0 30px #f59e0b; }
    50% { transform: translate(-50%, 0) scale(1.35); box-shadow: 0 0 25px #eab308, 0 0 45px #ef4444; }
  }
  .animate-tilak {
    animation: tilakGlow 1.8s ease-in-out infinite;
  }

  @keyframes thaliGlowPulse {
    0%, 100% { box-shadow: 0 0 40px rgba(234, 195, 74, 0.35), inset 0 0 30px rgba(234, 195, 74, 0.15); }
    50% { box-shadow: 0 0 80px rgba(234, 195, 74, 0.65), inset 0 0 50px rgba(234, 195, 74, 0.3); }
  }
  .animate-thali-glow {
    animation: thaliGlowPulse 3s ease-in-out infinite;
  }

  @keyframes bellSwing {
    0%, 100% { transform: rotate(0deg); }
    20% { transform: rotate(18deg); }
    40% { transform: rotate(-15deg); }
    60% { transform: rotate(10deg); }
    80% { transform: rotate(-6deg); }
  }
  .animate-bell-swing {
    animation: bellSwing 1.2s ease-in-out;
  }

  @keyframes incenseSmoke {
    0% { opacity: 0.2; transform: translateY(0) scaleX(1); }
    50% { opacity: 0.7; transform: translateY(-15px) scaleX(1.4); }
    100% { opacity: 0; transform: translateY(-30px) scaleX(1.8); }
  }
  .animate-incense {
    animation: incenseSmoke 2.5s ease-out infinite;
  }

  @keyframes petalFall {
    0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
    100% { transform: translateY(220px) rotate(360deg); opacity: 0; }
  }
  .animate-petal-fall {
    animation: petalFall 2.8s linear forwards;
  }

  @keyframes wristGlow {
    0%, 100% { filter: drop-shadow(0 0 8px #ffd700); }
    50% { filter: drop-shadow(0 0 20px #ef4444) drop-shadow(0 0 25px #ffd700); }
  }
  .animate-wrist-glow {
    animation: wristGlow 2s ease-in-out infinite;
  }
</style>
