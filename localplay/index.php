<?php
	$db = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_DATABASE']) or die("Could not connect to database!");
	$db->set_charset("utf8");
	$CONFIG = array();
	$results = $db->query("SELECT * FROM config;");
	while($result = $results->fetch_assoc()) { $CONFIG[$result['k']] = $result['v']; }
	$url = file_get_contents("play.txt");
	if(strlen($url) > 2 and substr($url, 0, 1) === '*')
	{
		$url = ltrim($url, '*');
		$results = $db->query("SELECT url FROM music WHERE artist = \"" . $url . "\" ORDER BY RAND() LIMIT 1;");
		while($result = $results->fetch_assoc()) { echo "https://" . str_replace('[bucket]', $CONFIG['BUCKET_MUSIC'], $CONFIG['STORAGE_HOST']) . "/" . $result['url']; }
	}
	else if(strlen($url) > 2 and substr($url, 0, 1) === '^')
	{
		$url = ltrim($url, '^');
		$results = $db->query("SELECT url FROM videos WHERE type = \"" . $url . "\" ORDER BY RAND() LIMIT 1;");
		while($result = $results->fetch_assoc()) { echo "https://" . str_replace('[bucket]', $CONFIG['BUCKET_VIDEOS'], $CONFIG['STORAGE_HOST']) . "/" . $result['url']; }
	}
	else
	{
		echo $url;
		file_put_contents("play.txt", "");
	}
	$db->close();
?>
