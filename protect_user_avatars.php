<?php
/**
 * SoulScript - User Avatar Protection & Auto-Healer Script
 * Audits all partner profile avatar photos (receiver_photo) across page_content table,
 * backs up Base64/disk files into persistent storage (/uploads_persistent/{page_id}/avatar_{hash}.webp),
 * and updates DB rows to clean self-hosted WebP URLs.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/media_helper.php';

header('Content-Type: application/json');

try {
    $db = getDB();

    $stmt = $db->query("SELECT page_id, partner_name, receiver_photo FROM page_content WHERE receiver_photo IS NOT NULL AND TRIM(receiver_photo) != ''");
    $rows = $stmt->fetchAll();

    $updatedAvatars = 0;
    $backedUpAvatars = 0;

    $updateStmt = $db->prepare("UPDATE page_content SET receiver_photo = :receiver_photo WHERE page_id = :page_id");

    foreach ($rows as $r) {
        $pageId = $r['page_id'];
        $rawPhoto = trim($r['receiver_photo']);

        if (empty($rawPhoto)) continue;

        // 1. If Base64 string -> Convert to WebP and save to persistent storage
        if (strpos($rawPhoto, 'data:image') === 0) {
            $newUrl = saveUploadedBase64Image($rawPhoto, $pageId, 'partner_avatar');
            if ($newUrl !== $rawPhoto) {
                $updateStmt->execute([':receiver_photo' => $newUrl, ':page_id' => $pageId]);
                $updatedAvatars++;
            }
            continue;
        }

        // 2. If URL or Path -> Ensure file exists in persistent storage
        $parsed = parse_url($rawPhoto);
        $path = $parsed['path'] ?? $rawPhoto;

        if (preg_match('/uploads\/([^\/]+)\/(.+)$/', $path, $m)) {
            $folder = $m[1];
            $fileName = $m[2];

            $publicFile = __DIR__ . '/uploads/' . $folder . '/' . $fileName;
            $persistentDir = getPersistentUploadsDir() . '/' . $folder;
            $persistentFile = $persistentDir . '/' . $fileName;

            if (!is_dir($persistentDir)) {
                @mkdir($persistentDir, 0777, true);
                @chmod($persistentDir, 0777);
            }

            // Copy public file to persistent backup if missing
            if (file_exists($publicFile) && !file_exists($persistentFile)) {
                @copy($publicFile, $persistentFile);
                @chmod($persistentFile, 0666);
                $backedUpAvatars++;
            }

            // Restore from persistent backup if public file is missing
            if (!file_exists($publicFile) && file_exists($persistentFile)) {
                $publicFolder = dirname($publicFile);
                if (!is_dir($publicFolder)) {
                    @mkdir($publicFolder, 0777, true);
                    @chmod($publicFolder, 0777);
                }
                @copy($persistentFile, $publicFile);
                @chmod($publicFile, 0666);
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'total_audited' => count($rows),
        'base64_converted_to_webp' => $updatedAvatars,
        'backed_up_to_persistent' => $backedUpAvatars,
        'message' => 'All user partner profile avatar photos audited, converted to WebP, and backed up to persistent storage!'
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
