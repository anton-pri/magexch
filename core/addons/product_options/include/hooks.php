<?php

function cw_on_warehouse_recalculate($product_id) {
   
    global $tables;
 
    if (cw_product_has_variants($product_id)) {

        $to_update = cw_query_first("select sum(avail) as avail, sum(avail_ordered) as avail_ordered, sum(avail_sold) as avail_sold, sum(avail_reserved) as avail_reserved from $tables[products_warehouses_amount] where product_id='$product_id' and warehouse_customer_id = 0 and variant_id != 0");
        $to_update['warehouse_customer_id'] = 0;
        $to_update['product_id'] = $product_id;
        $to_update['variant_id'] = 0;

        cw_call('cw_warehouse_insert_avail',array('insert'=>$to_update));
        
    }

}
