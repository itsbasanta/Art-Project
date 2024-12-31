<?php
include 'database.php';

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Error: You must be logged in as admin.");
}

if (isset($_GET['id'])) {
    $artwork_id = $_GET['id'];

    // You can either delete the artwork or mark it as rejected
    $stmt = $conn->prepare("UPDATE artworks SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $artwork_id);

    if ($stmt->execute()) {
        echo "Artwork rejected successfully. <a href='dashboard.php'>Go back to Dashboard</a>";
    } else {
        echo "Error rejecting artwork: " . $stmt->error;
    }
}
?>
