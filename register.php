<?php 
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = !empty(trim($_POST["email"])) ? trim($_POST["email"]) : NULL;
    $phone = !empty(trim($_POST["phone"])) ? trim($_POST["phone"]) : NULL;
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (empty($first_name) || empty($last_name) || empty($password) || empty($confirm_password)) {
        die("Error: All fields are required.");
    }
    if (!$email && !$phone) {
        die("Error: Either email or phone number is required.");
    }
    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    // ✅ **Strong Password Validation**
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        die("Error: Password must be at least 8 characters, contain an uppercase letter, a number, and a special character.");
    }

    // Validate email if provided
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }

    // Validate phone if provided
    if ($phone && !preg_match("/^[0-9]{10,15}$/", $phone)) {
        die("Error: Invalid phone number format.");
    }

    // ✅ **Hash Password Securely**
    $password_hash = password_hash($password, PASSWORD_ARGON2ID); // More secure than BCRYPT

    // Check for duplicates
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR phone_number = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Error: User already exists.");
    }
    $stmt->close();

    // ✅ **Insert Securely**
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $password_hash);

    if ($stmt->execute()) {
        // ✅ **Store user data for Firebase**
        $_SESSION["register_email"] = $email;
        $_SESSION["register_phone"] = $phone;
        $_SESSION["register_password"] = $password;

        // Redirect to Firebase registration page
        header("Location: firebase_register.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
