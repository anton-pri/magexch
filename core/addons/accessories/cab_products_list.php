<?php
// cab - customer also bought
if($config['accessories']['ac_cab_display'] == 'Y'){
	$products_ids = array_column($products, 'product_id');
	$cab_products = array();
	if (!empty($products_ids)) {
		$cab_products =  cw_call('cw_ac_cab_get_recommended', array('product_ids'=>$products_ids));
	}
	$smarty->assign('cab_products', $cab_products);

}
