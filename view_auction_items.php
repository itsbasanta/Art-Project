<?php
// Include database connection
include 'database.php';

// Query to fetch auction items along with user data
$sql = "SELECT ai.*, u.full_name, u.email FROM auction_items ai 
        JOIN users u ON ai.user_id = u.id 
        ORDER BY ai.created_at DESC";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Description</th>
                <th>Starting Price</th>
                <th>Auction End</th>
                <th>Uploaded By</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td><img src='{$row['image_path']}' alt='{$row['title']}' style='width:100px;height:auto;'></td>
                <td>{$row['title']}</td>
                <td>{$row['description']}</td>
                <td>{$row['starting_price']}</td>
                <td>{$row['auction_end']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['email']}</td>
                <td><a href='edit_photo.php?id={$row['id']}'>Edit</a> | <a href='delete_photo.php?id={$row['id']}'>Delete</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No auction items found.";
}
?>