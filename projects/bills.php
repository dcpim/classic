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
    $default_rate = $result['default_rate'];
    $default_hours = $result['default_hours'];
    $end_date = $result['end_date'];
    if($result['end_date'] == "") { $end_date = "(none)"; }
}
?>

<h3><a title='Back to project' href="./?id=<?php echo $id; ?>"><i class="fa fa-money"></i></a> Bills - <?php echo $name; ?></h3>

<br>

<?php
tabletop("bills", "<tr><th>ID</th><th>Description</th><th>Amount</th><th>Date</th></tr>");
$results = $db->query("SELECT * FROM bills WHERE prjid = " . $id . " ORDER BY id ASC;");
while($result = $results->fetch_assoc())
{
	$amount = 0;
    $results2 = $db->query("SELECT rate,qty FROM billables WHERE billid = " . $result['id'] . ";");
    while($result2 = $results2->fetch_assoc()) { $amount += ($result2['rate']*$result2['qty']); }
    echo "<tr><td><a href='./billables.php?id=" . $result['id'] . "'>" . $result['id'] . "</a></td><td>" . $result['note'] . "</td><td>$" . number_format($amount,2) . "</td><td>" . $result['date'];
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-hours='" . $result['hours'] . "' data-date='" . $result['date'] . "' data-discount='" . $result['discount'] . "' data-rate='" . $result['rate'] . "' data-tax_location='" . $result['tax_location'] . "' data-note=\"" . $result['note'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td></tr>";
}
tablebottom("bills", "0", "asc");
?>

<?php
if($login_admin == 1) { modal("update_bill.py", "", array(
	array("type" => "text", "var" => "note", "label" => "Description:", "options" => "maxlength='50' required"),
	array("type" => "date", "var" => "date", "label" => "Invoice date:", "options" => "maxlength='20' required"),
	array("type" => "number", "var" => "rate", "label" => "Billing rate:", "options" => "required"),
	array("type" => "number", "var" => "hours", "label" => "Hours per day:", "options" => "required"),
	array("type" => "number", "var" => "discount", "label" => "Discount:", "options" => "step='0.01'"),
	array("type" => "select", "var" => "tax_location", "label" => "Tax location:", "options" => "required", "choices" => array(
		"Quebec", "Ontario", "Canada", "Other"
	))
)); }

if($login_admin == 1) { pipelines("Bills pipelines", array(

	array("title" => "Create a new invoice for this project", "icon" => "money", "action" => "create_bill.py", "inputs" => array(
		array("type" => "text", "size" => "6", "options" => "name='note' maxlength='50' placeholder='Description' required"),
		array("type" => "hidden", "size" => "0", "options" => "name='rate' value='" . $default_rate . "' required"),
		array("type" => "hidden", "size" => "0", "options" => "name='hours' value='" . $default_hours . "' required"),
		array("type" => "hidden", "size" => "0", "options" => "name='prjid' value='" . $id . "' required"),
		array("type" => "date", "size" => "2", "options" => "name='date' maxlength='20' placeholder='Due date' required"),
		array("type" => "select", "size" => "2", "options" => "name='tax_location' required", "choices" => array(
			"Quebec", "Ontario", "Canada", "Other"
		)),
		array("type" => "submit", "size" => "2", "options" => "value='Create'")
	)),

)); }
?>

<?php include '../bottom.php'; ?>
