<?php
$display_antibot = true;

$config['image_verification']['spambot_arrest_login_attempts'] = 3;
global $show_antibot_arr;
$show_antibot_arr = array (
							"on_send_to_friend" => $config['image_verification']['spambot_arrest_on_send_to_friend'],
							"on_contact_us" => $config['image_verification']['spambot_arrest_on_contact_us'],
							"on_registration" => $config['image_verification']['spambot_arrest_on_registration'],
							"on_login" => $config['image_verification']['spambot_arrest_on_login'],
							"on_reviews" => $config['image_verification']['spambot_arrest_on_reviews'],
						  );

$antibot_validation_val = &cw_session_register("antibot_validation_val");

// Check for GD library presence 
$gd_not_loaded = false;
if (!extension_loaded('gd') || !function_exists("gd_info")) { 
// Turn off ImageVerification addon if GD is not installed
	unset($addons['image_verification']); 
} elseif (empty($section) || $section=='contactus' || $section=="login_customer") {
	include_once $app_main_dir."/addons/image_verification/spambot_arrest_func.php";	
	$antibot_validation_val = cw_generate_codes($show_antibot_arr, $antibot_validation_val);
	$smarty->assign('show_antibot', $show_antibot_arr);
	
	$antibot_sections = array();
	foreach($show_antibot_arr as $key=>$valuee) {
		$antibot_sections[$key] = $key;
	}
	$smarty->assign('antibot_sections', $antibot_sections);
}
?>
