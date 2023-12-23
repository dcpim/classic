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

<h3><a title='Back to project' href="./?id=<?php echo $id; ?>"><i class="fa fa-globe"></i></a> Bookmarks - <?php echo $name; ?></h3>

<br>

<?php
$colors = array("None" => "#FFFFFF");
$results = $db->query("SELECT * FROM bookmark_sections WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
	$colors[$result['name']] = $result['color'];
}
$results = $db->query("SELECT * FROM bookmarks WHERE prjid = " . $id . " ORDER BY section, name ASC;");
while($result = $results->fetch_assoc())
{
    echo "<div class='thumbnail' style='background-color:" . $colors[$result['section']] . ";";
	if($darkmode) { echo "border-width:1px !important;border-color:" . $colors[$result['section']] . " !important"; }
	echo "'>";
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-date='" . $result['date'] . "' data-prjid='" . $result['prjid'] . "' data-notes=\"" . $result['notes'] . "\" data-section=\"" . $result['section'] . "\" data-name=\"" . $result['name'] . "\" data-url=\"" . $result['url'] . "\" data-type='" . $result['type'] . "'><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "<h4><i class='fa fa-" . $result['type'] . "'></i> " . $result['section'] . " / " . $result['name'] . "</h4><a target=_new href='" . $result['url'] . "'>" . $result['url'] . "</a>";
	echo "<br><br><p>" . $result['notes'] . "</p>";
	echo "</div>";
}
?>

<?php
$sections = array("None");
$results = $db->query("SELECT * FROM bookmark_sections WHERE prjid = " . $id . " ORDER BY name;");
while($result = $results->fetch_assoc()) { array_push($sections, $result['name']); }

if($login_admin == 1) { modal("update_bookmark.py", "delete_bookmark.py", array(
	array("type" => "text", "var" => "name", "label" => "Description:", "options" => "maxlength='250' required"),
	array("type" => "text", "var" => "url", "label" => "URL:", "options" => "maxlength='250' required"),
	array("type" => "select", "var" => "type", "label" => "Type:", "options" => "required", "choices" => array(
		"globe", "youtube", "file", "camera", "question"
	)),
	array("type" => "select", "var" => "section", "options" => "required", "choices" => $sections),
	array("type" => "textarea", "var" => "notes", "label" => "Notes:", "options" => "maxlength='1000'"),
	array("type" => "date", "var" => "date", "label" => "Date:", "options" => "readonly"),
	array("type" => "hidden", "var" => "prjid")
)); }

if($login_admin == 1) { pipelines("Bookmark pipelines", array(

	array("title" => "Add a new bookmark", "icon" => "file-text-o", "action" => "create_bookmark.py", "inputs" => array(
		array("type" => "text", "size" => "3", "options" => "name='name' maxlength='250' placeholder='Description' required"),
		array("type" => "text", "size" => "4", "options" => "name='url' maxlength='250' placeholder='URL' required"),
		array("type" => "select", "size" => "3", "options" => "name='section' required", "choices" => $sections),
		array("type" => "hidden", "size" => "0", "options" => "name='prjid' value='" . $id . "' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Create bookmark'")
	)),

	array("title" => "Add a new section", "icon" => "book", "action" => "create_bookmark_section.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='150' placeholder='Section name' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Create section'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"bookmarks" => "List of bookmarks"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

