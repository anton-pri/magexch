<?php
$slg_response = &cw_session_register("slg_response");

if(empty($slg_response) || empty($doc_id) || @$slg_response['doc_id'] != $doc_id)
	exit;

if(!empty($slg_response['image'])) {
	header("Content-type: ".$slg_response['image_type']);
	header("Content-Disposition: attachment; filename=label.".substr(strstr($slg_response['image_type'],"/"),1));
	header("Content-Length: ".strlen($slg_response['image']));
	echo $slg_response['image'];
} elseif(!empty($slg_response['csv'])) {
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=data.csv");
	header("Content-Length: ".strlen($slg_response['csv']));
	echo $slg_response['csv'];
}
exit;
?>
