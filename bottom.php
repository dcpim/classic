			<div class="row">
				<p><center><font size=-1><i>The content on this site is confidential. Unauthorized use is prohibited.<br>DCPIM <?php
$results = $db->query("SELECT notes FROM projects WHERE id = 86;");
while($result = $results->fetch_assoc())
{
	echo "2." . substr_count($result['notes'], "<li>");
}
?> - <?php
$page_time_end = floor(microtime(true) * 1000);
echo number_format($page_time_end - $page_time_start);
?>ms - </i><a href="/profile"><i class="fa fa-user"></i></a>
<?php
if($public_page != 1 and $_SERVER['REQUEST_URI'] != "/a/" and $_SERVER['REQUEST_URI'] != "/")
{
	echo " <i>-</i> <i class='fa fa-lock'></i>";
}
$db->close();
?>
				</font></center></p>
			</div>
		</div>
<style>
@media print {
    a[href]::after {
        content: none !important;
    }
}
</style>
	</body>
</html>


