<?php
session_start();
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit();
}

// Validate input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST["product_id"]);
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);

    if (empty($name) || $price <= 0 || $stock < 0) {
        die("Error: Invalid product data.");
    }

    // Update product
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE product_id = ?");
    $stmt->bind_param("ssdii", $name, $description, $price, $stock, $product_id);

    if ($stmt->execute()) {
        header("Location: product_list.php?success=Product updated!");
        exit();
    } else {
        die("Error updating product.");
    }
}
?>
