<?php
session_start();
include 'db_connect.php';

// ✅ Destroy session
$_SESSION = [];
session_unset();
session_destroy();

// ✅ Clear "Remember Me" cookie
if (isset($_COOKIE["remember_me"])) {
    setcookie("remember_me", "", time() - 3600, "/", "", true, true); // Expire cookie
}

// ✅ Remove remember token from the database
if (isset($_SESSION["user_id"])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->close();
}

// ✅ Redirect to login page
header("Location: index.php");
exit();
?>
