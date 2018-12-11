<?php
cw_load('product', 'category', 'image', 'file_area', 'cart', 'attributes', 'tags');

global $customer_id, $product_info;
# kornev, required for taxes
$user_info = cw_user_get_info($customer_id, 1);
$product_info = cw_func_call('cw_product_get' ,array('id' => $product_id, 'user_account' => $user_info, 'info_type' => 65535));
if (!$product_info['product_id']) cw_header_location('index.php?target=error_message&error=product_disabled');

if (intval($cat) == 0) $cat = $product_info['category_id'];
$smarty->assign('cat', $cat);
$smarty->assign('menu_arrivals', cw_sections_get_featured('new_arrivals', $cat));

if ($product_info['product_id']) {
	$product_info['meta_descr'] = strip_tags($product_info['descr']);
	$product_info['meta_keywords'] = strip_tags($product_info['product'])." ".preg_replace("/[^a-zA-Z0-9]/", " ", strip_tags($product_info['descr']));
}

cw_include('include/products/send_to_friend.php');

if (!empty($send_to_friend_info)) {
	$smarty->assign('send_to_friend_info', $send_to_friend_info);
	if ($addons['image_verification'])
		$smarty->assign('antibot_err', $send_to_friend_info['antibot_err']);
	cw_session_unregister("send_to_friend_info");
}

# kornev, TOFIX
if ($addons['magnifier'])
	cw_include('addons/magnifier/product.php');

// Update view statistic
if (!defined('IS_ROBOT')) {
    cw_call_delayed('cw_product_run_counter', array('product_id' => $product_id, 'count' => 1, 'type' => 0));
}

if ($config['General']['disable_outofstock_products'] == "Y" && empty($product_info['distribution'])) {
    $is_avail = (cw_func_call('cw_product_check_avail', array('product' => $product_info)) || ($config['General']['disabled_products_access_by_direct_link'] == 'Y'));
 
    if(!$is_avail) 
        cw_header_location("index.php?target=error_message&error=access_denied&id=44");
}

if ($addons['wholesale_trading'] && empty($product_info['variant_id']))
	cw_include('addons/wholesale_trading/product.php');


if ($addons['recommended_products'])
	cw_include('addons/recommended_products/recommends.php');

$location = array_merge($location, cw_category_get_location($cat, '', 0, 1));
if ($product_info) $location[] = array($product_info['product'], '');

if ($config['Appearance']['categories_in_products']) {
	$product_info['category_category_url'] = cw_category_category_url($product_info['category_id']);
}
$product_info['tags'] = cw_tags_get_product_tags($product_id);

// Supplier delivery time
if ($product_info['system']['supplier_customer_id']) {
	$supplier_fields = cw_user_get_custom_fields($product_info['system']['supplier_customer_id'],0,'','field');
	if ($supplier_fields['min_delivery_time'] == $supplier_fields['max_delivery_time'])
		$product_info['supplier']['delivery_time'] = $supplier_fields['min_delivery_time'];
	else
		$product_info['supplier']['delivery_time'] = $supplier_fields['min_delivery_time'].'-'.$supplier_fields['max_delivery_time'];
	unset($supplier_fields);
}

$smarty->assign('product',              $product_info);
$smarty->assign('current_section_dir', 'products');
$smarty->assign('main',                'product');

# kornev, in the custoemr area we've got only the product attributes;
global $attributes;
$attributes = cw_func_call('cw_attributes_get', array('item_id' => $product_id, 'item_type' => 'P', 'attribute_class_ids' => $product_info['attribute_class_ids'], 'is_show' => 1));
$smarty->assign('attributes', $attributes);
