<?php
include 'authenticate.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
checkUser("admin");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Passing Register</title>

    <link rel="icon" type="image/webp" href="logo.webp" />
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="bill-passing-register.css">
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
        <section id="bill-details">
            <div class="heading-container">
                <button class="back-btn" id="roleRedirectButton" data-role="<?php echo $role; ?>">
                    <img src="icon/back-button.webp" alt="back button">
                </button>
                <h2 class="heading">Bill Passing Register</h2>
            </div>

            <!-- Form for selecting month and year -->
            <form class="form_container" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="input_box">
                    <label for="month">Select Month:</label>
                    <select id="month" name="month">
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
                    <label for="year">Select Year:</label>
                    <select id="year" name="year">
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
                <button class="action-button long-input" type="submit">Filter</button>
            </form>

            <?php
            // Include database connection file
            include 'db_connect.php';

            // Default month and year to current month and year
            $selectedMonth = isset($_GET['month']) ? $_GET['month'] : "01";
            $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

            // Fetch GST percentages
            $gst_query = "SELECT cgst_percentage, sgst_percentage FROM gst LIMIT 1";
            $gst_result = $conn->query($gst_query);
            $gst_data = $gst_result->fetch_assoc();
            $cgst_percentage = $gst_data['cgst_percentage'];
            $sgst_percentage = $gst_data['sgst_percentage'];

            // SQL query to fetch and aggregate data for selected month and year
            $query = "
                SELECT 
                    c.unit,
                    c.department,
                    GROUP_CONCAT(DISTINCT CONCAT(b.bill_month, '-', b.bill_year) ORDER BY b.bill_year, b.bill_month ASC SEPARATOR ', ') AS bill_dates,
                    SUM(b.periodic_charge + b.usage_amount + b.data_amount + b.voice + b.video + b.sms + b.vas) AS total_amount
                FROM 
                    cugdetails c
                JOIN 
                    bills b ON c.cug_number = b.cug_number
                WHERE 
                    b.bill_month = '$selectedMonth' AND b.bill_year = '$selectedYear'
                GROUP BY 
                    c.unit, c.department
                ORDER BY 
                    c.unit, c.department;
            ";

            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                echo '<table border="1">';
                echo '<tr><th>Unit</th><th>Department</th><th>Bill Dates</th><th>Amount</th></tr>';

                $grand_total_amount = 0;
                $current_unit = '';
                $unit_total_amount = 0;

                while ($row = $result->fetch_assoc()) {
                    $total_amount = $row['total_amount'];
                    $grand_total_amount += $total_amount;

                    if ($current_unit != $row['unit']) {
                        if ($current_unit != '') {
                            // Print unit total for previous unit
                            echo '<tr class="unit-total-row">';
                            echo '<td colspan="3" style="text-align: right;">Total for ' . $current_unit . ':</td>';
                            echo '<td> Rs. ' . number_format($unit_total_amount, 2) . '</td>';
                            echo '</tr>';
                            $unit_total_amount = 0;
                        }

                        $current_unit = $row['unit'];
                        echo '<tr class="unit-row">';
                        echo '<td colspan="4">' . $current_unit . '</td>';
                        echo '</tr>';
                    }

                    $unit_total_amount += $total_amount;

                    echo '<tr>';
                    echo '<td></td>';
                    echo '<td>' . $row['department'] . '</td>';
                    echo '<td>' . $row['bill_dates'] . '</td>';
                    echo '<td> Rs. ' . number_format($total_amount, 2) . '</td>';
                    echo '</tr>';
                }

                // Print unit total for last unit
                echo '<tr class="unit-total-row">';
                echo '<td colspan="3" style="text-align: right;">Total for ' . $current_unit . ':</td>';
                echo '<td> Rs. ' . number_format($unit_total_amount, 2) . '</td>';
                echo '</tr>';

                echo '<tr class="transparent-row"><td colspan="4"><hr></td></tr>';

                $grand_total_cgst = ($grand_total_amount * $cgst_percentage) / 100;
                $grand_total_sgst = ($grand_total_amount * $sgst_percentage) / 100;
                $grand_total_payable = $grand_total_amount + $grand_total_cgst + $grand_total_sgst;

                echo '<tr>';
                echo '<td colspan="3" style="text-align: right;">Grand Total :</td>';
                echo '<td> Rs. ' . number_format($grand_total_amount, 2) . '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td colspan="3" style="text-align: right;">CGST ₹</td>';
                echo '<td> Rs. ' . number_format($grand_total_cgst, 2) . '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td colspan="3" style="text-align: right;">SGST ₹</td>';
                echo '<td> Rs. ' . number_format($grand_total_sgst, 2) . '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td colspan="3" style="text-align: right;">Total Payable :</td>';
                echo '<td> Rs. ' . number_format($grand_total_payable, 2) . '</td>';
                echo '</tr>';

                echo '</table>';
            } else {
                echo '<p class="session-message error">No data found for selected month and year.</p>';
            }

            // Close database connection
            $conn->close();
            ?>

            <form method="post" action="generate_pdf_bill.php">
                <input type="hidden" name="month" value="<?php echo $selectedMonth; ?>">
                <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
                <button class="action-button" type="submit" name="generate_pdf">Generate PDF</button>
            </form>
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
            // Role based Redirection -------------------------
            const redirectButton = document.getElementById("roleRedirectButton");
            const userRole = redirectButton.getAttribute("data-role");

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

            // Set month and year based on URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const month = urlParams.get('month');
            const year = urlParams.get('year');

            if (month)
            {
                document.getElementById('month').value = month;
            }

            if (year)
            {
                document.getElementById('year').value = year;
            }


        });
    </script>

</body>

</html>