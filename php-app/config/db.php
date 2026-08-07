<?php
require_once __DIR__ . '/config.php';

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

            // Auto-Migration check for production database schema synchronization
            $autoMigrateCols = [
                'receiver_photo' => 'LONGTEXT DEFAULT NULL',
                'tagline_quote' => 'VARCHAR(500) DEFAULT NULL',
                'favorite_singers' => 'VARCHAR(255) DEFAULT NULL',
                'song_title' => 'VARCHAR(255) DEFAULT NULL',
                'song_artist' => 'VARCHAR(255) DEFAULT NULL',
                'letters_json' => 'LONGTEXT DEFAULT NULL',
                'tokens_json' => 'LONGTEXT DEFAULT NULL'
            ];
            foreach ($autoMigrateCols as $colName => $colDef) {
                try {
                    $pdo->exec("ALTER TABLE page_content ADD COLUMN {$colName} {$colDef}");
                } catch (Exception $ex) {
                    // Column already exists or ignore
                }
            }

            // Auto-Seed Demo Pages for all 4 templates if missing
            try {
                // Demo 1: Anniversary Reveal (ananya-rohan)
                $chk1 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'ananya-rohan'");
                $chk1->execute();
                if ($chk1->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_anniversary_01', 'Rohan Sharma', '+91 98765 43210', 'rohan@example.com', 'anniversary_reveal', 499.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_01', 'ord_demo_anniversary_01', 'anniversary_reveal', 'ananya-rohan', 'token_demo_edit_01', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashShimla = hashHintAnswer('shimla');
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, relationship_start_date) VALUES ('page_demo_01', 'Ananya', 'Rohan', 'Where did we take our very first trip together in 2022?', '$passHashShimla', 'Safar Khubsurat h manjil se bhi 🌹', 'Arijit Singh & KK', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview122/v4/91/9c/61/919c61c6-11f8-9a42-70df-89dfaa30560a/mzaf_10793618683517467657.plus.aac.p.m4a', 'Kesariya', 'Arijit Singh', 'Ananya, these past 3 years with you have been the most magical chapter of my life. Happy Anniversary my love!', '2022-08-15') ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");
                }

                // Demo 2: Birthday Magic (kavya-aarav)
                $chk2 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'kavya-aarav'");
                $chk2->execute();
                if ($chk2->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_birthday_03', 'Aarav Verma', '+91 97777 88888', 'aarav@example.com', 'birthday_magic', 399.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_03', 'ord_demo_birthday_03', 'birthday_magic', 'kavya-aarav', 'token_demo_edit_03', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashJuly = hashHintAnswer('july');
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, partner_dob) VALUES ('page_demo_03', 'Kavya', 'Aarav', 'What is Kavya\'s favorite birthday month?', '$passHashJuly', 'Wishing the happiest birthday to my sunshine 🎂', 'Shreya Ghoshal', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview116/v4/4a/01/2b/4a012b07-6b60-5629-234b-4bbf5287e07a/mzaf_10915606473185368541.plus.aac.p.m4a', 'Sun Raha Hai Na Tu', 'Shreya Ghoshal', 'Happy Birthday Kavya! May your year ahead be full of endless laughter, dream trips, and warm chai dates!', '2001-07-20') ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");
                }
                // Auto-heal double HTML entity encoded hint questions in page_content table
                $pdo->exec("UPDATE page_content SET hint_question = REPLACE(REPLACE(hint_question, '&amp;#039;', '\''), '&#039;', '\'') WHERE hint_question LIKE '%&#039;%' OR hint_question LIKE '%&amp;%'");

                // Demo 3: Perfect Proposal (priya-aman)
                $chk3 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'priya-aman'");
                $chk3->execute();
                if ($chk3->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_proposal_02', 'Aman Patel', '+91 98989 12345', 'aman@example.com', 'perfect_proposal', 599.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_02', 'ord_demo_proposal_02', 'perfect_proposal', 'priya-aman', 'token_demo_edit_02', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashParis = hashHintAnswer('paris');
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, love_letter_text) VALUES ('page_demo_02', 'Priya', 'Aman', 'What city is featured on our dream bucket-list wall art?', '$passHashParis', 'You are my home and my forever 💍', 'Atif Aslam', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview115/v4/bf/20/8a/bf208a04-aa24-5d9c-1123-ecadfb7a38b1/mzaf_16238805904724032486.plus.aac.p.m4a', 'Tera Hone Laga Hoon', 'Atif Aslam', 'Priya, from the moment you walked into my life, everything became brighter. Will you marry me?', 'Dearest Priya,\n\nI remember the exact moment I realized I wanted to spend the rest of my life with you. Will you take my hand and start our forever today?') ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");
                }

                // Demo 4: Long Distance Love (aanya-kabir)
                $chk4 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'aanya-kabir'");
                $chk4->execute();
                if ($chk4->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_distance_04', 'Kabir Mehta', '+91 96666 55555', 'kabir@example.com', 'long_distance_love', 449.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_04', 'ord_demo_distance_04', 'long_distance_love', 'aanya-kabir', 'token_demo_edit_04', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashMumbai = hashHintAnswer('mumbai');
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, buyer_city, buyer_timezone, partner_city, partner_timezone) VALUES ('page_demo_04', 'Aanya', 'Kabir', 'In which city did Kabir first whisper I love you?', '$passHashMumbai', 'Miles apart but connected by heart ✈️', 'KK', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview125/v4/9e/10/72/9e10729e-64c9-6e3e-4d43-228723c34f2d/mzaf_7824177439169654160.plus.aac.p.m4a', 'Dil Ibadat', 'KK', 'Distance means so little when someone means so much. Counting down every single day until our next hug in Mumbai!', 'Delhi', 'Asia/Kolkata', 'Mumbai', 'Asia/Kolkata') ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");
                }
            } catch (Exception $exSeed) {
                // Ignore seed errors
            }
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
            } else {
                die('Database Connection Error: ' . $e->getMessage());
            }
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
