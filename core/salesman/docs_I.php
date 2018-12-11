<?php
$docs_type = 'I';

if ($action == 'add') {
    cw_load('doc');
    $doc_id = cw_doc_create_empty($docs_type);
    cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit");
}
elseif ($doc_id)
    include $app_main_dir.'/include/orders/order.php';
else {
    $search_data = &cw_session_register('search_data', array());
    $search_data['orders'][$docs_type]['basic']['salesman_customer_id'] = $customer_id;
    include $app_main_dir.'/include/orders/orders.php';
}
