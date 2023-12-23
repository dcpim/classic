<?php include '../top.php'; ?>

<?php
$results = $db->query("SELECT * FROM projects WHERE id = " . intval($_GET['id']) . ";");
while($result = $results->fetch_assoc())
{
    $id = $result['id'];
    $name = $result['name'];
    $client = $result['client'];
    $notes = $result['notes'];
    $date = $result['date'];
    $address = $result['address'];
    $end_date = $result['end_date'];
    if($result['end_date'] == "") { $end_date = "(none)"; }
}
?>

<h3><a title="Back to project" href="./?id=<?php echo $id; ?>"><i class="fa fa-files-o"></i></a> Files - <?php echo $name; ?></h3>

<br>

<?php
tabletop("files", "<tr><th>Name</th><th>Size</th><th>Date</th></tr>");
$results = $db->query("SELECT * FROM project_files WHERE prjid = " . $id . " ORDER BY name ASC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>";
	echo "<i class='fa fa-" . fileicon($result['url']) . "'></i> &nbsp; <a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_FILES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>" . $result['name'] . "</a></td><td data-sort='" . $result['size'] . "'>";
	echo size_format($result['size']) . "</td><td>";
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . explode(' ',$result['date'])[0] . "' data-id='" . $result['id'] . "' data-prjid='" . $result['prjid'] . "' data-notes=\"" . $result['notes'] . "\" data-name=\"" . $result['name'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo explode(' ',$result['date'])[0] . "</td></tr>";
}
tablebottom("files", "0", "asc");
?>

<?php
if($login_admin == 1) { modal("update_file.py", "delete_file.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50' required"),
	array("type" => "date", "var" => "date", "label" => "Date:", "options" => "maxlength='20' required"),
	array("type" => "textarea", "var" => "notes", "label" => "Notes:", "options" => "maxlength='1000'"),
	array("type" => "hidden", "var" => "prjid")
)); }

if($login_admin == 1) { pipelines("Project files pipelines", array(

	array("title" => "Upload a file to this project", "icon" => "upload", "action" => "./upload_file.py", "inputs" => array(
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='50' placeholder='Name of file' required"),
		array("type" => "file", "size" => "4", "options" => "name='filename' required"),
		array("type" => "hidden", "size" => "0", "options" => "name='id' value='" . $id . "' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Upload file'")
	)),

	array("title" => "Add large file to database", "icon" => "archive", "action" => "./large_file.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='50' placeholder='Name of file' required"),
		array("type" => "text", "size" => "3", "options" => "name='url' maxlength='100' placeholder='URL (folder/file.ext)' required"),
		array("type" => "number", "size" => "3", "options" => "name='size' placeholder='Size in bytes' required"),
		array("type" => "hidden", "size" => "0", "options" => "name='id' value='" . $id . "' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Add entry'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"project_files" => "List of files"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

