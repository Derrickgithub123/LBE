<?php
session_start();

// If the user is not logged in as an admin, redirect to the login page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        .dashboard-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-links a {
            display: block;
            padding: 15px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            width: 200px;
            text-align: center;
            font-size: 16px;
            transition: 0.3s;
        }
      
        .view-products { background-color: #28a745; }
        .view-products:hover { background-color: #1e7e34; }
        
        .manage-reviews { background-color: #fd7e14; color: white; }
        .manage-reviews:hover { background-color: #e6690e; }
        
        .manage-categories { background-color: #ffc107; color: black; }
        .manage-categories:hover { background-color: #d39e00; }
        
        .manage-offers { background-color: #007bff; }
        .manage-offers:hover { background-color: #0056b3; }
        
        .logout { background-color: #343a40; }
        .logout:hover { background-color: #23272b; }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-user-shield"></i> Admin Dashboard</h2>
        <p>Welcome, Admin! Manage all product-related tasks here.</p>

        <div class="dashboard-links">
            <a href="view_products.php" class="view-products"><i class="fas fa-eye"></i> View Products</a>
            <a href="manage_categories.php" class="manage-categories"><i class="fas fa-list"></i> Manage Categories</a>
            <a href="admin_manage_reviews.php" class="manage-reviews"><i class="fas fa-star"></i> Manage Reviews</a>
            <a href="admin_offers.php" class="manage-offers"><i class="fas fa-tags"></i> Manage Offers</a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</body>
</html>
