<?php
global $product_id;

if (!$product_id)
	cw_header_location("index.php?target=error_message&error=access_denied&id=48");

$product_info = cw_func_call(
					'cw_product_get', 
					array(
						'id' 			=> $product_id, 
						'user_account' 	=> $user_account, 
						'info_type' 	=> 65535
					)
				);

if (isset($product_info['category_id']) && is_numeric($product_info['category_id'])) {
	$product_info['category'] = cw_func_call('cw_category_get', array('cat' => $product_info['category_id']));
}

// Created data
$product_info['created_text'] = "";
$creation_customer_id = cw_query_first_cell("SELECT creation_customer_id FROM $tables[products_system_info] WHERE product_id='$product_id'");

if (!empty($creation_customer_id)) {
	$user_data = cw_user_get_info($creation_customer_id, 1);
	$created_text = "";

	if ($user_data['main_address']) {
		$created_text = $user_data['main_address']['firstname'] . " " . $user_data['main_address']['lastname'] . " / ";
	}

	if ($user_data['email']) {
		$created_text .= $user_data['email'];
	}

	$product_info['created_text'] = $created_text;
}

if (!$product_info) {
	$top_message = array('content' => cw_get_langvar_by_name('lbl_products_deleted'), 'type' => 'E');
	cw_header_location('index.php?target=error_message');
}

$smarty->assign('product', $product_info);
$smarty->assign('main', 'preview');
