<?php
cw_load('salesman_orders');

$smarty->assign('salesman_orders', cw_get_salesman_pending_orders($customer_id));

$smarty->assign('main', 'salesman_created_orders');
$location[] = array(cw_get_langvar_by_name("lbl_salesman_created_orders"), "");


