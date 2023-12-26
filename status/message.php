<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'/>
 		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet"> 
        <style>
            *
            {
                background-color: #000000;
				font-family: 'Arial', monospace;
                width: 1675px;
                font-size: 28px;
				padding: 0px;
				margin: 0px;
            }
        </style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	</head>
	<body>
		<center>
<?php
$db = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_DATABASE']) or die("Could not connect to database!");
$db->set_charset("utf8");
$results = $db->query("SELECT internalizing FROM nutrition_stats ORDER BY internalizing DESC LIMIT 1;");
$max_days = 0;
while($result = $results->fetch_assoc())
{
	$max_days = $result['internalizing'];
}
$results = $db->query("SELECT nutrition.date,COUNT(*) c FROM nutrition INNER JOIN foods ON nutrition.food = foods.id WHERE foods.name LIKE '%- Snack -%' GROUP BY date HAVING c > 1 ORDER BY nutrition.date DESC LIMIT 1;");
$days = 0;
while($result = $results->fetch_assoc())
{
	$date1 = new DateTime($result['date']);
	$date2 = new DateTime(date("Y-m-d"));
	$days = $date2->diff($date1)->format("%a");
	echo file_get_contents("stocks.txt");
	echo " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <font color='white'><i>Being disciplined for <font color='";
	if($days >= $max_days) { echo "green'><b>" . number_format($days) . "</b></font> days.</i></font>"; }
	else { echo "red'><b>" . number_format($days) . "</b></font> day.</i></font>"; }
}
if($days > $max_days)
{
	$stmt = $db->prepare("INSERT IGNORE INTO nutrition_stats (internalizing) VALUES (?);");
	$stmt->bind_param('i', $days);
	$stmt->execute();
}
$db->close();
?>
		</center>
	</body>
</html>
