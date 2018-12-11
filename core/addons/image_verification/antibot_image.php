<?php
$antibot_validation_val = &cw_session_register("antibot_validation_val");
if ($regenerate == "Y" || empty($antibot_validation_val[$section]['code'])) {
	include_once $app_main_dir."/addons/image_verification/" .$config['image_verification']['spambot_arrest_str_generator'].".php";
	$image_length = $config['image_verification']['spambot_arrest_image_length'];
	$antibot_validation_val[$section]['code'] = cw_antibot_str_generator($image_length);
	$antibot_validation_val[$section]['used'] = "N";
}
cw_session_save();

$generation_str = $antibot_validation_val[$section]['code'];
include_once $app_main_dir."/addons/image_verification/img_generators/".$config['image_verification']['spambot_arrest_img_generator']."/".$config['image_verification']['spambot_arrest_img_generator'].".php";
?>
