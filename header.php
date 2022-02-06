<?php
$website_title = $_SESSION['website_title'];
$page_name = substr($_SERVER['SCRIPT_NAME'],1);

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Chakavak</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Chakavak SoftSwitch</h1>
				<a href="index.php"><i class="fas fa-user-circle"></i>Home</a>
				<a href="reg_stat.php"><i style="color: white;" class="fas fa-pencil"></i>Register Status</a>
				<a href="utility.php">Utility</a>
				<?php if (!empty($_SESSION["role"]) && $_SESSION["role"] == "ADMIN"){?><a href="admin.php"><i style="color: white;" class="fas fa-pencil"></i>Admin</a><?php } ?>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>