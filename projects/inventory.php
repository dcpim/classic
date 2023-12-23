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

<h3><a title='Back to project' href="./?id=<?php echo $id; ?>"><i class="fa fa-cubes"></i></a> Items inventory - <?php echo $name; ?></h3>

<br>

<?php
tabletop("items", "<tr><th>Item name</th><th>Serial number</th><th>Price</th><th>Date</th></tr>");
$results = $db->query("SELECT * FROM inventory WHERE prjid = " . $id . " ORDER BY id ASC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>";
	if($result['sold'] == 1 || $result['sold'] == 4) { echo "<strike>"; }
	if($result['link'] != "") { echo "<a href='" . $result['link'] . "'>"; }
	echo $result['name'];
	if($result['link'] != "") { echo "</a>"; }
	if($result['sold'] == 1 || $result['sold'] == 4) { echo "</strike>"; }
	echo "</td><td>" . $result['serial'] . "</td><td>$" . number_format($result['price'],2) . "</td><td>" . $result['date'] . "<span style='float:right'>";
	if($login_admin == 1) { echo "<a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-date='" . $result['date'] . "' data-serial=\"" . $result['serial'] . "\" data-link=\"" . $result['link'] . "\" data-notes=\"" . $result['notes'] . "\" data-sold='" . $result['sold'] . "' data-price='" . $result['price'] . "' data-name=\"" . $result['name'] . "\" data-statement=\"" . $result['statement'] . "\" data-invoice=\"" . $result['invoice'] . "\"><i class='fa fa-pencil-square-o'></i></a> "; }
	echo "</span></td></tr>";
}
tablebottom("items", "0", "asc");
?>

<?php
if($login_admin == 1) { modal("update_item.py", "delete_item.py", array(
	array("type" => "text", "var" => "name", "label" => "Item name:", "options" => "maxlength='50' required"),
	array("type" => "date", "var" => "date", "label" => "Date purchased:", "options" => "maxlength='20' required"),
	array("type" => "number", "var" => "price", "label" => "Price paid:", "options" => "step='0.01' required"),
	array("type" => "text", "var" => "serial", "label" => "Serial number:", "options" => "maxlength='50' required"),
	array("type" => "text", "var" => "link", "label" => "External link:", "options" => "maxlength='350'"),
	array("type" => "textarea", "var" => "notes", "label" => "Notes:", "options" => "maxlength='1000'"),
	array("type" => "selectkv", "var" => "sold", "label" => "Status:", "options" => "", "choices" => array(
		"0" => "In use", "1" => "Sold", "5" => "In storage", "2" => "Puchased", "3" => "On loan", "4">" Returned / disposed of", "6" => "For emergency use", "7" => "Used in EDC"
	)),
	array("type" => "hidden", "var" => "invoice"),
	array("type" => "hidden", "var" => "statement")
)); }

if($login_admin == 1) { pipelines("Project inventory pipelines", array(

	array("title" => "Add a new item to this project", "icon" => "truck", "action" => "./create_item.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='50' placeholder='Item name' required"),
		array("type" => "number", "size" => "3", "options" => "name='price' step='0.01' placeholder='Price paid' required"),
		array("type" => "text", "size" => "3", "options" => "name='serial' maxlength='50' placeholder='Serial number' required"),
		array("type" => "hidden", "size" => "0", "options" => "name='prjid' value='" . $id . "' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Add item'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"items" => "List of inventory items"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

