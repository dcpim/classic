<?php include '../top.php'; ?>

<?php
$id = 0;
if(intval($_GET['id']) > 0) { $id = intval($_GET['id']); }
if($login_admin != 1) { echo "<meta http-equiv='refresh' content='0; URL=/' />"; exit(); }
$results = $db->query("SELECT * FROM projects WHERE id = " . intval($_GET['id']) . ";");
while($result = $results->fetch_assoc())
{
	$name = $result['name'];
}
?>

<h3><a title='Back to project' href="/projects/?id=<?php echo $id; ?>"><i class="fa fa-lock"></i></a> Secrets - <?php echo $name; ?></h3>

<script src="/aes.js"></script>

<script>
function decrypt()
{
	document.getElementById("result1").innerHTML = "<br>";
	document.getElementById("result2").innerHTML = "<br>";
	var selector = document.getElementById('site');
	var encrypted = selector.options[selector.selectedIndex];
	var secret = document.getElementById('secret');
	var master = document.getElementById('master');
	var note = document.getElementById('note');
	var decrypted = CryptoJS.AES.decrypt(encrypted.value.split('|||')[0], master.value);
	if(master.value.length == 0)
	{
		document.getElementById("result1").innerHTML = "Missing master password";
		return;
	}
	note.value = encrypted.value.split('|||')[1];
	secret.value = decrypted.toString(CryptoJS.enc.Utf8);
}
function newsecret()
{
	document.getElementById("result1").innerHTML = "<br>";
	document.getElementById("result2").innerHTML = "<br>";
	var site = document.getElementById('new_site');
	var secret = document.getElementById('new_secret');
	var note = document.getElementById('new_note');
	var master = document.getElementById('master');
	var encrypted = CryptoJS.AES.encrypt(secret.value, master.value);
	var xhttp = new XMLHttpRequest();
	if(master.value.length == 0)
	{
		document.getElementById("result2").innerHTML = "Missing master password.";
		return;
	}
	if(secret.value.length == 0)
	{
		document.getElementById("result2").innerHTML = "Missing secret.";
		return;
	}
	xhttp.onreadystatechange = function()
	{
		if(this.readyState == 4)
		{
			if(this.responseText.includes("DONE"))
			{
				document.getElementById("result2").innerHTML = "New secret saved.";
			}
			else
			{
				document.getElementById("result2").innerHTML = this.responseText;
			}
		}
	};
	xhttp.open("POST", "secrets.py", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("id=<?php echo $id; ?>&action=create&site=" + encodeURIComponent(site.value) + "&note=" + encodeURIComponent(note.value) + "&secret=" + encodeURIComponent(encrypted.toString()));
}
function updatesecret()
{
	document.getElementById("result1").innerHTML = "<br>";
	document.getElementById("result2").innerHTML = "<br>";
	var selector = document.getElementById('site');
	var site = selector.options[selector.selectedIndex];
	var secret = document.getElementById('secret');
	var note = document.getElementById('note');
	var master = document.getElementById('master');
	var encrypted = CryptoJS.AES.encrypt(secret.value, master.value);
	var xhttp = new XMLHttpRequest();
	if(master.value.length == 0)
	{
		document.getElementById("result1").innerHTML = "Missing master password.";
		return;
	}
	if(secret.value.length == 0)
	{
		document.getElementById("result2").innerHTML = "Missing secret.";
		return;
	}
	xhttp.onreadystatechange = function()
	{
        if(this.readyState == 4)
        {
            if(this.responseText.includes("DONE"))
            {
                document.getElementById("result1").innerHTML = "Secret updated.";
            }
            else
            {
                document.getElementById("result1").innerHTML = this.responseText;
            }
        }
	};
	xhttp.open("POST", "secrets.py", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("id=<?php echo $id; ?>&action=update&site=" + encodeURIComponent(site.text) + "&note=" + encodeURIComponent(note.value) + "&secret=" + encodeURIComponent(encrypted.toString()));
}
function deletesecret()
{
	document.getElementById("result1").innerHTML = "<br>";
	document.getElementById("result2").innerHTML = "<br>";
	var selector = document.getElementById('site');
	var site = selector.options[selector.selectedIndex];
    var secret = document.getElementById('secret');
    var master = document.getElementById('master');
    var encrypted = CryptoJS.AES.encrypt(secret.value, master.value);
	var xhttp = new XMLHttpRequest();
    if(master.value.length == 0)
    {
        document.getElementById("result1").innerHTML = "Missing master password.";
        return;
    }
	xhttp.onreadystatechange = function()
	{
        if(this.readyState == 4)
        {
            if(this.responseText.includes("DONE"))
            {
                document.getElementById("result1").innerHTML = "Secret deleted.";
            }
            else
            {
                document.getElementById("result1").innerHTML = this.responseText;
            }
        }
	};
	xhttp.open("POST", "secrets.py", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("id=<?php echo $id; ?>&action=delete&site=" + encodeURIComponent(site.text) + "&secret=" + encodeURIComponent(encrypted.toString()));
}
function checkmaster()
{
	var master = document.getElementById('master');
	if(CryptoJS.AES.decrypt("U2FsdGVkX1/awIgkboZ3pAyJ1AglMueDqjj5k2KJReI=", master.value).toString(CryptoJS.enc.Utf8) == "ControlValue!")
	{
		document.getElementById("checkmaster").innerHTML = "<i class='fa fa-check'></i>";
	}
	else
	{
		document.getElementById("checkmaster").innerHTML = "<i class='fa fa-times'></i>";
	}
}
</script>

<div class="row"><br>
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<p><input class="form-control" type="password" id="master" placeholder="Master Password" onkeypress="checkmaster()" required><span id="checkmaster" style="float:right"></span></p>
		<br>
		<form>
			<p><select class="form-control" onchange="decrypt()" id="site"><option>Select a site name</option>
<?php
$results = $db->query("SELECT * FROM secrets WHERE prjid = " . $id . " ORDER BY site ASC;");
while($result = $results->fetch_assoc())
{
    echo "<option value=\"" . $result['secret'] . "|||" . $result['note'] . " \">" . $result['site'] . "</option>";
}
?>
			</select></p>
			<p><input class="form-control" maxlength='100' type="text" id="secret" id="secret" placeholder="Secret" required></p>
			<p><input class="form-control" maxlength='200' type="text" id="note" id="note" placeholder="Note (optional)"></p>
			<p><div class='col-md-6'><input class="form-control btn btn-primary" type="button" onclick="updatesecret()" value="Update secret"></div>
			<div class='col-md-6'><input class="form-control btn btn-danger" type="button" onclick="deletesecret()" value="Delete secret"></div></p><br>
		</form>
		<div id="result1"><br></div>
		<br>
		<form>
			<p><input class="form-control" maxlength='100' type="text" id="new_site" placeholder="Site name (login name)" required></p>
			<p><input class="form-control" maxlength='100' type="text" id="new_secret" placeholder="Secret" required></p>
			<p><input class="form-control" maxlength='200' type="text" id="new_note" placeholder="Note (optional)"></p>
			<p><div class='col-md-12'><input class="form-control btn btn-primary" type="button" onclick="newsecret()" value="Create new secret"></div></p><br>
		</form>
		<div id="result2"><br></div>
		<br>
	</div>
	<div class="col-md-3"></div>
</div>

<?php include '../bottom.php'; ?>

