<?php

// Include database connection file

include 'authenticate.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
checkUser($role);

include 'db_connect.php';
// Load the Excel workbook
require 'phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['cugno']) && $_FILES['cugno']['error'] === UPLOAD_ERR_OK) {
        // Retrieve file information
        $fileTmpPath = $_FILES['cugno']['tmp_name'];
        $fileName = $_FILES['cugno']['name'];
        $fileSize = $_FILES['cugno']['size'];
        $fileType = $_FILES['cugno']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Define allowed file extensions
        $allowedfileExtensions = array('xlsx', 'xls');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where uploaded files will be saved (relative to the project root)
            $storedFileDir = 'uploads/';
            $dest_path = $storedFileDir . $fileName;

            // Move the file to the directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {

                // Load the Excel file
                $spreadsheet = IOFactory::load($dest_path);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();

                // Loop through each row of the worksheet
                for ($row = 2; $row <= $highestRow; $row++) {
                    // Get row data as array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $sheet->getHighestColumn() . $row, NULL, TRUE, FALSE);

                    // Prepare values for insertion/update
                    $cug_number = $rowData[0][0];
                    $emp_number = $rowData[0][1];
                    $empname = $rowData[0][2];
                    $designation = $rowData[0][3];
                    $unit = $rowData[0][4];
                    $department = $rowData[0][5];
                    $bill_unit_no = $rowData[0][6];
                    $allocation = $rowData[0][7];
                    $operator = $rowData[0][8];
                    $plan = $rowData[0][9];
                    $status = 'Active'; // Default status

                    // Skip the row if CUG number is null
                    if (empty($cug_number)) {
                        continue;
                    }

                    // Check if the CUG number already exists in cugdetails table
                    $checkSql = "SELECT COUNT(*) AS count FROM cugdetails WHERE cug_number = ?";
                    $stmt = $conn->prepare($checkSql);
                    $stmt->bind_param("s", $cug_number);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $countRow = $result->fetch_assoc();

                    if ($countRow['count'] > 0) {
                        // Update the existing record in cugdetails table
                        $sql = "UPDATE cugdetails SET emp_number = ?, empname = ?, designation = ?, unit = ?, department = ?, bill_unit_no = ?, allocation = ?, operator = ?, plan = ?, status = ? WHERE cug_number = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssssssssss", $emp_number, $empname, $designation, $unit, $department, $bill_unit_no, $allocation, $operator, $plan, $status, $cug_number);
                    } else {
                        // Insert a new record in cugdetails table
                        $sql = "INSERT INTO cugdetails (cug_number, emp_number, empname, designation, unit, department, bill_unit_no, allocation, operator, plan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssssssssss", $cug_number, $emp_number, $empname, $designation, $unit, $department, $bill_unit_no, $allocation, $operator, $plan, $status);
                    }

                    // Execute SQL statement for cugdetails table
                    try {
                        $stmt->execute();
                    } catch (Exception $e) {
                        if (strlen((string) $cug_number) != 10) {
                            $_SESSION['message'] .= '<div class="message error"> CUG No. ' . $cug_number . ' has ' . strlen((string) $cug_number) . ' digits (should be 10 digits)</div>';
                        } else {
                            $_SESSION['message'] .= '<div class="message error">Error : ' . $e->getMessage() . ' for CUG No. ' . $cug_number . '</div>';
                        }
                        continue; // Skip to the next iteration of the loop
                    }

                    // Check if the CUG number already exists in cugdetails_transaction table
                    $checkTransactionSql = "SELECT COUNT(*) AS count FROM cugdetails_transaction WHERE cug_number = ?";
                    $stmtTransaction = $conn->prepare($checkTransactionSql);
                    $stmtTransaction->bind_param("s", $cug_number);
                    $stmtTransaction->execute();
                    $resultTransaction = $stmtTransaction->get_result();
                    $countTransactionRow = $resultTransaction->fetch_assoc();

                    if ($countTransactionRow['count'] > 0) {
                        // Update the existing record in cugdetails_transaction table
                        $sqlTransaction = "UPDATE cugdetails_transaction SET emp_number = ?, empname = ?, designation = ?, unit = ?, department = ?, bill_unit_no = ?, allocation = ?, operator = ?, plan = ?, status = ? WHERE cug_number = ?";
                        $stmtTransaction = $conn->prepare($sqlTransaction);
                        $stmtTransaction->bind_param("sssssssssss", $emp_number, $empname, $designation, $unit, $department, $bill_unit_no, $allocation, $operator, $plan, $status, $cug_number);
                    } else {
                        // Insert a new record in cugdetails_transaction table
                        $sqlTransaction = "INSERT INTO cugdetails_transaction (cug_number, emp_number, empname, designation, unit, department, bill_unit_no, allocation, operator, plan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmtTransaction = $conn->prepare($sqlTransaction);
                        $stmtTransaction->bind_param("sssssssssss", $cug_number, $emp_number, $empname, $designation, $unit, $department, $bill_unit_no, $allocation, $operator, $plan, $status);
                    }

                    // Execute SQL statement for cugdetails_transaction table
                    try {
                        $stmtTransaction->execute();
                    } catch (Exception $e) {
                        $_SESSION['message'] .= '<div class="message error">Exception caught while updating cugdetails_transaction table: ' . $e->getMessage() . '</div>';
                        continue; // Skip to the next iteration of the loop
                    }
                }

                // Insert file info into database
                $query = "INSERT INTO uploaded_files (file_name, file_size, file_type, stored_path) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("siss", $fileName, $fileSize, $fileType, $dest_path);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['message'] .= '<div class="message success">File is successfully uploaded and stored in the database.</div>';
                } else {
                    $_SESSION['message'] .= '<div class="message error">Failed to store file info in the database.</div>';
                }
            } else {
                $_SESSION['message'] .= '<div class="message error">Error moving uploaded file to destination directory.</div>';
            }
        } else {
            $_SESSION['message'] .= '<div class="message error">Invalid file extension. Allowed extensions are xlsx, xls.</div>';
        }
    } else {
        $_SESSION['message'] .= '<div class="message error">Error uploading file. Please try again.</div>';
    }

    // Redirect to the same page
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload CUG Numbers</title>
    <link rel="icon" type="image/webp" href="logo.webp" />
    <link rel="stylesheet" href="base.css" />
    <link rel="stylesheet" href="upload-bill.css" />
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
                <button class="back-btn" id="roleRedirectButton" data-role="<?php echo $role; ?>">
                    <img src="icon/back-button.webp" alt="back button">
                </button>
                <h2 class="heading">Upload CUG Numbers</h2>
            </div>
            <form class="form_container" action="" method="post" enctype="multipart/form-data">
                <div class="input_box long-input">
                    <label for="cugno">Upload CUG Numbers</label>
                    <input type="file" id="cugno" name="cugno" required />
                </div>
                <button class="submit-button" type="submit">
                    Submit
                </button>
            </form>

            <?php
            // Display the session message if it exists
            if (isset($_SESSION['message'])) {
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Clear the message after displaying
            }
            ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 East Coast Railway. All rights reserved.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function ()
        {
            const errorMessages = document.querySelectorAll(".error");
            const moreButton = document.createElement("button");
            moreButton.textContent = "More Errors";
            moreButton.classList.add("more-button");
            let firstFiveErrors = Array.from(errorMessages).slice(0, 5);
            let remainingErrors = Array.from(errorMessages).slice(5);

            remainingErrors.forEach(error => error.classList.add("hidden"));

            if (errorMessages.length > 5)
            {
                document.querySelector("#create-dealer").appendChild(moreButton);
            }

            moreButton.addEventListener("click", function ()
            {
                remainingErrors.forEach(error => error.classList.remove("hidden"));
                moreButton.remove();
            });

            const redirectButton = document.getElementById("roleRedirectButton");
            const userRole = redirectButton.getAttribute("data-role");

            // Role based Redirection -------------------------
            redirectButton.addEventListener("click", function ()
            {
                if (userRole === 'admin')
                {
                    window.location.href = 'admin-page.php';
                } else if (userRole === 'dealer')
                {
                    window.location.href = 'dealer-page.php';
                } else
                {
                    alert("Error: Unexpected role. Please login again.");
                }
            });

            // Onload button loading
            const formElement = document.querySelector(".form_container");
            const submitButton = document.querySelector(".submit-button");

            formElement.addEventListener('submit', () =>
            {
                submitButton.textContent = "Loading...";
                submitButton.disabled = true;
            });
        });
    </script>
</body>

</html>