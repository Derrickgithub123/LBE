<?php
$currentYear = date("Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
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
            color:  #f4c542;
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
    </style>
</head>
<body>
    <header><i class="fas fa-info-circle"></i> About Us</header>
    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="shop.php"><i class="fas fa-store"></i> Shop</a></li>
            <li><a href="contact.php"><i class="fas fa-phone-alt"></i> Contact</a></li>
            <li><a href="faqs.php"><i class="fas fa-question-circle"></i> FAQs</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2 onclick="toggleContent(this)"><i class="fas fa-users"></i> Who Are We <i class="fas fa-chevron-down"></i></h2>
            <div class="content">
                <p>Lightning Bolt Electronics is an online store that sells engineering electronic components, such as CCTV cameras, televisions, smartphones, computers, laptops, woofers, robot kits, lasers, and biometrics.</p>
            </div>
        </section>
        <section>
            <h2 onclick="toggleContent(this)"><i class="fas fa-eye"></i> Vision Statement <i class="fas fa-chevron-down"></i></h2>
            <div class="content">
                <p>To be one of the leading providers of automation and electrical control system solutions globally.</p>
            </div>
        </section>
        <section>
            <h2 onclick="toggleContent(this)"><i class="fas fa-bullhorn"></i> Our Mission <i class="fas fa-chevron-down"></i></h2>
            <div class="content">
                <ol>
                    <li>To provide quality service in both automation and electrical control solutions.</li>
                    <li>To offer cost-effective solutions coupled with high-quality service.</li>
                </ol>
            </div>
        </section>
        <section>
            <h2 onclick="toggleContent(this)"><i class="fas fa-handshake"></i> Core Values <i class="fas fa-chevron-down"></i></h2>
            <div class="content">
                <ol>
                    <li>Client First</li>
                    <li>Teamwork</li>
                    <li>Passion</li>
                    <li>Integrity</li>
                    <li>Excellence</li>
                </ol>
            </div>
        </section>
    </main>
    <footer>
    <p><i class="fas fa-copyright"></i> <?php echo date("Y"); ?> Lightning Bolt Electronics. All Rights Reserved.</p>
    <p><a href="terms&privacy.php"><i class="fas fa-file-contract"></i> Terms & Privacy</a></p>
    <div class="social-links">
            <a href="https://twitter.com/YourHandle" target="_blank" class="social-icon">
                <i class="fab fa-twitter"></i> Twitter
            </a>
            <a href="https://youtube.com/YourChannel" target="_blank" class="social-icon">
                <i class="fab fa-youtube"></i> YouTube
            </a>
            <a href="https://facebook.com/YourPage" target="_blank" class="social-icon">
                <i class="fab fa-facebook"></i> Facebook
            </a>
            <a href="https://instagram.com/YourHandle" target="_blank" class="social-icon">
                <i class="fab fa-instagram"></i> Instagram
            </a>
        </div>
    </footer>
    <script>
        function toggleContent(element) {
            var content = element.nextElementSibling;
            content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
        }
    </script>
</body>
</html>