<?php
if (!$eid || !$addons['Salesman'])
	exit;

$data = cw_query_first("SELECT * FROM $tables[salesman_banners_elements] WHERE elementid='$eid'");
if(!$data)
	exit;

header("Content-type: ".$data['data_type']);
echo $data['data'];
die();
?>
