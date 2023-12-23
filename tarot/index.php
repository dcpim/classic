<?php $public_page = 1; include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-podcast"></i></a> Welcome to this AI-powered Tarot Reading. Today you pulled...</h3>

<?php
$base_url = "https://" . str_replace('[bucket]', $CONFIG['BUCKET_IMAGES'], $CONFIG['STORAGE_HOST']) . "/Tarot/";
$default_img = "back.jpg";
$cards = array(
	"The fool" => "fool",
	"The magician" => "magician",
	"The high priestess" => "priestess",
	"The empress" => "empress",
	"The emperor" => "emperor",
	"The hierophant" => "hierophant",
	"The lovers" => "lovers",
	"The chariot" => "chariot",
	"Strength" => "strength",
	"The hermit" => "hermit",
	"The wheel of fortune" => "wheel",
	"Justice" => "justice",
	"The hanged man" => "hanged",
	"Death" => "death",
	"Temperance" => "temperance",
	"The devil" => "devil",
	"The tower" => "tower",
	"The star" => "star",
	"The moon" => "moon",
	"The sun" => "sun",
	"Judgement" => "judgement",
	"The world" => "world",
	"Two of Cups" => "cups",
	"Three of Cups" => "cups",
	"Four of Cups" => "cups",
	"Five of Cups" => "cups",
	"Six of Cups" => "cups",
	"Seven of Cups" => "cups",
	"Eight of Cups" => "cups",
	"Nine of Cups" => "cups",
	"Ten of Cups" => "cups",
	"Ace of Cups" => "cups",
	"Knight of Cups" => "cups",
	"Queen of Cups" => "cups",
	"King of Cups" => "cups",
	"Page of Cups" => "cups",
	"Two of Pentacles" => "pentacles",
	"Three of Pentacles" => "pentacles",
	"Four of Pentacles" => "pentacles",
	"Five of Pentacles" => "pentacles",
	"Six of Pentacles" => "pentacles",
	"Seven of Pentacles" => "pentacles",
	"Eight of Pentacles" => "pentacles",
	"Nine of Pentacles" => "pentacles",
	"Ten of Pentacles" => "pentacles",
	"Ace of Pentacles" => "pentacles",
	"Knight of Pentacles" => "pentacles",
	"Queen of Pentacles" => "pentacles",
	"King of Pentacles" => "pentacles",
	"Page of Pentacles" => "pentacles",
	"Two of Wands" => "wands",
	"Three of Wands" => "wands",
	"Four of Wands" => "wands",
	"Five of Wands" => "wands",
	"Six of Wands" => "wands",
	"Seven of Wands" => "wands",
	"Eight of Wands" => "wands",
	"Nine of Wands" => "wands",
	"Ten of Wands" => "wands",
	"Ace of Wands" => "wands",
	"Knight of Wands" => "wands",
	"Queen of Wands" => "wands",
	"King of Wands" => "wands",
	"Page of Wands" => "wands",
	"Two of Swords" => "swords",
	"Three of Swords" => "swords",
	"Four of Swords" => "swords",
	"Five of Swords" => "swords",
	"Six of Swords" => "swords",
	"Seven of Swords" => "swords",
	"Eight of Swords" => "swords",
	"Nine of Swords" => "swords",
	"Ten of Swords" => "swords",
	"Ace of Swords" => "swords",
	"Knight of Swords" => "swords",
	"Queen of Swords" => "swords",
	"King of Swords" => "swords",
	"Page of Swords" => "swords"
);
$pulled_cards = array_rand($cards, 3);
?>

<table style="border-spacing:5px;border-collapse:separate;padding-left:10px;padding-right:10px;width=100%;display:grid;<?php if($darkmode) { ?>background-color: #182025 !important;<?php } ?>">
	<tr>
		<td style="width:33%;padding:5px;vertical-align:top">
			<center><h2> <?php echo $pulled_cards[0]; ?></h2></center><br>
			<img id="card1" style="max-width:100%;margin:auto" src="<?php echo $base_url . $cards[$pulled_cards[0]]; ?>.jpg">
		</td>
		<td style="width:33%;padding:5px;vertical-align:top">
			<center><h2> <?php echo $pulled_cards[1]; ?></h2></center><br>
			<img id="card2" style="max-width:100%;margin:auto" src="<?php echo $base_url . $cards[$pulled_cards[1]]; ?>.jpg">
		</td>
		<td style="width:33%;padding:5px;vertical-align:top">
			<center><h2> <?php echo $pulled_cards[2]; ?></h2></center><br>
			<img id="card3" style="max-width:100%;margin:auto" src="<?php echo $base_url . $cards[$pulled_cards[2]]; ?>.jpg">
		</td>
	</tr>
</table>

<br>

<center><h4 id='reading'><img src='/images/loading.gif' title='Loading...'></img></h4></center>

<br>

<script>
var xhttp = new XMLHttpRequest();
xhttp.open("POST", "/pipelines/ai.py", true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.onreadystatechange = function()
{
	document.getElementById("reading").innerHTML = this.responseText;
};
xhttp.send("prompt=" + encodeURIComponent('Give me a one paragraph tarot reading if I pull the cards "<?php echo $pulled_cards[0]; ?>", "<?php echo $pulled_cards[1]; ?>" and "<?php echo $pulled_cards[2]; ?>".'));
</script>

<br>

<?php include '../bottom.php'; ?>

