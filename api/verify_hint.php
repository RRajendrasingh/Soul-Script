<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/media_helper.php';

$input = json_decode(file_get_contents('php://input'), true);

$slug              = trim($input['slug'] ?? '');
$answer            = trim($input['answer'] ?? '');
$bypass_edit_token = trim($input['bypass_edit_token'] ?? '');
$preview_mode      = trim($input['preview_mode'] ?? '');
$userIp            = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// BUYER EDIT / PREVIEW MODE: bypass hint check
if ($slug && ($bypass_edit_token || $preview_mode === '1')) {
    try {
        $db = getDB();
        if ($bypass_edit_token) {
            $stmtBp = $db->prepare("
                SELECT p.page_id, p.template_id, p.url_slug, p.status, p.expires_at, c.*
                FROM pages p
                JOIN page_content c ON p.page_id = c.page_id
                WHERE LOWER(p.url_slug) = LOWER(?) AND p.edit_token = ?
            ");
            $stmtBp->execute([$slug, $bypass_edit_token]);
        } else {
            $stmtBp = $db->prepare("
                SELECT p.page_id, p.template_id, p.url_slug, p.status, p.expires_at, c.*
                FROM pages p
                JOIN page_content c ON p.page_id = c.page_id
                WHERE LOWER(p.url_slug) = LOWER(?)
            ");
            $stmtBp->execute([$slug]);
        }
        $page = $stmtBp->fetch();

        if ($page) {
            // Valid buyer token — return full page data without hint check
            $stmtMedia = $db->prepare("SELECT * FROM page_media WHERE page_id = ? ORDER BY display_order ASC");
            $stmtMedia->execute([$page['page_id']]);
            $media = $stmtMedia->fetchAll();

            // Fetch Milestones dynamically if present for page_id
            $stmtM = $db->prepare("SELECT * FROM story_milestones WHERE page_id = ? ORDER BY entry_order ASC");
            $stmtM->execute([$page['page_id']]);
            $milestones = $stmtM->fetchAll();

            // Fetch Reasons / Promises list dynamically if present for page_id
            $stmtR = $db->prepare("SELECT reason_text FROM reasons_list WHERE page_id = ? ORDER BY entry_order ASC");
            $stmtR->execute([$page['page_id']]);
            $reasons = $stmtR->fetchAll(PDO::FETCH_COLUMN);

            // Fetch Proposal Response dynamically if present for page_id
            $stmtP = $db->prepare("SELECT * FROM proposal_responses WHERE page_id = ?");
            $stmtP->execute([$page['page_id']]);
            $proposalResponse = $stmtP->fetch() ?: null;
            if ($proposalResponse) {
                $proposalResponse['responded_at_formatted'] = date('j M, h:i a', strtotime($proposalResponse['responded_at']));
            }

            $lettersData = !empty($page['letters_json']) ? json_decode($page['letters_json'], true) : [];
            $tokensData  = !empty($page['tokens_json'])  ? json_decode($page['tokens_json'], true)  : [];

            // Normalize receiver photo & media file paths
            if (!empty($page['receiver_photo'])) {
                $page['receiver_photo'] = resolveMediaUrl($page['receiver_photo']);
            }
            foreach ($media as &$m) {
                if (!empty($m['file_path'])) {
                    $m['file_path'] = resolveMediaUrl($m['file_path']);
                }
            }
            unset($m);

            $data = [
                'page_id' => $page['page_id'],
                'proposal_response' => $proposalResponse,
                'tokens' => $tokensData
            ];
            $contentData = [
                'partner_name'     => $page['partner_name'],
                'buyer_name'       => $page['buyer_name'],
                'hint_question'    => $page['hint_question'],
                'love_note_text'   => $page['love_note_text'],
                'tagline_quote'    => $page['tagline_quote'] ?? 'Safar Khubsurat h manjil se bhi 🌹',
                'favorite_singers' => $page['favorite_singers'] ?? 'Arijit Singh & KK',
                'bg_music_url'     => $page['bg_music_url'] ?? '',
                'receiver_photo'   => $page['receiver_photo'] ?? '',
                'letters' => $lettersData,
                'tokens'  => $tokensData,
                'template_fields' => [
                    'relationship_start_date' => $page['relationship_start_date'],
                    'partner_dob'             => $page['partner_dob'],
                    'love_letter_text'        => $page['love_letter_text'],
                    'buyer_city'              => $page['buyer_city'],
                    'buyer_timezone'          => $page['buyer_timezone'],
                    'partner_city'            => $page['partner_city'],
                    'partner_timezone'        => $page['partner_timezone'],
                    'reunion_date'            => $page['reunion_date'],
                    'playlist_url'            => $page['playlist_url'],
                    'song_title'              => $page['song_title'],
                    'song_artist'             => $page['song_artist'],
                    'milestones'              => $milestones,
                    'reasons'                 => $reasons
                ],
                'media' => $media
            ];

            $html_content = '';
            $requestedTheme = trim($input['override_theme'] ?? $_GET['theme'] ?? '');
            $selectedTemplateId = (!empty($requestedTheme) && file_exists(__DIR__ . '/../templates/themes/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $requestedTheme) . '.php')) 
                ? preg_replace('/[^a-zA-Z0-9_-]/', '', $requestedTheme) 
                : $page['template_id'];
            $theme_file = __DIR__ . '/../templates/themes/' . $selectedTemplateId . '.php';
            if (file_exists($theme_file)) {
                $content = $contentData;
                $isEditMode = true;
                ob_start();
                require $theme_file;
                $html_content = ob_get_clean();
            }

            echo json_encode([
                'success' => true,
                'message' => 'Buyer edit mode — page loaded.',
                'page_id' => $page['page_id'],
                'template_id' => $page['template_id'],
                'url_slug' => $page['url_slug'],
                'expires_at' => $page['expires_at'],
                'html_content' => $html_content,
                'content' => $contentData,
                'proposal_response' => $proposalResponse
            ]);
            exit;
        }
    } catch (Exception $e) {
        // Fall through to normal answer flow
    }
}

if (!$slug || !$answer) {

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Slug and answer are required']);
    exit;
}

try {
    $db = getDB();

    // 1. Fetch page and content hash
    $stmt = $db->prepare("
        SELECT p.page_id, p.template_id, p.url_slug, p.status, p.expires_at,
               c.*
        FROM pages p
        JOIN page_content c ON p.page_id = c.page_id
        WHERE LOWER(p.url_slug) = LOWER(?)
    ");
    $stmt->execute([$slug]);
    $page = $stmt->fetch();

    if (!$page) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Page not found']);
        exit;
    }

    // 2. Check Rate Limiter
    $stmtLock = $db->prepare("SELECT attempts_count, locked_until FROM failed_attempts WHERE slug = ? AND ip_address = ?");
    $stmtLock->execute([$slug, $userIp]);
    $lockInfo = $stmtLock->fetch();

    $now = time();
    if ($lockInfo && !empty($lockInfo['locked_until'])) {
        $lockedUntilTime = strtotime($lockInfo['locked_until']);
        if ($lockedUntilTime > $now) {
            $secs = $lockedUntilTime - $now;
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'message' => "Too many wrong attempts. Locked for {$secs} more seconds.",
                'locked_until' => $lockedUntilTime
            ]);
            exit;
        }
    }

    // 3. Verify Hash (Case-insensitive, trimmed)
    $incomingHash = hashHintAnswer($answer);
    $isMatch = ($incomingHash === $page['hint_answer_hash']);

    if (!$isMatch) {
        $currentCount = $lockInfo ? (int)$lockInfo['attempts_count'] : 0;
        $newCount = $currentCount + 1;
        $newLockedUntil = null;

        if ($newCount >= 5) {
            $newLockedUntil = date('Y-m-d H:i:s', $now + 60); // 60s cooldown
            $newCount = 0; // Reset counter for next cycle
        }

        $stmtUpsert = $db->prepare("
            INSERT INTO failed_attempts (slug, ip_address, attempts_count, locked_until, updated_at)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE attempts_count = ?, locked_until = ?, updated_at = NOW()
        ");
        $stmtUpsert->execute([$slug, $userIp, $newCount, $newLockedUntil, $newCount, $newLockedUntil]);

        if ($newLockedUntil !== null) {
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'message' => '5 incorrect guesses. Lockout engaged for 60 seconds.',
                'locked_until' => $now + 60
            ]);
            exit;
        }

        $attemptsLeft = 5 - $newCount;
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => "Incorrect answer. {$attemptsLeft} attempt(s) remaining before a 60s cooldown.",
            'attempts_remaining' => $attemptsLeft
        ]);
        exit;
    }

    // 4. Verification Successful! Clear Lockout Record
    $stmtClear = $db->prepare("DELETE FROM failed_attempts WHERE slug = ? AND ip_address = ?");
    $stmtClear->execute([$slug, $userIp]);

    // 5. Fetch Full Media
    $stmtMedia = $db->prepare("SELECT * FROM page_media WHERE page_id = ? ORDER BY display_order ASC");
    $stmtMedia->execute([$page['page_id']]);
    $media = $stmtMedia->fetchAll();

    // 6. Fetch Milestones dynamically if present for page_id
    $stmtM = $db->prepare("SELECT * FROM story_milestones WHERE page_id = ? ORDER BY entry_order ASC");
    $stmtM->execute([$page['page_id']]);
    $milestones = $stmtM->fetchAll();

    // 7. Fetch Reasons / Sibling Promises List dynamically if present for page_id
    $stmtR = $db->prepare("SELECT reason_text FROM reasons_list WHERE page_id = ? ORDER BY entry_order ASC");
    $stmtR->execute([$page['page_id']]);
    $reasons = $stmtR->fetchAll(PDO::FETCH_COLUMN);

    // 8. Fetch Proposal Response dynamically if present for page_id
    $stmtP = $db->prepare("SELECT * FROM proposal_responses WHERE page_id = ?");
    $stmtP->execute([$page['page_id']]);
    $proposalResponse = $stmtP->fetch() ?: null;

    // Return Full Result Page Payload
    $lettersData = !empty($page['letters_json']) ? json_decode($page['letters_json'], true) : [];
    $tokensData = !empty($page['tokens_json']) ? json_decode($page['tokens_json'], true) : [];

    // Normalize receiver photo & media file paths
    if (!empty($page['receiver_photo'])) {
        $page['receiver_photo'] = resolveMediaUrl($page['receiver_photo']);
    }
    foreach ($media as &$m) {
        if (!empty($m['file_path'])) {
            $m['file_path'] = resolveMediaUrl($m['file_path']);
        }
    }
    unset($m);

    require_once __DIR__ . '/../includes/voucher_helper.php';
    $voucherStatus = getRakhiVoucherUnlockStatus($page['order_id'] ?? null, $page['page_id']);
    $affiliateProducts = getAffiliateProducts();

    $data = [
        'page_id' => $page['page_id'],
        'proposal_response' => $proposalResponse,
        'tokens' => $tokensData,
        'rakhi_voucher_status' => $voucherStatus,
        'rakhi_affiliate_products' => $affiliateProducts
    ];
    $contentData = [
        'partner_name' => $page['partner_name'],
        'buyer_name' => $page['buyer_name'],
        'hint_question' => $page['hint_question'],
        'love_note_text' => $page['love_note_text'],
        'tagline_quote' => $page['tagline_quote'] ?? 'Safar Khubsurat h manjil se bhi 🌹',
        'favorite_singers' => $page['favorite_singers'] ?? 'Arijit Singh & KK',
        'bg_music_url' => (!empty($page['bg_music_url']) && !str_contains($page['bg_music_url'], 'pixabay')) ? $page['bg_music_url'] : (APP_URL . '/assets/audio/rakhi_theme.mp3'),
        'receiver_photo' => $page['receiver_photo'] ?? '',
        'letters' => $lettersData,
        'tokens' => $tokensData,
        'template_fields' => [
            'relationship_start_date' => $page['relationship_start_date'],
            'partner_dob'             => $page['partner_dob'],
            'love_letter_text'        => $page['love_letter_text'],
            'buyer_city'              => $page['buyer_city'],
            'buyer_timezone'          => $page['buyer_timezone'],
            'partner_city'            => $page['partner_city'],
            'partner_timezone'        => $page['partner_timezone'],
            'reunion_date'            => $page['reunion_date'],
            'playlist_url'            => $page['playlist_url'],
            'song_title'              => $page['song_title'],
            'song_artist'             => $page['song_artist'],
            'milestones'              => $milestones,
            'reasons'                 => $reasons
        ],
        'media' => $media
    ];

    $html_content = '';
    $requestedTheme = trim($input['override_theme'] ?? $_GET['theme'] ?? '');
    $selectedTemplateId = (!empty($requestedTheme) && file_exists(__DIR__ . '/../templates/themes/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $requestedTheme) . '.php')) 
        ? preg_replace('/[^a-zA-Z0-9_-]/', '', $requestedTheme) 
        : $page['template_id'];
    $theme_file = __DIR__ . '/../templates/themes/' . $selectedTemplateId . '.php';
    if (file_exists($theme_file)) {
        $content = $contentData;
        $isEditMode = false;
        ob_start();
        require $theme_file;
        $html_content = ob_get_clean();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Hint verified successfully!',
        'page_id' => $page['page_id'],
        'template_id' => $page['template_id'],
        'url_slug' => $page['url_slug'],
        'expires_at' => $page['expires_at'],
        'voucher_status' => $voucherStatus,
        'affiliate_products' => $affiliateProducts,
        'html_content' => $html_content,
        'content' => $contentData,
        'proposal_response' => $proposalResponse
    ]);

} catch (Exception $e) {
    sendJsonError($e->getMessage(), 500);
}
