<?php
include $app_main_dir.'/include/security.php';

if (!$addons['Salesman'])
    cw_header_location('index.php');

$usertype = 'B';
include $app_main_dir.'/include/users/info.php';
$smarty->assign('page_acl', '__0100');

$smarty->assign('current_section_dir', 'users');
?>
