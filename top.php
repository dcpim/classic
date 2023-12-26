<?php
include 'functions.php';
$darkmode = 1;
if($_COOKIE["dcpim_net_darkmode"] == "0")
{
	$darkmode = 0;
}
if($_SERVER['REQUEST_URI'] == "/profile/darkmode.php")
{
	if($_COOKIE["dcpim_net_darkmode"] == "1")
	{
		setcookie("dcpim_net_darkmode", "0", time()+315360000, "/");
		$darkmode = 0;
	}
	else
	{
		setcookie("dcpim_net_darkmode", "1", time()+315360000, "/");
		$darkmode = 1;
	}
}
$page_time_start = floor(microtime(true) * 1000);
$login_status = 0; // Not logged in yet
$login_admin = 0; // Access level, none by default
$db = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_DATABASE']) or die("Could not connect to database!");
$db->set_charset("utf8");
$results = $db->query("SELECT * FROM config;");
$CONFIG = array();
while($result = $results->fetch_assoc()) // Populate config array
{
	$CONFIG[$result['k']] = $result['v'];
}
if($_POST['username'] and $_POST['password'])
{
	$login_status = 2; // Username and password are there but not valid yet
	$results = $db->query("SELECT * FROM log WHERE event = 'login';");
	$failed_attempts = 0;
	while($result = $results->fetch_assoc()) // Compute the amount of failed logins from today for that user
	{
		$rdate = explode(" ", $result['date']);
		if($rdate[0] == date("Y-m-d") and $result['result'] == 2 and $result['ip'] == $_SERVER['REMOTE_ADDR']) { $failed_attempts = $failed_attempts + 1; }
	}
	if($failed_attempts < 4) // Only allow successful login if less than 4 failed logins
	{
		$results = $db->query("SELECT curtoken FROM token ORDER BY id DESC LIMIT 1;");
		while($result = $results->fetch_assoc()) { $token = $result['curtoken']; }
		$results = $db->query("SELECT * FROM users;");
		while($result = $results->fetch_assoc())
		{
			if($result['username'] == $_POST['username'] and $result['password'] == sha1($_POST['password']))
			{
				if(($result['sms'] == "" and $result['totp'] == "") or $_POST['token'] == $token or $_POST['totp'] == $token)
				{
					$q = "INSERT INTO sessions (username, valid_until, ip, admin, browser) VALUES ('" . $result['username'] . "', " . (time()+604800) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " . $result['admin'] . ", md5('" . $_SERVER['HTTP_USER_AGENT'] . "'));";
					$db->query($q);
					setcookie("dcpim_net_session", $result['username'], time()+31536000, "/");
					setcookie("dcpim_net_token", sha1($result['password']), time()+31536000, "/"); // Double hashed!
					$login_status = 4; // Username and password are valid, logged in
					$login_admin = $result['admin'];
				}
			}
		}
		if($login_status == 2) // Credentials were provided but aren't valid, not too many failed attempts yet
		{
			notify("Login failed for user [" . $_POST['username'] . "] from [" . $_SERVER['REMOTE_ADDR'] . "]");
		}
	}
	$q = "INSERT INTO log (username, ip, event, result, date) VALUES ('" . $_POST['username'] . "', '"  . $_SERVER['REMOTE_ADDR'] . "', 'login', '" . $login_status . "', '" . date('Y-m-d h:i:s', time()) . "');";
	$db->query($q);
	$q = "DELETE FROM sessions WHERE valid_until < UNIX_TIMESTAMP();";
	$db->query($q);
	exec("curl https://" . $CONFIG['SERVER_HOST'] . "/pipelines/bucket_policies.py > /dev/null 2>&1 &");
}
else if($_SERVER['REQUEST_URI'] != "/a/" and $_COOKIE["dcpim_net_session"] != "")
{
	$login_status = 1; // Cookies are there but not valid yet
	$token_status = 0; // Password check
	$q = "SELECT * FROM users;";
	$results = $db->query($q);
	while($result = $results->fetch_assoc())
	{
		if($result['username'] == $_COOKIE["dcpim_net_session"] and sha1($result['password']) == $_COOKIE["dcpim_net_token"])
		{
			$token_status = 1; // Username and hash in cookies are valid
		}
	}
	$q = "SELECT * FROM sessions WHERE valid_until > " . time() . " ORDER BY id DESC;";
	$results = $db->query($q);
	while($result = $results->fetch_assoc())
	{
		if(($result['ip'] == $_SERVER['REMOTE_ADDR'] or $result['browser'] == md5($_SERVER['HTTP_USER_AGENT'])) and $result['username'] == $_COOKIE["dcpim_net_session"] and $token_status == 1)
		{
			$login_status = 3; // Cookies are there and valid, logged in
			$login_admin = $result['admin'];
			if($result['ip'] != $_SERVER['REMOTE_ADDR'] or $result['browser'] != md5($_SERVER['HTTP_USER_AGENT'])) // IP or browser is different, add entry in sessions
			{
				$q = "INSERT INTO sessions (username, valid_until, ip, admin, browser) VALUES ('" . $result['username'] . "', " . (time()+604800) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " . $result['admin'] . ", md5('" . $_SERVER['HTTP_USER_AGENT'] . "'));";
				$db->query($q);
				$q = "DELETE FROM sessions WHERE valid_until < UNIX_TIMESTAMP();";
				$db->query($q);
				exec("curl https://" . $CONFIG['SERVER_HOST'] . "/pipelines/bucket_policies.py > /dev/null 2>&1 &");
			}
			break;
		}
	}
}
if($public_page != 1 and $_SERVER['REQUEST_URI'] != "/a/" and $login_status == 0) // Not logged in, page isn't public, silent quit
{
	header("Status: 418 I'm a teapot.");
	exit(0);
}
?>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="robots" content="noindex">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>DCPIM</title>
		<link href="/bootstrap.min.css" rel="stylesheet" />
        <script src="/jquery-min.js"></script>
        <script src="/bootstrap.min.js"></script>
		<link href="/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="/responsive.bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/bootstrap-datepicker.min.css">
<?php if($darkmode) { ?>		<link rel="stylesheet" type="text/css" href="/darkmode.css"> <?php } ?>
		<script src="/jquery.dataTables.min.js"></script>
        <script src="/dataTables.responsive.min.js"></script>
        <script src="/responsive.bootstrap.min.js"></script>
        <script src="/bootstrap-datepicker.min.js"></script>
		<link rel="shortcut icon" href="/favicon.jpg">
		<style>.img-thumbnail { margin-bottom: 2px; margin-top: 2px; } .form-control { margin-bottom: 1px; margin-top: 1px; }</style>
	</head>
	<body>
		<div class="container">
<?php if($darkmode) { ?>
			<p><center><a href='/'><img style='max-width:100%' src='/images/toptitle.png' alt='DCPIM'></a></center></p>
<?php } else { ?>
			<p><center><a href='/'><img style='max-width:100%' src='/images/toptitle.jpg' alt='DCPIM'></a></center></p>
<?php } ?>
<?php
if($failed_attempts > 3) // Too many failed attempts
{
	echo "<div class='alert alert-danger' role='alert'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> You are locked out due to too many failed attempts.</div>";
	exit(0);
}
elseif($login_status == 1) // Cookie is there but is not valid
{
	echo "<div class='alert alert-danger' role='alert'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Your session has expired, please login again.</div>";
	echo "<meta http-equiv='refresh' content='1; URL=/a' />";
	exit(0);
}
elseif($login_status == 2) // Username and password are there but not valid
{
	echo "<div class='alert alert-danger' role='alert'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Username or password is incorrect.</div>";
	echo "<meta http-equiv='refresh' content='1; URL=/a' />";
	exit(0);
}
elseif($login_status == 4) // Username and password are valid, logged in
{
	echo "<div class='alert alert-success' role='alert'><i class='fa fa-info' aria-hidden='true'></i> Logged in successfully.</div>";
}
if($_SERVER['REQUEST_URI'] == "/" and $login_status == 3) // Session is valid, logged in
{
	// Last login box
	echo "<div class='alert alert-info' role='alert'>You are currently logged in as <b>" . $_COOKIE["dcpim_net_session"] . "</b> from <b>" . $_SERVER['REMOTE_ADDR'] . "</b>.";
	$results = $db->query("select * from log WHERE event = 'login' AND username = \"" . $_COOKIE["dcpim_net_session"] . "\" ORDER BY id DESC LIMIT 1;");
	$lastip = "";
	while($result = $results->fetch_assoc())
	{
		echo " Your last login attempt was on <b>" . $result['date'] . "</b> from <b>" . $result['ip'] . "</b>.";
		$lastip = $result['ip'];
	}
	if($login_admin == 1) // Add search box to last login box
	{
		echo "<div class='row' style='margin-top:5px;margin-bottom:5px;'><div class='col-sm-9'><input style='margin-bottom:5px!important' class='form-control' type='text' id='search_query' placeholder='Search, ChatGPT query or new task' value='' maxlength='100' onfocus='this.value=\"\";'></div><div class='col-sm-3'><button type='button' id='search_button' class='btn btn-primary' onclick='run_search()'>Search</button>&nbsp;&nbsp;<button type='button' id='ai_button' class='btn btn-primary' onclick='ai()'>ChatGPT</button>&nbsp;&nbsp;<button type='button' id='search_button' class='btn btn-primary' onclick='quick_task()'><i class='fa fa-plus-square' style='margin-top:3px;margin-bottom:3px'></i></button></div></div><div id='search_results'></div>";
	}
	echo "</div>";
	if($login_admin == 1) // Admin related items
	{
		$results = $db->query("SELECT * FROM tasks;"); // Display tasks list
		while($result = $results->fetch_assoc())
		{
			if($result['date'] == date('Y-m-d'))
			{
				$project = "Unknown";
				$client = "";
				$results2 = $db->query("SELECT * FROM projects WHERE id = " . $result['prjid'] . ";");
		        while($result2 = $results2->fetch_assoc()) { $project = $result2['name']; $client = $result2['client']; }
				echo "<div class='alert alert-warning' role='alert'><span style='float:right'><a title='Mark as done' href='javascript:check_task(" . $result['id'] . ", " . $result['prjid'] . ")'><i class='fa fa-check-square fa-2x' style='margin-top:-5'></i></a></span><b><i class='fa fa-exclamation-circle' aria-hidden='true'></i> Task due today:</b> <a href='/projects?id=" . $result['prjid'] . "'>" . $project . "</a> (" . $client . ") - " . $result['task'] . "</div>";
			}
			else if(strtotime($result['date']) < strtotime("today"))
			{
				$project = "Unknown";
				$client = "";
				$results2 = $db->query("SELECT * FROM projects WHERE id = " . $result['prjid'] . ";");
		        while($result2 = $results2->fetch_assoc()) { $project = $result2['name']; $client = $result2['client']; }
				echo "<div class='alert alert-danger' role='alert'><span style='float:right'><a title='Mark as done' href='javascript:check_task(" . $result['id'] . ", " . $result['prjid'] . ")'><i class='fa fa-check-square fa-2x' style='margin-top:-5'></i></a></span><b><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Task is past due:</b> <a href='/projects?id=" . $result['prjid'] . "'>" . $project . "</a> (" . $client . ") - " . $result['task'] . "</div>";
			}
		}
		$results = $db->query("SELECT * FROM rss_lastread ORDER BY id DESC LIMIT 1;"); // Display feeds entries
		while($result = $results->fetch_assoc())
		{
    		$lastread = $result['item'];
		}
		$results = $db->query("SELECT COUNT(*) FROM rss WHERE id > " . $lastread . ";");
		while($result = $results->fetch_assoc())
		{
			if($result['COUNT(*)'] > 0)
			{
				echo "<div class='alert alert-success' role='alert'><b><i class='fa fa-info' aria-hidden='true'></i>  New feed entries available:</b> <a href='/feeds'>" . $result['COUNT(*)'] . " entries.</a></div>";
			}
		}
	}
}
?>

<script>
function check_task(id, prjid)
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
            else if(this.responseText.includes("class='run_msg'><b>ERROR:</b>"))
			{
				alert(this.responseText.substring(this.responseText.indexOf("class='run_msg'><b>ERROR:</b>") + 29, this.responseText.lastIndexOf("<span")));
			}
			else
            {
                alert(this.responseText);
            }
        }
    };
    xhttp.open("POST", "/projects/check_task.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + encodeURIComponent(id) + "&prjid=" + encodeURIComponent(prjid));
}

function run_search()
{
    document.getElementById('search_results').innerHTML = "Loading...";
    var query = document.getElementById('search_query').value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if(this.readyState == 4 && this.status == 200)
        {
            document.getElementById('search_results').innerHTML = this.response.split("|||")[1];
        }
    };
    xhttp.open("POST", "/pipelines/search.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("q=" + encodeURIComponent(query));
}

if(document.getElementById('search_query'))
{
	document.getElementById('search_query').addEventListener("keyup", function(event)
	{
		if(event.keyCode === 13) { document.getElementById('search_button').click(); }
	});
}

function ai()
{
    document.getElementById('search_results').innerHTML = "Loading...";
    var query = document.getElementById('search_query').value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if(this.readyState == 4 && this.status == 200)
        {
            document.getElementById('search_results').innerHTML = this.response;
        }
    };
    xhttp.open("POST", "/pipelines/ai.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("prompt=" + encodeURIComponent(query) + "&model=chatgpt");
}

function quick_task()
{
    var query = document.getElementById('search_query').value;
	window.location.href = "/projects/create_task.py?prjid=1&title=" + encodeURIComponent(query);
}

var search = "";
$(document).ready(function()
{
	search = decodeURI(window.location.href.substr(window.location.href.indexOf('search=')).split('&')[0].split('=')[1])
	if(search == "undefined") { search = ""; }
})
</script>
