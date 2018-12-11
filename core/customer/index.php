<?php
cw_load('category', 'sections', 'image', 'attributes', 'tags');

$smarty->assign('featured_products', cw_sections_get_featured('featured_products', $cat));
$smarty->assign('menu_arrivals', cw_sections_get_featured('new_arrivals', $cat, 264));
$noindex = !empty($sort) || (!empty($page) && $page > 1) || !empty($att);
$smarty->assign('noindex', $noindex);

global $products,$product_filter, $navigation;

if ($cat) {
	
    $category_page = &cw_session_register('category_page', array());

    if (!is_array($category_page))
        $category_page = array(); 

    if (!defined("IS_ROBOT") && $cat) {
        cw_call_delayed('db_query', array("INSERT DELAYED INTO $tables[categories_stats] (category_id, views_stats) values ($cat,1) ON DUPLICATE KEY UPDATE views_stats = (views_stats + 1)"));
    }

    $data = array();
    $data['flat_search'] = true;
    $data['category_id'] = $cat;
    $data['search_in_subcategories'] = '';
    $data['category_main'] = 'Y';
    $data['category_extra'] = "Y";

    if (isset($manufacturer)) {
        $data['manufacturers'][$manufacturer] = 1;

        if (empty($manufacturer)) $data['manufacturers'] = null;
    }

    $data['page'] = $page;
    $data['objects_per_page'] = $category_page['objects_per_page'];
    $data['sort_field'] = $category_page['sort_field'];
    $data['sort_direction'] = $category_page['sort_direction'];

    $info_type = $product_list_template == 2?8+32+256:8+32+128;
// add product filter
    $info_type += 1024;
    $data['attributes'] = $att;

    list($products, $navigation, $product_filter) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => $info_type));
    $navigation['script'] = cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $cat, 'include_question'=>1, 'delimiter' => '&', 'att' => $att)));
    $smarty->assign('navigation', $navigation);
    $smarty->assign('products', $products);
    $smarty->assign('product_filter', $product_filter);

	if ($config['product']['pf_is_ajax'] == 'Y' && $ajax_filter) {
		$ns = cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $cat, 'include_question' => 1, 'delimiter' => '&')));
		$url = cw_product_get_filter_replaced_url($product_filter, $ns);
		$smarty->assign('replaced_url', $url);
	}

	$current_category = cw_func_call('cw_category_get', array('cat' => $cat));
    if (!$current_category) cw_header_location('index.php');
	$smarty->assign('current_category', $current_category);
    $location = array_merge($location, cw_category_get_location($cat, '', 0, 1));

	if (!empty($products) && count($products)) {
		$smarty->assign('category_category_url', str_replace($app_web_dir, "", cw_category_category_url($cat)));
	}

    $smarty->assign('sort_fields', cw_call('cw_product_get_sort_fields'));
    $smarty->assign('search_prefilled', $data);

# kornev, recent categories
    $recent_categories = &cw_session_register('recent_categories', array());
    array_unshift($recent_categories, $cat);

    if (count($recent_categories)) {
        $recent_categories = array_unique($recent_categories);
        $recent_categories = array_slice($recent_categories, 0, $config['category_settings']['recent_categories_amount']);

        $tmp = cw_query("select category_id from $tables[categories] where category_id in ('".implode("', '", $recent_categories)."')");
        $to_sort = array_flip($recent_categories);
        foreach($tmp as $val) {
            $ret = cw_func_call('cw_category_get', array('cat' => $val['category_id']));
            if ($ret)
                $result[$to_sort[$val['category_id']]] = $ret;
        }
        if (is_array($result))
            ksort($result);
        $smarty->assign('recent_categories', $result);
    }
}
else {
    $smarty->assign('clearance', cw_call('cw_sections_get', array('section' => 'clearance', array('all'=>1), 'where' => 'home_page=1', 'info_type' => 136)));

    $smarty->assign('hot_deals_home',  cw_call('cw_sections_get', array('section' => 'hot_deals', array('all'=>1), 'where' => 'home_page=1', 'info_type' => 136)));
    $hot_deals_hot =  cw_call('cw_sections_get', array('section' => 'hot_deals', array('all'=>1), 'where' => 'hot_deal=1', 'info_type' => 136));
    if (is_array($hot_deals_hot))
        $smarty->assign('hot_deals_hot', array_pop($hot_deals_hot));

    $featured_categories = cw_call('cw_featured_categories_get', array('current_language' => $current_language));
    $smarty->assign('featured_categories', $featured_categories);
}

$smarty->assign('tags', cw_tags_get_popular_tags());

$smarty->assign('bottom_line',  cw_call('cw_sections_get', array('section' => 'bottom_line', array('all'=>1), '', 'info_type' => 136)));

# kornev, deals of week
$hot_deals_week =  cw_call('cw_sections_get', array('section' => 'hot_deals', array('all'=>1), 'where' => 'week_deal=1', 'info_type' => 136));
if (is_array($hot_deals_week)) {
    $week_navigation = cw_core_get_navigation($target, sizeof($hot_deals_week), 1, 4);
    $smarty->assign('week_navigation', $week_navigation);
    $smarty->assign('hot_deals_week', $hot_deals_week);
}

if ($cat) {
    $last = array_pop($location);
    $last['1'] = '';
    $location[] = $last;
//    $location = array_merge($location, cw_product_get_filter_location($product_filter, $navigation['script']));

	$smarty->assign('product_filter_navigation', cw_product_get_filter_location($product_filter, $navigation['script']));
    $smarty->assign('cat', $cat);

    $smarty->assign('current_section_dir', 'products');
    $smarty->assign('main', 'subcategories');
    $smarty->assign('show_left_bar', TRUE);

    if (defined('IS_AJAX')) {
        define('PREVENT_XML_OUT', 1);
    }

    // turn off infinite scroll if products more than max_count_view_products
	$category_product_count = cw_category_product_count($cat);

	if (
		!empty($app_config_file['interface']['max_count_view_products']) 
		&& $category_product_count >= $app_config_file['interface']['max_count_view_products']
	) {
		$smarty->assign("infinite_scroll_manual_off", "Y");
	}
}
else {
    $lbl_site_meta_title = strip_tags(cw_get_langvar_by_name('lbl_site_meta_title', '', false, true));
    $location = array();
    $location[] = array((!empty($lbl_site_meta_title) ? $lbl_site_meta_title : $config['Company']['company_name']), 'index.php');
    $smarty->assign('main', 'welcome');
}

if (empty($cat)) {
    cw_include('customer/home_page_product_filter.php');
}
