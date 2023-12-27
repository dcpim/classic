<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-files-o"></i></a> Files</h3>

<?php
tabletop("files", "<tr><th>Name</th><th>Type</th><th>Size</th></tr>");
$totalsize = 0;
$results = $db->query("SELECT * FROM utils ORDER BY name ASC;");
while($result = $results->fetch_assoc())
{
	if($login_admin == 1 || $result['type'] != "Private")
	{
	    echo "<tr><td><i class='fa fa-" . fileicon($result['url']) . "'></i> &nbsp; <a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_FILES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>" . $result['name'] . "</a></td><td>" . $result['type'] . "</td><td data-sort='" . $result['size'] . "'>";
		if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
		echo size_format($result['size']) . "</td></tr>";
	}
	$totalsize = $totalsize + intval($result['size']);
}
$results = $db->query("SELECT size FROM project_files;");
while($result = $results->fetch_assoc()) { $totalsize = $totalsize + intval($result['size']); }
$results = $db->query("SELECT size FROM education_files;");
while($result = $results->fetch_assoc()) { $totalsize = $totalsize + intval($result['size']); }
$results = $db->query("SELECT size FROM medical_files;");
while($result = $results->fetch_assoc()) { $totalsize = $totalsize + intval($result['size']); }
tablebottom("files", "0", "asc");
?>

<p>Total size: <b><?php echo size_format($totalsize); ?></b></p>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50' required"),
	array("type" => "select", "var" => "type", "label" => "Type:", "options" => "required", "choices" => array(
		"Utility", "Reference", "Share", "Private"
	)),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly")
)); }

if($login_admin == 1) { pipelines("Files pipelines", array(

 	array("title" => "Upload a file", "icon" => "upload", "action" => "create.py", "inputs" => array(
		array("type" => "file", "size" => "3", "options" => "name='filename' required"),
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='50' placeholder='Name of file' required"),
		array("type" => "select", "size" => "2", "options" => "name='type' required", "choices" => array(
			"Share", "Utility", "Reference", "Private"
		)),
		array("type" => "submit", "size" => "2", "options" => "value='Add file'")
	)),

	array("title" => "Convert ZIP archive of images into PDF", "icon" => "book", "action" => "makepdf.py", "inputs" => array(
		array("type" => "file", "size" => "3", "options" => "name='filename' required"),
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='50' placeholder='Name of file' required"),
		array("type" => "select", "size" => "2", "options" => "name='type' required", "choices" => array(
			"Share", "Utility", "Reference", "Private"
		)),
		array("type" => "submit", "size" => "2", "options" => "value='Make PDF'")
	)),

	array("title" => "Upload a file to local filesystem", "icon" => "cloud-upload", "action" => "upload.py", "inputs" => array(
		array("type" => "file", "size" => "9", "options" => "name='filename' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Upload file'")
	)),

	array("title" => "Upload a file to public share", "icon" => "share", "action" => ".upload_share.py", "inputs" => array(
		array("type" => "file", "size" => "4", "options" => "name='filename' required"),
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='50' placeholder='Name of file' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Upload file'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"files" => "Files library"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

