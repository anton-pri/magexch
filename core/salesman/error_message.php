<?php
include $app_main_dir."/include/error_message.php";

$login_antibot_on = &cw_session_register("login_antibot_on");
$antibot_err = &cw_session_register("antibot_err");
$username = &cw_session_register("username");
$smarty->assign('username', $username);
$smarty->assign('login_antibot_on', $login_antibot_on);
if ($antibot_err) {
	$smarty->assign('antibot_err', $antibot_err);
	$antibot_err = false;
}
?>
