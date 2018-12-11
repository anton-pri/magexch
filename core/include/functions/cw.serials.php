<?php
function cw_serials_get_number($length) {
    if (!$length) $length = 16;
    return substr(strtoupper(md5(uniqid(rand()))), 0, $length);
}

function cw_serials_is_auto_generated($product_id) {
    global $tables;

# kronev, TOFIX
    return false;

    $is_required = cw_query_first_cell("select auto_serials from $tables[products] where product_id='$product_id'");
    return ($is_required == 'Y');
}
    
function cw_serials_check_product($product_id, $customer_id = 0) {
    global $tables, $config;

    $is_required = cw_serials_is_auto_generated($product_id);
    
    if (!$is_required) return false;

    if ($customer_id) $warehouse_condition = "and warehouse_customer_id='$customer_id'";

    $avails = cw_query("select sum(avail) as amount, warehouse_customer_id from $tables[products_warehouses_amount] where product_id='$product_id' $warehouse_condition group by warehouse_customer_id");
    $return = array();
    if ($avails)
        foreach($avails as $val) {
            $count_unused = cw_query_first_cell("select count(*) from $tables[serial_numbers] where product_id='$product_id' and warehouse='$val[warehouse_customer_id]' and doc_id=0");
            if ($count_unused < $val['amount']) {
                for($i = 0; $i < $val['amount']-$count_unused; $i++) {
                    do {
                        $number = cw_serials_get_number($config['sn']['auto_product_number']);
                        if(cw_query_first_cell("select count(*) from $tables[serial_numbers] where sn ='$number' and product_id='$product_id'"))
                            $number = false;
                    } while(!$number);
    
                    $return[] = $number;
                    cw_serials_add($val['warehouse_customer_id'], $product_id, $number);
                }
            }
        }
    return $return;
}

function cw_get_serial_numbers($customer_id, $product_id, $full_info = false) {
    global $tables, $current_area;

    $serials = cw_query($sql="select * from $tables[serial_numbers] where doc_id=0 and product_id='$product_id'".($customer_id?" and warehouse_customer_id='$customer_id'":"")." order by warehouse_customer_id");
    if (!$serials) $serials = array();
    return $serials;
}

function cw_serials_add($customer_id, $product_id, $serial) {
    global $tables;

    $count = cw_query_first_cell("select count(*) from $tables[serial_numbers] where sn='$serial' and product_id='$product_id'");
    if ($count) return false;

    $serial = trim($serial);
    if (empty($serial)) return true;

    $to_insert = array(
        'sn' => $serial,
        'product_id' => $product_id,
        'doc_id' => 0,
        'warehouse_customer_id' => $customer_id,
        'date' => time(),
    );
    cw_array2insert('serial_numbers', $to_insert);

    return true;
}

function cw_serials_delete($customer_id, $product_id, $serial) {
    global $tables;

    $count = cw_query_first_cell("select count(*) from $tables[serial_numbers] where sn='$serial' and product_id='$product_id' and doc_id=0".($customer_id?" and warehouse_customer_id='$customer_id'":""));
    if ($count) {
        db_query("delete from $tables[serial_numbers] where sn='$serial' and product_id='$product_id'".($customer_id?" and warehouse_customer_id='$customer_id'":""));
    }

}
?>
