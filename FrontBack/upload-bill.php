<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Upload CUG Bill</title>
	<link rel="stylesheet" href="base.css" />
	<link rel="stylesheet" href="upload-bill.css" />
</head>

<body>
	<header>
		<div class="header-top">
			<h1>East Coast Railway</h1>
			<h1>Closed User Group</h1>
		</div>
		<!-- <nav>
			<ul>
				<li><a href="index.html">Admin Page</a></li>
			</ul>
		</nav> -->
	</header>

	<main>
		<section id="create-dealer">
			<div class="heading-container">
				<button class="back-btn" onclick="window.location.href = './admin-page.html'"><img
						src="https://img.icons8.com/ios/32/long-arrow-left.png" alt="back button"></button>
				<h2 class="heading">Upload Bill</h2>
			</div>
			<form class="form_container" action="upload-bill.php" method="post">
				<div class="input_box long-input">
					<label for="cugno">Upload CUG Bill</label>
					<input 
						type="file" 
						id="cugno" 
						name="cugno" 
						placeholder="Enter CUG number" 
						required 
					/>
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