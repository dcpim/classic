<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-gamepad"></i></a> Gaming screenshots</h3>

<form method="POST" action="create.py" enctype="multipart/form-data" onSubmit="document.getElementById('submit').disabled=true;">
<div class="row">
<?php if($login_admin == 1) { ?>
		<div class="col-md-3">
			<input class="form-control" type="file" name="filename" required>
		</div>
		<div class="col-md-3">
			<input class="form-control" type="text" name="game" placeholder="Game name" required>
		</div>
		<div class="col-md-1">
			<input class="form-control btn btn-primary" type="submit" value="Add" id="submit">
		</div>
<?php } ?>
	<div class="col-md-4">
		<script>
			function dofilter()
			{
				window.location.replace("./?game=" + encodeURIComponent($("#filter").find('option:selected').val()));
			}
		</script>
		<select class="form-control" onchange="dofilter()" id="filter"><option>Game filter...</option><option value="">All</option>
<?php
$results = $db->query("SELECT game FROM games ORDER BY game ASC;");
$curgame = "";
while($result = $results->fetch_assoc())
{
	if($curgame != $result['game'])
	{
		if($curgame != "") { echo "<option value=\"" . $result['game'] . "\">" . $result['game'] . "</option>"; }
		$curgame = $result['game'];
	}
}
?>
		</select>
	</div>
</div>
</form>

<style>
.cell { position: relative; }
.cell img { display: block; }
.cell span { position: absolute; bottom:0; left:0; }
</style>

<div id="gallery">
<?php
$totalsize = 0;
$curyear = "0000";
$results = $db->query("SELECT * FROM games ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	if($_GET['game'] == "" or stripos($result['game'], $_GET['game']) !== false)
	{
		$year = substr($result['date'], 0, 4);
		if(strcmp($year, $curyear) and $_GET['game'] == "")
		{
			$curyear = $year;
			echo "<h2>" . $year . "</h2>";
		}
		echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img width=150 style='max-height:94px' src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['game'] . "\"></a>";
   		if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-game=\"" . $result['game'] . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
		echo "</div>";
		$totalsize = $totalsize + intval($result['size']);
	}
}
?>
</div>

<p>Total size: <b><?php echo size_format($totalsize); ?></b></p>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "game", "label" => "Game name:", "options" => "maxlength='50' required"),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly")
), "\$('#img' + id + ' img').attr('title', game); \$('#img' + id + ' span a').data('game', game);"); }
?>

<?php include '../bottom.php'; ?>

