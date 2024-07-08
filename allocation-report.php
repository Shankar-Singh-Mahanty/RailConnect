<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allocation Details</title>

    <link rel="icon" type="image/webp" href="logo.webp" />
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="allocation-report.css">
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
        <section id="allocation-details">
            <div class="heading-container">
                <button class="back-btn" id="roleRedirectButton" data-role="<?php echo $role; ?>">
                    <img src="icon/back-button.webp" alt="back button">
                </button>
                <h2 class="heading">Allocation Report</h2>
            </div>

            <?php
            // Include database connection file
            include 'db_connect.php';

            // Fetch GST percentages
            $gst_query = "SELECT cgst_percentage, sgst_percentage FROM gst LIMIT 1";
            $gst_result = $conn->query($gst_query);
            $gst_data = $gst_result->fetch_assoc();
            $cgst_percentage = $gst_data['cgst_percentage'];
            $sgst_percentage = $gst_data['sgst_percentage'];

            // SQL query to fetch and aggregate data
            $query = "
                SELECT 
                    c.allocation,
                    GROUP_CONCAT(DISTINCT CONCAT(b.bill_month, '-', b.bill_year) ORDER BY b.bill_year, b.bill_month ASC SEPARATOR ', ') AS bill_dates,
                    SUM(b.periodic_charge + b.usage_amount + b.data_amount + b.voice + b.video + b.sms + b.vas) AS total_amount
                FROM 
                    cugdetails c
                JOIN 
                    bills b ON c.cug_number = b.cug_number
                GROUP BY 
                    c.allocation
                ORDER BY 
                    c.allocation;
            ";

            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                echo '<table border="1">';
                echo '<tr><th>Allocation</th><th>Bill Dates</th><th>Amount</th></tr>';

                $grand_total_amount = 0;

                while ($row = $result->fetch_assoc()) {
                    $total_amount = $row['total_amount'];
                    $grand_total_amount += $total_amount;

                    echo '<tr>';
                    echo '<td>' . $row['allocation'] . '</td>';
                    echo '<td>' . $row['bill_dates'] . '</td>';
                    echo '<td> Rs. ' . number_format($total_amount, 2) . '</td>';
                    echo '</tr>';
                }

                $grand_total_cgst = ($grand_total_amount * $cgst_percentage) / 100;
                $grand_total_sgst = ($grand_total_amount * $sgst_percentage) / 100;
                $grand_total_payable = $grand_total_amount + $grand_total_cgst + $grand_total_sgst;

                echo '<tr>';
                echo '<td colspan="2" style="text-align: right;">Grand Total :</td>';
                echo '<td> Rs. ' . number_format($grand_total_amount, 2) . '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td colspan="2" style="text-align: right;">CGST ₹</td>';
                echo '<td> Rs. ' . number_format($grand_total_cgst, 2) . '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td colspan="2" style="text-align: right;">SGST ₹</td>';
                echo '<td> Rs. ' . number_format($grand_total_sgst, 2) . '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td colspan="2" style="text-align: right;">Total Payable :</td>';
                echo '<td> Rs. ' . number_format($grand_total_payable, 2) . '</td>';
                echo '</tr>';

                echo '</table>';
            } else {
                echo '<p>No data found.</p>';
            }

            // Close database connection
            $conn->close();
            ?>

            <form method="post" action="generate_pdf.php">
                <button type="submit" name="generate_pdf">Generate PDF</button>
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
        document.addEventListener("DOMContentLoaded", function () {
            // Role based Redirection -------------------------
            const redirectButton = document.getElementById("roleRedirectButton");
            const userRole = redirectButton.getAttribute("data-role");

            redirectButton.addEventListener("click", function () {
                if (userRole === 'admin') {
                    window.location.href = 'admin-page.html';
                } else if (userRole === 'dealer') {
                    window.location.href = 'dealer-page.html';
                } else {
                    alert("Error: Unexpected role. Please login again.");
                }
            });

        });
    </script>

</body>

</html>
