<?php
session_start();
require_once __DIR__ . '/../config/config.php';

$type = strtolower(trim($_GET['type'] ?? 'csv'));

$sampleData = [
    ['voucher_code' => 'AMZ-TEST-100-8912', 'amount' => 100],
    ['voucher_code' => 'AMZ-TEST-100-8913', 'amount' => 100],
    ['voucher_code' => 'AMZ-TEST-100-8914', 'amount' => 100],
    ['voucher_code' => 'AMZ-TEST-150-7711', 'amount' => 150],
    ['voucher_code' => 'AMZ-TEST-150-7712', 'amount' => 150],
    ['voucher_code' => 'AMZ-TEST-250-5501', 'amount' => 250],
    ['voucher_code' => 'AMZ-TEST-500-1101', 'amount' => 500],
    ['voucher_code' => 'AMZ-TEST-2000-0001', 'amount' => 2000],
];

if ($type === 'txt') {
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="sample_amazon_vouchers.txt"');
    foreach ($sampleData as $row) {
        echo $row['voucher_code'] . "\n";
    }
    exit;
}

// Default CSV Download (Forces Excel/CSV application)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="sample_amazon_vouchers.csv"');
$output = fopen('php://output', 'w');
fputcsv($output, ['voucher_code', 'amount']);
foreach ($sampleData as $row) {
    fputcsv($output, [$row['voucher_code'], $row['amount']]);
}
fclose($output);
exit;
