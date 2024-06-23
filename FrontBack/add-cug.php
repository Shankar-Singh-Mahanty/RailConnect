<?php
// Database configuration
$servername = "sql310.infinityfree.com";
$username = "if0_36776697";
$password = "ynM5koL6VnHkLYa";
$dbname = "if0_36776697_raildb"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$cugNo = $_POST['cugNo'];
$name = $_POST['name'];
$designation = $_POST['designation'];
$division = $_POST['division'];
$department = $_POST['department'];
$billUnit = $_POST['billUnit'];
$allocation = $_POST['allocation'];
$employeeStatus = $_POST['employeeStatus'];
$plan = $_POST['plan'];
$action = $_POST['action']; // This will be either "ACTIVATE" or "DEACTIVATE"

// Determine status based on action
$status = ($action === 'ACTIVATE') ? 'Active' : 'Inactive';

// Insert or update data in the database
$sql = "INSERT INTO cug_details (cug_no, name, designation, division, department, bill_unit, allocation, employee_status, plan, status) 
        VALUES ('$cugNo', '$name', '$designation', '$division', '$department', '$billUnit', '$allocation', '$employeeStatus', '$plan', '$status')
        ON DUPLICATE KEY UPDATE 
        name='$name', designation='$designation', division='$division', department='$department', bill_unit='$billUnit', allocation='$allocation', employee_status='$employeeStatus', plan='$plan', status='$status'";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
