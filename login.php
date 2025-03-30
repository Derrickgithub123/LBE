<?php
session_start();
include 'db_connect.php';

// ✅ Secure login attempt tracking
$max_attempts = 5;
$lockout_time = 15 * 60; // 15 minutes

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contact = trim($_POST["email_or_phone"]);  // Fix: Use the correct name for contact
    $password = trim($_POST["password"]);
    $remember_me = isset($_POST["remember_me"]); // Checkbox for remembering user

    if (empty($contact) || empty($password)) {
        die("Error: Email/Phone and password are required.");
    }

    // ✅ Get user data
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, phone_number, password_hash, role, failed_attempts, lockout_until 
                            FROM users WHERE email = ? OR phone_number = ?");
    $stmt->bind_param("ss", $contact, $contact);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        die("Error: User not found.");
    }

    // ✅ Check if account is locked
    if ($user["failed_attempts"] >= $max_attempts && strtotime($user["lockout_until"]) > time()) {
        die("Error: Too many failed attempts. Try again later.");
    }

    // ✅ Verify password
    if (password_verify($password, $user["password_hash"])) {
        // ✅ Reset failed attempts
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_until = NULL WHERE user_id = ?");
        $stmt->bind_param("i", $user["user_id"]);
        $stmt->execute();
        $stmt->close();

        // ✅ Set secure session variables
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["first_name"] = $user["first_name"];
        $_SESSION["last_name"] = $user["last_name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["phone_number"] = $user["phone_number"];

        // ✅ Store the correct contact (email or phone) based on what the user used
        $_SESSION["contact"] = filter_var($contact, FILTER_VALIDATE_EMAIL) ? $user["email"] : $user["phone_number"];

        $_SESSION["role"] = $user["role"];
        $_SESSION["logged_in"] = true;

        // ✅ Prevent session fixation
        session_regenerate_id(true);

        // ✅ Remember Me - Store user in cookie for 7 days
        if ($remember_me) {
            $token = bin2hex(random_bytes(32)); // Generate secure token
            setcookie("remember_me", $token, time() + (7 * 24 * 60 * 60), "/", "", true, true); // Secure HTTP-only cookie

            // Save token in the database
            $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE user_id = ?");
            $stmt->bind_param("si", $token, $user["user_id"]);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: shop.php");
        exit();
    } else {
        // ❌ Incorrect password → Increase failed attempts
        $failed_attempts = $user["failed_attempts"] + 1;
        $lockout_until = ($failed_attempts >= $max_attempts) ? date("Y-m-d H:i:s", time() + $lockout_time) : NULL;

        $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, lockout_until = ? WHERE user_id = ?");
        $stmt->bind_param("isi", $failed_attempts, $lockout_until, $user["user_id"]);
        $stmt->execute();
        $stmt->close();

        die("Error: Incorrect password.");
    }
}
?>
