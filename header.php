<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Document</title>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="Image/logo.png" alt="Logo" height="80px" width="auto">
        </div>
        <div class="search-container">
            <input type="text" id="search" placeholder="Search by art name or artist" oninput="showPredictions()">
            <button class="search-btn" onclick="performSearch()">🔍</button>
            <i class="fas fa-search search-icon" onclick="performSearch()"></i>
            <div class="predictions" id="predictions"></div>
        </div>

        <div class="nav-links">
            <a href="index.php" class="submenu-link">Home</a>
            <a href="index.php#artists" class="submenu-link">Artists</a>
            <a href="index.php#artworks" class="submenu-link">Artworks</a>
            <a href="index.php#footer" class="submenu-link">Contact Us</a>

            <!-- Conditional Display for Guest and Logged-In Users -->
            <?php if (isset($_SESSION['user'])): ?>
                <!-- Buy Button -->
                <a href="<?php echo isset($_SESSION['user']) ? 'my_profile.php' : 'login.php'; ?>" class="nav-link">My
                    Profile</a>
                <a href="index.php#artworks" class="nav-link">Buy</a>

                <!-- Sell Button -->
                <a href="<?php echo isset($_SESSION['user']) ? 'upload_artwork.php' : 'login.php'; ?>"
                    class="nav-link">Sell</a>
                <!-- Logged-In User: Show Logout Button -->
                <button class="btn" onclick="window.location.href='logout.php'">Log Out</button>
            <?php else: ?>
                <!-- Guest User: Show Login and Create Account Buttons -->
                <button class="btn" onclick="window.location.href='login.php'">Log In</button>
                <button class="btn" onclick="window.location.href='register.php'">Create an Account</button>
            <?php endif; ?>
        </div>
    </nav>
</body>

</html>