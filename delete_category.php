<?php
include('db_connect.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Category ID.");
}

$category_id = $_GET['id'];

// Delete category query
$stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);

if ($stmt->execute()) {
    echo "<p style='color:green;'>Category deleted successfully!</p>";
} else {
    echo "<p style='color:red;'>Error deleting category.</p>";
}

$stmt->close();
?>

<a href="manage_categories.php">Back to Manage Categories</a>
