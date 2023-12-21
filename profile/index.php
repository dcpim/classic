<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-user"></i></a> User profile</h3>

<div class='thumbnail'>
<?php
$username = "";
$results = $db->query("SELECT * FROM users;");
while($result = $results->fetch_assoc())
{
	if($result['username'] == $_COOKIE["dcpim_net_session"] and sha1($result['password']) == $_COOKIE["dcpim_net_token"])
	{
		$username = $result['username'];
		echo "<div class='thumbnail'><table width='99%' style='margin:5px;line-height: 1.5;'><tr>";
		echo "<td width='20%' style='padding-top:10px;padding-bottom:10px;padding-left:15px'><i class='fa fa-address-book fa-4x'></i></td>";
		echo "<td width='20%'><b>User name:</b><br><b>Email address:</b><br><b>Access level:</b><br></td>";
		echo "<td width='20%'>" . $result['username'] . "<br>" . $result['email'] . "<br>" . $result['admin'] . "<br>";
		echo "<td width='20%'><b>Last password reset:</b><br><b>Two-factor auth (SMS):</b><br><b>Two-factor auth (App):</b></td>";
		echo "<td width='20%'>" . $result['last_reset'] . "<br>";
		if($result['sms'] != "") { echo "Enabled<br>"; }
		else { echo "Disabled<br>"; }
		if($result['totp'] != "") { echo "Enabled<br>"; }
		else { echo "Disabled<br>"; }
		echo "</td></tr></table></div>";
	}
}
?>
</div>

<h4>Login sessions</h4>
<?php
tabletop("sessions", "<tr><th>IP Address</th><th>Browser hash</th><th>Valid until</th></tr>");
$results = $db->query("SELECT * FROM sessions WHERE username = \"" . $username . "\" GROUP BY ip ORDER BY valid_until;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>" . $result['ip'] . "</td><td>" . $result['browser'] . "</td><td>" . gmdate("Y-m-d H:i:s", $result['valid_until']) . "</td></tr>\n";
}
tablebottom("sessions", "2", "desc");
?>

<?php
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$key = '';
for ($i = 0; $i < 32; $i++) {
	$key .= $characters[random_int(0, strlen($characters) - 1)];
}

pipelines("Configuration options", array(

	array("title" => "Toggle dark mode", "icon" => "lightbulb-o", "action" => "darkmode.php", "inputs" => array(
		array("type" => "submit", "size" => "3", "options" => "value='Toggle'")
	)),

	array("title" => "Clear sessions", "icon" => "times", "action" => "clear_sessions.py", "inputs" => array(
		array("type" => "submit", "size" => "3", "options" => "value='Logout'")
	)),

	array("title" => "Change password", "icon" => "key", "action" => "reset_passwd.py", "inputs" => array(
		array("type" => "password", "size" => "3", "options" => "name='old_passwd' maxlength='50' placeholder='Old password' required"),
		array("type" => "password", "size" => "3", "options" => "name='new_passwd1' maxlength='50' placeholder='New password' required"),
		array("type" => "password", "size" => "3", "options" => "name='new_passwd2' maxlength='50' placeholder='New password' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Reset password'")
	)),

// TODO
//	array("title" => "Set two-factor authentication (SMS)", "icon" => "commenting-o", "action" => "sms.py", "inputs" => array(
//		array("type" => "text", "size" => "9", "options" => "name='key' maxlength='50' placeholder='SNS topic' required"),
//		array("type" => "submit", "size" => "3", "options" => "value='Set two-factor'")
//	)),

	array("title" => "Set two-factor authentication (Phone app)", "icon" => "mobile", "action" => "totp.py", "inputs" => array(
		array("type" => "hidden", "size" => "0", "options" => "name='key' maxlength='50' value='" . $key . "' required"),
		array("type" => "number", "size" => "3", "options" => "name='token' maxlength='10' placeholder='Token' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Set two-factor'"),
		array("type" => "row"),
		array("type" => "label", "label" => "Scan this QR code with your phone app to produce a 2FA token:")
	))

));
?>

<div id="qrcode"></div>
<script src="/qrcode.js"></script>
<script type="text/javascript">
new QRCode(document.getElementById("qrcode"), "otpauth://totp/DCPIM:<?php echo $username; ?>?secret=<?php echo $key; ?>&issuer=DCPIM");
</script>

<br>

<?php include '../bottom.php'; ?>

