<?php
include 'database.php';

// Start session and check if user is logged in
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to upload artwork. <a href='login.php'>Login here</a>.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $starting_price = mysqli_real_escape_string($conn, $_POST['starting_price']);
    $auction_time_period = mysqli_real_escape_string($conn, $_POST['auction_time_period']);

    $targetDir = "images/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $photoPath = $targetDir . basename($_FILES['photo']['name']);

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
        $stmt = $conn->prepare("INSERT INTO artworks (user_id, title, description, starting_price, auction_period, photo_path, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("issdis", $user_id, $title, $description, $starting_price, $auction_time_period, $photoPath);

        if ($stmt->execute()) {
            // Success message and automatic redirection
            echo "Artwork uploaded successfully. Redirecting to your profile...";
            header("refresh:2;url=my_profile.php");
            exit();
        } else {
            echo "Error inserting data: " . $stmt->error;
        }
    } else {
        echo "Error uploading file: " . $_FILES['photo']['error'];
    }
}
?>

<?php include 'header.php'; ?>

<style>
    body {
        background-image: url('image/artist.png');
        /* Replace with your image path */
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-position: left;
    }

    /* Styling the form */
    h2 {
        text-align: center;
        font-size: 2em;
        margin-bottom: 20px;
    }

    form {
        width: 50%;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    form input,
    form textarea,
    form button {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    form input[type="file"] {
        border: none;
        padding: 0;
    }

    form button {
        background-color: #007BFF;
        color: white;
        font-size: 1.1em;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    form button:hover {
        background-color: #0056b3;
    }

    /* Input fields */
    input[type="text"],
    input[type="number"] {
        font-size: 1.1em;
    }

    textarea {
        font-size: 1.1em;
        height: 120px;
        resize: none;
    }
</style>
<br>
<h2>Auction Your Art</h2>

<form method="POST" action="upload_artwork.php" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required><br>
    <textarea name="description" placeholder="Description" required></textarea><br>
    <input type="number" name="starting_price" placeholder="Starting Price" required><br>
    <input type="text" name="auction_time_period" placeholder="Auction Time Period (in days)" required><br>
    <input type="file" name="photo" accept="image/*" required><br>
    <button type="submit">Upload Artwork</button>
</form>

<?php include 'footer.php'; ?>