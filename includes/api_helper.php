<?php
// SoulScript — Unified API Response & Security Helper
// Provides standard JSON success & error response helpers with proper HTTP headers.

if (!function_exists('sendJsonSuccess')) {
    function sendJsonSuccess($data = [], $statusCode = 200) {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
        }
        $response = array_merge(['success' => true], $data);
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('sendJsonError')) {
    function sendJsonError($message = 'An error occurred', $statusCode = 400, $extraData = []) {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
        }
        $response = array_merge([
            'success' => false,
            'message' => $message
        ], $extraData);
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('sanitizeSlug')) {
    function sanitizeSlug($str) {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9\-]+/', '-', $str);
        return trim(preg_replace('/-+/', '-', $str), '-');
    }
}
