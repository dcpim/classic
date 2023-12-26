<?php include '../top.php'; ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<h3><a href="/" title="Back home"><i class="fa fa-server"></i></a> Device metrics</h3>

<h4>Disk usage in percentage</h4>
<table width='100%' height='200px' style='margin-left: 10px; margin-right: auto; text-align:center; justify-content: center;'><tr width='100%'>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='25%' id="chart_g1"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='25%' id="chart_g2"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='25%' id="chart_g3"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='25%' id="chart_g4"></td>
</tr></table><br>

<script type="text/javascript">
google.charts.load('current', {packages: ['gauge', 'corechart', 'line']});
<?php
$results = $db->query("SELECT * FROM device_stats ORDER BY device;");
$cn = 1;
while($record = $results->fetch_assoc())
{
?>
google.charts.setOnLoadCallback(drawChartG<?php echo $cn; ?>);
function drawChartG<?php echo $cn; ?>() {
    var data = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        ['<?php echo $record['device'] . " " . $record['disk'] . ""; ?>', <?php echo $record['diskusage']; ?>],
    ]);
    var options = { redFrom: 90, redTo: 100, yellowFrom: 80, yellowTo: 90, greenFrom: 0, greenTo: 80, max: 100 };
    var chart = new google.visualization.Gauge(document.getElementById('chart_g<?php echo $cn; ?>'));
    chart.draw(data, options);
}
<?php
    $cn = $cn + 1;
}
?>
</script>

<h4>Home internet speed</h4>
<div id="chart_div"></div>

<script>
google.charts.setOnLoadCallback(drawCurveTypes);

function drawCurveTypes()
{
    var data = google.visualization.arrayToDataTable([
        ['Date', 'Upload (Mbps)', 'Download (Mbps)'],
<?php
$results = $db->query("SELECT * FROM speedtest ORDER BY date DESC LIMIT 170;");
while($record = $results->fetch_assoc())
{
    $d = explode(' ', $record['date'])[0];
    $t = explode(' ', $record['date'])[1];
    echo "      [new Date(" . explode('-', $d)[0] . ", " . intval(explode('-', $d)[1]-1) . ", " . explode('-', $d)[2] . ", " . explode(':', $t)[0] . ", " . explode(':', $t)[1] . ", " . explode(':', $t)[2] . "), " . $record['upload'] . ", " . $record['download'] . "],\n";
}
?>
    ]);

    var options = { title: '', <?php if($darkmode) { ?>
backgroundColor: '#182025', titleTextStyle: {color: '#C0C0C0', bold: true}, legend: {textStyle: {color: '#C0C0C0'}, position: 'top', alignment: 'end'}, chartArea: {backgroundColor: '#182025', width: '100%', left: 30, right: 30}, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>

<br><h4>Public web and S3 accesses</h4>
<div id="chart_div2"></div>

<script>
	google.charts.load('current', {packages: ['corechart', 'line']});
	google.charts.setOnLoadCallback(drawCurveTypes2);

	function drawCurveTypes2()
	{
		var data2 = google.visualization.arrayToDataTable([
			['Date', 'S3 (hits)', 'Web (hits)'],
<?php
			$web = array();
			$results = $db->query("SELECT substring_index(date,' ',1),COUNT(*) FROM wwwlogs WHERE date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() GROUP BY substring_index(date,' ',1) ORDER BY date;");
			while($record = $results->fetch_assoc())
			{
				$web[$record["substring_index(date,' ',1)"]] = $record['COUNT(*)'];
			}
			$results = $db->query("SELECT substring_index(date,' ',1),COUNT(*) FROM s3_logs WHERE date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() GROUP BY substring_index(date,' ',1) ORDER BY date;");
			while($record = $results->fetch_assoc())
			{
				if(array_key_exists($record["substring_index(date,' ',1)"], $web)) {$a = $web[$record["substring_index(date,' ',1)"]];}
				else { $a = 0; }
				echo "        [new Date(" . explode('-', $record["substring_index(date,' ',1)"])[0] . ", " . intval(explode('-', $record["substring_index(date,' ',1)"])[1]-1) . ", " . explode('-', $record["substring_index(date,' ',1)"])[2] . "), " . $record['COUNT(*)'] . ", " . $a . "],\n";
			}
?>
		]);
		var options2 = { title: '', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 60, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
		var chart2 = new google.visualization.LineChart(document.getElementById('chart_div2'));
		chart2.draw(data2, options2);
	}
</script>

<br><h4>S3 storage</h4>
<div id="chart_div3"></div>

<script>
	google.charts.load('current', {packages: ['corechart', 'line']});
	google.charts.setOnLoadCallback(drawCurveTypes3);

	function drawCurveTypes3()
	{
		var data3 = google.visualization.arrayToDataTable([
			['Date', 'Size (GB)'],
<?php
$results = $db->query("SELECT * from s3_storage WHERE date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ORDER BY date DESC;");
$curdate = "";
$total = 0;
while($record = $results->fetch_assoc())
{
	if($curdate != "" and $curdate != $record['date'])
	{
		if($curdate != "")
		{
			echo "        [new Date(" . explode('-', $curdate)[0] . ", " . intval(explode('-', $curdate)[1]-1) . ", " . explode('-', $curdate)[2] . "), " . round($total/1000/1000/1024,2) . "],\n";
		}
		$total = 0;
	}
	$curdate = $record['date'];
	$total += $record['StorageBytes'];
}
echo "        [new Date(" . explode('-', $curdate)[0] . ", " . intval(explode('-', $curdate)[1]-1) . ", " . explode('-', $curdate)[2] . "), " . round($total/1000/1000/1024,2) . "],\n";
?>
		]);
		var options3 = { title: '', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 60, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
		var chart3 = new google.visualization.LineChart(document.getElementById('chart_div3'));
		chart3.draw(data3, options3);
	}
</script>

<br><h4>Database storage</h4>
<div id="chart_div4"></div>

<script>
	google.charts.load('current', {packages: ['corechart', 'line']});
	google.charts.setOnLoadCallback(drawCurveTypes4);

	function drawCurveTypes4()
	{
		var data4 = google.visualization.arrayToDataTable([
			['Date', 'Size (GB)'],
<?php
$results = $db->query("SELECT * from db_storage WHERE date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ORDER BY date DESC;");
while($record = $results->fetch_assoc())
{
	echo "        [new Date(" . explode('-', $record['date'])[0] . ", " . intval(explode('-', $record['date'])[1]-1) . ", " . explode('-', $record['date'])[2] . "), " . round($record['tsize']/1000/1024,2) . "],\n";
}
?>
		]);
		var options4 = { title: '', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 60, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
		var chart4 = new google.visualization.LineChart(document.getElementById('chart_div4'));
		chart4.draw(data4, options4);
	}
</script>

<br><h4>Backups and system updates</h4>
<table class='table table-striped table-hover display responsive' id='backups'>
    <thead><tr><th>Device name</th><th>Last backup</th><th>Last update</th><th>Uptime</th></tr></thead>
    <tbody>
<?php
$results = $db->query("SELECT * FROM device_stats GROUP BY device ORDER BY device;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>" . $result['device'] . "</td><td>" . $result['date'] . "</td><td>" . $result['updatedate'] . "</td><td>" . $result['uptime'] . "</td></tr>\n";
}
?>
	</tbody>
</table>
<script>$(document).ready(function(){$('#backups').DataTable({'oSearch':{'sSearch':search},'aLengthMenu':[10, 25, 50, 100, 500],'order':[[0,'asc']]});});</script>

<br><h4>LAN devices</h4>

<?php
tabletop("devices", "<tr><th>MAC Address</th><th>IP Address</th><th>First seen</th><th>Last seen</th><th>Notes</th><th>Hostname</th></tr>");
$results = $db->query("SELECT * FROM wlan_scan;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>" . $result['mac'] . "</td><td>" . $result['ip'] . "</td><td>" . $result['first_seen'] . "</td><td>" . $result['last_seen'] . "</td><td>" . $result['notes'] . "</td><td>" . $result['dns'];
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['mac'] . "' data-dns=\"" . $result['dns'] . "\" data-notes=\"" . $result['notes'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td></tr>\n";
}
tablebottom("devices", "3", "desc");
?>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "id", "label" => "MAC Address:", "options" => "readonly"),
	array("type" => "text", "var" => "notes", "label" => "Notes:", "options" => "maxlength='95'"),
	array("type" => "text", "var" => "dns", "label" => "Hostname:", "options" => "maxlength='40'"),
)); }
?>

<br>

<?php include '../bottom.php'; ?>

