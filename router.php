<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false; // serve requested file directly
}

if (preg_match('#^/gift/([^/]+)/?#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/reveal.php';
    exit;
}

if (preg_match('#^/edit/([^/]+)/?#', $uri, $m)) {
    $_GET['token'] = $m[1];
    require __DIR__ . '/edit.php';
    exit;
}

if ($uri === '/edit' || $uri === '/edit/') {
    require __DIR__ . '/edit.php';
    exit;
}

require __DIR__ . '/index.php';
