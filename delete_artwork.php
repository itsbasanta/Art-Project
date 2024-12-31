<?php
session_start();  // Ensure session is started
include 'database.php';

if (isset($_GET['id'])) {
    $artwork_id = $_GET['id'];

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Error: You must be logged in to delete artwork.");
    }

    // Verify if the artwork belongs to the logged-in user
    $stmt = $conn->prepare("SELECT user_id FROM artworks WHERE id = ?");
    $stmt->bind_param("i", $artwork_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $artwork = $result->fetch_assoc();

    // Check if the artwork belongs to the current logged-in user
    if ($artwork['user_id'] == $_SESSION['user_id']) {
        // Mark artwork as deleted by the user
        $deleteStmt = $conn->prepare("UPDATE artworks SET deleted_by_user = 1 WHERE id = ?");
        $deleteStmt->bind_param("i", $artwork_id);
        $deleteStmt->execute();
        header("Location: my_profile.php");  // Redirect to profile after marking as deleted
    } else {
        echo "Error: You cannot delete someone else's artwork.";
    }
} else {
    echo "Error: No artwork ID provided.";
}
?>
