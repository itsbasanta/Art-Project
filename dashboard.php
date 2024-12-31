<?php
// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    // Redirect to login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Include the database connection file
include 'database.php';


// Fetch data from the database (example: fetching users and art items)
$sqlUsers = "SELECT * FROM users";
$resultUsers = mysqli_query($conn, $sqlUsers);

$sqlArtworks = "SELECT * FROM artworks";
$resultArtworks = mysqli_query($conn, $sqlArtworks);
?>


<link rel="stylesheet" href="styles.css">
<nav class="navbar">
    <div class="logo">
        <img src="Image/logo.png" alt="Logo" height="50px" width="auto">
    </div>
    <h2>Welcome to the Admin Dashboard</h2>
    <!-- Conditional Display for Guest and Logged-In Users -->
    <?php if (isset($_SESSION['user'])): ?>
        <!-- Logged-In User: Show Logout Button -->
        <button class="btn" onclick="window.location.href='logout.php'">Log Out</button>
    <?php else: ?>
        <!-- Guest User: Show Login and Create Account Buttons -->
        <button class="btn" onclick="window.location.href='login.php'">Log In</button>
        <button class="btn" onclick="window.location.href='register.php'">Create an Account</button>
    <?php endif; ?>
    </div>
</nav>
<br> <br> <br>
<!-- Users List Section -->
<h2>Users List</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Admin Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display all users
        while ($row = mysqli_fetch_assoc($resultUsers)) {
            echo "<tr>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['full_name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . ($row['is_admin'] ? 'Admin' : 'User') . "</td>";
            // Modify actions: Check if the user is inactive
            if (isset($row['status']) && $row['status'] == 'inactive') {
                echo "<td>Non-active Account</td>";
            } else {
                echo "<td><a href='edit_user.php?id=" . $row['user_id'] . "'>Edit</a> | 
                         <a href='delete_user.php?id=" . $row['user_id'] . "' 
                            onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a></td>";
            }
            echo "</tr>";
        }
        ?>
    </tbody>
</table>



<!-- Artworks List Section -->
<br> <br>
<h2>Upload Artist</h2>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $photoPath = '';

    // Handle file upload
    if (!empty($_FILES['photo']['name'])) {
        // Check for file upload errors
        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "image/";
            $photoPath = $targetDir . basename($_FILES['photo']['name']);

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
                // Insert into database
                $sql = "INSERT INTO photos (title, description, photo_path) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'sss', $title, $description, $photoPath);
                mysqli_stmt_execute($stmt);
            } else {
                echo "Failed to move the uploaded file.";
            }
        } else {
            echo "File upload error: " . $_FILES['photo']['error'];
        }
    }
}
?>
<style>
    /* Reset some basic styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        color: #333;
        line-height: 1.6;
        padding: 20px;
    }

    /* Centering the form */
    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        margin: 0 auto;
        border: 1px solid #ddd;
    }

    /* Form title */
    form h2 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    /* Input and textarea styles */
    input[type="text"],
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    input[type="text"]:focus,
    textarea:focus,
    input[type="file"]:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Submit button */
    button {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: #fff;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0056b3;
    }

    button:active {
        background-color: #003f8f;
    }

    /* Placeholder text style */
    input::placeholder,
    textarea::placeholder {
        color: #aaa;
        font-style: italic;
    }

    /* Textarea resizing */
    textarea {
        resize: horizontal;
    }

    /* File input customization */
    input[type="file"] {
        padding: 10px;
        font-size: 16px;
    }

    /* Responsive container for form */
    form {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }


    .content-container {
        display: grid;
        /* Use CSS Grid */
        grid-template-columns: repeat(4, 1fr);
        /* 4 equal columns */
        gap: 20px;
        /* Space between grid items */
        padding: 20px;
        justify-content: center;
        /* Centers the grid within its container */
    }

    .content-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
        /* Optional: Adds a border */
        border-radius: 8px;
        /* Optional: Rounds edges */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Optional: Adds shadow */
    }

    .content-item img {
        width: 100%;
        height: auto;
        margin-bottom: 10px;
        /* Adds space below the image */
        border-radius: 4px;
        /* Optional: Rounds image corners */
    }

    .content-item h3 {
        margin: 10px 0;
        font-size: 1.2rem;
        /* Sets a readable font size */
    }

    .content-item p {
        margin: 10px 0;
        color: #555;
        /* Optional: Slightly gray text */
    }

    .content-item a {
        display: inline-block;
        text-decoration: none;
        color: #fff;
        /* Sets link text color */
        background-color: #007bff;
        /* Blue background for links */
        border-radius: 4px;
        transition: background-color 0.3s;
        /* Smooth hover effect */
    }

    .content-item a:hover {
        background-color: #0056b3;
        /* Darker blue on hover */
    }

    .table-container {
        width: 100%;
        margin: 20px auto;
        border-collapse: collapse;
        /* Ensures no gaps between table borders */
        text-align: left;
        /* Align text to the left */
        font-family: Arial, sans-serif;
        border: 1px solid #ddd;
        /* Border for the entire table */
    }

    .table-container th,
    .table-container td {
        padding: 10px;
        border: 1px solid #ddd;
        /* Borders for table cells */
        vertical-align: middle;
        /* Centers content vertically */
    }

    .table-container th {
        background-color: #f4f4f4;
        /* Light gray background for headers */
        font-weight: bold;
    }

    .table-container td img {
        width: 50px;
        /* Small photo size */
        height: auto;
        border-radius: 4px;
        /* Optional: Rounded corners */
    }

    .table-container a {
        text-decoration: none;
        /* Removes underline */
        color: #fff;
        background-color: #007bff;
        /* Blue buttons */
        padding: 5px 10px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .table-container a:hover {
        background-color: #0056b3;
        /* Darker blue on hover */
    }

    <style>table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 18px;
        text-align: left;
    }

    th,
    td {
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
        color: #333;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    a {
        color: #007BFF;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    h2 {
        font-family: Arial, sans-serif;
        font-size: 24px;
        margin-bottom: 15px;
        color: #333;
    }

    p {
        font-family: Arial, sans-serif;
        font-size: 18px;
        color: #555;
    }
</style>



</style>


<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="file" name="photo" accept="image/*" required>
    <button type="submit">Upload Photo</button>
</form>
<br> <br>
<h2>Artist List</h2>
<?php
echo "<table class='table-container'>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>";

$result = mysqli_query($conn, "SELECT * FROM photos ORDER BY uploaded_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td><img src='{$row['photo_path']}' alt='{$row['title']}'></td>
            <td>{$row['title']}</td>";

    // Check if the status is deleted
    if ($row['status'] == 'deleted') {
        echo "<td>Deleted</td>";
    } else {
        echo "<td>
                <a href='edit_photo.php?id={$row['id']}'>Edit</a>
                <a href='delete_photo.php?id={$row['id']}'>Delete</a>
              </td>";
    }
    echo "</tr>";
}
echo "</tbody>
      </table>";
?>


<br><br>


<?php
// Fetch all artworks pending approval
$sql = "SELECT artworks.*, users.full_name AS username, users.email 
        FROM artworks 
        INNER JOIN users ON artworks.user_id = users.user_id 
        WHERE artworks.status = 'pending' AND artworks.deleted_by_user = 0";  // Only show non-deleted artworks

$stmt = $conn->prepare($sql);

// Check if the preparation of the statement was successful
if ($stmt === false) {
    die('Error preparing statement: ' . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Pending Artworks</h2>";
echo "<table border='1'><tr><th>Artwork</th><th>Artwork Title</th><th>Full Name</th><th>Email</th><th>Action</th></tr>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='" . htmlspecialchars($row['photo_path']) . "' alt='Artwork Image' width='100'></td>";  // Display artwork image
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td><a href='accept_artworks.php?id=" . $row['id'] . "&action=accept'>Accept</a> | 
              <a href='accept_artworks.php?id=" . $row['id'] . "&action=reject'>Reject</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No pending artworks.</p>";
}

// Fetch accepted artworks
$sql_accepted = "SELECT artworks.*, users.full_name AS username, users.email 
                 FROM artworks 
                 INNER JOIN users ON artworks.user_id = users.user_id 
                 WHERE artworks.status = 'accepted' AND artworks.deleted_by_user = 0";  // Only show non-deleted artworks

$stmt_accepted = $conn->prepare($sql_accepted);
if ($stmt_accepted === false) {
    die('Error preparing statement for accepted artworks: ' . $conn->error);
}

$stmt_accepted->execute();
$result_accepted = $stmt_accepted->get_result();

echo "<h2>Accepted Artworks</h2>";
echo "<table border='1'><tr><th>Artwork</th><th>Artwork Title</th><th>Full Name</th><th>Email</th><th>Status</th></tr>";
if ($result_accepted->num_rows > 0) {
    while ($row = $result_accepted->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='" . htmlspecialchars($row['photo_path']) . "' alt='Artwork Image' width='100'></td>";  // Display artwork image
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>Accepted</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No accepted artworks.</p>";
}

// Fetch rejected artworks
$sql_rejected = "SELECT artworks.*, users.full_name AS username, users.email 
                 FROM artworks 
                 INNER JOIN users ON artworks.user_id = users.user_id 
                 WHERE artworks.status = 'rejected' AND artworks.deleted_by_user = 0";  // Only show non-deleted artworks

$stmt_rejected = $conn->prepare($sql_rejected);
if ($stmt_rejected === false) {
    die('Error preparing statement for rejected artworks: ' . $conn->error);
}

$stmt_rejected->execute();
$result_rejected = $stmt_rejected->get_result();

echo "<h2>Rejected Artworks</h2>";
echo "<table border='1'><tr><th>Artwork</th><th>Artwork Title</th><th>Full Name</th><th>Email</th><th>Status</th></tr>";
if ($result_rejected->num_rows > 0) {
    while ($row = $result_rejected->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='" . htmlspecialchars($row['photo_path']) . "' alt='Artwork Image' width='100'></td>";  // Display artwork image
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>Rejected</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No rejected artworks.</p>";
}

// Fetch deleted artworks
$sql_deleted = "SELECT artworks.*, users.full_name AS username, users.email 
                FROM artworks 
                INNER JOIN users ON artworks.user_id = users.user_id 
                WHERE artworks.deleted_by_user = 1";  // Show only deleted artworks

$stmt_deleted = $conn->prepare($sql_deleted);
if ($stmt_deleted === false) {
    die('Error preparing statement for deleted artworks: ' . $conn->error);
}

$stmt_deleted->execute();
$result_deleted = $stmt_deleted->get_result();

echo "<h2>Deleted Artworks</h2>";
echo "<table border='1'><tr><th>Artwork</th><th>Artwork Title</th><th>Full Name</th><th>Email</th><th>Status</th></tr>";
if ($result_deleted->num_rows > 0) {
    while ($row = $result_deleted->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='" . htmlspecialchars($row['photo_path']) . "' alt='Artwork Image' width='100'></td>";  // Display artwork image
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>Deleted by user</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No deleted artworks.</p>";
}

?>



<br> <br>
<?php
include 'footer.php';
?>