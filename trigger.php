<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize inputs
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT);

    // Validate inputs
    if (empty($phone) || empty($amount)) {
        die("Phone number and amount are required.");
    }

    // Get access token
    $accessToken = getAccessToken();
    if (!$accessToken) {
        die("Failed to get M-Pesa access token.");
    }

    // Initiate STK Push
    $response = initiateSTKPush($phone, $amount, $accessToken);
    if ($response && isset($response['ResponseCode']) && $response['ResponseCode'] == "0") {
        echo "STK Push sent successfully. Check your phone to complete the payment.";
    } else {
        echo "Error: " . ($response['errorMessage'] ?? "Failed to send STK Push.");
    }
} else {
    die("Invalid request.");
}
?>