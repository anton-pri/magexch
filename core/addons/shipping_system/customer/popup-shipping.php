<?php
cw_load('warehouse', 'map', 'cart');

$product_info = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 128+8192));

if ($action == 'estimate' && !empty($zipcode)) {
    $userinfo = array();
    $userinfo['current_address']['country'] = $country;
    $userinfo['current_address']['zipcode'] = $zipcode;
    $shippings = array();
    $product_info['amount'] = 1;
    $config['General']['apply_default_country'] = 'Y';
    $config['Shipping']['enable_all_shippings'] = 'N';

    $cart_tmp['products'] = array($product_info);
    $shippings = cw_func_call('cw_shipping_get_list', array('cart' => $cart_tmp, 'products' => array($product_info), 'userinfo' => $userinfo, 'warehouse_customer_id' => $warehouse));
    
	// Supplier delivery time
	if ($product_info['system']['supplier_customer_id']) {
		$supplier_fields = cw_user_get_custom_fields($product_info['system']['supplier_customer_id'],0,'','field');

		if ($supplier_fields['min_delivery_time'] == $supplier_fields['max_delivery_time'])
			$product_info['supplier']['delivery_time'] = $supplier_fields['min_delivery_time'];
		else
			$product_info['supplier']['delivery_time'] = $supplier_fields['min_delivery_time'].'-'.$supplier_fields['max_delivery_time'];		

	}

    $smarty->assign('shippings', $shippings);
}
$smarty->assign('zipcode', $zipcode);   
$smarty->assign('country', $country?$country:$user_account['country']);   

$smarty->assign('product', $product_info);

if (defined('IS_AJAX') && constant('IS_AJAX')) {
    cw_add_ajax_block(array(
        'id' => 'estimate_shipping_container',
        'action' => 'replace',
        'template' => 'customer/products/estimate-fields.tpl',
    ));
}
else {
    $avails = cw_warehouse_get_avails_customer($product_id);
    $smarty->assign('avails', $avails);
    $smarty->assign('count_avails', count($avails));
    $location[] = array(cw_get_langvar_by_name('lbl_estimate_ship_note'), '');

    $smarty->assign('home_style', 'popup');
    $smarty->assign('current_main_dir', 'addons/shipping_system');
    $smarty->assign('current_section_dir', 'customer');
    $smarty->assign('main', 'popup-shipping');
}
