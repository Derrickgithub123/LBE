<?php
session_start();
include("config.php"); // Database connection

// Fetch all products where `product_id` is 2 or higher
$query = "SELECT * FROM products WHERE product_id >= 2 ORDER BY product_id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query error: " . mysqli_error($conn));
}

// Check if products exist
if (mysqli_num_rows($result) == 0) {
    die("Error: No products found in the database.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Lightning Bolt Electronics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000; /* Changed to black */
            color: #fff; /* White text for readability */
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            text-align: center;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            background: #111; /* Dark card background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        .product-card img {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
        }
        .product-card h2 {
            font-size: 18px;
            margin: 10px 0;
        }
        .product-card h3 a {
            color: #5E68E6; /* Blue link */
            text-decoration: none;
            font-weight: bold;
        }
        .product-card h3 a:hover {
            text-decoration: underline;
        }
        .price {
            font-size: 20px;
            font-weight: bold;
            color: #ff4500;
        }
        .btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #218838;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #00bfff;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>All Products</h1>
        <div class="products-grid">
            <?php while ($product = mysqli_fetch_assoc($result)) : ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'img/default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">

                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>

                    <!-- Clickable Product Description -->
                    <h3>
                        <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>">
                            <?php echo htmlspecialchars($product['description'] ?? 'No description available'); ?>
                        </a>
                    </h3>

                    <p class="price">Price: Ksh <?php echo number_format($product['price'], 2); ?></p>
                    <p>Stock Available: <?php echo $product['stock_quantity'] ?? 'Out of Stock'; ?></p>

                    <!-- Add to Cart Form -->
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                        <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <br>
        <a href="index.php" class="back-link">← Back to Home</a>
    </div>

</body>
</html>
