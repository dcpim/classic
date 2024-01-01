<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-medkit"></i></a> Health data</h3>

<div class='thumbnail'><table width='99%' style='margin:5px;line-height: 1.5;'><tr>
    <td width='20%' style='padding-top:10px;padding-bottom:10px;padding-left:15px'><i class="fa fa-heartbeat fa-4x"></i></td>
<?php
$results = $db->query("SELECT * FROM health_info;");
while($record = $results->fetch_assoc())
{
	echo "<td width='40%'>" . $record['line1'] . "</td>";
	echo "<td width='40%'>" . $record['line2'] . "</td>";
}
?>
</tr></table></div>


<!-------------- GAUGES -------------->

<hr><h3>Charts</h3>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<table width='100%' height='200px' style='margin-left: 10px; margin-right: auto; text-align:center; justify-content: center;'><tr width='100%'>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='15%' id="chart_g4"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='15%' id="chart_g6"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='15%' id="chart_g5"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='15%' id="chart_g1"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='15%' id="chart_g2"></td>
<td <?php if($darkmode) { ?>style='background-color: #182025 !important;'<?php } ?> width='15%' id="chart_g3"></td>
</tr></table><br>

<script type="text/javascript">
google.charts.load('current', {'packages':['gauge']});
google.charts.setOnLoadCallback(drawChartG1);
google.charts.setOnLoadCallback(drawChartG2);
google.charts.setOnLoadCallback(drawChartG3);
google.charts.setOnLoadCallback(drawChartG4);
google.charts.setOnLoadCallback(drawChartG5);
google.charts.setOnLoadCallback(drawChartG6);

<?php
$results = $db->query("SELECT SUM(foods.calories)/30 AS calories, SUM(foods.sugar)/30 AS sugar, SUM(foods.fiber)/30 AS fiber FROM foods INNER JOIN nutrition ON nutrition.food = foods.id WHERE nutrition.date BETWEEN NOW() - INTERVAL 31 DAY AND NOW();");
while($record = $results->fetch_assoc())
{
	$total_calories = intval($record['calories']);
	$total_fiber = intval($record['fiber']);
	$total_sugar = intval($record['sugar']);
}
?>

function drawChartG1() {
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Calories', <?php echo $total_calories; ?>],
	]);
	var options = { redFrom: 3000, redTo: 4000, yellowFrom: 2500, yellowTo: 3000, greenFrom: 0, greenTo: 2500, max: 4000 };
	var chart = new google.visualization.Gauge(document.getElementById('chart_g1'));
	chart.draw(data, options);
}

function drawChartG2() {
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Sugar', <?php echo $total_sugar; ?>],
	]);
	var options = { redFrom: 50, redTo: 75, yellowFrom: 40, yellowTo: 50, greenFrom: 0, greenTo: 40, max: 75 };
	var chart = new google.visualization.Gauge(document.getElementById('chart_g2'));
	chart.draw(data, options);
}

function drawChartG3() {
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Fiber', <?php echo $total_fiber; ?>],
	]);
	var options = { redFrom: 0, redTo: 15, yellowFrom: 15, yellowTo: 20, greenFrom: 20, greenTo: 75, max: 75 };
	var chart = new google.visualization.Gauge(document.getElementById('chart_g3'));
	chart.draw(data, options);
}

<?php
$avgdistance = 0;
$results = $db->query("SELECT distance FROM health ORDER BY date DESC LIMIT 30;");
while($record = $results->fetch_assoc())
{
	$avgdistance += $record['distance'];
}
$avgdistance = round($avgdistance / 30, 1);
?>

function drawChartG4() {
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Distance', <?php echo $avgdistance; ?>],
	]);
	var options = { redFrom: 0, redTo: 1, yellowFrom: 1, yellowTo: 5, greenFrom: 5, greenTo: 30, max: 30 };
	var chart = new google.visualization.Gauge(document.getElementById('chart_g4'));
	chart.draw(data, options);
}

<?php
$results = $db->query("SELECT systolic FROM health WHERE systolic > 0 AND diastolic > 0 ORDER BY date DESC LIMIT 3;");
$blood = 0;
while($record = $results->fetch_assoc())
{
	$blood += $record['systolic'];
}
$blood = intval($blood/3);
?>

function drawChartG5() {
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Blood', <?php echo $blood; ?>],
	]);
	var options = { redFrom: 140, redTo: 200, yellowFrom: 120, yellowTo: 140, greenFrom: 0, greenTo: 120, max: 200 };
	var chart = new google.visualization.Gauge(document.getElementById('chart_g5'));
	chart.draw(data, options);
}

<?php
$weight = 0;
$results = $db->query("SELECT weight FROM health WHERE weight > 0 ORDER BY date DESC LIMIT 10;");
while($record = $results->fetch_assoc())
{
	$weight += intval($record['weight']);
}
$weight = intval($weight/10);
?>

function drawChartG6() {
	var data = google.visualization.arrayToDataTable([
		['Label', 'Value'],
		['Weight', <?php echo $weight; ?>],
	]);
	var options = { redFrom: 200, redTo: 250, yellowFrom: 165, yellowTo: 200, greenFrom: 0, greenTo: 165, max: 250 };
	var chart = new google.visualization.Gauge(document.getElementById('chart_g6'));
	chart.draw(data, options);
}
</script>


<!-------------- CHARTS -------------->

<div id="chart_div11"></div>

<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes11);

function drawCurveTypes11()
{
    var data11 = google.visualization.arrayToDataTable([
        ['Date', 'Distance (km)'],
<?php
$results = $db->query("SELECT YEAR(date) AS year, MONTH(date) AS month, ROUND(AVG(distance),1) AS distance FROM health WHERE distance != 0 GROUP BY MONTH(date), YEAR(date) ORDER BY YEAR(date), MONTH(date);");
while($record = $results->fetch_assoc())
{
	echo "[new Date(" . $record['year'] . ", " . ($record['month']-1) . ", 01), " . $record['distance'] . "],\n";
}
?>
    ]);

	var options11 = { title: 'Distance walked', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 40, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart11 = new google.visualization.LineChart(document.getElementById('chart_div11'));
    chart11.draw(data11, options11);
}
</script>

<div id="chart_div21"></div>

<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes21);

function drawCurveTypes21()
{
    var data21 = google.visualization.arrayToDataTable([
        ['Date', 'Weight (lbs)'],
<?php
$results = $db->query("SELECT YEAR(date) AS year, MONTH(date) AS month, ROUND(AVG(weight),0) AS weight FROM health WHERE weight != 0 GROUP BY MONTH(date), YEAR(date) ORDER BY YEAR(date), MONTH(date);");
while($record = $results->fetch_assoc())
{
	echo "[new Date(" . $record['year'] . ", " . ($record['month']-1) . ", 01), " . $record['weight'] . "],\n";
}
?>
    ]);

	var options21 = { title: 'Weight', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 40, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart21 = new google.visualization.LineChart(document.getElementById('chart_div21'));
    chart21.draw(data21, options21);
}
</script>

<div id="chart_div3"></div>

<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes3);

function drawCurveTypes3()
{
    var data3 = google.visualization.arrayToDataTable([
        ['Date', 'Diastolic (mmHg)', 'Systolic (mmHg)'],
<?php
$results = $db->query("SELECT YEAR(date) AS year, MONTH(date) AS month, ROUND(AVG(diastolic),0) AS diastolic, ROUND(AVG(systolic),0) AS systolic FROM health WHERE systolic != 0 AND diastolic != 0 GROUP BY MONTH(date), YEAR(date) ORDER BY YEAR(date), MONTH(date);");
while($record = $results->fetch_assoc())
{
	echo "[new Date(" . $record['year'] . ", " . ($record['month']-1) . ", 01), " . $record['diastolic'] . ", " . $record['systolic'] . "],\n";
}
?>
    ]);
    var options3 = { title: 'Blood pressure', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: {
color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 40, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart3 = new google.visualization.LineChart(document.getElementById('chart_div3'));
    chart3.draw(data3, options3);
}
</script>

<div id="chart_div4"></div>

<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes4);

function drawCurveTypes4()
{
    var data4 = google.visualization.arrayToDataTable([
        ['Date', 'Calories (kcal)'],
<?php
$results = $db->query("SELECT YEAR(nutrition.date) AS year, MONTH(nutrition.date) AS month, DAY(nutrition.date) AS day, SUM(foods.calories) AS calories FROM nutrition INNER JOIN foods ON nutrition.food = foods.id GROUP BY DAY(nutrition.date), MONTH(nutrition.date), YEAR(nutrition.date) ORDER BY YEAR(nutrition.date), MONTH(nutrition.date), DAY(nutrition.date);");
$month = -1;
$year = -1;
$calories = 0;
$count = 0;
while($record = $results->fetch_assoc())
{
	if($month == -1) { $month = $record['month']; }
	if($year == -1) { $year = $record['year']; }
	if($month != $record['month'])
	{
		echo "[new Date(" . $year . ", " . ($month-1) . ", 01), " . intval($calories/$count) . "],\n";
		$month = $record['month'];
		$year = $record['year'];
		$calories = 0;
		$count = 0;
	}
	$calories += $record['calories'];
	$count += 1;
}
?>
    ]);

    var options4 = { title: 'Calories consumed', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 40, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart4 = new google.visualization.LineChart(document.getElementById('chart_div4'));
    chart4.draw(data4, options4);
}
</script>

<div id="chart_div5"></div>

<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes5);

function drawCurveTypes5()
{
    var data5 = google.visualization.arrayToDataTable([
        ['Date', 'Fiber (g)', 'Sugar (g)'],
<?php
$results = $db->query("SELECT YEAR(nutrition.date) AS year, MONTH(nutrition.date) AS month, DAY(nutrition.date) AS day, SUM(foods.sugar) AS sugar, SUM(foods.fiber) AS fiber FROM nutrition INNER JOIN foods ON nutrition.food = foods.id GROUP BY DAY(nutrition.date), MONTH(nutrition.date), YEAR(nutrition.date) ORDER BY YEAR(nutrition.date), MONTH(nutrition.date), DAY(nutrition.date);");
$month = -1;
$year = -1;
$sugar = 0;
$fiber = 0;
$count = 0;
while($record = $results->fetch_assoc())
{
	if($month == -1) { $month = $record['month']; }
	if($year == -1) { $year = $record['year']; }
	if($month != $record['month'])
	{
		echo "[new Date(" . $year . ", " . ($month-1) . ", 01), " . intval($fiber/$count) . ", " . intval($sugar/$count) . "],\n";
		$month = $record['month'];
		$year = $record['year'];
		$sugar = 0;
		$fiber = 0;
		$count = 0;
	}
	$sugar += $record['sugar'];
	$fiber += $record['fiber'];
	$count += 1;
}
?>
    ]);

    var options5 = { title: 'Nutrients consumed', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 40, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart5 = new google.visualization.LineChart(document.getElementById('chart_div5'));
    chart5.draw(data5, options5);
}
</script>


<!-------------- TABLES -------------->

<hr><h3>Foods list</h3>

<?php
tabletop("foods", "<tr><th>Date</th><th>Type</th><th>Food name</th><th>Calories</th><th>Sugar</th><th>Fiber</th></tr>");
$results = $db->query("SELECT id,name FROM foods;");
$foods = array();
while($record = $results->fetch_assoc())
{
	$foods[$record['id']] = $record['name'];
}
$results = $db->query("SELECT nutrition.id AS id,nutrition.date AS date,foods.name AS name,foods.calories AS calories,foods.sugar AS sugar,foods.fiber AS fiber FROM nutrition INNER JOIN foods ON nutrition.food = foods.id ORDER BY nutrition.date DESC LIMIT 1000;");
while($record = $results->fetch_assoc())
{
	echo "<tr><td>" . $record['date'] . "</td><td>" . explode(" - ", $record['name'])[1] . "</td><td>" . explode(" - ", $record['name'])[2] . "</td><td>" . $record['calories'] . "</td><td>" . $record['sugar'] . "</td><td>";
    if($login_admin == 1) { echo "<span style='float:right'><a title='Delete entry' href='javascript:del_food(" . $record['id'] . ")'><i class='fa fa-times'></i></a></span>"; }
	echo $record['fiber'] . "</td></tr>\n";
}
tablebottom("foods", "0" ,"desc");
?>

<?php if($login_admin == 1) { ?>

<form method="POST" action="food.py" enctype="multipart/form-data">
    <div class="row">
        <div class="col-sm-8">
            <select data-live-search="true" class="selectpicker form-control" name="food">
<?php
$results = $db->query("SELECT id,name FROM foods WHERE name LIKE '" . intval(date('Y')-1) . "%' ORDER BY name;");
while($record = $results->fetch_assoc())
{
    echo "<option value='" . $record['id'] . "'>" . explode(" - ", $record['name'])[1] . " - " . explode(" - ", $record['name'])[2] . "</option>";
}
echo "<option value='-1'>--------------</option>";
$results = $db->query("SELECT id,name FROM foods WHERE name NOT LIKE '" . intval(date('Y')-1) . "%' ORDER BY name;");
while($record = $results->fetch_assoc())
{
    echo "<option value='" . $record['id'] . "'>" . explode(" - ", $record['name'])[1] . " - " . explode(" - ", $record['name'])[2] . "</option>";
}
?>
			</select>
        </div>
        <div class="col-sm-2">
          <input class="form-control" name="date" type="text" value="<?php echo date("Y-m-d", time() - 25200); ?>" maxlength='50' required></p>
		</div>
        <div class="col-sm-2">
            <input class="form-control btn btn-primary" type="submit" value="Add food"></p>
        </div>
    </div>
</form>

<?php } ?>

<hr><h3>Medical documents</h3>

<?php
tabletop("files", "<tr><th>Name</th><th>Size</th><th>Date</th></tr>");
$results = $db->query("SELECT * FROM medical_files ORDER BY name ASC;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td><i class='fa fa-" . fileicon($result['url']) . "'></i> &nbsp; <a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_FILES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'>" . $result['name'] . "</a></td><td>" . size_format($result['size']) . "</td><td>";
    if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-date='" . explode(' ',$result['date'])[0] . "' data-id='" . $result['id'] . "' data-name=\"" . $result['name'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo explode(' ',$result['date'])[0] . "</td></tr>";
}
tablebottom("files", "2", "desc");
?>

<script>
function del_food(id)
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if(this.readyState == 4)
        {
            if(this.responseText.includes("DONE!"))
            {
                location.reload();
            }
            else
            {
                alert(this.responseText);
            }
        }
    };
    xhttp.open("POST", "delete_food.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + encodeURIComponent(id));
}
</script>


<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50' required"),
	array("type" => "text", "var" => "date", "date" => "Date:", "options" => "maxlength='20' required")
)); }

if($login_admin == 1) { pipelines("Health pipelines", array(

	array("title" => "Upload medical document", "icon" => "upload", "action" => "upload.py", "inputs" => array(
		array("type" => "text", "size" => "5", "options" => "name='name' maxlength='50' placeholder='Document name' required"),
		array("type" => "file", "size" => "4", "options" => "name='filename' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Upload'")
	)),

	array("title" => "Upload Apple HealthKit", "icon" => "medkit", "action" => "healthkit.py", "inputs" => array(
		array("type" => "file", "size" => "9", "options" => "name='filename' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Upload'")
	)),

	array("title" => "Add food item", "icon" => "cutlery", "action" => "add_food.py", "inputs" => array(
		array("type" => "text", "size" => "7", "options" => "name='name' maxlength='50' placeholder='Food components' required"),
		array("type" => "text", "size" => "2", "options" => "name='year' maxlength='5' value='" . date("Y") . "' required"),
		array("type" => "select", "size" => "3", "options" => "name='type' required", "choices" => array(
			"Breakfast", "Lunch", "Supper", "Snack"
		)),
		array("type" => "row"),
		array("type" => "number", "size" => "3", "options" => "name='calories' placeholder='Calories' required"),
		array("type" => "number", "size" => "3", "options" => "name='sugar' placeholder='Sugar' required"),
		array("type" => "number", "size" => "3", "options" => "name='fiber' placeholder='Fiber' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Add food'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"medical" => "Medical documents"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

