<?php
$docs_type = 'S';

$search_data = &cw_session_register('search_data', array());
$search_data['orders'][$docs_type]['basic']['customer_id'] = $customer_id;
$smarty->assign('current_section_dir', 'doc');

if ($doc_id) 
    cw_include('include/orders/order.php');
else
    cw_include('include/orders/orders.php');
