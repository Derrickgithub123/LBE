<?php
require 'simple_html_dom.php'; // Import Simple HTML DOM parser

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>");
} else {
    echo "<p style='color: green;'>Connected to database successfully!</p>";
}

// Load the local `shop.html` file instead of `shop.php`
$html = file_get_html('shop.html');

// Check if page loads successfully
if (!$html) {
    die("<p style='color: red;'>Failed to load shop.html</p>");
}

// Set a default category_id (update based on your categories)
$category_id = 1;

// Loop through each product in `shop.html`
foreach ($html->find('.product-card') as $product) {
    // Extract product name from `<h3>`
    $name = trim($product->find('h3', 0)->plaintext ?? 'No Name');

    // Extract description (ensure it grabs the correct `<p>`)
    $description = trim($product->find('p', 0)->plaintext ?? 'No Description');

    // Extract price and clean format
    $price_raw = trim($product->find('span', 0)->plaintext ?? '0');
    $price = filter_var(str_replace(['Ksh', ',', ' '], '', $price_raw), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Extract image URL
    $image_url = trim($product->find('img', 0)->src ?? '');
    $image = basename($image_url);
    if (!empty($image_url) && strpos($image_url, 'http') === false) {
        $image_url = "http://localhost/portifolio/" . ltrim($image_url, '/');
    }

    // Set default stock quantity and timestamp
    $stock_quantity = 10;
    $created_at = date('Y-m-d H:i:s');

    // 🔍 Check if the product already exists in the database
    $stmt_check = $conn->prepare("SELECT name FROM products WHERE name = ?");
    $stmt_check->bind_param("s", $name);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 0) {
        // ✅ Insert the product into the database
        $stmt = $conn->prepare("
            INSERT INTO products (name, description, price, stock_quantity, category_id, image_url, image, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssdissss", $name, $description, $price, $stock_quantity, $category_id, $image_url, $image, $created_at);
        
        if ($stmt->execute()) {
            echo "<p style='color: blue;'>Inserted: $name - Price: Ksh $price</p>";
        } else {
            echo "<p style='color: red;'>Error inserting $name: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: gray;'>Skipped (already exists): $name</p>";
    }

    $stmt_check->close();
}

// Close database connection
$conn->close();
echo "<p style='color: green;'>Process completed successfully!</p>";
?>
