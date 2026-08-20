<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$isAdminLoggedIn = !empty($_SESSION['admin_logged_in']);

if (!$isAdminLoggedIn) {
    header("Location: index.php");
    exit;
}

$db = getDB();

// Ensure table exists
$db->exec("CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Handle Actions (Delete or Toggle Status)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $msgId  = intval($_GET['id'] ?? 0);

    if ($action === 'delete' && $msgId > 0) {
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$msgId]);
        header("Location: messages.php?msg=deleted");
        exit;
    } elseif ($action === 'toggle' && $msgId > 0) {
        $stmt = $db->prepare("UPDATE contact_messages SET status = IF(status = 'new', 'replied', 'new') WHERE id = ?");
        $stmt->execute([$msgId]);
        header("Location: messages.php?msg=updated");
        exit;
    }
}

// Fetch all messages
$stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
$totalCount = count($messages);
$newCount = count(array_filter($messages, fn($m) => ($m['status'] ?? 'new') === 'new'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $page_title = 'Customer Inquiries & Messages — ' . (defined('APP_NAME') ? APP_NAME : 'GiftReveal') . ' Admin';
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] min-h-screen relative overflow-x-hidden font-sans selection:bg-[#eac34a] selection:text-[#151215]">
  <!-- Ambient Luxury Background Glows -->
  <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-gradient-to-b from-[#3b1e3b]/30 via-[#221f21]/20 to-transparent blur-[120px] pointer-events-none z-0"></div>

  <?php 
  $current_page = 'admin';
  $isAdminPage = true;
  require_once __DIR__ . '/../includes/header.php'; 
  ?>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-20 relative z-10 space-y-8">
    <?php require_once __DIR__ . '/nav_header.php'; ?>

    <!-- Level 3: Standard Action Hero Card -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#221f21]/80 backdrop-blur-md p-6 rounded-3xl border border-[#4d444b] shadow-2xl">
      <div>
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-[#eac34a] mb-1">
          <i data-lucide="shield-check" class="w-4 h-4"></i> Admin Panel
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
          <span>Customer Inquiries</span>
          <span class="text-sm font-sans px-2.5 py-0.5 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 font-bold"><?php echo $totalCount; ?></span>
        </h1>
        <p class="text-xs sm:text-sm text-[#d0c3cb] mt-1">View, track, and reply to messages submitted by users via the website contact form.</p>
      </div>

      <div class="flex items-center gap-2">
        <span class="px-4 py-2 rounded-xl bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40 text-xs font-bold flex items-center gap-1.5 shadow-md">
          <i data-lucide="bell" class="w-4 h-4 text-[#eac34a]"></i>
          <span><?php echo $newCount; ?> Unread New</span>
        </span>
      </div>
    </div>

    <?php if (empty($messages)): ?>
      <div class="p-12 text-center rounded-3xl bg-[#1b171b] border border-[#4d444b]/40 space-y-3">
        <div class="w-16 h-16 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="inbox" class="w-8 h-8"></i>
        </div>
        <h3 class="text-lg font-serif font-bold text-[#e8e0e3]">No Inquiries Yet</h3>
        <p class="text-xs text-[#d0c3cb]">When users submit the Contact form on your website, messages will appear here in real-time.</p>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 gap-4">
        <?php foreach ($messages as $msg): ?>
          <div class="bg-[#1b171b] rounded-2xl border <?php echo ($msg['status'] ?? 'new') === 'new' ? 'border-[#eac34a]/50 shadow-[0_0_20px_rgba(234,195,74,0.1)]' : 'border-[#4d444b]/40'; ?> p-5 sm:p-6 space-y-4 transition-all">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-[#4d444b]/30 pb-3">
              <div class="space-y-1">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-sm text-[#e8e0e3]"><?php echo htmlspecialchars($msg['name']); ?></span>
                  <?php if (($msg['status'] ?? 'new') === 'new'): ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/40">NEW</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#1e3b20] text-[#a4e4b9] border border-[#a4e4b9]/30">REPLIED</span>
                  <?php endif; ?>
                </div>
                <div class="text-xs text-[#b8a7b3] flex items-center gap-3">
                  <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="text-[#e4b9df] hover:underline flex items-center gap-1">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i> <?php echo htmlspecialchars($msg['email']); ?>
                  </a>
                  <span>•</span>
                  <span><?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?></span>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo rawurlencode($msg['subject'] ?? 'Inquiry'); ?>" class="px-3 py-1.5 rounded-xl bg-[#eac34a] text-[#241a00] font-bold text-xs hover:bg-[#ffe088] transition flex items-center gap-1">
                  <i data-lucide="corner-up-left" class="w-3.5 h-3.5"></i> Reply Email
                </a>
                <a href="messages.php?action=toggle&id=<?php echo $msg['id']; ?>" class="px-3 py-1.5 rounded-xl bg-[#221f21] text-[#d0c3cb] border border-[#4d444b] font-semibold text-xs hover:bg-[#3b1e3b] hover:text-[#eac34a] transition">
                  <?php echo ($msg['status'] ?? 'new') === 'new' ? 'Mark Replied' : 'Mark New'; ?>
                </a>
                <a href="messages.php?action=delete&id=<?php echo $msg['id']; ?>" onclick="return confirm('Are you sure you want to delete this message?');" class="p-1.5 rounded-xl bg-rose-950/40 text-rose-300 border border-rose-500/30 hover:bg-rose-900/60 transition">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </a>
              </div>
            </div>

            <div class="space-y-2">
              <div class="text-xs font-bold text-[#ffe088] uppercase tracking-wider">
                Subject: <?php echo htmlspecialchars($msg['subject'] ?? 'General Inquiry'); ?>
              </div>
              <p class="text-xs sm:text-sm text-[#d0c3cb] leading-relaxed whitespace-pre-wrap bg-[#151215] p-4 rounded-xl border border-[#4d444b]/30">
                <?php echo htmlspecialchars($msg['message']); ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Universal Admin Footer -->
    <?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
  </main>

  <script>
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  </script>
</body>
</html>
