<?php
include 'config.php';

// Fetch only active offers
$query = "SELECT p.id, p.name, p.image, p.original_price, o.discount_price 
          FROM offers o 
          JOIN products p ON o.product_id = p.id 
          WHERE o.is_active = 1
          ORDER BY o.discount_price ASC";

$result = mysqli_query($conn, $query);
?>
