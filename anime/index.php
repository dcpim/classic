<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-tv"></i></a> Anime</h3>

<?php
tabletop("anime", "<tr><th>Title</th><th>Rating</th></tr>");
$totalsize = 0;
$results = $db->query("SELECT * FROM series;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td><a target='_new' href='" . $result['url'] . "'>" . $result['title'] . "</a>";
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-title=\"" . $result['title'] . "\" data-review=\"" . $result['review'] . "\" data-date='" . $result['date'] . "' data-stars='" . $result['stars'] . "' data-url=\"" . $result['url'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td><td data-sort='" . $result['stars'] . "'><center>";
	for ($x = 1; $x <= $result['stars']; $x++) { echo "<i class='fa fa-star'></i>"; }
	echo "</center></td></tr>";
}
tablebottom("anime", "0", "asc");
?>

<?php
if($login_admin == 1) { modal("update.py", "delete.py",array(
	array("type" => "text", "var" => "title", "label" => "Title:", "options" => "maxlength='50' required"),
	array("type" => "text", "var" => "url", "label" => "URL:", "options" => "maxlength='150' required"),
	array("type" => "textarea", "var" => "review", "label" => "Review:", "options" => "maxlength='2000'"),
	array("type" => "select", "var" => "stars", "label" => "Rating:", "options" => "required", "choices" => array(
		"1", "2", "3", "4", "5"
	)),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly")
)); }

if($login_admin == 1) { pipelines("Anime pipelines", array(

	array("title" => "Add a watched anime series", "icon" => "desktop", "action" => "./create.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='title' maxlength='50' placeholder='Title' required"),
		array("type" => "text", "size" => "4", "options" => "name='url' maxlength='150' placeholder='URL' required"),
		array("type" => "select", "size" => "1", "options" => "name='stars' required", "choices" => array(
			"1", "2", "3", "4", "5"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Add series'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"anime" => "List of Anime series"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

