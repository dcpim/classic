<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-film"></i></a> Videos</h3>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#anime">Anime</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#clip">Clips</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#funny">Funny</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#travel">Travel</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#local">Local</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#gaming">Gaming</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#music">Music</a></li>
    <li><a id="loading"><b>Loading...</b></a></li>
</ul>

<style>
.cell { position: relative; width: 280px; margin-right: 5px; }
.cell img { display: block; width: 260px; max-height: 180px; }
.cell span { position: absolute; bottom:0; left:0; color: #3EAEEE !important }
</style>

<script>
$(window).load(function()
{
    $(".menulink").show();
    $("#loading").hide();
});
</script>

<div id="gallery">
<div class="tab-content">

	<div id="anime" class="tab-pane fade in active">
<?php
$totalsize = 0;
$results = $db->query("SELECT * FROM videos WHERE type = 'Anime';");
while($result = $results->fetch_assoc())
{
    echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>";
	if($result['thumb'] != "") { echo "<center><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'><br>"; }
	echo "<font size=+1 style='color:#3EAEEE!important'>" . $result['name'] . " (" . $result['duration'] . ")</font>";
	if($result['thumb'] != "") { echo "</center>"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-size='" . size_format($result['size']) . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="clip" class="tab-pane fade in">
<?php
$results = $db->query("SELECT * FROM videos WHERE type = 'Clip';");
while($result = $results->fetch_assoc())
{
    echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>";
	if($result['thumb'] != "") { echo "<center><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'><br>"; }
	echo "<font size=+1 style='color:#3EAEEE!important'>" . $result['name'] . " (" . $result['duration'] . ")</font>";
	if($result['thumb'] != "") { echo "</center>"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-size='" . size_format($result['size']) . "' data-date='" . $result['date'] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="funny" class="tab-pane fade in">
<?php
$results = $db->query("SELECT * FROM videos WHERE type = 'Funny video';");
while($result = $results->fetch_assoc())
{
    echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>";
	if($result['thumb'] != "") { echo "<center><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'><br>"; }
	echo "<font size=+1 style='color:#3EAEEE!important'>" . $result['name'] . " (" . $result['duration'] . ")</font>";
	if($result['thumb'] != "") { echo "</center>"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-size='" . size_format($result['size']) . "' data-date='" . $result['date'] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="local" class="tab-pane fade in">
<?php
$results = $db->query("SELECT * FROM videos WHERE type = 'Local';");
while($result = $results->fetch_assoc())
{
    echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>";
	if($result['thumb'] != "") { echo "<center><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'><br>"; }
	echo "<font size=+1 style='color:#3EAEEE!important'>" . $result['name'] . " (" . $result['duration'] . ")</font>";
	if($result['thumb'] != "") { echo "</center>"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-size='" . size_format($result['size']) . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="travel" class="tab-pane fade in">
<?php
$results = $db->query("SELECT * FROM videos WHERE type = 'Travel';");
while($result = $results->fetch_assoc())
{
    echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>";
	if($result['thumb'] != "") { echo "<center><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'><br>"; }
	echo "<font size=+1 style='color:#3EAEEE!important'>" . $result['name'] . " (" . $result['duration'] . ")</font>";
	if($result['thumb'] != "") { echo "</center>"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-size='" . size_format($result['size']) . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="gaming" class="tab-pane fade in">
<?php
$results = $db->query("SELECT * FROM videos WHERE type = 'Gaming';");
while($result = $results->fetch_assoc())
{
    echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>";
	if($result['thumb'] != "") { echo "<center><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'><br>"; }
	echo "<font size=+1 style='color:#3EAEEE!important'>" . $result['name'] . " (" . $result['duration'] . ")</font>";
	if($result['thumb'] != "") { echo "</center>"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-size='" . size_format($result['size']) . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="music" class="tab-pane fade in">
<?php
$results = $db->query("SELECT * FROM videos WHERE type LIKE 'Music%';");
while($result = $results->fetch_assoc())
{
    echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>";
	if($result['thumb'] != "") { echo "<center><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'><br>"; }
	echo "<font size=+1 style='color:#3EAEEE!important'>" . $result['name'] . " (" . $result['duration'] . ")</font>";
	if($result['thumb'] != "") { echo "</center>"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-size='" . size_format($result['size']) . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>


</div>
</div>

<p>Total size: <b><?php echo size_format($totalsize); ?></b></p>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='100' required"),
	array("type" => "select", "var" => "type", "label" => "Type:", "options" => "required", "choices" => array(
		"Music video (English)", "Music video (Japanese)", "Music video (French)", "Music video (Russian)", "Funny video", "Anime", "Clip", "Travel", "Local", "Gaming"
	)),
	array("type" => "text", "var" => "size", "label" => "Size:", "options" => "readonly"),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly"),
)); }

if($login_admin == 1) { pipelines("Video pipelines", array(

	array("title" => "Upload a video", "icon" => "upload", "action" => "create.py", "inputs" => array(
		array("type" => "file", "size" => "3", "options" => "name='filename' required"),
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='100' placeholder='Video name' required"),
		array("type" => "select", "size" => "3", "options" => "name='type' required", "choices" => array(
			"Music video (English)", "Music video (Japanese)", "Music video (French)", "Music video (Russian)", "Funny video", "Anime", "Clip", "Travel", "Local", "Gaming"
		)),
		array("type" => "submit", "size" => "2", "options" => "value='Add video'")
	)),

	array("title" => "Import from YouTube", "icon" => "youtube", "action" => "create.py", "inputs" => array(
		array("type" => "text", "size" => "3", "options" => "name='url' placeholder='YouTube URL' required"),
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='100' placeholder='Video name' required"),
		array("type" => "select", "size" => "3", "options" => "name='type' required", "choices" => array(
			"Music video (English)", "Music video (Japanese)", "Music video (French)", "Music video (Russian)", "Funny video", "Anime", "Clip", "Travel", "Local", "Gaming"
		)),
		array("type" => "submit", "size" => "2", "options" => "value='Add video'")
	)),

	array("title" => "Convert an images archive to video", "icon" => "step-forward", "action" => "makeslideshow.py", "inputs" => array(
		array("type" => "file", "size" => "3", "options" => "name='filename' required"),
		array("type" => "text", "size" => "3", "options" => "name='name' maxlength='100' placeholder='Video name' required"),
		array("type" => "text", "size" => "1", "options" => "name='fps' placeholder='FPS' required"),
		array("type" => "select", "size" => "3", "options" => "name='type' required", "choices" => array(
			"Music video (English)", "Music video (Japanese)", "Music video (French)", "Music video (Russian)", "Funny video", "Anime", "Clip", "Travel", "Local", "Gaming"
		)),
		array("type" => "submit", "size" => "2", "options" => "value='Create video'")
	)),

	array("title" => "Create database entry for large video", "icon" => "database", "action" => "large_video.py", "inputs" => array(
		array("type" => "text", "size" => "7", "options" => "name='name' maxlength='100' placeholder='Video name' required"),
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='150' placeholder='Location of the video (folder/file.ext)' required"),
		array("type" => "row"),
		array("type" => "text", "size" => "5", "options" => "name='duration' maxlength='10' placeholder='Length of the video' required"),
		array("type" => "number", "size" => "5", "options" => "name='size' placeholder='Size of the file' required"),

		array("type" => "submit", "size" => "2", "options" => "value='Create entry'")
	)),

	array("title" => "Play a random music video on status screen", "icon" => "random", "action" => "localplay_random.py", "inputs" => array(
		array("type" => "submit", "size" => "3", "options" => "value='Play random'")
	)),

	array("title" => "Play a video URL or local wallpaper on status screen", "icon" => "youtube", "action" => "localplay.py", "inputs" => array(
		array("type" => "text", "size" => "10", "options" => "name='url' maxlength='200' placeholder='Video URL' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Play'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"videos" => "Videos library"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>
