<?php
require_once('header.php');

pricing_IPO_avg_price::delete_table();
//currently using only web prices, might be a problem
pricing_IPO_avg_price::pricing_insert_IPO_avg_price(1);//add all 3 stores

pricing_aipo::delete_table();
pricing_aipo::pricing_calc_aaipo();//this should be ok

echo '<br /><br /><h2>Update Prices Monthly is complete</h2>';