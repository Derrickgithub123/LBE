<?php
include("config.php");

// Validate the product_id
if (!isset($_GET['product_id']) || empty($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    die("Invalid product ID.");
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Error: Product not found.");
}

// Fetch reviews for this product
$reviews_query = "SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $reviews_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$reviews_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - <?php echo htmlspecialchars($product['name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .product-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .review-card {
            background: #111;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
            text-align: left;
        }
        .review-card p {
            margin: 5px 0;
        }
        .review-rating {
            color: gold;
            font-size: 18px;
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
            margin-top: 10px;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="product-title">Reviews for <?php echo htmlspecialchars($product['name']); ?></h1>

        <?php if (mysqli_num_rows($reviews_result) == 0) : ?>
            <p>No reviews available for this product.</p>
        <?php else : ?>
            <?php while ($review = mysqli_fetch_assoc($reviews_result)) : ?>
                <div class="review-card">
                    <p class="review-rating">Rating: ⭐ <?php echo $review['rating']; ?>/5</p>
                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <a href="index.php" class="btn">← Back to Home</a>
<a href="shop.php" class="btn">← Back to Shop</a>
<a href="product_details.php?product_id=<?php echo htmlspecialchars($product['product_id']); ?>" class="btn">← Back to Product Details</a>

    </div>

</body>
</html>
