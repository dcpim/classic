<?php include '../top.php'; ?>

<?php
$results = $db->query("SELECT * FROM projects WHERE id = " . intval($_GET['prjid']) . ";");
while($result = $results->fetch_assoc())
{
    $id = $result['id'];
    $name = $result['name'];
}
?>

<h3><a title="Back to project" href="./?id=<?php echo $id; ?>"><i class="fa fa-pencil"></i></a> Events journal - <?php echo $name; ?></h3>

<form method="POST" action="create_journal.py" enctype="multipart/form-data" onSubmit="document.getElementById('submit').disabled=true;">
	<div class="row">
		<div class="col-md-12">
			<input class="form-control" maxlength='200' type="text" name="title" placeholder="Title" required>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<textarea style="height:100px" class="form-control" maxlength='5000' name="entry" required></textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8" style='margin-top:15px'>
<?php if($_GET['auto']) { ?>
			<a href="./journal.php?prjid=<?php echo $id; ?>">Hide automatic entries</a>
<?php } else { ?>
			<a href="./journal.php?auto=1&prjid=<?php echo $id; ?>">Show automatic entries</a>
<?php } ?>
		</div>
		<div class="col-md-2">
			<select class="form-control" name="mood"><option value="smile-o">Good mood</option><option value="meh-o">Average mood</option><option value="frown-o">Bad mood</option></select>
		</div>
		<div class="col-md-2">
			<input type="hidden" name="prjid" value='<?php echo $id; ?>'>
			<input class="form-control btn btn-primary" type="submit" value="Save" id="submit">
		</div>
	</div>
</form>

<?php
$count = 0;
if($_GET['auto']) { $results = $db->query("SELECT COUNT(*) FROM journal WHERE prjid = " . $id . ";"); }
else { $results = $db->query("SELECT COUNT(*) FROM journal WHERE prjid = " . $id . " AND type = 0;"); }
while($result = $results->fetch_assoc())
{
	$count = $result['COUNT(*)'];
}
if($_GET['auto'])
{
	if($_GET['history']) { $results = $db->query("SELECT * FROM journal WHERE prjid = " . $id . " ORDER BY date DESC;"); }
	else { $results = $db->query("SELECT * FROM journal WHERE prjid = " . $id . " ORDER BY date DESC LIMIT 100;"); }
}
else
{
	if($_GET['history']) { $results = $db->query("SELECT * FROM journal WHERE prjid = " . $id . " AND type = 0 ORDER BY date DESC;"); }
	else { $results = $db->query("SELECT * FROM journal WHERE prjid = " . $id . " AND type = 0 ORDER BY date DESC LIMIT 100;"); }
}
while($result = $results->fetch_assoc())
{
	echo "<div class='img-thumbnail' style='width:99%;padding-left:10px;padding-right:10px;margin-bottom:10px";
	echo "'>";
	echo "<h3 style='margin-top:3px!important;margin-bottom:15px!important;";
	if($result['type'] == 1) { echo "color:#AA2222!important"; }
	echo "'>";
	if($login_admin == 1 && $result['type'] == 0) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-date='" . $result['date'] . "' data-mood='" . $result['mood'] . "' data-prjid='" . $result['prjid'] . "' data-entry=\"" . str_replace("<br>", "\n", $result['entry']) . "\" data-title=\"" . $result['title'] . "\"><i style='color: #3EAEEE !important' class='fa fa-pencil-square-o'></i></a></span>"; }
	echo $result['title'] . "</h3>";
	echo "<p><font size='+1'>" . $result['entry'] . "</font></p>";
	echo "<p><i class='fa fa-" . $result['mood'] . "'></i><span style='float:right'><i>" . $result['date'] . "</i></span></p>";
	echo "</div>\n";
}

if($count > 100 and !$_GET['history'])
{
	echo "<p><center><h3><a href='./journal.php?prjid=" . $id . "&history=1'>See all " . $count . " entries</a></h3></center></p>";
}

if($login_admin == 1) { modal("update_journal.py", "delete_journal.py", array(
	array("type" => "text", "var" => "title", "label" => "Title:", "options" => "maxlength='200' required"),
	array("type" => "textarea", "var" => "entry", "label" => "Entry:", "options" => "maxlength='5000'"),
	array("type" => "selectkv", "var" => "mood", "label" => "Mood:", "options" => "", "choices" => array(
		"meh-o" => "Average mood", "frown-o" => "Bad mood", "smile-o" => "Good mood"
	)),
	array("type" => "date", "var" => "date", "label" => "Date:", "options" => "readonly"),
	array("type" => "hidden", "var" => "prjid")
)); }
?>

<br>

<?php include '../bottom.php'; ?>
