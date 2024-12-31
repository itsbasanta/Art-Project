<?php
include 'database.php';
include 'header.php';

if (isset($_GET['id'])) {
    $artwork_id = $_GET['id'];

    // Fetch artwork details from the database
    $stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
    $stmt->bind_param("i", $artwork_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $artwork = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process the form submission to update artwork
        $title = $_POST['title'];
        $description = $_POST['description'];
        $starting_price = $_POST['starting_price'];
        $auction_period = $_POST['auction_period'];

        // Update the artwork in the database
        $updateStmt = $conn->prepare("UPDATE artworks SET title = ?, description = ?, starting_price = ?, auction_period = ? WHERE id = ?");
        $updateStmt->bind_param("ssdii", $title, $description, $starting_price, $auction_period, $artwork_id);
        $updateStmt->execute();
        header("Location: my_profile.php"); // Redirect after updating
    }
} else {
    echo "Artwork ID not specified.";
}
?>
<br><br>
<style>
    body {
    background-image: url('image/artist.png'); /* Replace with your image path */
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    background-position:  left;
}
    /* Style the container for the form */
form {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Style the form labels */
form label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
}

/* Style the text inputs and textarea */
form input[type="text"],
form input[type="number"],
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1em;
    box-sizing: border-box;
}

/* Style the textarea */
form textarea {
    resize: vertical;
    min-height: 150px;
}

/* Style the submit button */
form button {
    padding: 12px 20px;
    background-color: #4CAF50;
    color: white;
    font-size: 1.1em;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Add hover effect to the button */
form button:hover {
    background-color: #45a049;
}

</style>
<!-- HTML form to edit artwork -->
<form method="POST">
    <label>Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($artwork['title']); ?>" required>
    <label>Description:</label>
    <textarea name="description"><?= htmlspecialchars($artwork['description']); ?></textarea>
    <label>Starting Price:</label>
    <input type="number" name="starting_price" value="<?= htmlspecialchars($artwork['starting_price']); ?>" required>
    <label>Auction Period (days):</label>
    <input type="number" name="auction_period" value="<?= htmlspecialchars($artwork['auction_period']); ?>" required>
    <button type="submit">Update Artwork</button>
</form>

<?php include 'footer.php'; ?>