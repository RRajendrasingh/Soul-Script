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

            // Auto-Migration check for production database schema synchronization
            $autoMigrateCols = [
                'receiver_photo'  => 'LONGTEXT DEFAULT NULL',
                'tagline_quote'   => 'VARCHAR(500) DEFAULT NULL',
                'favorite_singers'=> 'VARCHAR(255) DEFAULT NULL',
                'song_title'      => 'VARCHAR(255) DEFAULT NULL',
                'song_artist'     => 'VARCHAR(255) DEFAULT NULL',
                'letters_json'    => 'LONGTEXT DEFAULT NULL',
                'tokens_json'     => 'LONGTEXT DEFAULT NULL',
                'reunion_date'    => 'DATE DEFAULT NULL'
            ];
            foreach ($autoMigrateCols as $colName => $colDef) {
                try {
                    $pdo->exec("ALTER TABLE page_content ADD COLUMN {$colName} {$colDef}");
                } catch (Exception $ex) {
                    // Column already exists — safe to ignore
                }
            }
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

            // Auto-migrate templates table extra columns if missing
            $tplCols = [
                'button_text'   => "VARCHAR(100) DEFAULT 'Personalize This Gift 🎁'",
                'demo_url'      => "TEXT DEFAULT NULL",
                'demo_password' => "VARCHAR(100) DEFAULT NULL",
                'sort_order'    => "INT DEFAULT 0"
            ];
            foreach ($tplCols as $cName => $cDef) {
                try {
                    $pdo->exec("ALTER TABLE templates ADD COLUMN {$cName} {$cDef}");
                } catch (Exception $exTplCol) {}
            }

            // Sync Raksha Bandhan Royal template into DB safely
            try {
                $chkRoyal = $pdo->prepare("SELECT COUNT(*) FROM templates WHERE template_id = 'raksha_bandhan_royal'");
                $chkRoyal->execute();
                if ($chkRoyal->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO templates (template_id, name, tagline, description, price_inr, preview_image_url, badge, button_text, demo_url, demo_password, active, sort_order) VALUES 
                    ('raksha_bandhan_royal', 'Raksha Bandhan Royal 👑', 'Shahi Farman Scroll & 3-Step Rakhi Ritual', 'Interactive 3-Step Tilak & Diya ceremony, Sibling Fight Meter, 3D Glass Vows, Shahi Farman Parchment Photo Scroll, and Wax-Sealed Shagun Envelope.', 449, 'https://digitalyogi24.com/assets/default_gallery/sample_fa6955df.webp', 'Royal Special 👑', 'Personalize Royal Gift 🎁', 'https://digitalyogi24.com/gift/mona-aman?theme=raksha_bandhan_royal', 'rakhi', 1, 2)");
                } else {
                    $pdo->exec("UPDATE templates SET active = 1, sort_order = 2 WHERE template_id = 'raksha_bandhan_royal'");
                }
            } catch (Exception $exTpl) {}

            // Auto-Seed raksha_bandhan_special and raksha_bandhan_royal templates if missing
            try {
                $pdo->exec("INSERT INTO templates (template_id, name, tagline, description, price_inr, preview_image_url, badge, button_text, demo_url, demo_password, active, sort_order) VALUES 
                ('raksha_bandhan_special', 'Raksha Bandhan Special 🪔', 'Celebrate the timeless bond of brother and sister', 'Interactive Rakhi tying ceremony, 5 sibling promise cards, childhood memory scrapbook, and digital Shagun envelope reveal.', 449, 'https://digitalyogi24.com/assets/default_gallery/sample_fa6955df.webp', 'Festival Special 🪔', 'Personalize This Gift 🎁', 'https://digitalyogi24.com/gift/manvi-testing', '1234', 1, 5),
                ('raksha_bandhan_royal', 'Raksha Bandhan Royal 👑', 'Shahi Farman Scroll & 3-Step Rakhi Ritual', 'Interactive 3-Step Tilak & Diya ceremony, Sibling Fight Meter, 3D Glass Vows, Shahi Farman Parchment Photo Scroll, and Wax-Sealed Shagun Envelope.', 449, 'https://digitalyogi24.com/assets/default_gallery/sample_fa6955df.webp', 'Royal Special 👑', 'Personalize Royal Gift 🎁', 'https://digitalyogi24.com/gift/mona-aman?theme=raksha_bandhan_royal', 'rakhi', 1, 6)
                ON DUPLICATE KEY UPDATE name=VALUES(name), tagline=VALUES(tagline), description=VALUES(description), demo_url=VALUES(demo_url), demo_password=VALUES(demo_password), active=1");
            } catch (Exception $exTpl) { /* ignore */ }

            // Auto-Seed Rich Content Demo Pages for all templates
            try {
                // Shared Sample Photos
                $demoPhotos = [
                    'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1494774157365-9e04c6720e47?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80'
                ];

                // Demo 1: Anniversary Reveal (ananya-rohan)
                $chk1 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'ananya-rohan'");
                $chk1->execute();
                if ($chk1->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_anniversary_01', 'Rohan Sharma', '+91 98765 43210', 'rohan@example.com', 'anniversary_reveal', 499.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_01', 'ord_demo_anniversary_01', 'anniversary_reveal', 'ananya-rohan', 'token_demo_edit_01', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashShimla = hashHintAnswer('shimla');

                    $lettersJson = json_encode([
                        ['id' => 'let_1', 'title' => 'Open When You Need A Smile 😊', 'category' => 'Love Note', 'content' => 'Ananya, whenever you feel low, remember that you are the brightest light in my life. I am always by your side!'],
                        ['id' => 'let_2', 'title' => 'Open On A Rainy Afternoon 🌧️', 'category' => 'Rainy Mood', 'content' => 'Grab a hot cup of chai and play our song! I promise to make every rainy day magical for us.'],
                        ['id' => 'let_3', 'title' => 'Open When You Miss Me 💖', 'category' => 'Long Distance', 'content' => 'Close your eyes and take a deep breath. Distance is just a test of how far love can travel. See you very soon!']
                    ]);

                    $tokensJson = json_encode([
                        ['id' => 'tok_1', 'title' => '1x Candlelight Dinner 🍷', 'code' => 'DINNER', 'description' => 'Redeemable for a romantic dinner date at your favorite restaurant!'],
                        ['id' => 'tok_2', 'title' => '1x Late Night Drive & Ice Cream 🍦', 'code' => 'DRIVE', 'description' => 'Redeemable anytime for a quiet midnight drive and dark chocolate ice cream.'],
                        ['id' => 'tok_3', 'title' => '1x Relaxing Foot/Back Massage 💆', 'code' => 'MASSAGE', 'description' => 'Redeemable for 30 minutes of pampering spa massage after a tiring day.']
                    ]);

                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, relationship_start_date, receiver_photo, letters_json, tokens_json) VALUES ('page_demo_01', 'Ananya', 'Rohan', 'Where did we take our very first trip together in 2022?', '$passHashShimla', 'Safar Khubsurat h manjil se bhi 🌹', 'Arijit Singh & KK', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview122/v4/91/9c/61/919c61c6-11f8-9a42-70df-89dfaa30560a/mzaf_10793618683517467657.plus.aac.p.m4a', 'Kesariya', 'Arijit Singh', 'Ananya, these past 3 years with you have been the most magical chapter of my life. Happy Anniversary my love!', '2022-08-15', '{$demoPhotos[0]}', " . $pdo->quote($lettersJson) . ", " . $pdo->quote($tokensJson) . ") ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");

                    // Seed Milestones
                    $pdo->exec("DELETE FROM story_milestones WHERE page_id = 'page_demo_01'");
                    $pdo->exec("INSERT INTO story_milestones (page_id, entry_order, milestone_date, title, description) VALUES 
                        ('page_demo_01', 1, '2022-08-15', 'The Rainy Coffee Date', 'Met at the cozy monsoon cafe on a quiet afternoon.'),
                        ('page_demo_01', 2, '2022-12-25', 'First Winter Trip to Shimla', 'Watched the white snowfall together under pine trees.'),
                        ('page_demo_01', 3, '2023-08-15', '365 Days of Togetherness', 'Celebrated 1 full year of non-stop laughter and memories.'),
                        ('page_demo_01', 4, '2024-05-10', 'Moved Into Our First Home', 'Peeled wall paint, set up fairy lights, and started our sanctuary.')");

                    // Seed Media Photos
                    $pdo->exec("DELETE FROM page_media WHERE page_id = 'page_demo_01'");
                    foreach ($demoPhotos as $idx => $pUrl) {
                        $mId = 'media_demo_01_' . ($idx + 1);
                        $pdo->exec("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES ('$mId', 'page_demo_01', '$pUrl', " . ($idx + 1) . ", 'Precious Memory #" . ($idx + 1) . "')");
                    }
                }

                // Demo 2: Birthday Magic (kavya-aarav)
                $chk2 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'kavya-aarav'");
                $chk2->execute();
                if ($chk2->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_birthday_03', 'Aarav Verma', '+91 97777 88888', 'aarav@example.com', 'birthday_magic', 399.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_03', 'ord_demo_birthday_03', 'birthday_magic', 'kavya-aarav', 'token_demo_edit_03', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashJuly = hashHintAnswer('july');
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, partner_dob, receiver_photo) VALUES ('page_demo_03', 'Kavya', 'Aarav', 'What is Kavya\'s favorite birthday month?', '$passHashJuly', 'Wishing the happiest birthday to my sunshine 🎂', 'Shreya Ghoshal', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview116/v4/4a/01/2b/4a012b07-6b60-5629-234b-4bbf5287e07a/mzaf_10915606473185368541.plus.aac.p.m4a', 'Sun Raha Hai Na Tu', 'Shreya Ghoshal', 'Happy Birthday Kavya! May your year ahead be full of endless laughter, dream trips, and warm chai dates!', '2001-07-20', '{$demoPhotos[2]}') ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");

                    // Seed Reasons
                    $pdo->exec("DELETE FROM reasons_list WHERE page_id = 'page_demo_03'");
                    $pdo->exec("INSERT INTO reasons_list (page_id, entry_order, reason_text) VALUES
                        ('page_demo_03', 1, 'Your infectious laugh that brightens up even the dullest days.'),
                        ('page_demo_03', 2, 'How you always remember to bring me warm chai on rainy afternoons.'),
                        ('page_demo_03', 3, 'Your kind, selfless and compassionate heart towards everyone.'),
                        ('page_demo_03', 4, 'Our endless late night conversations about life, dreams & universe.')");

                    // Seed Media Photos
                    $pdo->exec("DELETE FROM page_media WHERE page_id = 'page_demo_03'");
                    foreach ($demoPhotos as $idx => $pUrl) {
                        $mId = 'media_demo_03_' . ($idx + 1);
                        $pdo->exec("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES ('$mId', 'page_demo_03', '$pUrl', " . ($idx + 1) . ", 'Birthday Moment #" . ($idx + 1) . "')");
                    }
                }

                // Demo 3: Perfect Proposal (priya-aman)
                $chk3 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'priya-aman'");
                $chk3->execute();
                if ($chk3->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_proposal_02', 'Aman Patel', '+91 98989 12345', 'aman@example.com', 'perfect_proposal', 599.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_02', 'ord_demo_proposal_02', 'perfect_proposal', 'priya-aman', 'token_demo_edit_02', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashParis = hashHintAnswer('paris');
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, love_letter_text, receiver_photo) VALUES ('page_demo_02', 'Priya', 'Aman', 'What city is featured on our dream bucket-list wall art?', '$passHashParis', 'You are my home and my forever 💍', 'Atif Aslam', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview115/v4/bf/20/8a/bf208a04-aa24-5d9c-1123-ecadfb7a38b1/mzaf_16238805904724032486.plus.aac.p.m4a', 'Tera Hone Laga Hoon', 'Atif Aslam', 'Priya, from the moment you walked into my life, everything became brighter. Will you marry me?', 'Dearest Priya,\n\nFrom the very first afternoon we shared a quiet coffee together under the soft rain, I knew in my heart that you were different. Your smile has a way of making the rest of the world fade into quiet background noise.\n\nThrough every milestone, every late-night conversation, and every spontaneous trip, you have been my favorite part of every day. With you, home is no longer a place—it\'s a feeling, and that feeling is wherever you are.\n\nToday, I want to ask you the most important question of my life. Will you take my hand and walk through forever together with me?', '{$demoPhotos[1]}') ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");

                    // Seed Media Photos
                    $pdo->exec("DELETE FROM page_media WHERE page_id = 'page_demo_02'");
                    foreach ($demoPhotos as $idx => $pUrl) {
                        $mId = 'media_demo_02_' . ($idx + 1);
                        $pdo->exec("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES ('$mId', 'page_demo_02', '$pUrl', " . ($idx + 1) . ", 'Proposal Memory #" . ($idx + 1) . "')");
                    }
                }

                // Demo 4: Long Distance Love (aanya-kabir)
                $chk4 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'aanya-kabir'");
                $chk4->execute();
                if ($chk4->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_distance_04', 'Kabir Mehta', '+91 96666 55555', 'kabir@example.com', 'long_distance_love', 449.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_04', 'ord_demo_distance_04', 'long_distance_love', 'aanya-kabir', 'token_demo_edit_04', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashMumbai = hashHintAnswer('mumbai');
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, buyer_city, buyer_timezone, partner_city, partner_timezone, reunion_date, receiver_photo) VALUES ('page_demo_04', 'Aanya', 'Kabir', 'In which city did Kabir first whisper I love you?', '$passHashMumbai', 'Miles apart but connected by heart ✈️', 'KK', 'https://audio-ssl.itunes.apple.com/itunes-assets/AudioPreview125/v4/9e/10/72/9e10729e-64c9-6e3e-4d43-228723c34f2d/mzaf_7824177439169654160.plus.aac.p.m4a', 'Dil Ibadat', 'KK', 'Distance means so little when someone means so much. Counting down every single day until our next hug in Mumbai!', 'Delhi', 'Asia/Kolkata', 'Mumbai', 'Asia/Kolkata', DATE_ADD(NOW(), INTERVAL 30 DAY), '{$demoPhotos[3]}') ON DUPLICATE KEY UPDATE tagline_quote=VALUES(tagline_quote)");

                    // Seed Media Photos
                    $pdo->exec("DELETE FROM page_media WHERE page_id = 'page_demo_04'");
                    foreach ($demoPhotos as $idx => $pUrl) {
                        $mId = 'media_demo_04_' . ($idx + 1);
                        $pdo->exec("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES ('$mId', 'page_demo_04', '$pUrl', " . ($idx + 1) . ", 'Distance Memory #" . ($idx + 1) . "')");
                    }
                }

                // Demo 5: Raksha Bandhan Special (mona-aman)
                $chk5 = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = 'mona-aman'");
                $chk5->execute();
                if ($chk5->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_rakhi_05', 'Aman Sharma', '+91 97777 88888', 'aman@example.com', 'raksha_bandhan_special', 449.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                    $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_05', 'ord_demo_rakhi_05', 'raksha_bandhan_special', 'mona-aman', 'token_demo_edit_05', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live'");
                    $passHashRakhi = hashHintAnswer('rakhi');
                    $shagunTokensJson = json_encode([['shagun_voucher_code' => 'AMZ-RAKHI-9876']]);
                    $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, tokens_json, receiver_photo) VALUES ('page_demo_05', 'Mona', 'Aman', 'What sweet dish did Aman steal from Mona on last Diwali? (Hint: rakhi)', '$passHashRakhi', 'World\'s Best Sister 👑', 'Kishore Kumar', 'https://youtube.com/shorts/C-zaRcKXEP0', 'Phoolon Ka Taron Ka', 'Raksha Bandhan Special', 'Choti / Didi, mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🧧 (Aur haan, TV remote mera hi रहेगा! 😄)', '$shagunTokensJson', '{$demoPhotos[2]}') ON DUPLICATE KEY UPDATE partner_name=VALUES(partner_name), buyer_name=VALUES(buyer_name), hint_question=VALUES(hint_question), hint_answer_hash=VALUES(hint_answer_hash), tagline_quote=VALUES(tagline_quote), bg_music_url=VALUES(bg_music_url), song_title=VALUES(song_title), song_artist=VALUES(song_artist), love_note_text=VALUES(love_note_text), tokens_json=VALUES(tokens_json), receiver_photo=VALUES(receiver_photo)");
                    $pdo->exec("UPDATE page_content SET bg_music_url = 'https://youtube.com/shorts/C-zaRcKXEP0', song_title = 'Phoolon Ka Taron Ka', song_artist = 'Raksha Bandhan Special' WHERE page_id IN (SELECT page_id FROM pages WHERE url_slug = 'mona-aman')");

                    // Seed 5 Sibling Promises
                    $pdo->exec("DELETE FROM reasons_list WHERE page_id = 'page_demo_05'");
                    $rakhiPromises = [
                        "Always protect you and stand by your side 🛡️",
                        "Keep all your deepest secrets safe 🤫",
                        "Sponsor your favorite food and treat you 🍕",
                        "Never let you feel alone, no matter where I am 💖",
                        "Always be your forever crime partner 🕵️‍♂️"
                    ];
                    foreach ($rakhiPromises as $idx => $pText) {
                        $pdo->exec("INSERT INTO reasons_list (page_id, entry_order, reason_text) VALUES ('page_demo_05', " . ($idx + 1) . ", " . $pdo->quote($pText) . ")");
                    }

                    // Seed Media Photos with Memory Captions
                    $pdo->exec("DELETE FROM page_media WHERE page_id = 'page_demo_05'");
                    $rakhiCaptions = [
                        'First Coffee Date Together ☕',
                        'Sunset Beach Trip Memories 🌅',
                        'TV Remote Fight Day 😄',
                        'Late Night Maggi Party 🍜',
                        'Diwali Sweets Stealing 🪔',
                        'Best Sibling Hug Ever 🤗'
                    ];
                    foreach ($demoPhotos as $idx => $pUrl) {
                        $mId = 'media_demo_05_' . ($idx + 1);
                        $cap = $rakhiCaptions[$idx] ?? ('Sibling Memory #' . ($idx + 1));
                        $pdo->exec("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES ('$mId', 'page_demo_05', '$pUrl', " . ($idx + 1) . ", " . $pdo->quote($cap) . ")");
                    }
                }

                // Demo 6: Raksha Bandhan Royal Standalone Preview (manvi-rakhi-v2)
                $pdo->exec("INSERT INTO orders (order_id, buyer_name, buyer_phone, buyer_email, template_id, amount_paid, payment_status) VALUES ('ord_demo_rakhi_v2', 'Rajendra', '+91 97777 88888', 'rajendra@example.com', 'raksha_bandhan_royal', 449.00, 'paid') ON DUPLICATE KEY UPDATE payment_status='paid'");
                $pdo->exec("INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, expires_at) VALUES ('page_demo_rakhi_v2', 'ord_demo_rakhi_v2', 'raksha_bandhan_royal', 'manvi-rakhi-v2', 'token_demo_edit_rakhi_v2', 'live', DATE_ADD(NOW(), INTERVAL 10 YEAR)) ON DUPLICATE KEY UPDATE status='live', template_id='raksha_bandhan_royal'");
                $passHashRakhiV2 = hashHintAnswer('rakhi');
                $shagunTokensJsonV2 = json_encode([['shagun_voucher_code' => 'AMZ-ROYAL-RAKHI-2026']]);
                $pdo->exec("INSERT INTO page_content (page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, song_title, song_artist, love_note_text, tokens_json, receiver_photo) VALUES ('page_demo_rakhi_v2', 'Manvi', 'Rajendra', 'What is our special Rakhi secret word? (Hint: RAKHI)', '$passHashRakhiV2', 'World\'s Best Sister 👑', 'Kishore Kumar', 'https://youtube.com/shorts/C-zaRcKXEP0', 'Phoolon Ka Taron Ka', 'Raksha Bandhan Special', 'Manvi Didi, mera saara pyaar aur dher saare aashirwaad iss lifafe mein h! 🧧 (Aur haan, TV remote mera hi रहेगा! 😄)', '$shagunTokensJsonV2', '{$demoPhotos[2]}') ON DUPLICATE KEY UPDATE partner_name=VALUES(partner_name), buyer_name=VALUES(buyer_name), hint_question=VALUES(hint_question), hint_answer_hash=VALUES(hint_answer_hash), tagline_quote=VALUES(tagline_quote), bg_music_url=VALUES(bg_music_url), song_title=VALUES(song_title), song_artist=VALUES(song_artist), love_note_text=VALUES(love_note_text), tokens_json=VALUES(tokens_json), receiver_photo=VALUES(receiver_photo)");
                $pdo->exec("UPDATE page_content SET bg_music_url = 'https://youtube.com/shorts/C-zaRcKXEP0', song_title = 'Phoolon Ka Taron Ka', song_artist = 'Raksha Bandhan Special' WHERE page_id IN (SELECT page_id FROM pages WHERE url_slug = 'manvi-rakhi-v2')");

                    // Seed 5 Sibling Promises
                    $pdo->exec("DELETE FROM reasons_list WHERE page_id = 'page_demo_rakhi_v2'");
                    $rakhiPromisesV2 = [
                        "Always protect you and stand by your side 🛡️",
                        "Keep all your deepest secrets safe 🤫",
                        "Sponsor your favorite food and treat you 🍕",
                        "Never let you feel alone, no matter where I am 💖",
                        "Always be your forever crime partner 🕵️‍♂️"
                    ];
                    foreach ($rakhiPromisesV2 as $idx => $pText) {
                        $pdo->exec("INSERT INTO reasons_list (page_id, entry_order, reason_text) VALUES ('page_demo_rakhi_v2', " . ($idx + 1) . ", " . $pdo->quote($pText) . ")");
                    }

                    // Seed Media Photos for Shahi Farman Scroll
                    $pdo->exec("DELETE FROM page_media WHERE page_id = 'page_demo_rakhi_v2'");
                    $rakhiCaptionsV2 = [
                        'First Childhood Photo ☕',
                        'Trip to Shimla 🌅',
                        'TV Remote Fight Day 😄',
                        'Maggi Night Party 🍜',
                        'Diwali Sweets Stealing 🪔',
                        'Best Sibling Hug 🤗'
                    ];
                    foreach ($demoPhotos as $idx => $pUrl) {
                        $mId = 'media_demo_rv2_' . ($idx + 1);
                        $cap = $rakhiCaptionsV2[$idx] ?? ('Sibling Memory #' . ($idx + 1));
                        $pdo->exec("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES ('$mId', 'page_demo_rakhi_v2', '$pUrl', " . ($idx + 1) . ", " . $pdo->quote($cap) . ")");
                    }
                }

                // Auto-heal double HTML entity encoded hint questions in page_content table
                $pdo->exec("UPDATE page_content SET hint_question = REPLACE(REPLACE(hint_question, '&amp;#039;', '\''), '&#039;', '\'') WHERE hint_question LIKE '%&#039;%' OR hint_question LIKE '%&amp;%'");
            } catch (Exception $exSeed) {
                // Ignore seed errors silently
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
            }
            // Ensure demo seeds are active
            try {
                initDatabase($pdo);
            } catch (Exception $exSeed) { /* ignore */ }
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
