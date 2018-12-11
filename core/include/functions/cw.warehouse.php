<?php
function cw_warehouse_get_avail($product_id, $variant_id = 0, $type = 'avail') {
    global $tables;

//    $ret = cw_query_first_cell($sql="select sum($type) from $tables[products_warehouses_amount] where product_id='$product_id' and variant_id='$variant_id' and warehouse_customer_id!=0");
    $ret = cw_query_first_cell($sql="select sum($type) from $tables[products_warehouses_amount] where product_id='$product_id' and variant_id='$variant_id' and warehouse_customer_id=0");

    if (!$ret) $ret = 0;
    return $ret;
}

function cw_warehouse_get_sum_avail($product_id) {
    global $tables;

    $ret = cw_query_first_cell("select sum(avail) from $tables[products_warehouses_amount] where product_id='$product_id' and warehouse_customer_id=0");
    if (!$ret) $ret = 0;
    return $ret;
}

function cw_warehouse_get_max_amount_warehouse($product_id, $variant_id = 0) {
    global $tables, $customer_id;
    if ($customer_id && cw_warehouse_is_warehouse_assigned($customer_id)) {
        return cw_query_first_cell("select pa.warehouse_customer_id from $tables[products_warehouses_amount] as pa, $tables[customers_warehouses] as cp where pa.product_id='$product_id' and pa.variant_id='$variant_id' and cp.customer_id = '$customer_id' and pa.warehouse_customer_id=cp.division_id order by avail desc limit 1");
    }
    return cw_query_first_cell("select warehouse_customer_id from $tables[products_warehouses_amount] where product_id='$product_id' and variant_id='$variant_id' order by avail desc limit 1");
}

function cw_warehouse_get_warehouse_avail($warehouse_customer_id, $product_id, $field = 'avail', $variant_id = 0) {
    global $tables;

    if (!$field) $field = 'avail';
    $ret = cw_query_first_cell($sql="select $field from $tables[products_warehouses_amount] where warehouse_customer_id='$customer_id' and product_id='$product_id' and variant_id='$variant_id'");

    if (!$ret) $ret = 0;
    return $ret;
}

function cw_warehouse_get_avails($product_id) {
    global $tables, $addons;

//    $ret = cw_query("select pa.* from $tables[products_warehouses_amount] as pa, $tables[customers] as c where c.customer_id=pa.warehouse_customer_id and pa.product_id='$product_id' and pa.avail > 0 order by pa.warehouse_customer_id, pa.variant_id");
    $ret = cw_query($sql="select pa.*, wd.title as warehouse_title from $tables[products_warehouses_amount] as pa, $tables[warehouse_divisions] as wd where wd.division_id=pa.warehouse_customer_id and pa.product_id='$product_id' order by pa.warehouse_customer_id, pa.variant_id");
# kornev, TOFIX
    if (is_array($ret) && $addons['product_options']) {
        $tmp = cw_get_hash_options($product_id);
        foreach($ret as $k=>$value) {
            if ($value['variant_id']) {
                $options = cw_query("select option_id from $tables[product_variant_items] where variant_id='$value[variant_id]'");
                if (is_array($options))
                foreach($options as $opt)
                    $ret[$k]['options'][] = $tmp[$opt['option_id']];
            }
        }
    }
    return $ret;
}

function cw_warehouse_is_warehouse_assigned($customer_id) {
    global $tables, $cached_data;

    $count = cw_query_first_cell("select count(*) from $tables[customers_warehouses] where customer_id='$customer_id'");
    return (bool) $count;
}

function cw_warehouse_get_enabled_for_customer($customer_id) {
    global $tables;

    static $cached;
    if (!isset($cached)) {
        $is_assigned = cw_query_column("select division_id from $tables[customers_warehouses] where customer_id='$customer_id'");
        $is_enabled = cw_query_column("select division_id from $tables[warehouse_divisions] where enabled=1");
        if ($is_assigned) $cached = array_intersect($is_assigned, $is_enabled);
        else $cached = $is_enabled;
    }
    return $cached;
}

function cw_warehouse_get_avail_for_customer($product_id, $variant_id) {
    global $tables, $customer_id;

    $warehouses = cw_warehouse_get_enabled_for_customer($customer_id);
    if (!$warehouses) return 0;
    return cw_query_first_cell("select sum(avail+avail_ordered) from $tables[products_warehouses_amount] as pa where pa.warehouse_customer_id=0 and pa.product_id='$product_id' and pa.variant_id='$variant_id'");
}

function cw_warehouse_get_avails_customer($product_id, $avail = 0) {
    global $tables, $customer_id, $cart, $addons;

    if (!$addons['warehouse']) {
        return array(
            '' => array('avail' => array('0' => $avail)),
        );
    }
   
    $available_warehouses = cw_warehouse_get_enabled_for_customer($customer_id);
   
    $return = array();
    if ($available_warehouses) 
        foreach($available_warehouses as $division_id) {
            $data = cw_query("select variant_id, avail, (backorder & 1) as unlimited_products from $tables[warehouse_divisions] as wd left join $tables[products_warehouses_amount] as pwa on pwa.warehouse_customer_id=wd.division_id and product_id='$product_id' where wd.division_id='$division_id'");
            if ($data) {
                $return[$division_id]['settings']['unlimited_products'] = $data['0']['unlimited_products'];
                $return[$division_id]['avail'][0] = 0; # default amount
                foreach($data as $val) {
                    $return[$division_id]['avail'][$val['variant_id']] = $val['avail'];
# kornev, in cart feature
                    $in_cart = 0;
                    if ($cart['products'])
                    foreach ($cart['products'] as $cart_item) {
                        if ($cart_item['product_id'] == $product_id && $cart_item['variant_id'] == $val['variant_id'] && $cart_item['warehouse_customer_id'] == $division_id)
                            $in_cart += $cart_item['amount'];
                    }
                    $return[$division_id]['avail'][$val['variant_id']] -= $in_cart;
                }
            }
        }
    return $return;
}

function cw_get_warehouses() {
    return cw_user_get_short_list('P');
}

function cw_warehouse_group_products($products) {
    $ret = array();
    if (is_array($products))
        foreach($products as $k=>$product) {
            $ret[$product['warehouse']][] = $product;
        }
    return $ret;
}

function cw_warehouse_group_orders($orders) {
    $ret = array();
    if (is_array($orders))
        foreach($orders as $k=>$order) {
            $ret[$order['warehouse']] = $order;
        }
    return $ret;
}

function cw_warehouse_is_customer($customer_id, $division_id) {
    global $tables, $addons;

    if (!$customer_id || !$addons['warehouse']) return true;
    $counter = cw_query_first_cell("select count(*) from $tables[customers_warehouses] where customer_id='$customer_id'");    
    if (!$counter) return true;
    return cw_query_first_cell("select count(*) from $tables[customers_warehouses] where customer_id='$customer_id' and division_id='$division_id'");
}

function cw_warehouse_get_warehouses($customer_id) {
    global $tables;

    cw_load('user');
    $counter = cw_query_first_cell("select count(*) from $tables[customers_warehouses] where customer_id='$customer_id'");    
    if (!$counter) $divisions = cw_query("select * from $tables[warehouse_divisions] as d");
    else $divisions = cw_query("select c.customer_id from $tables[warehouse_divisions] as wd, $tables[customers_warehouses] as cp where wd.division_id = cp.division_id and cp.customer_id='$customer_id'");

    if ($divisions)
    foreach($divisions as $k=>$division) {
        $divisions[$k]['address'] =  cw_query_first("select * from $tables[customers_addresses] where address_id='$division[address_id]'");
        $divisions[$k]['address']['country_name'] = cw_get_country($divisions[$k]['address']['country']);
        $divisions[$k]['address']['state_name'] = cw_get_state($divisions[$k]['address']['country'], $divisions[$k]['address']['state']);
    }

    return $divisions;
}

function cw_warehouse_insert_avail($insert) {
    global $tables;

# kornev, old code here - we won't use it

    $insert['warehouse_customer_id'] = 0;
    cw_array2insert('products_warehouses_amount', $insert, 1);
/*
    cw_load('serials', 'accounting');

# kornev, we have to insert it to the supplied amount since the warehouse amount can be recalculated.
    $amount_now = cw_warehouse_get_warehouse_avail($insert['warehouse_customer_id'], $insert['product_id'], 'avail', $insert['variant_id']);
    $diff = $insert['avail'] - $amount_now;
    cw_accounting_create_initial_amount($insert['warehouse_customer_id'], $insert['product_id'], $insert['variant_id'], $diff);

/*
    if (!cw_query_first_cell("select count(*) from $tables[products_warehouses_amount] where product_id='$insert[product_id]' and warehouse_customer_id='$insert[warehouse_customer_id]' and variant_id='$insert[variant_id]'"))
        cw_array2insert('products_warehouses_amount', $insert);
    cw_array2update('products_warehouses_amount', $insert, "product_id='$insert[product_id]' and warehouse_customer_id='$insert[warehouse_customer_id]' and variant_id='$insert[variant_id]'", array('avail'));
# kornev, looking for inserted amount 
    cw_warehouse_recalculate($insert['product_id'], $insert['variant_id']);
*/

//    cw_serials_check_product($insert['product_id']);
}

function cw_warehouse_recalculate($product_id) {

}

function cw_warehouse_get_title($division_id) {
    global $tables;

    return cw_query_first_cell("select title from $tables[warehouse_divisions] where division_id='$division_id'");
}

function cw_warehouse_get_label($customer_id) {
    global $tables;

    $data = cw_query_first("select wd.title, ca.firstname, ca.lastname from $tables[customers_customer_info] as ci left join $tables[customers_addresses] as ca on ca.customer_id=ci.customer_id and ca.main=1 left join $tables[warehouse_divisions] as wd on wd.division_id=ci.division_id where ca.customer_id=ci.customer_id and ci.customer_id='$customer_id'");
    return "$data[title] / $data[firstname] $data[lastname]";
}

function cw_warehouse_add_to_cart_simple($product_id, $amount, $product_options, $price, $order_id= 0) {
    global $cart;

# kornev, if warehouse is not specified, use the first N warehouses, while required amouint is not added
    $possible_warehouses = cw_warehouse_get_avails_customer($product_id);
    $amount = abs(intval($amount));
    foreach($possible_warehouses as $warehouse=>$tmp) {
        $add_product = array();
        $add_product['product_id'] = abs(intval($product_id));
        $add_product['amount'] = abs(intval($amount));
        $add_product['product_options'] = $product_options;
        $add_product['price'] = abs(doubleval($price));
        $add_product['warehouse_customer_id'] = $warehouse;
        $add_product['salesman_doc_id'] = abs(doubleval($order_id));
        $result = cw_call('cw_add_to_cart', array(&$cart, $add_product));
        $amount = $amount - $result['added_amount'];
        if ($amount <= 0) break;
    }

    return $result;
}

function cw_warehouse_get_info($warehouse) {
    global $tables;

    $info['warehouse_info'] = cw_query_first("select * from $tables[customers] where customer_id='$warehouse'");
    $info['warehouse_info']['country_name'] = cw_get_country($info['warehouse_info']['b_country']);
    $info['warehouse_info']['state_name'] = cw_get_state($info['warehouse_info']['b_country'], $info['warehouse_info']['b_state']);

    return $info;
}

function cw_warehouse_check_avail_record($warehouse, $product_id, $variant = 0) {
    global $tables;
    
    $count = cw_query_first_cell($sql="select count(*) from $tables[products_warehouses_amount] where product_id='$product_id' and variant_id='$variant' and warehouse_customer_id='$warehouse'");
    if (!$count) {
        $ins = array(
            'product_id' => $product_id,
            'warehouse_customer_id' => $warehouse,
            'avail' => 0,
            'variant_id' => $variant,
        );
        cw_array2insert('products_warehouses_amount', $ins);
    }

/*
    $to_update = array();
    $types = array('avail', 'avail_ordered', 'avail_sold', 'avail_reserved');
    foreach($types as $field)
        $to_update[$field] = cw_query_first_cell("select sum($field) from $tables[products_supplied_amount] where product_id='$product_id' and variant_id='$variant_id' and warehouse_customer_id='$warehouse'");

    cw_array2update('products_warehouses_amount', $to_update, "product_id='$product_id' and variant_id='$variant' and warehouse_customer_id='$warehouse'");
    cw_warehouse_recalculate($product_id, $variant);
*/
}

function cw_warehouse_products_delete_amount($pwa_id) {
    global $tables;

    $info = cw_query_first("select warehouse_customer_id, variant_id, product_id from $tables[products_supplied_amount] where pwa_id='$pwa_id'");
    db_query("delete from $tables[products_supplied_amount] where pwa_id='$pwa_id'");

    cw_warehouse_check_avail_record($info['warehouse_customer_id'], $info['product_id'], $info['variant']);
}

function cw_warehouse_get_list_smarty() {
    return cw_user_get_short_list('P');
}   

function cw_warehouse_get_divisions() {
    global $tables;
    $divisions = cw_query("select * from $tables[warehouse_divisions]");
    if ($divisions)
    foreach($divisions as $k=>$division)
        $divisions[$k]['address'] =  cw_query_first("select * from $tables[customers_addresses] where address_id='$division[address_id]'");
    return $divisions;
}

function cw_warehouse_delete_division($division_id) {
    global $tables;
    
    $address_id = cw_query_first_cell("select address_id from $tables[warehouse_divisions] where division_id='$division_id'");
    db_query("delete from $tables[customers_addresses] where address_id='$address_id'");
    db_query("delete from $tables[warehouse_divisions] where division_id='$division_id'");
    db_query("delete from $tables[shipping_rates] where warehouse_customer_id='$division_id'");
    db_query("delete from $tables[discounts] where warehouse_customer_id='$division_id'");
    db_query("delete from $tables[tax_rates] where warehouse_customer_id='$division_id'");
    db_query("delete from $tables[zones] where warehouse_customer_id='$division_id'");
    db_query("delete from $tables[products_warehouses_amount] where warehouse_customer_id='$division_id'");
}

function cw_warehouse_get_like_user($division_id, $division_from) {
    global $tables;

    $info = cw_query_first("select * from $tables[warehouse_divisions] where division_id='$division_id'");
    $info['current_address'] = cw_query_first("select * from $tables[customers_addresses] where address_id='$info[address_id]'");
    $info['main_address'] = cw_query_first("select ca.* from $tables[customers_addresses] as ca, $tables[warehouse_divisions] as wd where wd.division_id='$division_from' and wd.address_id=ca.address_id");
    $info['usertype'] = 'W';
    return $info;
}

function cw_warehouse_reset_all_amount($division_id, $product_id = '') {
    global $tables;

    db_query("update $tables[products_supplied_amount] set is_hide=1 where warehouse_customer_id='$division_id'".($product_id?" and product_id='$product_id'":''));

    $data = cw_query("select * from $tables[products_warehouses_amount] where warehouse_customer_id='$division_id'".($product_id?" and product_id='$product_id'":''));
    $fields = array('avail', 'avail_ordered', 'avail_sold', 'avail_reserved');
    if (is_array($data))
    foreach($data as $tmp) {
        foreach($fields as $field)
            if ($tmp[$field] != 0) cw_accounting_create_initial_amount($division_id, $tmp['product_id'], $tmp['variant_id'], -$tmp[$field], $field);
    }
}

function cw_warehouse_reset_amount($division_id, $data) {
    global $tables, $user_account, $current_area;

    $fields = array('avail', 'avail_ordered', 'avail_sold', 'avail_reserved');

    $search_data = array(
        'manufacturers' => array($data['manufacturer_id'] => 1),
        'category_id' => $data['category_id'],
        'flat_search' => 1,
    );
    if ($data['product_class']) 
        $search_data['features'] = array($data['product_class'] => array());
    if ($data['supplier_customer_id']) 
        $search_data['suppliers'] = array($data['supplier_customer_id'] => 1);


    list($products, $navigation) = cw_func_call('cw_product_search', array('data' => $search_data, 'user_account' => $user_account, 'current_area' => $current_area));

    if ($products)
    foreach($products as $product)
        cw_warehouse_reset_all_amount($division_id, $product['product_id']);
}
