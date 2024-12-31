<?php
session_start(); // Start the session to access user details

include 'database.php'; // Database connection

// Check if session variables are set (user is logged in)
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate incoming POST data
    $artwork_id = intval($_POST['artwork_id']);
    $bid_price = floatval($_POST['bid_price']);

    // User info from session
    $user_id = $_SESSION['user_id'];
    $bidder_name = $_SESSION['user_name'] ?? ''; // Using default empty string if not set
    $bidder_email = $_SESSION['user_email'] ?? ''; // Using default empty string if not set

    // Validate that bid price is greater than 0 and artwork ID is valid
    if (empty($artwork_id) || empty($bid_price) || $bid_price <= 0) {
        die("Invalid bid price or artwork ID.");
    }

    // Check if the bid price is greater than the current highest bid for this artwork
    $check_bid_sql = "SELECT MAX(bid_price) AS highest_bid FROM bids WHERE artwork_id = ?";
    $stmt_check = $conn->prepare($check_bid_sql);
    if (!$stmt_check) {
        die("Error preparing check bid query: " . $conn->error);
    }
    $stmt_check->bind_param("i", $artwork_id);
    $stmt_check->execute();
    $stmt_check->bind_result($highest_bid);
    $stmt_check->fetch();
    $stmt_check->close();

    // If no bids have been placed, consider the starting price as the base
    $highest_bid = $highest_bid ?: 0;

    // Ensure that the new bid is greater than the current highest bid
    if ($bid_price <= $highest_bid) {
        die("Your bid must be higher than the current highest bid.");
    }

    // Insert bid into the database
    $stmt = $conn->prepare("INSERT INTO bids (artwork_id, user_id, bidder_name, bidder_email, bid_price) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error); // Debugging output
    }

    $stmt->bind_param("iissd", $artwork_id, $user_id, $bidder_name, $bidder_email, $bid_price);

   if ($stmt->execute()) {
    // Redirect back to the index.php page (with the updated highest bid value)
    header("Location: index.php?artwork_id=" . $artwork_id . "&highest_bid=" . $bid_price);
    exit;
} else {
    die("Error executing statement: " . $stmt->error);
}


    $stmt->close();
}

$conn->close();
?>
