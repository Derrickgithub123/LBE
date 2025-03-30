<?php
function registerPesapalOrder($accessToken) {
    $url = "https://pay.pesapal.com/v3/api/Transactions/SubmitOrderRequest";

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken
    ];

    $orderData = [
        "id" => uniqid("Order_"), // Unique Order ID
        "currency" => "KES",
        "amount" => 100, // Payment amount
        "description" => "Payment for order",
        "callback_url" => "https://portifolio.com/payment_callback.php", // Change this
        "notification_id" => "YOUR_NOTIFICATION_ID", // Get from PesaPal dashboard
        "billing_address" => [
            "email_address" => "kanyokoderrick15@gmail.com",
            "phone_number" => "0711845370",
            "first_name" => "stanely",
            "last_name" => "wanyoike"
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    
    if (isset($result['redirect_url'])) {
        header("Location: " . $result['redirect_url']); // Redirect user to PesaPal
        exit;
    } else {
        echo "Error processing payment: " . json_encode($result);
    }
}

// Get access token
require_once "access_token.php"; // Ensure this file generates and returns $accessToken
$accessToken = generatePesapalAccessToken();

// Register order
registerPesapalOrder($accessToken);
?>
