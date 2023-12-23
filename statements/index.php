<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-book"></i></a> Accounting statements</h3>

<?php
tabletop("acc", "<tr><th data-priority='1'>Document</th><th data-priority='2'>Type</th><th data-priority='2'>Scope</th><th data-priority='1'>Date</th></tr>");
$results = $db->query("SELECT * FROM statements ORDER BY date ASC;");
while($result = $results->fetch_assoc())
{
	echo "<tr";
	if(strpos($result['date'], $_GET['date']) !== false and strpos($result['type'], $_GET['type']) !== false and strpos($result['scope'], $_GET['scope']) !== false) { echo " style='background-color: yellow'"; }
	if(strpos($result['id'], $_GET['id']) !== false) { echo " style='background-color: yellow'"; }
	echo "><td><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_ACCOUNTING'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>" . $result['name'] . "</a></td><td>" . $result['type'] . "</td><td>" . $result['scope'] . "</td><td>" . $result['date'];
    if($login_admin == 1) { echo "<span style='float:right'><a class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . $result['date'] . "' data-scope='" . $result['scope'] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\" data-type=\"" . $result['type'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td></tr>";
}
tablebottom("acc", "3", "asc");
?>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50' required"),
	array("type" => "select", "var" => "type", "label" => "Type:", "options" => "required", "choices" => array(
		"Bank statement", "Credit card statement", "PayPal statement", "Investment statement", "Financial statement", "Business document", "Procurement", "Taxes"
	)),
	array("type" => "select", "var" => "scope", "label" => "Scope:", "options" => "required", "choices" => array(
		"Personal", "Business"
	)),
	array("type" => "date", "var" => "date", "label" => "Date:", "options" => "maxlength='20' required")
)); }

if($login_admin == 1) { pipelines("Statement pipelines", array(

	array("title" => "Add a bank, credit card or other statement or tax document", "icon" => "file-o", "action" => "create.py", "inputs" => array(
		array("type" => "file", "size" => "4", "options" => "name='filename' required"),
		array("type" => "text", "size" => "4", "options" => "maxlength='50' name='name' placeholder='Document name' required"),
		array("type" => "date", "size" => "4", "options" => "maxlength='20' name='date' placeholder='Date' required"),
		array("type" => "row"),
		array("type" => "select", "size" => "4", "options" => "name='type' required", "choices" => array(
			"Bank statement", "Credit card statement", "PayPal statement", "Investment statement", "Financial statement", "Business document", "Procurement", "Taxes"
		)),
		array("type" => "select", "size" => "4", "options" => "name='scope' required", "choices" => array(
			"Personal", "Business"
		)),
		array("type" => "empty", "size" => "2"),
		array("type" => "submit", "size" => "2", "options" => "value='Add statement'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"statements" => "List of statements"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

