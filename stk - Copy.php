<?php
include 'access_token.php';

$phone = "2547XXXXXXXX"; // Customer's phone number in international format
$amount = "100"; // Amount to charge
$businessShortCode = "174379"; // Use 174379 for sandbox
$passkey = "YOUR_PASSKEY"; 
$timestamp = date("YmdHis");
$password = base64_encode($businessShortCode . $passkey . $timestamp);
$callbackUrl = "https://yourdomain.com/callback.php"; 

$url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

$data = [
    "BusinessShortCode" => $businessShortCode,
    "Password" => $password,
    "Timestamp" => $timestamp,
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => $amount,
    "PartyA" => $phone,
    "PartyB" => $businessShortCode,
    "PhoneNumber" => $phone,
    "CallBackURL" => $callbackUrl,
    "AccountReference" => "Lightning Bolt Electronics",
    "TransactionDesc" => "Payment for Order #12345"
];

$accessToken = getAccessToken();
$headers = [
    "Authorization: Bearer " . $accessToken,
    "Content-Type: application/json"
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
?>
