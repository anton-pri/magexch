<?php
global $docs_type;
$docs_type = 'O';
cw_load('doc');

$search_data = &cw_session_register('search_data', array());

$search_data['orders'][$docs_type]['warehouse_area'] = $customer_id;

if ($doc_id) {
    $doc_data = cw_call('cw_doc_get', array($request_prepared['doc_id'], 0)); 
    if ($customer_id != $doc_data['info']['warehouse_customer_id']) 
        cw_header_location("index.php?target=error_message&error=access_denied&id=40");
    cw_include('include/orders/order.php');
}
else 
    cw_include('include/orders/orders.php');

$smarty->assign('page_acl', '__18');

$smarty->assign('current_section_dir', 'orders');
?>
