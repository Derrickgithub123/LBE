<?php
session_start();
include("config.php"); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['review_text']; // Use the correct column name
    $user_id = $_SESSION['user_id'] ?? 1; // Ensure user is logged in, or use a default user ID

    // Validate inputs
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        die("Invalid rating.");
    }

    $query = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiis", $product_id, $user_id, $rating, $comment);

    if (mysqli_stmt_execute($stmt)) {
        echo "Review submitted successfully!";
    } else {
        echo "Error submitting review: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
