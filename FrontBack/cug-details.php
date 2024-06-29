<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CUG Details</title>
	<link rel="stylesheet" href="base.css">
	<link rel="stylesheet" href="cug-details.css">
</head>

<body>
	<header>
		<div class="header-top">
			<h1>East Coast Railway</h1>
			<h1>Closed User Group</h1>
		</div>
	</header>
	<main>
		<section id="cug-details">
			<div class="heading-container">
				<button class="back-btn" onclick="window.location.href = 'admin-page.html'"><img
						src="https://img.icons8.com/ios/32/long-arrow-left.png" alt="back button"></button>
				<h2 class="heading">CUG Details</h2>
			</div>
			<table>
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
				<tbody>
					<?php

					// Include database connection script
					include 'db_connect.php';

					$sql = "SELECT * FROM cugdetails";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
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

							$created_at = new DateTime($row["created_at"]);
							$updated_at = new DateTime($row["updated_at"]);

							echo "<td>" . $created_at->format('g:i A \o\n jS M, y') . "</td>";
							echo "<td>" . $updated_at->format('g:i A \o\n jS M, y') . "</td>";
							echo "</tr>";
						}
					} else {
						echo "<tr><td colspan='12'>No records found</td></tr>";
					}

					$conn->close();
					?>
				</tbody>
			</table>
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