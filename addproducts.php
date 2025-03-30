<?php
include('db_connect.php'); // Ensure connection to DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $product_name, $description, $price, $category);

    if ($stmt->execute()) {
        echo "Product added successfully!";
    } else {
        echo "Error adding product.";
    }
}
?>
<form action="addproducts.php" method="POST">
    <input type="text" name="product_name" placeholder="Product Name" required>
    <textarea name="description" placeholder="Product Description" required></textarea>
    <input type="number" name="price" placeholder="Price" required>
    <input type="text" name="category" placeholder="Category" required>
    <button type="submit">Add Product</button>
</form>
