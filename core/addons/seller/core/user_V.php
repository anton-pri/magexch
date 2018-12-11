<?php
global $usertype, $app_main_dir;

$usertype = seller_area_letter;

include $app_main_dir . '/include/users/info.php';

$smarty->assign('page_acl', '__02');
