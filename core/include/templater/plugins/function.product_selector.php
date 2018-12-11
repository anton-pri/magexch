<?php

/**
 * @param $params
 *	products - existing product selection list (
 * 		array('id'=>100, 'name'=>'Product', 'quantity'=>1) or array('id'=>100) or 100(product_id)
 *  ),
 *  name_for_id - text for name attribute for "Product id" field
 *  name_for_name - text for name attribute for "Product name" field
 *  prefix_id - prefix for id attribute for "Product id" field
 *  prefix_name - prefix for name attribute for "Product name" field
 *  amount_name - value for id and name attribute for amount(quantity) field
 *  hide_id_field - set "Product id" field is hidden
 *  multiple - flag for multiple fields (default 0)
 *  extra_code_tpl - path to template which may be used to create additional input parameters
 *  form - form name
 *  without_form - flag to indicate the use of the name forms in field name (default 1)
 *  use_ajax - switches on/off ajax feature (default 1)
 *  cat_id - additional query param for popup windows uri
 *  supplier_id - additional query param for popup windows uri
 *  colspan - colspan for first td in row only for multiple fields
 * @param $smarty
 * @return mixed
 */
function smarty_function_product_selector($params, &$smarty) {
	$products = array(array('id' => '', 'name' => ''));
	$name_for_id = '';
	$name_for_name = '';
	$prefix_id = '';
	$prefix_name = '';
	$amount_name = '';
	$hide_id_field = 0;
	$multiple = 0;
	$extra_code_tpl = '';
	$form = '';
	$without_form = 1;
	$use_ajax = 1;
	$cat_id = '';
	$supplier_id = '';
	$colspan = '';

	// get existing product selection list
	if (isset($params['products']) && !empty($params['products'])) {
		$product_id = 0;
		if (is_array($params['products'])) {
			if (
				isset($params['products']['id'])
				&& !empty($params['products']['id'])
			) {
				$product_id = $params['products']['id'];
			}
            elseif (
                isset($params['products']['product_id'])
                && !empty($params['products']['product_id'])
            ) {
                $product_id = $params['products']['product_id'];
            }  
            else {
				$products = $params['products'];
			}
		} else if (is_numeric($params['products']) && $params['products'] > 0) {
			$product_id = $params['products'];
		}

		if (!empty($product_id)) {
			cw_load('product');
			$product_info = cw_func_call('cw_product_get', array('id' => $product_id, 'info_type' => 0));
			$products = array(array('id' => $product_id, 'name' => $product_info['product']));
		}
	}

	// get text for name attribute for "Product id" field
	if (isset($params['name_for_id']) && !empty($params['name_for_id'])) {
		$name_for_id = trim($params['name_for_id']);
	}

	// get text for name attribute for "Product name" field
	if (isset($params['name_for_name']) && !empty($params['name_for_name'])) {
		$name_for_name = trim($params['name_for_name']);
	}

	// get prefix for id attribute for submitted product selection
	if (isset($params['prefix_id']) && !empty($params['prefix_id'])) {
		$prefix_id = trim($params['prefix_id']);
	}

	// get prefix for name attribute for submitted product selection
	if (isset($params['prefix_name']) && !empty($params['prefix_name'])) {
		$prefix_name = trim($params['prefix_name']);
	}

	// get name for amount(quantity) field
	// if name is set, then the amount field will be display after product name field
	if (isset($params['amount_name']) && !empty($params['amount_name'])) {
		$amount_name = trim($params['amount_name']);
	}

	// if hide_id_field is set, then the "Product id" field is hidden
	if (isset($params['hide_id_field']) && !empty($params['hide_id_field'])) {
		$hide_id_field = $params['hide_id_field'];
	}

	// get multiple flag
	if (isset($params['multiple']) && !empty($params['multiple'])) {
		$multiple = $params['multiple'];
	}

	// get name of template which may be used to create additional input parameters
	if (isset($params['extra_code_tpl']) && !empty($params['extra_code_tpl'])) {
		$extra_code_tpl = trim($params['extra_code_tpl']);
	}

	// get form name
	if (isset($params['form']) && !empty($params['form'])) {
		$form = $params['form'];
		$without_form = 0;
	}

	// get ajax flag
	if (isset($params['use_ajax']) && !empty($params['use_ajax'])) {
		$use_ajax = $params['use_ajax'];
	}

	// get without_form param
	if (isset($params['without_form']) && !empty($params['without_form'])) {
		$without_form = $params['without_form'];
	}

	// get additional query params cat_id for popup windows uri
	if (isset($params['cat_id']) && !empty($params['cat_id'])) {
		$cat_id = $params['cat_id'];
	}

	// get additional query params supplier_id for popup windows uri
	if (isset($params['supplier_id']) && !empty($params['supplier_id'])) {
		$supplier_id = $params['supplier_id'];
	}

	// get colspan for first td in row only for multiple fields
	if ($multiple && isset($params['colspan']) && !empty($params['colspan'])) {
		$colspan = $params['colspan'];
	}

	$smarty->assign('psw_products', $products);
	$smarty->assign('psw_name_id', $name_for_id);
	$smarty->assign('psw_name_name', $name_for_name);
	$smarty->assign('psw_prefix_id', $prefix_id);
	$smarty->assign('psw_prefix_name', $prefix_name);
	$smarty->assign('psw_amount_name', $amount_name);
	$smarty->assign('psw_hide_id_field', $hide_id_field);
	$smarty->assign('psw_multiple', $multiple);
	$smarty->assign('psw_extra_code_tpl', $extra_code_tpl);
	$smarty->assign('psw_form', $form);
	$smarty->assign('psw_use_ajax', $use_ajax);
	$smarty->assign('psw_without_form', $without_form);
	$smarty->assign('psw_cat_id', $cat_id);
	$smarty->assign('psw_supplier_id', $supplier_id);
	$smarty->assign('psw_colspan', $colspan);

	return $smarty->fetch('main/select/product_selector.tpl');

}
