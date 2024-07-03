<?php
session_start();

// Include database connection script
include 'db_connect.php';

// Retrieve form data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $user_email = $_POST['email'];
    $user_password = $_POST['password'];
    // Hash the password using SHA-256
    $hashed_password = hash('sha256', $user_password);

    // Prepare and execute SQL statement
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_email, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        // Set session variable
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: admin-page.html");
        } else if ($user['role'] == 'dealer') {
            header("Location: dealer-page.html");
        } else {
            // Unexpected role
            echo "Error: Unexpected role.";
        }
    } else {
        // Invalid credentials
        $_SESSION['errorMessage'] = "Invalid email or password! Please try again.";
        header("Location: login.php");
        exit();
    }

    // Close connections
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="icon" type="image/webp" href="logo.webp" />
    <link rel="stylesheet" href="base.css" />
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <form action="login.php" method="post" class="login">
        <a href="./" class="back-home">Back to Home Page</a>
        <h2>Welcome!</h2>
        <p>Please log in</p>

        <input type="email" placeholder="Email" name="email" required />
        <input type="password" placeholder="Password" name="password" required />
        <input type="submit" value="Log In" />
        <?php
        if (isset($_SESSION['errorMessage'])) {
            echo '<div class="error">' . $_SESSION['errorMessage'] . '</div>';
            unset($_SESSION['errorMessage']);
        }
        ?>
    </form>
</body>

</html>