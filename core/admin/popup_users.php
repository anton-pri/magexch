<?php
include $app_main_dir.'/include/users/popup_users.php';

$smarty->assign('home_style', 'popup');

$smarty->assign('current_main_dir', 'admin');
$smarty->assign('current_section_dir', 'users');
$smarty->assign('main', 'popup_user');
