<?php
session_start();
// Security headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

require_once 'db_connect.php';

// Cart functionality - now with input validation
if (isset($_GET['add'])) {
    $product_id = (int)$_GET['add']; // Force integer type
    if ($product_id > 0) { // Validate it's a positive number
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        // Initialize if not set, then increment
        $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
        header("Location: index.php?added=true");
        exit();
    }
}

// Secure cart count calculation
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += is_array($item) ? ($item['quantity'] ?? 0) : (int)$item;
    }
}

// Check if offer is active using prepared statement
$offer_active = false;
$stmt = $conn->prepare("SELECT is_active FROM offers LIMIT 1");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $offer_row = $result->fetch_assoc();
        $offer_active = (bool)$offer_row['is_active'];
    }
    $stmt->close();
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

$query = "SELECT p.product_id, p.name, p.description, p.price, p.image_url, 
                 p.original_price, p.discount, p.price_difference 
          FROM products p" . $search_condition . " ORDER BY p.created_at DESC LIMIT 20";

// $query = "SELECT p.product_id, p.name, p.description, p.price, p.image_url, 
//        p.price 
// FROM products p  
// WHERE p.name LIKE '%laptop%'  
// ORDER BY p.created_at DESC  
// LIMIT 20;
// ";

$stmt = $conn->prepare($query);

if ($stmt) {
    // Bind parameters if we're doing a search
    if (!empty($search_params)) {
        $stmt->bind_param("ss", ...$search_params);
    }
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $has_offer = ($offer_active && $row['original_price'] > 0 && $row['price'] < $row['original_price']);
                ?>
                
                <?php
            }
        } else {
            echo "<p>No products found" . (!empty($search_query) ? " matching your search" : "") . ".</p>";
        }
    } else {
        echo "<p>Error executing query. Please try again later.</p>";
    }
    $stmt->close();
} else {
    echo "<p>Error preparing queryyyyyyyyyy. Please try again later.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightning Bolt Electronics</title>
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

    .hero-section {
        background: url('img/banner.jpg') no-repeat center center/cover;
        text-align: center;
        padding: 100px 0;
        color: white;
    }
    .hero-content h2 {
        font-size: 36px;
    }
    .hero-content p {
        font-size: 20px;
        margin-bottom: 20px;
    }
    .btn-shop-now {
        display: inline-block;
        background: white;
        color: black;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        transition: 0.3s;
    }
    .btn-shop-now:hover {
        background: #ccc;
    }
    .new-arrivals {
        padding: 20px;
        text-align: center;
    }
    .products-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        padding: 20px ;
    }
    .product-card {
        position: relative;
        background: #111;
        padding: 15px 0;
        border-radius: 8px;
        text-align: center;
        transition: transform 0.3s;
    }
    .product-card img {
        width: 100%;
        max-height: 200px;
        object-fit: contain;
    }
    .product-card h3 {
        font-size: 18px;
    }
    .product-card h3 a {
        color: #5E68E6;
        text-decoration: none;
        font-weight: bold;
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
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }
    
    #searchform button:hover {
        background-color: black;
    }
    
    .cart-icon {
        position: absolute;
        top: 10px;
        right: 500px;
        display: inline-block;
    }
    .cart-count {
        position: absolute;
        top: -10px;
        right: -10px;
        background: red;
        color: white;
        border-radius: 50%;
        padding: 5px 8px;
        font-size: 14px;
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
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 1000;
        display: none;
        animation: slideIn 0.5s, fadeOut 0.5s 2.5s;
    }
    @keyframes slideIn {
        from {right: -300px; opacity: 0;}
        to {right: 20px; opacity: 1;}
    }
    @keyframes fadeOut {
        from {opacity: 1;}
        to {opacity: 0;}
    }
    .offer-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ff4757;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: bold;
        z-index: 2;
    }
    .original-price {
        text-decoration: line-through;
        color: #aaa;
        font-size: 14px;
    }
    .current-price {
        color: #f4c542;
        font-weight: bold;
        font-size: 18px;
    }
    .price-container {
        margin: 10px 0;
    }
    .discount-tag {
        color: #ff4757;
        font-weight: bold;
        font-size: 14px;
        margin-top: 5px;
    }
    .btn-add-cart {
        background: #3498db;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-size: 16px;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        text-decoration: none;
    }
    .btn-add-cart i {
        margin-right: 8px;
    }
    .btn-add-cart:hover {
        background: #2980b9;
        transform: scale(1.05);
    }
    .discount-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #ff4757;
        color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
        z-index: 1000;
        display: none;
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }
    .close-popup {
        position: absolute;
        top: 5px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
    }
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
        nav ul li {
            display: block;
            text-align: center;
        }
    }
    
    /* General adjustments for mobile responsiveness */
@media (max-width: 768px) {
    /* Header adjustments */
    header {
        font-size: 20px; /* Smaller font size */
        padding: 10px; /* Reduced padding */
    }

    /* Search form adjustments */
    #searchform {
        position: relative; /* Change to relative for better positioning */
        margin: 10px; /* Add margin for spacing */
        width: calc(100% - 20px); /* Full width with padding */
        box-shadow: none; /* Remove shadow for simplicity */
    }

    .search-wrapper {
        width: 100%; /* Full width for search wrapper */
    }

    #searchinput {
        width: 100%; /* Full width for input */
        margin: 0; /* Reset margin */
        padding: 8px; /* Adjust padding */
    }

    #searchform button {
        padding: 8px 12px; /* Adjust button padding */
        margin-left: 5px; /* Add margin for spacing */
    }

    /* View cart button adjustments */
    .view-cart {
        position: fixed; /* Keep it fixed */
        bottom: 20px; /* Position at the bottom */
        left: 50%; /* Center horizontally */
        transform: translateX(-50%); /* Center alignment */
        background: green; /* Background color */
        color: white; /* Text color */
        padding: 10px 15px; /* Adjust padding */
        font-size: 14px; /* Smaller font size */
        border-radius: 5px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor */
        display: inline-block; /* Inline block for button */
        z-index: 1000; /* Ensure it stays on top */
    }

    .view-cart:hover {
        background: darkgreen; /* Darker background on hover */
    }

    /* Scroll buttons adjustments */
    #backToTopBtn, #scrollToBottomBtn {
        position: fixed; /* Keep them fixed */
        right: 20px; /* Position on the right */
        background-color: #f4c542; /* Background color */
        color: #000; /* Text color */
        border: none; /* No border */
        padding: 10px; /* Adjust padding */
        font-size: 16px; /* Smaller font size */
        border-radius: 50%; /* Circular buttons */
        cursor: pointer; /* Pointer cursor */
        display: none; /* Initially hidden */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Shadow effect */
        transition: background-color 0.3s ease-in-out; /* Smooth transition */
    }

    #backToTopBtn {
        bottom: 70px; /* Position above the bottom button */
    }

    #scrollToBottomBtn {
        bottom: 20px; /* Position at the bottom */
    }

    #backToTopBtn:hover, #scrollToBottomBtn:hover {
        background-color: #ffcc00; /* Change background on hover */
    }
}

/* Additional adjustments for very small screens */
@media (max-width: 480px) {
    /* Further reduce header font size */
    header {
        font-size: 18px; /* Smaller font size */
    }

    /* Adjust view cart button */
    .view-cart {
        font-size: 12px; /* Smaller font size */
        padding: 8px 12px; /* Smaller padding */
    }

    /* Adjust scroll buttons */
    #backToTopBtn, #scrollToBottomBtn {
        padding: 8px; /* Smaller padding */
        font-size: 14px; /* Smaller font size */
    }
}
</style>
        
    </style>
</head>
<body>
    <div class="discount-popup" id="discountPopup">
        <span class="close-popup" onclick="closePopup()">&times;</span>
        <h3>Special Offer Active!</h3>
        <p>Enjoy 10% off on selected products!</p>
    </div>

    <div id="notification" class="notification" style="display: none;"></div>
    
    <header>
        <i class="fas fa-bolt"></i> LB-Electronics
        <form id="searchform" action="index.php" method="GET">
            <div class="search-wrapper">
                <input type="text" id="searchinput" name="query" placeholder="Search products..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>
    
        <div class="cart-icon">
            <a href="cart.php">
                <i class="fas fa-shopping-cart fa-2x" style="color: white;"></i>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            </a>
        </div>
    </header>
   
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

    <section class="hero-section">
        <div class="hero-content">
            <h2>Welcome to Lightning Bolt Electronics</h2>
            <p>Your one-stop shop for the best electronics in town!</p>
            <a href="shop.php" class="btn-shop-now">Shop Now</a>
        </div>
    </section>
    
    <div class="cart-container">
        <a href="cart.php" class="view-cart">View Cart (<?php echo $cart_count; ?>)</a>
    </div>

    <section class="new-arrivals">
        <h2>New Arrivals</h2>
        <div class="products-container">
            <?php
            $query = "SELECT p.product_id, p.name, p.description, p.price, p.image_url, 
                 p.original_price, p.discount, p.price_difference 
          FROM products p" . $search_condition . " ORDER BY p.created_at DESC LIMIT 20;
            ;
            ";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                // Bind parameters if we're doing a search
                if (!empty($search_params)) {
                    $stmt->bind_param("ss", ...$search_params);
                }
                
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $has_offer = ($offer_active && $row['original_price'] > 0 && $row['price'] < $row['original_price']);
                            ?>
                            <div class="product-card">
                                <a href="product_details.php?product_id=<?php echo urlencode($row['product_id']); ?>">
                                    <img src="<?php echo htmlspecialchars($row['image_url'] ?? 'img/default.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($row['description']); ?>">
                                </a>

                                <p><?php echo htmlspecialchars($row['name'] ?? 'No Name Available'); ?></p>

                                <h3>
                                    <a href="product_details.php?product_id=<?php echo urlencode($row['product_id']); ?>">
                                        <?php echo htmlspecialchars($row['description']); ?>
                                    </a>
                                </h3>

                                <div class="price-container">
                                    <?php if ($has_offer): ?>
                                        <span class="original-price">Ksh <?php echo number_format($row['original_price'], 2); ?></span>
                                        <span class="current-price">Ksh <?php echo number_format($row['price'], 2); ?></span>
                                        <div class="discount-tag">10% OFF</div>
                                    <?php else: ?>
                                        <span class="current-price">Ksh <?php echo number_format($row['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>

                                <a href="index.php?add=<?php echo urlencode($row['product_id']); ?>" class="btn-add-cart">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No products found" . (isset($_GET['query']) ? " matching your search" : "") . ".</p>";
                    }
                } else {
                    echo "<p>Error executing query. Please try again later.</p>";
                }
                $stmt->close();
            } else {
                echo "<p>Error preparing query. Please try again later.</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <p><i class="fas fa-copyright"></i> <?php echo date("Y"); ?> Lightning Bolt Electronics. All Rights Reserved.</p>
        <p><a href="terms&privacy.php"><i class="fas fa-file-contract"></i> Terms & Privacy</a></p>
        <div class="social-links">
            <a href="https://twitter.com/YourHandle" target="_blank" class="social-icon">
                <i class="fab fa-twitter"></i> Twitter
            </a>
            <a href="https://facebook.com/YourPage" target="_blank" class="social-icon">
                <i class="fab fa-facebook"></i> Facebook
            </a>
            <a href="https://instagram.com/YourHandle" target="_blank" class="social-icon">
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