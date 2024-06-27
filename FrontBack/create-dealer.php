<?php
session_start(); // Start the session

// Set the default time zone
date_default_timezone_set('Asia/Kolkata');

// Include database connection script
include 'db_connect.php';

// Function to sanitize user input
function sanitizeInput($data)
{
    global $conn;
    return htmlspecialchars(mysqli_real_escape_string($conn, $data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST["dealer-name"]);
    $email = sanitizeInput($_POST["email"]);
    $password = sanitizeInput($_POST["password"]);
    $hashed_password = hash('sha256', $password); // Using SHA-256 for password hashing
    $contact = sanitizeInput($_POST["contact_number"]);
    $address = sanitizeInput($_POST["address"]);
    $created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO users (username, email, password, contact, address, created_at, role) 
            VALUES (?, ?, ?, ?, ?, ?, 'dealer')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $contact, $address, $created_at);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Dealer $username created successfully!";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: create-dealer.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Dealer</title>
    <link rel="stylesheet" href="base.css" />
    <link rel="stylesheet" href="create-dealer.css" />
</head>

<body>
    <header>
        <div class="header-top">
            <h1>East Coast Railway</h1>
            <h1>Closed User Group</h1>
        </div>
        <!-- <nav>
            <ul>
                <li><a href="index.html">Admin Page</a></li>
            </ul>
        </nav> -->
    </header>

    <main>
        <section id="create-dealer">
            <div class="heading-container">
                <button class="back-btn" onclick="window.location.href = './admin-page.html'"><img
                        src="https://img.icons8.com/ios/32/long-arrow-left.png" alt="back button"></button>
                <h2 class="heading">Create Dealer</h2>
            </div>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<p class='session-message'>" . $_SESSION['message'] . "</p>";
                unset($_SESSION['message']);
            }
            ?>
            <form class="form_container" action="create-dealer.php" method="post">
                <div class="input_box long-input">
                    <label for="dealer-name">Dealer Name</label>
                    <input type="text" id="dealer-name" name="dealer-name" placeholder="Enter Dealer Name" required />
                </div>
                <div class="input_box">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" required />
                </div>
                <div class="input_box">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" required />
                </div>
                <div class="input_box">
                    <label for="contact_number">Contact Number</label>
                    <input type="number" id="contact_number" name="contact_number" placeholder="Enter Contact Number"
                        required />
                </div>
                <div class="input_box">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter Address" required />
                </div>
                <button class="submit-button" type="submit">Create Dealer</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 East Coast Railway. All rights reserved.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </footer>
</body>

</html>