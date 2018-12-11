<?php
$_GET['user'] = $user = $customer_id;
$usertype = 'A';
$smarty->assign('current_section_dir', 'users');
include $app_main_dir.'/include/users/info.php';
$smarty->assign('page_acl', '__02');
