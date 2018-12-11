<?php
$docs_type = 'I';

if ($doc_id) 
    include $app_main_dir.'/include/orders/order.php';
else  {
    $search_data = cw_session_register('search_data', array());
    $search_data['orders'][$docs_type]['basic']['warehouse_customer_id'] = $user_account['warehouse_customer_id'];
    include $app_main_dir.'/include/orders/orders.php';
}

$smarty->assign('page_acl', '__13');
