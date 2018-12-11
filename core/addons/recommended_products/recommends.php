<?php
$data = array();
$data['flat_search'] = true;
$data['limit'] = $config['product']['number_of_recommends'];

if ($cat) {
	$data['categories'] = $cat;
	$data['category_id'] = $cat;
   	$data['search_in_subcategories'] = '';
	$data['category_main'] = 'Y'; 
	$data['category_extra'] = 'Y'; 
}	

if ($config['product']['use_inventory_products'] == 'Y' || $config['General']['disable_outofstock_products'] == 'Y')
    $data['avail_min'] = 1;

$data['sort_field'] = 'rand';
$data['where'] = "$tables[products].product_id != '$product_id'";

$data['limit'] = $config['product']['number_of_recommends'];

list($recommends, $navigation) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => 32+128));

$smarty->assign('recommends', $recommends);
