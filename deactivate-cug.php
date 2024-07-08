<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deactivation of CUG Number</title>
    <link rel="icon" type="image/webp" href="logo.webp" />
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="deactivate-cug.css">
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
    <div class="heading-container">
        <button class="back-btn" onclick="window.location.href = './dealer-page.html'"><img src="icon/back-button.webp"
                alt="back button"></button>
        <h2 class="heading">Deactivate CUG</h2>
    </div>
    <main class="main-content">
        <form class="cug-form" method="POST" action="">
            <div class="form-group">
                <label for="cugNo" class="cug-no-label">CUG NO</label>
                <input type="text" id="cugNo" name="cugNo" class="cug-no-input" placeholder="Enter CUG No." required>
                <button type="submit" name="search" class="submit-button">GO</button>
            </div>
        </form>
    </main>
    <?php
    // Include database connection script
    include 'db_connect.php';

    $cug_no = null;
    $message = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
        $cug_no = $_POST['cugNo'];

        // Select the record to display it
        $select_sql = "SELECT * FROM cugdetails_transaction WHERE cug_number = ?";
        $stmt = $conn->prepare($select_sql);
        $stmt->bind_param("s", $cug_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            echo "<form method='POST' action=''>";
            echo "<input type='hidden' name='cugNo' value='$cug_no'>";
            echo "<button type='submit' name='deactivate' class='deactivate-button'>Deactivate</button>";
            echo "</form>";

            echo "<table>
                    <thead>
                        <tr>
                            <th>CUG Number</th>
                            <th>Employee Number</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Unit</th>
                            <th>Department</th>
                            <th>Bill Unit No</th>
                            <th>Allocation</th>
                            <th>Operator</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Activate from</th>
                            <th>Inactive at</th>
                        </tr>
                    </thead>
                    <tbody>";
            echo "<tr>";
            echo "<td>" . $row["cug_number"] . "</td>";
            echo "<td>" . $row["emp_number"] . "</td>";
            echo "<td>" . $row["empname"] . "</td>";
            echo "<td>" . $row["designation"] . "</td>";
            echo "<td>" . $row["unit"] . "</td>";
            echo "<td>" . $row["department"] . "</td>";
            echo "<td>" . $row["bill_unit_no"] . "</td>";
            echo "<td>" . $row["allocation"] . "</td>";
            echo "<td>" . $row["operator"] . "</td>";
            echo "<td>" . $row["plan"] . "</td>";
            echo "<td>" . $row["status"] . "</td>";
            echo "<td>" . $row["activate_from"] . "</td>";
            echo "<td>" . $row["inactive_at"] . "</td>";
            echo "</tr>
                </tbody>
                </table>";
        } else {
            $message = "No records found";
        }

        $stmt->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deactivate'])) {
        $cug_no = $_POST['cugNo'];

        // Check if the status is already 'Inactive'
        $select_status_sql = "SELECT status FROM cugdetails_transaction WHERE cug_number = ?";
        $stmt_status = $conn->prepare($select_status_sql);
        $stmt_status->bind_param("s", $cug_no);
        $stmt_status->execute();
        $result_status = $stmt_status->get_result();

        if ($result_status->num_rows > 0) {
            $row_status = $result_status->fetch_assoc();
            if ($row_status['status'] == 'Inactive') {
                $message = "CUG number $cug_no is already deactivated.";
            } else {
                // Begin transaction
                $conn->begin_transaction();

                try {
                    // Delete record from cugdetails table
                    $delete_sql = "DELETE FROM cugdetails WHERE cug_number = ?";
                    $stmt_delete = $conn->prepare($delete_sql);
                    $stmt_delete->bind_param("s", $cug_no);

                    // Update the status in cugdetails_transaction table
                    $update_sql = "UPDATE cugdetails_transaction SET status = 'Inactive', inactive_at = NOW() WHERE cug_number = ?";
                    $stmt_update = $conn->prepare($update_sql);
                    $stmt_update->bind_param("s", $cug_no);

                    // Execute both statements and commit if both succeed
                    if ($stmt_update->execute() && $stmt_delete->execute()) {
                        // Commit transaction
                        $conn->commit();
                        $message = "CUG number $cug_no deactivated and deleted successfully.";
                    } else {
                        // Rollback transaction if either statement fails
                        $conn->rollback();
                        $message = "Error deactivating and deleting CUG number.";
                    }

                } catch (Exception $e) {
                    // Rollback transaction in case of error
                    $conn->rollback();
                    $message = "Error deactivating and deleting CUG number: " . $e->getMessage();
                }
            }
        } else {
            $message = "CUG number not found.";
        }

        $stmt_status->close();
    }

    // Display the message if available
    if ($message) {
        echo "<p class='session-message'>" . htmlspecialchars($message) . "</p>";
    }

    // Close the database connection
    $conn->close();
    ?>
</body>

</html>
