<?php
cw_load( 'user');

$location[] = array(cw_get_langvar_by_name("lbl_file_management"), "");

$root_dir = cw_user_get_files_location();
$what_to_edit = "files";
$action_script = "index.php?target=file_manage";

$smarty->assign('what_to_edit', $what_to_edit);
$smarty->assign('action_script', $action_script);

include $app_main_dir.'/include/image/file.php';
