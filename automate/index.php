<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-gears"></i></a> Automation control</h3>

<?php
tabletop("automate", "<tr><th data-priority='1'>Description</th><th data-priority='1'>Pipeline</th><th data-priority='2'>Schedule</th><th data-priority='2'>Last run</th><th data-priority='2'>Time</th><th data-priority='1'></th></tr>");
$results = $db->query("SELECT * FROM automate ORDER BY name ASC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td data-sort='" . $result['name'] . "'>";
	if($result['notify'] == 1) { echo "<i title='Notification enabled' class='fa fa-envelope-o'></i> "; }
	if($result['history'] == 1) { echo "<i title='History enabled' class='fa fa-calendar-plus-o'></i> "; }
	echo "<a href='./show_output.py?id=" . $result['id'] . "'>" . $result['name'] . "</a>";
	if($result['node'] != "local") { echo " (" . $result['node'] . ")"; }
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-node=\"" . $result['node'] . "\" data-name=\"" . $result['name'] . "\" data-notify=\"" .  $result['notify'] . "\" data-history=\"" .  $result['history'] . "\" data-nextrun=\"" . gmdate("Y-m-d H:i:s", $result['next_run']) . "\" data-params=\"" . $result['params'] . "\"  data-pipeline=\"" . $result['pipeline'] . "\" data-repeats=\"" . $result['repeats'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td><td>" . $result['pipeline'] . "</td><td>";
	if($result['repeats'] == 600) { echo "15 minutes"; }
	if($result['repeats'] == 3300) { echo "Every hour"; }
	if($result['repeats'] == 86100) { echo "Every day"; }
	if($result['repeats'] == 604500) { echo "Every week"; }
	if($result['repeats'] == 2591700) { echo "Every month"; }
	if($result['repeats'] == 0) { echo "Paused"; }
	echo "</td><td>" . $result['last_run'] . "</td><td data-sort='" . $result['duration'] . "'>";
	if($result['duration'] > 59) { echo number_format($result['duration']/60) . "m</td><td>"; }
	else { echo number_format($result['duration']) . "s</td><td>"; }
	if($result['result'] == 1) { echo "<i class='fa fa-check'></i>"; }
	else { echo "<i class='fa fa-times'></i>"; }
	echo "</td></tr>";
	$totalsize = $totalsize + intval($result['size']);
}
tablebottom("automate", "0", "asc");
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<h4>Statistics</h4>

<div id="chart_div"></div>

<script>
google.charts.load('current', {packages: ['gauge', 'corechart', 'line']});

google.charts.setOnLoadCallback(drawCurveTypes);

function drawCurveTypes()
{
    var data = google.visualization.arrayToDataTable([
        ['Date', 'Duration (s)'],
<?php
$results = $db->query("SELECT * FROM automate_stats ORDER BY date DESC LIMIT 675;");
while($record = $results->fetch_assoc())
{
	$d = explode(' ', $record['date'])[0];
	$t = explode(' ', $record['date'])[1];
	echo "      [new Date(" . explode('-', $d)[0] . ", " . intval(explode('-', $d)[1]-1) . ", " . explode('-', $d)[2] . ", " . explode(':', $t)[0] . ", " . explode(':', $t)[1] . ", " . explode(':', $t)[2] . "), " . $record['duration'] . "],\n";
}
?>
    ]);

    var options = { title: 'Runs duration', <?php if($darkmode) { ?>
backgroundColor: '#182025', titleTextStyle: {color: '#C0C0C0', bold: true}, legend: {textStyle: {color: '#C0C0C0'}, position: 'top', alignment: 'end'}, chartArea: {backgroundColor: '#182025', width: '100%', left: 40, right: 30}, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>

<div id="chart_div2"></div>

<script>
google.charts.setOnLoadCallback(drawCurveTypes2);

function drawCurveTypes2()
{
    var data2 = google.visualization.arrayToDataTable([
        ['Date', 'Successes', 'Failures'],
<?php
$results = $db->query("SELECT * FROM automate_stats ORDER BY date DESC LIMIT 675;");
while($record = $results->fetch_assoc())
{
	$d = explode(' ', $record['date'])[0];
	$t = explode(' ', $record['date'])[1];
	echo "      [new Date(" . explode('-', $d)[0] . ", " . intval(explode('-', $d)[1]-1) . ", " . explode('-', $d)[2] . ", " . explode(':', $t)[0] . ", " . explode(':', $t)[1] . ", " . explode(':', $t)[2] . "), " . $record['success'] . ", " . $record['failure'] . "],\n";
}
?>
    ]);

    var options2 = { title: 'Success ratio', <?php if($darkmode) { ?>backgroundColor: '#182025', titleTextStyle: {color: '#C0C0C0', bold: true}, legend: {textStyle: {color: '#C0C0C0'}, position: 'top', alignment: 'end'}, chartArea: {backgroundColor: '#182025', width: '100%', left: 40, right: 30}, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart2 = new google.visualization.LineChart(document.getElementById('chart_div2'));
    chart2.draw(data2, options2);
}
</script>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Description:", "options" => "maxlength='50' required"),
	array("type" => "text", "var" => "pipeline", "label" => "Pipeline script:", "options" => "maxlength='100' required"),
	array("type" => "text", "var" => "params", "label" => "Input parameters:", "options" => "maxlength='300'"),
	array("type" => "selectkv", "var" => "repeats", "label" => "Schedule:", "options" => "required", "choices" => array(
		"600" => "15 minutes", "3300" => "Every hour", "86100" => "Every day", "604500" => "Every week", "2591700" => "Every month", "0" => "Paused"
	)),
	array("type" => "selectkv", "var" => "node", "label" => "Node:", "options" => "required", "choices" => array(
		"local" => $CONFIG['SERVER_HOST'], "gura" => $CONFIG['REMOTE_NODE_1'], "adusa" => $CONFIG['REMOTE_NODE_1']
	)),
	array("type" => "text", "var" => "nextrun", "label" => "Next run:", "options" => "readonly"),
	array("type" => "checkbox", "var" => "notify", "label" => "Notify on failure", "options" => ""),
	array("type" => "checkbox", "var" => "history", "label" => "Save historical runs", "options" => ""),
), "location.reload();", "Run now", "setrun.py"); }

if($login_admin == 1) { pipelines("Automation pipelines", array(

	array("title" => "Add a new automation pipeline", "icon" => "gears", "action" => "create.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='50' placeholder='Description' required"),
		array("type" => "text", "size" => "3", "options" => "name='pipeline' maxlength='30' placeholder='Pipeline script' required"),
		array("type" => "selectkv", "size" => "3", "options" => "name='repeats' required", "choices" => array(
			"600" => "15 minutes", "3300" => "Every hour", "86100" => "Every day", "604500" => "Every week", "2591700" => "Every month"
		)),
		array("type" => "submit", "size" => "2", "options" => "value='Add pipeline'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

