<?php
include 'database.php';
include 'header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to place a bid.";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql_user = "SELECT * FROM users WHERE user_id = $user_id";
$result_user = mysqli_query($conn, $sql_user);
if (!$result_user) {
    die("Error fetching user data: " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($result_user);

$sql = "SELECT * FROM artworks WHERE status = 'accepted' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching artworks: " . mysqli_error($conn));
}

$current_time = time();

echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";

while ($row = mysqli_fetch_assoc($result)) {
    $artwork_id = $row['id'];
    $artwork_user_id = $row['user_id'];
    $created_at_timestamp = strtotime($row['created_at']);
    $auction_period = $row['auction_period'];
    $auction_end_time = $created_at_timestamp + ($auction_period * 86400);

    $bid_sql = "SELECT MAX(bid_price) AS highest_bid FROM bids WHERE artwork_id = $artwork_id";
    $bid_result = mysqli_query($conn, $bid_sql);
    $highest_bid = $bid_result ? mysqli_fetch_assoc($bid_result)['highest_bid'] ?? 0 : 0;

    if ($current_time < $auction_end_time) {
        echo "<div style='width: 23%; border: 1px solid #ddd; padding: 10px; text-align: center; background-color: #f9f9f9;'>
                <img src='{$row['photo_path']}' alt='{$row['title']}' style='width: 100%; height: auto;'>
                <h3>{$row['title']}</h3>
                <p>{$row['description']}</p>
                <p><strong>Starting Price: </strong>{$row['starting_price']}</p>
                <p><strong>Highest Bid: </strong>" . ($highest_bid ? $highest_bid : "No bids yet") . "</p>
                <p><strong>Time Remaining: </strong><span id='timer-{$artwork_id}'></span></p>";

        if ($artwork_user_id == $user_id) {
            echo "<p><strong>You cannot bid on your own artwork.</strong></p>";
        } else {
            // Check if the user has already placed a bid
            $check_bid_sql = "SELECT * FROM bids WHERE artwork_id = $artwork_id AND user_id = $user_id LIMIT 1";
            $check_bid_result = mysqli_query($conn, $check_bid_sql);

            if (mysqli_num_rows($check_bid_result) > 0) {
                echo "<p><strong>You have already bidded on this artwork.</strong></p>";
                echo "<a href='my_profile.php' style='text-decoration: none; color: blue;'>Go to your bidded artworks</a>";
            } else {
                echo "<form action='place_bid.php' method='POST' style='margin-top: 10px;'>
                        <input type='hidden' name='artwork_id' value='{$artwork_id}'>
                        <input type='hidden' name='user_id' value='{$user_id}'>
                        <input type='hidden' name='user_name' value='{$user['full_name']}'>
                        <input type='hidden' name='user_email' value='{$user['email']}'>
                        <label for='bid_price'>Your Bid (must be higher than " . ($highest_bid ? $highest_bid : $row['starting_price']) . "):</label><br>
                        <input type='number' name='bid_price' step='0.01' min='" . ($highest_bid ? $highest_bid + 0.1 : $row['starting_price'] + 0.1) . "' required>
                        <br><br>
                        <button type='submit' class='bid-button'>Place Bid</button>
                      </form>";
            }
        }

        echo "</div>";
    }
}

echo "</div>";
?>

<script>
    // Function to update timer for each artwork
    const updateTimer = (id, endTime) => {
        const now = new Date().getTime();
        const timeLeft = endTime - now;

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            document.getElementById('timer-' + id).innerHTML = days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
        } else {
            document.getElementById('timer-' + id).innerHTML = 'Auction Ended';
        }
    };

    // Loop through each artwork and set up the timer
    <?php
    // Fetch all artworks again to pass end times for JS
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $artwork_id = $row['id'];
        $created_at_timestamp = strtotime($row['created_at']);
        $auction_period = $row['auction_period'];
        $auction_end_time = $created_at_timestamp + ($auction_period * 86400);
        echo "const endTime{$artwork_id} = new Date('".date('Y-m-d H:i:s', $auction_end_time)."').getTime();";
        echo "setInterval(() => updateTimer({$artwork_id}, endTime{$artwork_id}), 1000);";
    }
    ?>
</script>
