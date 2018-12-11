<?php

function cw_accounting_generate_movement($doc_data, $related_causale = 0, $status_from = '', $status_to = '') {
    global $tables, $config;

    $inventory_decreasing_statuses = cw_doc_get_inventory_decreasing_statuses();

    $ac_info = array(
        'accounting_type' => 'P',
        'info' => array(
            'doc_status_from' => $status_from,
            'doc_status_to' => $status_to,
            'avail' => 0,
            'avail_ordered' => 0,
            'avail_sold' => 0,
            'avail_reserved' => 0,
        ),
    );
    if (in_array($status_to, $inventory_decreasing_statuses) && !in_array($status_from, $inventory_decreasing_statuses))
        $ac_info['info']['avail'] = 2;
    if (in_array($status_from, $inventory_decreasing_statuses) && !in_array($status_to, $inventory_decreasing_statuses))
        $ac_info['info']['avail'] = 1;

    //cw_log_add(__FUNCTION__, array($doc_data, $status_from, $status_to, $ac_info));

    if (!$ac_info || !is_array($doc_data['products'])) return;

    if (($ac_info['info']['doc_status_from'] == '' || $ac_info['info']['doc_status_from'] == $status_from) && ($ac_info['info']['doc_status_to'] == '' || $ac_info['info']['doc_status_to'] == $status_to)) {

        $doc_id = $doc_data['doc_id'];
        foreach($doc_data['products'] as $v) {
                $destination_warehouse = ($doc_data['type'] == 'D'?$doc_data['userinfo']['customer_id']:0);

                $avails_changes = array('avail', 'avail_ordered', 'avail_sold', 'avail_reserved');
                $return = array();
                foreach($avails_changes as $avail_type) {
                    $value = $ac_info['info'][$avail_type];
                    if ($value == 3) continue;
                    $af_price = false;
                    if ($avail_type == 'avail') $af_price = true;
                    $return = cw_accounting_update_stock($doc_data, $v, $value, $avail_type, 0, $af_price, $mid, $return);
/*
                    if ($destination_warehouse && is_array($return))
                    foreach($return as $affected_pwa_id => $amount) {
                        $v['amount'] = $amount;
                        cw_accounting_update_stock($doc_data, $v, $value, $avail_type, $destination_warehouse, false, 0, $affected_pwa_id);
                    }
*/
                }
        }
    }

}

function cw_accounting_create_initial_amount($warehouse_id, $product_id, $variant_id, $avail, $field = 'avail') {
    global $tables;

    $pwa_id = cw_query_first_cell("select pwa_id from $tables[products_supplied_amount] where product_id='$product_id' and warehouse_customer_id='$warehouse_id' and variant_id='$variant_id' and is_init=1");
    if ($pwa_id)
        db_query("update $tables[products_supplied_amount] set $field=$field+$avail where pwa_id='$pwa_id'");
    else {
        $record = array(
            'is_auto_calc' => 0,
            'product_id' => $product_id,
            'warehouse_customer_id' => $warehouse_id,
            $field => $avail,
            'variant_id' => $variant_id,
            'date' => cw_core_get_time(),
            'is_init' => 1,
            'is_hide' => 1,
        );
# kornev, is_init - it's not display it in some area, but include this amount in calculations
        $pwa_id = cw_array2insert('products_supplied_amount', $record);
    }

    cw_warehouse_check_avail_record($warehouse_id, $product_id, $variant_id);
}

function cw_accounting_update_stock($doc_data, $product, $way, $field, $destination_warehouse_id = 0, $affect_prices = false, $movements_id = 0, $source_pwa_id = 0) {
    global $addons, $tables;

    cw_load('product');

# kornev
# way = 1, the products has been purchased - we have to create new record with supplier
#          ps: the record can be already created... if a few movements have to be generated
# way = 2, the products has been sold - we have to decrease the most old records

# kornev
# if we are making the warehouse movements, we have to increase the products in one and decrease in another and visa versa
    $warehouse_customer_id = $doc_data['info']['warehouse_customer_id'];
    if ($destination_warehouse_id) {
        $warehouse_customer_id = $destination_warehouse_id;
        $way = $way == 2?1:2;
    }

    $variant_id = 0;
# kornev, TOFIX
    if ($addons['product_options'] && (!empty($product['extra_data']['product_options']) || !empty($product['options']))) {
        $options = (!empty($product['extra_data']['product_options'])?$product['extra_data']['product_options']:$product['options']);
        $variant_id = cw_get_variant_id($options);
    }

    $return = array();
    if ($way == 1) {
        $return = $product['amount'];
    }
    elseif ($way == 2) {
        $return = -$product['amount'];

        if ($field == 'avail' && in_array($doc_data['type'], array('O', 'I', 'G', 'S'))) {
            cw_call_delayed('cw_product_run_counter', array('product_id' => $product['product_id'], 'count' => $return, 'type' => 1));
	    }
    }

//    cw_warehouse_check_avail_record($warehouse_customer_id, $product['product_id'], $variant_id);
    cw_event('on_accounting_pre_update_stock', array($product, $variant_id, $field, &$return));

    if ($return) {

        cw_accounting_update_stock_db($product, $variant_id, $field, $return); 

        cw_event('on_accounting_update_stock', array($product, $variant_id, $field, $return));
    }
    cw_warehouse_recalculate($product['product_id'], $variant_id);

    cw_func_call('cw_product_build_flat', array('product_id' => $product['product_id']));

    return $return;
}

function cw_accounting_update_stock_db($product, $variant_id, $field, $change) {
    global $tables; 
    db_query($s = "update $tables[products_warehouses_amount] set $field = $field + $change where product_id='$product[product_id]' and warehouse_customer_id=0 and variant_id='$variant_id'");
}

function cw_accounting_current_stock($product, $variant_id, $field) {
    global $tables;
    return cw_query_first_cell("SELECT $field FROM $tables[products_warehouses_amount] WHERE product_id='$product[product_id]' and warehouse_customer_id=0 and variant_id='$variant_id'");
}
