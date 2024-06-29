<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload CUG Bill</title>
    <link rel="stylesheet" href="base.css" />
    <link rel="stylesheet" href="upload-bill.css" />
    <style>
        .custom-file-input {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .custom-file-input input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .custom-file-input::before {
            content: 'Choose file';
            display: inline-block;
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 5px 10px;
            cursor: pointer;
        }

        .custom-file-input input[type="file"]:hover + .file-name,
        .custom-file-input input[type="file"]:focus + .file-name {
            border-color: #007bff;
        }

        .file-name {
            display: inline-block;
            margin-left: 10px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-top">
            <h1>East Coast Railway</h1>
            <h1>Closed User Group</h1>
        </div>
    </header>

    <main>
        <section id="create-dealer">
            <div class="heading-container">
                <button class="back-btn" onclick="window.location.href = './admin-page.html'"><img
                        src="https://img.icons8.com/ios/32/long-arrow-left.png" alt="back button"></button>
                <h2 class="heading">Upload Bill</h2>
            </div>
            <form class="form_container" action="" method="post" enctype="multipart/form-data">
                <div class="input_box long-input custom-file-input">
                    <label for="cugno">Upload CUG Bill</label>
                    <input type="file" id="cugno" name="cugno" required onchange="showFileName()" />
                    <span id="file-name" class="file-name"></span>
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
                    $allowedfileExtensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv');

                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        // Directory where uploaded files will be saved (@stored path)
                        $storedFileDir = 'D:\ECoST\Uploaded_Files\ecost_rail_';
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
                            echo '<div class="message error">There was some error moving the file to store directory. Please make sure the store directory is writable by web server.</div>';
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

    <script>
        function showFileName() {
            const input = document.getElementById('cugno');
            const fileName = input.files[0].name;
            const fileNameSpan = document.getElementById('file-name');
            fileNameSpan.textContent = fileName;
        }
    </script>
</body>

</html>
