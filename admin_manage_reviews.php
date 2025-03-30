
<?php
session_start();
include("config.php");

// Fetch all reviews
$query = "
    SELECT reviews.review_id, reviews.product_id, reviews.user_id, reviews.rating, reviews.comment, 
           products.name AS product_name, users.first_name, users.last_name 
    FROM reviews
    JOIN products ON reviews.product_id = products.product_id
    JOIN users ON reviews.user_id = users.user_id
    ORDER BY reviews.review_id DESC";
$result = mysqli_query($conn, $query);

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_review'])) {
    $review_id = $_POST['review_id'];
    $delete_query = "DELETE FROM reviews WHERE review_id = $review_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Review deleted successfully!'); window.location.href='admin_manage_reviews.php';</script>";
    } else {
        echo "<script>alert('Error deleting review');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .reviews-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .reviews-table th, .reviews-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .reviews-table th {
            background-color: #007bff;
            color: white;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #b02a37;
        }
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background-color: #1e7e34;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Manage Product Reviews</h1>

    <table class="reviews-table">
        <tr>
            <th>Review ID</th>
            <th>Product</th>
            <th>User</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Action</th>
        </tr>

        <?php while ($review = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?php echo $review['review_id']; ?></td>
                <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                <td><?php echo htmlspecialchars($review['first_name'] . " " . $review['last_name']); ?></td>
                <td>⭐ <?php echo $review['rating']; ?>/5</td>
                <td><?php echo htmlspecialchars($review['comment']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                        <button type="submit" name="delete_review" class="delete-btn">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Admin Panel</a>
</div>

</body>
</html>
