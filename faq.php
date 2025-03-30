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

// Load the FAQ page content
$url = "http://localhost/portifolio/faqs.php";

function fetchUrlContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

$faqPage = fetchUrlContent($url);
if (!$faqPage) {
    die("Failed to load FAQs page.");
}

// Load HTML using Simple HTML DOM Parser
include('simple_html_dom.php');
$html = str_get_html($faqPage);

if (!$html) {
    die("Failed to parse FAQ page.");
}

$inserted = 0;
$updated = 0;
$skipped = 0;

foreach ($html->find('section') as $faq) {
    $questionNode = $faq->find('h3', 0);
    $answerNode = $faq->find('div.faq-content', 0);

    if ($questionNode && $answerNode) {
        $question = trim(htmlspecialchars_decode($questionNode->plaintext));
        $answer = trim(htmlspecialchars_decode($answerNode->plaintext));

        // Ignore empty answers
        if (empty($answer)) {
            echo "Skipping empty answer for: " . htmlspecialchars($question) . "<br>";
            continue;
        }

        // Check for existing entry
        $checkQuery = "SELECT id, answer FROM faqs WHERE question = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $question);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $existingAnswer);

        if ($stmt->fetch()) {
            if ($existingAnswer !== $answer) {
                // Update existing FAQ if the answer has changed
                $updateQuery = "UPDATE faqs SET answer = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("si", $answer, $id);
                if ($updateStmt->execute()) {
                    echo "Updated: " . htmlspecialchars($question) . "<br>";
                    $updated++;
                }
                $updateStmt->close();
            } else {
                echo "Skipping (No changes): " . htmlspecialchars($question) . "<br>";
                $skipped++;
            }
        } else {
            // Insert new FAQ
            $insertQuery = "INSERT INTO faqs (question, answer) VALUES (?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ss", $question, $answer);
            if ($stmt->execute()) {
                echo "Inserted: " . htmlspecialchars($question) . "<br>";
                $inserted++;
            }
        }
        $stmt->close();
    }
}

$conn->close();
echo "Inserted: $inserted, Updated: $updated, Skipped: $skipped.";
?>
