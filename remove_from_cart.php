<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$cart_id = $_GET['cart_id'];

mysqli_query($conn, "DELETE FROM cart WHERE cart_id='$cart_id' AND user_id='$user_id'");
header("Location: view_cart.php"); // Refresh cart
?>
