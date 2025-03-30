<?php
include 'config.php';

if (isset($_POST['toggle_status'])) {
    $offer_id = $_POST['offer_id'];

    // Toggle the current status
    $query = "UPDATE offers SET is_active = NOT is_active WHERE id = $offer_id";
    mysqli_query($conn, $query);

    header("Location: admin .php"); // Redirect back to admin panel
    exit();
}
?>
