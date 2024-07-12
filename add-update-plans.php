<?php
include 'authenticate.php';
checkUser("admin");

// Include database connection file
include 'db_connect.php';

$message = null;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Sanitize and retrieve form inputs
	$plan_name = filter_input(INPUT_POST, 'plan_name', FILTER_SANITIZE_STRING);
	$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
	$validity_days = filter_input(INPUT_POST, 'validity_days', FILTER_VALIDATE_INT);
	$data_per_day = filter_input(INPUT_POST, 'data_per_day', FILTER_VALIDATE_FLOAT);
	$talktime = filter_input(INPUT_POST, 'talktime', FILTER_SANITIZE_STRING);

	if ($plan_name && $price && $validity_days && $data_per_day && $talktime) {
		// Check if the plan already exists
		$query_check = "SELECT * FROM plans WHERE plan_name = ?";
		$stmt_check = $conn->prepare($query_check);
		$stmt_check->bind_param("s", $plan_name);
		$stmt_check->execute();
		$result_check = $stmt_check->get_result();

		if ($result_check->num_rows > 0) {
			// Plan exists, update the plan
			$query_update = "UPDATE plans SET price = ?, validity_days = ?, data_per_day = ?, talktime = ? WHERE plan_name = ?";
			$stmt_update = $conn->prepare($query_update);
			$stmt_update->bind_param("dids", $price, $validity_days, $data_per_day, $talktime, $plan_name);

			if ($stmt_update->execute()) {
				$message = "Plan updated successfully.";
			} else {
				$message = "Error: " . $stmt_update->error;
			}
			$stmt_update->close();
		} else {
			// Plan does not exist, insert a new plan
			$query_insert = "INSERT INTO plans (plan_name, price, validity_days, data_per_day, talktime) VALUES (?, ?, ?, ?, ?)";
			$stmt_insert = $conn->prepare($query_insert);
			$stmt_insert->bind_param("sdids", $plan_name, $price, $validity_days, $data_per_day, $talktime);

			if ($stmt_insert->execute()) {
				$message = "Plan $plan_name with price: $price added successfully.";
			} else {
				$message = "Error: " . $stmt_insert->error;
			}
			$stmt_insert->close();
		}

		$stmt_check->close();
	} else {
		$message = "All fields are required and must be valid.";
	}

	// Close database connection
	$conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Add Plans</title>
	<link rel="icon" type="image/webp" href="logo.webp" />
	<link rel="stylesheet" href="base.css">
	<link rel="stylesheet" href="add-update-plans.css">
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
		<section id="create-plan">
			<div class="heading-container">
				<button class="back-btn" onclick="window.location.href = './admin-page.php'">
					<img src="icon/back-button.webp" alt="back button">
				</button>
				<h2 class="heading">Add Plan</h2>
			</div>
			<?php
			// Display the message if available
			if ($message) {
				echo "<p class='session-message'>" . htmlspecialchars($message) . "</p>";
			}
			?>
			<form class="form_container" action="add-update-plans.php" method="post">
				<div class="input_box">
					<label for="plan_name">Plan Name</label>
					<input type="text" id="plan_name" name="plan_name" placeholder="Enter Plan Name" required>
				</div>
				<div class="input_box">
					<label for="price">Price (₹)</label>
					<input type="number" step="0.01" id="price" name="price" placeholder="Enter Price" required>
				</div>
				<div class="input_box">
					<label for="validity_days">Validity (days)</label>
					<input type="number" id="validity_days" name="validity_days" placeholder="Enter Validity in Days"
						required>
				</div>
				<div class="input_box">
					<label for="data_per_day">Data per Day (GB)</label>
					<input type="number" step="0.1" id="data_per_day" name="data_per_day"
						placeholder="Enter Data per Day" required>
				</div>
				<div class="input_box">
					<label for="talktime">Talktime</label>
					<input type="text" id="talktime" name="talktime" placeholder="Enter Talktime Details" required>
				</div>
				<button class="submit-button" type="submit">
					Submit
				</button>
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
</body>

</html>