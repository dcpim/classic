<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-female"></i></a> Art</h3>

<style>
.cell { position: relative; }
.cell img { display: block; }
.cell span { position: absolute; bottom:0; left:0; }
</style>

<?php if($login_admin == 1) { ?>
<form method="POST" action="create.py" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-4">
			<input class="form-control" type="file" name="filename" required>
		</div>
		<div class="col-md-2">
			<select class="form-control" name="genre"><option>AI</option><option>Comics</option><option>Fantasy</option><option>Fractal</option><option>Sci-fi</option><option>Star Wars</option><option>Urban</option></select>
		</div>
		<div class="col-md-5">
			<input class="form-control" placeholder="Name" type="text" name="name" required>
		</div>
		<div class="col-md-1">
			<input class="form-control btn btn-primary" type="submit" value="Add">
		</div>
	</div>
</form>
<?php } ?>

<script>
$(window).load(function(){
    $(".menulink").show();
    $("#loading").hide();
});
</script>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#AI">AI</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#Comics">Comics</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#Fantasy">Fantasy</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#Fractal">Fractal</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#Sci-fi">Sci-fi</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#Star_Wars">Star Wars</a></li>
    <li><a class="menulink" style="display:none" data-toggle="tab" href="#Urban">Urban</a></li>
    <li><a id="loading"><b>Loading...</b></a></li>
</ul>

<div id="gallery">
<div class="tab-content">
	<div id="AI" class="tab-pane fade in active">
<?php
$totalsize = 0;
$results = $db->query("SELECT COUNT(*) FROM renders WHERE genre = 'AI';");
while($result = $results->fetch_assoc())
{
	echo "<h3>AI (" . $result['COUNT(*)'] . ")</h4>";
}
$results = $db->query("SELECT * FROM renders WHERE genre = 'AI' ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-genre=\"" . $result['genre'] . "\" data-name=\"" . $result['name'] . "\" data-desc=\"" . str_replace('"', '&quot;', $result['description']) . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
    echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="Comics" class="tab-pane fade in">
<?php
$results = $db->query("SELECT COUNT(*) FROM renders WHERE genre = 'Comics';");
while($result = $results->fetch_assoc())
{
	echo "<h3>Comics (" . $result['COUNT(*)'] . ")</h4>";
}
$results = $db->query("SELECT * FROM renders WHERE genre = 'Comics' ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-genre=\"" . $result['genre'] . "\" data-name=\"" . $result['name'] . "\" data-desc=\"" . str_replace('"', '&quot;', $result['description']) . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
    echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="Fantasy" class="tab-pane fade in">
<?php
$results = $db->query("SELECT COUNT(*) FROM renders WHERE genre = 'Fantasy';");
while($result = $results->fetch_assoc())
{
	echo "<h3>Fantasy (" . $result['COUNT(*)'] . ")</h4>";
}
$results = $db->query("SELECT * FROM renders WHERE genre = 'Fantasy' ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-genre=\"" . $result['genre'] . "\" data-name=\"" . $result['name'] . "\" data-desc=\"" . str_replace('"', '&quot;', $result['description']) . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
    echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="Fractal" class="tab-pane fade in">
<?php
$results = $db->query("SELECT COUNT(*) FROM renders WHERE genre = 'Fractal';");
while($result = $results->fetch_assoc())
{
	echo "<h3>Fractal (" . $result['COUNT(*)'] . ")</h4>";
}
$results = $db->query("SELECT * FROM renders WHERE genre = 'Fractal' ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-genre=\"" . $result['genre'] . "\" data-name=\"" . $result['name'] . "\" data-desc=\"" . str_replace('"', '&quot;', $result['description']) . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
    echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="Sci-fi" class="tab-pane fade in">
<?php
$results = $db->query("SELECT COUNT(*) FROM renders WHERE genre = 'Sci-fi';");
while($result = $results->fetch_assoc())
{
	echo "<h3>Sci-fi (" . $result['COUNT(*)'] . ")</h4>";
}
$results = $db->query("SELECT * FROM renders WHERE genre = 'Sci-fi' ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-genre=\"" . $result['genre'] . "\" data-name=\"" . $result['name'] . "\" data-desc=\"" . str_replace('"', '&quot;', $result['description']) . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
    echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="Star_Wars" class="tab-pane fade in">
<?php
$results = $db->query("SELECT COUNT(*) FROM renders WHERE genre = 'Star Wars';");
while($result = $results->fetch_assoc())
{
	echo "<h3>Star Wars (" . $result['COUNT(*)'] . ")</h4>";
}
$results = $db->query("SELECT * FROM renders WHERE genre = 'Star Wars' ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-genre=\"" . $result['genre'] . "\" data-name=\"" . $result['name'] . "\" data-desc=\"" . str_replace('"', '&quot;', $result['description']) . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
    echo "</div>";
	$totalsize = $totalsize + intval($result['size']);
}
?>
	</div>

	<div id="Urban" class="tab-pane fade in">
<?php
$results = $db->query("SELECT COUNT(*) FROM renders WHERE genre = 'Urban';");
while($result = $results->fetch_assoc())
{
	echo "<h3>Urban (" . $result['COUNT(*)'] . ")</h4>";
}
$results = $db->query("SELECT * FROM renders WHERE genre = 'Urban' ORDER BY date DESC;");
while($result = $results->fetch_assoc())
{
	echo "<div id='img" . $result['id'] . "' class='cell img-thumbnail'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "' title=\"" . $result['name'] . "\"></a>";
    if($login_admin == 1) { echo "<span><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-genre=\"" . $result['genre'] . "\" data-name=\"" . $result['name'] . "\" data-desc=\"" . str_replace('"', '&quot;', $result['description']) . "\" data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o fa-2x'></i></a></span>"; }
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
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50'"),
	array("type" => "select", "var" => "genre", "label" => "Genre:", "options" => "required", "choices" => array(
		"AI", "Comics", "Fractal", "Fantasy", "Urban", "Sci-fi", "Star Wars"
	)),
	array("type" => "textarea", "var" => "desc", "label" => "Description:", "options" => "maxlength='20000'"),
	array("type" => "date", "var" => "date", "label" => "Date:", "options" => "maxlength='20'")
), "\$('#img' + id + ' img').attr('title', name); \$('#img' + id + ' span a').data('name', name); \$('#img' + id + ' span a').data('genre', genre); \$('#img' + id + ' span a').data('desc', desc); \$('#img' + id + ' span a').data('date', date);"); }
?>

<?php include '../bottom.php'; ?>

