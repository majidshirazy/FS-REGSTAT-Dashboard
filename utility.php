<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
exec("sudo systemctl is-active tcpdump.service",$result);
if ($result[0] == "active"){
    $sip_capture_status = "Active";
} else {
    $sip_capture_status = "Stop";
}
$pcap_file = "pcap/sip.pcap";
include_once('header.php');
	?>
		<div class="content">
			<h2>Capture Traffic</h2>
				<div class="pcap-child">
					<p>Set your options:</p>
					<form action=controller.php method="post">
						<label for="sip_packet_type">Type: </label>
						<select name=sip_packet_type>
							<option value="all">All Packets</option>
							<option value="invite">Call Packets Only</option>
							<option value="register">Register Packets Only</option>
						</select><br />
						<label for="sip_number">Number:</label>
						<input type=text name="sip_number" /><br />
						<label for="sip_port">Port:</label>
						<input type="text" name="sip_port" /><br />
						<label for="sip_other_options">Options:</label>
						<input type="text" name="sip_other_options" /><br />
						<input type="submit" value="Start" name="start_pcap" />
					</form>
				</div>
				<div class="pcap-child">
					<?= "Capturing Status is: ".$sip_capture_status." <br />"; ?>
					<?php if($sip_capture_status != "Stop") {?>
						<form action=controller.php method="post">
								<input type="hidden" value="stop_pcap" />
								<input type="submit" value="Stop" name="stop_pcap" />
						</form
						<?php } ?>
						<?php if(file_exists($pcap_file) && $sip_capture_status == "Stop") {?>
							<span></span>
						<a class="dl-link" href=<?=$pcap_file?>> Download </a>
						<?php } ?>
				</div>
		</div>
	</body>
</html>