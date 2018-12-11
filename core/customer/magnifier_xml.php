<?php
define('OFFERS_DONT_SHOW_NEW', 1);
define("BENCH_BLOCK", true);

if (empty($addons['magnifier']))
	exit;

if (!empty($imageid)) {
	$product_id = cw_query_first_cell("SELECT id FROM $tables[magnifier_images] WHERE imageid=".$imageid);
    $select_cond = " imageid='".$imageid."'";
}
include_once $app_main_dir.'/addons/magnifier/product_magnifier.php';

if (empty($zoomer_images) || !is_array($zoomer_images)) {
	echo "";
	return;
}

$xmlContent = "<list>";
foreach ($zoomer_images as $key=>$image) {
	$xmlContent .= "<image ipath=\"".$app_web_dir."/images/magnifier_images/".$product_id."/".$image['imageid']."/\" />";
}
$xmlContent .= "</list>";

$xmlContent .= "<hints><magnifier hint=\"".cw_get_langvar_by_name("lbl_zoomer_hint_magnifier",NULL,false,true)." \"/></hints>";
$work_area_bg = ( preg_match("/^#?[\da-f]{6}$/i", trim($config['magnifier']['magnifier_background'])) ? str_replace("#", "", trim($config['magnifier']['magnifier_background'])) : "999999" ); 
$xmlContent .= "<options><background bg=\"".$work_area_bg."\"/></options>";

echo $xmlContent;
die;
?>
