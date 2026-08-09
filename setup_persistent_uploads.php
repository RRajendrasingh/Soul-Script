<?php
/**
 * SoulScript - Hostinger Persistent Uploads Directory Setup & Restorer
 * Protects user uploads from being wiped during Hostinger Git Auto-Deployments
 */

header('Content-Type: application/json');

$domainRoot = dirname(__DIR__); // /home/u810420317/domains/digitalyogi24.com
$publicHtml = __DIR__;          // /home/u810420317/domains/digitalyogi24.com/public_html
$persistentDir = $domainRoot . '/uploads_persistent';
$publicUploads = $publicHtml . '/uploads';

$log = [];

function recursiveCopy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    @chmod($dst, 0777);
    $copied = 0;
    while (($file = readdir($dir)) !== false) {
        if ($file !== '.' && $file !== '..') {
            if (is_dir($src . '/' . $file)) {
                $copied += recursiveCopy($src . '/' . $file, $dst . '/' . $file);
            } else {
                @copy($src . '/' . $file, $dst . '/' . $file);
                @chmod($dst . '/' . $file, 0666);
                $copied++;
            }
        }
    }
    closedir($dir);
    return $copied;
}

try {
    if (!is_dir($persistentDir)) {
        @mkdir($persistentDir, 0777, true);
        @chmod($persistentDir, 0777);
        $log[] = "Created persistent uploads directory: $persistentDir";
    }

    if (!is_dir($publicUploads)) {
        @mkdir($publicUploads, 0777, true);
        @chmod($publicUploads, 0777);
        $log[] = "Created public_html/uploads directory";
    }

    // Sync all user files from persistent directory to public uploads
    $restoredCount = recursiveCopy($persistentDir, $publicUploads);
    $log[] = "Restored $restoredCount files from persistent backup to public_html/uploads";

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
