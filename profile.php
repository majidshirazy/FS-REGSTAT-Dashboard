<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
require_once "config/db.php";
$stmt = $db->prepare('SELECT `password`, `email` FROM `user` WHERE `id` = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
include_once('header.php');
?>

		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<form action=controller.php method="post">
					<table>
						<tr>
							<td>Username:</td>
							<td><label for="username"><?=$_SESSION['name']?></label></td>
						</tr>
						<tr>
							<td>Password:</td>
							<td><input type="text" name="password" value="<?=$password?>" /></td>
						</tr>
						<tr>
							<td>Email:</td>
							<td><input type="text" name="email" value="<?=$email?>" /></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: center;"><input  type="submit" value="Update" name="profile_update" /></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>