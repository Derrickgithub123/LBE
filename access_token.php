<?php
function generatePesapalAccessToken() {
    $consumerKey = "j9Bh0k9qIa0ZjXAFXCNm5Bp9h+8LAoX3"; // Replace with your Consumer Key
    $consumerSecret = "UnuB84siTNMhPZjcd160ODCA+YI="; // Replace with your Consumer Secret

    $url = "https://pay.pesapal.com/v3/api/Auth/RequestToken";

    // Correct Authorization header format
    $headers = [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($consumerKey . ":" . $consumerSecret)
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Use true in production
    curl_setopt($ch, CURLOPT_POST, true);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['token'])) {
        return $result['token'];
    } else {
        return "Error generating token: " . json_encode($result);
    }
}

$accessToken = generatePesapalAccessToken();
echo "Access Token: " . $accessToken;
?>
