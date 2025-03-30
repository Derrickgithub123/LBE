<?php
session_start();
include("config.php"); // Database connection

// Fetch all products from the database
$query = "SELECT * FROM products ORDER BY product_id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query error: " . mysqli_error($conn));
}

// Fetch reviews grouped by product
$reviews_query = "SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews GROUP BY product_id";
$reviews_result = mysqli_query($conn, $reviews_query);

$reviews_data = [];
while ($review = mysqli_fetch_assoc($reviews_result)) {
    $reviews_data[$review['product_id']] = $review;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Lightning Bolt Electronics</title>
    <a href="index.php" class="btn">← Back to Home</a>
    <a href="shop.php" class="btn">← Back to Shop</a>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
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
            background: #111;
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
            color: #5E68E6;
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
        .rating {
            font-size: 18px;
            color: gold;
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
        .review-form {
            margin-top: 15px;
            background: #222;
            padding: 10px;
            border-radius: 10px;
        }
        .review-form textarea {
            width: 90%;
            padding: 5px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Add your product reviews here!</h1>
        <div class="products-grid">
            <?php while ($product = mysqli_fetch_assoc($result)) : ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'img/default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">

                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>

                    <h3>
                        <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>">
                            <?php echo htmlspecialchars($product['description'] ?? 'No description available'); ?>
                        </a>
                    </h3>

                    <p class="price">Price: Ksh <?php echo number_format($product['price'], 2); ?></p>
                    <p>Stock Available: <?php echo $product['stock_quantity'] ?? 'Out of Stock'; ?></p>

                    <!-- Display Ratings -->
                    <?php
                    $product_id = $product['product_id'];
                    if (isset($reviews_data[$product_id])) {
                        $avg_rating = round($reviews_data[$product_id]['avg_rating'], 1);
                        $total_reviews = $reviews_data[$product_id]['total_reviews'];
                        echo "<p class='rating'>⭐ $avg_rating/5 ($total_reviews reviews)</p>";
                    } else {
                        echo "<p class='rating'>No ratings yet</p>";
                    }
                    ?>

                    <!-- Add to Cart Form -->
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                        <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                    </form>

                    <!-- Review Form -->
                    <div class="review-form">
                        <h3>Leave a Review</h3>
                        <form action="add_review.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                            <label for="rating">Rate(1-5):</label>
                            <select name="rating" required>
                                <option value="1">⭐ 1</option>
                                <option value="2">⭐⭐ 2</option>
                                <option value="3">⭐⭐⭐ 3</option>
                                <option value="4">⭐⭐⭐⭐ 4</option>
                                <option value="5">⭐⭐⭐⭐⭐ 5</option>
                            </select>

                            <br><br>

                            <label for="review_text">Your Review:</label>
                            <textarea name="review_text" required rows="3"></textarea>

                            <br><br>

                            <button type="submit" class="btn">Submit Review</button>
                            
                        </form>
                        <br><br>
                        <a href="product_review.php?product_id=<?php echo urlencode($product['product_id']); ?>" class="btn">See Reviews</a>

                    </div>

                </div>
            <?php endwhile; ?>
        </div>

        
    </div>

</body>
</html>
