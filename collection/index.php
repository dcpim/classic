<?php include '../top.php'; ?>

<table style='background-color:#182025!important;width:100%'><tr><td style='padding-right:10px;width:50%;background-color:#182025!important;vertical-align:top'>

<style>
.paginate_button, .ellipsis { background-color:#182025!important; }
</style>

<h3 style='background-color:#182025!important;'><a href="/" title="Back home"><i class="fa fa-grav"></i></a> Figures and goods</h3><br>

<?php
tabletop("collection", "<tr><th style='max-width:100px;max-height:150px;'>Thumbnail</th><th>Information</th><th>Rating</th></tr>");
$results = $db->query("SELECT * FROM collection WHERE sold = 0 AND type != 'Book';");
while($result = $results->fetch_assoc())
{
	echo "<tr><td style='width:100px;'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img style='max-width:100px;max-height:150px;' src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'></a></td><td>";
    if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-stars='" . $result['stars'] . "' data-sold='" . $result['sold'] . "' data-procurement='" . $result['procurement'] . "' data-subtype=\"" . $result['subtype'] . "\" data-type=\"" . $result['type'] . "\" data-brand=\"" . $result['brand'] . "\" data-name=\"" . $result['name'] . "\" data-price='" . $result['price'] . "'  data-currency='" . $result['currency'] . "' data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "<b style='font-size:22px!important'>" . $result['name'] . "</b><br>Brand: <i>" . $result['brand'] . "</i> (<i>" . $result['type'] . "</i>)<br>";
	if($result['date'] != "") { echo "Purchased date: <i>" . $result['date'] . "</i><br>"; }
	if($result['price'] > 0)
	{
		if($result['currency'] == "CDN") { echo "Cost: <i>$" . $result['price'] . "</i>"; }
		elseif($result['currency'] == "USD") { echo "Cost: <i>$" . $result['price'] . " " . $result['currency'] ."</i>"; }
		else { echo "Cost: <i>¥" . $result['price'] . "</i>"; }
	}
	echo "</td><td data-sort='" . $result['stars'] . "' style='vertical-align:middle'><nobr>";
	if($result['stars'] == 0) { echo "<b>Unrated</b>"; }
	else { for ($x = 1; $x <= $result['stars']; $x++) { echo "<i class='fa fa-star'></i>"; } }
	echo "</nobr></td></tr>";
}
tablebottom("collection", "1", "asc");
?>

</td><td style='padding-left:10px;width:50%;background-color:#182025!important;vertical-align:top'>

<h3 style='background-color:#182025!important;'><a href="/" title="Back home"><i class="fa fa-book"></i></a> Books library</h3><br>

<?php
tabletop("books", "<tr><th style='max-width:100px;max-height:150px;'>Thumbnail</th><th>Information</th><th>Rating</th></tr>");
$results = $db->query("SELECT * FROM collection WHERE sold = 0 AND type = 'Book';");
while($result = $results->fetch_assoc())
{
	echo "<tr><td style='width:100px;'><a target=_new href='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['url'] . "'><img style='max-width:100px;max-height:150px;' src='https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/" . $result['thumb'] . "'></a></td><td>";
    if($login_admin == 1) { echo "<span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-subtype=\"" . $result['subtype'] . "\" data-stars='" . $result['stars'] . "' data-sold='" . $result['sold'] . "' data-procurement='" . $result['procurement'] . "' data-type=\"" . $result['type'] . "\" data-brand=\"" . $result['brand'] . "\" data-name=\"" . $result['name'] . "\" data-price='" . $result['price'] . "'  data-currency='" . $result['currency'] . "' data-date=\"" . $result['date'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "<b style='font-size:22px!important'>" . $result['name'] . "</b><br>Brand: <i>" . $result['brand'] . "</i> (<i>" . $result['subtype'] . "</i>)<br>";
	if($result['date'] != "") { echo "Purchased date: <i>" . $result['date'] . "</i><br>"; }
	if($result['price'] > 0)
	{
		if($result['currency'] == "CDN") { echo "Cost: <i>$" . $result['price'] . "</i>"; }
		elseif($result['currency'] == "USD") { echo "Cost: <i>$" . $result['price'] . " " . $result['currency'] ."</i>"; }
		else { echo "Cost: <i>¥" . $result['price'] . "</i>"; }
	}
	echo "</td><td data-sort='" . $result['stars'] . "' style='vertical-align:middle'><nobr>";
	if($result['stars'] == 0) { echo "<b>Unrated</b>"; }
	else { for ($x = 1; $x <= $result['stars']; $x++) { echo "<i class='fa fa-star'></i>"; } }
	echo "</nobr></td></tr>";
}
tablebottom("books", "1", "asc");
?>

</td></tr></table>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="chart_div"></div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load('current', {packages: ['corechart']});
google.charts.setOnLoadCallback(drawCurveTypes);

function drawCurveTypes()
{
    var data = google.visualization.arrayToDataTable([
        ['Year', 'Figures', 'Books', 'Misc'],
<?php
$results = $db->query("SELECT date,type FROM collection ORDER BY date;");
$year = "";
$figures = 0;
$books = 0;
$misc = 0;
while($record = $results->fetch_assoc())
{
	$curyear = substr($record['date'], 0, 4);
	if($year != $curyear)
	{
		if($year != "")
		{
		    echo "      ['" . $year . "', " . $figures . ", " . $books . ", " . $misc . "],\n";
		}
		$year = $curyear;
		$figures = 0;
		$books = 0;
		$misc = 0;
	}
	if($record['type'] == "Figure") { $figures += 1; }
	if($record['type'] == "Book") { $books += 1; }
	else { $misc += 1; }
}
echo "      ['" . $year . "', " . $figures . ", " . $books . ", " . $misc . "],\n";
?>
    ]);

    var options = { isStacked: true, title: 'Collectibles acquired per year', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 30, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>

<?php if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "name", "label" => "Name:", "options" => "maxlength='50'"),
	array("type" => "text", "var" => "brand", "label" => "Brand:", "options" => "maxlength='30' required"),
	array("type" => "select", "var" => "type", "label" => "Type:", "options" => "required", "choices" => array(
		"Figure", "Book", "Misc"
	)),
	array("type" => "select", "var" => "subtype", "label" => "Genre:", "options" => "required", "choices" => array(
		"Anime and comics", "Fiction and myths", "History and politics", "Science and technology", "Video gaming", "Movies and shows", "World travel"
	)),
	array("type" => "number", "var" => "price", "label" => "Price:", "options" => ""),
	array("type" => "select", "var" => "currency", "label" => "Currency:", "options" => "", "choices" => array(
		"", "CDN", "JPY", "USD"
	)),
	array("type" => "select", "var" => "stars", "label" => "Rating:", "options" => "required", "choices" => array(
		"0", "1", "2", "3", "4", "5"
	)),
	array("type" => "date", "var" => "date", "label" => "Date:", "options" => "maxlength='20' required"),
	array("type" => "checkbox", "var" => "sold", "label" => "Sold item", "options" => ""),
	array("type" => "hidden", "var" => "procurement")
)); }

if($login_admin == 1) { pipelines("Collectible pipelines", array(

	array("title" => "Add new collectible", "icon" => "truck", "action" => "create.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "name='name' maxlength='50' placeholder='Name' required"),
		array("type" => "text", "size" => "4", "options" => "name='brand' maxlength='50' placeholder='Brand' required"),
		array("type" => "file", "size" => "3", "options" => "name='filename' required"),
		array("type" => "row"),
		array("type" => "select", "size" => "4", "options" => "name='type' required", "choices" => array(
			"Figure", "Book", "Misc"
		)),
		array("type" => "select", "size" => "4", "options" => "name='subtype' required", "choices" => array(
			"Anime and comics", "Fiction and myths", "History and politics", "Science and technology", "Video gaming", "Movies and shows", "World travel"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Add collectible'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

