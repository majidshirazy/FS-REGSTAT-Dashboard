<?php
session_start();
require_once "config/initial.php";
$_SESSION['website_title'] = "Administration Dashboard";
if (!isset($_SESSION['loggedin'])){
	header('Location: index.php');
	exit;
}
if ($_SESSION["role"] != "ADMIN"){
	header('Location: index.php');
	exit;
} else {
	$id = $_SESSION['id'];
	$usernmae = $_SESSION['name'];
	}
require_once "config/db.php";
$stmt = $db->prepare('SELECT `username`, `password`, `email`,`role` FROM `user`');
//$stmt->bind_param('s', $_SESSION["id"]);
$stmt->execute();
$stmt->bind_result($username, $password, $email, $role);
$row=1;
include_once ('header.php');
?>
		<div class="content">
			<h2>Administration Page</h2>
			<div>
				<h3>Edit current users:</h3>
					<table>
						<tr>
							<td>Row</td>
							<td>Username</td>
							<td>Password</td>
							<td>Email</td>
							<td>Role</td>
						</tr>

<?php
while ($stmt->fetch()) {
?>
						<tr>
							<form action=controller.php method="post">
							<td><label for="row"><?=$row;$row++?></label></td>
							<td>
								<input type="hidden" name="username" value="<?=$username?>"/>
								<label for="username"><?=$username?></label>
							</td>
							<td><input type="text" name="password" value="<?=$password?>" /></td>
							<td><input type="text" name="email" value="<?=$email?>" /></td>
							<td><input type="text" name="role" value="<?=$role?>" /></td>
							<td>
								<input type="submit" name="admin_update" value="update" />
								<input type="submit" name="delete_user" value="del" />
							</td>							
						</tr>
						</form>
<?php } $stmt->close(); ?>

					</table>
					
				
				
				<h3>Add new user</h3>
				<form action="controller.php" method="post">
				<table>
					<tr>
						<td>Username</td>
						<td>Password</td>
						<td>Email</td>
						<td>Role</td>
					</tr>
					<tr>
						<td><input type="text" name="new_username" placeholder="Username" id="username" required></td>
						<td><input type="password" name="new_password" placeholder="Password" id="password" required></td>
						<td><input type="email" name="new_email" placeholder="Email" id="email" required></td>
						<td><input type="role" name="new_role" placeholder="Role" id="role" value="user" required></td>
						<td><input type="submit" name="add_new_user" value="Add" ></td>
					</tr>
				</form>
			</div>
		</div>
	</body>
</html>