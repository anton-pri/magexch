<?php

namespace CW\catalog_product;

// hook-function checks if "original_url" is filled for catalog product
function cw_error_check(&$array_to_check, $rules, $attributes_type = '') {
	
	$result = cw_get_return(); // get array of errors returned by core cw_error_check()

	if ($attributes_type != 'P' || is_null($result)) return $result; // hook for products only
	
	if (!cw_is_catalog_product($array_to_check)) return $result; // do not check other product types
	
	if (empty($array_to_check['attributes']['original_url'])) 
		cw_add_top_message(cw_get_langvar_by_name('err_field_catalog_products', '', false, true),'W');

	return $result;
}


// hook-function hides attribute 'original_url' for non-catalog products 
function cw_attributes_get($params, $return) {
	
	if ($params['item_type'] != 'P' || empty($params['item_id'])) return $return;
	
	$product = cw_func_call('cw_product_get', array('id'=>$params['item_id'],'info_type'=>0));

	if (is_array($product) && $product['product_id'] && !cw_is_catalog_product($product)) {
		unset($return['original_url']);
	}
	
	return $return;
}

function cw_is_catalog_product(&$params) {

	$product = $params;
	
	if (empty($params['product_id'])) { 	// function is called from smarty
		global $smarty;
		$smarty_product = $smarty->get_template_vars('product');
		if ($smarty_product) {
			$product = $smarty_product;
			if ($product['product_type']==PRODUCT_TYPE_CATALOG) {
				$params['button_title'] = cw_get_langvar_by_name('lbl_catalog_product_button');
				$params['href'] = "index.php?target=catalog_redirect&product_id=$product[product_id]";
			}
		}

        return false; // prevent real template replacement because href is already replaced
	}
	
    return $product['product_type']==PRODUCT_TYPE_CATALOG;
}
