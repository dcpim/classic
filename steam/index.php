<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-steam-square"></i></a> Played games</h3>

<?php
tabletop("steam", "<tr><th style='max-width:165px'>Logo</th><th>Game information</th><th>Played</th><th>Rating</th></tr>");
$results = $db->query("SELECT * FROM steam WHERE played_time != 0 AND hidden != 1 ORDER BY game_name ASC;");
$total_played = 0;
while($result = $results->fetch_assoc())
{
	echo "<tr><td><img src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/SteamHeaders/" . $result['appid'] . ".jpg' title=\"" . $result['name'] . "\" style='width:160px;height:75px'></td><td>";
	if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-name=\"" . $result['game_name'] . "\" data-review=\"" . $result['review'] . "\" data-date='" . $result['date'] . "' data-appid='" . $result['appid'] . "' data-stars='" . $result['stars'] . "'><i class='fa fa-pencil-square-o'></i></a></span>"; }
	$filter = explode(' ', $result['game_name'])[0];
	if(count(explode(' ', $result['game_name'])) > 1) { $filter = explode(' ', $result['game_name'])[0] . " " . explode(' ', $result['game_name'])[1]; }
	$filter = preg_replace("/[^a-zA-Z0-9 ]+/", "", $filter);
	$filter = trim(str_replace("The", "", $filter));
	if($result['appid'] > 0) { echo "<b><a target=_new style='font-size:20px!important' href='https://store.steampowered.com/app/" . $result['appid'] . "/'>"; }
	else { echo "<b style='font-size:20px!important'>"; }
	if($result['game_name'] == "") { echo $result['appid']; }
	else { echo $result['game_name']; }
    echo "</a></b><br>Type: <i>" . $result['genre'] . "</i><br>Release: <i>" . $result['release_date'];
	echo "</td><td data-sort='" . $result['played_time'] . "' style='vertical-align:middle'>";
	if($result['played_time'] < 0) { echo "-"; }
	elseif($result['played_time'] < 60) { echo number_format($result['played_time'],0) . "m"; }
	elseif($result['played_time'] < 24*60) { echo number_format($result['played_time']/60,0) . "h"; }
	else { echo number_format($result['played_time']/24/60,0) . "d"; }
	echo "</td><td data-sort='" . $result['stars'] . "' style='vertical-align:middle'><center><nobr>";
	for ($x = 1; $x <= $result['stars']; $x++) { echo "<i class='fa fa-star fa-2x'></i>"; }
	echo "</center></nobr></td></tr>";
	$total_played += $result['played_time'];
}
tablebottom("steam", "3", "desc");
?>

<p>Total play time: <b><?php echo number_format($total_played/60,0); ?></b> hours.</p>

<br>

<div id="hobbies"></div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart2);

      function drawChart2() {

        var data2 = google.visualization.arrayToDataTable([
          ['Genre', 'Games'],
<?php
$results = $db->query("SELECT genre,COUNT(*) FROM steam WHERE genre IS NOT NULL AND hidden != 1 GROUP BY genre;");
while($result = $results->fetch_assoc())
{
	echo "['" . $result['genre'] . "', " . $result['COUNT(*)'] . "],";
}
?>
        ]);

        var options2 = { height: 500, title: 'Games by genre', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: {color: '#C0C0C0', bold: true}, legend: {textStyle: {color: '#C0C0C0'}}, chartArea: {backgroundColor: '#182025'}<?php } ?> };
        var chart2 = new google.visualization.PieChart(document.getElementById('hobbies'));

        chart2.draw(data2, options2);
      }
</script>

<?php
if($login_admin == 1) { modal("update.py", "delete.py",array(
	array("type" => "text", "var" => "name", "label" => "Game name:", "options" => "maxlength='50' required"),
	array("type" => "textarea", "var" => "review", "label" => "Review:", "options" => "maxlength='2000'"),
	array("type" => "select", "var" => "stars", "label" => "Rating:", "options" => "required", "choices" => array(
		"1", "2", "3", "4", "5"
	)),
	array("type" => "text", "var" => "appid", "label" => "Application ID:", "options" => "readonly"),
	array("type" => "text", "var" => "date", "label" => "Date:", "options" => "readonly")
)); }

if($login_admin == 1) { pipelines("Gaming pipelines", array(

	array("title" => "Add a non-Steam game", "icon" => "gamepad", "action" => "create.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='50' placeholder='Game name' required"),
		array("type" => "text", "size" => "4", "options" => "name='release' maxlength='10' placeholder='Release date' required"),
		array("type" => "select", "size" => "4", "options" => "name='genre' required", "choices" => array(
			"Action", "Adventure", "Casual", "Indie", "Massively Multiplayer", "RPG", "Simulation", "Strategy"
		)),
		array("type" => "row"),
		array("type" => "file", "size" => "4", "options" => "name='filename' required"),
		array("type" => "select", "size" => "2", "options" => "name='stars' required", "choices" => array(
			"1", "2", "3", "4", "5"
		)),
		array("type" => "empty", "size" => "3"),
		array("type" => "submit", "size" => "3", "options" => "value='Add game'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"games" => "List of played games"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>
