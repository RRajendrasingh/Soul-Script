<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$order_id       = trim($input['order_id'] ?? '');
$partner_name   = trim($input['partner_name'] ?? '');
$hint_question  = trim($input['hint_question'] ?? '');
$hint_answer    = trim($input['hint_answer'] ?? '');
$love_note_text = trim($input['love_note_text'] ?? '');
$custom_slug    = trim($input['custom_slug'] ?? '');
$photos         = $input['photos'] ?? []; // Array of base64 data URLs or uploaded URLs

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
    function sanitizeSlug($str) {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9\-]+/', '-', $str);
        return trim(preg_replace('/-+/', '-', $str), '-');
    }

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
    $song_title              = $template_fields['song_title'] ?? null;
    $song_artist             = $template_fields['song_artist'] ?? null;
    $receiver_photo          = $input['receiver_photo'] ?? null;

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

    if ($template_id === 'birthday_magic' && !empty($template_fields['reasons'])) {
        $stmtR = $db->prepare("INSERT INTO reasons_list (page_id, entry_order, reason_text) VALUES (?, ?, ?)");
        foreach ($template_fields['reasons'] as $idx => $reason) {
            if (!empty($reason)) {
                $stmtR->execute([$page_id, $idx + 1, $reason]);
            }
        }
    }

    // 7. Handle Photo Uploads & Saving to Disk
    $targetDir = UPLOAD_DIR . '/' . $page_id;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $savedMedia = [];
    $stmtMedia = $db->prepare("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES (?, ?, ?, ?, ?)");

    foreach ($photos as $idx => $photoData) {
        $media_id = 'media_' . $page_id . '_' . ($idx + 1);
        $filePath = '';

        if (strpos($photoData, 'data:image') === 0) {
            // Base64 image payload from client compressor
            preg_match('/data:image\/(.*?);base64,(.*)/', $photoData, $matches);
            $rawExt = strtolower($matches[1] ?? 'jpg');
            if ($rawExt === 'jpeg') $rawExt = 'jpg';
            
            // Security: Strict whitelist of allowed image extensions to prevent RCE
            $allowedExts = ['jpg', 'png', 'webp', 'gif'];
            $ext = in_array($rawExt, $allowedExts) ? $rawExt : 'jpg';

            $imageData = base64_decode($matches[2] ?? '');
            
            $fileName = ($idx + 1) . '.' . $ext;
            $fullDiskPath = $targetDir . '/' . $fileName;
            file_put_contents($fullDiskPath, $imageData);
            
            $filePath = APP_URL . '/uploads/' . $page_id . '/' . $fileName;
        } else {
            // Standard URL fallback (e.g. Unsplash sample photos)
            $filePath = $photoData;
        }

        $stmtMedia->execute([$media_id, $page_id, $filePath, $idx + 1, 'Moments of Joy']);
        $savedMedia[] = [
            'media_id' => $media_id,
            'file_path' => $filePath,
            'display_order' => $idx + 1
        ];
    }

    echo json_encode([
        'success' => true,
        'page_id' => $page_id,
        'url_slug' => $slug,
        'share_url' => APP_URL . '/gift/' . $slug,
        'edit_token' => $edit_token,
        'edit_url' => APP_URL . '/edit/' . $edit_token,
        'message' => 'Surprise reveal page created successfully!'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create page: ' . $e->getMessage()]);
}
