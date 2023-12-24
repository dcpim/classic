<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-picture-o"></i></a> Anime wallpapers</h3>

<form method="POST" action="create.py" enctype="multipart/form-data" onSubmit="document.getElementById('submit').disabled=true;">
	<div class="row">
<?php if($login_admin == 1) { ?>
		<div class="col-md-4">
			<input class="form-control" type="file" name="filename" required>
		</div>
		<div class="col-md-2">
			<select class="form-control" name="type"><option>Phone</option><option>Desktop</option></select>
		</div>
		<div class="col-md-1">
			<input class="form-control btn btn-primary" type="submit" value="Add" id="submit">
		</div>
<?php } ?>
	    <div class="col-md-4">
    	    <script>
    	        function dofilter()
    	        {
    	            window.location.replace("./?tag=" + encodeURIComponent($("#filter").find('option:selected').val()));
    	        }
    	    </script>
    	    <select class="form-control" onchange="dofilter()" id="filter"><option>Tag filter...</option><option value="">All</option><option value="ai">AI</option><option value="action">Action</option><option value="bondage">Bondage</option><option value="muscles">Muscular</option><option value="nsfw">Nudity</option><option value="jk">School uniform</option><option value="loli">Teenager</option><option value="casual">Thighs</option><option value="yuri">Yuri</option></select>
		</div>
	</div>
</form>

<script>
$(window).load(function()
{
	$(".menulink").show();
	$("#loading").hide();
});
</script>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#phone">Phone</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#desktop">Desktop</a></li>
    <li><a id="loading"><b>Loading...</b></a></li>
</ul>

<style>
.cell { position: relative; }
.cell img { display: block; }
.cell span { position: absolute; bottom:0; left:0; }
</style>

<div id="gallery">
<div class="tab-content">
<?php
$totalsize = 0;
$totalnum = 0;
$filter = preg_replace("/[^A-Za-z0-9 ]/", '', $_GET['tag']);
?>
    <div id="phone" class="tab-pane fade in active">
<?php
if($filter != "") { $results = $db->query("SELECT * FROM wallpapers WHERE landscape = 0 AND tag_" . $filter . " = 1 ORDER BY id DESC;"); }
else { $results = $db->query("SELECT * FROM wallpapers WHERE landscape = 0 ORDER BY id DESC;"); }
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-landscape='" . $result['landscape'] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-tag_action='" . $result['tag_action'] . "' data-tag_loli='" . $result['tag_loli'] . "' data-tag_jk='" . $result['tag_jk'] . "' data-tag_bondage='" . $result['tag_bondage'] . "' data-tag_casual='" . $result['tag_casual'] . "' data-tag_muscles='" . $result['tag_muscles'] . "' data-tag_nsfw='" . $result['tag_nsfw'] . "' data-tag_yuri='" . $result['tag_yuri'] . "' data-tag_ai='" . $result['tag_ai'] . "' data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
	$totalnum++;
}
?>
    </div>
    <div id="desktop" class="tab-pane fade in">
<?php
if($filter != "") { $results = $db->query("SELECT * FROM wallpapers WHERE landscape = 1 AND tag_" . $filter . " = 1 ORDER BY id DESC;"); }
else { $results = $db->query("SELECT * FROM wallpapers WHERE landscape = 1 ORDER BY id DESC;"); }
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-landscape='" . $result['landscape'] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\"  data-tag_action='" . $result['tag_action'] . "' data-tag_loli='" . $result['tag_loli'] . "' data-tag_jk='" . $result['tag_jk'] . "' data-tag_bondage='" . $result['tag_bondage'] . "' data-tag_casual='" . $result['tag_casual'] . "' data-tag_muscles='" . $result['tag_muscles'] . "' data-tag_nsfw='" . $result['tag_nsfw'] . "' data-tag_yuri='" . $result['tag_yuri'] . "' data-tag_ai='" . $result['tag_ai'] . "' data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
	echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
	$totalnum++;
}
?>
	</div>
</div>
</div>

<p>Total size: <b><?php echo size_format($totalsize); ?></b></p>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50'"),
	array("type" => "selectkv", "var" => "landscape", "label" => "Orientation:", "options" => "required", "choices" => array(
		"0" => "Phone", "1" => "Desktop"
	)),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly"),
	array("type" => "checkbox", "var" => "tag_ai", "label" => "AI", "options" => ""),
	array("type" => "checkbox", "var" => "tag_action", "label" => "Action", "options" => ""),
	array("type" => "checkbox", "var" => "tag_bondage", "label" => "Bondage", "options" => ""),
	array("type" => "checkbox", "var" => "tag_muscles", "label" => "Muscular", "options" => ""),
	array("type" => "checkbox", "var" => "tag_nsfw", "label" => "Nudity", "options" => ""),
	array("type" => "checkbox", "var" => "tag_jk", "label" => "School uniform", "options" => ""),
	array("type" => "checkbox", "var" => "tag_loli", "label" => "Teenager", "options" => ""),
	array("type" => "checkbox", "var" => "tag_casual", "label" => "Thighs", "options" => ""),
	array("type" => "checkbox", "var" => "tag_yuri", "label" => "Yuri", "options" => "")
), "\$('#img' + id + ' img').attr('title', name); \$('#img' + id + ' span a').data('landscape', landscape); \$('#img' + id + ' span a').data('name', name); \$('#img' + id + ' span a').data('tag_action', tag_action); \$('#img' + id + ' span a').data('tag_bondage', tag_bondage); \$('#img' + id + ' span a').data('tag_casual', tag_casual); \$('#img' + id + ' span a').data('tag_jk', tag_jk); \$('#img' + id + ' span a').data('tag_loli', tag_loli); \$('#img' + id + ' span a').data('tag_muscles', tag_muscles); \$('#img' + id + ' span a').data('tag_nsfw', tag_nsfw); \$('#img' + id + ' span a').data('tag_yuri', tag_yuri); \$('#img' + id + ' span a').data('tag_ai', tag_ai);"); }
?>

<?php include '../bottom.php'; ?>

