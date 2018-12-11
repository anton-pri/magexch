<?php
# kornev, TOFIX
if (!$addons['Salesman'])
    cw_header_location('index.php');

include $app_main_dir.'/addons/Salesman/banner_stats.php';

$smarty->assign('main', 'banner_info');
