<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deactivation of CUG Number</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="deactivate-cug.css">
</head>

<body>
    <header>
        <div class="header-top">
            <h1>East Coast Railway</h1>
            <h1>Closed User Group</h1>
        </div>
    </header>
    <div class="heading-container">
        <button class="back-btn" onclick="window.location.href = './dealer-page.html'"><img
                src="https://img.icons8.com/ios/32/long-arrow-left.png" alt="back button"></button>
        <h2 class="heading">Deactivate CUG</h2>
    </div>
    <main class="main-content">

        <form class="cug-form" method="POST" action="deactivate-cug.php">
            <div class="form-group">
                <label for="cugNo" class="cug-no-label">CUG NO</label>
                <input type="text" id="cugNo" name="cugNo" class="cug-no-input" placeholder="Enter CUG No." required>
                <button type="submit" class="submit-button">GO</button>
            </div>
        </form>
    </main>
    <?php

    // Include database connection script
    include 'db_connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['deactivate'])) {
            $cug_no = $_POST['cugNo'];

            $update_sql = "UPDATE cugdetails SET status = 'Inactive' WHERE cug_number = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("s", $cug_no);

            if ($stmt->execute()) {
                echo "<p class='session-message'>CUG number $cug_no deactivated successfully.</p>";
            } else {
                echo "<p class='session-message'>Error deactivating CUG number: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            $cug_no = $_POST['cugNo'];

            $sql = "SELECT * FROM cugdetails WHERE cug_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $cug_no);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                echo "<form method='POST' action='deactivate-cug.php'>";
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
                                    <th>Created At</th>
                                    <th>Updated At</th>
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
                echo "<td>" . $row["created_at"] . "</td>";
                echo "<td>" . $row["updated_at"] . "</td>";
                echo "</tr>
                        </tbody>
                        </table>";
            } else {
                echo "<p>No records found</p>";
            }

            $stmt->close();
        }
    }

    $conn->close();
    ?>
</body>

</html>