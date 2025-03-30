<?php
// Start session securely
if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'use_strict_mode' => true
    ]);
}

// Initialize cart securely
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Database connection with improved security
$host = 'localhost';
$dbname = 'project';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("System temporarily unavailable. Please try again later.");
}

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Input validation function
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Handle actions with CSRF protection
if (isset($_GET['action'])) {
    $action = sanitizeInput($_GET['action']);
    
    switch ($action) {
        case 'add':
            if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
                $product_id = (int)$_GET['product_id'];
                $quantity = isset($_GET['quantity']) ? max(1, (int)$_GET['quantity']) : 1;
                
                // Prevent session fixation
                session_regenerate_id(true);
                
                if (!isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] = ['quantity' => $quantity];
                } else {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                }
            }
            header("Location: cart.php");
            exit();
            
        case 'remove':
            if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
                $product_id = (int)$_GET['product_id'];
                if (isset($_SESSION['cart'][$product_id])) {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
            header("Location: cart.php"); 
            exit();
            
        case 'clear':
            $_SESSION['cart'] = [];
            session_regenerate_id(true);
            header("Location: cart.php");
            exit();
            
        default:
            header("Location: cart.php");
            exit();
    }
}

// Handle form submissions with CSRF protection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    if (isset($_POST['update_cart'])) {
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $product_id => $quantity) {
                $product_id = (int)$product_id;
                $quantity = max(1, (int)$quantity);
                
                if (isset($_SESSION['cart'][$product_id])) {
                    if ($quantity > 0) {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                    } else {
                        unset($_SESSION['cart'][$product_id]);
                    }
                }
            }
        }
        header("Location: cart.php");
        exit();
    }
    
    if (isset($_POST['proceed_to_checkout'])) {
        header("Location: checkout.php");
        exit();
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Calculate totals with error handling
$total = 0.00;
$cart_items = [];

foreach ($_SESSION['cart'] as $product_id => $item) {
    try {
        // Validate product ID
        $product_id = (int)$product_id;
        if ($product_id <= 0) continue;
        
        // Get product details securely
        $stmt = $pdo->prepare("SELECT product_id, name, price, image_url, stock_quantity FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Validate and sanitize product data
            $price = filter_var($product['price'], FILTER_VALIDATE_FLOAT);
            if ($price === false) {
                error_log("Invalid price for product ID: $product_id");
                continue;
            }
            
            $quantity = isset($item['quantity']) ? max(1, (int)$item['quantity']) : 1;
            $subtotal = round($price * $quantity, 2);
            $total = round($total + $subtotal, 2);
            
            $cart_items[$product_id] = [
                'product_id' => $product_id,
                'name' => sanitizeInput($product['name']),
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'image' => $product['image_url'] ?? 'default.jpg',
                'stock_quantity' => max(0, (int)($product['stock_quantity'] ?? 0))
            ];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        continue;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <style>
         <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .cart-item {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .cart-item img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 20px;
        }
        .item-details {
            flex-grow: 1;
        }
        .item-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .item-price {
            color: #e67e22;
            font-weight: bold;
            margin: 10px 0;
        }
        .item-subtotal {
            color: #27ae60;
            font-weight: bold;
            margin: 10px 0;
        }
        .item-quantity {
            margin-bottom: 10px;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            text-align: center;
        }
        .actions {
            margin-top: 10px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-success {
            background-color: #2ecc71;
            color: white;
        }
        .btn-update {
            background-color: #f39c12;
            color: white;
        }
        .btn-clear {
            background-color: #9b59b6;
            color: white;
        }
        .cart-summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: right;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        .currency {
            font-weight: bold;
        }
    </style>
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="shop.php" class="btn btn-primary">Go Shopping</a>
            </div>
        <?php else: ?>
            <form method="post" action="cart.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <?php foreach ($cart_items as $product_id => $item): ?>
                    <div class="cart-item">
                        <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                        <div class="item-details">
                            <div class="item-name"><?= $item['name'] ?></div>
                            <div class="item-price">Price: <span class="currency">KSH <?= number_format($item['price'], 2) ?></span></div>
                            <div class="item-subtotal" id="subtotal-<?= $product_id ?>">
                                Subtotal: <span class="currency">KSH <?= number_format($item['subtotal'], 2) ?></span>
                            </div>
                            <input type="hidden" id="price-<?= $product_id ?>" value="<?= $item['price'] ?>">
                            <div class="item-quantity">
                                Quantity: 
                                <input type="number" 
                                       name="quantity[<?= $product_id ?>]" 
                                       class="quantity-input" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="1" 
                                       max="<?= $item['stock_quantity'] ?>"
                                       onchange="updateSubtotal(this, <?= $product_id ?>)">
                                <?php if ($item['quantity'] > $item['stock_quantity']): ?>
                                    <span class="error">(Only <?= $item['stock_quantity'] ?> available)</span>
                                <?php endif; ?>
                            </div>
                            <div class="actions">
                                <a href="cart.php?action=remove&product_id=<?= $product_id ?>" class="btn btn-danger">Delete from Cart</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="cart-summary">
                    <h3>Total: <span class="currency" id="grand-total">KSH <?= number_format($total, 2) ?></span></h3>
                    <a href="cart.php?action=clear" class="btn btn-clear">Delete All Items</a>
                    <button type="submit" name="update_cart" class="btn btn-update">Update Quantities</button>
                    <button type="submit" name="proceed_to_checkout" class="btn btn-success">Proceed to Checkout</button>
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });

        function updateSubtotal(input, productId) {
            const quantity = parseInt(input.value) || 1;
            const price = parseFloat(document.getElementById('price-' + productId).value) || 0;
            const subtotal = (quantity * price).toFixed(2);
            
            document.getElementById('subtotal-' + productId).innerHTML = 
                'Subtotal: <span class="currency">KSH ' + subtotal + '</span>';
            updateTotal();
        }

        function updateTotal() {
            let grandTotal = 0;
            document.querySelectorAll('[id^="subtotal-"]').forEach(el => {
                const subtotalText = el.querySelector('.currency').textContent;
                grandTotal += parseFloat(subtotalText.replace(/[^\d.-]/g, ''));
            });
            document.getElementById('grand-total').textContent = 'KSH ' + grandTotal.toFixed(2);
        }
    </script>
</body>
</html>