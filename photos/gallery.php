<?php $public_page = 1; include '../top.php'; ?>

<style>
.cell { position: relative; }
.cell img { display: block; }
.cell span { position: absolute; bottom:0; left:0; }
</style>

<div id="gallery">
<?php
$totalsize = 0;
$results = $db->query("SELECT * FROM photos ORDER BY year DESC, event, date;");
$event = "";
while($result = $results->fetch_assoc())
{
	if($event != $result['year'] . " - " . $result['event'])
	{
		$event = $result['year'] . " - " . $result['event'];
		if($event == $_GET['e'])
		{
			echo "<h3><a href='./' title='Photo galleries'><i class='fa fa-camera'></i></a> " . $event . "</h3>";
		}
	}
	if($event == $_GET['e'])
	{
		$totalsize = $totalsize + intval($result['size']);
		echo "<div class='cell'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "' title=\"" . $result['name'] . "\" style='max-width:100%' class='img-thumbnail'></a>";
	    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-year=\"" . $result['year'] . "\" data-device=\"" . $result['device'] . "\" data-event=\"" . $result['event'] . "\" data-name=\"" . $result['name'] . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
		echo "</div>";
	}
}
?>
</div>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Description:", "options" => "maxlength='50'"),
	array("type" => "text", "var" => "event", "label" => "Event:", "options" => "maxlength='30' required"),
	array("type" => "text", "var" => "year", "label" => "Year:", "options" => "maxlength='5' required"),
	array("type" => "text", "var" => "device", "label" => "Device:", "options" => "readonly"),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly")
)); }
?>

<?php include '../bottom.php'; ?>

