<?php
include('db_connect.php'); // Include database connection

?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Manage Categories</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 20px; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #007BFF; color: white; text-transform: uppercase; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #f1f1f1; }
        .btn { padding: 8px 14px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 14px;
               color: white; display: inline-block; }
        .edit-btn { background: black; }
        .delete-btn { background: black; }
        .delete-btn:hover { background: red; }
        .edit-btn:hover { background: green; }
        .add-btn { display: inline-block; background: #007BFF; color: white; padding: 12px 18px; margin: 20px auto;
                   border-radius: 5px; text-decoration: none; font-size: 16px; }
        form { margin: 20px auto; width: 50%; }
        input[type='text'] { padding: 10px; width: 80%; border: 1px solid #ddd; border-radius: 5px; }
        input[type='submit'] { padding: 10px 15px; border: none; border-radius: 5px; background: #007BFF; color: white; cursor: pointer; }
    </style>
</head>
<body>

<h2>Manage Categories</h2>

<!-- Search Category -->
<form method='POST' action='manage_categories.php'>
    <input type='text' name='category_id' placeholder='Enter Category ID'>
    <input type='submit' value='Search'>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_id']) && !empty($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>Category found: " . $result->fetch_assoc()['name'] . "</p>";
    } else {
        echo "<p style='color: red;'>No category found with that ID!</p>";
    }
    $stmt->close();
}
?>

<!-- Display All Categories -->
<table>
    <tr>
        <th>Category ID</th>
        <th>Category Name</th>
        <th>Actions</th>
    </tr>

    <?php
    $result = $conn->query("SELECT * FROM categories");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['category_id'] . "</td>
                <td>" . $row['name'] . "</td>
                <td>
                    <a href='edit_category.php?id=" . $row['category_id'] . "' class='btn edit-btn'>Edit</a>
                    <a href='delete_category.php?id=" . $row['category_id'] . "' class='btn delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                </td>
              </tr>";
    }
    ?>
</table>

<a href="add_category.php" class="add-btn">Add New Category</a>

</body>
</html>
