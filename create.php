<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

$order_id = $_GET['order_id'] ?? '';
$preselected_template = $_GET['template'] ?? '';
$order = null;
$error = null;
$show_checkout_form = false;

// Valid templates list dynamically populated from DB
$valid_templates = [
    'anniversary_reveal'    => ['name' => 'Anniversary Reveal',    'price' => 499],
    'birthday_magic'        => ['name' => 'Birthday Magic',        'price' => 399],
    'perfect_proposal'      => ['name' => 'Perfect Proposal',      'price' => 599],
    'long_distance_love'    => ['name' => 'Long Distance Love',    'price' => 449],
    'raksha_bandhan_royal'  => ['name' => 'Raksha Bandhan Royal', 'price' => 599],
    'raksha_bandhan_festive_light' => ['name' => 'Raksha Bandhan Festive Light', 'price' => 449],
];

try {
    $dbTpl = getDB();
    $stmtTpl = $dbTpl->query("SELECT template_id, name, price_inr FROM templates WHERE active = 1");
    $dbTemplates = $stmtTpl->fetchAll();
    foreach ($dbTemplates as $dt) {
        $valid_templates[$dt['template_id']] = [
            'name'  => $dt['name'],
            'price' => (float)$dt['price_inr']
        ];
    }
} catch (Exception $exT) {}

if ($order_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        $error = "Order not found. Please start from the template gallery.";
    } else if ($order['payment_status'] !== 'paid') {
        $error = "Payment required: The partner details form is only unlocked for paid orders.";
    }
} elseif ($preselected_template && isset($valid_templates[$preselected_template])) {
    // User came from homepage Customize link — show checkout form
    $show_checkout_form = true;
    $loggedInBuyer = null;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['buyer_email'])) {
        try {
            $dbB = getDB();
            $stmtB = $dbB->prepare("SELECT buyer_name, buyer_phone, buyer_email FROM orders WHERE LOWER(buyer_email) = LOWER(?) ORDER BY created_at DESC LIMIT 1");
            $stmtB->execute([$_SESSION['buyer_email']]);
            $loggedInBuyer = $stmtB->fetch();
            if (!$loggedInBuyer) {
                $loggedInBuyer = ['buyer_email' => $_SESSION['buyer_email'], 'buyer_name' => '', 'buyer_phone' => ''];
            }
        } catch (Exception $eB) {}
    }
} else {
    $error = "Missing order ID. Please select a template and complete checkout first.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Personalize Surprise — ' . APP_NAME;
  require_once __DIR__ . '/includes/head.php'; 
  ?>
  <script src="<?php echo APP_URL; ?>/assets/js/compressor.js"></script>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden">

  <!-- Background Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>  <!-- Unified Global Navbar -->
  <?php 
  $current_page = 'create';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-12 relative z-10 space-y-8">


  <?php if ($show_checkout_form):
    $tpl = $valid_templates[$preselected_template];
  ?>
    <!-- Inline Checkout Form (from homepage Customize link) -->
    <div class="bg-[#221f21] rounded-3xl border border-[#eac34a]/30 p-8 shadow-2xl space-y-6">
      <div class="text-center space-y-2">
        <span class="text-[10px] uppercase tracking-[0.2em] text-[#eac34a] font-bold">Complete Your Order</span>
        <h1 class="text-3xl font-bold font-serif text-[#e8e0e3]"><?php echo htmlspecialchars($tpl['name']); ?></h1>
        <p class="text-xs text-[#d0c3cb]">Enter your details below to unlock your personalization form after payment.</p>
      </div>

      <?php if (strpos($preselected_template, 'raksha_bandhan') !== false): 
        require_once __DIR__ . '/includes/voucher_helper.php';
        $unlockDateShort = getFormattedUnlockDateShort();
      ?>
        <div class="p-3.5 bg-gradient-to-r from-[#3b2a1a] via-[#281d12] to-[#3b1e3b] border border-[#eac34a]/60 text-[#eac34a] rounded-2xl text-xs font-bold text-center flex items-center justify-center gap-2 shadow-md">
          <i data-lucide="gift" class="w-4 h-4 text-[#eac34a] shrink-0"></i>
          <span>🎁 Raksha Bandhan Special: Guaranteed Amazon Cash Voucher (₹100 to ₹2,000) Unlocks on <?php echo htmlspecialchars($unlockDateShort); ?>!</span>
        </div>
      <?php endif; ?>

      <div id="checkoutErrorMsg" class="hidden p-3 bg-[#3b1e3b] border border-[#e4b9df]/40 text-[#e4b9df] rounded-xl text-xs font-semibold text-center"></div>
      
      <?php if (!empty($loggedInBuyer)): ?>
        <div id="loggedInNotice" class="p-3 bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9] rounded-xl text-xs font-semibold text-center flex items-center justify-center gap-2">
          <i data-lucide="user-check" class="w-4 h-4 text-[#a4e4b9]"></i> 
          <span>Logged in as <strong><?php echo htmlspecialchars($loggedInBuyer['buyer_email']); ?></strong> (Buying a New Gift)</span>
        </div>
      <?php else: ?>
        <div id="loggedInNotice" class="hidden p-3 bg-[#1e3b20] border border-[#a4e4b9]/40 text-[#a4e4b9] rounded-xl text-xs font-semibold text-center flex items-center justify-center gap-2"></div>
      <?php endif; ?>

      <form id="checkoutForm" onsubmit="handleCheckoutSubmit(event); return false;" class="space-y-4">
        <input type="hidden" id="selectedTemplateId" value="<?php echo htmlspecialchars($preselected_template); ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-semibold text-[#d0c3cb] block mb-1">Your Full Name *</label>
            <input type="text" id="buyerName" value="<?php echo htmlspecialchars($loggedInBuyer['buyer_name'] ?? ''); ?>" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. Rohan Sharma" required>
          </div>
          <div>
            <label class="text-xs font-semibold text-[#d0c3cb] block mb-1">WhatsApp Number *</label>
            <div class="flex rounded-xl overflow-hidden border border-[#4d444b] focus-within:border-[#eac34a] bg-[#100d10]">
              <div class="bg-[#221f21] text-[#eac34a] font-mono text-xs font-bold px-3 flex items-center border-r border-[#4d444b] shrink-0">IN +91</div>
              <input type="tel" id="buyerPhone" value="<?php echo htmlspecialchars(preg_replace('/^\+91/', '', $loggedInBuyer['buyer_phone'] ?? '')); ?>" class="w-full bg-transparent px-3 py-2.5 text-sm text-[#e8e0e3] focus:outline-none font-mono" placeholder="9876543210" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
            </div>
          </div>
          <div>
            <label class="text-xs font-semibold text-[#d0c3cb] block mb-1">Email Address *</label>
            <input type="email" id="buyerEmail" value="<?php echo htmlspecialchars($loggedInBuyer['buyer_email'] ?? ''); ?>" onchange="checkExistingBuyerEmail(this.value)" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="you@example.com" required>
            <div id="existingEmailNotice" class="hidden mt-2 p-3 bg-amber-950/80 border border-amber-500/40 rounded-xl text-amber-300 text-xs leading-relaxed">
              🔑 <strong>Account Found:</strong> An account already exists for this email. Please enter your existing account password below, or <a href="<?php echo APP_URL; ?>/edit.php" class="underline font-bold text-[#eac34a]">Log In at Portal</a>.
            </div>
          </div>
          <div id="buyerPasswordGroup" class="<?php echo !empty($loggedInBuyer) ? 'hidden' : ''; ?>">
            <label class="text-xs font-semibold text-[#d0c3cb] block mb-1">Secret Edit Password * <span class="text-[10px] text-[#eac34a]">(min 6 chars)</span></label>
            <input type="password" id="buyerPassword" value="<?php echo !empty($loggedInBuyer) ? 'LOGGED_IN_SESSION' : ''; ?>" <?php echo empty($loggedInBuyer) ? 'minlength="6" required' : ''; ?> class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-3.5 py-2.5 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none font-mono" placeholder="••••••••">
          </div>
        </div>

        <div class="p-4 bg-[#100d10] border border-[#4d444b] rounded-2xl flex items-center justify-between gap-4">
          <div>
            <span class="text-[11px] uppercase font-extrabold text-[#d0c3cb]/70 tracking-wider block">Total</span>
            <span class="font-serif text-3xl font-extrabold text-[#eac34a]">₹<?php echo $tpl['price']; ?></span>
          </div>
          <button type="submit" id="checkoutBtn" class="px-6 py-3.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg flex items-center gap-2 hover:bg-[#ffe088] transition-all">
            <span>Proceed to Pay & Personalize</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </button>
        </div>
      </form>
    </div>

  <?php elseif ($error): ?>
    <div class="bg-[#221f21] rounded-3xl border border-[#eac34a]/30 p-8 text-center space-y-4 shadow-2xl">
      <div class="w-12 h-12 rounded-full bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center mx-auto border border-[#eac34a]/30">
        <i data-lucide="lock" class="w-6 h-6"></i>
      </div>
      <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Payment Verification Required</h3>
      <p class="text-xs text-[#d0c3cb]"><?php echo htmlspecialchars($error); ?></p>
      <a href="<?php echo APP_URL; ?>/#gallery" class="inline-block px-6 py-2.5 rounded-full bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider shadow-md hover:bg-[#ffe088]">
        Browse Templates & Complete Order
      </a>
    </div>
  <?php else: ?>

    <!-- Order Header Pill -->
    <div class="flex items-center justify-between text-xs font-semibold text-[#d0c3cb]">
      <a href="<?php echo APP_URL; ?>" class="flex items-center gap-1 hover:text-[#eac34a] transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Templates</span>
      </a>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#3b1e3b] border border-[#e4b9df]/20 text-[#e4b9df]">
        <i data-lucide="shield-check" class="w-4 h-4 text-[#eac34a]"></i>
        <span>Order #<?php echo substr($order['order_id'], -8); ?> (Paid ₹<?php echo (int)$order['amount_paid']; ?>)</span>
      </div>
    </div>

    <!-- Title Box -->
    <div class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#eac34a]/30 shadow-2xl space-y-2">
      <span class="text-[10px] uppercase tracking-[0.2em] text-[#eac34a] font-bold">Personalization Form</span>
      <h1 class="text-3xl font-bold font-serif text-[#e8e0e3]">Build Your Surprise Reveal</h1>
      <p class="text-xs text-[#d0c3cb]">Fill in the details below to generate your unlisted private reveal page. You can edit these anytime later using your Email &amp; Password.</p>
    </div>

    <!-- Success Modal Card (Matches 5th SS Exact DOM Layout) -->
    <div id="successCard" class="hidden max-w-md mx-auto bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/60 shadow-[0_20px_50px_rgba(0,0,0,0.9)] text-center space-y-6">
      
      <!-- Glowing Heart Header Badge -->
      <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-[#eac34a] via-[#e4b9df] to-[#cca830] p-[1.5px] mx-auto shadow-[0_0_25px_rgba(234,195,74,0.4)]">
        <div class="w-full h-full bg-[#151215] rounded-full flex items-center justify-center">
          <i data-lucide="heart" class="w-7 h-7 text-[#eac34a] fill-[#eac34a]"></i>
        </div>
      </div>

      <div>
        <span class="text-[10px] uppercase tracking-[0.2em] text-[#eac34a] font-bold bg-[#3b1e3b] px-3.5 py-1 rounded-full border border-[#e4b9df]/20">
          ✨ SURPRISE PAGE GENERATED &amp; HASHED!
        </span>
        <h2 class="text-2xl font-bold font-serif text-[#e8e0e3] mt-3">Your Private Gift Link is Ready</h2>
        <p class="text-xs text-[#d0c3cb] mt-1 font-normal">
          Your custom surprise page is protected with your hint password gate and active for 12 months.
        </p>
      </div>
      
      <!-- Private Link Card -->
      <div class="bg-[#151215] p-5 rounded-2xl border border-[#4d444b] text-left space-y-2">
        <label class="text-[10px] uppercase font-bold text-[#eac34a] tracking-widest block">PRIVATE SUBDOMAIN LINK</label>
        <div class="flex gap-2">
          <input type="text" id="shareUrlInput" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs font-mono font-bold text-[#e8e0e3]" readonly>
          <button onclick="copyToClipboard('shareUrlInput')" class="px-4 py-2.5 bg-[#3b1e3b] border border-[#eac34a]/40 text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-xs rounded-xl uppercase shrink-0 transition-all">Copy</button>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <!-- Bright Green WhatsApp Button -->
        <a id="whatsappShareBtn" href="#" target="_blank" class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
          <i data-lucide="share-2" class="w-4 h-4"></i>
          <span>SHARE SURPRISE LINK ON WHATSAPP</span>
        </a>

        <!-- Yellow Open & Test Button -->
        <a id="previewBtn" href="#" target="_blank" class="w-full py-3.5 bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
          <i data-lucide="external-link" class="w-4 h-4"></i>
          <span>OPEN &amp; TEST LOCK SCREEN (HINT GATE)</span>
        </a>

        <!-- Bottom Action Links -->
        <div class="flex items-center justify-between pt-2 text-xs font-semibold text-[#d0c3cb]">
          <button type="button" onclick="showQrModal()" class="hover:text-[#eac34a] flex items-center gap-1.5">
            <i data-lucide="qr-code" class="w-4 h-4 text-[#eac34a]"></i>
            <span>Show Phone QR Code</span>
          </button>
          <a href="<?php echo APP_URL; ?>" class="hover:text-[#eac34a]">Done &amp; Return Home</a>
        </div>
      </div>
    </div>

    <!-- Main Creation Form -->
    <form id="createPageForm" class="bg-[#221f21] p-6 sm:p-8 rounded-3xl border border-[#4d444b]/50 shadow-2xl space-y-6" onsubmit="handleFormSubmit(event); return false;">
      <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
      <input type="hidden" name="template_id" value="<?php echo htmlspecialchars($order['template_id']); ?>">

      <?php
      $tId = $order['template_id'] ?? 'anniversary_reveal';
      
      // Master Template UI Configuration Registry
      $templateSchemas = [
          'anniversary_reveal' => [
              'recipientLabel' => "Partner's First Name *",
              'recipientPlaceholder' => "e.g. Ananya",
              'recipientDefaultVal' => "Ananya",
              'taglineLabel' => "Custom Romantic Quote / Tagline Banner *",
              'taglineDefault' => "Safar Khubsurat h manjil se bhi 🌹",
              'messageLabel' => "Short Love Note / Signature Message *",
              'messagePlaceholder' => "e.g. Ananya, happy anniversary!",
              'messageDefaultVal' => "Every single day spent with you has been a beautiful gift. Happy Anniversary my love!",
              'photoLabel' => "Gift Receiver / Partner Profile Photo 🖼️ (Optional)",
              'hints' => [
                  ['label' => '"First Trip Location?"', 'q' => 'Where did we take our very first trip together in 2022?', 'a' => 'Shimla'],
                  ['label' => '"My Secret Nickname?"', 'q' => 'What is the nickname I call you when we are alone?', 'a' => 'Piku'],
                  ['label' => '"First Date Cafe?"', 'q' => 'Where was our first date cafe?', 'a' => 'Starbucks']
              ],
              'music' => [
                  'title' => 'Tum Hi Ho', 'artist' => 'Arijit Singh'
              ]
          ],
          'birthday_magic' => [
              'recipientLabel' => "Birthday Person's First Name (Friend / Loved One) *",
              'recipientPlaceholder' => "e.g. Rohan",
              'recipientDefaultVal' => "Rohan",
              'taglineLabel' => "Custom Birthday Tagline / Motto *",
              'taglineDefault' => "Cheers to another year of awesome memories! 🥂",
              'messageLabel' => "Birthday Wish / Personal Message *",
              'messagePlaceholder' => "e.g. Happy Birthday Rohan! 🎂",
              'messageDefaultVal' => "Wishing you the happiest of birthdays filled with joy!",
              'photoLabel' => "Birthday Person Profile Photo 🖼️ (Optional)",
              'hints' => [
                  ['label' => '"Childhood Nickname?"', 'q' => 'What was your funny childhood nickname?', 'a' => 'Chintu'],
                  ['label' => '"Favorite Movie?"', 'q' => 'What is your all-time favorite movie?', 'a' => 'Inception'],
                  ['label' => '"Dream Vacation?"', 'q' => 'Which country is your dream vacation destination?', 'a' => 'Japan']
              ],
              'music' => [
                  'title' => 'Baar Baar Din Ye Aaye', 'artist' => 'Kishore Kumar'
              ]
          ],
          'perfect_proposal' => [
              'recipientLabel' => "Partner's First Name *",
              'recipientPlaceholder' => "e.g. Priya",
              'recipientDefaultVal' => "Priya",
              'taglineLabel' => "Custom Romantic Quote / Tagline Banner *",
              'taglineDefault' => "Safar Khubsurat h manjil se bhi 🌹",
              'messageLabel' => "Short Love Note / Signature Message *",
              'messagePlaceholder' => "e.g. Priya, my love...",
              'messageDefaultVal' => "Every single day spent with you has been a beautiful gift. Happy Anniversary my love!",
              'photoLabel' => "Partner's Profile Photo 🖼️ (Optional)",
              'hints' => [
                  ['label' => '"First Trip Location?"', 'q' => 'Where did we take our very first trip together in 2022?', 'a' => 'Shimla'],
                  ['label' => '"My Secret Nickname?"', 'q' => 'What is the nickname I call you when we are alone?', 'a' => 'Piku'],
                  ['label' => '"First Date Cafe?"', 'q' => 'Where was our first date cafe?', 'a' => 'Starbucks']
              ],
              'music' => [
                  'title' => 'Perfect', 'artist' => 'Ed Sheeran'
              ]
          ],
          'long_distance_love' => [
              'recipientLabel' => "Partner's First Name *",
              'recipientPlaceholder' => "e.g. Ananya",
              'recipientDefaultVal' => "Ananya",
              'taglineLabel' => "Custom Quote / Tagline Banner *",
              'taglineDefault' => "Miles apart but connected by heart ✈️",
              'messageLabel' => "Short Love Note / Signature Message *",
              'messagePlaceholder' => "e.g. Ananya, I miss you...",
              'messageDefaultVal' => "Every single day spent with you has been a beautiful gift. Happy Anniversary my love!",
              'photoLabel' => "Partner's Profile Photo 🖼️ (Optional)",
              'hints' => [
                  ['label' => '"First Trip Location?"', 'q' => 'Where did we take our very first trip together in 2022?', 'a' => 'Shimla'],
                  ['label' => '"My Secret Nickname?"', 'q' => 'What is the nickname I call you when we are alone?', 'a' => 'Piku'],
                  ['label' => '"First Date Cafe?"', 'q' => 'Where was our first date cafe?', 'a' => 'Starbucks']
              ],
              'music' => [
                  'title' => 'Tera Yaar Hoon Main', 'artist' => 'Arijit Singh'
              ]
          ],
          'raksha_bandhan_royal' => [
              'recipientLabel' => "Brother / Sister's First Name *",
              'recipientPlaceholder' => "e.g. Mona",
              'recipientDefaultVal' => "Mona",
              'taglineLabel' => "Custom Sibling Motto / Tagline Banner *",
              'taglineDefault' => "World's Best Sister 👑",
              'messageLabel' => "Shagun Envelope Message / Slogan *",
              'messagePlaceholder' => "e.g. Happy Raksha Bandhan Mona Di! 🪔",
              'messageDefaultVal' => "Choti / Didi, mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🧧 (Aur haan, TV remote mera hi रहेगा! 😄)",
              'photoLabel' => "Brother / Sister's Profile Photo 🖼️ (Optional)",
              'hints' => [
                  ['label' => '"Favorite Cartoon?"', 'q' => 'What was our favorite cartoon show in childhood?', 'a' => 'Tom and Jerry'],
                  ['label' => '"Childhood Nickname?"', 'q' => 'What funny nickname did I call you in childhood?', 'a' => 'Chutki'],
                  ['label' => '"Biggest Fight?"', 'q' => 'What did we have our biggest childhood fight over?', 'a' => 'TV Remote']
              ],
              'music' => [
                  'title' => 'Phoolon Ka Taaron Ka', 'artist' => 'Kishore Kumar'
              ]
          ],
          'raksha_bandhan_festive_light' => [
              'recipientLabel' => "Brother / Sister's First Name *",
              'recipientPlaceholder' => "e.g. Mona",
              'recipientDefaultVal' => "Mona",
              'taglineLabel' => "Custom Sibling Motto / Tagline Banner *",
              'taglineDefault' => "World's Best Sister 🌸",
              'messageLabel' => "Shagun Envelope Message / Slogan *",
              'messagePlaceholder' => "e.g. Happy Raksha Bandhan Mona Di! 🎁",
              'messageDefaultVal' => "Choti / Didi, mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🎁",
              'photoLabel' => "Brother / Sister's Profile Photo 🖼️ (Optional)",
              'hints' => [
                  ['label' => '"Favorite Cartoon?"', 'q' => 'What was our favorite cartoon show in childhood?', 'a' => 'Tom and Jerry'],
                  ['label' => '"Childhood Nickname?"', 'q' => 'What funny nickname did I call you in childhood?', 'a' => 'Chutki'],
                  ['label' => '"Biggest Fight?"', 'q' => 'What did we have our biggest childhood fight over?', 'a' => 'TV Remote']
              ],
              'music' => [
                  'title' => 'Phoolon Ka Taaron Ka', 'artist' => 'Kishore Kumar'
              ]
          ]
      ];

      // Fallback to anniversary if not found
      $schema = $templateSchemas[$tId] ?? $templateSchemas['anniversary_reveal'];
      
      $recipientLabel = $schema['recipientLabel'];
      $recipientPlaceholder = $schema['recipientPlaceholder'];
      $recipientDefaultVal = $schema['recipientDefaultVal'];
      $taglineLabel = $schema['taglineLabel'];
      $taglineDefault = $schema['taglineDefault'];
      $taglinePresetText = $schema['taglineDefault'];
      $messageLabel = $schema['messageLabel'];
      $messagePlaceholder = $schema['messagePlaceholder'];
      $messageDefaultVal = $schema['messageDefaultVal'];
      $photoLabel = $schema['photoLabel'];
      $hintsList = $schema['hints'];
      $defaultMusic = $schema['music'];
      
      // Keep old vars for legacy checks
      $isBirthday = ($tId === 'birthday_magic');
      $isProposal = ($tId === 'perfect_proposal');
      $isLdr = ($tId === 'long_distance_love');
      $isRakhi = (strpos($tId, 'raksha_bandhan') !== false);
      ?>

      <div class="border-b border-[#4d444b]/40 pb-4">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">1. General Details</h3>
      </div>

      <div class="space-y-4 text-xs">
        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1"><?php echo $recipientLabel; ?></label>
          <input type="text" name="partner_name" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="<?php echo $recipientPlaceholder; ?>" value="<?php echo $recipientDefaultVal; ?>" required>
        </div>

        <!-- Gift Receiver Avatar Profile Photo Upload -->
        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1.5"><?php echo $photoLabel; ?></label>
          <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] flex flex-col sm:flex-row items-center gap-4">
            <div id="partnerAvatarContainer" class="w-16 h-16 rounded-full bg-[#3b1e3b] text-[#eac34a] border-2 border-[#eac34a] flex items-center justify-center font-bold text-2xl shadow-[0_0_20px_rgba(234,195,74,0.3)] shrink-0 overflow-hidden">
              <span id="partnerAvatarFallback"><?php echo strtoupper(substr($recipientDefaultVal, 0, 1)); ?></span>
              <img id="partnerAvatarImg" src="" class="w-full h-full object-cover hidden">
            </div>
            <div class="flex-1 text-center sm:text-left space-y-2">
              <input type="file" id="receiverPhotoInput" accept="image/*" onchange="handleReceiverPhotoUpload(this)" class="hidden">
              <input type="hidden" name="receiver_photo" id="receiverPhotoData" value="">
              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <button type="button" onclick="document.getElementById('receiverPhotoInput').click()" class="px-4 py-2 bg-[#3b1e3b] hover:bg-[#eac34a] text-[#eac34a] hover:text-[#241a00] border border-[#eac34a]/40 font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shadow-md">
                  <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                  <span>Upload / Crop Photo</span>
                </button>
                <button type="button" onclick="removeCreatePhoto()" id="removeCreatePhotoBtn" class="px-3 py-2 bg-[#221f21] hover:bg-rose-900/40 text-rose-400 border border-rose-500/30 font-bold text-xs rounded-xl transition-all cursor-pointer hidden flex items-center gap-1.5">
                  <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                  <span>Remove Photo</span>
                </button>
              </div>
              <p class="text-[10px] text-[#d0c3cb]/70">Upload a portrait photo to show at the top of the surprise page. If no photo is added, their initial character will be displayed.</p>
            </div>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block font-semibold text-[#d0c3cb]"><?php echo $taglineLabel; ?></label>
            <button type="button" onclick="setPresetQuote('<?php echo addslashes($taglinePresetText); ?>')" class="text-[10px] text-[#eac34a] hover:underline font-bold">✨ Use Preset Tagline</button>
          </div>
          <input type="text" id="taglineQuoteInput" name="tagline_quote" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. <?php echo htmlspecialchars($taglineDefault); ?>" value="<?php echo htmlspecialchars($taglineDefault); ?>" required>
        </div>

        <!-- Universal Music Engine (iTunes Live Search + YouTube Link + Favorite Singer) -->
        <div class="bg-[#151215] p-5 rounded-2xl border border-[#eac34a]/30 space-y-4">
          <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-2">
            <label class="font-bold text-[#eac34a] text-xs uppercase tracking-wider flex items-center gap-1.5">
              <i data-lucide="music" class="w-4 h-4 text-[#eac34a]"></i>
              <span>Background Music Engine 🎵</span>
            </label>
          </div>

          <!-- Choice Mode Radios -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-center gap-2 p-3 bg-[#221f21] border border-[#4d444b] rounded-xl cursor-pointer hover:border-[#eac34a]">
              <input type="radio" name="music_mode" value="itunes_search" checked onchange="toggleMusicMode('itunes_search')" class="text-[#eac34a]">
              <div class="text-xs">
                <strong class="block text-[#e8e0e3]">🔍 Live Song Search</strong>
                <span class="text-[10px] text-[#d0c3cb]">Search any song / singer</span>
              </div>
            </label>

            <label class="flex items-center gap-2 p-3 bg-[#221f21] border border-[#4d444b] rounded-xl cursor-pointer hover:border-[#eac34a]">
              <input type="radio" name="music_mode" value="youtube_link" onchange="toggleMusicMode('youtube_link')" class="text-[#eac34a]">
              <div class="text-xs">
                <strong class="block text-[#e8e0e3]">🎥 YouTube Link</strong>
                <span class="text-[10px] text-[#d0c3cb]">Paste YouTube URL</span>
              </div>
            </label>
          </div>

          <!-- iTunes Live Search Container -->
          <div id="itunesSearchBox" class="space-y-3">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Search &amp; Select Song (Birthday Anthems, Party, Bollywood, English) 🎶</label>
              <input type="text" id="itunesQueryInput" oninput="handleItunesSearch(this.value)" class="w-full bg-[#100d10] border border-[#4d444b] focus:border-[#eac34a] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:outline-none" placeholder="🔍 Type song name or singer e.g. Happy Birthday, Tum Hi Ho, Arijit Singh, Taylor Swift...">
            </div>

            <!-- Selected Track Card -->
            <div id="selectedTrackCard" class="bg-[#100d10] p-3 rounded-xl border border-[#eac34a]/60 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img id="selectedTrackImg" src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80" class="w-10 h-10 rounded-lg object-cover border border-[#4d444b]">
                <div class="flex-1 min-w-0">
                  <span class="block font-bold text-xs text-[#e8e0e3]" id="selectedTrackTitle"><?php echo htmlspecialchars($defaultMusic['title']); ?></span>
                  <span class="block text-[10px] text-[#d0c3cb] truncate" id="selectedTrackArtist"><?php echo htmlspecialchars($defaultMusic['artist']); ?></span>
                </div>
              </div>
              <span class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 font-bold">✓ Selected</span>
            </div>

            <!-- Live Search Results Container -->
            <div id="itunesResultsList" class="hidden space-y-2 max-h-60 overflow-y-auto bg-[#100d10] p-2 rounded-xl border border-[#4d444b]"></div>
          </div>

          <!-- YouTube Link Container -->
          <div id="youtubeLinkBox" class="hidden space-y-3">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Paste YouTube Video / Audio URL 🎥</label>
              <input type="url" id="youtubeUrlInput" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/...">
              <p class="text-[10px] text-[#d0c3cb]/70 mt-1">Paste any public YouTube video link for their favorite song.</p>
            </div>
          </div>
        </div>
        </div>

        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1"><?php echo $messageLabel; ?></label>
          <textarea name="love_note_text" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="3" placeholder="<?php echo htmlspecialchars($messagePlaceholder); ?>"><?php echo htmlspecialchars($messageDefaultVal); ?></textarea>
        </div>
      </div>

      <div class="border-b border-[#4d444b]/40 pb-4 pt-4">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">2. Template Specific Fields</h3>
      </div>

      <?php if ($order['template_id'] === 'anniversary_reveal'): ?>
        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Relationship Start Date * (For live time counter)</label>
            <input type="date" name="relationship_start_date" value="2022-08-15" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>

          <div class="flex items-center justify-between pt-2">
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider">"Our Story" Milestones (3 to 6 Memories) *</label>
            <button type="button" onclick="addMilestoneCard()" class="px-3.5 py-1.5 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1">
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
              <span>Add Milestone</span>
            </button>
          </div>

          <div id="milestonesContainer" class="space-y-4">
            <!-- Dynamic Milestone Cards -->
          </div>
        </div>

      <?php elseif ($order['template_id'] === 'birthday_magic'): ?>
        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Partner's Date of Birth * (For next birthday countdown)</label>
            <input type="date" name="partner_dob" value="1998-11-20" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" required>
          </div>

          <label class="block font-semibold text-[#d0c3cb] pt-2">Reasons I Love Celebrating You (3 to 5 entries)</label>
          <div class="space-y-2">
            <input type="text" name="reasons[]" value="Your infectious laugh that brightens any room" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Reason 1 e.g. Your infectious laugh that brightens any room" required>
            <input type="text" name="reasons[]" value="How you always remember to bring me chai on rainy afternoons" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Reason 2 e.g. How you always remember to bring me chai" required>
            <input type="text" name="reasons[]" value="Your kind, selfless and compassionate heart" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Reason 3 e.g. Your kind and compassionate heart" required>
          </div>
        </div>

      <?php elseif ($order['template_id'] === 'perfect_proposal'): ?>
        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Full Emotional Love Letter * (Centerpiece of your proposal)</label>
            <textarea name="love_letter_text" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl p-4 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="8" placeholder="Dearest Priya, I remember the exact moment I realized I wanted to spend the rest of my life with you..." required>Dearest Priya,

From the very first afternoon we shared a quiet coffee together under the soft rain, I knew in my heart that you were different. Your smile has a way of making the rest of the world fade into quiet background noise.

Through every milestone, every late-night conversation, and every spontaneous trip, you have been my favorite part of every day. With you, home is no longer a place—it's a feeling, and that feeling is wherever you are.

Today, I want to ask you the most important question of my life. Will you take my hand and walk through forever together with me?</textarea>
          </div>
        </div>

      <?php elseif ($order['template_id'] === 'long_distance_love'): ?>
        <div class="space-y-4 text-xs">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Your City *</label>
              <input type="text" name="buyer_city" value="Mumbai" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" placeholder="e.g. Mumbai" required>
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] mb-1">Partner's City *</label>
              <input type="text" name="partner_city" value="London" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" placeholder="e.g. London" required>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Next Reunion Date &amp; Time * (For live countdown)</label>
            <input type="datetime-local" name="reunion_date" value="2026-09-15T18:00" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" required>
          </div>

          <div>
            <label class="block font-semibold text-[#d0c3cb] mb-1">Shared Song or Spotify Playlist Link (Optional)</label>
            <input type="url" name="playlist_url" value="https://open.spotify.com/track/0VjIjW4GlUZAMYd2vXMi3b" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3]" placeholder="https://open.spotify.com/track/...">
          </div>
        </div>
      <?php elseif (strpos($order['template_id'] ?? '', 'raksha_bandhan') !== false): ?>
        <div class="space-y-4 text-xs">
          <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider">5 Sibling Promises / Vows *</label>
          <div class="space-y-2">
            <input type="text" name="reasons[]" value="Always protect you and stand by your side 🛡️" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Promise 1 e.g. Always protect you 🛡️" required>
            <input type="text" name="reasons[]" value="Keep all your deepest secrets safe 🤫" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Promise 2 e.g. Keep all secrets safe 🤫" required>
            <input type="text" name="reasons[]" value="Sponsor your favorite food and treat you 🍕" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Promise 3 e.g. Sponsor your favorite food 🍕" required>
            <input type="text" name="reasons[]" value="Never let you feel alone, no matter where I am 💖" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Promise 4 e.g. Never let you feel alone 💖" required>
            <input type="text" name="reasons[]" value="Always be your forever crime partner 🕵️‍♂️" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-2.5 text-xs text-[#e8e0e3]" placeholder="Promise 5 e.g. Always be your crime partner 🕵️‍♂️" required>
          </div>

          <div class="pt-2">
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider mb-1">Pre-select Preferred Rakhi Style 🧵 (Optional)</label>
            <select name="selected_rakhi_design" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none">
              <option value="gold_zardosi">👑 Gold Zardosi (Royal Golden Thread &amp; Beads)</option>
              <option value="ruby_silk">💎 Ruby Royal Silk (Crimson Gemstone &amp; Silk)</option>
              <option value="peacock">🦚 Peacock Feather (Vibrant Mayur Pankh Design)</option>
              <option value="sacred_om">🕉️ Sacred Om Thread (Pure Auspicious Mauli Thread)</option>
            </select>
          </div>

          <div class="pt-2">
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider mb-1">Amazon / Gift Voucher Code 🎁 (Optional)</label>
            <input type="text" name="shagun_voucher_code" id="shagunVoucherCodeInput" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. AMZ-RAKHI-9876 (Leave blank if no voucher code)">
            <p class="text-[10px] text-[#d0c3cb] mt-1">If entered, a golden "Claim Gift Voucher" badge with 1-click copy will appear inside the Shagun Envelope!</p>
          </div>
        </div>
      <?php endif; ?>

      <!-- 3. Photos Section (Matches 4th SS Exact DOM Layout) -->
      <div class="space-y-4 pt-4 border-t border-[#4d444b]/40">
        <div class="flex items-center justify-between">
          <div>
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider">PHOTOS (SELECT 1-25 PHOTOS) *</label>
            <span class="text-[11px] text-[#d0c3cb]" id="selectedPhotoCount">Selected: 3/25 photos</span>
          </div>
          <button type="button" onclick="triggerFileInput()" class="px-4 py-2 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1.5">
            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
            <span>Upload Photos</span>
          </button>
          <input type="file" id="photoFileInput" accept="image/*" multiple class="hidden" onchange="handleFileSelect(event)">
        </div>

        <!-- Selected Uploads Grid -->
        <div class="bg-[#151215] p-4 rounded-3xl border border-[#4d444b] min-h-[120px]">
          <div id="photoPreviewContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <!-- Dynamic Upload Thumbnails with X -->
          </div>
        </div>

        <!-- Quick Pick Sample Photos Gallery -->
        <div class="space-y-2 pt-2">
          <label class="text-[11px] font-bold uppercase tracking-wider text-[#eac34a] flex items-center gap-1">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            <span>QUICK PICK SAMPLE PHOTOS:</span>
          </label>
          <div class="grid grid-cols-3 sm:grid-cols-6 gap-3" id="samplePhotosGrid">
            <!-- Rendered by JS -->
          </div>
        </div>
      </div>

      <?php
      $themeFeatures = [
          'anniversary_reveal' => ['letters' => true, 'tokens' => true],
          'long_distance_love' => ['letters' => true, 'tokens' => true],
          'birthday_magic' => ['letters' => true, 'tokens' => false],
          'perfect_proposal' => ['letters' => true, 'tokens' => false],
          'raksha_bandhan_royal' => ['letters' => false, 'tokens' => false],
          'raksha_bandhan_festive_light' => ['letters' => false, 'tokens' => false],
      ];
      $currentFeatures = $themeFeatures[$order['template_id'] ?? ''] ?? ['letters' => true, 'tokens' => true];
      ?>

      <!-- Sealed Letters Section ("Open When..." Cards) -->
      <div class="space-y-4 pt-4 border-t border-[#4d444b]/40" <?php echo $currentFeatures['letters'] ? '' : 'style="display:none;"'; ?>>
        <div class="flex items-center justify-between">
          <div>
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider">✉️ SEALED LETTERS ("OPEN WHEN..." CARDS)</label>
            <span class="text-[11px] text-[#d0c3cb]">Create emotional notes your partner can unseal</span>
          </div>
          <button type="button" onclick="addCreateLetter()" class="px-4 py-2 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1.5 cursor-pointer">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Letter</span>
          </button>
        </div>
        <div id="createLettersList" class="space-y-3"></div>
      </div>

      <!-- Love Tokens & Redeemable Coupons Section -->
      <div class="space-y-4 pt-4 border-t border-[#4d444b]/40" <?php echo $currentFeatures['tokens'] ? '' : 'style="display:none;"'; ?>>
        <div class="flex items-center justify-between">
          <div>
            <label class="block font-bold text-[#eac34a] text-xs uppercase tracking-wider">🎟️ LOVE TOKENS &amp; REDEEMABLE COUPONS</label>
            <span class="text-[11px] text-[#d0c3cb]">Create fun coupons your partner can redeem anytime</span>
          </div>
          <button type="button" onclick="addCreateToken()" class="px-4 py-2 rounded-xl bg-[#3b1e3b] text-[#eac34a] font-bold text-xs border border-[#eac34a]/40 hover:bg-[#eac34a] hover:text-[#241a00] transition-all flex items-center gap-1.5 cursor-pointer">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Coupon</span>
          </button>
        </div>
        <div id="createTokensList" class="space-y-3"></div>
      </div>

      <div class="border-b border-[#4d444b]/40 pb-4 pt-4">
        <h3 class="text-lg font-bold font-serif text-[#e8e0e3]">4. Hint Security Gate &amp; Secret Password Setup</h3>
      </div>

      <div class="space-y-4 text-xs">
        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Secret Hint Question * (Asked to recipient on unlock screen)</label>
          <input type="text" id="hintQuestionInput" name="hint_question" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. <?php echo htmlspecialchars($hintsList[0]['q']); ?>" value="<?php echo htmlspecialchars($hintsList[0]['q']); ?>" required>
          
          <div class="flex flex-wrap gap-2 mt-2">
            <span class="text-[10px] text-[#eac34a] font-bold self-center">✨ Preset Hints:</span>
            <?php foreach($hintsList as $hint): ?>
            <button type="button" onclick="setHintPreset('<?php echo htmlspecialchars(addslashes($hint['q'])); ?>', '<?php echo htmlspecialchars(addslashes($hint['a'])); ?>')" class="text-[10px] bg-[#3b1e3b] text-[#e4b9df] px-2.5 py-1 rounded-full border border-[#e4b9df]/20 hover:bg-[#eac34a] hover:text-[#241a00] transition-colors"><?php echo htmlspecialchars($hint['label']); ?></button>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Secret Hint Answer * (Case-insensitive unlock key)</label>
          <input type="text" id="hintAnswerInput" name="hint_answer" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. <?php echo htmlspecialchars($hintsList[0]['a']); ?>" value="<?php echo htmlspecialchars($hintsList[0]['a']); ?>" required>
        </div>

        <div>
          <label class="block font-semibold text-[#d0c3cb] mb-1">Custom Link Slug Path (Optional)</label>
          <input type="text" name="custom_slug" class="w-full bg-[#151215] border border-[#4d444b] rounded-xl px-4 py-3 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="e.g. ananya-rohan (auto-generated if left blank)">
        </div>
      </div>

      <button type="submit" id="submitPageBtn" class="w-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] font-sans font-bold text-xs uppercase tracking-[0.2em] py-4 rounded-full shadow-[0_0_25px_rgba(234,195,74,0.3)] transition-all cursor-pointer">
        Generate &amp; Publish Secret Reveal Page →
      </button>
    </form>

  <?php endif; ?>

  </main>

  <!-- Unified Global Footer -->
  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <script>
    lucide.createIcons();

    let cropImg = null;
    let cropScale = 1;
    let cropOffsetX = 0;
    let cropOffsetY = 0;
    let isDraggingCrop = false;
    let startDragX = 0;
    let startDragY = 0;

    function handleReceiverPhotoUpload(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
          cropImg = new Image();
          cropImg.onload = function() {
            openCircleCropModal();
          };
          cropImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    }

    function openCircleCropModal() {
      if (!cropImg) return;
      cropScale = 1;
      cropOffsetX = 0;
      cropOffsetY = 0;
      document.getElementById('cropZoomRange').value = 1;
      document.getElementById('circleCropModal').classList.remove('hidden');
      setupCropCanvasEvents();
      updateCropCanvas();
      lucide.createIcons();
    }

    function closeCircleCropModal() {
      document.getElementById('circleCropModal').classList.add('hidden');
      document.getElementById('receiverPhotoInput').value = '';
    }

    function updateCropCanvas() {
      const canvas = document.getElementById('cropCanvas');
      if (!canvas || !cropImg) return;
      const ctx = canvas.getContext('2d');
      const zoom = parseFloat(document.getElementById('cropZoomRange').value || 1);

      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const baseScale = Math.max(canvas.width / cropImg.width, canvas.height / cropImg.height);
      const drawWidth = cropImg.width * baseScale * zoom;
      const drawHeight = cropImg.height * baseScale * zoom;

      const drawX = (canvas.width - drawWidth) / 2 + cropOffsetX;
      const drawY = (canvas.height - drawHeight) / 2 + cropOffsetY;

      ctx.drawImage(cropImg, drawX, drawY, drawWidth, drawHeight);
    }

    function setupCropCanvasEvents() {
      const wrapper = document.getElementById('cropCanvasWrapper');
      if (!wrapper || wrapper.dataset.bound) return;
      wrapper.dataset.bound = "true";

      const startDrag = (e) => {
        isDraggingCrop = true;
        const pageX = e.touches ? e.touches[0].pageX : e.pageX;
        const pageY = e.touches ? e.touches[0].pageY : e.pageY;
        startDragX = pageX - cropOffsetX;
        startDragY = pageY - cropOffsetY;
      };

      const moveDrag = (e) => {
        if (!isDraggingCrop) return;
        const pageX = e.touches ? e.touches[0].pageX : e.pageX;
        const pageY = e.touches ? e.touches[0].pageY : e.pageY;
        cropOffsetX = pageX - startDragX;
        cropOffsetY = pageY - startDragY;
        updateCropCanvas();
      };

      const stopDrag = () => {
        isDraggingCrop = false;
      };

      wrapper.addEventListener('mousedown', startDrag);
      window.addEventListener('mousemove', moveDrag);
      window.addEventListener('mouseup', stopDrag);

      wrapper.addEventListener('touchstart', startDrag, { passive: true });
      window.addEventListener('touchmove', moveDrag, { passive: true });
      window.addEventListener('touchend', stopDrag);
    }

    function applyCircleCrop() {
      if (!cropImg) return;

      const outCanvas = document.createElement('canvas');
      outCanvas.width = 400;
      outCanvas.height = 400;
      const ctx = outCanvas.getContext('2d');

      const srcCanvas = document.getElementById('cropCanvas');
      ctx.drawImage(srcCanvas, 0, 0, 400, 400);

      // Export compressed JPEG (~35KB) to prevent MySQL packet errors
      const croppedDataUrl = outCanvas.toDataURL('image/jpeg', 0.85);

      const partnerName = document.querySelector('input[name="partner_name"]')?.value || 'Partner';
      updatePartnerPhotoAvatar(croppedDataUrl, partnerName);
      closeCircleCropModal();
    }

    function updatePartnerPhotoAvatar(photoUrl, partnerName) {
      const fallback = document.getElementById('partnerAvatarFallback');
      const img = document.getElementById('partnerAvatarImg');
      const removeBtn = document.getElementById('removeCreatePhotoBtn');
      const hiddenInput = document.getElementById('receiverPhotoData');

      const nameChar = (partnerName || 'P').charAt(0).toUpperCase();
      if (fallback) fallback.innerText = nameChar;

      if (photoUrl && photoUrl.trim() !== '') {
        if (hiddenInput) hiddenInput.value = photoUrl;
        if (img) {
          img.src = photoUrl;
          img.classList.remove('hidden');
        }
        if (fallback) fallback.classList.add('hidden');
        if (removeBtn) {
          removeBtn.classList.remove('hidden');
          removeBtn.classList.add('flex');
        }
      } else {
        if (hiddenInput) hiddenInput.value = '';
        if (img) {
          img.src = '';
          img.classList.add('hidden');
        }
        if (fallback) fallback.classList.remove('hidden');
        if (removeBtn) {
          removeBtn.classList.add('hidden');
          removeBtn.classList.remove('flex');
        }
      }
    }

    function removeCreatePhoto() {
      const partnerName = document.querySelector('input[name="partner_name"]')?.value || 'Partner';
      document.getElementById('receiverPhotoInput').value = '';
      updatePartnerPhotoAvatar('', partnerName);
    }

    let selectedPhotoObjects = [];

    // Fetch dynamic initial samples on create page initialization
    fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php')
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success' && data.samples && data.samples.length > 0) {
          const init3 = data.samples.slice(0, 3);
          selectedPhotoObjects = init3.map(s => ({
            url: s.url,
            caption: s.caption || 'Our Special Moment 💕'
          }));
          if (typeof renderPhotoPicker === 'function') renderPhotoPicker();
        }
      })
      .catch(() => {});

    let createLettersList = [
      { id: "let_1", title: "Open When You Miss Me 💌", category: "Romantic", unlock_condition: "immediate", content: "Remember that no matter the distance, my heart is always right there with you." },
      { id: "let_2", title: "Open When You Feel Stressed 🌸", category: "Support", unlock_condition: "immediate", content: "Take a deep breath. You are capable of amazing things and I am always proud of you!" }
    ];

    let createTokensList = [
      { id: "tok_1", title: "1x Midnight Ice Cream Run 🍦", code: "ICECREAM", description: "Valid anytime, no questions asked!" },
      { id: "tok_2", title: "1x Movie Night Choice 🎬", code: "MOVIENIGHT", description: "You pick the movie, I make the popcorn!" },
      { id: "tok_3", title: "1x Unlimited Warm Hugs 🫂", code: "HUGS", description: "Redeemable for 10 minutes of uninterrupted tight hugs." }
    ];

    function renderCreateLetters() {
      const container = document.getElementById('createLettersList');
      if (!container) return;
      container.innerHTML = createLettersList.map((letObj, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-[#eac34a]">Letter #${i + 1}</span>
            <button type="button" onclick="removeCreateLetter(${i})" class="text-rose-400 hover:text-rose-300 text-xs font-bold flex items-center gap-1 cursor-pointer">
              <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Remove
            </button>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-[#d0c3cb] text-[11px] mb-1">Letter Title ("Open When...")</label>
              <input type="text" class="create-letter-title w-full bg-[#221f21] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" value="${escapeHtml(letObj.title)}">
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] text-[11px] mb-1">Category / Tag</label>
              <input type="text" class="create-letter-cat w-full bg-[#221f21] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" value="${escapeHtml(letObj.category)}">
            </div>
          </div>
          <div>
            <label class="block font-semibold text-[#d0c3cb] text-[11px] mb-1">Letter Content / Secret Message</label>
            <textarea rows="2" class="create-letter-content w-full bg-[#221f21] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3]">${escapeHtml(letObj.content)}</textarea>
          </div>
        </div>
      `).join('');
      if (typeof lucide === 'object') lucide.createIcons();
    }

    function addCreateLetter() {
      createLettersList.push({
        id: "let_" + Date.now(),
        title: "Open When You Need A Smile 😊",
        category: "Love",
        unlock_condition: "immediate",
        content: "I love you more than words can express!"
      });
      renderCreateLetters();
    }

    function removeCreateLetter(index) {
      createLettersList.splice(index, 1);
      renderCreateLetters();
    }

    function renderCreateTokens() {
      const container = document.getElementById('createTokensList');
      if (!container) return;
      container.innerHTML = createTokensList.map((tok, i) => `
        <div class="bg-[#151215] p-4 rounded-2xl border border-[#4d444b] space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-[#eac34a]">Token #${i + 1}</span>
            <button type="button" onclick="removeCreateToken(${i})" class="text-rose-400 hover:text-rose-300 text-xs font-bold flex items-center gap-1 cursor-pointer">
              <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Remove
            </button>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-[#d0c3cb] text-[11px] mb-1">Coupon Title</label>
              <input type="text" class="create-token-title w-full bg-[#221f21] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" value="${escapeHtml(tok.title)}">
            </div>
            <div>
              <label class="block font-semibold text-[#d0c3cb] text-[11px] mb-1">Coupon Code</label>
              <input type="text" class="create-token-code w-full bg-[#221f21] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" value="${escapeHtml(tok.code)}">
            </div>
          </div>
          <div>
            <label class="block font-semibold text-[#d0c3cb] text-[11px] mb-1">Coupon Description / Rule</label>
            <input type="text" class="create-token-desc w-full bg-[#221f21] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3]" value="${escapeHtml(tok.description)}">
          </div>
        </div>
      `).join('');
      if (typeof lucide === 'object') lucide.createIcons();
    }

    function addCreateToken() {
      createTokensList.push({
        id: "tok_" + Date.now(),
        title: "1x Romantic Dinner Out 🍷",
        code: "DINNER",
        description: "Redeemable for a candle-light dinner at your favorite restaurant!"
      });
      renderCreateTokens();
    }

    function removeCreateToken(index) {
      createTokensList.splice(index, 1);
      renderCreateTokens();
    }

    // Initialize Milestones, Letters, Tokens & Sample Photos on DOM load
    document.addEventListener('DOMContentLoaded', () => {
      initDefaultMilestones();
      renderPhotoPicker();
      renderCreateLetters();
      renderCreateTokens();
    });

    // --- DYNAMIC MILESTONES MANAGER (Matches 1st & 3rd SS) ---
    function initDefaultMilestones() {
      const container = document.getElementById('milestonesContainer');
      if (!container) return;

      container.innerHTML = '';
      const defaults = [
        { title: 'The Day We First Met', date: '2022-08-15', desc: 'Met at the cozy coffee shop on a rainy afternoon.' },
        { title: 'Our First Snow Trip', date: '2022-12-25', desc: 'Watched the winter snowfall under the pines.' },
        { title: 'One Year Celebration', date: '2023-08-15', desc: '365 days of non-stop laughter and memories.' }
      ];

      defaults.forEach((m, idx) => addMilestoneCard(m.title, m.date, m.desc));
    }

    function addMilestoneCard(title = '', date = '', desc = '') {
      const container = document.getElementById('milestonesContainer');
      if (!container) return;

      const idx = container.children.length + 1;
      const card = document.createElement('div');
      card.className = 'bg-[#151215] p-5 rounded-2xl border border-[#4d444b] space-y-3 relative group';

      card.innerHTML = `
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-[#eac34a]">Milestone #${idx}</span>
          <button type="button" onclick="removeMilestoneCard(this)" class="text-[#d0c3cb] hover:text-rose-400 p-1 transition-colors">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <input type="date" name="milestone_date[]" class="bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" value="${date}" required>
          <input type="text" name="milestone_title[]" class="bg-[#100d10] border border-[#4d444b] rounded-xl px-3 py-2 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" placeholder="Title e.g. First Trip Together" value="${title}" required>
        </div>
        <textarea name="milestone_desc[]" class="w-full bg-[#100d10] border border-[#4d444b] rounded-xl p-3 text-xs text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none" rows="2" placeholder="Short description...">${desc}</textarea>
      `;

      container.appendChild(card);
      renumberMilestones();
      lucide.createIcons();
    }

    function removeMilestoneCard(btn) {
      const container = document.getElementById('milestonesContainer');
      if (container.children.length <= 1) {
        alert('Please keep at least 1 milestone!');
        return;
      }
      btn.closest('.group').remove();
      renumberMilestones();
    }

    function renumberMilestones() {
      const container = document.getElementById('milestonesContainer');
      Array.from(container.children).forEach((card, idx) => {
        const titleSpan = card.querySelector('span');
        if (titleSpan) titleSpan.innerText = `Milestone #${idx + 1}`;
      });
    }

    // --- PHOTO PICKER MANAGER (Matches 4th SS Exact UI Layout) ---
    function triggerFileInput() {
      document.getElementById('photoFileInput').click();
    }

    async function handleFileSelect(e) {
      const files = Array.from(e.target.files);
      for (let file of files) {
        if (selectedPhotoObjects.length >= 25) {
          alert('⚠️ Maximum limit of 25 photos reached! Please remove a photo before adding more.');
          break;
        }
        try {
          const compressed = await compressImage(file, 1600, 1600, 0.82);
          selectedPhotoObjects.push({ url: compressed, caption: 'Moments of Joy' });
        } catch (err) {
          console.error(err);
        }
      }
      renderPhotoPicker();
    }

    function removePhoto(idx) {
      if (selectedPhotoObjects.length <= 1) {
        alert('Please keep at least 1 photo!');
        return;
      }
      selectedPhotoObjects.splice(idx, 1);
      renderPhotoPicker();
    }

    function updatePhotoCaption(idx, val) {
      if (selectedPhotoObjects[idx]) {
        selectedPhotoObjects[idx].caption = val;
      }
    }

    function toggleSamplePhoto(url, caption) {
      const idx = selectedPhotoObjects.findIndex(p => p.url === url);
      if (idx > -1) {
        if (selectedPhotoObjects.length <= 1) {
          alert('Please keep at least 1 photo!');
          return;
        }
        selectedPhotoObjects.splice(idx, 1);
      } else {
        if (selectedPhotoObjects.length >= 25) {
          alert('⚠️ Maximum limit of 25 photos reached! Please remove a photo before adding more.');
          return;
        }
        selectedPhotoObjects.push({ url: url, caption: caption || 'A Beautiful Memory' });
      }
      renderPhotoPicker();
    }

    let cachedSamplePhotos = [];
    let currentSampleCategory = 'all';

    function filterSampleCategory(cat) {
      currentSampleCategory = cat;
      document.querySelectorAll('.sample-cat-pill').forEach(btn => {
        if (btn.dataset.cat === cat) {
          btn.className = 'sample-cat-pill px-3 py-1.5 rounded-full font-bold text-[11px] transition-all bg-[#eac34a] text-[#241a00] border border-[#eac34a] shadow-md cursor-pointer shrink-0';
        } else {
          btn.className = 'sample-cat-pill px-3 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0';
        }
      });
      renderSampleGrid();
    }

    function renderSampleGrid() {
      const modalGrid = document.getElementById('sampleModalGrid');
      const countLabel = document.getElementById('sampleModalCountLabel');
      if (!modalGrid) return;

      if (countLabel) countLabel.innerText = `Selected: ${selectedPhotoObjects.length} / 25`;

      const filtered = cachedSamplePhotos.filter(s => currentSampleCategory === 'all' || s.category === currentSampleCategory);

      if (filtered.length === 0) {
        modalGrid.innerHTML = `<div class="col-span-full py-12 text-center text-xs text-[#d0c3cb]">No photos found in this category.</div>`;
        return;
      }

      modalGrid.innerHTML = filtered.map(sample => {
        const isSel = selectedPhotoObjects.some(p => p.url === sample.url);
        return `
          <div onclick="toggleSamplePhoto('${sample.url}', '${(sample.caption || 'Romantic Memory').replace(/'/g, "\\'")}')" 
             class="sample-library-card ${isSel ? 'selected' : ''} group">
            <img src="${sample.url}" onerror="this.onerror=null; this.src='<?php echo APP_URL; ?>/assets/default_gallery/sample_fa6955df.webp';" class="sample-library-img">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/95 via-black/70 to-transparent text-white text-[10px] text-center pt-4 pb-1.5 px-1 truncate font-semibold z-10">
              ${sample.caption || 'Romantic Memory'}
            </div>
            ${isSel ? `
              <div class="absolute top-2 right-2 bg-[#eac34a] text-[#241a00] text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg flex items-center gap-1 z-20">
                ✓ Selected
              </div>
            ` : `
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-10">
                <span class="px-2.5 py-1 rounded-lg bg-[#3b1e3b] text-[#eac34a] font-bold text-[11px] border border-[#eac34a]/50 shadow-lg">+ Add Photo</span>
              </div>
            `}
          </div>
        `;
      }).join('');
    }

    async function openSampleLibraryModal() {
      const modal = document.getElementById('sampleLibraryModal');
      const countLabel = document.getElementById('sampleModalCountLabel');

      if (modal) modal.classList.remove('hidden');
      if (countLabel) countLabel.innerText = `Selected: ${selectedPhotoObjects.length} / 25`;

      try {
        if (cachedSamplePhotos.length === 0) {
          const res = await fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php?user_mode=1');
          const data = await res.json();
          if (data.status === 'success' && data.samples.length > 0) {
            cachedSamplePhotos = data.samples;
          }
        }
        renderSampleGrid();
        if (typeof lucide === 'object') lucide.createIcons();
      } catch (err) {
        const modalGrid = document.getElementById('sampleModalGrid');
        if (modalGrid) modalGrid.innerHTML = `<div class="col-span-full py-10 text-center text-xs text-red-300">Error loading sample photos.</div>`;
      }
    }

    function toggleSamplePhoto(url, caption) {
      const idx = selectedPhotoObjects.findIndex(p => p.url === url);
      if (idx >= 0) {
        selectedPhotoObjects.splice(idx, 1);
      } else {
        if (selectedPhotoObjects.length >= 25) {
          alert('⚠️ Maximum limit of 25 photos reached! Please remove a photo before adding more.');
          return;
        }
        selectedPhotoObjects.push({ url: url, caption: caption || 'A Beautiful Memory' });
      }
      renderPhotoPicker();
      renderSampleGrid();
    }

    function closeSampleLibraryModal() {
      const modal = document.getElementById('sampleLibraryModal');
      if (modal) modal.classList.add('hidden');
    }

    window.openSampleLibraryModal = openSampleLibraryModal;
    window.closeSampleLibraryModal = closeSampleLibraryModal;

    let dragSourceIdx = null;
    let touchTimer = null;
    let isTouchDragging = false;
    let touchTargetIdx = null;

    function handlePhotoDragStart(e, idx, mode) {
      dragSourceIdx = idx;
      if (e.dataTransfer) e.dataTransfer.effectAllowed = 'move';
      if (e.currentTarget) e.currentTarget.style.opacity = '0.4';
    }

    function handlePhotoDragOver(e) {
      e.preventDefault();
      if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
    }

    function handlePhotoDrop(e, targetIdx, mode) {
      e.preventDefault();
      if (dragSourceIdx === null || dragSourceIdx === targetIdx) return;
      
      let list = (mode === 'edit') ? (typeof dashPhotosList !== 'undefined' ? dashPhotosList : []) : selectedPhotoObjects;
      if (dragSourceIdx >= 0 && dragSourceIdx < list.length && targetIdx >= 0 && targetIdx < list.length) {
        const [movedItem] = list.splice(dragSourceIdx, 1);
        list.splice(targetIdx, 0, movedItem);
        if (mode === 'edit' && typeof renderDashScrapbookPhotos === 'function') {
          renderDashScrapbookPhotos();
        } else if (typeof renderPhotoPicker === 'function') {
          renderPhotoPicker();
        }
      }
    }

    function handlePhotoDragEnd(e) {
      dragSourceIdx = null;
      if (e.currentTarget) e.currentTarget.style.opacity = '1';
    }

    function handlePhotoTouchStart(e, idx, mode) {
      dragSourceIdx = idx;
      touchTargetIdx = idx;
      isTouchDragging = false;
      const elem = e.currentTarget;
      
      touchTimer = setTimeout(() => {
        isTouchDragging = true;
        if (elem) {
          elem.classList.add('ring-2', 'ring-[#eac34a]', 'scale-95');
          elem.style.opacity = '0.6';
        }
        if (navigator.vibrate) navigator.vibrate(40);
      }, 250);
    }

    function handlePhotoTouchMove(e, mode) {
      if (!isTouchDragging) {
        clearTimeout(touchTimer);
        return;
      }
      if (e.cancelable) e.preventDefault();
      const touch = e.touches[0];
      const targetElem = document.elementFromPoint(touch.clientX, touch.clientY);
      if (targetElem) {
        const card = targetElem.closest('[data-photo-index]');
        if (card && card.getAttribute('data-photo-index') !== null) {
          touchTargetIdx = parseInt(card.getAttribute('data-photo-index'), 10);
        }
      }
    }

    function handlePhotoTouchEnd(e, mode) {
      clearTimeout(touchTimer);
      if (isTouchDragging && dragSourceIdx !== null && touchTargetIdx !== null && dragSourceIdx !== touchTargetIdx) {
        let list = (mode === 'edit') ? (typeof dashPhotosList !== 'undefined' ? dashPhotosList : []) : selectedPhotoObjects;
        if (dragSourceIdx >= 0 && dragSourceIdx < list.length && touchTargetIdx >= 0 && touchTargetIdx < list.length) {
          const [movedItem] = list.splice(dragSourceIdx, 1);
          list.splice(touchTargetIdx, 0, movedItem);
          if (mode === 'edit' && typeof renderDashScrapbookPhotos === 'function') {
            renderDashScrapbookPhotos();
          } else if (typeof renderPhotoPicker === 'function') {
            renderPhotoPicker();
          }
        }
      }
      dragSourceIdx = null;
      touchTargetIdx = null;
      isTouchDragging = false;
    }

    function generateWhatsAppShareUrl(templateId, partnerName, shareUrl) {
      const pName = (partnerName || '').trim();
      const nameSnippet = pName ? ` for ${pName}` : '';
      let msg = '';
      const tid = (templateId || '').toLowerCase();
      
      if (tid.includes('rakhi') || tid.includes('raksha')) {
        msg = `I created a special Raksha Bandhan surprise website${nameSnippet}! 🪔🧵 Open your gift link here: ${shareUrl}`;
      } else if (tid.includes('birthday')) {
        msg = `I created a special Birthday surprise website${nameSnippet}! 🎂🎁 Open your gift link here: ${shareUrl}`;
      } else if (tid.includes('friendship') || tid.includes('friend')) {
        msg = `I created a special Friendship memory website${nameSnippet}! 🌟✨ Open your gift link here: ${shareUrl}`;
      } else if (tid.includes('proposal') || tid.includes('propose')) {
        msg = `I created a special Proposal surprise website${nameSnippet}! 💍💖 Open your gift link here: ${shareUrl}`;
      } else if (tid.includes('anniversary')) {
        msg = `I created a special Anniversary surprise website${nameSnippet}! 💕✨ Open your gift link here: ${shareUrl}`;
      } else {
        msg = `I created a secret romantic surprise${nameSnippet}! ❤️ Open your gift link here: ${shareUrl}`;
      }

      return `https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`;
    }

    async function checkExistingBuyerEmail(email) {
      const notice = document.getElementById('existingEmailNotice');
      if (!email || !email.includes('@')) {
        if (notice) notice.classList.add('hidden');
        return;
      }
      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/check_email.php?email=' + encodeURIComponent(email));
        const data = await res.json();
        if (data.exists && notice) {
          notice.classList.remove('hidden');
        } else if (notice) {
          notice.classList.add('hidden');
        }
      } catch (e) {
        if (notice) notice.classList.add('hidden');
      }
    }

    function renderPhotoPicker() {
      const countElem = document.getElementById('selectedPhotoCount');
      if (countElem) countElem.innerText = `Selected: ${selectedPhotoObjects.length}/25 photos`;

      // Render Selected Uploads with Caption Inputs
      const prevContainer = document.getElementById('photoPreviewContainer');
      if (prevContainer) {
        prevContainer.innerHTML = selectedPhotoObjects.map((item, idx) => `
          <div data-photo-index="${idx}" draggable="true"
               ondragstart="handlePhotoDragStart(event, ${idx}, 'create')" 
               ondragover="handlePhotoDragOver(event)" 
               ondrop="handlePhotoDrop(event, ${idx}, 'create')" 
               ondragend="handlePhotoDragEnd(event)"
               ontouchstart="handlePhotoTouchStart(event, ${idx}, 'create')"
               ontouchmove="handlePhotoTouchMove(event, 'create')"
               ontouchend="handlePhotoTouchEnd(event, 'create')"
               class="relative w-full flex-col flex group bg-[#100d10] p-1.5 rounded-2xl border-2 border-[#eac34a]/60 shadow-md cursor-grab active:cursor-grabbing select-none hover:border-[#eac34a] transition-all">
            <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden">
              <img src="${item.url}" class="w-full h-full object-cover pointer-events-none">
              <div class="absolute top-1.5 left-1.5 bg-black/70 text-[#eac34a] text-[10px] px-1.5 py-0.5 rounded-md font-mono flex items-center gap-1 backdrop-blur-sm pointer-events-none">
                <span>⠿</span>
                <span>#${idx + 1}</span>
              </div>
              <button type="button" onclick="removePhoto(${idx})" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-black/80 text-white flex items-center justify-center text-xs font-bold hover:bg-rose-600 transition-colors z-10">
                ✕
              </button>
            </div>
            <input type="text" placeholder="✍️ Memory caption..." value="${escapeHtml(item.caption || '')}" oninput="updatePhotoCaption(${idx}, this.value)" class="w-full bg-[#1b171b] border border-[#4d444b] rounded-lg px-2 py-1 text-[10px] text-[#e8e0e3] focus:border-[#eac34a] focus:outline-none placeholder-[#8a7b85] mt-1.5">
          </div>
        `).join('');
      }

      // Render Quick Pick Sample Gallery with Golden Checkmark
      const sampleGrid = document.getElementById('samplePhotosGrid');
      if (sampleGrid) {
        fetch('<?php echo APP_URL; ?>/api/admin_sample_gallery.php')
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success' && data.samples && data.samples.length > 0) {
              const top5 = data.samples.slice(0, 5);
              let html = top5.map((photo) => {
                const isSelected = selectedPhotoObjects.some(p => p.url === photo.url);
                return `
                  <a href="javascript:void(0)" onclick="toggleSamplePhoto('${photo.url}', '${(photo.caption || 'Romantic Memory').replace(/'/g, "\\'")}')" class="relative aspect-square rounded-2xl overflow-hidden border-2 cursor-pointer transition-all block ${isSelected ? 'border-[#eac34a] shadow-[0_0_15px_rgba(234,195,74,0.4)] scale-95' : 'border-[#4d444b] opacity-60 hover:opacity-100'}">
                    <img src="${photo.url}" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[9px] text-center py-0.5 px-1 truncate font-semibold">${photo.caption || 'Romantic Memory'}</div>
                    ${isSelected ? `
                      <div class="absolute inset-0 bg-[#eac34a]/20 flex items-center justify-center">
                        <div class="w-6 h-6 rounded-full bg-[#eac34a] text-[#241a00] flex items-center justify-center text-xs font-extrabold shadow-md">✓</div>
                      </div>
                    ` : ''}
                  </a>
                `;
              }).join('');

              // 6th Item: View All Anchor Card
              html += `
                <a href="javascript:void(0)" onclick="openSampleLibraryModal()" class="aspect-square rounded-2xl border border-[#eac34a]/60 bg-gradient-to-br from-[#3b1e3b] to-[#221f21] p-2 flex flex-col items-center justify-center text-center group cursor-pointer hover:scale-105 transition-all shadow-lg hover:border-[#eac34a]">
                  <i data-lucide="images" class="w-5 h-5 text-[#eac34a] mb-1 group-hover:scale-110 transition-transform"></i>
                  <span class="text-xs font-bold text-[#e8e0e3] group-hover:text-[#eac34a]">View All ➡️</span>
                  <span class="text-[9px] text-[#d0c3cb] mt-0.5">More Samples</span>
                </a>
              `;

              sampleGrid.innerHTML = html;
              if (typeof lucide === 'object') lucide.createIcons();
            }
          })
          .catch(() => {});
      }
    }

    let currentSelectedMusicUrl = 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3';
    let currentSelectedSongTitle = '<?php echo addslashes($defaultMusic['title']); ?>';
    let currentSelectedArtist = '<?php echo addslashes($defaultMusic['artist']); ?>';
    let searchDebounceTimer = null;

    function setPresetQuote(quoteText) {
      const input = document.getElementById('taglineQuoteInput');
      if (input) input.value = quoteText;
    }

    function setHintPreset(question, answer) {
      const qInput = document.getElementById('hintQuestionInput');
      const aInput = document.getElementById('hintAnswerInput');
      if (qInput) qInput.value = question;
      if (aInput) aInput.value = answer;
    }

    function toggleMusicMode(mode) {
      const itunesBox = document.getElementById('itunesSearchBox');
      const ytBox = document.getElementById('youtubeLinkBox');
      const randBox = document.getElementById('randomSingerBox');

      if (itunesBox) itunesBox.classList.add('hidden');
      if (ytBox) ytBox.classList.add('hidden');
      if (randBox) randBox.classList.add('hidden');

      if (mode === 'itunes_search' && itunesBox) itunesBox.classList.remove('hidden');
      if (mode === 'youtube_link' && ytBox) ytBox.classList.remove('hidden');
      if (mode === 'random_singer' && randBox) randBox.classList.remove('hidden');
    }

    function cleanAttrStr(str) {
      if (!str) return '';
      return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function handleItunesSearch(query) {
      clearTimeout(searchDebounceTimer);
      const resultsContainer = document.getElementById('itunesResultsList');
      if (!query.trim()) {
        resultsContainer.classList.add('hidden');
        return;
      }

      searchDebounceTimer = setTimeout(async () => {
        try {
          resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-[#eac34a]">Searching iTunes Music Database...</div>';
          resultsContainer.classList.remove('hidden');

          const res = await fetch(`https://itunes.apple.com/search?term=${encodeURIComponent(query)}&entity=song&limit=8`);
          const data = await res.json();

          if (data.results && data.results.length > 0) {
            resultsContainer.innerHTML = data.results.map(item => `
              <div class="p-2.5 bg-[#151215] hover:bg-[#221f21] rounded-xl flex items-center justify-between border border-[#4d444b]/50 transition-all">
                <div class="flex items-center gap-3 overflow-hidden">
                  <img src="${item.artworkUrl60 || item.artworkUrl100}" class="w-10 h-10 rounded-lg object-cover border border-[#4d444b] shrink-0">
                  <div class="truncate">
                    <span class="block font-bold text-xs text-[#e8e0e3] truncate">${escapeHtml(item.trackName)}</span>
                    <span class="block text-[10px] text-[#d0c3cb] truncate">🎤 ${escapeHtml(item.artistName)} • ${escapeHtml(item.collectionName || '')}</span>
                  </div>
                </div>
                <button type="button" onclick="selectItunesTrack('${cleanAttrStr(item.previewUrl)}', '${cleanAttrStr(item.trackName)}', '${cleanAttrStr(item.artistName)}', '${cleanAttrStr(item.artworkUrl100)}')" class="px-3 py-1.5 bg-[#3b1e3b] text-[#eac34a] hover:bg-[#eac34a] hover:text-[#241a00] font-bold text-[11px] rounded-lg border border-[#eac34a]/40 shrink-0 transition-all cursor-pointer">
                  + Select
                </button>
              </div>
            `).join('');
          } else {
            resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-[#d0c3cb]">No songs found. Try typing artist name or song title.</div>';
          }
        } catch (err) {
          resultsContainer.innerHTML = '<div class="p-3 text-center text-xs text-rose-400">Search error: ' + escapeHtml(err.message) + '</div>';
        }
      }, 350);
    }

    function selectItunesTrack(previewUrl, trackName, artistName, imgUrl) {
      currentSelectedMusicUrl = previewUrl;
      currentSelectedSongTitle = trackName;
      currentSelectedArtist = artistName;

      document.getElementById('selectedTrackImg').src = imgUrl || 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=150&q=80';
      document.getElementById('selectedTrackTitle').innerText = trackName;
      document.getElementById('selectedTrackArtist').innerText = 'Artist: ' + artistName;

      document.getElementById('itunesResultsList').classList.add('hidden');
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    async function handleFormSubmit(e) {
      e.preventDefault();
      const btn = document.getElementById('submitPageBtn');
      btn.innerText = 'Creating Secret Page...';
      btn.disabled = true;

      const form = document.getElementById('createPageForm');
      const formData = new FormData(form);

      const milestones = [];
      const mTitles = formData.getAll('milestone_title[]');
      const mDates = formData.getAll('milestone_date[]');
      const mDescs = formData.getAll('milestone_desc[]');
      for (let i = 0; i < mTitles.length; i++) {
        if (mTitles[i]) {
          milestones.push({ title: mTitles[i], date: mDates[i] || '', description: mDescs[i] || '' });
        }
      }

      const reasons = formData.getAll('reasons[]').filter(r => r.trim() !== '');

      const musicMode = formData.get('music_mode') || 'itunes_search';
      let bgMusicUrl = '';
      let songTitle = '';
      let favoriteSingers = '';

      if (musicMode === 'itunes_search') {
        bgMusicUrl = currentSelectedMusicUrl;
        songTitle = currentSelectedSongTitle;
        favoriteSingers = currentSelectedArtist;
      } else if (musicMode === 'youtube_link') {
        const ytInput = document.getElementById('youtubeUrlInput');
        bgMusicUrl = ytInput ? ytInput.value.trim() : '';
        songTitle = 'YouTube Favorite Song';
        favoriteSingers = 'YouTube Track';
      } else {
        favoriteSingers = document.getElementById('favoriteSingerChoice').value || 'Arijit Singh';
        bgMusicUrl = 'random_singer';
        songTitle = 'Random Hit Track';
      }

      // Collect Sealed Letters
      const letterCards = document.querySelectorAll('#createLettersList > div');
      const letters = [];
      letterCards.forEach((card, idx) => {
        const title = card.querySelector('.create-letter-title')?.value.trim();
        const category = card.querySelector('.create-letter-cat')?.value.trim() || 'General';
        const content = card.querySelector('.create-letter-content')?.value.trim();
        if (title && content) {
          letters.push({
            id: 'let_' + (idx + 1),
            title: title,
            category: category,
            unlock_condition: 'immediate',
            content: content
          });
        }
      });

      // Collect Love Tokens
      const tokenCards = document.querySelectorAll('#createTokensList > div');
      const tokens = [];
      tokenCards.forEach((card, idx) => {
        const title = card.querySelector('.create-token-title')?.value.trim();
        const code = card.querySelector('.create-token-code')?.value.trim() || ('TOKEN' + (idx + 1));
        const desc = card.querySelector('.create-token-desc')?.value.trim();
        if (title) {
          tokens.push({
            id: 'tok_' + (idx + 1),
            title: title,
            code: code,
            description: desc || ''
          });
        }
      });

      const payload = {
        order_id: formData.get('order_id'),
        partner_name: formData.get('partner_name'),
        receiver_photo: document.getElementById('receiverPhotoData')?.value || '',
        tagline_quote: formData.get('tagline_quote'),
        favorite_singers: favoriteSingers,
        bg_music_url: bgMusicUrl,
        song_title: songTitle,
        song_artist: favoriteSingers,
        love_note_text: formData.get('love_note_text'),
        hint_question: formData.get('hint_question'),
        hint_answer: formData.get('hint_answer'),
        custom_slug: formData.get('custom_slug'),
        photos: selectedPhotoObjects,
        letters: letters,
        tokens: tokens,
        template_fields: {
          relationship_start_date: formData.get('relationship_start_date') || null,
          partner_dob: formData.get('partner_dob') || null,
          love_letter_text: formData.get('love_letter_text') || null,
          buyer_city: formData.get('buyer_city') || null,
          partner_city: formData.get('partner_city') || null,
          reunion_date: formData.get('reunion_date') || null,
          playlist_url: formData.get('playlist_url') || null,
          song_title: songTitle,
          song_artist: favoriteSingers,
          milestones: milestones,
          reasons: reasons,
          letters: letters,
          tokens: tokens,
          shagun_voucher_code: formData.get('shagun_voucher_code') || ''
        }
      };

      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/create_page.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          form.classList.add('hidden');
          const successCard = document.getElementById('successCard');
          successCard.classList.remove('hidden');
          
          document.getElementById('shareUrlInput').value = data.share_url;
          document.getElementById('previewBtn').href = data.share_url;

          // Dynamic Occasion-Based WhatsApp Share Link
          const partnerName = document.getElementById('partnerName')?.value || '';
          const templateId = '<?php echo htmlspecialchars($tpl_id); ?>';
          document.getElementById('whatsappShareBtn').href = generateWhatsAppShareUrl(templateId, partnerName, data.share_url);

          // Set QR image URL
          const qrImg = document.getElementById('qrCodeImg');
          if (qrImg) qrImg.src = `https://quickchart.io/qr?text=${encodeURIComponent(data.share_url)}&size=300`;

          window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Server error: ' + err.message);
      } finally {
        btn.innerText = 'Generate & Publish Secret Reveal Page →';
        btn.disabled = false;
      }
    }

    function copyToClipboard(inputId) {
      const input = document.getElementById(inputId);
      input.select();
      document.execCommand('copy');
      alert('Link copied to clipboard!');
    }

    function showQrModal() {
      const modal = document.getElementById('qrModal');
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    }

    function closeQrModal() {
      const modal = document.getElementById('qrModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

  </script>

  <!-- Mobile QR Code Modal -->
  <div id="qrModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4" onclick="closeQrModal()">
    <div class="bg-[#221f21] p-8 rounded-3xl border border-[#eac34a]/50 max-w-sm w-full text-center space-y-4 shadow-2xl relative" onclick="event.stopPropagation()">
      <h3 class="text-xl font-bold font-serif text-[#e8e0e3]">Mobile QR Code</h3>
      <p class="text-xs text-[#d0c3cb]">Scan this QR code with any phone camera to open the gift link.</p>
      
      <div class="bg-white p-4 rounded-2xl inline-block shadow-lg mx-auto">
        <img id="qrCodeImg" src="" alt="Gift Page QR Code" class="w-48 h-48 object-contain mx-auto">
      </div>

      <div>
        <button onclick="closeQrModal()" class="px-6 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase rounded-full hover:bg-[#ffe088] transition-all cursor-pointer">
          Done &amp; Close
        </button>
      </div>
    </div>
  </div>

  <!-- Interactive Circle Crop Modal -->
  <div id="circleCropModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center p-4 hidden">
    <div class="bg-[#221f21] border border-[#eac34a]/40 rounded-3xl p-6 max-w-md w-full text-center space-y-4 shadow-2xl relative">
      <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3">
        <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
          <i data-lucide="crop" class="w-4 h-4 text-[#eac34a]"></i>
          <span>Adjust &amp; Crop Partner Photo</span>
        </h3>
        <button onclick="closeCircleCropModal()" type="button" class="text-[#d0c3cb] hover:text-white text-lg font-bold">✕</button>
      </div>

      <!-- Circle Preview Container -->
      <div class="relative w-64 h-64 mx-auto overflow-hidden bg-[#151215] rounded-2xl border border-[#4d444b] flex items-center justify-center cursor-move select-none" id="cropCanvasWrapper">
        <canvas id="cropCanvas" width="256" height="256" class="touch-none"></canvas>
        <!-- Circular Overlay Mask Guide -->
        <div class="absolute inset-0 rounded-full border-4 border-[#eac34a] shadow-[0_0_0_9999px_rgba(21,18,21,0.7)] pointer-events-none"></div>
      </div>

      <!-- Controls: Zoom & Position reset -->
      <div class="space-y-3 pt-2">
        <div class="flex items-center gap-3 text-xs">
          <span class="text-[#d0c3cb] font-semibold w-12 text-right">Zoom:</span>
          <input type="range" id="cropZoomRange" min="0.5" max="3" step="0.05" value="1" oninput="updateCropCanvas()" class="w-full accent-[#eac34a]">
        </div>
        <p class="text-[10px] text-[#d0c3cb]/70">Drag photo to align partner's face inside the golden circle.</p>
      </div>

      <!-- Modal Action Buttons -->
      <div class="flex items-center gap-3 pt-2">
        <button type="button" onclick="closeCircleCropModal()" class="w-1/2 py-2.5 bg-[#151215] text-[#d0c3cb] border border-[#4d444b] rounded-xl font-bold text-xs">Cancel</button>
        <button type="button" onclick="applyCircleCrop()" class="w-1/2 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:bg-[#ffe088] transition-all">Crop &amp; Apply</button>
      </div>
    </div>
  </div>

  <!-- Smart Smooth Auto-Hiding Header Script -->
  <script>
    (function() {
      let lastScrollY = window.scrollY;
      const header = document.getElementById('createHeader');
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

    <?php if ($show_checkout_form): ?>
    // Inline checkout form JS (for create.php?template= flow)
    let currentTemplateId = document.getElementById('selectedTemplateId')?.value || '';
    let currentPrice = <?php echo $show_checkout_form ? $tpl['price'] : 0; ?>;
    let isLoggedInBuyerSession = false;

    async function checkActiveBuyerSession() {
      try {
        const res = await fetch('<?php echo APP_URL; ?>/api/buyer_session.php', { credentials: 'same-origin' });
        const data = await res.json();
        const passGroup = document.getElementById('buyerPasswordGroup');
        const passInput = document.getElementById('buyerPassword');
        const noticeBox = document.getElementById('loggedInNotice');

        if (data.logged_in && data.buyer_email) {
          isLoggedInBuyerSession = true;
          if (data.buyer_name && document.getElementById('buyerName')) document.getElementById('buyerName').value = data.buyer_name;
          if (data.buyer_phone && document.getElementById('buyerPhone')) document.getElementById('buyerPhone').value = data.buyer_phone;
          if (data.buyer_email && document.getElementById('buyerEmail')) document.getElementById('buyerEmail').value = data.buyer_email;

          if (passGroup) passGroup.classList.add('hidden');
          if (passInput) {
            passInput.removeAttribute('required');
            passInput.value = 'LOGGED_IN_SESSION';
          }

          if (noticeBox) {
            noticeBox.innerHTML = `<i data-lucide="user-check" class="w-4 h-4 text-[#a4e4b9]"></i> <span>Logged in as <strong>${escapeHtml(data.buyer_email)}</strong> (Buying a New Gift)</span>`;
            noticeBox.classList.remove('hidden');
            if (typeof lucide === 'object') lucide.createIcons();
          }
        }
      } catch (err) {
        console.log('Session check error:', err);
      }
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', checkActiveBuyerSession);
    } else {
      checkActiveBuyerSession();
    }

    async function handleCheckoutSubmit(e) {
      e.preventDefault();
      const errBox = document.getElementById('checkoutErrorMsg');
      if (errBox) errBox.classList.add('hidden');

      const rawPhone = document.getElementById('buyerPhone')?.value.trim() || '';
      const cleanPhone = rawPhone.replace(/[^0-9]/g, '');
      if (!/^[6-9]\d{9}$/.test(cleanPhone)) {
        if (errBox) { errBox.innerText = 'Please enter a valid 10-digit Indian mobile number.'; errBox.classList.remove('hidden'); }
        return;
      }
      const buyerPassword = document.getElementById('buyerPassword')?.value || '';
      if (!isLoggedInBuyerSession && buyerPassword.length < 6) {
        if (errBox) { errBox.innerText = 'Secret Edit Password must be at least 6 characters.'; errBox.classList.remove('hidden'); }
        return;
      }

      const btn = document.getElementById('checkoutBtn');
      if (btn) { btn.innerText = 'Creating Order...'; btn.disabled = true; }

      const payload = {
        buyer_name: document.getElementById('buyerName')?.value || '',
        buyer_phone: '+91' + cleanPhone,
        buyer_email: document.getElementById('buyerEmail')?.value || '',
        buyer_password: buyerPassword,
        template_id: currentTemplateId
      };

      try {
        const res = await fetch('/api/create_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          // ADMIN SUPER BYPASS: Logged in Website Owner skips Razorpay completely & gets FREE 1-click access!
          if (data.is_admin_order || (data.order && data.order.payment_status === 'paid')) {
            window.location.href = '/create.php?order_id=' + data.order.order_id;
            return;
          }

          const options = {
            key: data.razorpay_key_id,
            amount: currentPrice * 100,
            currency: 'INR',
            name: '<?php echo defined('APP_NAME') ? APP_NAME : 'GiftReveal'; ?>',
            description: 'Surprise Reveal Page Order',
            order_id: data.order.razorpay_order_id,
            handler: async function(response) {
              await fetch('/api/webhook_razorpay.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                  order_id: data.order.order_id, 
                  razorpay_payment_id: response.razorpay_payment_id,
                  razorpay_order_id: response.razorpay_order_id,
                  razorpay_signature: response.razorpay_signature,
                  status: 'paid' 
                })
              });
              window.location.href = '/create.php?order_id=' + data.order.order_id;
            },
            prefill: { name: payload.buyer_name, email: payload.buyer_email, contact: payload.buyer_phone },
            theme: { color: '#eac34a' }
          };
          const rzp = new Razorpay(options);
          rzp.open();
        } else {
          if (errBox) { errBox.innerText = 'Error: ' + data.message; errBox.classList.remove('hidden'); }
        }
      } catch (err) {
        if (errBox) { errBox.innerText = 'Server error: ' + err.message; errBox.classList.remove('hidden'); }
      } finally {
        if (btn) { btn.innerText = 'Proceed to Pay & Personalize'; btn.disabled = false; }
      }
    }
    <?php endif; ?>
  </script>
  <!-- Sample Library Picker Modal (Top-Level Fail-Safe Modal) -->
  <div id="sampleLibraryModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[9999] flex items-center justify-center p-3 sm:p-5 hidden">
    <div class="bg-[#221f21] border border-[#eac34a]/40 rounded-3xl p-4 sm:p-6 max-w-4xl w-full text-left space-y-4 shadow-2xl relative max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between border-b border-[#4d444b]/40 pb-3 shrink-0">
        <div>
          <h3 class="text-base font-bold font-serif text-[#e8e0e3] flex items-center gap-2">
            <i data-lucide="sparkles" class="w-4 h-4 text-[#eac34a]"></i>
            <span>Sample Romantic Library</span>
          </h3>
          <p class="text-[11px] text-[#d0c3cb] mt-0.5">Tap any photo to add it directly to your scrapbook gallery (Up to 25 photos max).</p>
        </div>
        <a href="javascript:void(0)" onclick="closeSampleLibraryModal()" class="text-[#d0c3cb] hover:text-white text-lg font-bold p-1 cursor-pointer">✕</a>
      </div>

      <!-- Category Filter Pills Bar (Clean scrollbar-none) -->
      <div id="sampleCategoryFilters" class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-[#4d444b]/30 shrink-0 text-xs [scrollbar-width:none] [-ms-overflow-style:none]">
        <button type="button" onclick="filterSampleCategory('all')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-bold text-[11px] transition-all bg-[#eac34a] text-[#241a00] border border-[#eac34a] shadow-md cursor-pointer shrink-0" data-cat="all">All Photos ✨</button>
        <button type="button" onclick="filterSampleCategory('anniversary')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="anniversary">Anniversary 🌹</button>
        <button type="button" onclick="filterSampleCategory('birthday')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="birthday">Birthday 🎂</button>
        <button type="button" onclick="filterSampleCategory('proposal')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="proposal">Proposal 💍</button>
        <button type="button" onclick="filterSampleCategory('raksha_bandhan')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="raksha_bandhan">Rakhi</button>
        <button type="button" onclick="filterSampleCategory('long_distance')" class="sample-cat-pill px-3.5 py-1.5 rounded-full font-medium text-[11px] transition-all bg-[#151215] text-[#d0c3cb] border border-[#4d444b] hover:border-[#eac34a]/60 hover:text-white cursor-pointer shrink-0" data-cat="long_distance">Long Distance ✈️</button>
      </div>

      <!-- Scrollable Grid of Admin Sample Photos -->
      <div id="sampleModalGrid" class="sample-modal-grid pr-1 flex-1 min-h-[300px]">
        <div class="col-span-full text-center py-10 text-[#d0c3cb] text-xs">
          <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-[#eac34a] mb-2"></i>
          Loading sample gallery photos...
        </div>
      </div>

      <div class="pt-3 border-t border-[#4d444b]/40 flex items-center justify-between shrink-0">
        <span class="text-xs text-[#eac34a] font-semibold" id="sampleModalCountLabel">Selected: 0 / 25</span>
        <a href="javascript:void(0)" onclick="closeSampleLibraryModal()" class="px-5 py-2.5 bg-[#eac34a] text-[#241a00] font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-[#ffe088] transition-all shadow-md cursor-pointer">
          Done Selecting
        </a>
      </div>
    </div>
  </div>

</body>
</html>
