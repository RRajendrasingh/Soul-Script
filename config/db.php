<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/api_helper.php';

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $cleanHost = preg_replace('#^https?://#i', '', DB_HOST);
            $cleanHost = rtrim($cleanHost, '/');
            if (empty($cleanHost)) {
                $cleanHost = 'localhost';
            }

            $dsn = "mysql:host=" . $cleanHost . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

            // Core schema synchronization (tables created IF NOT EXISTS)
            // Auto-migrate reasons_list table if missing
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS reasons_list (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    page_id VARCHAR(100) NOT NULL,
                    entry_order INT DEFAULT 0,
                    reason_text TEXT NOT NULL,
                    INDEX idx_reasons_page_id (page_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            } catch (Exception $exRl) { /* ignore */ }

            // Auto-migrate Raksha Bandhan Vouchers & Affiliate Store Tables if missing
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS rakhi_voucher_allocations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    order_id VARCHAR(100) NOT NULL,
                    page_id VARCHAR(100) DEFAULT NULL,
                    allocated_amount INT NOT NULL DEFAULT 100,
                    voucher_code VARCHAR(100) DEFAULT NULL,
                    is_claimed TINYINT(1) DEFAULT 0,
                    claimed_at DATETIME DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY idx_rv_order_id (order_id),
                    INDEX idx_rv_page_id (page_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                $pdo->exec("CREATE TABLE IF NOT EXISTS rakhi_vouchers_vault (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    voucher_code VARCHAR(100) NOT NULL,
                    amount INT NOT NULL DEFAULT 100,
                    status VARCHAR(20) DEFAULT 'available',
                    assigned_order_id VARCHAR(100) DEFAULT NULL,
                    assigned_at DATETIME DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY idx_rvv_code (voucher_code),
                    INDEX idx_rvv_status_amt (status, amount)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                $pdo->exec("CREATE TABLE IF NOT EXISTS affiliate_products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    category VARCHAR(100) DEFAULT 'Rakhi Gift',
                    price_text VARCHAR(50) DEFAULT '₹499',
                    image_url TEXT,
                    affiliate_url TEXT NOT NULL,
                    is_active TINYINT(1) DEFAULT 1,
                    sort_order INT DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Seed initial 4 sample affiliate products if table is empty
                $chkAff = $pdo->query("SELECT COUNT(*) FROM affiliate_products")->fetchColumn();
                if ($chkAff == 0) {
                    $pdo->exec("INSERT INTO affiliate_products (id, title, category, price_text, image_url, affiliate_url, is_active, sort_order) VALUES
                    (1, 'Cadbury Celebrations Premium Rakhi Chocolate Gift Box', 'Chocolates 🍫', '₹349', 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?auto=format&fit=crop&w=400&q=80', 'https://www.amazon.in/dp/B0757FG9X6?tag=babaproduct04-21', 1, 1),
                    (2, 'Titan Karishma Analog Dial Women\'s Luxury Watch', 'Watches ⌚', '₹1,495', 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=400&q=80', 'https://www.amazon.in/dp/B00FLL4K90?tag=babaproduct04-21', 1, 2),
                    (3, 'The Body Shop British Rose Deluxe Beauty Gift Set', 'Skincare 💄', '₹1,895', 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=400&q=80', 'https://www.amazon.in/dp/B01HME57CS?tag=babaproduct04-21', 1, 3),
                    (4, 'Personalized Wooden Photo Frame Keepsake Desk Gift', 'Photo Keepsake 🖼️', '₹499', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=400&q=80', 'https://www.amazon.in/dp/B08F7K9L2P?tag=babaproduct04-21', 1, 4)");
                }
            } catch (Exception $exRakhiV) { /* ignore */ }

            // Auto-migrate templates table extra columns if missing
            $tplCols = [
                'original_price_inr' => "DECIMAL(10,2) DEFAULT NULL",
                'button_text'        => "VARCHAR(100) DEFAULT 'Personalize This Gift 🎁'",
                'demo_url'           => "TEXT DEFAULT NULL",
                'demo_password'      => "VARCHAR(100) DEFAULT NULL",
                'sort_order'         => "INT DEFAULT 0",
                'is_archived'        => "TINYINT(1) NOT NULL DEFAULT 0"
            ];
            foreach ($tplCols as $cName => $cDef) {
                try {
                    $pdo->exec("ALTER TABLE templates ADD COLUMN {$cName} {$cDef}");
                } catch (Exception $exTplCol) {}
            }

            // Sync Raksha Bandhan Royal & Festive Light templates into DB safely if missing
            try {
                $chkRoyal = $pdo->prepare("SELECT COUNT(*) FROM templates WHERE template_id = 'raksha_bandhan_royal'");
                $chkRoyal->execute();
                if ($chkRoyal->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO templates (template_id, name, tagline, description, price_inr, preview_image_url, badge, button_text, demo_url, demo_password, active, sort_order) VALUES 
                    ('raksha_bandhan_royal', 'Raksha Bandhan Royal 👑', 'Shahi Farman Scroll & 3-Step Rakhi Ritual', 'Interactive 3-Step Tilak & Diya ceremony, Sibling Fight Meter, 3D Glass Vows, Shahi Farman Parchment Photo Scroll, and Wax-Sealed Shagun Envelope.', 449, 'https://digitalyogi24.com/assets/default_gallery/sample_fa6955df.webp', 'Royal Special 👑', 'Personalize Royal Gift 🎁', 'https://digitalyogi24.com/gift/manvi-rakhi-v2', 'rakhi', 1, 2)");
                }

                $chkFestive = $pdo->prepare("SELECT COUNT(*) FROM templates WHERE template_id = 'raksha_bandhan_festive_light'");
                $chkFestive->execute();
                if ($chkFestive->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO templates (template_id, name, tagline, description, price_inr, preview_image_url, badge, button_text, demo_url, demo_password, active, sort_order) VALUES 
                    ('raksha_bandhan_festive_light', 'Raksha Bandhan Festive Light 🌸', 'Interactive Scratch Card & 3D Album', 'Real-Time Touch/Mouse Scratch Card, 5-Step Virtual Ceremony, 3D Memory Photobook, Amazon Gift Voucher Reveal, and 300 DPI Physical Keepsakes.', 449, 'https://digitalyogi24.com/assets/default_gallery/sample_fa6955df.webp', 'Festive Special 🌸', 'Personalize Light Gift 🎁', 'https://digitalyogi24.com/gift/ananya-rohan', 'rakhi', 1, 1)");
                }
                $pdo->exec("UPDATE pages SET template_id = 'raksha_bandhan_festive_light' WHERE url_slug IN ('ananya-rohan', 'ritu-rajendra')");
            } catch (Exception $exTpl) {}

            // Auto-heal double HTML entity encoded hint questions in page_content table
            try {
                $pdo->exec("UPDATE page_content SET hint_question = REPLACE(REPLACE(hint_question, '&amp;#039;', '\''), '&#039;', '\'') WHERE hint_question LIKE '%&#039;%' OR hint_question LIKE '%&amp;%'");
            } catch (Exception $exHeal) {}
        } catch (PDOException $e) {
            // Return JSON error response if accessed via API
            if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failure: ' . $e->getMessage()
                ]);
                exit;
            }
            throw $e;
        }
    }
    return $pdo;
}

/**
 * Hash hint answer consistently (trimmed, lowercase, SHA-256 + salt)
 */
function hashHintAnswer($answer) {
    $clean = strtolower(trim($answer));
    return hash('sha256', $clean . HASH_SALT);
}


