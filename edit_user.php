<?php
// Start the session
session_start();

// Include the database connection file
include 'database.php'; // Ensure this file sets up $conn

// Check if the 'id' parameter is passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']); // Sanitize the user ID
} else {
    die("Invalid User ID"); // Display error if 'id' is missing or not numeric
}

// Query to fetch user details
$sql = "SELECT * FROM users WHERE user_id = ?"; // Use 'id' or 'user_id' as per the column name
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("SQL Error: " . mysqli_error($conn)); // Print SQL error if preparation fails
}

mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("No user found with the given ID."); // Display error if no user is found
}

// Fetch user data
$row = mysqli_fetch_assoc($result);
$fullName = $row['full_name']; // Set the full_name
$email = $row['email']; // Set the email

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedName = mysqli_real_escape_string($conn, $_POST['full_name']);
    $updatedEmail = mysqli_real_escape_string($conn, $_POST['email']);

    $updateSql = "UPDATE users SET full_name = ?, email = ? WHERE user_id = ?"; // Adjust column name if needed
    $updateStmt = mysqli_prepare($conn, $updateSql);

    if ($updateStmt) {
        mysqli_stmt_bind_param($updateStmt, 'ssi', $updatedName, $updatedEmail, $userId);
        $updateSuccess = mysqli_stmt_execute($updateStmt);

        if ($updateSuccess) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error updating user details.";
        }
    } else {
        echo "SQL Error: " . mysqli_error($conn);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        label {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            font-size: 1.1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
<body>
<h1>Edit User Details</h1>
    <form method="POST">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($fullName); ?>" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <br>
        <button type="submit">Update User</button>
    </form>
</body>
</html>

