<?php
$pageTitle = "register";
include 'header.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Auction</title>
    <style>
 /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: url('image/artist.png');
  background-repeat: no-repeat;
  background-position: 200px center;
}

.signup-container h2 {
    font-size: 2rem;
    color: #ff4589;
    font-weight: bold;
    text-transform: uppercase;
    position: relative;
    
}

form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 300px;
}

form label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    font-size: 14px;
    color: black;
}

form input[type="text"],
form input[type="email"],
form input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

form input[type="text"]:focus,
form input[type="email"]:focus,
form input[type="password"]:focus {
    outline: none;
    border-color: #007BFF;
    box-shadow: 0 0 4px rgba(0, 123, 255, 0.3);
}

form span {
    color: red;
    font-size: 12px;
    display: block;
    margin-top: -10px;
    margin-bottom: 10px;
}


form .btn-submit[type="submit"] {
    background: #ff4589;
    color: #fff;
    font-size: 1rem;
    padding: 0.8rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

form .btn-submit:hover[type="submit"]:hover {
    background: #ff2c75;
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(255, 44, 117, 0.5);
}

.login-link {
    margin-top: 15px;
    text-align: center;
    font-size: 14px;
    color: #333;
}

.login-link a {
    color: #007BFF;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
<br> <br><br> <br> <br> <br><br> <br><br>
    <div class="signup-container">
        <h2>Sign Up</h2>

        <?Php
    if(isset($_POST['submit'])){
    //inserting data into database table
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordRepeat = $_POST['repeat_password'];
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $username = $_POST['username'];


    $errors = array();

     // Basic validation
     if (empty($name) || empty($email) || empty($password) || empty($passwordRepeat)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters.";
    }
    if ($password !== $passwordRepeat) {
        $errors[] = "Passwords do not match.";
    }

    require_once "database.php"; // Make sure this file contains $conn
    $sql = "SELECT * FROM users WHERE email = '$email'";


    $result = mysqli_query($conn, $sql);
    
    $rowcount = mysqli_num_rows($result);
    if ($rowcount > 0) {
        array_push($errors, "Email already exists.");
    }

    // Display errors if any
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        


        // SQL query to insert data
        $sql = "INSERT INTO users (full_name, email, password, username) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $passwordHash, $username);

            // Execute statement
            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Your account has been created successfully!</div>";
                echo "<script>window.location.href = 'login.php';</script>"; 
                exit();
            } else {
                
                echo "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            echo "<div class='alert alert-danger'>Database error. Could not prepare statement.</div>";
        }

        // Close the database connection
        mysqli_close($conn);
    }
}
  
?>

        <form action="register.php" method="post" id="auctionform">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>
            <span id="error_full_name"></span>

            <label for="username">Username</label>
            <input type="text" id="name" name="username" placeholder="Enter username" required>
            <span id="error_user_name"></span>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <span id="error_email"></span>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required >
            <span id="error_password"></span>

            <label for="Repeat_password">Repeat-Password</label>
            <input type="password" id="repeat_password" name="repeat_password" >
            <span id="error_repeat_password"></span>
      

            <input type="submit" class="btn-submit" value="Create Account" id = "submit" name="submit">
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
    <br>
</body>
</html>
<?php
include 'footer.php';
?>