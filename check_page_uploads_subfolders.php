<?php
header('Content-Type: application/json');

$uploadsDir = __DIR__ . '/uploads';
$allSubdirs = [];

if (is_dir($uploadsDir)) {
    $items = scandir($uploadsDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = $uploadsDir . '/' . $item;
        if (is_dir($full)) {
            $allSubdirs[$item] = array_values(array_diff(scandir($full), ['.', '..']));
        } else {
            $allSubdirs['root_files'][] = $item;
        }
    }
}

echo json_encode([
    'uploads_subfolders' => $allSubdirs
], JSON_PRETTY_PRINT);
