<?php
global $product_id;
global $product_accessories,  $recommended_products;

if (empty($product_id)) return;

if (!($accessories = cw_cache_get($product_id, 'product_accessories'))) {
    $product_accessories = cw_call('cw_ac_get_linked_products',array($product_id,1));

    $_product_accessories = array();
    foreach ($product_accessories as $pa_k => $pa_v) {
        if ($pa_v['product_id'])
            $_product_accessories[$pa_k] = $pa_v; 
    }
    $product_accessories = $_product_accessories;

    $recommended_products = cw_call('cw_ac_get_recommended',array($product_id));
    $accessories = array($product_accessories,$recommended_products); 
    cw_cache_save($accessories,$product_id,'product_accessories');
}

list($product_accessories,$recommended_products) = $accessories;

$smarty->assign('product_accessories', $product_accessories);
$smarty->assign('product_recommended', $recommended_products);

