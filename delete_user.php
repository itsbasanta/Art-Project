<?php
// Include database connection
include 'database.php';

// Get user ID to delete (or mark as inactive)
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Update the status to 'inactive' instead of deleting
    $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE user_id = ?");
    $stmt->bind_param("i", $user_id); // 'i' for integer type
    $stmt->execute();

    // Redirect back to the users list
    header('Location: dashboard.php');
    exit();
}
?>
