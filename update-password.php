<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if passwords match
    if ($new_password !== $confirm_password) {
        header("Location: ../reset-password.html?error=password_mismatch");
        exit();
    }

    // 2. Verify token
    $stmt = $conn->prepare("SELECT id, reset_token, reset_expires FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 3. Check if token is expired
        if (strtotime($user['reset_expires']) < time()) {
            header("Location: ../reset-password.html?error=token_expired");
            exit();
        }

        // 4. Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // 5. Update password in database
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user['id']);

        if ($stmt->execute()) {
            // Redirect to login page after successful reset
            header("Location: ../login.html?status=password_reset_success");
            exit();
        } else {
            // Redirect to reset password page if update fails
            header("Location: ../reset-password.html?error=update_failed");
            exit();
        }
    } else {
        // Redirect to reset password page if token is invalid
        header("Location: ../reset-password.html?error=invalid_token");
        exit();
    }
}
?>