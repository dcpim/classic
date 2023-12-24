<?php
$db = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_DATABASE']) or die("Could not connect to database!");
$db->set_charset("utf8");
$results = $db->query("SELECT * FROM videos WHERE type = 'Music video (Japanese)' OR  type = 'Music video (English)' OR  type = 'Music video (French)' ORDER BY RAND();");
echo "echo 1 > /tmp/fifo\n";
while($result = $results->fetch_assoc())
{
	echo "omxplayer " . $result['url'] . " < /tmp/fifo\n";
}
$db->close();
?>
