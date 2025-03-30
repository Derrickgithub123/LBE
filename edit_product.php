<?php
include('db_connect.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Product ID.");
}

$product_id = $_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No product found with that ID.");
}

$product = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input, textarea {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            border: none;
            background: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-edit"></i> Edit Product</h2>

    <form method="POST" action="update_product.php">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id'] ?? '') ?>">

        <label>Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>

        <label>Price (Ksh):</label>
        <input type="text" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>" required>

        <label>Stock Quantity:</label>
        <input type="number" name="stock_quantity" value="<?= htmlspecialchars($product['stock_quantity'] ?? '') ?>" required>

        <label>Category:</label>
        <input type="text" name="category_id" value="<?= htmlspecialchars($product['category_id'] ?? '') ?>" required>

        <button type="submit"><i class="fa-solid fa-save"></i> Update Product</button>
    </form>
</div>

</body>
</html>
