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
	$contact_name = $result['contact_name'];
	$contact_email = $result['contact_email'];
	$contact_phone = $result['contact_phone'];
	$end_date = $result['end_date'];
	$default_rate = $result['default_rate'];
	$default_hours = $result['default_hours'];
	$reason = $result['reason'];
}
?>

<h3>Update Project <?php echo $id; ?></h3><br>

<form method="POST" action="./save.py">

	<h4>Project details</h4>

	<div class="row">
		<div class="col-md-6">
			Project name:
			<input class="form-control" maxlength='50' type="text" name="name" value="<?php echo $name; ?>" required>
		</div>
	</div><br>
	<div class="row">
		<div class="col-md-6">
			Default hours per day:
			<input class="form-control" type="number" name="default_hours" value="<?php echo $default_hours; ?>" required>
		</div>
	</div><br>
	<div class="row">
		<div class="col-md-6">
			Default billing rate:
			<input class="form-control" type="number" name="default_rate" value="<?php echo $default_rate; ?>" required>
		</div>
	</div><br>

	<h4>Client details</h4>

	<div class="row">
		<div class="col-md-6">
			Client name:
			<input class="form-control" maxlength='50' type="text" name="client" value="<?php echo $client; ?>" required>
		</div>
	</div><br>
	<div class="row">
		<div class="col-md-6">
			Address (optional):
			<input class="form-control" maxlength='150' type="text" name="address" value="<?php echo $address; ?>">
		</div>
	</div><br>

	<h4>Timeframe</h4>

	<div class="row">
		<div class="col-md-6">
			Start date:
			<input class="form-control" type="text" id="date" name="date" value="<?php echo $date; ?>" required>
			<script>$('#date').datepicker({format:'yyyy-mm-dd'});</script>
		</div>
	</div><br>
	<div class="row">
		<div class="col-md-6">
			End date (optional):
			<input class="form-control" type="text" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
			<script>$('#end_date').datepicker({format:'yyyy-mm-dd'});</script>
		</div>
	</div><br>
	<div class="row">
		<div class="col-md-6">
			Ending reason (optional):
			<input class="form-control" maxlength='490' type="text" name="reason" value="<?php echo $reason; ?>">
		</div>
	</div><br>

	<h4>Contact details</h4>

	<div class="row">
		<div class="col-md-6">
			Contact name (optional):
			<input class="form-control" maxlength='50' type="text" name="contact_name" value="<?php echo $contact_name; ?>">
		</div>
	</div><br>
	<div class="row">
		<div class="col-md-6">
			Contact email (optional):
			<input class="form-control" maxlength='50' type="text" name="contact_email" value="<?php echo $contact_email; ?>">
		</div>
	</div><br>
	<div class="row">
		<div class="col-md-6">
			Contact phone (optional):
			<input class="form-control" maxlength='50' type="text" name="contact_phone" value="<?php echo $contact_phone; ?>">
		</div>
	</div><br>

	<div class="row">
		<div class="col-md-2">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input class="form-control btn btn-primary" type="submit" value="Save">
		</div>
	</div><br>
</form>

<br>

<?php include '../bottom.php'; ?>

