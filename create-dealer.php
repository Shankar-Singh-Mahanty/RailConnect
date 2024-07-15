<?php
// Set the default time zone
date_default_timezone_set('Asia/Kolkata');

include 'authenticate.php';
checkUser("admin");

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
    $contact = sanitizeInput($_POST["contact_number"]);
    $address = sanitizeInput($_POST["address"]);
    $hashed_password = hash('sha256', $password); // Using SHA-256 for password hashing
    $created_at = date("Y-m-d H:i:s");

    $errors = [];

    // Validate inputs
    if (empty($username))
        $errors[] = "Dealer name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Valid email is required.";
    if (empty($password))
        $errors[] = "Password is required.";
    if (empty($contact) || !is_numeric($contact) || strlen($contact) != 10)
        $errors[] = "Valid 10-digit contact number is required.";
    if (empty($address))
        $errors[] = "Address is required.";

    // Check for duplicate email
    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists. Please use a different email.";
    }
    $stmt->close();

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
    } else {
        $sql = "INSERT INTO users (username, email, password, contact, address, created_at, role) 
                VALUES (?, ?, ?, ?, ?, ?, 'dealer')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $email, $hashed_password, $contact, $address, $created_at);

        try {
            if ($stmt->execute()) {
                $_SESSION['message'] = "Dealer $username with email: '$email' & password: '$password' created successfully. These credentials can be used for login.";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error creating dealer: " . $e->getMessage();
        }

        $stmt->close();
    }

    $conn->close();
    header("Location: create-dealer.php");
    exit();
}

if (isset($_SESSION['form_data'])) {
    $username = $_SESSION['form_data']['dealer-name'];
    $email = $_SESSION['form_data']['email'];
    $password = $_SESSION['form_data']['password'];
    $contact = $_SESSION['form_data']['contact_number'];
    $address = $_SESSION['form_data']['address'];
    unset($_SESSION['form_data']);
} else {
    $username = "";
    $email = "";
    $password = "";
    $contact = "";
    $address = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Dealer</title>
    <link rel="icon" type="image/webp" href="logo.webp" />
    <link rel="stylesheet" href="base.css" />
    <link rel="stylesheet" href="create-dealer.css" />
</head>

<body>
    <header>
        <div class="header-top">
            <a href="./">
                <h1>East Coast Railway</h1>
                <h1>Closed User Group</h1>
            </a>
        </div>
    </header>

    <main>
        <section id="create-dealer">
            <div class="heading-container">
                <button class="back-btn" onclick="window.location.href = './admin-page.php'"><img
                        src="icon/back-button.webp" alt="back button"></button>
                <h2 class="heading">Create Dealer</h2>
            </div>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<p class='session-message success'>" . $_SESSION['message'] . "</p>";
                unset($_SESSION['message']);
            }
            if (isset($_SESSION['errors'])) {
                echo "<div class='session-message error'>";
                foreach ($_SESSION['errors'] as $error) {
                    echo "<p>$error</p>";
                }
                echo "</div>";
                unset($_SESSION['errors']);
            }
            ?>
            <form class="form_container" action="create-dealer.php" method="post">
                <div class="input_box long-input">
                    <label for="dealer-name">Dealer Name</label>
                    <input type="text" id="dealer-name" name="dealer-name" placeholder="Enter Dealer Name"
                        value="<?php echo htmlspecialchars($username); ?>" required />
                </div>
                <div class="input_box">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email"
                        value="<?php echo htmlspecialchars($email); ?>" required />
                </div>
                <div class="input_box">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password"
                        value="<?php echo htmlspecialchars($password); ?>" required />
                </div>
                <div class="input_box">
                    <label for="contact_number">Contact Number</label>
                    <input type="number" id="contact_number" name="contact_number" placeholder="Enter Contact Number"
                        value="<?php echo htmlspecialchars($contact); ?>" required />
                </div>
                <div class="input_box">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter Address"
                        value="<?php echo htmlspecialchars($address); ?>" required />
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
