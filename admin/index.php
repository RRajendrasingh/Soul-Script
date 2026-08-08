<?php
session_start();
require_once __DIR__ . '/../config/config.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nofollow">
  <title>Admin Dashboard — <?php echo APP_NAME; ?></title>
  
  <!-- Google Fonts Preconnect & Preload (Eliminates FOUT / Font Jump) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=block">
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=block" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            serif: ['"Bodoni Moda"', 'serif'],
            sans: ['Montserrat', 'sans-serif'],
          }
        }
      }
    }
  </script>
  
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Unified Global Navbar -->
  <?php 
  $current_page = 'admin';
  $isAdminPage = true;
  require_once __DIR__ . '/../includes/header.php'; 
  ?>

  <main class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-12 relative z-10 space-y-8">
    <?php if (!$isLoggedIn): ?>
      <!-- Admin Login Form Card -->
      <div class="max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#4d444b] shadow-2xl space-y-6 text-left">
        <div class="text-center space-y-2">
          <div class="w-12 h-12 rounded-full bg-[#3b1e3b] border border-[#e4b9df]/40 mx-auto flex items-center justify-center">
            <i data-lucide="shield-check" class="w-6 h-6 text-[#eac34a]"></i>
          </div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Admin Authentication</h2>
          <p class="text-xs text-[#d0c3cb]">Enter your administrator credentials to access the dashboard.</p>
        </div>

        <?php if ($loginError): ?>
          <div class="p-3 bg-[#3b1e3b] border border-[#e4b9df]/50 rounded-xl text-xs text-[#e4b9df] text-center font-semibold">
            <?php echo htmlspecialchars($loginError); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#d0c3cb] mb-1.5">Username</label>
            <input type="text" name="admin_user" required class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="admin">
          </div>
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#d0c3cb] mb-1.5">Password</label>
            <input type="password" name="admin_pass" required class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="••••••••">
          </div>
          <button type="submit" class="w-full py-3 rounded-xl bg-[#eac34a] text-[#241a00] font-bold uppercase tracking-wider text-xs shadow-lg hover:bg-[#ffe088] transition-all">
            Login to Admin
          </button>
        </form>
      </div>
    <?php else: ?>
      <!-- Header Title -->
      <div>
        <h1 class="text-3xl font-bold font-serif text-[#e8e0e3]">Orders &amp; Page Management</h1>
        <p class="text-xs text-[#d0c3cb] mt-1">Real-time overview of all customer orders, generated surprise reveal links, and proposal responses.</p>
      </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b] space-y-2">
        <span class="text-[10px] uppercase tracking-widest text-[#d0c3cb] font-bold">Total Revenue</span>
        <div class="text-3xl font-bold font-serif text-[#eac34a]" id="statRevenue">₹0</div>
      </div>

      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b] space-y-2">
        <span class="text-[10px] uppercase tracking-widest text-[#d0c3cb] font-bold">Total Orders</span>
        <div class="text-3xl font-bold font-serif text-[#e8e0e3]" id="statOrders">0</div>
      </div>

      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b] space-y-2">
        <span class="text-[10px] uppercase tracking-widest text-[#d0c3cb] font-bold">Paid Orders</span>
        <div class="text-3xl font-bold font-serif text-[#e4b9df]" id="statPaid">0</div>
      </div>

      <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b] space-y-2">
        <span class="text-[10px] uppercase tracking-widest text-[#d0c3cb] font-bold">Live Reveal Pages</span>
        <div class="text-3xl font-bold font-serif text-[#eac34a]" id="statLive">0</div>
      </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-[#221f21] p-6 rounded-3xl border border-[#4d444b] flex flex-col sm:flex-row gap-4 justify-between items-center">
      <div class="flex gap-4 w-full sm:w-auto flex-1 max-w-xl">
        <input type="text" id="searchInput" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] placeholder-[#d0c3cb]/50 focus:border-[#eac34a] focus:outline-none" placeholder="Search by buyer name, email, or slug..." onkeyup="fetchOrders()">
        
        <select id="statusFilter" class="bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" onchange="fetchOrders()">
          <option value="">All Statuses</option>
          <option value="paid">Paid Orders</option>
          <option value="pending">Pending Orders</option>
          <option value="live">Live Pages</option>
          <option value="expired">Expired Pages</option>
        </select>
      </div>

      <button onclick="fetchOrders()" class="px-4 py-2.5 rounded-xl bg-[#151215] border border-[#4d444b] text-xs font-semibold text-[#eac34a] hover:border-[#eac34a] flex items-center gap-1.5">
        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
        <span>Refresh</span>
      </button>
    </div>

    <!-- Orders Table -->
    <div class="bg-[#221f21] rounded-3xl border border-[#4d444b] overflow-x-auto shadow-2xl">
      <table class="w-full text-left text-xs text-[#d0c3cb]">
        <thead class="bg-[#100d10] text-[#e8e0e3] uppercase text-[10px] tracking-wider border-b border-[#4d444b]">
          <tr>
            <th class="p-4">Order ID &amp; Date</th>
            <th class="p-4">Buyer Details</th>
            <th class="p-4">Template &amp; Amount</th>
            <th class="p-4">Secret URL Slug</th>
            <th class="p-4">Payment</th>
            <th class="p-4">Proposal Response</th>
            <th class="p-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody id="ordersTableBody" class="divide-y divide-[#4d444b]/40">
          <tr><td colspan="7" class="p-8 text-center text-[#d0c3cb]/60">Loading order records...</td></tr>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </main>

  <script>
    lucide.createIcons();

    async function fetchOrders() {
      const search = document.getElementById('searchInput').value;
      const status = document.getElementById('statusFilter').value;
      const tbody = document.getElementById('ordersTableBody');

      try {
        const res = await fetch(`<?php echo APP_URL; ?>/api/admin.php?action=list&search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`);
        const data = await res.json();

        if (data.success) {
          document.getElementById('statRevenue').innerText = '₹' + (data.stats.total_revenue || 0);
          document.getElementById('statOrders').innerText = data.stats.total_orders || 0;
          document.getElementById('statPaid').innerText = data.stats.paid_orders || 0;
          document.getElementById('statLive').innerText = data.stats.live_pages || 0;

          const orders = data.orders || [];
          if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-[#d0c3cb]/60">No matching orders found.</td></tr>';
            return;
          }

          tbody.innerHTML = orders.map(o => `
            <tr class="hover:bg-[#151215]/50 transition-colors">
              <td class="p-4">
                <span class="font-mono font-bold text-[#e8e0e3]">${o.order_id}</span><br>
                <span class="text-[10px] text-[#d0c3cb]/60">${new Date(o.created_at).toLocaleDateString()}</span>
              </td>
              <td class="p-4">
                <strong class="text-[#e8e0e3]">${o.buyer_name}</strong><br>
                <span class="text-[11px] text-[#d0c3cb]/80">${o.buyer_email}</span><br>
                <span class="text-[11px] text-[#d0c3cb]/80">${o.buyer_phone}</span>
              </td>
              <td class="p-4">
                <span class="px-2.5 py-0.5 rounded-full bg-[#3b1e3b] text-[#e4b9df] text-[10px] font-bold uppercase tracking-wider">${o.template_name || o.template_id}</span><br>
                <span class="font-serif font-bold text-[#eac34a] text-sm mt-1 inline-block">₹${o.amount_paid}</span>
              </td>
              <td class="p-4">
                ${o.url_slug ? `
                  <a href="${data.app_url}/gift/${o.url_slug}" target="_blank" class="text-[#eac34a] font-mono hover:underline font-semibold">
                    /gift/${o.url_slug} ↗
                  </a>
                ` : '<span class="text-[#d0c3cb]/40 italic">Not generated</span>'}
              </td>
              <td class="p-4">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${o.payment_status === 'paid' ? 'bg-[#221f21] text-[#e4b9df] border border-[#e4b9df]/40' : 'bg-[#3b1e3b] text-[#eac34a]'}">
                  ${o.payment_status}
                </span>
                ${o.payment_status === 'pending' ? `
                  <button onclick="simulatePayment('${o.order_id}')" class="mt-1.5 block text-[10px] text-[#eac34a] underline">Mark Paid</button>
                ` : ''}
              </td>
              <td class="p-4">
                ${o.proposal_response ? `
                  <span class="px-2.5 py-0.5 rounded-full bg-[#3b1e3b] text-[#eac34a] text-[10px] font-bold uppercase">
                    ${o.proposal_response.toUpperCase() === 'YES' ? '💍 YES!' : '💬 Let\'s Talk'}
                  </span>
                  ${o.proposal_note ? `<p class="text-[10px] italic text-[#d0c3cb] mt-1">"${o.proposal_note}"</p>` : ''}
                ` : '<span class="text-[#d0c3cb]/40">—</span>'}
              </td>
              <td class="p-4 text-right">
                <div class="flex flex-wrap items-center justify-end gap-1.5">
                  ${o.page_id ? `
                    <button onclick="deletePage('${o.page_id}')" class="px-2.5 py-1 rounded-lg bg-[#3b1e3b]/50 border border-[#e4b9df]/30 text-[10px] font-semibold text-[#e4b9df] hover:border-[#e4b9df] hover:bg-[#3b1e3b] transition-colors cursor-pointer flex items-center gap-1" title="Delete Generated Surprise Page">
                      <i data-lucide="file-x" class="w-3 h-3"></i>
                      <span>Delete Page</span>
                    </button>
                  ` : ''}
                  <button onclick="deleteOrder('${o.order_id}')" class="px-2.5 py-1 rounded-lg bg-rose-950/40 border border-rose-500/30 text-[10px] font-semibold text-rose-300 hover:border-rose-400 hover:bg-rose-900/60 hover:text-white transition-colors cursor-pointer flex items-center gap-1" title="Delete Entire Order Record">
                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                    <span>Delete Entry</span>
                  </button>
                </div>
              </td>
            </tr>
          `).join('');
          lucide.createIcons();
        }
      } catch (err) {
        console.error(err);
      }
    }

    async function simulatePayment(orderId) {
      if (!confirm("Mark order " + orderId + " as PAID?")) return;
      const formData = new FormData();
      formData.append('action', 'simulate_payment');
      formData.append('order_id', orderId);
      await fetch('<?php echo APP_URL; ?>/api/admin.php', { method: 'POST', body: formData });
      fetchOrders();
    }

    async function deletePage(pageId) {
      if (!confirm("Permanently delete this page?")) return;
      const formData = new FormData();
      formData.append('action', 'delete_page');
      formData.append('page_id', pageId);
      await fetch('<?php echo APP_URL; ?>/api/admin.php', { method: 'POST', body: formData });
      fetchOrders();
    }

    async function deleteOrder(orderId) {
      if (!confirm("Permanently delete order entry " + orderId + " and all associated data from database?")) return;
      const formData = new FormData();
      formData.append('action', 'delete_order');
      formData.append('order_id', orderId);
      await fetch('<?php echo APP_URL; ?>/api/admin.php', { method: 'POST', body: formData });
      fetchOrders();
    }

    function copyLink(link) {
      navigator.clipboard.writeText(link);
      alert("Edit link copied to clipboard:\n" + link);
    }

    fetchOrders();

    // Smart Smooth Auto-Hiding Header Script
    (function() {
      let lastScrollY = window.scrollY;
      const header = document.getElementById('adminHeader');
      const scrollThreshold = 5;

      if (!header) return;

      window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        // Always show header near top of page (0 to 60px)
        if (currentScrollY <= 60) {
          header.classList.remove('-translate-y-full');
          lastScrollY = currentScrollY;
          return;
        }

        // Ignore micro scroll jitter
        if (Math.abs(currentScrollY - lastScrollY) < scrollThreshold) {
          return;
        }

        if (currentScrollY > lastScrollY) {
          // Scrolling Down -> Smoothly Hide Header
          header.classList.add('-translate-y-full');
        } else if (currentScrollY < lastScrollY) {
          // Scrolling Up -> Smoothly Reveal Header
          header.classList.remove('-translate-y-full');
        }

        lastScrollY = currentScrollY;
      }, { passive: true });
    })();
  </script>
</body>
</html>
