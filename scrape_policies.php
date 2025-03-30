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

// Sample terms and privacy policy sections
$data = [
    ["Introduction", "terms", "Welcome to Lightning Bolt Electronics. By using our website, you agree to the following terms and conditions."],
    ["Use of Website", "terms", "You must be at least 18 years old or have parental consent to use our website. Unauthorized use of our website may give rise to a claim for damages."],
    ["Payments", "terms", "We accept PayPal, M-Pesa, and Bank Transfers. All payments must be completed before products are shipped."],
    ["Returns and Refunds", "terms", "Returns are accepted within 14 days of purchase. Refunds will be processed based on the original payment method."],
    ["Changes to Terms", "terms", "We reserve the right to modify these terms at any time. Continued use of the website means you accept the new terms."],
    ["Information We Collect", "privacy", "We collect personal information such as your name, email, phone number, and payment details when you make a purchase."],
    ["How We Use Your Information", "privacy", "We use your information to process orders, provide customer support, and improve our services."],
    ["Data Security", "privacy", "We implement security measures to protect your personal data from unauthorized access."],
    ["Cookies", "privacy", "We use cookies to enhance your browsing experience. You can disable cookies in your browser settings."],
    ["Third-Party Services", "privacy", "We do not sell or share your personal information with third parties except for payment processing."],
    ["Policy Updates", "privacy", "We may update this privacy policy from time to time. Changes will be posted on this page."]
];

// Insert into database
foreach ($data as $row) {
    $stmt = $conn->prepare("INSERT INTO policies (section, category, content) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $row[0], $row[1], $row[2]);
    $stmt->execute();
}

echo "Data inserted successfully!";
$conn->close();
?>
