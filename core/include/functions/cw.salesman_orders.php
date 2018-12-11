<?php
# kornev, 42799

if (!defined('APP_START')) die('Access denied');

function cw_get_salesman_orders($user) {
    global $tables;
    $data = cw_query("select * from $tables[salesman_orders] where customer='$user'");
    if (is_array($data))
        foreach($data as $k=>$v) {
            $data[$k]['products'] = unserialize($v['cart']);
            unset($data[$k]['cart']);
        }
    return $data;
}

function cw_get_salesman_pending_orders($salesman = '') {
    global $tables;
    $data = cw_query("select * from $tables[salesman_orders] where status=0".($salesman?" and salesman_customer_id='$salesman'":""));
    if (is_array($data))
        foreach($data as $k=>$v) {
            $data[$k]['products'] = unserialize($v['cart']);
            unset($data[$k]['cart']);
        }
    return $data;
}

function cw_get_salesman_order($doc_id) {
    global $tables;

    $obj = cw_query_first("select * from $tables[salesman_orders] where id='$doc_id'");
    $obj['products'] = unserialize($obj['cart']);
    return $obj;
}

function cw_save_salesman_order($cart_tmp, $doc_id = 0) {
    $salesman_order = array();
    $products = $cart_tmp['products'];
    $save_fields = array('product_id', 'productcode', 'product', 'warehouse', 'taxed_price', 'taxes', 'extra_data', 'catalog_price', 'product_options', 'amount', 'new', 'free_price', 'price', 'items_in_stock');
    if (is_array($products))
        foreach($products as $k=>$v) {
            if ($v['deleted']) {
                unset($products[$k]);
                continue;
            }
            foreach($v as $field=>$val)
                if (!in_array($field, $save_fields)) unset($products[$k][$field]);
        }

    $salesman_order['cart'] = addslashes(serialize($products));
    $salesman_order['customer_id'] = $cart_tmp['customer_id'];
    $salesman_order['salesman_customer_id'] = $cart_tmp['salesman_customer_id'];
    $salesman_order['status'] = 0;
    if ($doc_id)
        $salesman_order['id'] = $doc_id;
    return cw_array2insert('salesman_orders', $salesman_order, true);
}
?>
