<?php
session_start();
header("Cache-Control: no-cache, must-revalidate"); // Forces reload
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Expired in the past
include 'db_connect.php'; // Include your database connection file

// Fetch products from the database
$sql = "SELECT products.*, categories.name AS category_name 
        FROM products 
        JOIN categories ON products.category_id = categories.category_id 
        ORDER BY category_name";

$result = $conn->query($sql);

// Initialize an array to store products by category
$products_by_category = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category_name']; // Use category name instead of ID
        $products_by_category[$category][] = $row;
    }
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
</head>
<style>
    body {
            background-color: #000;
            color: #fff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header */
        header {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
        }
/* Dropdown styling */
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

/* Show dropdown on hover */
.account-dropdown:hover .account-dropdown-content {
    display: block;
}


.products-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .product-card {
            background: #111;
            padding: 15px;
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
        color: #5E68E6; /* Dark blue */
        text-decoration: none;
        font-weight: bold;
    }
        .product-card p {
            color: #fff;
        }

        .product-card .btn {
            display: inline-block;
            background: white;
            color: black;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        /* Footer */
        footer {
            background: #222;
            color: #fff;
            text-align: center;
            padding: 15px;
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer ul li {
            display: inline;
            margin: 0 10px;
        }

        footer ul li a {
            color: white;
            text-decoration: none;
        }

        .social-icons a {
            color: white;
            font-size: 20px;
            margin: 0 10px;
        }
        #searchform {
      position: absolute;
      top: 10px;
      right: 40px;
      display: flex;
      align-items: center;
      background-color: white;
      padding: 5px;
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
  
  .magnifier {
      font-size: 18px;
      color: black;
      margin-left: 10px;
  }
  
    .cart-icon{
      position: absolute;
      top: 10px;
      right: 500px;
      display: inline-block;
    }
    .cart-icon img{
      width: 40px;
    }
    .cart-count{
      position: absolute;
      top: -10px;
      right: -10px;
      background: red;
      color: white;
      border-radius: 50%;
      padding: 5px 8px;
      font-size: 14px;
    }
  .cart{
    border: 1px solid #000;
    padding: 10px;
    margin-top: 20px;
   
  }
  #cart-items{
    list-style-type: none;
    padding: 0;
    font-weight: bold;
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
.btn-add-cart {
        display: block;
        margin: 10px auto; /* Centers the button */
        background-color: red;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        text-align: center;
        transition: 0.3s;
    }

    .btn-add-cart:hover {
        background-color: #218838;
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
    </style>
<body>

    <section>
        <header><i class="fas fa-shopping-bag"></i> Shop</header>
      
      

<nav>
    <ul>
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="shop.php"><i class="fas fa-store"></i> Shop</a></li>
        <li><a href="contact.html"><i class="fas fa-phone-alt"></i> Contact</a></li>
        <li><a href="about.html"><i class="fas fa-info-circle"></i> About</a></li>

        <li>
            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <?php else: ?>
                <a href="login.html"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php endif; ?>
        </li>
    </ul>
</nav>


        <form id="searchform">
            <div class="search-wrapper">
                <input type="text" id="searchinput" placeholder="Search product...">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>

        <div class="cart-icon">
            <a href="cart.html">
                <img src="img/cart.jpg.jpg" alt="Cart">
                <span class="cart-count">0</span>
            </a>
        </div>
    </section>

    <div class="cart-container">
        <button class="view-cart" onclick="window.location.href='cart.html'">View Cart</button>
    </div>

    <aside class="filter-sidebar">
        <h3>Filters</h3>
        <form id="filter-form">
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="all">All</option>
                <option value="smartphones">Smartphones</option>
                <option value="laptops">Laptops</option>
                <option value="appliances">Large Appliances</option>
                <option value="appliances">Other Large Appliances</option>
                <option value="appliances">Small Appliances</option>
                <option value="security">Security Devices</option>
                <option value="cameras">Cameras</option>
                <option value="networking">Networking Devices</option>
                <option value="computing">Computing Devices</option>
                <option value="accessories">Electrical Accessories</option>
            </select>

            <label for="price-range">Price Range:</label>
            <input type="range" id="price-range" min="0" max="100000" step="1000" value="50000">
            <span id="price-label">Ksh 50,000</span>

            <label for="sort-by">Sort By:</label>
            <select id="sort-by">
                <option value="name-asc">Name: A-Z</option>
                <option value="name-desc">Name: Z-A</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
            </select>
        </form>
    </aside>

    <div class="shop-container">
        <p>Powering Your World, One Device at a Time!</p>
    </div>

    <section class="products-section">
    <?php foreach ($products_by_category as $category_name => $products) : ?>
        <h2><?php echo htmlspecialchars($category_name); ?></h2>
        <div class="product-grid">
            <?php foreach ($products as $product) : ?>
                <div class="product-card">
                    <!-- Product Image & Link -->
                    <a href="product_details.php?product_id=<?php echo urlencode($product['product_id']); ?>">
                        <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'img/default.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </a>

                    <!-- Product Name (No Link) -->
                    <p><?php echo htmlspecialchars($product['name'] ?? 'No Name Available'); ?></p>

                    <!-- Product Description (Now a Clickable Link) -->
                    <h3>
    <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>">
        <?php echo htmlspecialchars($product['description'] ?? 'No description available'); ?>
    </a>
</h3>


                    <!-- Product Price -->
                    <p><strong>Price: Ksh <?php echo number_format($product['price'], 2); ?></strong></p>

                    <!-- Add to Cart Button -->
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                        <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</section>

    <footer>
        <p>&copy; <span id="current-year"></span> Lightning Bolt Electronics. All Rights Reserved.</p>
        <ul>
            <li><a href="terms.html"><i class="fas fa-file-alt"></i> Terms</a></li>
            <li><a href="privacy.html"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
        </ul>
        <div class="social-links">
                <a href="https://twitter.com/YourHandle" target="_blank" class="social-icon">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
                <a href="https://youtube.com/YourChannel" target="_blank" class="social-icon">
                    <i class="fab fa-youtube"></i> YouTube
                </a>
                <a href="https://facebook.com/YourPage" target="_blank" class="social-icon">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a href="https://instagram.com/YourHandle" target="_blank" class="social-icon">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
            </div>
        </section>
        
    </footer>

    <script>
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>

</body>
</html>
