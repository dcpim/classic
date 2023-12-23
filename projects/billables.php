<?php include '../top.php'; ?>

<?php
$billid = intval($_GET['id']);
$results = $db->query("SELECT * FROM bills WHERE id = " . $billid . " ORDER BY id ASC;");
while($result = $results->fetch_assoc())
{
	$prjid = $result['prjid'];
	$hourly = $result['hours'];
	$default_rate = $result['rate'];
}
?>

<h3><a title='Back to bills' href="bills.php?id=<?php echo $prjid; ?>"><i class="fa fa-money"></i></a> Billables - Invoice #<?php echo $billid; ?></h3>

<br>

<?php
tabletop("bills", "<tr><th>Description</th><th>Rate</th><th>Qty</th><th>Amount</th></tr>");
$balance = 0;
$qtys = 0;
$results = $db->query("SELECT * FROM billables WHERE billid = " . $billid . " ORDER BY id ASC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>";
	echo $result['note'] . "</a></td><td>$" . number_format($result['rate'],2) . "</td><td>" . $result['qty'] . "</td><td>$" . number_format($result['rate']*$result['qty'],2);
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-rate='" . $result['rate'] . "' data-qty='" . $result['qty'] . "' data-note=\"" . $result['note'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
 	echo "</td></tr>";
	$balance += $result['rate'] * $result['qty'];
	if($default_rate == $result['rate']) { $qtys += $result['qty']; }
}
?>
    </tbody>
	<tfoot><tr><th>Totals</th><th></th><th><?php echo $qtys; if($hourly > 0) { echo "h<br>" . ($qtys/$hourly) . "d"; } ?></th><th>$<?php echo number_format($balance,2); ?></th></tr></tfoot>
</table>
<script>$(document).ready(function(){$('#bills').DataTable({'oSearch':{'sSearch':search},'aLengthMenu':[10, 25, 50, 100, 500],'order':[[0,'asc']]});});</script>

<?php
if($login_admin == 1) { modal("update_billable.py", "", array(
	array("type" => "text", "var" => "note", "label" => "Description:", "options" => "maxlength='150' required"),
	array("type" => "number", "var" => "qty", "label" => "Quantity:", "options" => "required"),
	array("type" => "number", "var" => "rate", "label" => "Rate:", "options" => "step='0.01' required")
)); }

if($login_admin == 1) { pipelines("Billables pipelines", array(

	array("title" => "Add new billable to this invoice", "icon" => "money", "action" => "create_billable.py", "inputs" => array(
		array("type" => "text", "size" => "6", "options" => "name='note' maxlength='150' placeholder='Description' required"),
		array("type" => "hidden", "size" => "0", "options" => "name='billid' value='" . $billid . "' required"),
		array("type" => "number", "size" => "2", "options" => "name='qty' placeholder='Quantity' required"),
		array("type" => "number", "size" => "2", "options" => "name='rate' step='0.01' placeholder='Rate' value='" . $default_rate . "' required"),
		array("type" => "submit", "size" => "2", "options" => "value='Add'")
	)),

)); }
?>

<?php include '../bottom.php'; ?>

