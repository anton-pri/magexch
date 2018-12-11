<?php

if ($REQUEST_METHOD == "POST") {
    if (!empty($product_id) && $mode == 'details' && $action == 'product_modify') {
        db_query("delete from $tables[product_shipping_options_values] where product_id='$product_id'");  
 
        foreach ($product_shipping_values as $shipping_value) {
            if (!empty($shipping_value['shipping_id'])) 
                cw_array2insert('product_shipping_options_values', array('product_id'=>$product_id, 'shipping_id'=>$shipping_value['shipping_id'], 'price'=>$shipping_value['price']), true); 
        }
    }
}
