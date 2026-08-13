<?php
/**
 * SoulScript - Admin Template & Gift Card Management API
 * Supports CRUD, Cover Uploads (WebP + Persistent Storage), Sequence Re-ordering, and Active Toggling
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/media_helper.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    $method = $_SERVER['REQUEST_METHOD'];

    // Ensure templates table columns exist
    $tplCols = [
        'button_text'   => "VARCHAR(100) DEFAULT 'Personalize This Gift 🎁'",
        'demo_url'      => "TEXT DEFAULT NULL",
        'demo_password' => "VARCHAR(100) DEFAULT NULL",
        'sort_order'    => "INT DEFAULT 0"
    ];
    foreach ($tplCols as $cName => $cDef) {
        try { $db->exec("ALTER TABLE templates ADD COLUMN {$cName} {$cDef}"); } catch (Exception $ex) {}
    }

    if ($method === 'GET') {
        $stmt = $db->query("SELECT * FROM templates ORDER BY sort_order ASC, template_id ASC");
        $templates = $stmt->fetchAll();

        $baseUrl = rtrim(APP_URL, '/');

        // Populate default demo URLs if empty
        $defaultDemos = [
            'anniversary_reveal'   => ['url' => $baseUrl . '/gift/ananya-rohan', 'pass' => 'butterfly'],
            'birthday_magic'       => ['url' => $baseUrl . '/gift/rohan-birthday', 'pass' => 'magic'],
            'perfect_proposal'    => ['url' => $baseUrl . '/gift/rahul-priya', 'pass' => 'proposal'],
            'long_distance_love'   => ['url' => $baseUrl . '/gift/aarav-meera', 'pass' => 'reunion'],
            'raksha_bandhan_special' => ['url' => $baseUrl . '/gift/manvi-testing', 'pass' => '1234']
        ];

        foreach ($templates as &$t) {
            $tid = $t['template_id'];
            if (empty($t['demo_url']) && isset($defaultDemos[$tid])) {
                $t['demo_url'] = $defaultDemos[$tid]['url'];
                $t['demo_password'] = $defaultDemos[$tid]['pass'];
            }
            $t['create_url'] = $baseUrl . '/create.php?template=' . urlencode($tid);
            $t['preview_image_url'] = resolveMediaUrl($t['preview_image_url']);
            $t['price_inr'] = (float)$t['price_inr'];
            $t['active'] = (int)$t['active'];
            $t['sort_order'] = (int)($t['sort_order'] ?? 0);
            $t['button_text'] = !empty($t['button_text']) ? $t['button_text'] : 'Personalize This Gift 🎁';
        }
        unset($t);

        echo json_encode([
            'status' => 'success',
            'total_templates' => count($templates),
            'templates' => $templates
        ], JSON_PRETTY_PRINT);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $action = trim($input['action'] ?? 'save');

        // --- ACTION 1: REORDER SEQUENCE ---
        if ($action === 'reorder') {
            $sequence = $input['sequence'] ?? [];
            if (!is_array($sequence)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid sequence array']);
                exit;
            }
            $stmtReorder = $db->prepare("UPDATE templates SET sort_order = :sort_order WHERE template_id = :template_id");
            foreach ($sequence as $orderIdx => $tid) {
                $newPos = (int)($orderIdx + 1);
                $stmtReorder->execute([':sort_order' => $newPos, ':template_id' => $tid]);
            }
            echo json_encode(['status' => 'success', 'message' => 'Card sequence order updated successfully!']);
            exit;
        }

        // --- ACTION 2: TOGGLE ACTIVE / INACTIVE ---
        if ($action === 'toggle_status') {
            $template_id = trim($input['template_id'] ?? '');
            if (empty($template_id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Template ID is required']);
                exit;
            }
            $stmtToggle = $db->prepare("UPDATE templates SET active = IF(active = 1, 0, 1) WHERE template_id = ?");
            $stmtToggle->execute([$template_id]);
            echo json_encode(['status' => 'success', 'message' => 'Template status toggled successfully!']);
            exit;
        }

        // --- ACTION 3: DELETE TEMPLATE ---
        if ($action === 'delete') {
            $template_id = trim($input['template_id'] ?? '');
            if (empty($template_id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Template ID is required']);
                exit;
            }
            $stmtDel = $db->prepare("DELETE FROM templates WHERE template_id = ?");
            $stmtDel->execute([$template_id]);
            echo json_encode(['status' => 'success', 'message' => 'Template deleted successfully!']);
            exit;
        }

        // --- ACTION 4: SAVE / EDIT TEMPLATE ---
        if ($action === 'save') {
            $name = trim($input['name'] ?? '');
            if (empty($name)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Template title is required']);
                exit;
            }

            $rawTid = trim($input['template_id'] ?? '');
            if (empty($rawTid)) {
                // Auto-generate slug from title
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $name)));
                $slug = trim($slug, '_');
                $rawTid = !empty($slug) ? $slug : ('template_' . time());
            }

            $tagline       = trim($input['tagline'] ?? '');
            $description   = trim($input['description'] ?? '');
            $price_inr     = (float)($input['price_inr'] ?? 449);
            $badge         = trim($input['badge'] ?? '');
            $button_text   = trim($input['button_text'] ?? 'Personalize This Gift 🎁');
            $demo_url      = trim($input['demo_url'] ?? '');
            $demo_password = trim($input['demo_password'] ?? '');
            $active        = isset($input['active']) ? (int)$input['active'] : 1;
            $cover_photo   = trim($input['cover_photo'] ?? '');

            $preview_image_url = trim($input['existing_image_url'] ?? '');

            // Process Cover Photo Upload if provided as Base64 data
            if (!empty($cover_photo) && strpos($cover_photo, 'data:image') === 0) {
                $assetsDir = __DIR__ . '/../assets/default_gallery';
                if (!is_dir($assetsDir)) @mkdir($assetsDir, 0777, true);

                $persistentDir = getPersistentUploadsDir() . '/default_gallery';
                if (!is_dir($persistentDir)) @mkdir($persistentDir, 0777, true);

                $hash = substr(md5($cover_photo . microtime()), 0, 8);
                $fileName = 'template_' . $hash . '.webp';

                $fullPath = $assetsDir . '/' . $fileName;
                $persistentPath = $persistentDir . '/' . $fileName;

                // Strip Base64 header
                $parts = explode(',', $cover_photo);
                $rawBytes = base64_decode(end($parts));

                $img = @imagecreatefromstring($rawBytes);
                if ($img !== false && function_exists('imagewebp')) {
                    ob_start();
                    imagewebp($img, null, 84);
                    $webpBytes = ob_get_clean();
                    imagedestroy($img);
                    if (!empty($webpBytes)) $rawBytes = $webpBytes;
                }

                @file_put_contents($fullPath, $rawBytes);
                @chmod($fullPath, 0666);

                @file_put_contents($persistentPath, $rawBytes);
                @chmod($persistentPath, 0666);

                $baseUrl = rtrim(APP_URL, '/');
                $preview_image_url = $baseUrl . '/assets/default_gallery/' . $fileName;

                // Register caption in sample_captions.json
                $captionsFile = $assetsDir . '/sample_captions.json';
                $captions = file_exists($captionsFile) ? (@json_decode(file_get_contents($captionsFile), true) ?: []) : [];
                $captions[$fileName] = $name . ' Cover';
                @file_put_contents($captionsFile, json_encode($captions, JSON_PRETTY_PRINT));
                @file_put_contents($persistentDir . '/sample_captions.json', json_encode($captions, JSON_PRETTY_PRINT));
            }

            // Check if template exists
            $stmtChk = $db->prepare("SELECT COUNT(*) FROM templates WHERE template_id = ?");
            $stmtChk->execute([$rawTid]);
            $exists = ($stmtChk->fetchColumn() > 0);

            if ($exists) {
                $sql = "UPDATE templates SET 
                    name = :name, tagline = :tagline, description = :description, price_inr = :price_inr, 
                    badge = :badge, button_text = :button_text, demo_url = :demo_url, demo_password = :demo_password, active = :active";
                $params = [
                    ':name'          => $name,
                    ':tagline'       => $tagline,
                    ':description'   => $description,
                    ':price_inr'     => $price_inr,
                    ':badge'         => $badge,
                    ':button_text'   => $button_text,
                    ':demo_url'      => $demo_url,
                    ':demo_password' => $demo_password,
                    ':active'        => $active,
                    ':template_id'   => $rawTid
                ];
                if (!empty($preview_image_url)) {
                    $sql .= ", preview_image_url = :preview_image_url";
                    $params[':preview_image_url'] = $preview_image_url;
                }
                $sql .= " WHERE template_id = :template_id";
                $stmtSave = $db->prepare($sql);
                $stmtSave->execute($params);
            } else {
                // Insert new template
                $maxSort = (int)$db->query("SELECT MAX(sort_order) FROM templates")->fetchColumn();
                $newSort = $maxSort + 1;

                if (empty($preview_image_url)) {
                    $preview_image_url = rtrim(APP_URL, '/') . '/assets/default_gallery/sample_fa6955df.webp';
                }

                $stmtSave = $db->prepare("INSERT INTO templates 
                    (template_id, name, tagline, description, price_inr, preview_image_url, badge, button_text, demo_url, demo_password, active, sort_order) 
                    VALUES 
                    (:template_id, :name, :tagline, :description, :price_inr, :preview_image_url, :badge, :button_text, :demo_url, :demo_password, :active, :sort_order)");
                $stmtSave->execute([
                    ':template_id'       => $rawTid,
                    ':name'              => $name,
                    ':tagline'           => $tagline,
                    ':description'       => $description,
                    ':price_inr'         => $price_inr,
                    ':preview_image_url' => $preview_image_url,
                    ':badge'             => $badge,
                    ':button_text'       => $button_text,
                    ':demo_url'          => $demo_url,
                    ':demo_password'     => $demo_password,
                    ':active'            => $active,
                    ':sort_order'        => $newSort
                ]);
            }

            $baseUrl = rtrim(APP_URL, '/');
            echo json_encode([
                'status' => 'success',
                'template_id' => $rawTid,
                'create_url' => $baseUrl . '/create.php?template=' . urlencode($rawTid),
                'message' => 'Gift card saved successfully!'
            ], JSON_PRETTY_PRINT);
            exit;
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_PRETTY_PRINT);
}
