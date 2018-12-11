<?php
$docs_type = 'O';

if ($doc_id) 
    include $app_main_dir.'/include/orders/order.php';
else  {
    $search_Data = &cw_session_register('search_data', array());
    $search_data['orders'][$docs_type]['basic']['warehouse_customer_id'] = $user_account['warehouse_customer_id'];
    include $app_main_dir.'/include/orders/orders.php';
}

$smarty->assign('page_acl', '__12');
