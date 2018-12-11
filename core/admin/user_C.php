<?php
global $usertype;

$usertype = 'C';

include $app_main_dir . '/include/users/info.php';

$smarty->assign('page_acl', '__0801');
