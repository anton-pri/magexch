<?php
cw_load('image', 'warehouse','attributes');

global $manufacturers, $featured_manufacturers, $manufacturer, $products, $product_filter, $navigation;
global $products,$product_filter, $search_data, $category_page, $use_search_conditions, $mode;

if ($manufacturer_id) {
    $category_page = &cw_session_register('category_page', array());

	# Get products data for current category and store it into $products array

	$search_data['products']['manuf']['attribute_names'] = array('manufacturer_id' => $manufacturer_id);
    $search_data['products']['manuf']['flat_search'] = 1;

    $search_data['products']['manuf']['objects_per_page'] = $category_page['objects_per_page'];
    $search_data['products']['manuf']['sort_field'] = $category_page['sort_field'];
    $search_data['products']['manuf']['sort_direction'] = $category_page['sort_direction'];

    $search_data['products']['manuf']['info_type'] = $product_list_template == 2?8+32+256:8+32+128;
    $search_data['products']['manuf']['info_type'] += 1024;
    $search_data['products']['manuf']['attributes'] = $att;

    $mode = 'search';
    $use_search_conditions = 'manuf';

    cw_include('include/products/search.php');

    $smarty->assign('sort', $search_data['products']['sort_field']);
	$smarty->assign('sort_direction', $search_data['products']['sort_direction']);
    $smarty->assign('search_prefilled', $search_data['products']['manuf']);
	
	$manufacturer = cw_func_call('cw_manufacturer_get', array('manufacturer_id' => $manufacturer_id));
    if (!$manufacturer) cw_header_location('index.php');
    
    $attrs = cw_attributes_get_attributes_by_field(array('field'=>'clean_url'));
    $manufacturer['url'] = cw_attribute_get_value($attrs['M'],$manufacturer_id);

	$smarty->assign('manufacturer', $manufacturer);
    $smarty->assign('manufacturer_id', $manufacturer_id);
	$smarty->assign('main', "manufacturer_products");

    $get_params = $_GET; unset($get_params['target']);
    $navigation['script'] = cw_call('cw_core_get_html_page_url', array(array_merge($get_params,array(
    'var' => 'manufacturers', 'manufacturer_id' => $manufacturer_id, 'include_question'=>1, 'delimiter' => '&', 'att' => $att,
    ))));
    $navigation['script_raw'] = cw_call('cw_clean_url_get_dynamic_url',array($navigation['script']));

	$location[count($location)-1][1] = $app_catalogs['customer'].'/'.strtolower($current_language).'/index.php?target=manufacturers';
	$location[] = array($manufacturer['manufacturer'], "");
    $location = array_merge($location, cw_product_get_filter_location($product_filter, $navigation['script']));
    array_pop($location); // remove manufacturer as part of filter in location

    if (defined('IS_AJAX')) {
        define('PREVENT_XML_OUT', 1);
    }

    $location[] = array(cw_get_langvar_by_name('lbl_manufacturers'), 'index.php?target=manufacturers');
    $location[] = array($manufacturer['manufacturer'], '');

}
else {
    list($manufacturers, $navigation) = cw_func_call('cw_manufacturer_search', array('data' => array('avail' => 1, 'page' => $request_prepared['page'], 'all'=>true), 'info_type' => 0)); // info_type 2 => 0
    list($featured_manufacturers, $navigation) = cw_func_call('cw_manufacturer_search', array('data' => array('avail' => 1, 'featured'=>1,'page' => $request_prepared['page'], 'all'=>true), 'info_type' => 1+0));

    $smarty->assign_by_ref('manufacturers', $manufacturers);
    $smarty->assign_by_ref('featured_manufacturers', $featured_manufacturers);
    $smarty->assign('main', 'manufacturers_list');
    $location[] = array(cw_get_langvar_by_name('lbl_manufacturers'), '');
}

$smarty->assign('navigation', $navigation);
$smarty->assign('current_section_dir', 'manufacturer');
$smarty->assign('show_left_bar', TRUE);
