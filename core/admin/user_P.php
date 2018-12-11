<?php
if (!$addons['warehouse'])
    cw_header_location('index.php');

// TODO: Move to addon
$usertype = 'P';
include $app_main_dir.'/include/users/info.php';
$smarty->assign('page_acl', '__03');
