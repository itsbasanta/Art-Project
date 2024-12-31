<?php
// Include the database connection
include 'database.php'; // Ensure the path is correct

// Initialize variables
$title = "";
$description = "";
$photoPath = "";
$error = "";

// Check if the ID is provided
if (isset($_GET['id'])) {
    $photoId = intval($_GET['id']);

    // Ensure the connection is valid
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare SELECT statement to fetch current photo details securely
    $stmt = mysqli_prepare($conn, "SELECT * FROM photos WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $photoId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $photo = mysqli_fetch_assoc($result);
        $title = htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($photo['description'], ENT_QUOTES, 'UTF-8');
        $photoPath = $photo['photo_path'];
    } else {
        die("Photo not found.");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Initialize variable to hold new photo path
        $newPhotoPath = $photoPath;

        // Handle photo upload if a new file is provided
        if (!empty($_FILES['photo']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($_FILES['photo']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Only JPG, PNG, and GIF files are allowed.";
            } else {
                $targetDir = "image/";
                // Ensure the uploads directory exists
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                // Generate a unique file name to prevent overwriting
                $uniqueName = uniqid() . "_" . basename($_FILES['photo']['name']);
                $newPhotoPath = $targetDir . $uniqueName;

                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $newPhotoPath)) {
                    $error = "Failed to upload file. Check the 'image/' directory permissions.";
                } else {
                    // Optionally, delete the old photo file
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }
            }
        }

        // If no errors, proceed to update the database
        if (empty($error)) {
            // Prepare UPDATE statement
            $updateStmt = mysqli_prepare($conn, "UPDATE photos SET title = ?, description = ?, photo_path = ? WHERE id = ?");
            mysqli_stmt_bind_param($updateStmt, 'sssi', $title, $description, $newPhotoPath, $photoId);

            if (mysqli_stmt_execute($updateStmt)) {
                // Redirect to the dashboard after successful update
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Error updating photo: " . mysqli_error($conn);
            }
        }
    }
} else {
    echo "Invalid photo ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Photo</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Form Container */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 500px;
        }

        /* Form Heading */
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007BFF;
        }

        /* Form Elements */
        .form-container input[type="text"],
        .form-container textarea,
        .form-container input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-container input[type="text"]:focus,
        .form-container textarea:focus,
        .form-container input[type="file"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        /* Submit Button */
        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        /* Error Message */
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Current Photo Display */
        .current-photo {
            text-align: center;
            margin-bottom: 15px;
        }

        .current-photo img {
            width: 150px;
            height: auto;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        /* Back Link */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Photo</h2>
    <?php
    if (!empty($error)) {
        echo "<div class='error'>{$error}</div>";
    }
    ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" name="title" id="title" value="<?= $title ?>" required><br>

        <label for="description">Description:</label><br>
        <textarea name="description" id="description" rows="5" required><?= $description ?></textarea><br>

        <label for="photo">Upload New Photo (optional):</label><br>
        <input type="file" name="photo" id="photo" accept="image/*"><br>

        <div class="current-photo">
            <p>Current Photo:</p>
            <img src="<?= htmlspecialchars($photoPath) ?>" alt="<?= $title ?>">
        </div>

        <button type="submit">Update Photo</button>
    </form>
    <a href="dashboard.php" class="back-link">Back to Dashboard</a>
</div>

</body>
</html>
