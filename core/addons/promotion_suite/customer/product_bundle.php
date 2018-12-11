<?php
global $product_bundle;

// TOFIX: retrieve product offer thru function
$offer_id = cw_query_first_cell("SELECT offer_id FROM $tables[ps_offers] WHERE pid='$product_id' AND active='1'");
$product_bundle = cw_call('cw_ps_offer', array($offer_id));

if (is_array($product_bundle['conditions']['P']['products']))
foreach ($product_bundle['conditions']['P']['products'] as $k=>$v) {
	if ($k != $product_id)
		$product_bundle['products'][$k] = cw_func_call('cw_product_get', array('id'=>$k, 'info_type'=>65535));
}
$smarty->assign('product_bundle', $product_bundle);
