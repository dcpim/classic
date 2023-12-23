<?php include 'top.php'; ?>

<?php if($login_admin == 5 or $login_admin == 1) { ?>
<h4>External links</h4>

<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a target=_new href="/sig"><i class="fa fa-pencil fa-5x"></i><br>Signature sample</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a target=_new href="https://drive.google.com"><i class="fa fa-files-o fa-5x"></i><br>Google Drive</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a target=_new href="<?php echo $CONFIG['AWS_LINK'] ?>"><i class="fa fa-amazon fa-5x"></i><br>AWS Console</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a target=_new href="/share"><i class="fa fa-share fa-5x"></i><br>Share</a></center>
</div>

<?php } if($login_admin == 1 or $login_admin == 2 or $login_admin == 3) { ?>
<h4>Finance</h4>

<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/transactions"><i class="fa fa-list fa-5x"></i><br>Transactions</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/invoices"><i class="fa fa-arrow-circle-o-down fa-5x"></i><br>Invoices</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/expenses"><i class="fa fa-arrow-circle-o-up fa-5x"></i><br>Expenses</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/reports"><i class="fa fa-id-card fa-5x"></i><br>Reports</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/payroll"><i class="fa fa-envelope-open-o  fa-5x"></i><br>Payroll</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/income"><i class="fa fa-money fa-5x"></i><br>Personal</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/statements"><i class="fa fa-book fa-5x"></i><br>Statements</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/networth"><i class="fa fa-usd fa-5x"></i><br>Networth</a></center>
</div>

<?php } if($login_admin == 1 or $login_admin == 2 or $login_admin == 4) { ?>
<h4>Family section</h4>

<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/photos"><i class="fa fa-camera fa-5x"></i><br>Photos</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/videos"><i class="fa fa-film fa-5x"></i><br>Videos</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/music"><i class="fa fa-music fa-5x"></i><br>Music</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/files"><i class="fa fa-files-o fa-5x"></i><br>Files</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/health"><i class="fa fa-medkit fa-5x"></i><br>Health</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/lifechart"><i class="fa fa-user-circle-o fa-5x"></i><br>Lifechart</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
    <center><a href="/art"><i class="fa fa-female fa-5x"></i><br>Art</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
    <center><a href="/collection"><i class="fa fa-grav fa-5x"></i><br>Collection</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
    <center><a href="/tarot"><i class="fa fa-podcast fa-5x"></i><br>Tarot</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
    <center><a href="/story"><i class="fa fa-pencil-square fa-5x"></i><br>Story</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
    <center><a href="/generate"><i class="fa fa-paw fa-5x"></i><br>Generate</a></center>
</div>

<?php } if($login_admin == 1) { ?>
<h4>Personal section</h4>

<div class='img-thumbnail' style='display:inline-block;width:150px'>
    <center><a href="/wallpapers"><i class="fa fa-picture-o fa-5x"></i><br>Wallpapers</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/games"><i class="fa fa-gamepad fa-5x"></i><br>Screenshots</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/anime"><i class="fa fa-tv fa-5x"></i><br>Anime</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/steam"><i class="fa fa-steam-square fa-5x"></i><br>Games</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/feeds"><i class="fa fa-rss fa-5x"></i><br>Feeds</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/projects/list.php"><i class="fa fa-paperclip fa-5x"></i><br>Projects</a></center>
</div>

<h4>Administrative tools</h4>

<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/automate"><i class="fa fa-gears fa-5x"></i><br>Automation</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/devices"><i class="fa fa-server fa-5x"></i><br>Devices</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
	<center><a href="/logs"><i class="fa fa-file-text fa-5x"></i><br>Logs</a></center>
</div>
<div class='img-thumbnail' style='display:inline-block;width:150px'>
    <center><a href="/mail"><i class="fa fa-ban fa-5x"></i><br>Mail</a></center>
</div>

<br><br>

<script>
$(document).on("click", ".update", function() {});
</script>

<?php } ?>

<?php include 'bottom.php'; ?>

