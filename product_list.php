<?php
session_start();
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit();
}

// Fetch all products
$query = "SELECT product_id, name, price FROM products";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Products</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; border-radius: 3px; }
        .edit-btn { background-color: #28a745; }
        .delete-btn { background-color: #dc3545; }
    </style>
</head>
<body>
    <h2>Manage Products</h2>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row["name"]); ?></td>
            <td><?php echo htmlspecialchars($row["price"]); ?></td>
            <td>
                <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn edit-btn">Edit</a>
                <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn delete-btn">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
