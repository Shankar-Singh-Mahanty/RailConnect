<?php
// Include database connection file
include 'db_connect.php';

// Load the Excel workbook
require 'phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                <button class="back-btn" onclick="window.location.href = './admin-page.html'">
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
            // Include database connection file
            include 'db_connect.php';

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

                                // Prepare values for insertion
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
                                $status = '$rowData[0][10]';

                                // Prepare the SQL statement
                                $sql = "INSERT INTO cugdetails (cug_number, emp_number, empname, designation, unit, department, bill_unit_no, allocation, operator, plan, status) 
                                                                    VALUES ('$cug_number', '$emp_number', '$empname', '$designation', '$unit', '$department', '$bill_unit_no', '$allocation', '$operator', '$plan','Active')";

                                // Attempt to execute the SQL statement
                                try {
                                    $result = $conn->query($sql);

                                    if ($result === TRUE) {
                                        // echo "Record inserted successfully<br>";
                                    } else {
                                        echo '<div class="message error">Error inserting record: ' . $conn->error . '</div>';
                                        // echo "Error inserting record: " . $conn->error . "<br>";
                                    }
                                } catch (Exception $e) {
                                    // Handle any exceptions, such as duplicate key errors
                                    echo '<div class="message error">Exception caught: ' . $e->getMessage() . '</div>';
                                    // echo "Exception caught: " . $e->getMessage() . "<br>";
                                    continue; // Skip to the next iteration of the loop
                                }
                            }


                            // Insert file info into database
                            $query = "INSERT INTO uploaded_files (file_name, file_size, file_type, stored_path) VALUES (?, ?, ?, ?)";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("siss", $fileName, $fileSize, $fileType, $dest_path);
                            $stmt->execute();

                            if ($stmt->affected_rows > 0) {
                                echo '<div class="message success">File is successfully uploaded and stored in the database.</div>';
                            } else {
                                echo '<div class="message error">Failed to store file info in the database.</div>';
                            }

                            $stmt->close();
                        } else {
                            echo '<div class="message error">There was some error moving the file to store directory. Please make sure the store directory is writable by the web server.</div>';
                        }
                    } else {
                        echo '<div class="message error">Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions) . '</div>';
                    }
                } else {
                    echo '<div class="message error">There is no file uploaded or there is an error with the file upload.</div>';
                }
            }

            // Close database connection
            $conn->close();
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
</body>

</html>