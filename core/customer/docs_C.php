<?php
$docs_type = 'C';

$search_data = &cw_session_register('search_data', array());
$search_data['orders'][$docs_type]['basic']['customer_id'] = $customer_id;
$smarty->assign('current_section_dir', 'doc');

if ($doc_id) 
    include $app_main_dir.'/include/orders/order.php';
else
    include $app_main_dir.'/include/orders/orders.php';
