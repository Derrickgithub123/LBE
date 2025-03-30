
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php'; // Database connection

// Ensure user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Check if columns exist, if not create them
$check_original_price = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'original_price'");
if (mysqli_num_rows($check_original_price) == 0) {
    mysqli_query($conn, "ALTER TABLE products ADD COLUMN original_price DECIMAL(10,2) NULL AFTER price");
    mysqli_query($conn, "UPDATE products SET original_price = price WHERE original_price IS NULL OR original_price = 0");
}

$check_discount = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'discount'");
if (mysqli_num_rows($check_discount) == 0) {
    mysqli_query($conn, "ALTER TABLE products ADD COLUMN discount VARCHAR(10) NULL AFTER original_price");
}

$check_price_difference = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'price_difference'");
if (mysqli_num_rows($check_price_difference) == 0) {
    mysqli_query($conn, "ALTER TABLE products ADD COLUMN price_difference DECIMAL(10,2) NULL AFTER discount");
}

// Activate Offer: Apply 10% discount
if (isset($_POST['activate_offer'])) {
    mysqli_query($conn, "UPDATE products SET original_price = price WHERE original_price IS NULL OR original_price = 0");
    mysqli_query($conn, "UPDATE products SET price_difference = original_price * 0.1, price = original_price * 0.9, discount = '10% off' WHERE original_price IS NOT NULL");
    mysqli_query($conn, "UPDATE offers SET is_active = 1");

    $_SESSION['message'] = "Offer activated successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Deactivate Offer: Restore original prices
if (isset($_POST['deactivate_offer'])) {
    mysqli_query($conn, "UPDATE products SET price = original_price, price_difference = NULL, discount = NULL WHERE original_price IS NOT NULL");
    mysqli_query($conn, "UPDATE offers SET is_active = 0");

    $_SESSION['message'] = "Offer deactivated successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch products and offers
// $products = mysqli_query($conn, "SELECT p.product_id, p.name, p.image_url, p.description, 
//                                         p.price, p.original_price, p.discount, p.price_difference, 
//                                         COALESCE(o.is_active, 0) AS is_active 
//                                  FROM products p 
//                                  LEFT JOIN offers o ON p.product_id = o.product_id 
//                                  GROUP BY p.product_id");


$products = mysqli_query($conn, "SELECT p.product_id, p.name, p.image_url, p.description, 
                                        p.price, p.original_price, p.discount, p.price_difference, 
                                        COALESCE(o.is_active, 0) AS is_active 
                                 FROM products p 
                                 LEFT JOIN offers o ON p.product_id = o.product_id");

if (!$products) {
    die("SQL Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .image { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        .btn { 
            margin: 10px; 
            padding: 10px 20px; 
            border: none; 
            cursor: pointer; 
            font-size: 16px; 
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn:hover { opacity: 0.9; transform: scale(1.02); }
        .activate { background-color: #28a745; color: white; }
        .deactivate { background-color: #dc3545; color: white; }
        .back { background-color: #17a2b8; color: white; }
        .price-change { font-weight: bold; color: #28a745; }
        .original-price { text-decoration: line-through; color: #888; }
        .inactive { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Product Offers</h2>

        <!-- Offer Buttons -->
        <form method="POST">
            <button type="submit" name="activate_offer" class="btn activate">Activate Offer</button>
            <button type="submit" name="deactivate_offer" class="btn deactivate">Deactivate Offer</button>
            <a href="admin.php" class="btn back">Back to Admin Panel</a>
        </form>

        <?php if (isset($_SESSION['message'])) { ?>
            <p style="color: green; font-weight: bold;"> <?php echo $_SESSION['message']; unset($_SESSION['message']); ?> </p>
        <?php } ?>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Original Price (Ksh)</th>
                    <th>Current Price (Ksh)</th>
                    <th>Discount(%)</th>
                    <th>Price Difference (Ksh)</th>
                    <th>Offer Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($products)) { ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Product Image" class="image"></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>Ksh <?php echo number_format($row['original_price'], 2); ?></td>
                    <td>Ksh <?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['discount'] ? $row['discount'] : '—'; ?></td>
                    <td><?php echo $row['price_difference'] ? 'Ksh ' . number_format($row['price_difference'], 2) : '—'; ?></td>
                    <td class="<?php echo $row['is_active'] ? '' : 'inactive'; ?>">
                        <?php echo ($row['is_active']) ? "✅ Active" : "❌ Inactive"; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
