<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-money"></i></a> Personal transactions</h3>

<?php
tabletop("acc", "<tr><th data-priority='1'>Date</th><th data-priority='2'>Description</th><th data-priority='2'>Debit</th><th data-priority='2'>Credit</th><th data-priority='1'>Balance</th></tr>");
$mortgage_rate = 997.52; // Previous mortgage rate
$mortgage_rate2022 = 904.15; // Current mortgage rate
$tfsa_limit = 46800; // Current TFSA contribution limit, need to adjust each year
$rrsp_limit = 104900; // Current RRSP contribution limit, need to adjust each year
$balance = 6440.48; // Balance at the start of the data set
$debits = 0; // Yearly spending
$credits = 0; // Yearly income
$equity = 0; // Yearly equity
$rrsp = 0; // Yearly RRSP
$rrspfix = 0; // Fix RRSP value for Jan-Mar entries
$tfsa = 0; // Yearly TFSA
$crypto = 0; // Yearly Crypto
$stocks = 0; // Yearly others
$year = 0; // Current year
$curspending = 0; // Monthly spending
$curincome = 0; // Monthly income
$cursavings = 0; // Monhtly savings & equity
$curmonth = ""; // Current month
$chart = array(); // Monthly cashflow chart values
$chartsavings = array(); // Savings rate chart values
echo "<tr><td>2018-01-01</td><td>Starting balance</td><td>-$0.00</td><td>$0.00</td><td>$" . number_format($balance,2) . "</td></tr>";
$results = $db->query("SELECT * FROM income ORDER BY date ASC;");
while($result = $results->fetch_assoc())
{
	$entrydate = date('Y-m-d', strtotime($result['date']));
	if($year != explode('-',$result['date'])[0])
	{
		$chartsavings[$year] = array($stocks, $equity, $rrsp, $tfsa, $crypto, $credits);
		$chartsavings[$year-1][2] += $rrspfix;
        $year = explode('-',$result['date'])[0];
		$limitdate = date('Y-m-d', strtotime($year."-03-01"));
        $stocks = 0;
		$crypto = 0;
		$rrsp = 0;
		$rrspfix = 0;
		$tfsa = 0;
		$credits = 0;
		$debits = 0;
		$equity = 0;
    }
	if($curmonth != explode('-',$result['date'])[0] . "-" . explode('-',$result['date'])[1])
	{
		if($cursavings < 0) { $cursavings = 0; }
		if($curmonth != "") { array_push($chart, array($curmonth, $curspending, $curincome, $cursavings)); }
		$curmonth = explode('-',$result['date'])[0] . "-" . explode('-',$result['date'])[1];
		$curspending = 0;
		$curincome = 0;
		$cursavings = 0;
	}
	if($result['is_saving'] != 1)
	{
		$debits = $debits + $result['debit'];
		$credits = $credits + $result['credit'];
		$curspending = $curspending + $result['debit'];
		$curincome = $curincome + $result['credit'];
	}
	else
	{
		$cursavings = $cursavings + $result['debit'] - $result['credit'];
	}
	if(stripos($result['note'], 'mortgage') !== false)
	{
		if(str_contains($result['date'], "2022") and intval($result['debit']) > $mortgage_rate2022)
		{
			$equity = $equity + $result['debit'] - $mortgage_rate2022;
			$curspending = $curspending - $result['debit'] + $mortgage_rate2022;
			$cursavings = $cursavings + $result['debit'] - $mortgage_rate2022;
		}
		elseif(str_contains($result['date'], "2023") and intval($result['debit']) > $mortgage_rate2022)
		{
			$equity = $equity + $result['debit'] - $mortgage_rate2022;
			$curspending = $curspending - $result['debit'] + $mortgage_rate2022;
			$cursavings = $cursavings + $result['debit'] - $mortgage_rate2022;
		}
		elseif(str_contains($result['date'], "2024") and intval($result['debit']) > $mortgage_rate2022)
		{
			$equity = $equity + $result['debit'] - $mortgage_rate2022;
			$curspending = $curspending - $result['debit'] + $mortgage_rate2022;
			$cursavings = $cursavings + $result['debit'] - $mortgage_rate2022;
		}
		elseif(str_contains($result['date'], "2025") and intval($result['debit']) > $mortgage_rate2022)
		{
			$equity = $equity + $result['debit'] - $mortgage_rate2022;
			$curspending = $curspending - $result['debit'] + $mortgage_rate2022;
			$cursavings = $cursavings + $result['debit'] - $mortgage_rate2022;
		}
		elseif(intval($result['debit']) > $mortgage_rate)
		{
			$equity = $equity + $result['debit'] - $mortgage_rate;
			$curspending = $curspending - $result['debit'] + $mortgage_rate;
			$cursavings = $cursavings + $result['debit'] - $mortgage_rate;
		}
	}
	$balance = $balance + $result['credit'] - $result['debit'];
	echo "<tr><td>" . $result['date'] . "</td><td>";
	if($result['is_saving'] == 1)
	{
		if(stripos($result['note'], 'TFSA') !== false)
		{
			if($year > 2020) { $tfsa_limit = $tfsa_limit - $result['debit'] + $result['credit']; }
			$tfsa = $tfsa - $result['credit'] + $result['debit'];
		}
		else if(stripos($result['note'], 'RRSP') !== false)
		{
			if($year > 2020) { $rrsp_limit = $rrsp_limit - $result['debit'] + $result['credit']; }
			if($entrydate < $limitdate) { $rrspfix = $rrspfix - $result['credit'] + $result['debit']; }
			else { $rrsp = $rrsp - $result['credit'] + $result['debit']; }
		}
		else if(stripos($result['note'], 'Crypto') !== false)
		{
			$crypto = $crypto - $result['credit'] + $result['debit'];
		}
		else
		{
			$stocks = $stocks - $result['credit'] + $result['debit'];
		}
		echo "<i class='fa fa-usd'></i> ";
	}
	echo $result['note'];
	echo "</td><td>-$" . number_format($result['debit'],2) . "</td><td>$" . number_format($result['credit'],2) . "</td><td>";
    if($login_admin == 1) { echo " <span style='float:right'><a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-note=\"" . $result['note'] . "\" data-date=\"" . $result['date'] . "\" data-debit='" . $result['debit'] . "' data-credit='" . $result['credit'] . "'><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "$" . number_format($balance,2) . "</td></tr>";
}
$chartsavings[$year-1][2] += $rrspfix;
$chartsavings[$year] = array($stocks, $equity, $rrsp, $tfsa, $crypto, $credits);
echo "</tbody><tfoot><tr><th>Totals for " . $year . "</th><th></th><th>-$" . number_format($debits,2) . "</th><th>$" . number_format($credits,2) . "</th><th>$" . number_format(abs($credits - $debits),2) . "</th></tr></tfoot>";
?>
</table>
<script>$(document).ready(function(){$('#acc').DataTable({'oSearch':{'sSearch':search},'aLengthMenu':[10, 25, 50, 100, 500],'order':[[0,'asc']]});});</script>

<p>Remaining contribution limit: RRSP: <b>$<?php echo number_format($rrsp_limit,2); ?></b>, TFSA: <b>$<?php echo number_format($tfsa_limit,2); ?></b></p>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<br><div id="chart_div"></div>

<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes);

function drawCurveTypes()
{
    var data = google.visualization.arrayToDataTable([
        ['Date', 'Spending ($)', 'Income ($)', 'Savings & equity ($)'],
<?php
foreach($chart as $record)
{
	$i = intval($record[2]);
	if($i > 20000) { $i = 20000; }
    echo "      [new Date(" . explode('-', $record[0])[0] . "," . intval(explode('-', $record[0])[1]-1) . ",1), " . intval($record[1]) . ", " . $i . ", " . intval($record[3]) . "],\n";
}
?>
    ]);

    var options = { title: 'Monthly cash flow', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 60, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>

<br><div id="chart_div3"></div>

<script>
google.charts.load('current', {packages: ['corechart']});
google.charts.setOnLoadCallback(drawCurveTypes3);

function drawCurveTypes3()
{
    var data3 = google.visualization.arrayToDataTable([
        ['Date', 'Stocks ($)', 'Equity ($)', 'RRSP ($)', 'TFSA ($)', 'Crypto ($)', {role:'annotation'}],
<?php
foreach($chartsavings as $year => $s)
{
	if($s[5] == 0) { $s[5] = 1; }
	if($year > 2017) { echo "[	'" . $year . "', " . max(round($s[0]),0) . ", " . max(round($s[1]),0) .  ", " . max(round($s[2]),0) . ", " . max(round($s[3]),0) . ", " . max(round($s[4]),0) . ", '" . round(($s[0]+$s[1]+$s[2]+$s[3]+$s[4])*100/$s[5]) . "%'],\n"; }
}
?>
    ]);

    var options3 = { isStacked: true, title: 'Calculated savings rate', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 60, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart3 = new google.visualization.ColumnChart(document.getElementById('chart_div3'));
    chart3.draw(data3, options3);
}
</script>

<br><div id="chart_div2"></div>

<script>
google.charts.load('current', {packages: ['corechart']});
google.charts.setOnLoadCallback(drawChart2);

function drawChart2()
{
    var data2 = google.visualization.arrayToDataTable([
        ['Year', 'Spending ($)', 'Income ($)'],
<?php
$results = $db->query("SELECT * FROM old_spending ORDER BY date ASC;");
$year = "";
$curyear = 0;
$income = 0;
$spending = 0;
while($result = $results->fetch_assoc())
{
	$curyear = explode('-', $result['date'])[0];
	if($curyear < 2018)
	{
		if($curyear != $year)
		{
			if($year != "")
			{
			    echo "      ['" . $year . "', " . $spending . ", " . $income . "],\n";
			}
			$year = $curyear;
			$income = 0;
			$spending = 0;
		}
		$income = $income + $result['credit'];
		$spending = $spending + $result['debit'];
	}
}
echo "      ['" . $year . "', " . $spending . ", " . $income . "],\n";
$results = $db->query("SELECT * FROM income ORDER BY date ASC;");
$year = "";
$income = 0;
$curyear = "";
$spending = 0;
while($result = $results->fetch_assoc())
{
	if($result['is_saving'] != 1)
	{
		$curyear = explode('-', $result['date'])[0];
		if($curyear != $year)
		{
			if($year != "")
			{
			    echo "      ['" . $year . "', " . $spending . ", " . $income . "],\n";
			}
			$year = $curyear;
			$income = 0;
			$spending = 0;
		}
		$income = $income + $result['credit'];
		$spending = $spending + $result['debit'];
	}
}
echo "      ['" . $year . "', " . $spending . ", " . $income . "],\n";
?>
    ]);

    var options2 = { title: 'Yearly totals', <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 60, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> };
    var chart2 = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
    chart2.draw(data2, options2);
}
</script>

<br><div id="piechart"></div>

<script>
google.charts.load('current', {packages: ['corechart']});
google.charts.setOnLoadCallback(drawPie);

function drawPie()
{
    var dataPie = google.visualization.arrayToDataTable([
        ['Type', 'Amount'],
<?php
$year = date('Y');
$utils = 0;
$others = 0;
$food = 0;
$travel = 0;
$collectibles = 0;
$housing = 0;
$media = 0;
$subs = 0;
$clothing = 0;
$results = $db->query("SELECT * FROM income WHERE date like '" . $year . "%' AND debit > 0 ORDER BY date ASC;");
while($result = $results->fetch_assoc())
{
    if($result['is_saving'] != 1)
    {
		if(
			stripos($result['note'], "tangerine") !== false ||
			stripos($result['note'], "credit") !== false ||
			stripos($result['note'], "domino") !== false ||
			stripos($result['note'], "restaur") !== false ||
			stripos($result['note'], "food") !== false)
		{
			$food += $result['debit'] - $result['credit'];
		}
		else if(
			stripos($result['note'], "travel") !== false ||
			stripos($result['note'], "hotel") !== false ||
			stripos($result['note'], "flight") !== false)
		{
			$travel += $result['debit'] - $result['credit'];
		}
 		else if(
			stripos($result['note'], "videotron") !== false ||
			stripos($result['note'], "mint") !== false ||
			stripos($result['note'], "hydro") !== false ||
			stripos($result['note'], "desjardins") !== false ||
			stripos($result['note'], "public") !== false)
		{
			$utils += $result['debit'] - $result['credit'];
		}
		else if(
			stripos($result['note'], "mortgage") !== false ||
			stripos($result['note'], "hoa") !== false ||
			stripos($result['note'], "city") !== false ||
			stripos($result['note'], "school") !== false ||
			stripos($result['note'], "condo") !== false ||
			stripos($result['note'], "insurance") !== false)
		{
			$housing += $result['debit'] - $result['credit'];
		}
		else if(
			stripos($result['note'], "steam") !== false ||
			stripos($result['note'], "gam") !== false ||
			stripos($result['note'], "movie") !== false ||
			stripos($result['note'], "show") !== false ||
			stripos($result['note'], "event") !== false ||
			stripos($result['note'], "concert") !== false ||
			stripos($result['note'], "convention") !== false ||
			stripos($result['note'], "in-app") !== false ||
			stripos($result['note'], "apple") !== false)
		{
			$media += $result['debit'] - $result['credit'];
		}
		else if(
			stripos($result['note'], "cloth") !== false)
		{
			$clothing += $result['debit'] - $result['credit'];
		}
		else if(
			stripos($result['note'], "collect") !== false ||
			stripos($result['note'], "figure") !== false ||
			stripos($result['note'], "japan") !== false ||
			stripos($result['note'], "book") !== false)
		{
			$collectibles += $result['debit'] - $result['credit'];
		}
		else if(
			stripos($result['note'], "youtube") !== false ||
			stripos($result['note'], "aws") !== false ||
			stripos($result['note'], "icloud") !== false ||
			stripos($result['note'], "subscri") !== false)
		{
			$subs += $result['debit'] - $result['credit'];
		}
		else
		{
			$others += $result['debit'] - $result['credit'];
		}
	}
}
echo "['Utilities', " . $utils . "],";
echo "['Food', " . $food . "],";
echo "['Housing', " . $housing . "],";
echo "['Clothing', " . $clothing . "],";
echo "['Travels', " . $travel . "],";
echo "['Collectibles', " . $collectibles . "],";
echo "['Entertainment', " . $media . "],";
echo "['Subscriptions', " . $subs . "],";
echo "['Others', " . $others . "],";
?>
        ]);

        var optionsPie = {
            height: 500,
            chartArea: {'width': '100%', 'height': '80%'},
			sliceVisibilityThreshold: 0,
            title: 'Spending categories for <?php echo $year; ?>',
 <?php if($darkmode) { ?> backgroundColor: '#182025', titleTextStyle: { color: '#C0C0C0', bold: true }, legend: { textStyle: { color: '#C0C0C0' }, position: 'top', alignment: 'end' }, chartArea: { backgroundColor: '#182025', width: '100%', left: 60, right: 30 }, hAxis:{textStyle:{color:'#707070'}}, vAxis:{textStyle:{color:'#707070'}} <?php } ?> 
        };

        var chartPie = new google.visualization.PieChart(document.getElementById('piechart'));

        chartPie.draw(dataPie, optionsPie);
      }

</script>

<?php
if($login_admin == 1) { modal("update.py", "delete.py", array(
	array("type" => "text", "var" => "note", "label" => "Description:", "options" => "maxlength='100' required"),
	array("type" => "date", "var" => "date", "label" => "Date:", "options" => "maxlength='20' required"),
	array("type" => "number", "var" => "debit", "label" => "Debit:", "options" => "step='0.01' required"),
	array("type" => "number", "var" => "credit", "label" => "Credit:", "options" => "step='0.01' required"),
)); }

if($login_admin == 1) { pipelines("Personal income pipelines", array(

	array("title" => "Add a personal bank account transaction", "icon" => "money", "action" => "create.py", "inputs" => array(
		array("type" => "text", "size" => "4", "options" => "maxlength='50' name='note' placeholder='Description' required"),
		array("type" => "date", "size" => "4", "options" => "maxlength='20' name='date' placeholder='Date' required"),
		array("type" => "checkbox", "size" => "4", "label" => " Is savings or investment", "options" => "name='is_saving'"),
		array("type" => "row"),
		array("type" => "number", "size" => "4", "options" => "step='0.01' name='debit' placeholder='Amount spent' required"),
		array("type" => "number", "size" => "4", "options" => "step='0.01' name='credit' placeholder='Amount received' required"),
		array("type" => "submit", "size" => "3", "options" => "value='Add transaction'")
	)),

	array("title" => "Export CSV data", "icon" => "share-square-o", "action" => "/pipelines/export.py", "inputs" => array(
		array("type" => "selectkv", "size" => "9", "options" => "name='data' required", "choices" => array(
			"income" => "List of personal transactions"
		)),
		array("type" => "submit", "size" => "3", "options" => "value='Export'")
	))

)); }
?>

<?php include '../bottom.php'; ?>

