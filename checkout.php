<?php
// Start session securely
if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'use_strict_mode' => true
    ]);
}

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Database connection
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

// Kenyan counties array
$kenyanCounties = [
    'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo-Marakwet',
    'Embu', 'Garissa', 'Homa Bay', 'Isiolo', 'Kajiado',
    'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga',
    'Kisii', 'Kisumu', 'Kitui', 'Kwale', 'Laikipia',
    'Lamu', 'Machakos', 'Makueni', 'Mandera', 'Meru',
    'Migori', 'Marsabit', 'Mombasa', 'Murang\'a', 'Nairobi',
    'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua',
    'Nyeri', 'Samburu', 'Siaya', 'Taita-Taveta', 'Tana River',
    'Tharaka-Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu',
    'Vihiga', 'Wajir', 'West Pokot'
];

// Calculate totals
$total = 0.00;
$cart_items = [];

foreach ($_SESSION['cart'] as $product_id => $item) {
    try {
        $product_id = (int)$product_id;
        if ($product_id <= 0) continue;
        
        $stmt = $pdo->prepare("SELECT product_id, name, price, stock_quantity FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            $price = filter_var($product['price'], FILTER_VALIDATE_FLOAT);
            if ($price === false) continue;
            
            $quantity = isset($item['quantity']) ? max(1, (int)$item['quantity']) : 1;
            $subtotal = round($price * $quantity, 2);
            $total = round($total + $subtotal, 2);
            
            $cart_items[$product_id] = [
                'product_id' => $product_id,
                'name' => sanitizeInput($product['name']),
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'stock_quantity' => max(0, (int)($product['stock_quantity'] ?? 0))
            ];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        continue;
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // Validate inputs
    $required_fields = [
        'first_name', 'last_name', 'email', 
        'phone', 'address', 'county', 
        'payment_method'
    ];
    
    $errors = [];
    $customer_data = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        } else {
            $customer_data[$field] = sanitizeInput($_POST[$field]);
        }
    }
    
    // Validate email
    if (!filter_var($customer_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }
    
    // Validate phone
    if (!preg_match('/^[0-9\+\-\s]{10,15}$/', $customer_data['phone'])) {
        $errors[] = "Invalid phone number format";
    }
    
    // Validate county
    if (!in_array($customer_data['county'], $kenyanCounties)) {
        $errors[] = "Please select a valid Kenyan county";
    }

    // Process order if no errors
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Insert order
            $stmt = $pdo->prepare("
                INSERT INTO orders (
                    first_name, last_name, email, phone, 
                    address, county, payment_method, 
                    total_amount, order_status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([
                $customer_data['first_name'],
                $customer_data['last_name'],
                $customer_data['email'],
                $customer_data['phone'],
                $customer_data['address'],
                $customer_data['county'],
                $customer_data['payment_method'],
                $total
            ]);
            
            $order_id = $pdo->lastInsertId();
            
            // Insert order items
            $stmt = $pdo->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_name, 
                    quantity, unit_price, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($cart_items as $item) {
                $stmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $item['subtotal']
                ]);
                
                // Update stock
                $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?")
                   ->execute([$item['quantity'], $item['product_id']]);
            }
            
            $pdo->commit();
            
            // Clear cart and redirect
            $_SESSION['cart'] = [];
            session_regenerate_id(true);
            header("Location: order_success.php?order_id=" . $order_id);
            exit();
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Order processing error: " . $e->getMessage());
            $errors[] = "Error processing your order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Complete Your Purchase</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .checkout-form, .order-summary {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2c3e50;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .payment-options {
            margin: 20px 0;
        }
        .payment-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .payment-option input {
            margin-right: 10px;
        }
        .payment-option.selected {
            border-color: #3498db;
            background-color: #f8f9fa;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }
        .btn-block {
            display: block;
            width: 100%;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .order-total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fdecea;
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <h3>Please fix the following errors:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="checkout-grid">
            <div class="checkout-form">
                <form method="post" action="checkout.php">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <h2>Customer Information</h2>
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?= isset($_POST['first_name']) ? $_POST['first_name'] : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required
                               value="<?= isset($_POST['last_name']) ? $_POST['last_name'] : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required
                               value="<?= isset($_POST['phone']) ? $_POST['phone'] : '' ?>"
                               placeholder="e.g. 0712345678">
                    </div>
                    
                    <h2>Shipping Address</h2>
                    <div class="form-group">
                        <label for="address">Street Address *</label>
                        <textarea id="address" name="address" rows="3" required><?= isset($_POST['address']) ? $_POST['address'] : '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="county">County *</label>
                        <select id="county" name="county" required>
                            <option value="">Select Your County</option>
                            <?php foreach ($kenyanCounties as $county): ?>
                                <option value="<?= $county ?>"
                                    <?= (isset($_POST['county']) && $_POST['county'] == $county) ? 'selected' : '' ?>>
                                    <?= $county ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <h2>Payment Method *</h2>
                    <div class="payment-options">
                        <label class="payment-option <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'mpesa') ? 'selected' : '' ?>">
                            <input type="radio" name="payment_method" value="mpesa" required 
                                   <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'mpesa') ? 'checked' : '' ?>>
                            M-Pesa
                        </label>
                        
                        
                        
                        <label class="payment-option <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cash') ? 'selected' : '' ?>">
                            <input type="radio" name="payment_method" value="cash"
                                   <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cash') ? 'checked' : '' ?>>
                            Cash on Delivery
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-block">Complete Order (KSH <?= number_format($total, 2) ?>)</button>
                </form>
            </div>
            
            <div class="order-summary">
                <h2>Your Order Summary</h2>
                
                <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <span><?= $item['name'] ?> × <?= $item['quantity'] ?></span>
                        <span>KSH <?= number_format($item['subtotal'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-total">
                    <span>Total:</span>
                    <span>KSH <?= number_format($total, 2) ?></span>
                </div>
                
                <div style="margin-top: 30px;">
                    <a href="cart.php" class="btn" style="background-color: #95a5a6; display: block; text-align: center;">
                        Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Highlight selected payment method
        document.querySelectorAll('.payment-option input').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.payment-option').forEach(option => {
                    option.classList.remove('selected');
                });
                
                if (this.checked) {
                    this.closest('.payment-option').classList.add('selected');
                }
            });
            
            // Initialize selected state on page load
            if (radio.checked) {
                radio.closest('.payment-option').classList.add('selected');
            }
        });
    </script>
</body>
</html>