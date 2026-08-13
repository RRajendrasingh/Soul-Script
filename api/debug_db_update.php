<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$db = getDB();

$stmt = $db->prepare("UPDATE templates SET sort_order = ? WHERE template_id = ?");
$stmt->execute([1, 'raksha_bandhan_royal']);
$affectedRoyal = $stmt->rowCount();

$stmt->execute([2, 'birthday_magic']);
$affectedBirthday = $stmt->rowCount();

$stmtCheck = $db->query("SELECT template_id, name, sort_order FROM templates ORDER BY sort_order ASC");
$templates = $stmtCheck->fetchAll();

echo json_encode([
    'affected_royal' => $affectedRoyal,
    'affected_birthday' => $affectedBirthday,
    'templates' => $templates
], JSON_PRETTY_PRINT);
