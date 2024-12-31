<?php
// Include database connection
include 'database.php';  

// Check if an ID is passed via GET
if (isset($_GET['id'])) {
    $photo_id = $_GET['id'];

    // Fetch the photo details to show confirmation
    $query = "SELECT * FROM photos WHERE id = $photo_id";
    $result = mysqli_query($conn, $query);
    $photo = mysqli_fetch_assoc($result);

    if ($photo) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
            // Mark as deleted
            $delete_query = "UPDATE photos SET status = 'deleted' WHERE id = $photo_id";
            if (mysqli_query($conn, $delete_query)) {
                echo "Photo marked as deleted successfully.";
                header("Location: dashboard.php");  // Redirect after deletion
                exit();
            } else {
                echo "Error marking photo as deleted.";
            }
        }
    } else {
        echo "Photo not found!";
    }
} else {
    echo "Invalid photo ID!";
}
?>

<!-- Confirmation Modal Popup -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h2>Are you sure you want to mark this photo as deleted?</h2>
    <img src="<?php echo $photo['photo_path']; ?>" alt="<?php echo $photo['title']; ?>" style="width: 150px; height: 150px;">
    <p>Title: <?php echo $photo['title']; ?></p>
    <form method="POST">
        <button type="submit" name="delete">Yes, Mark as Deleted</button>
        <button type="button" onclick="closeModal()">Cancel</button>
    </form>
  </div>
</div>

<!-- CSS to style the popup -->
<style>
    /* Modal styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    /* Modal content */
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        text-align: center;
    }

    /* Close button */
    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close-btn:hover,
    .close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Style buttons */
    button {
        padding: 10px 20px;
        margin: 10px;
        font-size: 16px;
        cursor: pointer;
    }

    button[type="submit"] {
        background-color: #f44336;
        color: white;
        border: none;
    }

    button[type="button"] {
        background-color: #4CAF50;
        color: white;
        border: none;
    }
</style>

<!-- JavaScript to handle the close action and redirect -->
<script>
    window.onload = function() {
        // Automatically show the modal when the page loads
        document.getElementById('deleteModal').style.display = "block";
    }

    // Function to close the modal and redirect to the dashboard
    function closeModal() {
        window.location.href = "dashboard.php"; // Redirect to the artist table
    }
</script>
