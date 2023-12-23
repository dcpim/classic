<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-rss"></i></a> Feeds</h3>

<?php
tabletop("feeds", "<tr><th>Name</th><th>URL</th><th>Filter</th></tr>");
$results = $db->query("SELECT * FROM rss_feeds;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>" . $result['name'] . "</td><td><a target=_new href='" . $result['url'] . "'>" . $result['url'] . "</a></td><td>" . $result['filter'];
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-name=\"" . $result['name'] . "\" data-id='" . $result['id'] . "' data-url=\"" . $result['url'] . "\" data-filter=\"" . $result['filter'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td></tr>";
}
tablebottom("feeds", "0", "asc");
?>

<form method="POST" action="create.py" enctype="multipart/form-data" onSubmit="document.getElementById('submit').disabled=true;">
	<div class="row">
		<div class="col-md-4">
			<input class="form-control" maxlength='245' type="text" name="name" placeholder="Name" required>
		</div>
		<div class="col-md-4">
			<input class="form-control" maxlength='245' type="text" name="url" placeholder="URL" required>
		</div>
		<div class="col-md-2">
			<input class="form-control" maxlength='95' type="text" name="filter" placeholder="Filter (optional)">
		</div>
		<div class="col-md-2">
			<input class="form-control btn btn-primary" type="submit" value="Add feed" id="submit">
		</div>
	</div>
</form>

<?php
$newlastread = 0;
$results = $db->query("SELECT * FROM rss_lastread ORDER BY id DESC LIMIT 1;");
while($result = $results->fetch_assoc())
{
	$lastread = $result['item'];
}

$feeds = array();
$results = $db->query("SELECT * FROM rss_feeds;");
while($result = $results->fetch_assoc())
{
	if($result['filter'] != "") { $feeds[$result['id']] = $result['name'] . " (" . $result['filter'] . ")"; }
	else { $feeds[$result['id']] = $result['name']; }
}

$results = $db->query("SELECT * FROM rss ORDER BY id DESC LIMIT 100;");
while($result = $results->fetch_assoc())
{
	if($result['id'] == $lastread)
	{
		echo "<br><div class='alert alert-info' role='alert'><center><h3><i>End of new entries.</i></h3></center></div><br>\n";
	}
	echo "<div class='img-thumbnail' style='width:99%;padding-left:10px;padding-right:10px;margin-bottom:10px'>";
	echo "<h3 style='margin-top:3px!important;margin-bottom:15px!important'><a target=_new href='" . $result['url'] . "'>" . $result['title'] . "</a></h3>";
	if($result['image'] != "")
	{
		echo "<table width='99%'><tr><td width='30%'><img style='width:95%;' src='" . $result['image'] . "'></td><td><font size='+1'>" . $result['description'] . "</font></td></tr></table><br>";
	}
	else
	{
		echo "<p><font size='+1'>" . $result['description'] . "</font></p>";
	}
	$feed = "Unknown";
	if(array_key_exists($result['feed'], $feeds)) { $feed = $feeds[$result['feed']]; }
	echo "<font size='-1'><i><span style='float:right'>" . $result['date'] . "</span><span>" . $feed . "</span></i></font>";
	echo "</div>\n";
	if($result['id'] > $newlastread) { $newlastread = $result['id']; }
}

$lastread = $newlastread;
$stmt = $db->prepare("INSERT INTO rss_lastread (item, date) VALUES (?, ?);");
$stmt->bind_param('ss', $lastread, date('Y-m-d'));
$stmt->execute();
?>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='245' required"),
	array("type" => "text", "var" => "url", "label" => "URL:", "options" => "maxlength='245' required"),
	array("type" => "text", "var" => "filter", "label" => "Filter:", "options" => "maxlength='95'")
)); }
?>

<?php include '../bottom.php'; ?>

