<?php
include 'authenticate.php';
checkUser("admin");
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>ECoR CUG Admin Page</title>
	<link rel="icon" type="image/webp" href="logo.webp" />
	<link rel="stylesheet" href="base.css" />
	<link rel="stylesheet" href="admin-page.css" />

</head>

<body>
	<header>
		<div class="header-top">
			<a href="./">
				<h1>East Coast Railway</h1>
				<h1>Closed User Group</h1>
			</a>
			<!-- Add this button -->
			<a class="logout-button" href="logout.php">
				Logout
			</a>
		</div>
	</header>

	<section id="admin-services">
		<h2>Admin Services</h2>
		<div class="services-container">
			<div class="service">
				<a href="./create-dealer.php">
					<img src="icon/create_dealer.webp" alt="Create Dealer" />
					<h3>Create Dealer</h3>
					<p>Create new dealers for the CUG system.</p>
				</a>
			</div>
			<div class="service">
				<a href="cug-details.php">
					<img src="icon/cug_details.webp" alt="CUG Details" />
					<h3>CUG Details</h3>
					<p>
						View and manage CUG details, activate or deactivate
						CUGs.
					</p>
				</a>
			</div>
			<div class="service">
				<a href="./add-cug.php">
					<img src="icon/add_cug1.webp" alt="Add New CUG" />
					<h3>Add New CUG</h3>
					<p>Add new CUG numbers to the system.</p>
				</a>
			</div>
			<div class="service">
				<a href="./upload-cug.php">
					<img src="icon/upload_cug.webp" alt="Add New CUG" />
					<h3>Upload CUG Numbers</h3>
					<p>Upload new CUG numbers to the system.</p>
				</a>
			</div>
			<div class="service">
				<a href="allotment-history.php">
					<img src="icon/allotment_history.webp" alt="Allotment History" />
					<h3>Allotment History</h3>
					<p>Check the history of CUG number allotments.</p>
				</a>
			</div>
			<div class="service">
				<a href="allocation-report.php">
					<img src="icon/allocation_report.webp" alt="Allocation-Wise Report" />
					<h3>Allocation-Wise Report</h3>
					<p>Generate reports based on CUG allocations.</p>
				</a>
			</div>
			<div class="service">
				<a href="bill-passing-register.php">
					<img src="icon/report.webp" alt="Bill Passing Register" />
					<h3>Bill Passing Register</h3>
					<p>Generate reports based on CUG Units.</p>
				</a>
			</div>
			<div class="service">
				<a href="cug-status-report.php">
					<img src="icon/status_report.webp" alt="CUG Status Report" />
					<h3>CUG Status Report</h3>
					<p>Get the current status report of CUG numbers.</p>
				</a>
			</div>
			<!-- <div class="service">
					<a href="active-deactive-report.html">
						<img
							src="icon/active_deactive_report.webp"
							alt="Active / De-Active Report"
						/>
						<h3>Active / De-Active Report</h3>
						<p>
							View reports of active and deactivated CUG numbers.
						</p>
					</a>
				</div> -->
			<div class="service">
				<a href="upload-bill.php">
					<img src="icon/upload_bill2.webp" alt="Upload CUG Bill" />
					<h3>Upload CUG Bill</h3>
					<p>Upload the latest CUG bills to the system.</p>
				</a>
			</div>
			<div class="service">
				<a href="add-update-plans.php">
					<img src="icon/add_and_update.webp" alt="Upload CUG Bill" />
					<h3>Add Plans</h3>
					<p>Add new plans to the existing plans.</p>
				</a>
			</div>
			<!-- <div class="service">
					<a href="add-bill.php">
						<img
							src="icon/upload_numbers.webp"
							alt="Upload New CUG Nos"
						/>
						<h3>Add New Bill</h3>
						<p>Add new CUG bill for users.</p>
					</a>
				</div> -->
		</div>
	</section>

	<footer>
		<p>&copy; 2024 East Coast Railway. All rights reserved.</p>
		<div class="footer-links">
			<a href="#">Privacy Policy</a>
			<a href="#">Terms of Service</a>
		</div>
	</footer>
	<script>
		document.addEventListener("DOMContentLoaded", () =>
		{
			const card = document.querySelector(".service");

			card.addEventListener("mousemove", (e) =>
			{
				const rect = card.getBoundingClientRect();
				const x = e.clientX - rect.left;
				const y = e.clientY - rect.top;
				const centerX = rect.width / 2;
				const centerY = rect.height / 2;

				const shadowX = (x - centerX) / 10;
				const shadowY = (y - centerY) / 10;

				document.documentElement.style.setProperty(
					"--card-shadow-x",
					`${ shadowX }rem`
				);
				document.documentElement.style.setProperty(
					"--card-shadow-y",
					`-${ shadowY }rem`
				);
			});

			card.addEventListener("mouseleave", () =>
			{
				document.documentElement.style.setProperty(
					"--card-shadow-x",
					"0px"
				);
				document.documentElement.style.setProperty(
					"--card-shadow-y",
					"0px"
				);
			});
		});
	</script>
</body>

</html>