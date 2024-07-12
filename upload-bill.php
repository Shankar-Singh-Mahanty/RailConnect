<?php
include 'authenticate.php';
checkUser("admin");


include 'db_connect.php';

// Load the Excel workbook
require 'phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['bill_file']) && $_FILES['bill_file']['error'] === UPLOAD_ERR_OK) {
        // Retrieve file information
        $fileTmpPath = $_FILES['bill_file']['tmp_name'];
        $fileName = $_FILES['bill_file']['name'];
        $fileSize = $_FILES['bill_file']['size'];
        $fileType = $_FILES['bill_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Define allowed file extensions
        $allowedfileExtensions = ['xlsx', 'xls'];

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

                // Get Bill Month and Year
                $bill_month_from_form = $_POST['bill_month'];
                $bill_year_from_form = $_POST['bill_year'];

                // Delete existing entries for the specified month and year
                $deleteSql = "DELETE FROM bills WHERE bill_month = ? AND bill_year = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("ii", $bill_month_from_form, $bill_year_from_form);
                $deleteStmt->execute();

                // Loop through each row of the worksheet
                for ($row = 2; $row <= $highestRow; $row++) {
                    // Get row data as array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $sheet->getHighestColumn() . $row, NULL, TRUE, FALSE);

                    // Prepare values for insertion
                    $cug_number = $rowData[0][0];
                    $periodic_charge = $rowData[0][1];
                    $usage_amount = $rowData[0][2];
                    $data_amount = $rowData[0][3];
                    $voice = $rowData[0][4];
                    $video = $rowData[0][5];
                    $sms = $rowData[0][6];
                    $vas = $rowData[0][7];
                    $bill_year = $bill_year_from_form;
                    $bill_month = $bill_month_from_form;

                    // Skip the row if CUG number is null
                    if (empty($cug_number)) {
                        continue;
                    }

                    // Prepare the SQL statement
                    $sql = "INSERT INTO bills (cug_number, periodic_charge, usage_amount, data_amount, voice, video, sms, vas, bill_year, bill_month) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("idddddddii", $cug_number, $periodic_charge, $usage_amount, $data_amount, $voice, $video, $sms, $vas, $bill_year, $bill_month);

                    try {
                        $result = $stmt->execute();

                        if ($result === TRUE) {
                            // echo "Record inserted successfully<br>";
                        } else {
                            $_SESSION['message'] .= '<div class="message error">Error inserting record: ' . $stmt->error . '</div>';
                        }
                    } catch (Exception $e) {
                        // Handle specific foreign key constraint error
                        if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                            $_SESSION['message'] .= '<div class="message error">The CUG no. ' . $cug_number . ' is not present in the database.</div>';
                        } elseif (strlen((string) $cug_number) != 10) {
                            $_SESSION['message'] .= '<div class="message error"> CUG No. ' . $cug_number . ' has ' . strlen((string) $cug_number) . ' digits (should be 10 digits)</div>';
                        } else {
                            $_SESSION['message'] .= '<div class="message error">Exception caught: ' . $e->getMessage() . '</div>';
                        }
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

                $stmt->close();
            } else {
                $_SESSION['message'] .= '<div class="message error">There was some error moving the file to store directory. Please make sure the store directory is writable by the web server.</div>';
            }
        } else {
            $_SESSION['message'] .= '<div class="message error">Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions) . '</div>';
        }
    } else {
        $_SESSION['message'] .= '<div class="message error">There is no file uploaded or there is an error with the file upload.</div>';
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
    <title>Upload CUG Bill</title>
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
                <button class="back-btn" onclick="window.location.href = './admin-page.php'">
                    <img src="icon/back-button.webp" alt="back button">
                </button>
                <h2 class="heading">Upload Bill</h2>
            </div>
            <form class="form_container" action="" method="post" enctype="multipart/form-data">
                <div class="input_box">
                    <label for="bill_month">Bill Month</label>
                    <select id="bill_month" name="bill_month" required>
                        <option value="" disabled selected>Select Month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="input_box">
                    <label for="bill_year">Select Year:</label>
                    <select id="bill_year" name="bill_year" required>
                        <?php
                        // Generate options for years, assuming a range of 5 years from current year
                        $currentYear = date('Y');
                        for ($i = 0; $i < 50; $i++) {
                            $year = $currentYear - $i;
                            echo '<option value="' . $year . '">' . $year . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="input_box long-input">
                    <label for="bill_file">Upload CUG Bill</label>
                    <input type="file" id="bill_file" name="bill_file" required />
                </div>
                <button class="submit-button" type="submit">Submit</button>
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


            // Onload button loading
            const formElement = document.querySelector(".form_container");
            const submitButton = document.querySelector(".submit-button");

            formElement.addEventListener('submit', () =>
            {
                submitButton.textContent = "Loading...";
                submitButton.disabled = true;
            })

        });
    </script>
</body>

</html>