<?php
include $app_main_dir.'/include/security.php';
 
if(!$addons['Salesman'])
    cw_header_location("index.php?target=error_message&error=access_denied&id=18");

if(!$eid) {
	cw_header_location("index.php?target=error_message&error=access_denied&id=19");
}

$data = cw_query_first("SELECT * FROM $tables[salesman_banners_elements] WHERE elementid='$eid'");
if(!$data)
	cw_header_location("index.php?target=error_message&error=access_denied&id=20");

if($data['data_type'] == "application/x-shockwave-flash") {
    $data['body'] = "<OBJECT class_id=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" WIDTH=\"$data[data_x]\" HEIGHT=\"$data[data_y]\"><PARAM NAME=movie VALUE=\"$current_location/banner_element.php?eid=$data[elementid]\"><PARAM NAME=quality VALUE=high><EMBED src=\"$current_location/banner_element.php?eid=$v[elementid]\" quality=high WIDTH=\"$data[data_x]\" HEIGHT=\"$data[data_y]\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED></OBJECT>";
} else {
	$data['body'] = "<img src=\"$current_location/banner_element.php?eid=$data[elementid]\" border=\"0\" width=\"$data[data_x]\" height=\"$data[data_y]\">";
}
if($action== 'js') {
    header("Content-type: text/javascript");
    echo "document.write('".$data['body']."');";
} else
	echo $data['body'];
?>
