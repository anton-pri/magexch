<?php
if($config['accessories']['ac_rv_display_recently_viewed_pr']=='Y'){

global $product_id;

if (empty($product_id)) return;

cw_call('cw_ac_rv_product_set_cookie' ,array($product_id));
}