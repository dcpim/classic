<?php $public_page = 1; include '../top.php'; ?>

<?php exec("curl https://" . $CONFIG['SERVER_HOST'] . "/pipelines/bucket_policies.py > /dev/null 2>&1 &"); ?>

<meta http-equiv='refresh' content='1; URL=/' />

<?php include '../bottom.php'; ?>

