<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentYear = date("Y");

// Fetch terms
$terms = $conn->query("SELECT section, content FROM policies WHERE category='terms'");

// Fetch privacy policy
$privacy = $conn->query("SELECT section, content FROM policies WHERE category='privacy'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Privacy - Lightning Bolt Electronics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #111;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            border-bottom: 2px solid #fff;
        }
        main {
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }
        section {
            margin-bottom: 10px;
            padding: 10px;
            background: #222;
            border-radius: 5px;
        }
        h2 {
            color: #f4c542;
            cursor: pointer;
            padding: 10px;
            background: #333;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            padding: 0 10px;
            background: #111;
            border-radius: 5px;
        }
        .content p {
            padding: 10px;
            color: #ccc;
        }
    </style>
</head>
<body>

    <header><i class="fas fa-file-contract"></i> Terms & Conditions</header>
    <main>
        <?php while ($row = $terms->fetch_assoc()) { ?>
            <section>
                <h2 onclick="toggleContent(this)"><?php echo $row['section']; ?> <i class="fas fa-chevron-down"></i></h2>
                <div class="content">
                    <p><?php echo $row['content']; ?></p>
                </div>
            </section>
        <?php } ?>

        <header><i class="fas fa-shield-alt"></i> Privacy Policy</header>
        
        <?php while ($row = $privacy->fetch_assoc()) { ?>
            <section>
                <h2 onclick="toggleContent(this)"><?php echo $row['section']; ?> <i class="fas fa-chevron-down"></i></h2>
                <div class="content">
                    <p><?php echo $row['content']; ?></p>
                </div>
            </section>
        <?php } ?>
    </main>

    <footer>
        <p>&copy; <?php echo $currentYear; ?> Lightning Bolt Electronics. All Rights Reserved.</p>
    </footer>

    <script>
        function toggleContent(header) {
            var content = header.nextElementSibling;
            content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
        }
    </script>

</body>
</html>
<?php
$conn->close();
?>
