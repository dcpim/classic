<?php $public_page = 1; include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-paw"></i></a> AI Image Generator</h3>

<script>
function generate()
{
	document.getElementById('submit').disabled = true;
	var prompt = document.getElementById('prompt').value;
	var a = document.createElement("a");
	a.setAttribute("target", "_blank");
	a.crossOrigin = "Anonymous";
	document.getElementById("output").appendChild(a);
	var img = document.createElement("img");
	img.setAttribute("src", "data:image/jpeg;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==");
	img.setAttribute("height", "375");
	img.setAttribute("width", "375");
	img.crossOrigin = "Anonymous";
	img.setAttribute("style", "margin:1px;padding:1px");
	img.setAttribute("alt", prompt);
	a.appendChild(img);
	var xhttp = new XMLHttpRequest();
	xhttp.open("POST", "generate.py", true);
	xhttp.onreadystatechange = function()
	{
		if(this.readyState == 4 && this.status == 200)
		{
			document.getElementById('submit').disabled = false;
			if(this.response.indexOf("<") !== -1)
			{
				if(this.response.indexOf("Exception:") !== -1)
				{
					var error = this.response.substring(this.response.lastIndexOf("Exception:") + 11, this.response.lastIndexOf("-->"));
					alert(error);
				}
				else
				{
					alert(this.response);
				}
			}
			else
			{
				img.src = "data:image/jpeg;base64," + this.response;
				a.href = "data:image/jpeg;base64," + this.response;
			}
		}
	};
	var data = new FormData();
	var file = document.getElementById('filename').files[0];
	data.append("filename", file);
	data.append("prompt", prompt);
	xhttp.send(data);
}
</script>

<div class="row">
	<div class="col-md-1">
	</div>
	<div class="col-md-9">
		<input class="form-control" maxlength="200" type="text" id="prompt" placeholder="Enter your prompt here..." required>
	</div>
	<div class="col-md-2">
		<button type='button' id='submit' class='btn btn-primary' onclick='generate()'>Generate</button>
	</div>
</div>

<br>

<div id="output"></div>

<br>

<?php include '../bottom.php'; ?>

