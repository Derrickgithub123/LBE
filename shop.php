<?php
// Enhanced security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Start secure session
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'use_strict_mode' => true
]);
session_regenerate_id(true);

// Set cache control headers
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 

// Database connection with error handling
require_once 'db_connect.php';

// Initialize variables with sanitized defaults
$offer_name = htmlspecialchars("Special Discount", ENT_QUOTES, 'UTF-8');
$offer_end_date = date('F j, Y', strtotime('+7 days'));
$discount_percentage = 10;
$cart_count = 0;
$search_query = '';
$category_filter = 'all';
$sort_by = 'name-asc';
$products_by_category = [];
$notification_message = '';

// Initialize cart count with proper type checking
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item)) {
            $cart_count += (int)($item['quantity'] ?? 0);
        } elseif (is_numeric($item)) {
            $cart_count += (int)$item;
        }
    }
}

$search_condition = "";
$search_params = [];
$search_query = "";

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);
    $search_condition = " WHERE name LIKE ? OR description LIKE ?";
    $search_param = "%" . $search_query . "%";
    $search_params = [$search_param, $search_param];
}


// Fetch active offer status
$offer_active = false;
$discount_percentage = 0;

$offer_stmt = $conn->prepare("SELECT is_active, discount_percentage FROM offers WHERE is_active = 1 LIMIT 1");
if ($offer_stmt) {
    $offer_stmt->execute();
    $offer_result = $offer_stmt->get_result();
    if ($offer_result && $offer_result->num_rows > 0) {
        $offer_data = $offer_result->fetch_assoc();
        $offer_active = (bool)$offer_data['is_active'];
        $discount_percentage = isset($offer_data['discount_percentage']) ? (int)$offer_data['discount_percentage'] : 0;
    }
    $offer_stmt->close();
}

// Handle AJAX requests first
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'cart_count' => $cart_count,
        'message' => 'Cart updated successfully'
    ]);
    exit;
}

// Process add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    
    if ($product_id) {
        // Verify product exists
        $product_check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
        $product_check->bind_param("i", $product_id);
        $product_check->execute();
        $product_result = $product_check->get_result();
        
        if ($product_result && $product_result->num_rows > 0) {
            // Initialize cart if not exists
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Add item to cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = ['quantity' => $quantity];
            }
            
            
            $notification_message = "Product added to cart!";
            
            // If AJAX request, return JSON response
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'cart_count' => $cart_count,
                    'message' => $notification_message
                ]);
                exit;
            }
        } else {
            $notification_message = "Product not found!";
        }
        $product_check->close();
    }
    
    // Store notification in session and redirect
    $_SESSION['notification'] = $notification_message;
    header('Location: shop.php');
    exit();
}

// Get notification from session if exists
if (isset($_SESSION['notification'])) {
    $notification_message = htmlspecialchars($_SESSION['notification'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['notification']);
}
// Process filters with validation
$allowed_categories = ['all'];
$category_stmt = $conn->prepare("SELECT DISTINCT name FROM categories ORDER BY name");
if ($category_stmt) {
    $category_stmt->execute();
    $category_result = $category_stmt->get_result();
    while ($cat = $category_result->fetch_assoc()) {
        $allowed_categories[] = $cat['name'];
    }
    $category_stmt->close();
}

if (isset($_GET['category']) && in_array($_GET['category'], $allowed_categories)) {
    $category_filter = $_GET['category'];
}

$allowed_sorts = ['name-asc', 'name-desc', 'price-asc', 'price-desc'];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts)) {
    $sort_by = $_GET['sort'];
}

// Build the product query with prepared statements
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.category_id 
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($search_query)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

if ($category_filter != 'all') {
    $sql .= " AND c.name = ?";
    $params[] = $category_filter;
    $types .= 's';
}

// Add sorting
switch ($sort_by) {
    case 'name-desc': $sql .= " ORDER BY p.name DESC"; break;
    case 'price-asc': $sql .= " ORDER BY p.price ASC"; break;
    case 'price-desc': $sql .= " ORDER BY p.price DESC"; break;
    default: $sql .= " ORDER BY p.name ASC";
}

// Execute query with prepared statement
$product_stmt = $conn->prepare($sql);
if ($product_stmt) {
    if (!empty($params)) {
        $product_stmt->bind_param($types, ...$params);
    }
    $product_stmt->execute();
    $result = $product_stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Apply discount if offer is active
            if ($offer_active && $discount_percentage > 0) {
                $row['original_price'] = $row['price'];
                $row['price'] = round($row['price'] * (1 - ($discount_percentage / 100)), 2);
            }
            $category = $row['category_name'];
            $products_by_category[$category][] = $row;
        }
    }
    $product_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightning Bolt Electronics</title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="icon" href="img/e.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
    body {
            background-color: #000;
            color: #fff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .products-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 35px;
    padding: 75px;
}

/* Category Headers */
.products-container h2 {
    grid-column: 1 / -1;
    border-bottom: 2px solid #444;
    padding-bottom: 10px;
    margin-top: 20px;
    color: #fff;
    font-size: 1.5rem;
}

/* Product Card */
.product-card {
    background: #111;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #333;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

/* Product Image */
.product-card img {
    width: 100%;
    height: 180px;
    object-fit: contain;
    margin-bottom: 15px;
}

/* Product Name - Small White */
.product-card h3 {
    font-size: 14px;
    color: #fff;
    margin: 0 0 8px 0;
    font-weight: normal;
    line-height: 1.3;
}

.product-card h3 a {
    color: #fff;
    text-decoration: none;
}

/* Product Description - Blue and Large */
.product-card p {
    color: #5E68E6;
    font-size: 16px;
    margin: 0 0 15px 0;
    line-height: 1.5;
    min-height: 48px;
}

/* Price Container */
.price-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin: 15px 0;
    flex-wrap: wrap;
}

/* Original Price - White with Strikethrough */
.original-price {
    font-size: 14px;
    color: #fff;
    text-decoration: line-through;
}

/* Current Price - Yellow */
.current-price {
    font-size: 18px;
    font-weight: bold;
    color: #f4c542;
}

/* Discount Tag - Smaller Size */
.offer-tag {
    background: #ff4757;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: bold;
}

/* Add to Cart Button */
.btn-add-cart {
    display: block;
    width: 100%;
    padding: 10px;
    background: #5E68E6;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-add-cart:hover {
    background: #4a52c7;
}

.btn-add-cart i {
    margin-right: 8px;
}

/* No Products Found */
.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px;
}

.no-products h3 {
    color: #fff;
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.no-products p {
    color: #aaa;
    margin-bottom: 20px;
}

.no-products .btn {
    display: inline-block;
    padding: 10px 20px;
    background: #5E68E6;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s;
}

.no-products .btn:hover {
    background: #4a52c7;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .products-container {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 992px) {
    .products-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .products-container {
        grid-template-columns: repeat(2, 1fr);
        padding: 35px;
        gap: 25px;
    }
}

@media (max-width: 576px) {
    .products-container {
        grid-template-columns: 1fr;
        padding: 20px;
        gap: 20px;
    }
    
    .product-card {
        max-width: 100%;
    }
}
        nav ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        background-color: #222;
        padding: 10px 0;
        }

    nav ul li {
        margin: 0 15px;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s;
    }

    nav ul li a:hover {
        color: #f4c542;
    }

    .account-dropdown {
        position: relative;
        display: inline-block;
    }

    .account-dropdown-content {
        display: none;
        position: absolute;
        margin-right: 100px;
        background-color: white;
        min-width: 150px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1;
        padding: 10px;
        border-radius: 5px;
    }

    .account-dropdown-content a {
        text-decoration: none;
        display: block;
        padding: 8px;
        color: black;
    }

    .account-dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .account-dropdown:hover .account-dropdown-content {
        display: block;
    }
    nav ul li .logout-btn {
        background-color: red;
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }
    nav ul li .logout-btn:hover {
        background-color: darkred;
    }
    #backToTopBtn, #scrollToBottomBtn {
        position: fixed;
        right: 20px;
        background-color: #f4c542;
        color: #000;
        border: none;
        padding: 12px 15px;
        font-size: 18px;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease-in-out;
    }
    #backToTopBtn {
        bottom: 60px;
    }
    #backToTopBtn:hover {
        background-color: #ffcc00;
    }
    #scrollToBottomBtn {
        bottom: 20px;
    }
    #scrollToBottomBtn:hover {
        background-color: #ffcc00;
    }
    .view-cart {
        position: fixed;
        top: 60%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: green;
        color: white;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: bold;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .view-cart:hover {
        background: darkgreen;
    }
/* Product Cards - Updated Styling */
.products-container {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 35px;
    padding: 75px;
}

.product-card {
    background: #111;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    transition: transform 0.3s;
    border: 1px solid #333;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-card img {
    width: 100%;
    max-height: 200px;
    object-fit: contain;
    margin-bottom: 15px;
}

.product-card h3 {
        font-size: 15px;

    }
    .product-card h3 a {
        color:white;
        text-decoration: none;

    }

.product-card p {
    color: #5E68E6; /* Blue */
    font-size: 20px; /* Large */
    margin: 0 0 15px 0;
    line-height: 1.4;
}

/* Price styling - unchanged as requested */
.price-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.original-price {
    font-size: 14px;
    color: #fff; /* White */
    text-decoration: line-through;
}

.current-price {
    font-size: 18px;
    font-weight: bold;
    color: #f4c542; /* Yellow */
}

.offer-tag {
    background: #ff4757;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px; /* Reduced size */
    font-weight: bold;
}

.btn-add-cart {
    display: block;
    width: 100%;
    padding: 10px;
    background: #5E68E6;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-add-cart:hover {
    background: #4a52c7;
}

/* Popup/Alert styling - completely unchanged */
.alert-offer {
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    color: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 1000;
    display: flex;
    align-items: center;
    max-width: 350px;
    animation: slideIn 0.5s forwards;
}

@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

.alert-offer i {
    font-size: 24px;
    margin-right: 15px;
}

.alert-offer-content {
    flex: 1;
}

.alert-offer h4 {
    margin: 0 0 5px 0;
    font-size: 18px;
}

.alert-offer p {
    margin: 0;
    font-size: 14px;
}

.close-alert {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    margin-left: 10px;
}
footer {
        background-color: #111;
        text-align: center;
        padding: 15px;
        color: #fff;
        font-size: 14px;
        border-top: 2px solid #444;
    }
    footer p {
        margin: 5px 0;
    }
    footer a {
        color: #f4c542;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s ease-in-out;
    }
    footer a:hover {
        color: #ffcc00;
    }
    .social-links {
        margin-top: 10px;
    }
    .social-links a {
        color: #fff;
        margin: 0 15px;
        font-size: 18px;
        transition: color 0.3s;
        text-decoration: none;
    }
    .social-links a:hover {
        color: #f4c542;
    }
    #searchform {
        position: absolute;
        top: 10px;
        right: 40px;
        display: flex;
        align-items: center;
        background-color: white;
        padding: 5px 0;
        border-radius: 5px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10; 
    }
    
    .search-wrapper {
        display: flex;
        align-items: center;
    }
    
    #searchinput {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 200px; 
        font-size: 14px;
        color: #333; 
        background-color: #f9f9f9; 
    }
    
    #searchform button {
        padding: 8px 12px;
        border: none;
        background-color:black;
        color: white;
        margin: 10px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }
    
    #searchform button:hover {
        background-color: black;
    }
/* Rest of your existing styles remain unchanged */
/* ... */

@media (max-width: 1200px) {
    .products-container {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 992px) {
    .products-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .products-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .products-container {
        grid-template-columns: 1fr;
    }
}
.filter-sidebar {
            background: #333;
            padding: 20px;
            border-radius: 8px;
            margin: 20px;
            width: 250px;
            top:130px;
            float: left;
            color: #fff;
        }
        
        .filter-sidebar h3 {
            margin-top: 0;
            border-bottom: 1px solid #555;
            padding-bottom: 10px;
            color: #f4c542;
        }
        
        .filter-sidebar label {
            display: block;
            margin: 15px 0 5px;
            color: #fff;
        }
        
        .filter-sidebar select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: none;
            background: black;
            color: #fff;
        }
        
        .filter-sidebar button {
            background: #5E68E6;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            width: 100%;
            margin-top: 15px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .filter-sidebar button:hover {
            background: #4a52c7;
        }
        /* Style for the sort container (if you want to group them) */
.sort-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

/* Label style */
label[for="sort-by"] {
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

/* Select box style */
#sort-by {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    font-size: 14px;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 180px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Hover state */
#sort-by:hover {
    border-color: #bbb;
}

/* Focus state */
#sort-by:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

/* Option styles */
#sort-by option {
    padding: 8px;
    background-color: white;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .sort-container {
        flex-direction: column;
        align-items: flex-start;
    }
    
    #sort-by {
        width: 100%;
    }
}

        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px;
        } /* Your existing CSS styles remain exactly the same */
        @keyframes slideIn {
        from {right: -300px; opacity: 0;}
        to {right: 20px; opacity: 1;}
    }
    @keyframes fadeOut {
        from {opacity: 1;}
        to {opacity: 0;}
    }
    /* General adjustments for mobile responsiveness */
@media (max-width: 768px) {
    /* Adjust header font size */
    header {
        font-size: 20px;
        padding: 10px 0;
    }

    /* Search form adjustments */
    #searchform {
        position: relative; /* Change to relative for better positioning */
        top: auto; /* Reset top positioning */
        right: auto; /* Reset right positioning */
        margin: 10px; /* Add margin for spacing */
        width: calc(100% - 20px); /* Full width with padding */
        box-shadow: none; /* Remove shadow for simplicity */
    }

    #searchinput {
        width: 100%; /* Full width for input */
        margin: 0; /* Reset margin */
    }

    /* Filter sidebar adjustments */
    .filter-sidebar {
        width: 100%; /* Full width on mobile */
        margin: 10px 0; /* Add margin for spacing */
        position: relative; /* Reset position */
        float: none; /* Remove float */
    }

    /* Adjust product card padding */
    .product-card {
        padding: 15px; /* Reduce padding for mobile */
    }

    /* Adjust product image height */
    .product-card img {
        height: 150px; /* Reduce image height */
    }

    /* Adjust product name font size */
    .product-card h3 {
        font-size: 12px; /* Smaller font size */
    }

    /* Adjust product description font size */
    .product-card p {
        font-size: 14px; /* Smaller font size */
    }

    /* Adjust price container */
    .price-container {
        flex-direction: column; /* Stack items vertically */
        align-items: flex-start; /* Align items to the start */
    }

    /* Adjust no products found message */
    .no-products {
        padding: 20px; /* Reduce padding */
    }

    /* Adjust buttons */
    .btn-add-cart {
        padding: 8px; /* Reduce padding */
        font-size: 14px; /* Smaller font size */
    }

    /* Adjust sort container */
    .sort-container {
        flex-direction: column; /* Stack items vertically */
        align-items: flex-start; /* Align items to the start */
    }

    /* Adjust select box */
    #sort-by {
        width: 100%; /* Full width */
        margin-bottom: 10px; /* Add margin for spacing */
    }
}

/* Additional adjustments for very small screens */
@media (max-width: 480px) {
    /* Further reduce header font size */
    header {
        font-size: 18px;
    }

    /* Adjust button sizes */
    #backToTopBtn, #scrollToBottomBtn {
        padding: 10px; /* Smaller padding */
        font-size: 16px; /* Smaller font size */
    }

    /* Adjust view cart button */
    .view-cart {
        font-size: 14px; /* Smaller font size */
        padding: 10px 20px; /* Smaller padding */
    }
}
</style>
</head>
<body>
    <?php if ($offer_active): ?>
    <div class="alert-offer" id="offerAlert">
        <i class="fas fa-tag"></i>
        <div class="alert-offer-content">
            <h4>Special Offer</h4>
            <p>Enjoy 10% off on selected products!</p>
        </div>
        <button class="close-alert" onclick="document.getElementById('offerAlert').style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endif; ?>

    <?php if (!empty($notification_message)): ?>
    <div class="notification" id="notification">
        <?= $notification_message ?>
    </div>
    <?php endif; ?>

    <section>
        <header><i class="fas fa-shopping-bag"></i> Shop</header>
      
        <button onclick="scrollToTop()" id="backToTopBtn"><i class="fas fa-arrow-up"></i></button>
        <button onclick="scrollToBottom()" id="scrollToBottomBtn"><i class="fas fa-arrow-down"></i></button>

        <nav>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="shop.php"><i class="fas fa-store"></i> Shop</a></li>
                <li><a href="contact.php"><i class="fas fa-phone-alt"></i> Contact</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="faqs.php"><i class="fas fa-question-circle"></i> FAQs</a></li>

                <li>
                    <?php if (isset($_SESSION["user_id"])): ?>
                        <div class="account-dropdown">
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <div class="account-dropdown-content">
                                <a href="profile.php">My Account</a>
                                <a href="orders.php">My Orders</a>
                                <a href="logout.php" class="logout-btn">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.html"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>

        <form id="searchform" method="GET" action="shop.php">
            <div class="search-wrapper">
                <input type="text" id="searchinput" name="query" placeholder="Search products..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>

        <div class="cart-icon">
            <a href="cart.php">
                <i class="fas fa-shopping-cart" style="font-size: 24px; color: white;"></i>
                <span class="cart-count"><?= htmlspecialchars($cart_count, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        </div>
    </section>

    <a href="cart.php" class="view-cart">
        <i class="fas fa-shopping-cart"></i> View Cart (<?= htmlspecialchars($cart_count, ENT_QUOTES, 'UTF-8'); ?>)
    </a>

    <aside class="filter-sidebar">
        <h3>Filters</h3>
        <form id="filter-form" method="GET" action="shop.php">
            <?php if (!empty($search_query)): ?>
                <input type="hidden" name="q" value="<?= htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
            
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="all" <?= $category_filter == 'all' ? 'selected' : '' ?>>All Categories</option>
                <?php
                $categories_sql = "SELECT DISTINCT name FROM categories ORDER BY name";
                $categories_result = $conn->query($categories_sql);
                while ($cat = $categories_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>" <?= $category_filter == $cat['name'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="sort-by">Sort By:</label>
            <select id="sort-by" name="sort">
                <option value="name-asc" <?= $sort_by == 'name-asc' ? 'selected' : '' ?>>Name: A-Z</option>
                <option value="name-desc" <?= $sort_by == 'name-desc' ? 'selected' : '' ?>>Name: Z-A</option>
                <option value="price-asc" <?= $sort_by == 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price-desc" <?= $sort_by == 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
            </select>

            <button type="submit">Apply Filters</button>
            <?php if ($category_filter != 'all' || $sort_by != 'name-asc'): ?>
                <a href="shop.php" style="display: block; text-align: center; margin-top: 10px; color: #f4c542;">Reset Filters</a>
            <?php endif; ?>
        </form>
    </aside>

    <div class="shop-container">
        <p>Powering Your World, One Device at a Time!</p>
    </div>
    
    <?php if ($offer_active): ?>
    <div class="offer-banner">
        <i class="fas fa-tag"></i> Special Offer: <?= htmlspecialchars($offer_name, ENT_QUOTES, 'UTF-8'); ?> - Get <?= htmlspecialchars(10, ENT_QUOTES, 'UTF-8'); ?>% off! Ends <?= htmlspecialchars($offer_end_date, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>
    <section>
        <div class="products-container">
            <?php if (empty($products_by_category)): ?>
                <div class="no-products">
                    <h3>No products found matching your criteria</h3>
                    <p>Try adjusting your filters or search terms</p>
                    <a href="shop.php" class="btn" style="display: inline-block; margin-top: 20px;">Show All Products</a>
                </div>
            <?php else: ?>
                <?php foreach ($products_by_category as $category => $products): ?>
                    <h2 style="grid-column: 1 / -1; border-bottom: 2px solid #444; padding-bottom: 10px; margin-top: 20px;">
                        <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <h3>
                                <a href="product_details.php?product_id=<?= (int)$product['product_id']; ?>">
                                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h3>
                            <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="price-container">
                                <?php 
                                $show_discount = isset($product['original_price']) && $product['original_price'] > 0 && $product['original_price'] > $product['price'];
                                $discount_percent = $show_discount ? round((($product['original_price'] - $product['price']) / $product['original_price']) * 100, 2) : 0;
                                
                                if ($offer_active && $show_discount): ?>
                                    <span class="original-price">Ksh <?= number_format($product['original_price'], 2); ?></span>
                                    <span class="current-price">Ksh <?= number_format($product['price'], 2); ?></span>
                                    <span class="offer-tag"><?= (int)$discount_percent ?>% OFF</span>
                                <?php else: ?>
                                    <span class="current-price">Ksh <?= number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="shop.php" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?= (int)$product['product_id']; ?>">
                                <input type="hidden" name="add_to_cart" value="1">
                                <button type="submit" class="btn-add-cart">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p><i class="fas fa-copyright"></i> <?= date("Y"); ?> Lightning Bolt Electronics. All Rights Reserved.</p>
        <p><a href="terms&privacy.php"><i class="fas fa-file-contract"></i> Terms & Privacy</a></p>
        <div class="social-links">
            <a href="https://twitter.com/YourHandle" target="_blank" rel="noopener noreferrer" class="social-icon">
                <i class="fab fa-twitter"></i> Twitter
            </a>
            <a href="https://facebook.com/YourPage" target="_blank" rel="noopener noreferrer" class="social-icon">
                <i class="fab fa-facebook"></i> Facebook
            </a>
            <a href="https://instagram.com/YourHandle" target="_blank" rel="noopener noreferrer" class="social-icon">
                <i class="fab fa-instagram"></i> Instagram
            </a>
        </div>
    </footer>

    <script>
        // Scroll buttons functionality
        window.onscroll = function() { toggleScrollButtons(); };

        function toggleScrollButtons() {
            var topButton = document.getElementById("backToTopBtn");
            var bottomButton = document.getElementById("scrollToBottomBtn");

            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                topButton.style.display = "block";
            } else {
                topButton.style.display = "none";
            }

            if (window.innerHeight + window.scrollY < document.body.scrollHeight - 300) {
                bottomButton.style.display = "block";
            } else {
                bottomButton.style.display = "none";
            }
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: "smooth" });
        }

        function scrollToBottom() {
            window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });
        }
        
        // Show notification
        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
        
        // Close popup
        function closePopup() {
            document.getElementById('discountPopup').style.display = 'none';
        }

        // Show discount popup if offer is active
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($offer_active): ?>
                document.getElementById('discountPopup').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('discountPopup').style.display = 'none';
                }, 5000);
            <?php endif; ?>
            
            // Check for added to cart notification
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('added')) {
                showNotification('Product added to cart!');
            }
        });

        // Auto-refresh when offer status changes (for admin)
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        function checkOfferStatus() {
            fetch('check_offer_status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.statusChanged) {
                        location.reload();
                    }
                });
        }
        setInterval(checkOfferStatus, 5000);
        <?php endif; ?>
    </script>
</body>
</html>