<?php
# kornev, TOFIX
if (!$addons['bestsellers']) 
    cw_header_location('index.php');

cw_load('bestseller', 'image');
list($products, $navigation, $product_filter) = cw_bestseller_get_menu(0, 20, 0);

// Sorting and filtering of selected products
$data = array();
$data['sort_field'] = $category_page['sort_field'];
$data['sort_direction'] = $category_page['sort_direction'];
$data['all'] = 1;
$data['flat_search'] = 1;
$data['attributes'] = $att;
$ids=""; foreach ($products as $v) $ids.=$v['product_id']." ";
$ids=str_replace(" ",",",trim($ids));
if ($ids!='') {
	$data['where'] = "$tables[products].product_id in ($ids)";
	list($products, $nav, $product_filter) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => 8+32+128+256+1024));
}

$smarty->assign('navigation', $navigation);
$smarty->assign('products', $products);
$smarty->assign('product_filter', $product_filter);

$smarty->assign('sort_fields', cw_product_get_sort_fields());
$smarty->assign('search_prefilled', $data);

$location[] = array(cw_get_langvar_by_name('lbl_top_sellers'), '');
$location = array_merge($location, cw_product_get_filter_location($product_filter, $navigation['script']));

$smarty->assign('current_section_dir', 'special_sections');
$smarty->assign('main', 'top_sellers');
