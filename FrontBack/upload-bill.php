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
                <button class="back-btn" onclick="window.location.href = './admin-page.html'">
                    <img src="icon/back-button.webp" alt="back button">
                </button>
                <h2 class="heading">Upload Bill</h2>
            </div>
            <form class="form_container" action="" method="post" enctype="multipart/form-data">
                <div class="input_box long-input">
                    <label for="cugno">Upload CUG Bill</label>
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