<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/media_helper.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$token = trim($_GET['token'] ?? $input['token'] ?? $_POST['token'] ?? '');

if (!$token) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Edit token is required']);
    exit;
}

try {
    $db = getDB();

    $stmt = $db->prepare("
        SELECT p.*, c.*, o.buyer_name as order_buyer_name, o.buyer_phone, o.buyer_email, o.buyer_password_hash
        FROM pages p
        JOIN page_content c ON p.page_id = c.page_id
        JOIN orders o ON p.order_id = o.order_id
        WHERE p.edit_token = ?
    ");
    $stmt->execute([$token]);
    $page = $stmt->fetch();

    if (!$page) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired edit link token']);
        exit;
    }

    $page_id = $page['page_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Fetch Milestones
        $stmtM = $db->prepare("SELECT * FROM story_milestones WHERE page_id = ? ORDER BY entry_order ASC");
        $stmtM->execute([$page_id]);
        $milestones = $stmtM->fetchAll();

        // Fetch Reasons
        $stmtR = $db->prepare("SELECT reason_text FROM reasons_list WHERE page_id = ? ORDER BY entry_order ASC");
        $stmtR->execute([$page_id]);
        $reasons = $stmtR->fetchAll(PDO::FETCH_COLUMN);

        // Fetch Media
        $stmtMedia = $db->prepare("SELECT * FROM page_media WHERE page_id = ? ORDER BY display_order ASC");
        $stmtMedia->execute([$page_id]);
        $media = $stmtMedia->fetchAll();

        // Normalize receiver photo & media file paths
        if (!empty($page['receiver_photo'])) {
            $page['receiver_photo'] = normalizeMediaUrl($page['receiver_photo']);
        }
        foreach ($media as &$m) {
            if (!empty($m['file_path'])) {
                $m['file_path'] = normalizeMediaUrl($m['file_path']);
            }
        }
        unset($m);

        // Fetch Proposal Response
        $stmtP = $db->prepare("SELECT * FROM proposal_responses WHERE page_id = ?");
        $stmtP->execute([$page_id]);
        $proposal_response = $stmtP->fetch();
        if ($proposal_response) {
            $proposal_response['responded_at_formatted'] = date('j M, h:i a', strtotime($proposal_response['responded_at']));
        }

        // Fetch all buyer gifts for Multi-Gift Switcher
        $stmtBuyerPages = $db->prepare("
            SELECT p.edit_token, p.page_id, p.template_id, p.url_slug, c.partner_name, p.created_at
            FROM pages p
            JOIN orders o ON p.order_id = o.order_id
            LEFT JOIN page_content c ON p.page_id = c.page_id
            WHERE LOWER(o.buyer_email) = LOWER(?)
            ORDER BY p.created_at DESC
        ");
        $stmtBuyerPages->execute([$page['buyer_email']]);
        $allBuyerPages = $stmtBuyerPages->fetchAll();

        $buyerPagesList = array_map(function($bp) {
            return [
                'edit_token' => $bp['edit_token'],
                'page_id' => $bp['page_id'],
                'template_id' => $bp['template_id'],
                'url_slug' => $bp['url_slug'],
                'partner_name' => htmlspecialchars_decode($bp['partner_name'] ?? 'Partner', ENT_QUOTES),
                'created_at' => $bp['created_at']
            ];
        }, $allBuyerPages);

        echo json_encode([
            'success' => true,
            'page' => $page,
            'milestones' => $milestones,
            'reasons' => $reasons,
            'media' => $media,
            'proposal_response' => $proposal_response,
            'share_url' => APP_URL . '/gift/' . $page['url_slug'],
            'buyer_pages' => $buyerPagesList
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $partner_name   = trim($input['partner_name'] ?? $page['partner_name']);
        $hint_question  = trim($input['hint_question'] ?? $page['hint_question']);
        $hint_answer    = trim($input['hint_answer'] ?? '');
        $love_note_text = trim($input['love_note_text'] ?? $page['love_note_text']);

        // Check if hint answer is being updated
        $hint_answer_hash = $page['hint_answer_hash'];
        if (!empty($hint_answer)) {
            $hint_answer_hash = hashHintAnswer($hint_answer);
        }

        // Check if buyer account password is being updated
        $current_buyer_password = trim($input['current_buyer_password'] ?? '');
        $new_buyer_password     = trim($input['new_buyer_password'] ?? '');

        if (!empty($current_buyer_password) || !empty($new_buyer_password)) {
            if (empty($current_buyer_password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Please enter your current account password to change your password.']);
                exit;
            }
            if (empty($new_buyer_password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Please enter your new account password.']);
                exit;
            }
            if (strlen($new_buyer_password) < 4) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'New password must be at least 4 characters long.']);
                exit;
            }

            // Verify current password hash against orders table
            $currentHash = hashHintAnswer($current_buyer_password);
            if ($currentHash !== $page['buyer_password_hash']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Current account password is incorrect. Please try again.']);
                exit;
            }

            // Password verified! Update to new password hash
            $newHash = hashHintAnswer($new_buyer_password);
            $db->prepare("UPDATE orders SET buyer_password_hash = ? WHERE order_id = ?")->execute([$newHash, $page['order_id']]);
        }

        $template_fields = $input['template_fields'] ?? [];

        $tagline_quote    = trim($input['tagline_quote'] ?? ($template_fields['tagline_quote'] ?? $page['tagline_quote']));
        $favorite_singers = trim($input['favorite_singers'] ?? ($template_fields['favorite_singers'] ?? $page['favorite_singers']));
        $bg_music_url     = trim($input['bg_music_url'] ?? ($template_fields['bg_music_url'] ?? $page['bg_music_url']));
        $receiver_photo   = trim($input['receiver_photo'] ?? ($template_fields['receiver_photo'] ?? $page['receiver_photo']));
        if (!empty($receiver_photo)) {
            $receiver_photo = saveUploadedBase64Image($receiver_photo, $page_id, 'partner_avatar');
        }

        $letters_json     = isset($input['letters']) ? json_encode($input['letters']) : (isset($template_fields['letters']) ? json_encode($template_fields['letters']) : $page['letters_json']);
        $tokens_json      = isset($input['tokens']) ? json_encode($input['tokens']) : (isset($template_fields['tokens']) ? json_encode($template_fields['tokens']) : $page['tokens_json']);

        $relationship_start_date = $template_fields['relationship_start_date'] ?? $page['relationship_start_date'];
        $partner_dob             = $template_fields['partner_dob'] ?? $page['partner_dob'];
        $love_letter_text        = $template_fields['love_letter_text'] ?? $page['love_letter_text'];
        $buyer_city              = $template_fields['buyer_city'] ?? $page['buyer_city'];
        $buyer_timezone          = $template_fields['buyer_timezone'] ?? $page['buyer_timezone'];
        $partner_city            = $template_fields['partner_city'] ?? $page['partner_city'];
        $partner_timezone        = $template_fields['partner_timezone'] ?? $page['partner_timezone'];
        $reunion_date            = $template_fields['reunion_date'] ?? $page['reunion_date'];
        $playlist_url            = $template_fields['playlist_url'] ?? $page['playlist_url'];
        $song_title              = trim($input['song_title'] ?? ($template_fields['song_title'] ?? $page['song_title']));
        $song_artist             = trim($input['song_artist'] ?? ($template_fields['song_artist'] ?? $page['song_artist']));

        // Update Page Content
        $stmtUpdate = $db->prepare("
            UPDATE page_content SET
                partner_name = ?, hint_question = ?, hint_answer_hash = ?, love_note_text = ?,
                tagline_quote = ?, favorite_singers = ?, bg_music_url = ?, receiver_photo = ?, letters_json = ?, tokens_json = ?,
                relationship_start_date = ?, partner_dob = ?, love_letter_text = ?, buyer_city = ?,
                buyer_timezone = ?, partner_city = ?, partner_timezone = ?, reunion_date = ?,
                playlist_url = ?, song_title = ?, song_artist = ?
            WHERE page_id = ?
        ");
        $stmtUpdate->execute([
            $partner_name, $hint_question, $hint_answer_hash, $love_note_text,
            $tagline_quote, $favorite_singers, $bg_music_url, $receiver_photo, $letters_json, $tokens_json,
            $relationship_start_date, $partner_dob, $love_letter_text, $buyer_city,
            $buyer_timezone, $partner_city, $partner_timezone, $reunion_date,
            $playlist_url, $song_title, $song_artist,
            $page_id
        ]);

        // Update Milestones if provided
        if (isset($template_fields['milestones'])) {
            $db->prepare("DELETE FROM story_milestones WHERE page_id = ?")->execute([$page_id]);
            $stmtM = $db->prepare("INSERT INTO story_milestones (page_id, entry_order, milestone_date, title, description) VALUES (?, ?, ?, ?, ?)");
            foreach ($template_fields['milestones'] as $idx => $m) {
                if (!empty($m['title'])) {
                    $stmtM->execute([$page_id, $idx + 1, $m['date'] ?? '', $m['title'], $m['description'] ?? '']);
                }
            }
        }

        // Update Reasons if provided
        if (isset($template_fields['reasons'])) {
            $db->prepare("DELETE FROM reasons_list WHERE page_id = ?")->execute([$page_id]);
            $stmtR = $db->prepare("INSERT INTO reasons_list (page_id, entry_order, reason_text) VALUES (?, ?, ?)");
            foreach ($template_fields['reasons'] as $idx => $reason) {
                if (!empty($reason)) {
                    $stmtR->execute([$page_id, $idx + 1, $reason]);
                }
            }
        }

        if (isset($template_fields['shagun_voucher_code'])) {
            $tokens_json = json_encode([['shagun_voucher_code' => trim($template_fields['shagun_voucher_code'])]]);
            $db->prepare("UPDATE page_content SET tokens_json = ? WHERE page_id = ?")->execute([$tokens_json, $page_id]);
        }

        // Update Media / Scrapbook Photos if provided
        if (isset($input['media_photos']) && is_array($input['media_photos'])) {
            if (count($input['media_photos']) > 10) {
                $input['media_photos'] = array_slice($input['media_photos'], 0, 10);
            }
            $db->prepare("DELETE FROM page_media WHERE page_id = ?")->execute([$page_id]);
            $stmtMedia = $db->prepare("INSERT INTO page_media (media_id, page_id, file_path, display_order, caption) VALUES (?, ?, ?, ?, ?)");
            foreach ($input['media_photos'] as $idx => $photoItem) {
                $photoData = is_array($photoItem) ? ($photoItem['url'] ?? '') : $photoItem;
                $photoCaption = is_array($photoItem) ? trim($photoItem['caption'] ?? '') : '';
                if (!empty($photoData)) {
                    $finalUrl = saveUploadedBase64Image($photoData, $page_id, 'scrapbook_' . ($idx + 1));
                    $media_id = 'media_' . $page_id . '_' . time() . '_' . ($idx + 1);
                    $finalCaption = !empty($photoCaption) ? $photoCaption : 'Moments of Joy';
                    $stmtMedia->execute([$media_id, $page_id, $finalUrl, $idx + 1, $finalCaption]);
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Surprise reveal page updated successfully!',
            'share_url' => APP_URL . '/gift/' . $page['url_slug']
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
