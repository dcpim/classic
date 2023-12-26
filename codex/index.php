<?php include '../top.php'; ?>

<?php
$results = $db->query("SELECT * FROM projects WHERE id = " . intval($_GET['prjid']) . ";");
while($result = $results->fetch_assoc())
{
	$name = $result['name'];
}
?>

<link rel="stylesheet" href="/prism.css">
<script src="/prism.js"></script>

<h3><a title='Back to project' href="/projects/?id=<?php echo intval($_GET['prjid']); ?>"><i class="fa fa-code"></i></a> Codex - <?php echo $name; ?></h3>

<script>
$(window).load(function(){
    $("#notebooks").show();
    $("#loading").hide();
	Prism.highlightAllUnder(document);
});
</script>

<div id="loading"><p><center><b>Loading...</b></center></p></div>

<table class='table table-hover display' id='notebooks' style="display:none">
    <thead><tr><th><h4><span style='float:right'>
<!---<span style='float:right;color:#999999'>Filter: <a href='/codex/'>All</a>, <a href='/codex/?lang=bash'>Bash</a>, <a href='/codex/?lang=batch'>Batch</a>, <a href='/codex/?lang=javascript'>JavaScript</a>, <a href='/codex/?lang=json'>JSON</a>, <a href='/codex/?lang=php'>PHP</a>, <a href='/codex/?lang=powershell'>Powershell</a>, <a href='/codex/?lang=python'>Python</a>, <a href='/codex/?lang=sql'>SQL</a>, <a href='/codex/?lang=yaml'>YAML</a></span>--->
<a href='create.py?prjid=<?php echo intval($_GET['prjid']); ?>'><i class="fa fa-plus"></i> &nbsp; Add entry</a></span></h4></th></tr></thead>
    <tbody>
<?php
if($_GET['id']) { $results = $db->query("SELECT * FROM code WHERE id = " . intval($_GET['id']) . " ORDER BY description ASC;"); }
else if($_GET['lang']) { $results = $db->query("SELECT * FROM code WHERE prjid = " . intval($_GET['prjid']) . " and language = \"" . preg_replace("/[^a-z]+/", "", $_GET['lang']) . "\" ORDER BY description ASC;"); }
else if($_GET['prjid']) { $results = $db->query("SELECT * FROM code WHERE prjid = " . intval($_GET['prjid']) . " ORDER BY description ASC;"); }
else { $results = $db->query("SELECT * FROM code WHERE prjid = 0 ORDER BY description ASC;"); }
while($result = $results->fetch_assoc())
{
	echo "<tr><td><h4><span style='float:right;color:#999999'>" . $result['language'] . " &nbsp; ";
	if($result['sync'] != "") { echo " <i class='fa fa-link'></i> &nbsp; "; }
	if($login_admin == 1) { echo "<a title='Edit entry' class='update' href='#' data-toggle='modal' data-target='#updateModal' data-id='" . $result['id'] . "' data-content=\"" . str_replace('"', '&quot;', $result['content']) . "\" data-date=\"" . $result['date'] . "\" data-sync=\"" . $result['sync'] . "\" data-pub=\"" . $result['pub'] . "\" data-description=\"" . $result['description'] . "\" data-prjid=\"" . $result['prjid'] . "\" data-language=\"" . $result['language'] . "\"><i class='fa fa-pencil-square-o'></i></a> &nbsp; "; }
	echo "<a href='/codex/?id=" . $result['id'] . "&prjid=" . intval($_GET['prjid']) . "'><i class='fa fa-external-link'></i></a></span>" . $result['description'] . "</h4><pre style='word-wrap: normal; white-space:pre-wrap; overflow-wrap:normal'><code style='word-wrap: normal; white-space:pre-wrap; overflow-wrap:normal' class='line-numbers language-" . strtolower($result['language']) . "'>" . str_replace("<", "&lt;", $result['content']) . "</code></pre></td></tr>";
}
?>
	</tbody>
</table>
<script>$(document).ready(function(){$('#notebooks').DataTable({'paging':false,'ordering':false,'aaSorting':[]});});</script>

<script>
$(document).on("click", ".update", function()
{
    $(".modal-body #modal_id").val($(this).data('id'));
    $(".modal-body #modal_sync").val($(this).data('sync'));
    $(".modal-body #modal_content").val($(this).data('content'));
    $(".modal-body #modal_description").val($(this).data('description'));
    $(".modal-body #modal_language").val($(this).data('language'));
    $(".modal-body #modal_prjid").val($(this).data('prjid'));
	if($(this).data('pub') == 1) { $('.modal-body #modal_pub').prop('checked', true); }
	else { $('.modal-body #modal_pub').prop('checked', false); }
});
function save_file()
{
    var id = document.getElementById('modal_id').value;
    var sync = document.getElementById('modal_sync').value;
    var description = document.getElementById('modal_description').value;
    var language = document.getElementById('modal_language').value;
    var prjid = document.getElementById('modal_prjid').value;
    var content = document.getElementById('modal_content').value;
	var pub = 0;
	if(document.getElementById('modal_pub').checked) { pub = 1; }
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "update.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
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
    xhttp.send("id=" + encodeURIComponent(id) + "&description=" + encodeURIComponent(description) + "&content=" + encodeURIComponent(content) + "&language=" + encodeURIComponent(language) + "&prjid=" + encodeURIComponent(prjid) + "&pub=" + encodeURIComponent(pub) + "&sync=" + encodeURIComponent(sync));
}
function delete_file()
{
    var id = document.getElementById('modal_id').value;
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "delete.py", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
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
    xhttp.send("id=" + encodeURIComponent(id));
}
</script>

<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update entry</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Description: <input class="form-control" maxlength='150' type="text" id="modal_description" value="" required><br>
        Language: <select class="form-control" id="modal_language" required><option>Python</option><option>JavaScript</option><option>SQL</option><option>Bash</option><option>Batch</option><option>PHP</option><option>PowerShell</option><option>JSON</option><option>YAML</option></select><br>
		Content: <textarea class="form-control" rows=10 id="modal_content" required></textarea><br>
        Sync from external location (Local, HTTPS or S3): <input class="form-control" maxlength='250' type="text" id="modal_sync" value="">
		<font size=-1><i>Syntax: /folder/file.ext, s3://bucket/archive.zip$/folder/file.ext, https://example.com/file.ext</i></font><br><br>
        Share file publicly: <input type="checkbox" id="modal_pub"><br>
        <input type="hidden" id="modal_prjid" value="">
        <input type="hidden" id="modal_id" value="" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="save_file()" data-dismiss="modal">Save</button>
        <button type="button" class="btn btn-danger" style="float:left" onclick="delete_file()" data-dismiss="modal">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('modal_content').addEventListener('keydown', function(e) {
  if (e.key == 'Tab') {
    e.preventDefault();
    var start = this.selectionStart;
    var end = this.selectionEnd;

    // set textarea value to: text before caret + tab + text after caret
    this.value = this.value.substring(0, start) +
      "\t" + this.value.substring(end);

    // put caret at right position again
    this.selectionStart =
      this.selectionEnd = start + 1;
  }
});
</script>

<?php include '../bottom.php'; ?>

