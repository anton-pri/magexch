<?php
if ($mode == 'zones') {
    $warehouse_condition = " and is_shipping=1";
    $is_shipping=1;
    include $app_main_dir.'/include/shipping/zones.php';
    $smarty->assign('current_main_dir', 'main');
    $smarty->assign('current_section_dir', 'zones');
}
else {
    $warehouse_condition = "and warehouse_customer_id='".$user_account['warehouse_customer_id']."'";
    include $app_main_dir.'/include/shipping/rates.php';
}

$smarty->assign('mode', $mode);
