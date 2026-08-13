<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$db = getDB();

// Test updating sort_order to 1 for raksha_bandhan_royal
$stmt1 = $db->prepare("UPDATE templates SET sort_order = 1 WHERE template_id = 'raksha_bandhan_royal'");
$stmt1->execute();
$count1 = $stmt1->rowCount();

// Test updating sort_order to 2 for birthday_magic
$stmt2 = $db->prepare("UPDATE templates SET sort_order = 2 WHERE template_id = 'birthday_magic'");
$stmt2->execute();
$count2 = $stmt2->rowCount();

$stmtCheck = $db->query("SELECT template_id, name, sort_order FROM templates ORDER BY sort_order ASC, template_id ASC");
$rows = $stmtCheck->fetchAll();

echo json_encode([
    'count_royal' => $count1,
    'count_birthday' => $count2,
    'rows' => $rows
], JSON_PRETTY_PRINT);
