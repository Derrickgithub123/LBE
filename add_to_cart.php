<?php
session_start();
include 'db_connect.php'; // Database connection

$user_id = $_SESSION['user_id']; // Get user session ID
$product_id = $_POST['product_id'];

// Check if product is already in cart
$check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
if (mysqli_num_rows($check) > 0) {
    // Update quantity
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
} else {
    // Insert new item
    mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES ('$user_id', '$product_id', 1, NOW())");
}

echo "Added to cart!";
?>
