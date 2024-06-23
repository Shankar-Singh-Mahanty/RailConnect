<?php
// Set the default time zone
date_default_timezone_set('Asia/Kolkata');

// Database connection parameters
$servername = "sql310.infinityfree.com";
$username = "if0_36776697";
$password = "ynM5koL6VnHkLYa";
$dbname = "if0_36776697_raildb";

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
        echo "New dealer created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
