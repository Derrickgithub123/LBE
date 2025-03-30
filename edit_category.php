<?php
include('db_connect.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Category ID.");
}

$category_id = $_GET['id'];
$category_name = "";

// Fetch category details
$stmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $category = $result->fetch_assoc();
    $category_name = $category['name'];
} else {
    die("Category not found.");
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_category_name = trim($_POST['name']);

    if (!empty($new_category_name)) {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $new_category_name, $category_id);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Category updated successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error updating category.</p>";
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
    <title>Edit Category</title>
</head>
<body>
    <h2>Edit Category</h2>
    <form method="POST" action="">
        <input type="text" name="name" value="<?php echo htmlspecialchars($category_name); ?>" required>
        <input type="submit" value="Update Category">
    </form>
    <br>
    <a href="manage_categories.php">Back to Manage Categories</a>
</body>
</html>
