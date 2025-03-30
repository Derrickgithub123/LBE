<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

include 'db_connect.php';

// Fetch user details
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
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
    <title>Edit Profile</title>
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
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
            cursor: pointer;
        }
        .btn-save {
            background-color: #28a745;
        }
        .btn-save:hover {
            background-color: #218838;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-user-pen"></i> Edit Profile</h2>
        <form action="update_profile.php" method="post">
            <label><i class="fa-solid fa-user"></i> First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user["first_name"]); ?>" required>
            <br>

            <label><i class="fa-solid fa-user"></i> Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user["last_name"]); ?>" required>
            <br>

            <label><i class="fa-solid fa-lock"></i> New Password (optional):</label>
            <input type="password" name="password">
            <br>

            <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        </form>

        <a href="profile.php" class="btn btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Profile</a>
    </div>
</body>
</html>
