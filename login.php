<?php
// Start the session
session_start();

// Include the database connection and header
include 'database.php';
$pageTitle = "Login";
include 'header.php';

// Initialize error message variable
$error_message = "";

// Check if the login form is submitted
if (isset($_POST["login"])) {
    // Sanitize user input to prevent SQL injection
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if user is trying to log in as admin
    if ($email === 'admin@artauction.com') {
        // Handle admin login
        handleAdminLogin($email, $password);
    } else {
        // Handle regular user login
        handleUserLogin($email, $password);
    }
}

function handleAdminLogin($email, $password) {
    global $conn; // Access global database connection

    // Prepare SQL statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // 's' for string type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        // Check if password is correct and the user is an admin
        if (password_verify($password, $row['password']) && $row['is_admin'] == 1) {
            // Admin login successful, redirect to dashboard
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user'] = $row['email'];
            $_SESSION['is_admin'] = 1;
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid admin credentials
            echo "Invalid admin credentials.";
        }
    } else {
        // No user found with the provided email
        echo "No user found with that email.";
    }

    $stmt->close(); // Close the prepared statement
}

function handleUserLogin($email, $password) {
    global $conn, $error_message; // Access global connection and error message

    // Prepare SQL statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // 's' for string type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        // Check if the account is inactive
        if ($row['status'] == 'inactive') {
            // If the account is inactive, set an error message
            $error_message = "Account has been deleted/banned due to unfair behavior.";
        } else {
            // Check if the password is correct
            if (password_verify($password, $row['password'])) {
                // User login successful, set session and redirect
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user'] = $row['email'];
                $_SESSION['is_admin'] = $row['is_admin'];

                // Redirect to the index page after successful login
                header("Location: index.php");
                exit();
            } else {
                // Invalid password
                $error_message = "Invalid password.";
            }
        }
    } else {
        // No user found with the provided email
        $error_message = "No user found with that email.";
    }

    $stmt->close(); // Close the prepared statement
}
?>

<br><br><br><br>
<div class="login-container">
    <h1>Login</h1>

    <?php
    // Display the error message if any
    if ($error_message != "") {
        echo "<div class='error-message' style='color: red; font-weight: bold;'>$error_message</div>";
    }
    ?>

    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
        <span id="error_email"></span>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        <span id="error_password"></span>

        <input type="submit" value="Login" name="login" id="login">
    </form>

    <div class="signup-link">
        Don't have an account? <a href="register.php">Sign Up</a>
    </div>
</div>

<?php
include 'footer.php';
?>




<style>
 /* General body styling */
body {
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    overflow: hidden;
    background-image: url('image/artist.png');
  background-repeat: no-repeat;
  background-position: 200px center;
    
}

/* Login container styling */
.login-container {
    background: rgba(255, 255, 255, 0.9);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
    text-align: center;
    width: 100%;
    max-width: 350px;
    box-sizing: border-box;
    animation: slideIn 0.8s ease-in-out;
    font-weight: bold;
}



/* Form title */
.login-container h1 {
    font-size: 2rem;
    color: #ff4589;
    margin-bottom: 1.5rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
     font-weight: bold;
}

/* Label styling */
.login-container form label {
    font-size: 1rem;
    color: #333;
    display: block;
    margin-bottom: 0.5rem;
    text-align: left;
}

/* Input field styling */
.login-container form input[type="email"],
.login-container form input[type="password"] {
    width: 100%;
    padding: 0.75rem;
    margin-bottom: 1.2rem;
    border: 2px solid #ff4589;
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
    transition: all 0.3s ease;
}

/* Input hover and focus effects */
.login-container form input[type="email"]:hover,
.login-container form input[type="password"]:hover {
    border-color: #ff78a5;
    box-shadow: 0 0 10px rgba(255, 69, 137, 0.5);
}

.login-container form input[type="email"]:focus,
.login-container form input[type="password"]:focus {
    border-color: #ff4589;
    box-shadow: 0 0 10px rgba(255, 69, 137, 0.8);
}

/* Submit button styling */
.login-container form input[type="submit"] {
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

.login-container form input[type="submit"]:hover {
    background: #ff2c75;
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(255, 44, 117, 0.5);
}

/* Signup link styling */
.signup-link {
    margin-top: 1rem;
    color: #333;
}

.signup-link a {
    color: #ff4589;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.signup-link a:hover {
    color: #ff2c75;
}

/* Background animation */
body::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, #ffffff1a, #ff45891a);
    animation: rotate 10s linear infinite;
    z-index: -1;
}
    </style>




