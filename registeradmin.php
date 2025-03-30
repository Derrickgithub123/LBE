<?php
session_start();
require_once 'db_connect.php'; // Using the updated connection file

// Initialize variables
$error = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $first_name = trim(filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING));
    $last_name = trim(filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
    $password = trim($_POST["password"]);

    // Validate inputs
    if (empty($first_name)) {
        $error = "First name is required";
    } elseif (empty($last_name)) {
        $error = "Last name is required";
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Valid email is required";
    } elseif (empty($password) || strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Check if admin email exists with prepared statement
        $check_sql = "SELECT email FROM admins WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            $error = "Database error. Please try again later.";
            error_log("Prepare failed: " . $conn->error);
        } else {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $error = "Admin with this email already exists.";
            } else {
                // Insert new admin
                $insert_sql = "INSERT INTO admins (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                
                if ($insert_stmt === false) {
                    $error = "Database error. Please try again later.";
                    error_log("Prepare failed: " . $conn->error);
                } else {
                    $insert_stmt->bind_param("ssss", $first_name, $last_name, $email, $password_hash);
                    
                    if ($insert_stmt->execute()) {
                        $success = true;
                        $_SESSION['registration_success'] = true;
                        header("Location: admin_login.html");
                        exit();
                    } else {
                        $error = "Registration failed. Please try again.";
                        error_log("Execute failed: " . $insert_stmt->error);
                    }
                    $insert_stmt->close();
                }
            }
            $check_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Lightning Bolt Electronics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .input-group {
            margin-bottom: 15px;
            position: relative;
        }
        .input-group input {
            width: 90%;
            padding: 10px;
            padding-left: 35px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }
        .input-group i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #d4edda;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            font-size: 18px;
        }
        .toggle-password:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fas fa-user-shield"></i> Admin Registration</h2>

        <?php if (!empty($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> Admin registered successfully!
            </div>
        <?php endif; ?>

        <form action="registeradmin.php" method="POST" autocomplete="off">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" placeholder="First Name" 
                       value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" placeholder="Last Name" 
                       value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password (min 8 characters)" required>
                <i class="fas fa-eye toggle-password" id="toggle-password"></i>
            </div>
            <button type="submit">
                <i class="fas fa-user-plus"></i> Register Admin
            </button>
        </form>
    </div>

    <script>
        // Password toggle functionality
        document.getElementById('toggle-password').addEventListener('click', function() {
            const password = document.getElementById('password');
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>