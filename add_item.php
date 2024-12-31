<?php
// Include database connection
include 'database.php';

if (isset($_POST['submit'])) {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $starting_bid = $_POST['starting_bid'];
    $image_name = $_FILES['image']['name'];
    $image_temp = $_FILES['image']['tmp_name'];
    $image_folder = 'uploads/' . $image_name;

    if (move_uploaded_file($image_temp, $image_folder)) {
        $query = "INSERT INTO items (name, description, starting_bid, image) VALUES ('$item_name', '$description', '$starting_bid', '$image_name')";
        if (mysqli_query($conn, $query)) {
            echo "Item added successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error uploading image.";
    }
}

mysqli_close($conn);
?>