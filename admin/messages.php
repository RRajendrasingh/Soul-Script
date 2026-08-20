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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Inquiries &amp; Messages — <?php echo APP_NAME; ?> Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #120f12; color: #e8e0e3; }
    .font-serif { font-family: 'Cinzel', serif; }
  </style>
</head>
<body class="min-h-screen pb-16">

  <!-- Header Banner -->
  <header class="bg-[#1b171b]/95 border-b border-[#3b1e3b] sticky top-0 z-40 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-4 py-3.5 sm:py-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
      <div class="flex items-center gap-3">
        <a href="index.php" class="p-2 rounded-xl bg-[#241c24] text-[#eac34a] hover:bg-[#3b1e3b] transition-all flex items-center justify-center border border-[#eac34a]/20 shrink-0">
          <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div class="min-w-0">
          <h1 class="text-base sm:text-xl font-bold font-serif text-[#e8e0e3] flex items-center gap-2 truncate">
            <i data-lucide="mail" class="w-4 h-4 sm:w-5 sm:h-5 text-[#eac34a] shrink-0"></i>
            <span class="truncate">Customer Inquiries (<?php echo $totalCount; ?>)</span>
          </h1>
          <p class="text-[11px] text-[#d0c3cb] hidden sm:block">View and reply to messages sent via website contact form</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <span class="px-3 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 text-xs font-bold">
          <?php echo $newCount; ?> New Messages
        </span>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-6">

    <!-- Admin Navigation Bar -->
    <?php require_once __DIR__ . '/nav_header.php'; ?>

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
                    <span class="px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-500/40 text-[10px] font-bold uppercase tracking-wider">New</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 rounded-full bg-gray-800 text-gray-400 text-[10px] font-semibold uppercase">Replied</span>
                  <?php endif; ?>
                </div>
                <div class="text-xs text-[#d0c3cb] flex flex-wrap items-center gap-3">
                  <span class="text-[#eac34a] font-medium"><?php echo htmlspecialchars($msg['email']); ?></span>
                  <span>•</span>
                  <span><?php echo date('j M Y, h:i A', strtotime($msg['created_at'])); ?></span>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <a href="mailto:<?php echo urlencode($msg['email']); ?>?subject=<?php echo urlencode('Re: ' . ($msg['subject'] ?? 'Inquiry on ' . APP_NAME)); ?>" class="px-3.5 py-1.5 rounded-xl bg-[#eac34a] text-[#241a00] font-bold text-xs hover:bg-[#ffe088] transition-all flex items-center gap-1.5 shadow-md">
                  <i data-lucide="reply" class="w-3.5 h-3.5"></i>
                  <span>Reply Email</span>
                </a>
                <a href="messages.php?action=toggle&id=<?php echo $msg['id']; ?>" class="px-3 py-1.5 rounded-xl bg-[#241c24] border border-[#4d444b] text-[#d0c3cb] hover:text-[#e8e0e3] text-xs font-semibold transition-all">
                  <?php echo ($msg['status'] ?? 'new') === 'new' ? 'Mark Replied' : 'Mark New'; ?>
                </a>
                <a href="messages.php?action=delete&id=<?php echo $msg['id']; ?>" onclick="return confirm('Are you sure you want to delete this message?');" class="p-1.5 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 hover:bg-rose-900/60 text-xs transition-all" title="Delete">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </a>
              </div>
            </div>

            <!-- Message Subject & Body -->
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

  </main>

  <script>
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  </script>
</body>
</html>
