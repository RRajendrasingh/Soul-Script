<?php
require_once __DIR__ . '/config.php';

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $cleanHost = preg_replace('#^https?://#i', '', DB_HOST);
            $cleanHost = rtrim($cleanHost, '/');
            if (empty($cleanHost)) {
                $cleanHost = 'localhost';
            }

            $dsn = "mysql:host=" . $cleanHost . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Return JSON error response if accessed via API
            if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failure: ' . $e->getMessage()
                ]);
                exit;
            } else {
                die('Database Connection Error: ' . $e->getMessage());
            }
        }
    }
    return $pdo;
}

/**
 * Hash hint answer consistently (trimmed, lowercase, SHA-256 + salt)
 */
function hashHintAnswer($answer) {
    $clean = strtolower(trim($answer));
    return hash('sha256', $clean . HASH_SALT);
}
