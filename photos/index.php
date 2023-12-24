<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-camera"></i></a> Photo galleries</h3>

<ul class="list-group">
<?php
$totalsize = 0;
$results = $db->query("SELECT * FROM photos ORDER BY year DESC, event;");
$event = "";
$num = 0;
while($result = $results->fetch_assoc())
{
	if($event != $result['year'] . " - " . $result['event'])
	{
		if($event != "") { echo "<li class='list-group-item col-sm-6'><a href=\"./gallery.php?e=" . $event . "\">" . $event . "</a><span style='float:right'><i>" . $num . " photos</i></span></li>"; }
		$event = $result['year'] . " - " . $result['event'];
		$num = 0;
	}
	$num = $num + 1;
	$totalsize = $totalsize + intval($result['size']);
}
echo "<li class='list-group-item col-sm-6'><a href=\"./gallery.php?e=" . $event . "\">" . $event . "</a><span style='float:right'><i>" . $num . " photos</i></span></li>";
?>
</ul>

<div style='clear:both'></div>
<p>Total size: <b><?php echo size_format($totalsize); ?></b></p>

<?php
if($login_admin == 1) { pipelines("Photo pipelines", array(

	array("title" => "Upload a photo", "icon" => "upload", "action" => "create.py", "inputs" => array(
		array("type" => "file", "size" => "4", "options" => "name='filename' required"),
		array("type" => "text", "size" => "4", "options" => "name='event' maxlength='50' placeholder='Event' required"),
		array("type" => "text", "size" => "2", "options" => "name='year' maxlength='5' placeholder='Year' value='" . date("Y") . "' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Import photo'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"photos" => "Photos library"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

