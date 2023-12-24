<?php include '../top.php'; ?>

<h3><a href="/" title="Back home"><i class="fa fa-file-text"></i></a> Log entries</h3>

<h4>Event logs</h4>

<div class="row">
	<div class="col-md-2">
		<form action="./">
		<input type="hidden" name="filter" value="1">
		<input class="form-control btn btn-primary" type="submit" value="Last errors">
		</form>
	</div>
	<div class="col-md-2">
		<form action="./">
		<input type="hidden" name="filter" value="2">
		<input class="form-control btn btn-primary" type="submit" value="SQL deletions">
		</form>
	</div>
    <div class="col-md-2">
        <form action="./">
        <input type="hidden" name="filter" value="3">
        <input class="form-control btn btn-primary" type="submit" value="AWS commands">
        </form>
    </div>
    <div class="col-md-2">
        <form action="./">
        <input type="hidden" name="filter" value="4">
        <input class="form-control btn btn-primary" type="submit" value="Project tasks">
        </form>
    </div>
	<div class="col-md-2">
		<form action="./">
		<input class="form-control btn btn-primary" type="submit" value="All log entries">
		</form>
	</div>
</div>

<?php
tabletop("logs", "<tr><th>Time</th><th>Source</th><th>Message</th></tr>");
if($_GET['filter'] == 1) { $results = $db->query("SELECT * FROM log WHERE event LIKE '%error%' ORDER BY id DESC LIMIT 1000;"); }
else if($_GET['filter'] == 2) { $results = $db->query("SELECT * FROM log WHERE result LIKE 'DELETE %' ORDER BY id DESC LIMIT 1000;"); }
else if($_GET['filter'] == 3) { $results = $db->query("SELECT * FROM log WHERE result LIKE 'aws %' ORDER BY id DESC LIMIT 1000;"); }
else if($_GET['filter'] == 4) { $results = $db->query("SELECT * FROM log WHERE event LIKE '%task%' ORDER BY id DESC LIMIT 1000;"); }
else { $results = $db->query("SELECT * FROM log ORDER BY id DESC LIMIT 1000;"); }
while($result = $results->fetch_assoc())
{
	echo "<tr><td>" . $result['date'] . "</td><td>" . $result['event'] . "<br>" . $result['ip'] . "</td><td>" . $result['result'] . "</td></tr>\n";
}
tablebottom("logs", "0", "desc");
?>

<h4>System logs</h4>

<?php
tabletop("syslog", "<tr><th>Time</th><th>Process</th><th>Message</th></tr>");
$results = $db->query("SELECT * FROM syslog ORDER BY date DESC LIMIT 1000;");
while($result = $results->fetch_assoc())
{
	echo "<tr><td>" . $result['date'] . "</td><td>" . $result['process'] . "</td><td>" . $result['message'] . "</td></tr>\n";
}
tablebottom("syslog", "0", "desc");
?>

<h3>Intrusion detection</h3>

<h4>Recent suspicious web queries</h4>

<?php
tabletop("wwwlogs", "<tr><th>Time</th><th>Query</th><th>IP address</th><th>Organization</th><th>Country</th></tr>");
$results = $db->query("SELECT * FROM wwwlogs WHERE code != 301 AND code != 418 ORDER BY date DESC LIMIT 1000;");
while($result = $results->fetch_assoc())
{
	echo "<tr><td>" . $result['date'] . "</td><td>[" . $result['code'] . "] " . $result['url'] . "</td><td>" . $result['ip'] . "</td><td>" . $result['orgname'] . "</td><td>" . $result['country'] . "</td></tr>\n";
}
tablebottom("wwwlogs", "0", "desc");
?>

<h4>Recent suspicious S3 actions</h4>

<?php
tabletop("s3logs", "<tr><th>Time</th><th>Action</th><th>Object</th><th>IP address</th><th>Organization</th><th>Country</th></tr>");
$results = $db->query("SELECT * FROM s3_logs WHERE code > 299 AND object != \"-\" AND orgname != \"CLOUDFLARENET\" AND orgname != \"AKAMAI\" AND orgname != \"FASTLY\" ORDER BY date DESC LIMIT 1000;");
while($result = $results->fetch_assoc())
{
	echo "<tr><td>" . $result['date'] . "</td><td>" . $result['query'] . "</td><td>[" . $result['code'] . "] s3://" . $result['bucket'] . "/" . $result['object'] . "</td><td>" . $result['ip'] . "</td><td>" . $result['orgname'] . "</td><td>" . $result['country'] . "</td></tr>\n";
}
tablebottom("s3logs", "0", "desc");
?>

<h4>Nearby access points</h4>

<?php
tabletop("ssid", "<tr><th>MAC Address</th><th>Ch</th><th>Freq</th><th>Encryption</th><th>Str</th><th>Name</th><th>Vendor</th><th>First seen</th><th>Last seen</th></tr>");
$results = $db->query("SELECT * FROM wlan_ssid;");
while($result = $results->fetch_assoc())
{
    echo "<tr><td>" . $result['mac'] . "</td><td>" . $result['channel'] . "</td><td>" . $result['frequency'] . "</td><td>" . $result['encryption'] . "</td><td>" . $result['quality'] . "%</td><td>" . $result['name'] . "</td><td>" . $result['vendor'] . "</td><td>" . $result['first_seen'] . "</td><td>" . $result['last_seen'] . "</td></tr>\n";
}
tablebottom("ssid", "8", "desc");
?>

<h3>Storage analysis</h3>

<h4>Database tables</h4>

<?php
tabletop("tables", "<tr><th>Table name</th><th>Rows</th><th>Size</th></tr>");
$results = $db->query("SELECT TABLE_NAME, TABLE_ROWS, DATA_LENGTH+INDEX_LENGTH AS SIZE FROM information_schema.TABLES WHERE TABLE_SCHEMA = \"" . $_SERVER['DB_DATABASE'] . "\";");
$tot_rows = 0;
$tot_size = 0;
while($result = $results->fetch_assoc())
{
	$tot_rows += $result['TABLE_ROWS'];
	$tot_size += $result['SIZE'];
	echo "<tr><td>" . $result['TABLE_NAME'] . "</td><td>" . number_format($result['TABLE_ROWS']) . "</td><td data-sort='" . $result['SIZE'] . "'>" . size_format($result['SIZE']) . "</td></tr>\n";
}
?>
	</tbody><tfoot>
		<tr><th>Total</th><th><?php echo number_format($tot_rows); ?></th><th><?php echo size_format($tot_size); ?></th></tr>
	</tfoot>
</table>
<script>$(document).ready(function(){$('#tables').DataTable({'oSearch':{'sSearch':search},'aLengthMenu':[10, 25, 50, 100, 500],'order':[[1,'desc']]});});</script>

<h4>S3 buckets</h4>

<?php
tabletop("lens", "<tr><th>Bucket name</th><th>Total size</th><th>Object count</th><th>Old versions</th><th>Deleted objects</th></tr>");
$results = $db->query("SELECT * from s3_storage ORDER BY date DESC;");
$curdate = "";
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
while($result = $results->fetch_assoc())
{
	if($curdate != "" and $curdate != $result['date']) { break; }
	echo "<tr><td>" . $result['bucket'] . "</td><td data-sort='" . $result['StorageBytes'] . "'>" . size_format($result['StorageBytes']) . "</td><td>" . number_format($result['ObjectCount']) . "</td><td>" . number_format($result['NonCurrentVersionObjectCount']) . "</td><td>" . number_format($result['DeleteMarkerObjectCount']) . "</td></tr>\n";
	$curdate = $result['date'];
	$tot1 += $result['StorageBytes'];
	$tot2 += $result['ObjectCount'];
	$tot3 += $result['NonCurrentVersionObjectCount'];
	$tot4 += $result['DeleteMarkerObjectCount'];
}
?>
	</tbody><tfoot>
		<tr><th>Total</th><th><?php echo size_format($tot1); ?></th><th><?php echo number_format($tot2); ?></th><th><?php echo number_format($tot3); ?></th><th><?php echo number_format($tot4); ?></th></tr>
	</tfoot>
</table>
<script>$(document).ready(function(){$('#lens').DataTable({'oSearch':{'sSearch':search},'aLengthMenu':[10, 25, 50, 100, 500],'order':[[1,'desc']]});});</script>

<br>
<?php include '../bottom.php'; ?>
