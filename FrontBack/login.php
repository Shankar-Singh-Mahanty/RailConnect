<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="base.css" />
    <link rel="stylesheet" href="login.css" />
</head>
<body>
    <form action="login_process.php" method="post" class="login">
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
