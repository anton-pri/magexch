<?php
cw_load('salesman_orders');

$order = cw_call('cw_get_salesman_order',array($doc_id));

if ($order['salesman'] != $customer_id) cw_header_location('index.php?target=salesman_created_orders');

$smarty->assign('user', $user);
$smarty->assign('order', $order);
$smarty->assign('orders', $orders);
$smarty->assign('doc_id', $doc_id);

$location[] = array(cw_get_langvar_by_name("lbl_salesman_created_orders"), "index.php?target=salesman_created_orders");
$location[] = array(cw_get_langvar_by_name("lbl_order"), "");

# Assign the current location line
$smarty->assign('main', 'salesman_order');


