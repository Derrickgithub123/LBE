<?php
session_start();

// Check if the user is already logged in as admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin.php");  // Redirect to admin panel if already logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('db_connect.php'); // Include your DB connection file

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a query to check the admin credentials
    $stmt = $conn->prepare("SELECT * FROM admins WHERE LOWER(email) = LOWER(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Admin exists, fetch data
        $admin = $result->fetch_assoc();

        // Check if password matches
        if (password_verify($password, $admin['password_hash'])) {
            // If admin is found, log them in and redirect to admin_dashboard.php
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['role'] = 'admin';
            header("Location: admin.php"); // Redirect to admin panel
            exit();
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "No admin found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lightning Bolt Electronics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(255, 255, 255, 0.2);
            text-align: center;
            width: 350px;
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .input-group {
            position: relative;
            width: 100%;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: white;
        }

        input::placeholder {
            color: #aaa;
        }

        /* View/Hide Password Icon */
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #bbb;
        }

        /* Buttons */
        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: red;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #d32f2f;
        }

        /* Register Link */
        .register-section {
            margin-top: 15px;
        }

        .register-link {
            color: #ff5252;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>

        <form action="admin_login.php" method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>
        
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
                <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
            </div>
        
            <button class="login-btn" type="submit">Login</button>
        </form>

        <?php if(isset($error_message)) { echo "<p style='color: red;'>$error_message</p>"; } ?>
    </div>

    <script>
        function togglePassword() {
            let passwordField = document.getElementById("password");
            let icon = document.querySelector(".toggle-password");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>
</body>
</html>
