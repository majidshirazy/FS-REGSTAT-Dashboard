<?php 
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
require_once "config/initial.php";
require_once "config/db.php";
include_once('header.php');

?>
<div class="content">
	<h2>Registration Status</h2>
	<div>
		<h4>Search of specific number</h4>
			<form action="reg_stat.php" method="post">
        <div>
            <input type=text name="sip_user" />
            <input type="submit" value="Search" name="search_for_registered" />
			<input type="submit" value="Show all" name="show_all" />
        	</form><br /><br />
		</div>
		<center>
<?php
$sip_user = $_POST["sip_user"];
if (isset($_POST["search_for_registered"])){
	if (empty($sip_user)){
		header("Location: reg_stat.php");
	}
    #You can use these paramateres in results.
	#sip_user, network_ip, network_port, status, expires, ping_status, user_agent.
    $query = "SELECT * from sip_registrations where sip_user = $sip_user;";
    $result = $sqlite_db->query($query);
	$search = $result->fetchArray(SQLITE3_ASSOC);
	if (empty($search)){
?>
		<h4>SIP number <?= $sip_user?> is not registered.</h4>
<?php
	} else {
		$expire_date = date("Y-m-d H:i:s", substr($search['expires'], 0, 10));
?>		
			<table>
				<tr>
					<th class="head" style="width:10%">Number</th>
					<th style="width:10%">User IP:Port</th>
					<th style="width:10%">Registartion</th>
					<th>Status</th>
					<th style="width:20%">User Agent</th>
					<th>Expire Date</th>
				</tr>
				<tr>
					<td><?=$search['sip_user'] ;?></td>
					<td><?=$search['network_ip'].":".$search['network_port'] ;?></tc>
					<td><?php print_r(explode("(",$search['status'])[0]); ?></td>
					<td><?=$search['ping_status']; ?></td>
					<td><?php print_r(explode(" ",$search['user_agent'])[0]); ?></td>
					<td><?=$expire_date; ?></td>
				</tr>
<?php 
	}
} else {
	?>
			<table>
				<tr>
					<th class="head" style="width:10%">Number</th>
					<th style="width:10%">User IP:Port</th>
					<th style="width:10%">Registartion</th>
					<th>Status</th>
					<th style="width:20%">User Agent</th>
					<th>Expire Date</th>
				</tr>
<?php

	$ret = $sqlite_db->query("SELECT sip_user,network_ip,network_port,status,expires,ping_status,user_agent from sip_registrations;");
	$row_count = 0;
	while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
		$row_count +=1;
		$_SESSION['row_count'] = $row_count;
		$expire_date = date("Y-m-d H:i:s", substr($row['expires'], 0, 10));
		$expire_interval = $row['expires'] - strtotime(date("Y-m-d H:i:s"));

?>
				<tr>
					<td><?=$row['sip_user'] ;?></td>
					<td><?=$row['network_ip'].":".$row['network_port'] ;?></tc>
					<td><?php print_r(explode("(",$row['status'])[0]); ?></td>
					<td><?=$row['ping_status']; ?></td>
					<td><?php print_r(explode(" ",$row['user_agent'])[0]); ?></td>
					<td><?=$expire_date; ?></td>
				</tr>
<?php 
	}
	?>
				<tr>
					<td colspan="6"> <h3> Total Registered numbers: <?=$row_count?></h3></td>
				</t>
<?php
}
$sqlite_db->close();
?>
		
		
			</table>
			</center>
	</div>
</div>
</body>
</html> 
