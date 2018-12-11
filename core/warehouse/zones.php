<?php
$is_shipping=0;
$warehouse_condition = "AND warehouse_customer_id='".$user_account['warehouse_customer_id']."'  and is_shipping=0";
include $app_main_dir.'/include/shipping/zones.php';
$smarty->assign('current_main_dir', 'main');
$smarty->assign('current_section_dir', 'zones');
