<?php
if (!defined('APP_START')) die('Access denied');

# Generates codes(symbols) for each ACTIVE location 
function cw_generate_codes($pages, $codes = array()) {
	global $config, $app_main_dir;

        if (empty($codes))
            $codes = array();

	include_once $app_main_dir."/addons/image_verification/" .$config['image_verification']['spambot_arrest_str_generator'].".php";
	
	$image_length = $config['image_verification']['spambot_arrest_image_length'];
	foreach ($pages as $page => $value) {
		if ($value == 'Y'  &&  (!isset($codes[$page]['used']) || $codes[$page]['used'] != "N")) {
			$codes[$page]['code'] = cw_antibot_str_generator($image_length);
			$codes[$page]['used'] = "N";
		}
	}

	return $codes;
}

# Validates code from image
function cw_validate_image(&$image_str, $input_str) {
	if (!empty($input_str))
		$image_str['used'] = "Y";

	return ($image_str['code'] != $input_str);
}

?>
