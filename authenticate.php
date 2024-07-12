<?php
session_start();
function checkUser($role)
{
	$oppRole = "guest";
	if ($role == "admin") {
		$oppRole = "dealer";
	} elseif ($role == "dealer") {
		$oppRole = "admin";
	}

	// Check if the user is authenticated
	if (!isset($_SESSION['role'])) {
		header('Location: logout.php');
		exit();
	}

	if ($_SESSION['role'] == $oppRole) {
		header("Location: $oppRole-page.php");
		exit();
	}

	// // Check if the user is authenticated as an admin
	// if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
	// 	header('Location: login.php');
	// 	exit();
	// }
}
