<?php
cw_load('warehouse', 'image', 'category');

$category_page = &cw_session_register('category_page', array());

$mode='search'; 
if (empty($page)) $page=1;

$search_data = &cw_session_register("search_data", array());

# The list of the fields allowed for searching
$allowable_search_fields = array (
	"substring",
	"by_title",
	"by_shortdescr",
	"by_fulldescr",
	"by_keywords",
	"by_manufacturer",
	"by_ean",
	"by_productcode",
	"category_id",
	"category_main",
	"category_extra",
	"search_in_subcategories",
	"price_max",
	"price_min",
	"price_max",
	"avail_min",
	"avail_max",
	"weight_min",
	"weight_max",
	"manufacturers");

if ($REQUEST_METHOD == 'GET' && $mode == 'search') {
	# Check the variables passed from GET-request
	$get_vars = array();
	foreach ($_GET as $k=>$v) {
		if (in_array($k, $allowable_search_fields))
			$get_vars[$k] = $v;
	}

	# Prepare the search data
	if (!empty($get_vars))
		$search_data['products']['customer_search'] = $get_vars;
}
$search_data['products']['customer_search']['substring'] = '';
$search_data['products']['customer_search']['flat_search'] = 1;

$search_data['products']['customer_search']['objects_per_page'] = $category_page['objects_per_page'];
$search_data['products']['customer_search']['sort_field'] = $category_page['sort_field'];
$search_data['products']['customer_search']['sort_direction'] = $category_page['sort_direction'];
$search_data['products']['customer_search']['info_type'] = $product_list_template == 2?8+32+256:8+32+128;

if (!empty($_COOKIE[RC_COOKIE_HISTORY_TEMP])) 
{
    $tmp = stripcslashes($_COOKIE[RC_COOKIE_HISTORY_TEMP]); 
    $arr_cookie = unserialize($tmp);
    if (is_array($arr_cookie)) list($id_cookie, $date_cookie) = $arr_cookie;
    if (!empty($date_cookie)) 
        $search_data['products']['customer_search']['creation_date_start']  = $date_cookie;
    unset($date_cookie); unset($arr_cookie);
} 

$use_search_conditions = 'customer_search';
include $app_main_dir.'/include/products/search.php';
$smarty->assign('products', $products);

if (count($products)) {
	# Generate the URL of the search result page for accesing it via GET-request
	$search_url_args = array();
	foreach ($search_data['products'] as $k=>$v) {
		if (in_array($k, $allowable_search_fields) && !empty($v)) {
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
}

$smarty->assign('search_prefilled', $search_data['products']['customer_search']);

if (!empty($QUERY_STRING)) {
    $location[] = array(cw_get_langvar_by_name('lbl_search_results'), '');
    $smarty->assign('main', 'search');
}
else {
    $location[] = array(cw_get_langvar_by_name('lbl_advanced_search'), '');
    $smarty->assign('main', 'advanced_search');
}
if ($search_data['products']['customer_search']['substring'])
    $location[] = array($search_data['products']['customer_search']['substring'], '');

$smarty->assign('current_section_dir', 'products');
?>
