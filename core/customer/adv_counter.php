<?php
cw_load('files');

if (!$addons['Salesman'])
	exit;

if ($campaign_id) {
	if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[salesman_adv_campaigns] WHERE campaign_id = '$campaign_id'"))
		db_query("INSERT INTO $tables[salesman_adv_clicks] VALUES ('$campaign_id', '".time()."')");
}

header("Content-type: image/gif");
cw_readfile($smarty->template_dir."/images/spacer.gif", true);
?>
