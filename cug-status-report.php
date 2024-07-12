<?php
include 'authenticate.php';
checkUser("admin");
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CUG Status Report</title>
	<link rel="icon" type="image/webp" href="logo.webp" />
	<link rel="stylesheet" href="base.css">
	<link rel="stylesheet" href="cug-status-report.css">
</head>

<body>
	<header>
		<div class="header-top">
			<a href="">
				<h1>East Coast Railway</h1>
				<h1>Closed User Group</h1>
			</a>
		</div>
	</header>
	<main>
		<section id="cug-status-report">
			<div class="heading-container">
				<button class="back-btn" onclick="window.location.href = 'admin-page.php'"><img
						src="icon/back-button.webp" alt="back button"></button>
				<h2 class="heading">CUG Status Report</h2>
			</div>
			<div class="filter-container">
				<label for="status-filter">Filter by Status:</label>
				<select id="status-filter" onchange="filterTableByStatus()">
					<option value="all">All</option>
					<option value="Active">Active</option>
					<option value="Inactive">Inactive</option>
				</select>
			</div>
			<div class="table-container">
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
					<tbody id="cug-details">
						<?php
						// Include database connection script
						include 'db_connect.php';

						$sql = "SELECT * FROM cugdetails_transaction";
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

								$activate_from = new DateTime($row["activate_from"]);
								$inactive_at = new DateTime($row["inactive_at"]);

								echo "<td>" . $activate_from->format('g:i A \o\n jS M, y') . "</td>";
								echo "<td>" . $inactive_at->format('g:i A \o\n jS M, y') . "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='12'>No records found</td></tr>";
						}

						$conn->close();
						?>
					</tbody>
				</table>
			</div>
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
		function filterTableByStatus() {
			const filter = document.getElementById('status-filter').value.toUpperCase();
			const table = document.querySelector('#cug-status-report table tbody');
			const tr = table.getElementsByTagName('tr');

			for (let i = 0; i < tr.length; i++) {
				let td = tr[i].getElementsByTagName('td')[10]; // Status column is at index 10
				if (td) {
					if (filter === 'ALL' || td.innerHTML.toUpperCase() === filter) {
						tr[i].style.display = "";
					} else {
						tr[i].style.display = "none";
					}
				}
			}
		}
	</script>
</body>

</html>
