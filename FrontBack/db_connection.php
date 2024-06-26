<?php

session_start(); 

date_default_timezone_set('Asia/Kolkata');

$servername = "localhost";
$username = "root";
$password = ""; // your database password
$dbname = "raildb";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
