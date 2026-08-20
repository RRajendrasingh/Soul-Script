<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/voucher_helper.php';

$loginError = '';
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: " . APP_URL . "/admin/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_user'], $_POST['admin_pass'])) {
    if ($_POST['admin_user'] === ADMIN_USER && $_POST['admin_pass'] === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $loginError = 'Invalid admin username or password.';
    }
}

$isLoggedIn = !empty($_SESSION['admin_logged_in']);
$msg = '';

if ($isLoggedIn) {
    $db = getDB();

    // Handle Delete Action
    if (isset($_GET['delete_id'])) {
        $delStmt = $db->prepare("DELETE FROM affiliate_products WHERE id = ?");
        $delStmt->execute([intval($_GET['delete_id'])]);
        $msg = "✅ Affiliate product removed successfully!";
    }

    // Handle Add New Product Action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'Rakhi Gift');
        $priceText = trim($_POST['price_text'] ?? '₹499');
        $imageUrl = trim($_POST['image_url'] ?? '');
        $affiliateUrl = trim($_POST['affiliate_url'] ?? '');

        if (!empty($title) && !empty($affiliateUrl)) {
            $ins = $db->prepare("INSERT INTO affiliate_products (title, category, price_text, image_url, affiliate_url) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([$title, $category, $priceText, $imageUrl, $affiliateUrl]);
            $msg = "✅ New Amazon Affiliate Product added successfully!";
        } else {
            $msg = "❌ Product Title and Amazon Affiliate URL are required.";
        }
    }

    // Fetch All Affiliate Products
    $products = $db->query("SELECT * FROM affiliate_products ORDER BY sort_order ASC, id DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Amazon Affiliate Store Manager — ' . APP_NAME;
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <?php 
  $current_page = 'admin';
  $isAdminPage = true;
  require_once __DIR__ . '/../includes/header.php'; 
  ?>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-20 relative z-10 space-y-8">

    <?php if (!$isLoggedIn): ?>
      <!-- LOGIN SCREEN -->
      <div class="max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-6 text-center">
        <div class="w-14 h-14 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
          <i data-lucide="shopping-bag" class="w-7 h-7"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Admin Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">Amazon Affiliate Product Recommendations Control</p>
        </div>
        <form method="POST" class="space-y-4 text-left">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Username</label>
            <input type="text" name="admin_user" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Password</label>
            <input type="password" name="admin_pass" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <button type="submit" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer">
            Log In To Affiliate Store
          </button>
        </form>
      </div>

    <?php else: ?>
      <?php require_once __DIR__ . '/nav_header.php'; ?>

      <!-- Level 3: Standard Action Hero Card -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#221f21]/80 backdrop-blur-md p-6 rounded-3xl border border-[#4d444b] shadow-2xl">
        <div>
          <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-[#eac34a] mb-1">
            <i data-lucide="shield-check" class="w-4 h-4"></i> Double Earnings Engine
          </div>
          <h1 class="text-2xl sm:text-3xl font-bold font-serif text-[#e8e0e3]">🛒 Amazon Affiliate Store Products</h1>
          <p class="text-xs sm:text-sm text-[#d0c3cb] mt-1">Add curated Amazon gifts shown under recipient Amazon Vouchers to earn extra 6-10% commissions.</p>
        </div>
      </div>

      <?php if ($msg): ?>
        <div class="p-4 rounded-2xl text-xs font-bold bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9]">
          <?php echo htmlspecialchars($msg); ?>
        </div>
      <?php endif; ?>

      <!-- ADD NEW PRODUCT FORM -->
      <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/40 shadow-2xl space-y-5">
        <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Add New Amazon Affiliate Gift Item</h3>

        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="add_product">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Product Title</label>
              <input type="text" name="title" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Cadbury Celebrations Luxury Pack" required>
            </div>
            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Category Badge</label>
              <input type="text" name="category" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Chocolates 🍫 or Watches ⌚">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Price Text</label>
              <input type="text" name="price_text" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. ₹499 or ₹1,495">
            </div>
            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Product Image URL</label>
              <input type="url" name="image_url" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="https://images.unsplash.com/... or Amazon Image Link">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Your Amazon Affiliate Link (with tag)</label>
            <input type="url" name="affiliate_url" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none font-mono" placeholder="https://www.amazon.in/dp/B0757FG9X6?tag=yoursoulscript-21" required>
          </div>

          <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#eac34a] via-[#f7d774] to-[#cca830] hover:brightness-110 text-[#241a00] font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all cursor-pointer flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Affiliate Product to Showcase</span>
          </button>
        </form>
      </div>

      <!-- ACTIVE PRODUCTS TABLE -->
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b]/40 shadow-xl space-y-4 overflow-x-auto">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">Active Amazon Affiliate Showcase Items</h3>
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#4d444b]/40 text-[#d0c3cb] font-extrabold">
              <th class="py-3 px-3">Title</th>
              <th class="py-3 px-3">Category</th>
              <th class="py-3 px-3">Price</th>
              <th class="py-3 px-3">Affiliate Link</th>
              <th class="py-3 px-3">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#4d444b]/20">
            <?php foreach ($products as $p): ?>
              <tr class="hover:bg-[#151215]/50 transition-all">
                <td class="py-3 px-3 font-bold text-[#e8e0e3]"><?php echo htmlspecialchars($p['title']); ?></td>
                <td class="py-3 px-3 text-[#eac34a]"><?php echo htmlspecialchars($p['category']); ?></td>
                <td class="py-3 px-3"><?php echo htmlspecialchars($p['price_text']); ?></td>
                <td class="py-3 px-3 font-mono text-[#d0c3cb]/80 truncate max-w-[200px]">
                  <a href="<?php echo htmlspecialchars($p['affiliate_url']); ?>" target="_blank" class="underline hover:text-[#eac34a]">
                    <?php echo htmlspecialchars($p['affiliate_url']); ?>
                  </a>
                </td>
                <td class="py-3 px-3">
                  <a href="?delete_id=<?php echo $p['id']; ?>" onclick="return confirm('Remove this product?');" class="text-rose-400 hover:underline font-bold">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <!-- Universal Admin Footer -->
    <?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
  </main>
</body>
</html>
