<?php
header('Content-Type: application/json');

$scriptDir = __DIR__;
$uploadsDir = __DIR__ . '/uploads';

$foundUploadDirs = [];

// Glob search for uploads directory
$possiblePaths = [
    __DIR__ . '/uploads',
    dirname(__DIR__) . '/uploads',
    $_SERVER['DOCUMENT_ROOT'] . '/uploads',
    $_SERVER['DOCUMENT_ROOT'] . '/public_html/uploads',
    '/home/' . get_current_user() . '/public_html/uploads',
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $files = is_dir($path) ? scandir($path) : [];
        $foundUploadDirs[$path] = array_values(array_diff($files, ['.', '..']));
    }
}

// Check database entries in page_media
require_once __DIR__ . '/config/db.php';
$db = getDB();
$stmt = $db->query("SELECT page_id, file_path FROM page_media LIMIT 10");
$sampleRows = $stmt->fetchAll();

echo json_encode([
    'script_dir' => $scriptDir,
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? '',
    'upload_directories_found' => $foundUploadDirs,
    'sample_db_rows' => $sampleRows
], JSON_PRETTY_PRINT);
