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

// Fetch FAQs from the database
$sql = "SELECT question, answer FROM faqs ORDER BY id ASC";
$result = $conn->query($sql);

$currentYear = date("Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>FAQs - Lightning Bolt Electronics</title>
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
        nav ul {
            list-style: none;
            padding: 0;
            background: #222;
            overflow: hidden;
            text-align: center;
        }
        nav ul li {
            display: inline;
            padding: 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
        }
        .faq-search {
            text-align: center;
            margin: 20px;
        }
        .faq-search input {
            width: 40%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background: #222;
            color: white;
        }
        .faq-search input:focus {
            outline: none;
            border: 2px solid #555;
        }
        .faq-search button {
            padding: 10px;
            background: #f4c542;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-left: 5px;
        }
        .faq-search button i {
            color: #000;
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
        h3 {
            color:  #f4c542;
            cursor: pointer;
            padding: 10px;
            background: #333;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            padding: 0 10px;
            background: #111;
            border-radius: 5px;
        }
        .faq-content ul, .faq-content p {
            padding: 10px;
        }
        footer {
    background-color: #111;
    text-align: center;
    padding: 15px;
    color: #fff;
    font-size: 14px;
    border-top: 2px solid #444;
}

footer p {
    margin: 5px 0;
}

footer a {
    color: #f4c542;
    text-decoration: none; /* Removes underline */
    font-weight: bold;
    transition: color 0.3s ease-in-out;
}

footer a:hover {
    color: #ffcc00; /* Changes color on hover */
}

        .social-links {
            margin-top: 10px;
        }
        .social-links a {
            color: #fff;
            margin: 0 15px;
            font-size: 18px;
            transition: color 0.3s;
            text-decoration: none;
        }
        .social-links a:hover {
            color: #f4c542;
        }
        .dark-mode {
            background-color: #fff;
            color: #000;
        }
        .toggle-dark-mode {
            cursor: pointer;
            padding: 10px;
            background: #444;
            border: none;
            color: #fff;
            border-radius: 5px;
            display: block;
            margin: 10px auto;
        }
    </style>
</head>
<body>
    <header><i class="fas fa-question-circle"></i> FAQs</header>
    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="shop.php"><i class="fas fa-store"></i> Shop</a></li>
            <li><a href="contact.php"><i class="fas fa-phone-alt"></i> Contact</a></li>
            <li><a href="faqs.php"><i class="fas fa-question-circle"></i> FAQs</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
            
        </ul>
    </nav>

    <button class="toggle-dark-mode" onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <div class="faq-search">
        <input type="text" id="faq-search" placeholder="Search FAQs..." onkeyup="searchFAQs()">
        <button><i class="fas fa-search"></i></button>
    </div>

    <main id="faq-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<section class="faq-item">';
                echo '<h3 onclick="toggleFAQ(this)"><i class="fas fa-question-circle"></i> ' . htmlspecialchars($row['question']) . ' <i class="fas fa-chevron-down"></i></h3>';
                echo '<div class="faq-content"><p>' . nl2br(htmlspecialchars($row['answer'])) . '</p></div>';
                echo '</section>';
            }
            
        } else {
            echo "<p style='text-align:center;'>No FAQs found.</p>";
        }
        ?>
    </main>
    <script>
        function toggleFAQ(event) {
            var content = event.nextElementSibling;
            content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
        }
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }
        function searchFAQs() {
            let input = document.getElementById('faq-search').value.toLowerCase();
            let faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                let question = item.querySelector('h3').textContent.toLowerCase();
                item.style.display = question.includes(input) ? 'block' : 'none';
            });
        }
    </script>
     <footer>
     <p><i class="fas fa-copyright"></i> <?php echo date("Y"); ?> Lightning Bolt Electronics. All Rights Reserved.</p>
     <p><a href="terms&privacy.php"><i class="fas fa-file-contract"></i> Terms & Privacy</a></p>
        <div class="social-links">
            <a href="https://twitter.com/YourHandle" target="_blank" class="social-icon"><i class="fab fa-twitter"></i> Twitter</a>
            <a href="https://youtube.com/YourChannel" target="_blank" class="social-icon"><i class="fab fa-youtube"></i> YouTube</a>
            <a href="https://facebook.com/YourPage" target="_blank" class="social-icon"><i class="fab fa-facebook"></i> Facebook</a>
            <a href="https://instagram.com/YourHandle" target="_blank" class="social-icon"><i class="fab fa-instagram"></i> Instagram</a>
        </div>
    </footer>

    <script>
        function toggleFAQ(event) {
            var content = event.nextElementSibling;
            content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
        }
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }
    </script>
    https://tawk.to/chat/67ded66dbbe8dd191bd18b72/1imv7b7tf
</body>
</html>
<?php
$conn->close();
?>