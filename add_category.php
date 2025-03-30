<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['name']);

    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Category added successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error adding category.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>Category name cannot be empty.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Category</title>
</head>
<body>
    <h2>Add New Category</h2>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Enter category name" required>
        <input type="submit" value="Add Category">
    </form>
    <br>
    <a href="manage_categories.php">Back to Manage Categories</a>
</body>
</html>
