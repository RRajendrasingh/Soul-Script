<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/email_helper.php';
require_once __DIR__ . '/../includes/media_helper.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$order_id       = trim($input['order_id'] ?? '');
$partner_name   = trim($input['partner_name'] ?? '');
$hint_question  = trim($input['hint_question'] ?? '');
$hint_answer    = trim($input['hint_answer'] ?? '');
$love_note_text = trim($input['love_note_text'] ?? '');
$custom_slug    = trim($input['custom_slug'] ?? '');
$photos         = $input['photos'] ?? []; // Array of base64 data URLs or uploaded URLs
if (is_array($photos) && count($photos) > 26) {
    $photos = array_slice($photos, 0, 26);
}

if (!$order_id || !$partner_name || !$hint_question || !$hint_answer) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required partner & hint fields']);
    exit;
}

// Partner Name length (2 to 60)
if (mb_strlen($partner_name) < 2 || mb_strlen($partner_name) > 60) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Partner name must be between 2 and 60 characters.']);
    exit;
}

// Hint Question length (5 to 200)
if (mb_strlen($hint_question) < 5 || mb_strlen($hint_question) > 200) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hint question must be between 5 and 200 characters.']);
    exit;
}

// Hint Answer length (2 to 100)
if (mb_strlen($hint_answer) < 2 || mb_strlen($hint_answer) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hint answer must be between 2 and 100 characters.']);
    exit;
}

// Love Note max length (1000)
if (mb_strlen($love_note_text) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Love note text cannot exceed 1000 characters.']);
    exit;
}

try {
    $db = getDB();

    // 1. Enforce Server-Side Payment Status Check
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Associated order not found']);
        exit;
    }

    if ($order['payment_status'] !== 'paid') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Payment required: Partner details form is only accessible after payment confirmation.'
        ]);
        exit;
    }

    // 2. Generate Path-based Slug
    $rawSlug = $custom_slug ?: ($partner_name . '-' . $order['buyer_name']);
    $baseSlug = sanitizeSlug($rawSlug) ?: ('gift-' . rand(100, 999));
    $slug = $baseSlug;

    // Collision check
    $stmt = $db->prepare("SELECT COUNT(*) FROM pages WHERE url_slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetchColumn() > 0) {
        $slug = $baseSlug . '-' . rand(100, 999);
    }

    // 3. Generate IDs and Hashing
    $page_id = 'page_' . time() . '_' . rand(100, 999);
    $edit_token = bin2hex(random_bytes(32)); // 64 char random token
    $hint_answer_hash = hashHintAnswer($hint_answer);
    $expires_at = date('Y-m-d H:i:s', strtotime('+12 months'));

    $template_id = $order['template_id'];

    // 4. Save Page Record
    $stmt = $db->prepare("
        INSERT INTO pages (page_id, order_id, template_id, url_slug, edit_token, status, created_at, expires_at)
        VALUES (?, ?, ?, ?, ?, 'live', NOW(), ?)
    ");
    $stmt->execute([$page_id, $order_id, $template_id, $slug, $edit_token, $expires_at]);

    // 5. Save Page Content
    $template_fields = $input['template_fields'] ?? [];

    $tagline_quote           = trim($input['tagline_quote'] ?? ($template_fields['tagline_quote'] ?? 'Safar Khubsurat h manjil se bhi 🌹'));
    $favorite_singers        = trim($input['favorite_singers'] ?? ($template_fields['favorite_singers'] ?? 'Arijit Singh & KK'));
    $bg_music_url            = trim($input['bg_music_url'] ?? ($template_fields['bg_music_url'] ?? 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3'));
    $letters_json            = isset($input['letters']) ? json_encode($input['letters']) : (isset($template_fields['letters']) ? json_encode($template_fields['letters']) : null);
    $tokens_json             = isset($input['tokens']) ? json_encode($input['tokens']) : (isset($template_fields['tokens']) ? json_encode($template_fields['tokens']) : null);

    $relationship_start_date = $template_fields['relationship_start_date'] ?? null;
    $partner_dob             = $template_fields['partner_dob'] ?? null;
    $love_letter_text        = $template_fields['love_letter_text'] ?? null;
    $buyer_city              = $template_fields['buyer_city'] ?? null;
    $buyer_timezone          = $template_fields['buyer_timezone'] ?? null;
    $partner_city            = $template_fields['partner_city'] ?? null;
    $partner_timezone        = $template_fields['partner_timezone'] ?? null;
    $reunion_date            = $template_fields['reunion_date'] ?? null;
    $playlist_url            = $template_fields['playlist_url'] ?? null;
    $song_title              = isset($input['song_title']) && trim($input['song_title']) !== '' ? trim($input['song_title']) : ($template_fields['song_title'] ?? null);
    $song_artist             = isset($input['song_artist']) && trim($input['song_artist']) !== '' ? trim($input['song_artist']) : ($template_fields['song_artist'] ?? ($favorite_singers ?: null));
    $receiver_photo          = $input['receiver_photo'] ?? null;
    if (!empty($receiver_photo)) {
        $receiver_photo = saveUploadedBase64Image($receiver_photo, $page_id, 'partner_avatar');
    }

    $stmt = $db->prepare("
        INSERT INTO page_content (
            page_id, partner_name, buyer_name, hint_question, hint_answer_hash, tagline_quote, favorite_singers, bg_music_url, receiver_photo, letters_json, tokens_json, love_note_text,
            relationship_start_date, partner_dob, love_letter_text, buyer_city, buyer_timezone,
            partner_city, partner_timezone, reunion_date, playlist_url, song_title, song_artist
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $page_id,
        $partner_name,
        $order['buyer_name'],
        $hint_question,
        $hint_answer_hash,
        $tagline_quote,
        $favorite_singers,
        $bg_music_url,
        $receiver_photo,
        $letters_json,
        $tokens_json,
        $love_note_text,
        $relationship_start_date,
        $partner_dob,
        $love_letter_text,
        $buyer_city,
        $buyer_timezone,
        $partner_city,
        $partner_timezone,
        $reunion_date,
        $playlist_url,
        $song_title,
        $song_artist
    ]);

    // 6. Handle Repeatable Entries (Milestones / Reasons)
    if ($template_id === 'anniversary_reveal' && !empty($template_fields['milestones'])) {
        $stmtM = $db->prepare("INSERT INTO story_milestones (page_id, entry_order, milestone_date, title, description) VALUES (?, ?, ?, ?, ?)");
        foreach ($template_fields['milestones'] as $idx => $m) {
            if (!empty($m['title'])) {
                $stmtM->execute([$page_id, $idx + 1, $m['date'] ?? '', $m['title'], $m['description'] ?? '']);
            }
        }
    }

    if (($template_id === 'birthday_magic' || $template_id === 'raksha_bandhan_royal' || $template_id === 'raksha_bandhan_festive_light') && !empty($template_fields['reasons'])) {
        $stmtR = $db->prepare("INSERT INTO reasons_list (page_id, entry_order, reason_text) VALUES (?, ?, ?)");
        foreach ($template_fields['reasons'] as $idx => $reason) {
            if (!empty($reason)) {
                $stmtR->execute([$page_id, $idx + 1, $reason]);
            }
        }
    }

    $shagun_voucher_code = trim($template_fields['shagun_voucher_code'] ?? $input['shagun_voucher_code'] ?? '');
    $selected_rakhi_design = trim($template_fields['selected_rakhi_design'] ?? $input['selected_rakhi_design'] ?? 'gold_zardosi');

    $tokens = [];
    if (!empty($shagun_voucher_code)) {
        $tokens[] = [
            'shagun_voucher_code' => $shagun_voucher_code,
            'selected_rakhi_design' => $selected_rakhi_design
        ];
    } else {
        $tokens[] = [
            'selected_rakhi_design' => $selected_rakhi_design
        ];
    }
    $tokens_json = json_encode($tokens);
    if (!empty($tokens)) {
        $db->prepare("UPDATE page_content SET tokens_json = ? WHERE page_id = ?")->execute([$tokens_json, $page_id]);
    }

    // 7. Handle Photo Uploads & Saving to Disk (Limit initial default pre-fill to exactly 6 items)
    $savedMedia = [];
    $stmtMedia = $db->prepare("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES (?, ?, ?, ?, ?)");

    // If no user photos provided, fetch top 6 sample items from self-hosted assets/default_gallery/
    if (empty($photos) || !is_array($photos)) {
        $assetsDir = __DIR__ . '/../assets/default_gallery';
        $captionsFile = $assetsDir . '/sample_captions.json';
        $captionsMap = file_exists($captionsFile) ? (@json_decode(file_get_contents($captionsFile), true) ?: []) : [];

        $sampleFiles = is_dir($assetsDir) ? array_values(array_diff(scandir($assetsDir), ['.', '..'])) : [];
        $samplePhotos = [];

        $defaultCaptionsPool = [
            'Our First Coffee Date ☕',
            'Sunset Memories 🌅',
            'Together Always 💑',
            'Moments of Pure Joy 😊',
            'Forever & Always 💖',
            'Best Day Ever 🎉'
        ];

        $idx = 0;
        foreach ($sampleFiles as $f) {
            if ($idx >= 6) break; // Smart 6-photo pre-fill limit
            if (strpos($f, 'sample_') === 0 || preg_match('/\.(webp|jpg|png)$/i', $f)) {
                $samplePhotos[] = [
                    'url' => rtrim(APP_URL, '/') . '/assets/default_gallery/' . $f,
                    'caption' => $captionsMap[$f] ?? $defaultCaptionsPool[$idx % count($defaultCaptionsPool)]
                ];
                $idx++;
            }
        }
        $photos = array_slice($samplePhotos, 0, 6);
    } else {
        // If user submitted photos, take up to 25 items
        $photos = array_slice($photos, 0, 25);
    }

    foreach ($photos as $idx => $photoItem) {
        $media_id = 'media_' . $page_id . '_' . ($idx + 1);
        $photoData = is_array($photoItem) ? ($photoItem['url'] ?? '') : $photoItem;
        $photoCaption = is_array($photoItem) ? trim($photoItem['caption'] ?? '') : '';

        if (!empty($photoData)) {
            $filePath = saveUploadedBase64Image($photoData, $page_id, 'photo_' . ($idx + 1));
            $finalCaption = !empty($photoCaption) ? $photoCaption : 'Moments of Joy 💕';
            $stmtMedia->execute([$media_id, $page_id, $filePath, $idx + 1, $finalCaption]);
            $savedMedia[] = [
                'media_id' => $media_id,
                'file_path' => $filePath,
                'caption' => $finalCaption,
                'display_order' => $idx + 1
            ];
        }
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['edit_token'] = $edit_token;

    // Dispatch Automated Payment Receipt & Access Email to Buyer (non-blocking)
    try {
        sendOrderReceiptEmail([
            'buyer_name' => $order['buyer_name'],
            'buyer_email' => $order['buyer_email'],
            'order_id' => $order['order_id'],
            'template_name' => $order['template_id'],
            'amount_paid' => $order['amount_paid'],
            'url_slug' => $slug
        ]);
    } catch (Exception $mailErr) {
        error_log("Receipt email notice: " . $mailErr->getMessage());
    }

    echo json_encode([
        'success' => true,
        'page_id' => $page_id,
        'url_slug' => $slug,
        'share_url' => APP_URL . '/gift/' . $slug,
        'edit_token' => $edit_token,
        'edit_url' => APP_URL . '/edit.php',
        'message' => 'Surprise reveal page created successfully!'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create page: ' . $e->getMessage()]);
}
