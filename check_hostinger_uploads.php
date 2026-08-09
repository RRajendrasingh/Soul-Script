<?php
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json');

$uploadDir = __DIR__ . '/uploads';
$dirs = [];

if (is_dir($uploadDir)) {
    $items = scandir($uploadDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $uploadDir . '/' . $item;
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            $dirs[$item] = array_values($files);
        } else {
            $dirs['root_files'][] = $item;
        }
    }
}

echo json_encode([
    'upload_dir_exists' => is_dir($uploadDir),
    'is_writable' => is_writable($uploadDir),
    'contents' => $dirs
], JSON_PRETTY_PRINT);
