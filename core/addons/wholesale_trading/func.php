<?php
function cw_wholesale_get_prices($product_id) {
    global $tables;
    
    return cw_query("select * from $tables[products_prices] where product_id='$product_id' order by membership_id, quantity");
}
?>
