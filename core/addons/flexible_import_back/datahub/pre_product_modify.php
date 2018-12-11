<?php

if ($REQUEST_METHOD == "POST") { 
//    cw_log_add('datahub_pre_modify', array('product_id'=>$product_id));
    cw_datahub_save_product_values(array($product_id));
}
