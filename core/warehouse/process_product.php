<?php
if ($REQUEST_METHOD == "POST" && $action == 'update' && is_array($posted_data)) {
    $insert = array();
    $insert['warehouse_customer_id'] = $customer_id;
    $insert['variant_id'] = 0;

    foreach ($posted_data as $k=>$v) {
        $insert['avail'] = $v['avail'];
        $insert['product_id'] = $k;
        cw_load('warehouse');
        cw_warehouse_insert_avail($insert, true);
    }
    $top_message['content'] = cw_get_langvar_by_name("msg_adm_products_upd");
    $top_message['type'] = "I";
}

cw_header_location("index.php?target=search&mode=search".(intval($navpage)>1 ? "&page=$navpage" : ""));
