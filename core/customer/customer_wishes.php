<?php
cw_load('product', 'image');

# kornev, TOFIX
if (!$addons['bestsellers'])
    cw_header_location('index.php');

$data = array();
$data['where'] = "IFNULL($tables[products_stats].views_stats,0) > 0";
$data['sort_condition'] = "$tables[products_stats].views_stats";
$data['sort_direction'] = 1;
$data['limit'] = $config['bestsellers']['top_wishes_limit'];
$data['all'] = 1;
$data['attributes'] = $att;

list($products, $navigation, $product_filter) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => 0));

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

$navigation['script'] = cw_call('cw_core_get_html_page_url', array(array('var' => $target, 'delimiter' => '&', 'att' => $att)));
$smarty->assign('navigation', $navigation);
$smarty->assign('products', $products);
$smarty->assign('product_filter', $product_filter);

$smarty->assign('sort_fields', cw_product_get_sort_fields());
$smarty->assign('search_prefilled', $data);

$location[] = array(cw_get_langvar_by_name('lbl_customer_wishes'), '');
$location = array_merge($location, cw_product_get_filter_location($product_filter, $navigation['script']));

$smarty->assign('current_section_dir', 'special_sections');
$smarty->assign('main', 'customer_wishes');
