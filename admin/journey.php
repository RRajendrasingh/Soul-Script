<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

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
$roadmapFile = __DIR__ . '/../config/roadmap_data.json';

// Helper to load roadmap
function loadRoadmapData($file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if (is_array($data) && isset($data['built_features'], $data['pending_tasks'])) {
            return $data;
        }
    }
    return [
        'built_features' => [],
        'pending_tasks' => []
    ];
}

// Helper to save roadmap
function saveRoadmapData($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

$roadmap = loadRoadmapData($roadmapFile);
$flashMsg = '';

if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // 1. TOGGLE TASK STATUS (Move between Pending & Completed)
    if ($action === 'toggle_task' && !empty($_POST['task_id'])) {
        $taskId = trim($_POST['task_id']);
        $targetTo = trim($_POST['target_status'] ?? '');

        // Look in pending_tasks
        $foundIndex = -1;
        foreach ($roadmap['pending_tasks'] as $idx => $t) {
            if ($t['id'] === $taskId) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex !== -1) {
            // Move from pending to built_features
            $task = $roadmap['pending_tasks'][$foundIndex];
            $task['status'] = 'completed';
            $task['completed_at'] = date('Y-m-d H:i');
            array_splice($roadmap['pending_tasks'], $foundIndex, 1);
            $roadmap['built_features'][] = $task;
            saveRoadmapData($roadmapFile, $roadmap);
            $flashMsg = "🎉 Task '{$task['title']}' completed and moved to Completed Architecture above!";
        } else {
            // Look in built_features to move back to pending
            foreach ($roadmap['built_features'] as $idx => $t) {
                if ($t['id'] === $taskId) {
                    $task = $t;
                    $task['status'] = 'pending';
                    unset($task['completed_at']);
                    array_splice($roadmap['built_features'], $idx, 1);
                    $roadmap['pending_tasks'][] = $task;
                    saveRoadmapData($roadmapFile, $roadmap);
                    $flashMsg = "🔄 Task '{$task['title']}' moved back to Pending Checklist below.";
                    break;
                }
            }
        }
    }

    // 2. ADD NEW CUSTOM TASK
    elseif ($action === 'add_task') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $priority = trim($_POST['priority'] ?? 'High');
        $description = trim($_POST['description'] ?? '');

        if (!empty($title)) {
            $newTask = [
                'id' => 'task_' . time() . '_' . rand(100, 999),
                'title' => $title,
                'category' => $category,
                'priority' => $priority,
                'description' => $description,
                'status' => 'pending'
            ];
            $roadmap['pending_tasks'][] = $newTask;
            saveRoadmapData($roadmapFile, $roadmap);
            $flashMsg = "✨ New task '{$title}' added to Pending Checklist!";
        }
    }

    // 3. DELETE TASK
    elseif ($action === 'delete_task' && !empty($_POST['task_id'])) {
        $taskId = trim($_POST['task_id']);
        foreach ($roadmap['pending_tasks'] as $idx => $t) {
            if ($t['id'] === $taskId) {
                array_splice($roadmap['pending_tasks'], $idx, 1);
                saveRoadmapData($roadmapFile, $roadmap);
                $flashMsg = "🗑️ Task removed.";
                break;
            }
        }
        foreach ($roadmap['built_features'] as $idx => $t) {
            if ($t['id'] === $taskId) {
                array_splice($roadmap['built_features'], $idx, 1);
                saveRoadmapData($roadmapFile, $roadmap);
                $flashMsg = "🗑️ Item removed.";
                break;
            }
        }
    }
}

$builtCount = count($roadmap['built_features']);
$pendingCount = count($roadmap['pending_tasks']);
$totalItems = $builtCount + $pendingCount;
$completionRate = $totalItems > 0 ? round(($builtCount / $totalItems) * 100) : 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Master Architecture & Dynamic Roadmap — ' . APP_NAME;
  require_once __DIR__ . '/../includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Ambient Glows -->
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
          <i data-lucide="compass" class="w-7 h-7"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold font-serif text-[#e8e0e3]">Admin Vault Login</h2>
          <p class="text-xs text-[#d0c3cb] mt-1">Master Architecture &amp; Dynamic Roadmap</p>
        </div>
        <?php if ($loginError): ?>
          <div class="p-3 bg-rose-900/40 border border-rose-500/40 text-rose-300 rounded-xl text-xs font-semibold">
            <?php echo htmlspecialchars($loginError); ?>
          </div>
        <?php endif; ?>
        <form method="POST" class="space-y-4 text-left">
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Username</label>
            <input type="text" name="admin_user" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <div>
            <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Admin Password</label>
            <input type="password" name="admin_pass" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>
          <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#eac34a] to-[#d4af37] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:brightness-110 transition-all cursor-pointer">
            Unlock Roadmap Access
          </button>
        </form>
      </div>

    <?php else: ?>
      <?php require_once __DIR__ . '/nav_header.php'; ?>

      <!-- HEADER & METRICS SUMMARY -->
      <div class="bg-[#221f21] p-6 sm:p-7 rounded-3xl border border-[#4d444b]/40 shadow-xl space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#4d444b]/40 pb-5">
          <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#3b1e3b] text-[#eac34a] border border-[#eac34a]/30 text-[10px] uppercase font-extrabold tracking-wider">
              <i data-lucide="sparkles" class="w-3 h-3"></i>
              <span>Google Antigravity &times; SoulScript Architecture</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-serif text-[#e8e0e3]">
              Master Architecture &amp; Live Feature Roadmap 🚀
            </h1>
            <p class="text-xs text-[#d0c3cb]">
              Complete vertical engineering blueprint. Mark tasks as done to move them to the completed architecture list above.
            </p>
          </div>

          <!-- ADD TASK BUTTON -->
          <button onclick="document.getElementById('addTaskModal').classList.remove('hidden')" class="px-4 py-2.5 bg-gradient-to-r from-[#eac34a] to-[#d4af37] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:brightness-110 transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Add Custom Task</span>
          </button>
        </div>

        <!-- 4 QUICK STATUS PILLS -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div class="bg-[#151215] p-3.5 rounded-xl border border-[#4d444b]/30">
            <span class="text-[10px] text-[#d0c3cb]/70 uppercase font-bold block">Completed Modules</span>
            <span class="text-xl font-bold font-serif text-[#a4e4b9]"><?php echo $builtCount; ?> Built</span>
          </div>
          <div class="bg-[#151215] p-3.5 rounded-xl border border-[#4d444b]/30">
            <span class="text-[10px] text-[#d0c3cb]/70 uppercase font-bold block">Pending Tasks</span>
            <span class="text-xl font-bold font-serif text-[#eac34a]"><?php echo $pendingCount; ?> Pending</span>
          </div>
          <div class="bg-[#151215] p-3.5 rounded-xl border border-[#4d444b]/30">
            <span class="text-[10px] text-[#d0c3cb]/70 uppercase font-bold block">Total Blueprint Items</span>
            <span class="text-xl font-bold font-serif text-[#e8e0e3]"><?php echo $totalItems; ?> Total</span>
          </div>
          <div class="bg-[#151215] p-3.5 rounded-xl border border-[#4d444b]/30">
            <span class="text-[10px] text-[#d0c3cb]/70 uppercase font-bold block">Production Readiness</span>
            <span class="text-xl font-bold font-serif text-[#a4e4b9]"><?php echo $completionRate; ?>% Ready</span>
          </div>
        </div>
      </div>

      <?php if ($flashMsg): ?>
        <div class="p-4 rounded-2xl text-xs font-bold bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9] flex items-center justify-between">
          <span><?php echo htmlspecialchars($flashMsg); ?></span>
          <button onclick="this.parentElement.remove()" class="text-[#a4e4b9] hover:underline text-[11px]">Dismiss</button>
        </div>
      <?php endif; ?>

      <!-- ========================================== -->
      <!-- SECTION 1: BUILT & COMPLETED ARCHITECTURE -->
      <!-- ========================================== -->
      <section class="space-y-3">
        <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-2">
          <div class="flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-[#a4e4b9]"></i>
            <h2 class="text-lg font-bold font-serif text-[#e8e0e3]">
              Completed Architecture &amp; Core Features (<?php echo $builtCount; ?>)
            </h2>
          </div>
          <span class="text-[11px] text-[#a4e4b9] font-bold">100% Production Ready</span>
        </div>

        <div class="space-y-2.5">
          <?php foreach ($roadmap['built_features'] as $item): ?>
            <div class="bg-[#221f21] p-4 rounded-2xl border border-[#4d444b]/40 hover:border-[#a4e4b9]/40 transition-all space-y-1.5">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                  <!-- TOGGLE BACK BUTTON -->
                  <form method="POST" class="inline">
                    <input type="hidden" name="action" value="toggle_task">
                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                    <input type="hidden" name="target_status" value="pending">
                    <button type="submit" title="Click to move back to pending" class="w-5 h-5 rounded-md bg-[#1e3b20] text-[#a4e4b9] border border-[#a4e4b9]/40 flex items-center justify-center cursor-pointer hover:bg-rose-900/40 hover:text-rose-300 hover:border-rose-500/40 transition-all">
                      <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    </button>
                  </form>
                  <span class="text-sm font-bold text-[#e8e0e3]"><?php echo htmlspecialchars($item['title']); ?></span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-[10px] bg-[#151215] text-[#d0c3cb] px-2.5 py-0.5 rounded-full border border-[#4d444b]/40 font-mono">
                    <?php echo htmlspecialchars($item['category'] ?? 'Architecture'); ?>
                  </span>
                  <span class="text-[10px] bg-[#1e3b20] text-[#a4e4b9] px-2.5 py-0.5 rounded-full font-bold">
                    Built ✅
                  </span>
                  <form method="POST" onsubmit="return confirm('Remove this item?');" class="inline">
                    <input type="hidden" name="action" value="delete_task">
                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                    <button type="submit" class="text-rose-400/60 hover:text-rose-300 text-xs p-1" title="Delete">
                      <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                  </form>
                </div>
              </div>
              <p class="text-xs text-[#d0c3cb] pl-7 leading-relaxed">
                <?php echo htmlspecialchars($item['description']); ?>
              </p>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- ========================================== -->
      <!-- SECTION 2: PENDING PRE-LAUNCH CHECKLIST -->
      <!-- ========================================== -->
      <section class="space-y-3 pt-4">
        <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-2">
          <div class="flex items-center gap-2">
            <i data-lucide="list-todo" class="w-5 h-5 text-[#eac34a]"></i>
            <h2 class="text-lg font-bold font-serif text-[#e8e0e3]">
              Pending Action Items &amp; Pre-Launch Checklist (<?php echo $pendingCount; ?>)
            </h2>
          </div>
          <span class="text-[11px] text-[#eac34a] font-bold">Click checkbox to complete &uarr;</span>
        </div>

        <?php if (empty($roadmap['pending_tasks'])): ?>
          <div class="bg-[#221f21] p-8 rounded-2xl text-center border border-[#a4e4b9]/30 text-[#a4e4b9] space-y-1">
            <i data-lucide="party-popper" class="w-8 h-8 mx-auto"></i>
            <div class="font-bold text-sm">All Tasks Completed! Ready for 100% Public Go-Live! 🚀</div>
          </div>
        <?php else: ?>
          <div class="space-y-2.5">
            <?php foreach ($roadmap['pending_tasks'] as $item): ?>
              <div class="bg-[#221f21] p-4 rounded-2xl border border-[#4d444b]/40 hover:border-[#eac34a]/40 transition-all space-y-1.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                  <div class="flex items-center gap-2.5">
                    <!-- CLICK TO COMPLETE BUTTON -->
                    <form method="POST" class="inline">
                      <input type="hidden" name="action" value="toggle_task">
                      <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                      <input type="hidden" name="target_status" value="completed">
                      <button type="submit" title="Click to mark as DONE and move to Completed list above" class="w-5 h-5 rounded-md bg-[#151215] border border-[#eac34a]/60 flex items-center justify-center cursor-pointer hover:bg-[#a4e4b9] hover:border-[#a4e4b9] hover:text-[#151215] text-transparent transition-all">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                      </button>
                    </form>
                    <span class="text-sm font-bold text-[#e8e0e3]"><?php echo htmlspecialchars($item['title']); ?></span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-[10px] bg-[#151215] text-[#d0c3cb] px-2.5 py-0.5 rounded-full border border-[#4d444b]/40 font-mono">
                      <?php echo htmlspecialchars($item['category'] ?? 'Task'); ?>
                    </span>
                    <span class="text-[10px] <?php echo ($item['priority'] ?? 'High') === 'High' ? 'bg-[#2a1f0a] text-[#eac34a] border border-[#eac34a]/40' : 'bg-[#1e3b20] text-[#a4e4b9] border border-[#a4e4b9]/40'; ?> px-2.5 py-0.5 rounded-full font-bold">
                      <?php echo htmlspecialchars($item['priority'] ?? 'High'); ?> Priority
                    </span>
                    <form method="POST" onsubmit="return confirm('Remove this task?');" class="inline">
                      <input type="hidden" name="action" value="delete_task">
                      <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                      <button type="submit" class="text-rose-400/60 hover:text-rose-300 text-xs p-1" title="Delete">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                      </button>
                    </form>
                  </div>
                </div>
                <p class="text-xs text-[#d0c3cb] pl-7 leading-relaxed">
                  <?php echo htmlspecialchars($item['description']); ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- ========================================== -->
      <!-- MODAL: ADD CUSTOM TASK -->
      <!-- ========================================== -->
      <div id="addTaskModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#221f21] p-6 sm:p-7 rounded-3xl border border-[#eac34a]/40 shadow-2xl max-w-lg w-full space-y-4">
          <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
            <h3 class="text-lg font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
              <i data-lucide="plus-circle" class="w-5 h-5 text-[#eac34a]"></i>
              <span>Add Custom Roadmap Task</span>
            </h3>
            <button onclick="document.getElementById('addTaskModal').classList.add('hidden')" class="text-[#d0c3cb] hover:text-white">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>

          <form method="POST" class="space-y-3.5">
            <input type="hidden" name="action" value="add_task">
            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Task Title (with Emoji)</label>
              <input type="text" name="title" placeholder="e.g. 🎯 Launch Instagram Influencer Collab" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Category</label>
                <input type="text" name="category" placeholder="Marketing, UI, Backend..." value="General" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
              </div>
              <div>
                <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Priority</label>
                <select name="priority" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
                  <option value="High">High Priority</option>
                  <option value="Medium" selected>Medium Priority</option>
                  <option value="Low">Low Priority</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-[#d0c3cb] mb-1">Detailed Description &amp; Action Plan</label>
              <textarea name="description" rows="3" placeholder="Brief notes on what needs to be done..." class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none"></textarea>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#eac34a] to-[#d4af37] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:brightness-110 transition-all cursor-pointer">
              Add Task to Roadmap
            </button>
          </form>
        </div>
      </div>

    <!-- Universal Admin Footer -->
    <?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
  </main>
</body>
</html>
