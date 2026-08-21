<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

[$rzpKeyId, $rzpKeySecret] = function_exists('getEffectiveRazorpayCredentials') 
    ? getEffectiveRazorpayCredentials() 
    : ['rzp_test_TSO6FpIhNqiwSy', 'E8uEAHS3yi7gi1Zlviiw0qMp'];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_USERPWD, trim($rzpKeyId) . ':' . trim($rzpKeySecret));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'amount' => 44900,
    'currency' => 'INR',
    'receipt' => 'diag_' . time()
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: GiftReveal/1.0'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo json_encode([
    'key_id' => $rzpKeyId,
    'http_code' => $httpCode,
    'curl_error' => $err,
    'response' => json_decode($resp, true) ?: $resp
]);
