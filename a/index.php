<?php include '../top.php'; ?>

<script>
function send_token()
{
	var username = document.getElementById('username').value;
	var xhttp = new XMLHttpRequest();
	if(username == "")
	{
        document.getElementById("result_1").innerHTML = "<b>Missing username.</b>";
		return;
	}
	xhttp.open("POST", "send_token.py", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function()
    {
        if(this.readyState == 4)
        {
            if(this.responseText.includes("DONE!"))
			{
	            document.getElementById("result_1").innerHTML = "<b>Token sent.</b>";
			}
			else
			{
	            document.getElementById("result_1").innerHTML = "<b>Failed to find a valid contact.</b>";
			}
        }
    };
	xhttp.send("username=" + encodeURIComponent(username));
}
function send_validate()
{
	var username = document.getElementById('username').value;
	var token = document.getElementById('validate').value;
	var xhttp = new XMLHttpRequest();
	if(username == "")
	{
        document.getElementById("result_2").innerHTML = "<b>Missing username.</b>";
		return;
	}
	xhttp.open("POST", "validate.py", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function()
    {
        if(this.readyState == 4)
        {
            if(this.responseText.includes("DONE!"))
			{
				var token = this.responseText.substring(this.responseText.indexOf("###") + 3, this.responseText.lastIndexOf("|||"));
				document.getElementById('totp').value = token;
	            document.getElementById("result_2").innerHTML = "<b>Token valid.</b>";
			}
			else
			{
	            document.getElementById("result_2").innerHTML = "<b>Invalid token.</b>";
			}
        }
    };
	xhttp.send("username=" + encodeURIComponent(username) + "&token=" + encodeURIComponent(token));
}
</script>

<form method="POST" action="/b/">
<div class="row">
	<div class="col-md-3">
	</div>
	<div class="col-md-6">
		<h3>Login form</h3>
	</div>
	<div class="col-md-3">
	</div>
</div>
<div class="row">
	<div class="col-md-3">
	</div>
	<div class="col-md-3">
		<input class="form-control" type="text" id="username" name="username" placeholder="Username" required>
	</div>
	<div class="col-md-3">
		<input class="form-control" type="password" name="password" placeholder="Password" required>
	</div>
	<div class="col-md-3">
	</div>
</div>
<div class="row">
	<div class="col-md-3">
	</div>
	<div class="col-md-6">
		<hr>
		<h4><i class="fa fa-commenting-o"></i> Two-factor authentication (SMS)</h3>
	</div>
	<div class="col-md-3">
	</div>
</div>
<div class="row">
	<div class="col-md-3">
	</div>
	<div class="col-md-4">
		<input class="form-control" type="number" name="token" placeholder="Token (optional)">
		<div id="result_1"></div>
	</div>
	<div class="col-md-2">
		<input class="form-control btn btn-primary" type="button" onclick="send_token()" value="Send token">
	</div>
	<div class="col-md-3">
	</div>
</div>
<div class="row">
	<div class="col-md-3">
	</div>
	<div class="col-md-6">
		<hr>
		<h4><i class="fa fa-mobile"></i> Two-factor authentication (Phone app)</h3>
	</div>
	<div class="col-md-3">
	</div>
</div>
<div class="row">
	<div class="col-md-3">
	</div>
	<div class="col-md-4">
		<input class="form-control" type="number" id="validate" placeholder="Token (optional)">
		<input type="hidden" name="totp" id="totp">
		<div id="result_2"></div>
	</div>
	<div class="col-md-2">
		<input class="form-control btn btn-primary" type="button" onclick="send_validate()" value="Validate">
	</div>
	<div class="col-md-3">
	</div>
</div>
<div class="row">
	<div class="col-md-3">
	</div>
	<div class="col-md-6">
		<hr>
		<input class="form-control btn btn-primary" type="submit" value="Login">
	</div>
	<div class="col-md-3">
	</div>
</div>
</form>

<?php include '../bottom.php'; ?>

