<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

// Determine which contact detail the user registered with
$contact_info = !empty($_SESSION["email"]) ? $_SESSION["email"] : $_SESSION["phone_number"];
$contact_label = !empty($_SESSION["email"]) ? "Email" : "Phone";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Lightning Bolt</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .account-container {
            background: #222;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            margin: auto;
            text-align: left;
        }
        .account-container h2 {
            text-align: center;
        }
        .account-info p {
            font-size: 18px;
            margin: 10px 0;
        }
        .logout-btn {
            display: block;
            background-color: red;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 15px;
            text-align: center;
        }
        .logout-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<div class="account-container">
    <h2>My Account</h2>
    <div class="account-info">
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($_SESSION["first_name"]); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($_SESSION["last_name"]); ?></p>
        <p><strong><?php echo $contact_label; ?>:</strong> <?php echo htmlspecialchars($contact_info); ?></p>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
