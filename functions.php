<?php
// Parses file size and returns human readable string
function size_format($size)
{
	if($size/1024/1000 > 999) { return number_format($size/1024/1000/1000,1) . " GB"; }
	elseif($size/1024 > 999) { return number_format($size/1024/1000,1) . " MB"; }
	else { return number_format($size/1024,1) . " KB"; }
}

// Sends SNS notification
function notify($msg)
{
	global $CONFIG;
	shell_exec("aws sns publish --region '" . $CONFIG['AWS_REGION'] . "' --topic-arn '" . $CONFIG['SNS_ARN'] . "' --message \"" . str_replace('"', "'", $msg) . "\"");
}

// Returns icon for a file type
function fileicon($filename)
{
	$icon = "file-o";
	if(substr($filename, -3) == "jpg") { $icon = "file-image-o"; }
	if(substr($filename, -3) == "gif") { $icon = "file-image-o"; }
	if(substr($filename, -3) == "png") { $icon = "file-image-o"; }
	if(substr($filename, -3) == "pdf") { $icon = "file-pdf-o"; }
	if(substr($filename, -3) == "zip") { $icon = "file-zip-o"; }
	if(substr($filename, -3) == "tar") { $icon = "file-zip-o"; }
	if(substr($filename, -3) == ".gz") { $icon = "file-zip-o"; }
	if(substr($filename, -3) == "mp4") { $icon = "file-video-o"; }
	if(substr($filename, -3) == "mp3") { $icon = "file-audio-o"; }
	if(substr($filename, -3) == "txt") { $icon = "file-text-o"; }
	if(substr($filename, -3) == "exe") { $icon = "file-code-o"; }
	return $icon;
}

// Generate a random string
function randomstr($length = 10)
{
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for($i = 0; $i < $length; $i++)
	{
		$randomString .= $characters[random_int(0, $charactersLength - 1)];
	}
	return $randomString;
}

// Display one or more pipeline
function pipelines($title, $pipelines)
{
	echo "<hr><h3>" . $title . "</h3>";
	foreach($pipelines as $pipeline)
	{
		$submit_id = randomstr();
		$divs = 0;
		echo "<h4><i class='fa fa-" . $pipeline['icon'] . "' aria-hidden='true'></i> " . $pipeline['title'] . "</h4>";
		echo "<form method='POST' action='" . $pipeline['action'] . "' enctype='multipart/form-data' onSubmit='document.getElementById(\"" . $submit_id . "\").disabled=true;'><div class='row'>";
		foreach($pipeline['inputs'] as $input)
		{
			if($input['type'] == "row")
			{
				echo "</div><div class='row'>";
			}
			if($input['type'] == "empty")
			{
				echo "<div class='col-md-" . $input['size'] . "'></div>";
			}
			if($input['type'] == "label")
			{
				echo "<div class='col-md-" . $input['size'] . "'><h4>" . $input['label'] . "</h4></div>";
			}
			if($input['type'] == "file")
			{
				echo "<div class='col-md-" . $input['size'] . "'><input class='form-control' type='file' " . $input['options'] . "></div>";
			}
			if($input['type'] == "text")
			{
				echo "<div class='col-md-" . $input['size'] . "'><input class='form-control' type='text' " . $input['options'] . "></div>";
			}
			if($input['type'] == "password")
			{
				echo "<div class='col-md-" . $input['size'] . "'><input class='form-control' type='password' " . $input['options'] . "></div>";
			}
			if($input['type'] == "hidden")
			{
				echo "<input class='form-control' type='hidden' " . $input['options'] . ">";
			}
			if($input['type'] == "checkbox")
			{
				$tmp = randomstr();
				echo "<div class='col-md-" . $input['size'] . "'><input class='form-control-check' type='checkbox' id='" . $tmp . "' " . $input['options'] . "><label for='" . $tmp . "'> &nbsp; " . $input['label'] . "</label></div>";
			}
			if($input['type'] == "date")
			{
				$tmp = randomstr();
				echo "<div class='col-md-" . $input['size'] . "'><input class='form-control' type='text' id='" . $tmp . "' value='" . date('Y-m-d') . "' " . $input['options'] . "></div>";
				echo "<script>$('#" . $tmp . "').datepicker({format:'yyyy-mm-dd'});</script>";
			}
			if($input['type'] == "number")
			{
				echo "<div class='col-md-" . $input['size'] . "'><input class='form-control' type='number' " . $input['options'] . "></div>";
			}
			if($input['type'] == "submit")
			{
				echo "<div class='col-md-" . $input['size'] . "'><input class='form-control btn btn-primary' type='submit' id='" . $submit_id . "' " . $input['options'] . "></div>";
			}
			if($input['type'] == "select")
			{
				echo "<div class='col-md-" . $input['size'] . "'><select class='form-control' " . $input['options'] . ">";
				foreach($input['choices'] as $choice) { echo "<option>" . $choice . "</option>"; }
				echo "</select></div>";
			}
			if($input['type'] == "selectkv")
			{
				echo "<div class='col-md-" . $input['size'] . "'><select class='form-control' " . $input['options'] . ">";
				foreach($input['choices'] as $k => $v) { echo "<option value=\"" . $k . "\">" . $v . "</option>"; }
				echo "</select></div>";
			}
		}
		echo "</div></form>";
	}
	echo "<br>";
}

// Display the top of a standard table
function tabletop($id, $headers, $noresp=0)
{
	echo "<table class='table table-striped table-hover display ";
	if($noresp == 0) { echo "responsive"; }
	echo "' id='" . $id . "'>";
	echo "<thead>" . $headers . "</thead>";
	echo "<tbody>";
}

// Display the bottom of a standard table
function tablebottom($id, $colnum, $direction)
{
	echo "</tbody></table>";
	echo "<script>\$(document).ready(function(){\$('#" . $id . "').DataTable({'oSearch':{'sSearch':search},'aLengthMenu':[10, 25, 50, 100, 500],'order':[";
	if($colnum != "") { echo "[" . $colnum . ",'" . $direction . "']"; }
	echo "]});});</script>";
}

// Display the elements of a modal update dialog
function modal($update, $delete, $inputs, $custom_event = "location.reload();", $custom_button = "", $custom_button_api = "")
{
	$update_function = randomstr();
	$delete_function = randomstr();
	$custom_function = randomstr();
	echo "<script>";
	echo "\$(document).on('click', '.update', function() {";
	foreach($inputs as $input)
	{
		if($input['type'] == "checkbox")
		{
			echo "if(\$(this).data('" . $input['var'] . "') == 1) { \$('.modal-body #modal_" . $input['var'] . "').prop('checked',true); }";
			echo "else { \$('.modal-body #modal_" . $input['var'] . "').prop('checked',false); }";
		}
		else
		{
			echo "\$('.modal-body #modal_" . $input['var'] . "').val(\$(this).data('" . $input['var'] . "'));";
		}
	}
	echo "\$('.modal-body #modal_id').val(\$(this).data('id'));";
	echo "});";
	echo "function " . $update_function . "() {";
	foreach($inputs as $input)
	{
		if(!str_contains($input['options'], "readonly"))
		{
			if($input['type'] == "checkbox")
			{
				echo "var " . $input['var'] . " = 0;";
				echo "if(document.getElementById('modal_" . $input['var'] . "').checked) { " . $input['var'] . " = 1; }";
			}
			else
			{
				echo "var " . $input['var'] . " = document.getElementById('modal_" . $input['var'] . "').value;";
			}
		}
	}
	echo "var id = document.getElementById('modal_id').value;";
	echo "var xhttp = new XMLHttpRequest();";
	echo "xhttp.open('POST', '" . $update . "', true);";
	echo "xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');";
	echo "xhttp.onreadystatechange = function() {";
	echo "if(this.readyState == 4) { if(this.responseText.includes('DONE!')) {";
	echo $custom_event;
	echo "} else { alert(this.responseText); } } };";
	echo "xhttp.send('id=' + encodeURIComponent(id)";
	foreach($inputs as $input)
	{
		if(!str_contains($input['options'], "readonly"))
		{
			echo " + '&" . $input['var'] . "=' + encodeURIComponent(" . $input['var'] . ")";
		}
	}
	echo "); }";
	echo "function " . $delete_function . "() {";
	echo "var id = document.getElementById('modal_id').value;";
	echo "var xhttp = new XMLHttpRequest();";
	echo "xhttp.open('POST', '" . $delete . "', true);";
	echo "xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');";
	echo "xhttp.onreadystatechange = function() {";
	echo "if(this.readyState == 4) { if(this.responseText.includes('DONE!')) {";
	echo "location.reload();";
	echo "} else { alert(this.responseText); } } };";
	echo "xhttp.send('id=' + encodeURIComponent(id)); }";
	if($custom_button_api != "")
	{
		echo "function " . $custom_function . "() {";
		echo "var id = document.getElementById('modal_id').value;";
		echo "var xhttp = new XMLHttpRequest();";
		echo "xhttp.open('POST', '" . $custom_button_api . "', true);";
		echo "xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');";
		echo "xhttp.onreadystatechange = function() {";
		echo "if(this.readyState == 4) { if(this.responseText.includes('DONE!')) {";
		echo "location.reload();";
		echo "} else { alert(this.responseText); } } };";
		echo "xhttp.send('id=' + encodeURIComponent(id)); }";
	}
	echo "</script>";
	echo "<div class='modal fade' id='updateModal' tabindex='-1' role='dialog' aria-labelledby='updateLabel' aria-hidden='true'>";
	echo "<div class='modal-dialog' role='document'>";
	echo "<div class='modal-content'>";
	echo "<div class='modal-header'>";
	echo "<h5 class='modal-title' id='updateLabel'>Update entry</h5>";
	echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
	echo "<span aria-hidden='true'>&times;</span>";
	echo "</button></div><div class='modal-body'>";
	foreach($inputs as $input)
	{
		if($input['type'] == "text")
		{
			echo $input['label'] . " <input class='form-control' type='text' id='modal_" . $input['var'] . "' " . $input['options'] . "><br>";
		}
		if($input['type'] == "number")
		{
			echo $input['label'] . " <input class='form-control' type='number' id='modal_" . $input['var'] . "' " . $input['options'] . "><br>";
		}
		if($input['type'] == "hidden")
		{
			echo "<input type='hidden' id='modal_" . $input['var'] . "'>";
		}
		if($input['type'] == "date")
		{
			echo $input['label'] . " <input class='form-control' type='text' id='modal_" . $input['var'] . "' " . $input['options'] . "><br>";
			echo "<script>$('#modal_" . $input['var'] . "').datepicker({format:'yyyy-mm-dd'});</script>";
		}
		if($input['type'] == "textarea")
		{
			echo $input['label'] . " <textarea class='form-control' style='height:75px' type='text' id='modal_" . $input['var'] . "' " . $input['options'] . "></textarea><br>";
		}
		if($input['type'] == "select")
		{
			echo $input['label'] . " <select class='form-control' id='modal_" . $input['var'] . "' " . $input['options'] . ">";
			foreach($input['choices'] as $choice)
			{
				echo "<option>" . $choice . "</option>";
			}
			echo "</select><br>";
		}
		if($input['type'] == "selectkv")
		{
			echo $input['label'] . " <select class='form-control' id='modal_" . $input['var'] . "' " . $input['options'] . ">";
			foreach($input['choices'] as $k => $v)
			{
				echo "<option value=\"" . $k . "\">" . $v . "</option>";
			}
			echo "</select><br>";
		}
		if($input['type'] == "checkbox")
		{
			echo "<input class='form-control-check' type='checkbox' id='modal_" . $input['var'] . "' " . $input['options'] . "><label for='modal_" . $input['var'] . "'> &nbsp; " . $input['label'] . "</label><br>";
		}
	}
	echo "<input type='hidden' id='modal_id' required>";
	echo "</div><div class='modal-footer'>";
	echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>";
	echo "<button type='button' class='btn btn-primary' onclick='" . $update_function . "()' data-dismiss='modal'>Save</button>";
	if($delete != "")
	{
		echo "<button type='button' class='btn btn-danger' style='float:left' onclick='" . $delete_function . "()' data-dismiss='modal'>Delete</button>";
	}
	if($custom_button != "")
	{
		echo "<button type='button' class='btn btn-primary' style='float:left' onclick='" . $custom_function . "()' data-dismiss='modal'>" . $custom_button . "</button>";
	}
	echo "</div></div></div></div>";
}
?>
