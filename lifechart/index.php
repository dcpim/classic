<?php include '../top.php'; ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<h3><a href="/" title="Back home"><i class="fa fa-user-circle-o"></i></a> Lifechart data</h3>

<br><h4>Timeline</h4>

<div id="timeline"></div>

<script>
      google.charts.load('current', {'packages':['timeline', 'corechart', 'line', 'calendar']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var container = document.getElementById('timeline');
        var chart = new google.visualization.Timeline(container);
        var dataTable = new google.visualization.DataTable();

		dataTable.addColumn({ type: 'string', id: 'Term' });
        dataTable.addColumn({ type: 'string', id: 'Activity' });
        dataTable.addColumn({ type: 'date', id: 'Start' });
        dataTable.addColumn({ type: 'date', id: 'End' });
        dataTable.addRows([
<?php
$results = $db->query("SELECT * FROM timeline ORDER BY id;");
while($result = $results->fetch_assoc())
{
	echo "[ '" . $result['role'] . "', '" . $result['org'] . "', new Date(" . explode('-', $result['start_date'])[0] . "," . intval(explode('-', $result['start_date'])[1]-1) . ", " . explode('-', $result['start_date'])[2] . "), new Date(";
	if($result['end_date'] == "")
	{
		echo "";
	}
	else
	{
		echo explode('-', $result['end_date'])[0] . "," . intval(explode('-', $result['end_date'])[1]-1) . ", " . explode('-', $result['end_date'])[2];
	}
	echo ")],";
}
?>
          ]);

	    var options = {
    		  height: 500,
		      timeline: { colorByRowLabel: true },
	    };
        chart.draw(dataTable, options);
      }
</script>

<h4>Family tree</h4>

<div id="orgchart_div"></div><br>

<script>
google.charts.load('current', {packages:["orgchart"]});
google.charts.setOnLoadCallback(drawOrgChart);

function drawOrgChart()
{
    var data5 = google.visualization.arrayToDataTable([
        ['Name', 'Child']
<?php
$results = $db->query("SELECT * FROM family_tree ORDER BY id;");
while($result = $results->fetch_assoc())
{
	echo ",[{'v':'" . $result['name'] . "', 'f':'" . $result['name'] . "<div style=\"color:red; font-style:italic\">" . $result['lifespan'] . "</div>'}, '" . $result['child'] . "']";
}
?>
    ]);

    var options5 = {'allowHtml': true, <?php if($darkmode) { ?>backgroundColor: '#182025', titleTextStyle: {color: '#C0C0C0', bold: true}, legend: {textStyle: {color: '#C0C0C0'}}, chartArea: {backgroundColor: '#182025'}<?php } ?> };
    var chart5 = new google.visualization.OrgChart(document.getElementById('orgchart_div'));
    chart5.draw(data5, options5);
}
</script>

<br><br><h4>Notes and certificates</h4>

<?php
tabletop("files", "<tr><th>Name</th><th>Size</th><th>Date</th></tr>");
$results = $db->query("SELECT * FROM education_files ORDER BY name ASC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td><i class='fa fa-" . fileicon($result['url']) . "'></i> &nbsp; <a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_FILES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>" . $result['name'] . "</a></td><td>" . size_format($result['size']) . "</td><td>";
    if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . explode(' ',$result['date'])[0] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo explode(' ',$result['date'])[0] . "</td></tr>";
}
tablebottom("files", "2", "desc");
?>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50' required"),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "maxlength='20' required")
)); }

if($login_admin == 1) { pipelines("Lifechart pipelines", array(

	array("title" => "Upload a note or certificate", "icon" => "upload", "action" => "create.py", "inputs" => array(
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='50' placeholder='Document name' required"),
		array("type" => "file", "size" => "4", "options" => "name='filename' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Upload'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"education" => "List of notes and certificates"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php' ?>
