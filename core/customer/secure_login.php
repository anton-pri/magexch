<?php
$location[] = array(cw_get_langvar_by_name("lbl_authentication"), "");

$login_antibot_on = &cw_session_register("login_antibot_on");
$username = &cw_session_register("username");
if ($addons['manufacturers'])
	include $app_main_dir."/addons/manufacturers/customer_manufacturers.php";

if ($login_antibot_on)
	$smarty->assign('login_antibot_on', $login_antibot_on);

$smarty->assign('username', $username);
$smarty->assign('main', 'secure_login_form');
?>
