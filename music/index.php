<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-music"></i></a> Music</h3>

<style>
audio::-internal-media-controls-download-button {
    display:none;
}
audio::-webkit-media-controls-enclosure {
    overflow:hidden;
}
audio::-webkit-media-controls-panel {
    width: calc(100% + 33px);
}
</style>
<p>
	<audio id="audio" style="width:100%;" controls>
		<source id="audio_source" src="" type="audio/mpeg">
	</audio>
</p>
<br>

<script>
function player(url)
{
	var audio = document.getElementById('audio');
	var source = document.getElementById('audio_source');
	source.src = url;
	audio.load();
	audio.play();
};
</script>

<?php
tabletop("music", "<tr><th style='display:none'></th><th data-priority='1'>Title</th><th data-priority='1'>Artist</th><th data-priority='2'>Length</th><th data-priority='2'>Size</th></tr>", 1);
$totalsize = 0;
$totalmins = 0;
$totalsecs = 0;
$results = $db->query("SELECT * FROM music ORDER BY id DESC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td style='display:none'>" . $result['id'] . "</td><td>";
	echo "<i class='fa fa-" . fileicon($result['url']) . "'></i> &nbsp; <a href='javascript:player(\"https://" . str_replace('[bucket]', $CONFIG['BUCKET_MUSIC'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "\");'>" . $result['title'] . "</a></td><td>" . $result['artist'];
	echo "</td><td>" . $result['duration'];
	echo "</td><td data-sort='" . $result['size'] . "'>" . size_format($result['size']);
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['title'] . "\" data-artist=\"" . $result['artist'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td></tr>\n";
	$totalsize = $totalsize + intval($result['size']);
	if(str_contains($result['duration'], ':'))
	{
		$a = explode(":", $result['duration']);
		$totalmins = $totalmins + intval($a[0]);
		$totalsecs = $totalsecs + intval($a[1]);
	}
}
$totalmins = $totalmins + intval($totalsecs / 60);
$totalhours = $totalmins / 60;
tablebottom("music", "0", "desc");
?>

<p>Total size: <b><?php echo size_format($totalsize); ?> (<?php echo number_format($totalhours,1); ?> hours)</b></p>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Title:", "options" => "maxlength='30' required"),
	array("type" => "text", "var" => "artist", "label" => "Artist:", "options" => "maxlength='30' required"),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly"),
)); }

if($login_admin == 1) { pipelines("Music pipelines", array(

	array("title" => "Import MP3 from YouTube", "icon" => "youtube", "action" => "create.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='url' maxlength='150' placeholder='YouTube URL' required"),
		array("type" => "text", "size" => "3", "options" => "name='title' maxlength='30' placeholder='Title' required"),
		array("type" => "text", "size" => "3", "options" => "name='artist' maxlength='30' placeholder='Artist' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Add music'")
	)),

	array("title" => "Export music archive", "icon" => "cloud-download", "action" => "archive.py", "inputs" => array(
		array("type" => "number", "size" => "9", "options" => "name='songs' placeholder='Current number of songs' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Export music archive'"),
		array("type" => "row"),
		array("type" => "label", "size" => "12", "label" => "Download link: <a href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_FILES'], $CONFIG['STORAGE_HOST']) . "/Share/music.zip'>https://" . str_replace('[bucket]', $CONFIG['BUCKET_FILES'], $CONFIG['STORAGE_HOST']) . "/Share/music.zip</a>", "options" => "")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"music" => "Music library"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

