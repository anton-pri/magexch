<?php
global $usertype;

$usertype = seller_area_letter;

$smarty->assign('current_main_dir', 'seller');
$smarty->assign('current_section_dir', 'acc_manager');

include $app_main_dir . '/include/users/info.php';
