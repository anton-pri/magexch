<?php
if (!defined('APP_START')) die('Access denied');

# GD extensions array
$gd_req_extensions = array (
								"GIF(".cw_get_langvar_by_name("lbl_gif_read_support").")" => "GIF Read Support",
								"GIF(".cw_get_langvar_by_name("lbl_gif_create_support").")" => "GIF Create Support",
								"JPG" => "JPG Support",
								"PNG" => "PNG Support"
						);
$spambot_requirements = "";
# Check for GD library presence
if (extension_loaded('gd')) {   # If GD loaded
	if (function_exists("gd_info")) { # If gd_info function exists
		$gd_config = gd_info();
		foreach ($gd_req_extensions as $ext=>$conf_name) {
			if (empty($gd_config[$conf_name])) {
				if (empty($spambot_requirements)) {
					$spambot_requirements = cw_get_langvar_by_name("lbl_gd_ext_missing") . $ext;
				} else {
					$spambot_requirements .= ", $ext";
				}
			}

		}
		if (!empty($spambot_requirements)) {
			$spambot_requirements .= ". <br />".cw_get_langvar_by_name("lbl_module_incorrect_work");
		}
	} else {
		$spambot_requirements = cw_get_langvar_by_name("lbl_gd_info_missing");
	}
} else {
	$spambot_requirements = cw_get_langvar_by_name("lbl_gd_lib_missing");
}
$smarty->assign('spambot_requirements', $spambot_requirements);
?>
