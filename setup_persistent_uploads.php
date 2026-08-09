<?php
/**
 * SoulScript - Hostinger Persistent Uploads Directory Setup
 * Protects user uploads from being wiped during Hostinger Git Auto-Deployments
 */

header('Content-Type: application/json');

$domainRoot = dirname(__DIR__); // /home/u810420317/domains/digitalyogi24.com
$publicHtml = __DIR__;          // /home/u810420317/domains/digitalyogi24.com/public_html
$persistentDir = $domainRoot . '/uploads_persistent';
$publicUploads = $publicHtml . '/uploads';

$log = [];

try {
    // 1. Create persistent folder outside public_html if missing
    if (!is_dir($persistentDir)) {
        if (@mkdir($persistentDir, 0777, true)) {
            @chmod($persistentDir, 0777);
            $log[] = "Created persistent uploads directory: $persistentDir";
        } else {
            $log[] = "Failed creating $persistentDir";
        }
    } else {
        $log[] = "Persistent directory already exists: $persistentDir";
    }

    // 2. Check if symlink can be created or if uploads folder can be linked
    $isSymlink = is_link($publicUploads);
    $log[] = "Is public_html/uploads a symlink? " . ($isSymlink ? 'YES' : 'NO');

    // Return detailed environment info
    echo json_encode([
        'status' => 'success',
        'domain_root' => $domainRoot,
        'public_html' => $publicHtml,
        'persistent_dir' => $persistentDir,
        'public_uploads' => $publicUploads,
        'logs' => $log
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
