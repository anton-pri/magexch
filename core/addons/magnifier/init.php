<?php

cw_addons_set_template(
	array('replace', 'customer/products/thumbnail.tpl', 'addons/magnifier/popup_magnifier.tpl', 'zoomer_images_count')
);


$gd_not_loaded = false;
if (extension_loaded('gd') && function_exists("gd_info")) {
	$gd_config = gd_info();
	if (!empty($gd_config['GIF Read Support']) && !empty($gd_config['JPG Support']) && !empty($gd_config['PNG Support']))
		$gd_config['correct_version'] = true;
} 
else
	$gd_not_loaded = true;

define("NO_CHANGE_LOCATION_Z", true);

$max_image_size = 2000;

$x_tile_size = 100;
$y_tile_size = 100;

$x_thmb = 80;
$y_thmb = 65;

$x_work_area = 366-2;
$y_work_area = 281-2;

$jpg_qlt_tile = '80';
$jpg_qlt_level = '85';
$jpg_qlt_thmb = '95';

if (!function_exists("imagejpeg") || !function_exists("imagecopyresampled") || !function_exists("imageCreatetruecolor")) {
	cw_unset($addons, "magnifier");
	return;
}
?>
