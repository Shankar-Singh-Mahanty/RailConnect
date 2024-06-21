<?php
// Set the default time zone
date_default_timezone_set('Asia/Kolkata'); // Replace with your desired time zone

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "Shan@1506";
$dbname = "shandb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize user input
function sanitizeInput($data) {
    global $conn;
    return htmlspecialchars(mysqli_real_escape_string($conn, $data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST["dealer-name"]);
    $email = sanitizeInput($_POST["email"]);
    $password = password_hash(sanitizeInput($_POST["password"]), PASSWORD_BCRYPT); // Using bcrypt for password hashing
    $contact = sanitizeInput($_POST["contact_number"]);
    $address = sanitizeInput($_POST["address"]);
    $created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO Users (username, email, password, contact, address, created_at, role) 
            VALUES ('$username', '$email', '$password', '$contact', '$address', '$created_at', 'dealer')";

    if ($conn->query($sql) === TRUE) {
        echo "New dealer created successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
