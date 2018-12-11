<?php
$location[] = array(cw_get_langvar_by_name("lbl_authentication"), "");
$smarty->assign('main', 'secure_login_form');

$login_antibot_on = cw_session_register("login_antibot_on");
$antibot_err = cw_session_register("antibot_err");
$username = cw_session_register("username");
$smarty->assign('username', $username);
$smarty->assign('login_antibot_on', $login_antibot_on);
if ($antibot_err) {
	$smarty->assign('antibot_err', $antibot_err);
	$antibot_err = false;
}
?>
