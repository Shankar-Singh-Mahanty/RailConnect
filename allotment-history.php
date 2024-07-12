<?php
include 'authenticate.php';
checkUser("admin");
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Allotment History</title>
	<link rel="icon" type="image/webp" href="logo.webp" />
	<link rel="stylesheet" href="base.css">
	<link rel="stylesheet" href="allotment-history.css">
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
		<button class="back-btn" onclick="window.location.href = './admin-page.php'"><img src="icon/back-button.webp"
				alt="back button"></button>
		<h2 class="heading">Allotment History</h2>
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

	$message = null;

	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
		$cug_no = $_POST['cugNo'];

		// Select the record to display it, ordered by activate_from date
		$select_sql = "SELECT * FROM cugdetails_transaction WHERE cug_number = ? ORDER BY activate_from DESC";
		$stmt = $conn->prepare($select_sql);
		$stmt->bind_param("s", $cug_no);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$_SESSION['search_results'] = $result->fetch_all(MYSQLI_ASSOC);
		} else {
			$_SESSION['message'] = "No records found";
		}

		$stmt->close();

		// Redirect to the same page to prevent form resubmission
		header("Location: " . $_SERVER['PHP_SELF']);
		exit();
	}

	// Display the message if available
	if (isset($_SESSION['message'])) {
		echo "<p class='session-message'>" . htmlspecialchars($_SESSION['message']) . "</p>";
		unset($_SESSION['message']);
	}

	// Display the search results if available
	if (isset($_SESSION['search_results'])) {
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

		foreach ($_SESSION['search_results'] as $row) {
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
			echo "</tr>";
		}

		echo "</tbody></table>";
		unset($_SESSION['search_results']);
	}

	// Close the database connection
	$conn->close();
	?>
	<footer>
		<p>&copy; 2024 East Coast Railway. All rights reserved.</p>
		<div class="footer-links">
			<a href="#">Privacy Policy</a>
			<a href="#">Terms of Service</a>
		</div>
	</footer>
</body>

</html>