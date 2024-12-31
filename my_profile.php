<?php
include 'header.php';
include 'database.php';

// Ensure the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to view your profile. <a href='login.php'>Login here</a>.");
}

$user_id = $_SESSION['user_id'];
$current_time = time();
?>

<br><br>
<div class='container'>

    <!-- Section: Uploaded Artworks -->
    <h2>Your Uploaded Artworks</h2>
    <?php
    $sql_uploaded = "SELECT * FROM artworks WHERE user_id = ? AND (status != 'user_deleted' AND deleted_by_user = 0)";
    $stmt_uploaded = $conn->prepare($sql_uploaded);
    $stmt_uploaded->bind_param("i", $user_id);
    $stmt_uploaded->execute();
    $result_uploaded = $stmt_uploaded->get_result();

    if ($result_uploaded->num_rows > 0) {
        echo "<div class='grid-container'>";
        while ($row = $result_uploaded->fetch_assoc()) {
            $artwork_id = $row['id'];
            $created_at_timestamp = strtotime($row['created_at']);
            $auction_end_time = $created_at_timestamp + ($row['auction_period'] * 86400);
            $remaining_time = max(0, $auction_end_time - $current_time);

            $bid_sql = "SELECT MAX(bid_price) AS highest_bid FROM bids WHERE artwork_id = ?";
            $stmt_bid = $conn->prepare($bid_sql);
            $stmt_bid->bind_param("i", $artwork_id);
            $stmt_bid->execute();
            $bid_result = $stmt_bid->get_result();
            $highest_bid = $bid_result->fetch_assoc()['highest_bid'] ?? 'No bids yet';

            $status = htmlspecialchars($row['status']); // Assuming the 'status' field exists in the artworks table
    
            // Determine the status color
            switch ($status) {
                case 'accepted':
                    $status_color = 'green';
                    break;
                case 'pending':
                    $status_color = 'orange';
                    break;
                case 'rejected':
                    $status_color = 'red';
                    break;
                default:
                    $status_color = 'black'; // Fallback color for unknown statuses
            }

            echo "<div class='artwork'>";
            echo "<img src='" . htmlspecialchars($row['photo_path']) . "' alt='Artwork'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>Starting Price: Rs." . htmlspecialchars($row['starting_price']) . "</p>";
            echo "<p>Highest Bid: Rs." . htmlspecialchars($highest_bid) . "</p>";
            echo "<p>Status: <span style='color: $status_color;'>" . htmlspecialchars($status) . "</span></p>";

            if ($remaining_time > 0) {
                echo "<p>Auction Period: " . ceil($remaining_time / 86400) . " days left</p>";
            } else {
                echo "<p>Auction Period: Ended</p>";
            }

            echo "<div class='action-buttons'>";

            // Allow the user to edit or delete the artwork unless it's rejected
            if ($status !== 'rejected') {
                echo "<a href='edit_artwork.php?id=$artwork_id'>Edit</a> | ";
            }

            // Include the delete functionality
            echo "<a href='delete_artwork.php?id=$artwork_id' onclick='return confirm(\"Are you sure you want to delete this artwork?\")'>Delete</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p class='no-artworks'>No artworks uploaded yet.</p>";
    }

    $stmt_uploaded->close();
    ?>
</div>

<?php
// Section: Bidded Artworks
echo "<h2>Your Bidded Artworks</h2>";

$sql_bidded = "
    SELECT DISTINCT a.*, b.bid_price AS user_bid, (
        SELECT MAX(bid_price) FROM bids WHERE artwork_id = a.id
    ) AS highest_bid,
    (
        SELECT user_id FROM bids 
        WHERE artwork_id = a.id 
        ORDER BY bid_price DESC, bid_time ASC LIMIT 1
    ) AS winner_id
    FROM artworks a
    INNER JOIN bids b ON a.id = b.artwork_id
    WHERE b.user_id = ?
    ORDER BY b.bid_time DESC";
$stmt_bidded = $conn->prepare($sql_bidded);
$stmt_bidded->bind_param("i", $user_id);
$stmt_bidded->execute();
$result_bidded = $stmt_bidded->get_result();

if ($result_bidded->num_rows > 0) {
    echo "<div class='grid-container'>";

    while ($row = $result_bidded->fetch_assoc()) {
        $artwork_id = $row['id'];
        $created_at_timestamp = strtotime($row['created_at']);
        $auction_end_time = $created_at_timestamp + ($row['auction_period'] * 86400);
        $remaining_time = max(0, $auction_end_time - $current_time);

        echo "<div class='artwork'>";
        echo "<img src='" . htmlspecialchars($row['photo_path']) . "' alt='Artwork'>"; // Artwork image
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>"; // Artwork title
        echo "<p>Your Bid: Rs." . htmlspecialchars($row['user_bid']) . "</p>"; // User's bid
        echo "<p>Highest Bid: Rs." . htmlspecialchars($row['highest_bid']) . "</p>"; // Current highest bid

        if ($remaining_time > 0) {
            echo "<p>Auction Period: " . ceil($remaining_time / 86400) . " days left</p>"; // Time remaining
        } else {
            echo "<p>Auction Period: Ended</p>"; // Auction ended
            if ($row['winner_id'] == $user_id) {
                echo "<p style='color: green; font-weight: bold;'>You have won the bid!</p>"; // Winner message
            } else {
                echo "<p style='color: red;'>Auction won by another bidder.</p>"; // Not the winner
            }
        }

        echo "</div>";
    }

    echo "</div>";
} else {
    echo "<p class='no-artworks'>You haven't placed any bids yet.</p>";
}

$stmt_bidded->close();
$conn->close();

echo "</div>";
?>

<?php
include 'footer.php';
?>

<style>

    /* Center the headings */
    h2 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
    }

    /* Grid container for artworks */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        /* 4 columns */
        gap: 20px;
        margin: 20px;
    }

    /* Artwork card styling */
    .artwork {
        border: 1px solid #ddd;
        padding: 15px;
        text-align: center;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .artwork img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .artwork h3 {
        font-size: 18px;
        margin: 10px 0;
    }

    .artwork p {
        font-size: 14px;
        color: #555;
    }

    .artwork .action-buttons {
        margin-top: 10px;
    }

    .artwork .action-buttons a {
        text-decoration: none;
        color: #007bff;
        margin-right: 10px;
    }

    .artwork .action-buttons a:hover {
        text-decoration: underline;
    }

    /* Style for no artworks message */
    .no-artworks {
        text-align: center;
        font-size: 16px;
        color: #777;
    }

    /* Form and button styling */
    form {
        margin-top: 10px;
    }

    form input[type="number"],
    form button {
        padding: 10px;
        font-size: 16px;
        width: 100%;
        margin-top: 10px;
    }

    form button {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }

    form button:hover {
        background-color: #0056b3;
    }
</style>