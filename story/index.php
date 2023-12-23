<?php $public_page = 1; include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-pencil-square"></i></a> AI Story</h3>

<style>
svg g text{
//font-size:.9em;
}
.bs-callout {
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 10px;
    padding-bottom: 10px;
    margin: 20px 0;
    border: 1px solid #eee;
    border-left-width: 5px;
    border-radius: 3px;
}
.bs-callout h4 {
    margin-top: 0;
    margin-bottom: 5px;
}
.bs-callout p:last-child {
    margin-bottom: 0;
}
.bs-callout code {
    border-radius: 3px;
}
.bs-callout+.bs-callout {
    margin-top: -5px;
}
.bs-callout-default {
    border-left-color: #777;
}
.bs-callout-default h4 {
    color: #777;
}
.bs-callout-primary {
    border-left-color: #428bca;
}
.bs-callout-primary h4 {
    color: #428bca;
}
.bs-callout-success {
    border-left-color: #5cb85c;
}
.bs-callout-success h4 {
    color: #5cb85c;
}
.bs-callout-danger {
    border-left-color: #d9534f;
}
.bs-callout-danger h4 {
    color: #d9534f;
}
.bs-callout-warning {
    border-left-color: #f0ad4e;
}
.bs-callout-warning h4 {
    color: #f0ad4e;
}
.bs-callout-info {
    border-left-color: #5bc0de;
}
.bs-callout-info h4 {
    color: #5bc0de;
}
</style>

<script>
function chatgpt()
{
	var prompt = document.getElementById('prompt').value;
	var e = document.getElementById('world');
	var world = e.options[e.selectedIndex].text;
    var xhttp = new XMLHttpRequest();
	if(prompt == "") { return; }
	$(':button').prop('disabled', true)
    xhttp.onreadystatechange = function()
    {
        if(this.readyState == 4)
        {
			$(':button').prop('disabled', false)
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
	xhttp.open("POST", "story.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("prompt=" + encodeURIComponent(prompt) + "&world=" + encodeURIComponent(world));
}
function restart()
{
    var xhttp = new XMLHttpRequest();
	$(':button').prop('disabled', true)
    xhttp.onreadystatechange = function()
    {
        if(this.readyState == 4)
        {
		$(':button').prop('disabled', false)
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
	xhttp.open("POST", "restart.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}
</script>

<div class="row">
	<div class="col-md-12">
		<select id="world" class="form-control">
<?php
$results = $db->query("SELECT * FROM ai_story ORDER BY id DESC;");
while($result = $results->fetch_assoc())
{
	if($result['world'] != "")
	{
		echo "<option>" . $result['world'] . "</option>";
		break;
	}
}
?>
			<option>The story happens in a fantasy world with elves, monsters and magic.</option>
			<option>This is the real world and the story must be realistic.</option>
			<option>The story happens in the Star Trek universe.</option>
			<option>The story happens in the Star Wars universe.</option>
			<option>The world is weird and wild, and the story events must be as strange as possible.</option>
			<option>The story must be accurate to real world history.</option>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-md-8">
		<input class="form-control" type="text" id="prompt" placeholder="Enter a sentence to continue the story..." maxlength="150" required>
	</div>
	<div class="col-md-2">
		<input class="form-control btn btn-primary" type="button" value="Add to story" onClick="javascript:chatgpt();">
	</div>
	<div class="col-md-2">
		<input class="form-control btn btn-danger" type="button" value="Restart story" onClick="javascript:restart();">
	</div>
</div>

<?php
$results = $db->query("SELECT * FROM ai_story ORDER BY id DESC;");
while($result = $results->fetch_assoc())
{
	if($result['username'] == "System")
	{
		echo "<div class='bs-callout bs-callout-danger'>";
	}
	else if($result['username'] == "ChatGPT")
	{
		echo "<div class='bs-callout bs-callout-info'>";
	}
	else if($result['username'] == "Titan")
	{
		echo "<div class='bs-callout bs-callout-warning'>";
	}
	else
	{
		echo "<div class='bs-callout bs-callout-success'>";
	}
	echo "<p><i><span style='float:right'><font size=-1>" . $result['world'] . " | " . $result['date'] . "</font></span>" . $result['username'] . "</i></p><h4>" . $result['sentence'] . "</h4>";
	echo "</div>";
	if(!$_GET['all'] && $result['username'] == "System" && $result['sentence'] == "<hr>")
	{
		break;
	}
}

if(!$_GET['all']) { echo "<p><center><h3><a href='./?all=1'>Show full history</a></h3></center></p>"; }
else { echo "<p><center><h3><a href='./'>Hide full history</a></h3></center></p>"; }

?>

<br>

<?php include '../bottom.php'; ?>

