<?php
cw_load('warehouse', 'image', 'category', 'attributes', 'config');

global $products,$product_filter, $search_data, $category_page, $use_search_conditions, $mode, $navigation;

$search_data = &cw_session_register("search_data", array());
$category_page = &cw_session_register('category_page', array());

if (isset($new_search) || 
	(defined('FACET_URL') && !$search_data['products']['customer_search']['redirected_to_facet'])
   ) {
   // New search requested or
   // Customer puts facet_url directly in address without autoredirect from combination (redirect happens when product filter is used)
   // see addons/clean_urls/init/abstract.php for related flags
	$search_data['products']['customer_search'] = array();
    unset($new_search,$_GET['new_search']);
} else {
	cw_unset($search_data['products']['customer_search'],'redirected_to_facet');
}

$search_data['products']['customer_search']['flat_search'] = 1;
$search_data['products']['customer_search']['status'] = cw_core_get_required_status($current_area);
$search_data['products']['customer_search']['objects_per_page'] = $category_page['objects_per_page'];
$search_data['products']['customer_search']['sort_field'] = $category_page['sort_field'];
$search_data['products']['customer_search']['sort_direction'] = $category_page['sort_direction'];
$search_data['products']['customer_search']['info_type'] = $product_list_template == 2?8+32+128+256:8+32+128;
# kornev, add product filter
$search_data['products']['customer_search']['info_type'] += 1024;
$search_data['products']['customer_search']['attributes'] = $att;

$use_search_conditions = 'customer_search';
    
// clean page num for infinite scroll if change display type
if (
	$config['Appearance']['infinite_scroll'] == 'Y'
	&& !$_GET['page']
	&& (
		isset($_GET['items_per_page'])
		|| isset($_GET['sort'])
		|| isset($_GET['sort_direction'])
		|| isset($_GET['set_view'])
	)
) {
	$search_data['products'][$use_search_conditions]['page'] = 1;
}
    
cw_include('include/products/search.php');

if (count($products)) {
	# Generate the URL of the search result page for accesing it via GET-request
	$search_url_args = array();
	foreach ($search_data['products'] as $k=>$v) {
		if (in_array($k, (array)$allowable_search_fields) && !empty($v)) {
			if (is_array($v)) {
				foreach ($v as $k1=>$v1)
					$search_url_args[] = $k."[".$k1."]=".urlencode($v1);
			}
			else
				$search_url_args[] = "$k=".urlencode($v);
		}
	}

	if ($search_url_args && $page > 1)
		$search_url_args[] = "page=$page";

	$search_url = "index.php?target=search&mode=search".(!empty($search_url_args) ? "&".implode("&", $search_url_args) : "");
	$smarty->assign('search_url', $search_url);
} elseif ($mode=='search' && cw_call('cw_allow_no_products_found_redirect')) {
	cw_header_location($current_location.'/no_products_found');
}
$smarty->assign('products', $products);
$smarty->assign('search_prefilled', $search_data['products']['customer_search']);

if ($mode=='search') {
    $location = array_merge($location, cw_product_get_filter_location($product_filter, $navigation['script_raw']));

    $location[] = array(cw_get_langvar_by_name('lbl_search_results'), '');
    $smarty->assign('main', 'search');
    $smarty->assign('show_left_bar', TRUE);
} elseif ($mode=='no_products_found') {
    $mode = 'search';
	$smarty->assign('mode', $mode);
    $location[] = array(cw_get_langvar_by_name('lbl_search_results'), '');
    $smarty->assign('main', 'search');
    $smarty->assign('show_left_bar', TRUE);	
}
else {

    $location[] = array(cw_get_langvar_by_name('lbl_advanced_search'), '');
    $smarty->assign('main', 'search');
}
if ($search_data['products']['customer_search']['substring'])
    $location[] = array($search_data['products']['customer_search']['substring'], '');

$smarty->assign('current_section_dir', 'products');

if (defined('IS_AJAX')) {
	define('PREVENT_XML_OUT', 1);
}
