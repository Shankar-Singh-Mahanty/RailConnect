<?php
session_start(); // Start the session

// Set the default time zone
date_default_timezone_set('Asia/Kolkata');

// Include database connection script
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Sanitize and retrieve form inputs
	$cug_no = filter_input(INPUT_POST, 'cugno', FILTER_SANITIZE_NUMBER_INT);
	$periodic_charge = filter_input(INPUT_POST, 'periodic_charge', FILTER_VALIDATE_FLOAT);
	$usage_amount = filter_input(INPUT_POST, 'usage_amount', FILTER_VALIDATE_FLOAT);
	$data_amount = filter_input(INPUT_POST, 'data_amount', FILTER_VALIDATE_FLOAT);
	$voice = filter_input(INPUT_POST, 'voice', FILTER_VALIDATE_FLOAT);
	$video = filter_input(INPUT_POST, 'video', FILTER_VALIDATE_FLOAT);
	$sms = filter_input(INPUT_POST, 'sms', FILTER_VALIDATE_FLOAT);
	$vas = filter_input(INPUT_POST, 'vas', FILTER_VALIDATE_FLOAT);

	if (!is_numeric($cug_no) || !is_numeric($periodic_charge) || !is_numeric($usage_amount) || !is_numeric($data_amount) || !is_numeric($voice) || !is_numeric($video) || !is_numeric($sms) || !is_numeric($vas)) {
		$_SESSION['message'] = "All fields are required and must be valid numbers.";

		// Preserve form data in session to repopulate fields
		$_SESSION['form_data'] = [
			'cugno' => $cug_no,
			'periodic_charge' => $periodic_charge,
			'usage_amount' => $usage_amount,
			'data_amount' => $data_amount,
			'voice' => $voice,
			'video' => $video,
			'sms' => $sms,
			'vas' => $vas
		];
	} else {
		// Prepare statement to retrieve cug_id based on cug_number
		$stmt_cug = $conn->prepare("SELECT cug_id FROM cugdetails WHERE cug_number = ? AND status = 'Active'");
		$stmt_cug->bind_param("i", $cug_no);
		$stmt_cug->execute();
		$stmt_cug->bind_result($cug_id);

		// Fetch the cug_id
		if ($stmt_cug->fetch()) {
			$stmt_cug->close();

			// Prepare and bind the INSERT statement for bills table
			$stmt_insert = $conn->prepare("INSERT INTO bills (cug_id, periodic_charge, usage_amount, data_amount, voice, video, sms, vas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt_insert->bind_param("iddddddd", $cug_id, $periodic_charge, $usage_amount, $data_amount, $voice, $video, $sms, $vas);

			// Execute the statement
			if ($stmt_insert->execute()) {
				$_SESSION['message'] = "Bill is successfully uploaded for CUG number: $cug_no";
				// Clear form data from session after successful submission
				unset($_SESSION['form_data']);
			} else {
				$_SESSION['message'] = "Error: " . $stmt_insert->error;
			}

			$stmt_insert->close();
		} else {
			$_SESSION['message'] = "CUG number $cug_no not found or not active.";
		}

	}

	// Close database connection
	$conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Add Bill</title>
	<link rel="icon" type="image/webp" href="logo.webp" />
	<link rel="stylesheet" href="base.css" />
	<link rel="stylesheet" href="add-bill.css" />
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
		<section id="create-dealer">
			<div class="heading-container">
				<button class="back-btn" onclick="window.location.href = './admin-page.html'"><img
						src="https://img.icons8.com/ios/32/long-arrow-left.png" alt="back button"></button>
				<h2 class="heading">Add New Bill</h2>
			</div>
			<?php
			if (isset($_SESSION['message'])) {
				echo "<p class='session-message'>" . $_SESSION['message'] . "</p>";
				unset($_SESSION['message']);
			}
			$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [
				'cugno' => '',
				'periodic_charge' => '',
				'usage_amount' => '',
				'data_amount' => '',
				'voice' => '',
				'video' => '',
				'sms' => '',
				'vas' => ''
			];
			?>
			<form class="form_container" action="#" method="post">
				<div class="input_box">
					<label for="cugno">CUG NO</label>
					<input type="number" id="cugno" name="cugno" placeholder="Enter CUG number"
						value="<?php echo htmlspecialchars($form_data['cugno']); ?>" required />
				</div>
				<div class="input_box">
					<label for="periodic_charge">Periodic Charge</label>
					<input type="number" id="periodic_charge" name="periodic_charge" placeholder="Enter Periodic Charge"
						value="<?php echo htmlspecialchars($form_data['periodic_charge']); ?>" required />
				</div>
				<div class="input_box">
					<label for="usage_amount">Usage Amount</label>
					<input type="number" id="usage_amount" name="usage_amount" placeholder="Enter Usage Amount"
						value="<?php echo htmlspecialchars($form_data['usage_amount']); ?>" required />
				</div>
				<div class="input_box">
					<label for="data_amount">Data Amount</label>
					<input type="number" id="data_amount" name="data_amount" placeholder="Enter Data Amount"
						value="<?php echo htmlspecialchars($form_data['data_amount']); ?>" required />
				</div>
				<div class="input_box">
					<label for="voice">Voice</label>
					<input type="number" id="voice" name="voice" placeholder="Enter Voice charges"
						value="<?php echo htmlspecialchars($form_data['voice']); ?>" required />
				</div>
				<div class="input_box">
					<label for="video">Video</label>
					<input type="number" id="video" name="video" placeholder="Enter Video charges"
						value="<?php echo htmlspecialchars($form_data['video']); ?>" required />
				</div>
				<div class="input_box">
					<label for="sms">SMS</label>
					<input type="number" id="sms" name="sms" placeholder="Enter SMS charges"
						value="<?php echo htmlspecialchars($form_data['sms']); ?>" required />
				</div>
				<div class="input_box">
					<label for="vas">VAS</label>
					<input type="number" id="vas" name="vas" placeholder="Enter VAS charges"
						value="<?php echo htmlspecialchars($form_data['vas']); ?>" required />
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