<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "root123";
$dbname = "admindb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['NAME'];
    $cug_no = $_POST['CUG_NO'];
    $emp_no = $_POST['EMP_NO'];
    $designation = $_POST['DESIGNATION'];
    $division = $_POST['DIVISION'];
    $department = $_POST['department'];
    $bill_unit = $_POST['BILL_UNIT'];
    $allocation = $_POST['ALLOCATION'];
    $operator = $_POST['operator'];
    $plan = $_POST['plan'];
    $status = "Active"; // Default status

    // Validate required fields
    if (empty($name) || empty($cug_no) || empty($emp_no) || empty($designation) || empty($division) || $department == "default" || empty($bill_unit) || empty($allocation) || $operator == "default" || empty($plan)) {
        echo "All fields are required.";
    } else {
        // Validate plan option
        $allowed_plans = ['Plan A', 'Plan B', 'Plan C'];
        if (!in_array($plan, $allowed_plans)) {
            echo "Invalid plan selected.";
            exit; // Stop further execution
        }

        // Map plan value to database ENUM values ('A', 'B', 'C')
        $plan_value = '';
        switch ($plan) {
            case 'Plan A':
                $plan_value = 'A';
                break;
            case 'Plan B':
                $plan_value = 'B';
                break;
            case 'Plan C':
                $plan_value = 'C';
                break;
            default:
                echo "Invalid plan selected.";
                exit; // Stop further execution
        }

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO CUGDetails (cug_number, emp_number, empname, designation, division, department, bill_unit, allocation, operator, plan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssdssss", $cug_no, $emp_no, $name, $designation, $division, $department, $bill_unit, $allocation, $operator, $plan_value, $status);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New CUG allocated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>
