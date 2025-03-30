<?php
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["OrderTrackingId"])) {
    $orderTrackingId = $_GET["OrderTrackingId"];
    
    // Log the response for debugging
    file_put_contents("pesapal_log.txt", json_encode($_GET) . "\n", FILE_APPEND);

    echo "Payment successful! Order ID: " . $orderTrackingId;
} else {
    echo "Invalid request";
}
?>
