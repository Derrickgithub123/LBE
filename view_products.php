<?php
include('db_connect.php');

$result = $conn->query("SELECT * FROM products");

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Manage Products</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 20px; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #007BFF; color: white; text-transform: uppercase; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #f1f1f1; }
        .btn-container { display: flex; justify-content: center; gap: 10px; }
        .btn { padding: 8px 14px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 14px;
               color: white; display: inline-block; }
        .edit-btn { background: black; }
        .delete-btn { background: black; }
        .delete-btn:hover { background: red; }
        .edit-btn:hover { background: green; }
        .add-btn { display: inline-block; background: #007BFF; color: white; padding: 12px 18px; margin: 20px auto;
                   border-radius: 5px; text-decoration: none; font-size: 16px; }
        img { width: 50px; height: auto; border-radius: 5px; }
        .out-of-stock { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h2>Manage Products</h2>

<table>
    <tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Image</th>
        <th>Description</th>
        <th>Price (KES)</th>
        <th>Category ID</th>
        <th>Stock Quantity</th>
        <th>Actions</th>
    </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['product_id'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td><img src='" . $row['image_url'] . "'></td>";
    echo "<td>" . $row['description'] . "</td>";
    echo "<td>KES " . number_format($row['price'], 2) . "</td>";
    echo "<td>" . $row['category_id'] . "</td>";
    
    // Show stock or "Out of Stock"
    if ($row['stock_quantity'] > 0) {
        echo "<td>" . $row['stock_quantity'] . "</td>";
    } else {
        echo "<td class='out-of-stock'>Out of Stock</td>";
    }

    echo "<td>
        <div class='btn-container'>
            <a href='edit_product.php?id=" . $row['product_id'] . "' class='btn edit-btn'>Edit</a> 
            <a href='delete_product.php?id=" . $row['product_id'] . "' class='btn delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
        </div>
    </td>";
    echo "</tr>";
}
echo "</table>";

?>
<a href="add_product.php" class="add-btn">Add New Product</a>

</body>
</html>
