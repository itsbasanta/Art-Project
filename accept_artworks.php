<?php
include 'database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access Denied. You must be logged in to perform this action.");
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $artwork_id = intval($_GET['id']);
    $action = $_GET['action']; // 'accept' or 'reject'

    // Get the user_id and current status of the artwork by joining the artworks and users tables
    $stmt = $conn->prepare("
        SELECT artworks.user_id, artworks.status 
        FROM artworks
        JOIN users ON artworks.user_id = users.user_id
        WHERE artworks.id = ?
    ");

    // Check if the prepare function is successful
    if (!$stmt) {
        die("Error preparing SQL statement: " . $conn->error);
    }

    // Bind the parameter for the artwork ID
    $stmt->bind_param("i", $artwork_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $status);
    $stmt->fetch();
    $stmt->close();

    // Check if the artwork exists
    if (!$user_id) {
        echo "Artwork not found. Please check if the artwork ID exists.<br>";
        exit;
    }

    // Artwork exists, process the action
    if ($action === 'reject') { // Updated condition for lowercase 'reject'
        // Update the artwork status to 'rejected'
        $status = 'Rejected';
        $stmt = $conn->prepare("UPDATE artworks SET status = ? WHERE id = ?");
        if (!$stmt) {
            die("Error preparing SQL statement for update: " . $conn->error);
        }

        $stmt->bind_param("si", $status, $artwork_id);
        if ($stmt->execute()) {
            // Notify the user
            $message = "Your artwork has been rejected.";

            // Insert the message into the messages table
            $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
            if (!$stmt) {
                die("Error preparing SQL statement for message insertion: " . $conn->error);
            }

            $stmt->bind_param("is", $user_id, $message);
            if ($stmt->execute()) {
                // Redirect to the dashboard page after rejection
                header("Location: dashboard.php");
                exit; // Make sure to stop further execution
            } else {
                die("Error executing message insertion: " . $stmt->error);
            }
        } else {
            die("Error updating artwork status: " . $stmt->error);
        }
    } elseif ($action === 'accept') { // Ensure this condition is correct for 'accept'
        // Update the artwork status to 'accepted'
        $status = 'Accepted';
        $stmt = $conn->prepare("UPDATE artworks SET status = ? WHERE id = ?");
        if (!$stmt) {
            die("Error preparing SQL statement for update: " . $conn->error);
        }

        $stmt->bind_param("si", $status, $artwork_id);
        if ($stmt->execute()) {
            // Notify the user
            $message = "Your artwork has been accepted!";

            // Insert the message into the messages table
            $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
            if (!$stmt) {
                die("Error preparing SQL statement for message insertion: " . $conn->error);
            }

            $stmt->bind_param("is", $user_id, $message);
            if ($stmt->execute()) {
                // Redirect to the dashboard page after acceptance
                header("Location: dashboard.php");
                exit; // Make sure to stop further execution
            } else {
                die("Error executing message insertion: " . $stmt->error);
            }
        } else {
            die("Error updating artwork status: " . $stmt->error);
        }
    } else {
        echo "Invalid action.<br>";
    }
} else {
    echo "Invalid request.<br>";
}
?>