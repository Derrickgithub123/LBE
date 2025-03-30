<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        // Example: Save the message to a file (you can modify this to save in a database)
        $file = fopen("messages.txt", "a");
        fwrite($file, "Name: $name\nEmail: $email\nMessage: $message\n\n");
        fclose($file);

        $success_message = "Your message has been sent successfully!";
    } else {
        $error_message = "Please fill out all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="img/e.jpg">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Contact Us</title>
</head>
<body>
    <header><i class="fas fa-phone-alt"></i> Contacts</header>
    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="shop.php"><i class="fas fa-store"></i> Shop</a></li>
            <li><a href="contact.php"><i class="fas fa-phone-alt"></i> Contact Us</a></li>
            <li><a href="faqs.php"><i class="fas fa-question-circle"></i> FAQs</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
        </ul>
    </nav>

    <section class="map-section">
        <h2>Find Us on the Map</h2>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15955.073970288962!2d36.8138156!3d-1.2863894!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f10f5d1a5a58f%3A0xced6b56a4684b88e!2sNairobi!5e0!3m2!1sen!2ske!4v1695413517658!5m2!1sen!2ske" 
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <div>
        <h2>Our Location & Contacts</h2>
        <p><i class="fas fa-map-marker-alt"></i> Munyu Road Business Center</p>
        <p>Nairobi</p>
        <p>3rd Floor Room D4</p>
        <p>
            <i class="fas fa-envelope"></i> 
            Email: <a href="mailto:lightningboltelectronics@gmail.com">lightningboltelectronics@outlook.com</a>
        </p>        
        <p><i class="fas fa-phone-alt"></i> Phone: +254711845370</p>
        <h2>Shop Opening Time</h2>
        <p>Mon-Fri: 9 AM - 6 PM</p>
        <p>Sat: 9 AM - 3 PM</p>
        <p>Sun: Closed</p>
        <p>ONLINE: 24/7</p>
    </div>

    <section>
        <h2>Follow us on Social Media: @lightningboltelectronics</h2>
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
    </section>

    <section>
        <h2>Text us on WhatsApp: +254711845370</h2>
        <a href="https://wa.me/+254711845370" target="_blank" class="whatsapp-icon">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
    </section>

    <div class="feedback-container">
        <h5>Messages</h5>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form id="feedbackForm" action="contact.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required placeholder="Your Name">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Your Email">
            </div>
            <div class="form-group">
                <label for="message">Your Message:</label>
                <textarea id="message" name="message" rows="4" required placeholder="Your message..."></textarea>
            </div>
            <button type="submit">Submit Message</button>
        </form>
    </div>

    <section class="location-one">
        <img src="img/download.jpeg" alt="Location 1">
    </section>
    <section class="location-two">
        <img src="img/location2.jpeg" alt="Location 2">
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Lightning Bolt Electronics. All Rights Reserved.</p>
        <p><a href="terms&privacy.php">Terms & Privacy</a></p>

        <div class="social-links">
            <a href="https://twitter.com/YourHandle" target="_blank" class="social-icon">
                <i class="fab fa-twitter"></i> Twitter
            </a>
            <a href="https://web.facebook.com/profile.php?id=61574549278549" target="_blank">
                <i class="fab fa-facebook fa-2x"></i>
            </a>
            <a href="https://instagram.com/YourHandle" target="_blank" class="social-icon">
                <i class="fab fa-instagram"></i> Instagram
            </a>
        </div>
    </footer>
</body>
</html>
