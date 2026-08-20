<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';

$messageSent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if JSON payload
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input)) {
            $_POST = $input;
        }
    }

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? 'General Inquiry');
    $msg     = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($msg)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } else {
        try {
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

            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $msg]);

            // Attempt email dispatch
            $adminEmail = 'support@giftreveal.in';
            $appName = defined('APP_NAME') ? APP_NAME : 'GiftReveal';
            $mailSubject = "📬 New Customer Inquiry from " . $name . " - " . $appName;
            $mailBody = "New message received from website contact form:\n\nName: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$msg\n\nTime: " . date('Y-m-d H:i:s');
            $headers = "From: " . $appName . " <no-reply@giftreveal.in>\r\nReply-To: $email";
            @mail($adminEmail, $mailSubject, $mailBody, $headers);

            $messageSent = true;

            if (stripos($contentType, 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been received.']);
                exit;
            }
        } catch (Exception $e) {
            $error = 'Unable to save your message right now. Please email us directly at support@giftreveal.in';
            if (stripos($contentType, 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error]);
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $pageTitle = 'Contact Us — ' . APP_NAME . ' | Support & Inquiries';
  require_once __DIR__ . '/includes/head.php'; 
  ?>
</head>
<body class="bg-[#151215] text-[#e8e0e3] font-sans min-h-screen relative overflow-x-hidden selection:bg-[#eac34a]/30 selection:text-[#ffe088]">

  <!-- Ambient Glows -->
  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#3b1e3b]/30 blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-[#cca830]/10 blur-[130px]"></div>
  </div>

  <!-- Header -->
  <?php 
  $current_page = 'contact';
  require_once __DIR__ . '/includes/header.php'; 
  ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-36 pb-20 relative z-10 space-y-12">
    
    <!-- Hero Header -->
    <div class="text-center space-y-4">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#3b1e3b]/60 border border-[#eac34a]/30 text-[#eac34a] text-xs font-semibold uppercase tracking-widest">
        <i data-lucide="mail" class="w-3.5 h-3.5"></i>
        <span>Get in Touch</span>
      </div>
      <h1 class="text-3xl sm:text-5xl font-bold font-serif text-[#e8e0e3] tracking-tight">
        We're Here to <span class="bg-gradient-to-r from-[#eac34a] via-[#ffe088] to-[#e4b9df] bg-clip-text text-transparent">Help You</span>
      </h1>
      <p class="text-sm sm:text-base text-[#d0c3cb] max-w-xl mx-auto leading-relaxed">
        Have questions about custom surprises, your order, or business partnerships? Reach out to our dedicated support team.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
      
      <!-- Contact Details Info Card (2 cols) -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-[#221f21]/90 rounded-3xl border border-[#4d444b]/40 p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
          <h2 class="text-xl font-serif font-bold text-[#eac34a] flex items-center gap-2.5">
            <i data-lucide="headphones" class="w-5 h-5 text-[#eac34a]"></i>
            <span>Customer Support</span>
          </h2>

          <div class="space-y-4 text-xs sm:text-sm">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center shrink-0 border border-[#eac34a]/30 mt-0.5">
                <i data-lucide="mail" class="w-4 h-4"></i>
              </div>
              <div>
                <span class="block text-[11px] text-[#d0c3cb]/60 uppercase tracking-wider font-semibold">Email Us</span>
                <a href="mailto:support@giftreveal.in" class="text-[#e8e0e3] font-medium hover:text-[#eac34a] transition-colors">
                  support@giftreveal.in
                </a>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center shrink-0 border border-[#eac34a]/30 mt-0.5">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
              </div>
              <div>
                <span class="block text-[11px] text-[#d0c3cb]/60 uppercase tracking-wider font-semibold">Operating Address</span>
                <address class="not-italic text-[#d0c3cb]/90 leading-snug">
                  <?php echo APP_NAME; ?><br>
                  Alwar, Rajasthan, India
                </address>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-[#3b1e3b] text-[#eac34a] flex items-center justify-center shrink-0 border border-[#eac34a]/30 mt-0.5">
                <i data-lucide="clock" class="w-4 h-4"></i>
              </div>
              <div>
                <span class="block text-[11px] text-[#d0c3cb]/60 uppercase tracking-wider font-semibold">Response Time</span>
                <span class="text-[#d0c3cb]/90">Within 2–4 hours during business hours</span>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-[#4d444b]/40">
            <p class="text-[11px] text-[#d0c3cb]/70 leading-relaxed">
              Already have an active order? You can log in directly at the <a href="<?php echo APP_URL; ?>/edit.php" class="text-[#eac34a] underline hover:text-[#ffe088]">Buyer Portal</a> to manage your reveal.
            </p>
          </div>
        </div>
      </div>

      <!-- Contact Message Form (3 cols) -->
      <div class="lg:col-span-3">
        <div class="bg-[#221f21]/90 rounded-3xl border border-[#4d444b]/40 p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
          <h2 class="text-xl font-serif font-bold text-[#e8e0e3] mb-6">Send Us a Message</h2>

          <!-- Success Message Container -->
          <div id="contactSuccessMsg" class="<?php echo $messageSent ? '' : 'hidden'; ?> p-6 rounded-2xl bg-[#3b1e3b]/80 border border-[#eac34a] text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-[#eac34a] text-[#241a00] flex items-center justify-center mx-auto shadow-lg">
              <i data-lucide="check" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-serif font-bold text-[#ffe088]">Thank You!</h3>
            <p class="text-xs sm:text-sm text-[#d0c3cb]">
              Your message has been received. Our support team will get back to you shortly at your email.
            </p>
            <button type="button" onclick="resetContactForm()" class="mt-2 px-4 py-1.5 rounded-full bg-[#151215] border border-[#4d444b] text-xs text-[#eac34a] hover:bg-[#3b1e3b] transition-all">
              Send Another Message
            </button>
          </div>

          <!-- Error Alert -->
          <div id="contactErrorMsg" class="<?php echo $error ? '' : 'hidden'; ?> mb-4 p-3 rounded-xl bg-red-950/60 border border-red-500/50 text-xs text-red-200">
            <?php echo htmlspecialchars($error); ?>
          </div>

          <form id="contactForm" method="POST" action="" class="<?php echo $messageSent ? 'hidden' : ''; ?> space-y-4" onsubmit="handleContactSubmit(event)">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-[#d0c3cb] mb-1.5 uppercase tracking-wider">Your Name *</label>
                <input type="text" id="contactName" name="name" required class="w-full px-4 py-2.5 rounded-xl bg-[#151215] border border-[#4d444b]/60 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:ring-1 focus:ring-[#eac34a] outline-none transition-all placeholder-[#d0c3cb]/30" placeholder="e.g. Rahul Sharma">
              </div>
              <div>
                <label class="block text-xs font-semibold text-[#d0c3cb] mb-1.5 uppercase tracking-wider">Email Address *</label>
                <input type="email" id="contactEmail" name="email" required class="w-full px-4 py-2.5 rounded-xl bg-[#151215] border border-[#4d444b]/60 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:ring-1 focus:ring-[#eac34a] outline-none transition-all placeholder-[#d0c3cb]/30" placeholder="rahul@example.com">
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-[#d0c3cb] mb-1.5 uppercase tracking-wider">Subject</label>
              <input type="text" id="contactSubject" name="subject" class="w-full px-4 py-2.5 rounded-xl bg-[#151215] border border-[#4d444b]/60 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:ring-1 focus:ring-[#eac34a] outline-none transition-all placeholder-[#d0c3cb]/30" placeholder="Order inquiry, Customization, or Feedback">
            </div>

            <div>
              <label class="block text-xs font-semibold text-[#d0c3cb] mb-1.5 uppercase tracking-wider">Your Message *</label>
              <textarea id="contactText" name="message" rows="4" required class="w-full px-4 py-2.5 rounded-xl bg-[#151215] border border-[#4d444b]/60 text-sm text-[#e8e0e3] focus:border-[#eac34a] focus:ring-1 focus:ring-[#eac34a] outline-none transition-all placeholder-[#d0c3cb]/30 resize-none" placeholder="Tell us how we can help you..."></textarea>
            </div>

            <button type="submit" id="contactSubmitBtn" class="w-full py-3 rounded-full bg-[#eac34a] hover:bg-[#ffe088] text-[#241a00] text-xs sm:text-sm font-bold uppercase tracking-[0.15em] shadow-[0_0_20px_rgba(234,195,74,0.3)] hover:shadow-[0_0_30px_rgba(234,195,74,0.5)] transition-all flex items-center justify-center gap-2">
              <i data-lucide="send" class="w-4 h-4"></i>
              <span id="contactBtnText">Send Message</span>
            </button>
          </form>
        </div>
      </div>

    </div>

  </main>

  <!-- Footer -->
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
  
  <script>
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }

    async function handleContactSubmit(e) {
      e.preventDefault();
      const btn = document.getElementById('contactSubmitBtn');
      const btnText = document.getElementById('contactBtnText');
      const errorDiv = document.getElementById('contactErrorMsg');
      const successDiv = document.getElementById('contactSuccessMsg');
      const form = document.getElementById('contactForm');

      const payload = {
        name: document.getElementById('contactName').value.trim(),
        email: document.getElementById('contactEmail').value.trim(),
        subject: document.getElementById('contactSubject').value.trim() || 'General Inquiry',
        message: document.getElementById('contactText').value.trim()
      };

      if (!payload.name || !payload.email || !payload.message) {
        errorDiv.innerText = 'Please fill in all required fields.';
        errorDiv.classList.remove('hidden');
        return;
      }

      btn.disabled = true;
      btnText.innerText = 'Sending...';
      errorDiv.classList.add('hidden');

      try {
        const res = await fetch('contact.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          form.classList.add('hidden');
          successDiv.classList.remove('hidden');
          form.reset();
        } else {
          errorDiv.innerText = data.message || 'Something went wrong. Please try again.';
          errorDiv.classList.remove('hidden');
        }
      } catch (err) {
        // Fallback to classic form submit
        form.submit();
      } finally {
        btn.disabled = false;
        btnText.innerText = 'Send Message';
      }
    }

    function resetContactForm() {
      document.getElementById('contactSuccessMsg').classList.add('hidden');
      document.getElementById('contactForm').classList.remove('hidden');
    }
  </script>
</body>
</html>
