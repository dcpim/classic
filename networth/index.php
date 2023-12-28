<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-usd"></i></a> Personal networth</h3>

<?php
$results = $db->query("SELECT * FROM networth ORDER BY date DESC LIMIT 13;");
$mdiff = 0;
$ydiff = 0;
$lastdate = "";
$curnetworth = 0;
while($result = $results->fetch_assoc())
{
	$assets = $result['banks'] + $result['investments'] + $result['assets'] + $result['equity'] + $result['biz'];
	$liabilities = $result['loans'] + $result['cc'] + $result['mortgage'];
	$networth = $assets - $liabilities;

	if($curnetworth == 0)
	{
		echo "<br><div class='row'><div class='col-md-6'><table class='table table-striped table-hover display responsive'><tbody>";
		echo "<tr><td colspan=2><center><b>Assets</b></center></td></tr>";
		echo "<tr><td>Bank accounts</td><td align=right>$" . number_format($result['banks']) . "</td></tr>";
		echo "<tr><td>Investments</td><td align=right>$" . number_format($result['investments']) . "</td></tr>";
		echo "<tr><td>Physical assets</td><td align=right>$" . number_format($result['assets']) . "</td></tr>";
		echo "<tr><td>Home equity</td><td align=right>$" . number_format($result['equity']) . "</td></tr>";
		echo "<tr><td>Business equity</td><td align=right>$" . number_format($result['biz']) . "</td></tr>";
		echo "<tr><td><b>Total assets</b></td><td align=right><b>$" . number_format($assets) . "</b></td></tr>";
		echo "</tbody></table></div>";

		echo "<div class='col-md-6'><table class='table table-striped table-striped display responsive'><tbody>";
		echo "<tr><td colspan=2><center><b>Liabilities</b></center></td></tr>";
		echo "<tr><td>Mortgage</td><td align=right>$" . number_format($result['mortgage']) . "</td></tr>";
		echo "<tr><td>Credit cards</td><td align=right>$" . number_format($result['cc']) . "</td></tr>";
		echo "<tr><td>Loans</td><td align=right>$" . number_format($result['loans']) . "</td></tr>";
		echo "<tr><td>&nbsp;</td><td align=right></td></tr>";
		echo "<tr><td>&nbsp;</td><td align=right></td></tr>";
		echo "<tr><td><b>Total liabilities</b></td><td align=right><b>$" . number_format($liabilities) . "</b></td></tr>";
		echo "</tbody></table></div></div>";

		echo "<div class='row'><div class='col-md-4'></div>";
		echo "<div class='col-md-4 thumbnail'><table class='table display responsive'><tbody>";
		echo "<tr><td><h3><b>Networth</b></h3></td><td align=right><h3><b>$" . number_format($networth) . "</b></h3></td></tr>";
		echo "</tbody></table></div></div>";

		echo "<h4><center><b>Last update: " . $result['date'] . " &nbsp; / &nbsp; Monthly change: <span id='month'></span> &nbsp; / &nbsp; Yearly change: <span id='year'></span></b></center></h4><br><hr>";

		$curnetworth = $networth;
	}

	if($curnetworth != 0)
	{
		$ydiff = ($curnetworth - $networth) / $networth * 100;
		if($mdiff == 0) { $mdiff = $ydiff; }

		if($mdiff > 0)
		{
			echo "<script>document.getElementById('month').innerHTML = \"<b style='color: green !important'>" . number_format($mdiff,1) . "%</b>\";</script>";
		}
		else
		{
			echo "<script>document.getElementById('month').innerHTML = \"<b style='color: red !important'>" . number_format($mdiff,1) . "%</b>\";</script>";
		}
		if($ydiff > 0)
		{
			echo "<script>document.getElementById('year').innerHTML = \"<b style='color: green !important'>" . number_format($ydiff,1) . "%</b>\";</script>";
		}
		else
		{
			echo "<script>document.getElementById('year').innerHTML = \"<b style='color: red !important'>" . number_format($ydiff,1) . "%</b>\";</script>";
		}
	}
}
$phy = 0;
$results = $db->query("SELECT price FROM inventory WHERE sold != 1 AND sold != 4 AND prjid != 33;");
while($result = $results->fetch_assoc())
{
	$phy += $result['price'];
}
$results = $db->query("SELECT price,currency FROM collection WHERE sold != 1;");
while($result = $results->fetch_assoc())
{
	if($result['price'] == 0) { $phy += 0; }
	elseif(strcasecmp($result['currency'], "JPY") == 0) { $phy += ($result['price'] * 0.011); }
	elseif(strcasecmp($result['currency'], "USD") == 0) { $phy += ($result['price'] * 1.24); }
	else { $phy += $result['price']; }
}
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<br><div id="chart_div"></div>

<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes);

function drawCurveTypes()
{
    var data = google.visualization.arrayToDataTable([
        ['Date', 'Assets ($)', 'Liabilities ($)', 'Networth ($)'],

<?php
$results = $db->query("SELECT * FROM networth ORDER BY date ASC;");
while($result = $results->fetch_assoc())
{
	$assets = $result['banks'] + $result['investments'] + $result['assets'] + $result['equity'] + $result['biz'];
	$liabilities = $result['loans'] + $result['cc'] + $result['mortgage'];
	$networth = $assets - $liabilities;
	echo "	[new Date(" . explode('-', $result['date'])[0] . "," . intval(explode('-', $result['date'])[1]-1) . ",1), " . $assets . ", " . $liabilities . ", " . $networth . "],\n";
}
?>
    ]);

    var options = { title: 'Networth over time', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 70, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>

<br><div id="chart_div2"></div>

<script>
google.charts.setOnLoadCallback(drawCurveTypes2);

function drawCurveTypes2()
{
    var data2 = google.visualization.arrayToDataTable([
        ['Year', 'Savings ($)', 'Mortgage ($)'],
<?php
$results = $db->query("SELECT banks,biz,investments,mortgage,loans,cc FROM networth ORDER BY date DESC LIMIT 12;");
$year = date('Y');
$l_sv = 0;
$l_mtg = 0;
$sv = 0; // assets (minus equity)
$mtg = 0; // liabilities
while($result = $results->fetch_assoc())
{
	if($l_sv == 0)
	{
		$sv = $result['banks'] + $result['investments'];
		$mtg = $result['loans'] + $result['cc'] + $result['mortgage'];
	}
	$l_sv = $result['banks'] + $result['investments'];
	$l_mtg = $result['loans'] + $result['cc'] + $result['mortgage'];
}
echo "	['" . ($year-1) . "', " . $l_sv . ", " . $l_mtg . "],\n";
echo "	['" . $year . "', " . $sv . ", " . $mtg . "],\n";
$mtg_diff = $l_mtg - $mtg;
$sv_diff = $sv - $l_sv;
for($x = 1; $x < 20; $x++)
{
	$sv = $sv + $sv_diff;
	$mtg = $mtg - $mtg_diff - ($mtg_diff*($x/10));
	if($mtg < 0) { $mtg = 0; $sv = $sv + ($mtg_diff*($x/10)); }
	if($sv > 1000000) { break; }
	echo "	['" . ($year+$x) . "', " . intval($sv) . ", " . intval($mtg) . "],\n";
}
?>
    ]);

    var options2 = { title: 'FIRE prediction timeline', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 70, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart2 = new google.visualization.LineChart(document.getElementById('chart_div2'));
    chart2.draw(data2, options2);
}
</script>

<?php
if($login_admin == 1) { pipelines("Networth pipelines", array(

	array("title" => "Add a networth entry", "icon" => "money", "action" => "add.py", "inputs" => array(
		array("type" => "label", "size" => "6", "label" => "Assets:"),
		array("type" => "label", "size" => "6", "label" => "Liabilities:"),
		array("type" => "row"),
		array("type" => "number", "size" => "6", "options" => "name='banks' placeholder='Bank accounts' required"),
		array("type" => "number", "size" => "6", "options" => "name='cc' placeholder='Credit cards' required"),
		array("type" => "row"),
		array("type" => "number", "size" => "6", "options" => "name='equity' placeholder='Home equity' required"),
		array("type" => "number", "size" => "6", "options" => "name='mortgage' placeholder='Mortgage' required"),
		array("type" => "row"),
		array("type" => "number", "size" => "6", "options" => "name='biz' placeholder='Business retained earnings' required"),
		array("type" => "number", "size" => "6", "options" => "name='loans' placeholder='Loans' required"),
		array("type" => "row"),
		array("type" => "number", "size" => "6", "options" => "name='investments' placeholder='Investments' required"),
		array("type" => "empty", "size" => "6"),
		array("type" => "row"),
		array("type" => "number", "size" => "6", "options" => "name='assets' placeholder='Physical assets' value='" . round($phy) . "' required"),
		array("type" => "empty", "size" => "6"),
		array("type" => "row"),
		array("type" => "date", "size" => "6", "options" => "maxlength='20' name='date' placeholder='Date' required"),
		array("type" => "submit", "size" => "6", "options" => "value='Add entry'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"networth" => "Networth history"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>
