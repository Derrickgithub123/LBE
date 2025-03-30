<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $review_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
    $stmt->bind_param("i", $review_id);
    
    if ($stmt->execute()) {
        echo "Review deleted successfully.";
    } else {
        echo "Error deleting review.";
    }
    $stmt->close();
}
$conn->close();
?>
