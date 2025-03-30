<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['product_id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock_quantity'], $_POST['category_id'])) {
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];
        $category_id = $_POST['category_id'];

        // Prepare and execute update query
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock_quantity=?, category_id=? WHERE product_id=?");
        $stmt->bind_param("ssdiii", $name, $description, $price, $stock_quantity, $category_id, $product_id);

        if ($stmt->execute()) {
            header("Location: view_products.php?message=Product updated successfully");
            exit();
        } else {
            echo "Error updating product: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required!";
    }
} else {
    echo "Invalid request!";
}

$conn->close();
?>
