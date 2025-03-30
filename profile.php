<?php 
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

// Prevent caching after logout
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

include 'db_connect.php';

// Fetch user details
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone_number FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
        .btn-logout {
            background-color: #dc3545;
        }
        .btn-logout:hover {
            background-color: #b02a37;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-user"></i> User Profile</h2>
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($user["first_name"]); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user["last_name"]); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($_SESSION["contact"]); ?></p>

        <a href="edit_profile.php" class="btn btn-edit"><i class="fa-solid fa-pen"></i> Edit Profile</a>
        <a href="logout.php" class="btn btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</body>
</html>
