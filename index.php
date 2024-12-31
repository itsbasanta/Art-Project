<?php
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}
?>

<?php
$pageTitle = "Home Page"; // Set the page title dynamically
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Auction</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>    
 
<br>

<?php
// Include database connection
include 'database.php';

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    $userEmail = $_SESSION['user'];
    
    // Fetch the user's name from the database using their email
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userName = htmlspecialchars($row['full_name']);
    } else {
        $userName = "Guest";
    }
    $stmt->close();
} else {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>

<div class="intro-section">
    <h1>Welcome to the World of Art, <?php echo $userName; ?>!</h1>
    <p>Let’s get great value for your art and bid on others stunning creations.</p>
</div>

  <h2>Some famous artist from World</h2>
<div id="artists" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: space-between; padding: 20px;">
  
  <!-- Artworks List Section -->
  <?php
  include 'database.php';

  $result = mysqli_query($conn, "SELECT * FROM photos ORDER BY uploaded_at DESC");

  echo '<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">';
  while ($row = mysqli_fetch_assoc($result)) {
      echo "<div style='border: 1px solid #ddd; padding: 10px; text-align: center;'>
              <a href='photo.php?id={$row['id']}'>
                  <img src='{$row['photo_path']}' alt='{$row['title']}' style='width: 100%; height: auto;'>
              </a>
              <a href='photo.php?id={$row['id']}'><h3>{$row['title']}</h3></a>
            </div>";
  }
  echo '</div>';
  ?>
</div>

<br>

<!-- Somewhere else on the page -->
<div id="artworks">
    <h2>Bid Your Best Price</h2> <br>
    <!-- Your artworks content goes here -->
     
    <?php
include 'artworks.php';
?> 
</div>


    <script src="script.js"></script>
</body>
</html>
<?php
include 'footer.php';
?> 
