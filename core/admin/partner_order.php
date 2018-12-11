<?php
cw_load('salesman_orders', 'mail');

$order = cw_call('cw_get_salesman_order',array($doc_id));

if ($action == 'approve') {

    $smarty->assign('doc_id', $doc_id);
    $smarty->assign('order', $order);
    $customer_email = cw_query_first_cell("select email from $tables[customers] where customer_id='".$order['customer']."'");
    cw_call('cw_send_mail', array($config['Company']['site_administrator'], $customer_email, 'mail/salesman_order_customer_subj.tpl', 'mail/salesman_order_customer.tpl'));
    
    db_query("update $tables[salesman_orders] set status=1 where id='$doc_id'");
    if ($orders)
        cw_header_location("index.php?target=salesman_created_orders");
    else
        cw_header_location("index.php?target=user_modify&user=".$order['customer']."&usertype=C");
}

$smarty->assign('user', $user);
$smarty->assign('order', $order);
$smarty->assign('orders', $orders);
$smarty->assign('doc_id', $doc_id);

$location[] = array(cw_get_langvar_by_name("lbl_customer"), "index.php?target=user_modify&?user=".$order['customer']."&usertype=C");
$location[] = array(cw_get_langvar_by_name("lbl_order"), "");

$smarty->assign('main', 'salesman_order');
?>
