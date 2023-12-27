<?php include '../top.php'; ?>

<link href="/editor.css" type="text/css" rel="stylesheet"/>
<script src="/editor.js"></script>

<?php
$results = $db->query("SELECT * FROM projects WHERE id = " . intval($_GET['id']) . ";");
while($result = $results->fetch_assoc())
{
	$id = $result['id'];
	$name = $result['name'];
	$client = $result['client'];
	$notes = $result['notes'];
	$date = $result['date'];
	$address = $result['address'];
	$contact_name = $result['contact_name'];
	$contact_email = $result['contact_email'];
	$end_date = $result['end_date'];
	if($result['end_date'] == "") { $end_date = "(none)"; }
}

$reload_on_save = 0;

$results = $db->query("SELECT COUNT(*) FROM journal WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
	$count_journal = $result['COUNT(*)'];
}

$results = $db->query("SELECT COUNT(*) FROM tasks WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
	$count_tasks = $result['COUNT(*)'];
}

$results = $db->query("SELECT COUNT(*) FROM project_files WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
    $count_files = $result['COUNT(*)'];
}

$results = $db->query("SELECT COUNT(*) FROM secrets WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
    $count_secrets = $result['COUNT(*)'];
}

$results = $db->query("SELECT COUNT(*) FROM bills WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
    $count_bills = $result['COUNT(*)'];
}

$results = $db->query("SELECT COUNT(*) FROM inventory WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
    $count_items = $result['COUNT(*)'];
}

$results = $db->query("SELECT COUNT(*) FROM bookmarks WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
    $count_bookmarks = $result['COUNT(*)'];
}

$results = $db->query("SELECT COUNT(*) FROM code WHERE prjid = " . $id . ";");
while($result = $results->fetch_assoc())
{
    $count_codex = $result['COUNT(*)'];
}
?>

<h3><a href="list.php" title='Project list'><i class="fa fa-address-card-o"></i></a> Project - <?php echo $name; ?></h3>

<div class='thumbnail'><table width='100%' style='line-height: 1.5;'><tr>
	<td width='20%' style='padding-top:10px;padding-bottom:10px;padding-left:15px;'><i class="fa fa-address-card-o fa-4x"></i></td>
	<td width='40%'>Project ID: <b><?php echo $id; ?></b><br>Client: <b><?php echo $client; ?></b><br>Address: <b><?php echo $address; ?></b></td>
	<td width='40%'>Contact: <b><a href='mailto:<?php echo $contact_email; ?>'><?php echo $contact_name; ?></a></b><br>Timeframe: <b><?php echo $date; ?> &nbsp; - &nbsp; <?php echo $end_date; ?></b><br>Activities: <b><?php echo $count_codex + $count_bookmarks + $count_files + $count_tasks + $count_secrets + $count_journal + $count_bills + $count_items; ?></b>
	<span style='float:right'><a title='Update project' href="update.php?id=<?php echo $id; ?>"><i class="fa fa-pencil-square-o fa-2x"></i></a></span></td>
</tr></table></div>

<div class='img-thumbnail' style='display:inline-block;width:150px;margin-left:6px'>
	<center><a href="files.php?id=<?php echo $id; ?>"><i class="fa fa-files-o fa-5x"></i><br>Files</a> <span class="badge badge-light"><?php echo $count_files; ?></span></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px;margin-left:6px'>
	<center><a href="/secrets/?id=<?php echo $id; ?>"><i class="fa fa-lock fa-5x"></i><br>Secrets</a> <span class="badge badge-light"><?php echo $count_secrets; ?></span></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px;margin-left:6px'>
	<center><a href="journal.php?prjid=<?php echo $id; ?>"><i class="fa fa-pencil fa-5x"></i><br>Events</a> <span class="badge badge-light"><?php echo $count_journal; ?></span></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px;margin-left:6px'>
	<center><a href="bills.php?id=<?php echo $id; ?>"><i class="fa fa-money fa-5x"></i><br>Billables</a> <span class="badge badge-light"><?php echo $count_bills; ?></span></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px;margin-left:6px'>
	<center><a href="inventory.php?id=<?php echo $id; ?>"><i class="fa fa-cubes fa-5x"></i><br>Inventory</a> <span class="badge badge-light"><?php echo $count_items; ?></span></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px;margin-left:6px'>
	<center><a href="bookmarks.php?id=<?php echo $id; ?>"><i class="fa fa-globe fa-5x"></i><br>Bookmarks</a> <span class="badge badge-light"><?php echo $count_bookmarks; ?></span></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px;margin-left:6px'>
	<center><a href="/codex/?prjid=<?php echo $id; ?>"><i class="fa fa-code fa-5x"></i><br>Codex</a> <span class="badge badge-light"><?php echo $count_codex; ?></span></center>
</div>

<br><br><span style='float:right'><a href='javascript:create_task(<?php echo $id; ?>)'><i class="fa fa-plus fa-2x"></i></a></span>
<h4>Tasks</h4>
<table class='table table-striped table-hover display responsive' id='tasks'>
    <thead><tr><th>Description</th><th>Next due date</th><th>Repeats</th></tr></thead>
    <tbody>
<?php
$results = $db->query("SELECT * FROM tasks WHERE prjid = " . $id . ";");
$taskcount = 0;
while($result = $results->fetch_assoc())
{
	if($darkmode)
	{
	    echo "<tr>";
		if($result['date'] == date('Y-m-d')) { echo "<td style='background-color:#555500 !important'>"; }
		else if(strtotime($result['date']) < strtotime("today")) { echo "<td style='background-color:#550000 !important'>"; }
		else { echo "<td>"; }
	}
	else
	{
		if($result['date'] == date('Y-m-d')) { echo "<tr style='background-color:#FFFFBB !important'>"; }
		else if(strtotime($result['date']) < strtotime("today")) { echo "<tr style='background-color:#FFBBBB !important'>"; }
		else { echo "<tr>"; }
	    echo "<td>";
	}
	if(strlen($result['url']) > 1) { echo "<a target=_new href='" . $result['url'] . "'>"; }
	echo $result['task'] . "</a>";
    if($login_admin == 1) { echo "<span style='float:right'><a title='Edit task' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-date=\"" . $result['date'] . "\" data-prjid='" . $id . "' data-url=\"" . $result['url'] . "\" data-details=\"" . $result['details'] . "\" data-repeat=\"" . $result['repeatset'] . "\" data-task=\"" . $result['task'] . "\"><i class='fa fa-pencil-square-o'></i></a></span>"; }
	echo "</td>";
	if($darkmode)
	{
		if($result['date'] == date('Y-m-d')) { echo "<td style='background-color:#555500 !important'>"; }
		else if(strtotime($result['date']) < strtotime("today")) { echo "<td style='background-color:#550000 !important'>"; }
		else { echo "<td>"; }
	}
	else
	{
		echo "<td>";
	}
	echo $result['date'] . "</td>";
	if($darkmode)
	{
		if($result['date'] == date('Y-m-d')) { echo "<td style='background-color:#555500 !important'>"; }
		else if(strtotime($result['date']) < strtotime("today")) { echo "<td style='background-color:#550000 !important'>"; }
		else { echo "<td>"; }
	}
	else
	{
		echo "<td>";
	}
	echo $result['repeatset'] . "<span style='float:right'><a title='Mark as done' href='javascript:check_task(" . $result['id'] . ", " . $id . ")'><i class='fa fa-check-square'></i></a></span></td></tr>";
	$taskcount += 1;
}
?>
    </tbody>
</table>
<?php if($taskcount > 0) { ?> <script>$(document).ready(function(){$('#tasks').DataTable({'lengthChange':false,'searching':false,'order':[[1,'asc']]});});</script> <?php } ?>

<br><h4>Notes</h4>

<script>
function save_notes()
{
	if(!document.getElementById("result")) { $("#notes").data("statusBar").append('<div class="label" id="result"></div>'); }
	document.getElementById("result").innerHTML = "";
	var notes = $("#notes").Editor("getText");
	var id = "<?php echo $id; ?>";
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if(this.readyState == 4)
		{
			if(this.responseText.includes("DONE"))
			{
				document.getElementById("result").innerHTML = "<b><i>Saved successfully.</i></b>";
				$("#curnote").find('option:selected').val(notes.replace('"', '&quot;'));
			}
			else if(this.responseText.includes("class='run_msg'><b>ERROR:</b>"))
			{
				document.getElementById("result").innerHTML = this.responseText.substring(this.responseText.indexOf("class='run_msg'><b>ERROR:</b>") + 29, this.responseText.lastIndexOf("<span"));
			}
			else { document.getElementById("result").innerHTML = this.responseText; }
		}
	};
	xhttp.open("POST", "save_note.py", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("id=" + encodeURIComponent(id) + "&name=Default&notes=" + encodeURIComponent(notes));
}
function share_notes()
{
	document.getElementById("result").innerHTML = "";
	var notes = $("#notes").Editor("getText");
	var note_name = document.getElementById('note_name').value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function()
	{
		if(this.readyState == 4)
		{
			if(this.responseText.includes("DONE"))
			{
				var url = this.responseText.substring(this.responseText.lastIndexOf("<URL>") + 5, this.responseText.lastIndexOf("</URL>"));
				document.getElementById("result").innerHTML = "<i><a targe=_blank href='" + url + "'>" + url + "</a></i>";
			}
			else if(this.responseText.includes("class='run_msg'><b>ERROR:</b>"))
			{
				document.getElementById("result").innerHTML = this.responseText.substring(this.responseText.indexOf("class='run_msg'><b>ERROR:</b>") + 29, this.responseText.lastIndexOf("<span"));
			}
			else { document.getElementById("result").innerHTML = this.responseText; }
		}
	};
	xhttp.open("POST", "share_note.py", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("name=" + encodeURIComponent(note_name) + "&notes=" + encodeURIComponent(notes));
}
</script>

<input class="form-control" type="hidden" value="Default" id="note_name" required>
<div class="form-control Editor-editor" style="width:90%;height:500px" id="notes" name="notes"></div><br>

<script>
document.addEventListener("DOMContentLoaded", function()
{
	document.querySelector('[title="Save note"]').setAttribute("onclick", "save_notes()");
	document.querySelector('[title="Share note"]').setAttribute("onclick", "share_notes()");
});
</script>

<script type="text/javascript">
$(document).ready( function()
{
	$("#notes").Editor({});
	$("#notes").Editor("setText", "<?php echo str_replace("\n", "", str_replace("\"", "&quot;", $notes)); ?>")
	var wordCount = $("#notes").Editor("countWords", $("#notes").data("editor"));
	var charCount = $("#notes").Editor("countChars", $("#notes").data("editor"));
	$("#notes").data("statusBar").html('<div class="label">'+'Words : ' + wordCount + '</div>');
	$("#notes").data("statusBar").append('<div class="label">'+'Characters : ' + charCount + '</div>');
	$("#notes").data("statusBar").append('<div class="label" id="result">'+'Note loaded : ' + document.getElementById("note_name").value + '</div>');
});
</script>
<br>

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
    xhttp.open("POST", "./check_task.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + encodeURIComponent(id) + "&prjid=" + encodeURIComponent(prjid));
}

function create_task(prjid)
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
    xhttp.open("POST", "./create_task.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("prjid=" + encodeURIComponent(prjid));
}
</script>

<?php
if($login_admin == 1) { modal("save_task.py", "delete_task.py", array(
	array("type" => "text", "var" => "task", "label" => "Task title:", "options" => "maxlength='150' required"),
	array("type" => "hidden", "var" => "prjid"),
	array("type" => "date", "var" => "date", "label" => "Due date:", "options" => "maxlength='20' required"),
	array("type" => "select", "var" => "repeat", "label" => "Repeat:", "options" => "required", "choices" => array(
		"Never", "Next day", "Next work day", "Next week", "Next month", "Next year"
	)),
	array("type" => "text", "var" => "url", "label" => "Task URL:", "options" => "maxlength='200'"),
	array("type" => "textarea", "var" => "details", "label" => "Task details:", "options" => "maxlength='1000'")
)); }
?>

<?php include '../bottom.php'; ?>

