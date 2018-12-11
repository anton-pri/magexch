<?php
global $is_shipping;
global $zones_condition;
$is_shipping=0;
$zones_condition = " and is_shipping=0";
cw_include('include/zones/zones.php');
$smarty->assign('current_main_dir', 'main');
$smarty->assign('current_section_dir', 'zones');
