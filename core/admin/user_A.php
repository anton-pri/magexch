<?php
global $usertype;

$usertype = 'A';

include $app_main_dir . '/include/users/info.php';

$smarty->assign('page_acl', '__02');
