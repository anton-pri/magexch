<?php
cw_load('files');

$action = preg_replace("/[^a-zA-Z0-9]/", "", $action);

$template_name = "help/hlp_".strtolower($action).".tpl";

if ($action == "TSTLBL") {
	$status = &cw_session_register("status");
	$error = &cw_session_register("error");
	if (!empty($status)) {
		$smarty->assign('status', $status);
		$status = false;
	}
	if (!empty($error)) {
		$smarty->assign('error', $error);
		$error = false;
	}
	$smarty->assign('tmp_dir', $var_dirs['tmp'].'/usps_test_labels/');
}
if (file_exists(cw_realpath($smarty->template_dir.DIRECTORY_SEPARATOR.$template_name)))
	$smarty->assign('template_name', $template_name);
