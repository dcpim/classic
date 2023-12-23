<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-paperclip"></i></a> Projects list</h3>

<?php
tabletop("projects", "<tr><th>Name</th><th>Client</th><th>Timeframe</th></tr>");
$results = $db->query("SELECT * FROM projects ORDER BY end_date,last_update DESC;");
while($result = $results->fetch_assoc())
{
	$badge = 0;
	$results2 = $db->query("SELECT COUNT(*) FROM project_files WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$results2 = $db->query("SELECT COUNT(*) FROM bills WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$results2 = $db->query("SELECT COUNT(*) FROM secrets WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$results2 = $db->query("SELECT COUNT(*) FROM tasks WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$results2 = $db->query("SELECT COUNT(*) FROM code WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$results2 = $db->query("SELECT COUNT(*) FROM journal WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$results2 = $db->query("SELECT COUNT(*) FROM inventory WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$results2 = $db->query("SELECT COUNT(*) FROM bookmarks WHERE prjid = " . $result['id'] . ";");
	while($result2 = $results2->fetch_assoc()) { $badge += $result2['COUNT(*)']; }
	$end_date = $result['end_date'];
	if($result['end_date'] == "") { $end_date = "(none)"; }
	echo "<tr><td><a href='./?id=" . $result['id'] . "'>" . $result['name'] . "</a> <span class='badge' style='float:right'>" . $badge . "</span></td><td>" . $result['client'] . "</td><td>" . $result['date'] . " &nbsp - &nbsp; " . $end_date . "</td></tr>";
}
tablebottom("projects", "", "");
?>

<link rel="stylesheet" href="/jquery-ui.css">
<script src="/jquery-ui.js"></script>

<script>
    var client_presets = [
<?php
$results = $db->query("SELECT * FROM presets WHERE type = 'client' ORDER BY name ASC;");
while($result = $results->fetch_assoc()) { echo "\"" . $result['name'] . "\","; }
?>
    ];
</script>
<br>

<div id="chart_div9"></div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
google.charts.load('current', {packages: ['gauge', 'corechart', 'line']});

google.charts.setOnLoadCallback(drawCurveTypes);

function drawCurveTypes()
{
    var data = google.visualization.arrayToDataTable([
        ['Date', 'Updates'],
<?php
$results = $db->query("SELECT * FROM project_updates WHERE date > '" . date('Y-m-d', strtotime("-6 month", time())) . "' ORDER BY date;");
while($record = $results->fetch_assoc())
{
    echo "      [new Date(" . explode('-', $record['date'])[0] . ", " . intval(explode('-', $record['date'])[1]-1) . ", " . explode('-', $record['date'])[2] . "), " . $record['num'] . "],\n";
}
?>
    ]);

    var options = { title: 'Project updates per day', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 30, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart = new google.visualization.LineChart(document.getElementById('chart_div9'));
    chart.draw(data, options);
}
</script>

<?php
if($login_admin == 1) { pipelines("Project pipelines", array(

	array("title" => "Create a new project", "icon" => "file-o", "action" => "./create.py", "inputs" => array(
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='50' placeholder='Project name' required"),
		array("type" => "text", "size" => "4", "options" => "name='client' maxlength='50' placeholder='Client name' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Create project'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"projects" => "List of projects"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

