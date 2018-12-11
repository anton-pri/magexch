<?php
define('AREA_TYPE', 'C');
$current_area = AREA_TYPE;

cw_load('files', 'speed_bar', 'sections', 'tabs', 'image');

$session_failed_transaction = &cw_session_register("session_failed_transaction");
$add_to_cart_time = &cw_session_register("add_to_cart_time");
$always_allow_shop = &cw_session_register("always_allow_shop");

if (!empty($_GET['shopkey'])) $always_allow_shop = (!empty($config['General']['shop_closed_key']) && $_GET['shopkey'] == $config['General']['shop_closed_key']);

if ($config['General']['shop_closed'] == "Y" && !$always_allow_shop){
	if (!cw_readfile($app_dir.DIRECTORY_SEPARATOR.$shop_closed_file, true))
		echo cw_get_langvar_by_name("txt_shop_temporarily_unaccessible",false,false,true);
	exit();
}

if (!defined('HTTPS_CHECK_SKIP')) cw_include('customer/https.php');

$cat = intval(@$cat);
$page = intval(@$page);

if ($target!='image') {
    cw_include('customer/referer.php');
    cw_include('include/check_useraccount.php');
    cw_include('init/lng.php');
    cw_include('include/settings.php');
}

$location = array();
$location[] = array($config['Company']['company_name'], $app_web_dir.'/index.php');

if ($addons['interneka'])
	cw_include('addons/interneka/interneka.php');

$smarty->assign('speed_bar', cw_func_call('cw_speed_bar_search', array('language' => $current_language, 'data' => array('active' => 1))));

if($config['Appearance']['search_for_arrivals'] == "Y")
$smarty->assign('menu_arrivals', cw_call('cw_sections_get', array('section' => 'arrivals',array('all'=>1), 'where' => 'side_box=1', 'info_type' => 256)));

if($config['Appearance']['search_for_accessories'] == "Y")
$smarty->assign('menu_accessories', cw_call('cw_sections_get', array('section' => 'accessories', array('all'=>1),'',null)));

# kornev
$possible_views = array('products', 'products_gallery', 'products_compact');
$product_list_template = &cw_session_register("product_list_template");
if (isset($_GET['set_view']) && isset($possible_views[$_GET['set_view']])) $product_list_template = $_GET['set_view'];
if (empty($possible_views[$product_list_template])) $product_list_template = 0;
$smarty->assign('set_view', $product_list_template);
$smarty->assign('product_list_template', $possible_views[$product_list_template]);

$category_page = &cw_session_register('category_page', array());

if($_GET['sort'])
    $category_page['sort_field'] = $_GET['sort'];
elseif(!isset($category_page['sort_field']))
    $category_page['sort_field'] = $config['Appearance']['products_order'];

if (isset($_GET['sort_direction'])) $category_page['sort_direction'] = $_GET['sort_direction'];

if($_GET['per_page'])
    $category_page['objects_per_page'] = $per_page;
elseif(!isset($category_page['objects_per_page']))
    $category_page['objects_per_page'] = $config['Appearance']['products_per_page'];

# kornev, sections position
$smarty->assign('left_sections', cw_query("select * from $tables[sections_pos] where location='L' order by orderby"));
$smarty->assign('right_sections', cw_query("select * from $tables[sections_pos] where location='R' order by orderby"));

# kornev, may be the same sections like in admin are required
global $app_skins_dirs;
$smarty->assign('current_main_dir', $app_skins_dirs[AREA_TYPE]);
$smarty->assign('current_section_dir', 'main');
$smarty->assign('current_target', $target);

if ($target != 'ajax' && $target!='image') {
    $avatar = cw_user_get_avatar($customer_id);
    $smarty->assign('user_avatar', $avatar);
    cw_include('include/area_sections.php');
    $smarty->assign('current_target', $target);
}

// Get menu items from addons
$main_menu_list = array();
cw_event('on_cart_menu_build', array(&$main_menu_list));
foreach($main_menu_list as $k => $v) {
    $main_menu_list[$k]["path"] = trim($v["path"], "\/");
}
$smarty->assign('main_menu_list', $main_menu_list);

cw_call('cw_auth_security');

if (!(defined('IS_AJAX') && !defined('PREVENT_XML_OUT'))) {
// This causes error - link www.saratogawine.com/?gclid=CPzYx7XJvMUCFQsCwwodupsA5A goes to 404 instead of home

    if ($area == "customer" && $target == "index" && empty($cat)) {
        global $clean_url_request_uri;

        $uri_parts = parse_url(str_replace($app_web_dir, '', $REQUEST_URI));
        $clean_url_request_uri = strtolower(trim($uri_parts['path'],'/'));

        if (!empty($clean_url_request_uri) && strpos($clean_url_request_uri,"index.php") === false) {
            global $error, $code, $page_code; 
            $target = "error_message"; 
            $error = "http";
            $code = "404"; 
            $page_code = $code;
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", TRUE,404); 
        }
    }
}

//logging code
cw_include('include/logging_data.php');
