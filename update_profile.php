<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION["user_id"];
$first_name = trim($_POST["first_name"]);
$last_name = trim($_POST["last_name"]);
$password = trim($_POST["password"]);

// Validate inputs
if (empty($first_name) || empty($last_name)) {
    die("Error: First name and last name are required.");
}

// Update user info in the database
if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_ARGON2ID);
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, password_hash = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $first_name, $last_name, $password_hash, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $first_name, $last_name, $user_id);
}

// Execute update
if ($stmt->execute()) {
    // Update session variables to reflect new changes
    $_SESSION["first_name"] = $first_name;
    $_SESSION["last_name"] = $last_name;

    header("Location: profile.php");
    exit();
} else {
    echo "Error updating profile.";
}

$stmt->close();
$conn->close();
?>
