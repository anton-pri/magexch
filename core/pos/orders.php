<?php
$docs_type = 'G';

$aom_orders = &cw_session_register('aom_orders', array());

if ($mode == 'add') {
    cw_load('aom', 'doc');
    $prefix = cw_query_first_cell("select doc_prefix from $tables[customers_customer_info] where customer_id='$customer_id'");
    $doc = cw_aom_get_doc_storage($docs_type, array('warehouse_customer_id' => $user_account['warehouse_customer_id'], 'gd_type' => 1, 'company_id' => $user_account['company_id']), $prefix, array('pos' => array('pos_customer_id' => $customer_id)));
    $doc['pos']['gd_type'] = 1;
    $aom_orders[$doc['display_doc_id']] = $doc;
    cw_header_location("index.php?target=$target&doc_id=$doc[display_doc_id]&mode=edit");
}
else {
    if (!$doc_id || !isset($aom_orders[$doc_id]) || !$aom_orders[$doc_id]['type'])
        cw_header_location("index.php?target=$target&mode=add");
    $mode == 'edit';
    include $app_main_dir.'/include/orders/order.php';
}
