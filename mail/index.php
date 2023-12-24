<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-ban"></i></a> Mail delivery failures</h3>

<?php
tabletop("deliveries", "<tr><th>Date</th><th>Message</th></tr>");
$results = $db->query("SELECT * FROM mail_failures ORDER BY id DESC LIMIT 1000;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>" . $result['date'] . "</td><td>" . str_replace("reject: ", "", str_replace("<", "&lt;", $result['message'])) . "</td></tr>";
}
tablebottom("deliveries", "0", "desc");
?>

<br>
<h4>Local blacklist</h4>
<?php
tabletop("blacklist", "<tr><th>Email address</th><th>Hits</th><th>Date</th></tr>");
$results = $db->query("SELECT * FROM mail_blacklist ORDER BY email ASC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>" . $result['email'] . "</td><td>" . $result['hits'] . "</td><td>" . $result['date'];
	if($login_admin == 1) { echo "<span style='float:right'><a title='Delete entry' href='delete.py?id=" . $result['id'] . "'><i class='fa fa-times'></i></a></span>"; }
	echo "</td></tr>";
}
tablebottom("blacklist", "0", "asc");
?>

<?php
if($login_admin == 1) { pipelines("Mail pipelines", array(

	array("title" => "Add a new blocked email address", "icon" => "ban", "action" => "add.py", "inputs" => array(
		array("type" => "text", "size" => "8", "options" => "name='email' maxlength='150' placeholder='Email address or domain' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Block'")
	)),

)); }
?>

<?php include '../bottom.php'; ?>

