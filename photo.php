<?php
include 'header.php';
?>

<?php
include 'database.php'; // Ensure this file sets up the $conn variable

if (isset($_GET['id'])) {
    $photoId = intval($_GET['id']);
    
    // Check if the database connection is established
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $result = mysqli_query($conn, "SELECT * FROM photos WHERE id = $photoId");
    if ($row = mysqli_fetch_assoc($result)) {
        echo "<div style='display: flex; justify-content: center; align-items: center; height: 70vh;'>
        <img src='{$row['photo_path']}' alt='{$row['title']}' style='width: 40%; height: 100%;'>
      </div>";

        echo "<h1>{$row['title']}</h1>";
        echo "<p>{$row['description']}</p>";
    } else {
        echo "Photo not found.";
    }
} else {
    echo "Invalid photo ID.";
}
?>
