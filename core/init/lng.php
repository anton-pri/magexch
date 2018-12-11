<?php
global $current_language;
$current_language = &cw_session_register('current_language');
$shop_company = &cw_session_register('shop_company');
$edited_language = &cw_session_register('edited_language');

if (!empty($_GET['language']))
    $set_new_language = $current_language = strtoupper($_GET['language']);

if (!$current_language) {
    $current_language = $config['default_customer_language'];
    if (!$current_language) $current_language = 'EN';
}

$all_languages = cw_func_call('cw_core_get_available_languages');

$set_new_language = '';
if (!empty($_GET['sl']))
	$set_new_language = $_GET['sl'];
if (isset($_GET['sc']) && $user_account['additional_info']['can_change_company_id']) {
    $shop_company = $_GET['sc'];
    cw_header_location($l_redirect);
}
if (!$user_account['additional_info']['can_change_company_id'])
    $shop_company = $user_account['additional_info']['company_id'];

if (!empty($_GET['language']) && $current_area == 'C')
    $set_new_language = array_search($_GET['language'], array_keys($all_languages));

if ($_GET['els'])
    $edited_language = $els;

if ($set_new_language) {
    $shop_language = $set_new_language;
    unset($set_new_language);
}
elseif($current_language)
    $shop_language = $current_language;
elseif ($customer_id)
    $shop_language = cw_query_first_cell("select language from $tables[customers] where customer_id='$customer_id'");
elseif($_COOKIE['shop_language']) {
    $shop_language = $_COOKIE['shop_language'];
}
else {
    $shop_language = $config['default_customer_language'];
    if (!$shop_language) $shop_language = 'EN';
}

if (!isset($all_languages[$config['default_customer_language']]) && count($all_languages))
    $config['default_customer_language'] = key($all_languages);
if (!isset($all_languages[$config['default_admin_language']]) && count($all_languages))
    $config['default_admin_language'] = key($all_languages);

if (empty($shop_language) || !$all_languages[$shop_language]) {
    if (in_array($current_area, array('C', 'R')))
        $shop_language = $config['default_customer_language'];
    else
        $shop_language = $config['default_admin_language'];
}

# kornev
# define the language and if it's changed - redefine the settings;
if ($shop_language != $current_language) {
    $__tmp = $current_language;
    $current_language = $shop_language;
    if ($customer_id) {
        cw_load('user');
        cw_user_set_language($customer_id, $current_language);
    }
# for one year
    cw_set_cookie ('shop_language', '', cw_core_get_time()-31536000, "/");
    cw_set_cookie ('shop_language', $current_language, time()+31536000, "/");
    if ($app_http_host != $app_https_host) {
        cw_set_cookie ('shop_language', '', cw_core_get_time()-31536000, "/", $app_https_host, 1);
        cw_set_cookie ('shop_language', $current_language, time()+31536000, "/", $app_https_host, 1);
    }
}

if ($all_languages[$shop_language]['text_direction'])
    $smarty->assign('reading_direction_tag', ' dir="RTL"');
else
    $smarty->assign('reading_direction_tag', '');

$smarty->assign ('all_languages', $all_languages);
$smarty->assign ('shop_language', $shop_language);
$smarty->assign ('all_languages_cnt', sizeof($all_languages));
if (empty($edited_language)) $edited_language = $shop_language;
$smarty->assign('edited_language', $edited_language);

$smarty->assign('shop_company', $shop_company);

if ($current_area != 'Y' && $config['Company']['country']) {
    $config['Company']['country_name'] = cw_get_country($config['Company']['country']);
    $config['Company']['state_name'] = cw_get_state($config['Company']['state'], $config['Company']['country']);
    $config['Company']['country_has_states'] = cw_query_first_cell("SELECT display_states FROM $tables[map_countries] WHERE code = '".$config['Company']['country']."'") == 'Y';
}

# kornev, define the lng links
$_tmp = @parse_url($REQUEST_URI);
parse_str($_tmp['query'], $merge_arr);
if (!is_array($merge_arr)) $merge_arr = array();

foreach(array('sl', 'redirect', 'language', 'area') as $k)
    unset($merge_arr[$k]);

$lng_urls = array();
foreach($all_languages as $code=>$tmp) {
/*
# kornev, we have removed the language from the url and don't need that in default code
    if ($current_area == 'C') {
        $merge_arr['language'] = $code;

        $lang_redirect = cw_core_assign_addition_params($_tmp['path'], $merge_arr, array(), '&', false);

        if (preg_match('/\/.{2}\/.*-category-([0-9]+)\.html/', $lang_redirect, $fnd))
            $lang_redirect = cw_call('cw_core_get_html_page_url', array(array_merge($merge_arr, array('var' => 'cat', 'cat_id' => $fnd[1]))));
        elseif(preg_match('/\/.{2}\/.*-product-([0-9]+)\.html/', $lang_redirect, $fnd))
            $lang_redirect = cw_call('cw_core_get_html_page_url', array(array_merge($merge_arr, array('var' => 'product', 'pid' => $fnd[1]))));
        elseif(preg_match('/\/.{2}\/.*-manufacturer-([0-9]+)\.html/', $lang_redirect, $fnd))
            $lang_redirect = cw_call('cw_core_get_html_page_url', array(array_merge($merge_arr, array('var' => 'manuf', 'mid' => $fnd[1]))));
        elseif(preg_match('/\/.{2}\/.*-page-([0-9]+)\.html/', $lang_redirect, $fnd))
            $lang_redirect = cw_call('cw_core_get_html_page_url', array(array_merge($merge_arr, array('var' => 'pages', 'page_id' => $fnd[1]))));
        elseif(preg_match('/\/.{2}\/(.*)-help\.html/', $lang_redirect, $fnd))
            $lang_redirect = cw_call('cw_core_get_html_page_url', array(array_merge($merge_arr, array('var' => 'help', 'section' => $fnd[1]))));
        elseif(in_array($target, array('home', 'clearance', 'top_sellers', 'customer_wishes', 'super_deals', 'hot_deals', 'manufacturers', 'help', 'top_rated')))
            $lang_redirect = $current_location.'/'.strtolower($code).'/index.php'.($target != 'home'?'?target='.$target:'');
    }
    else {
*/
        $merge_arr['sl'] = $code;
        $lang_redirect = cw_core_assign_addition_params($_tmp['path'], $merge_arr, array(), '&', false);
//    }

    $lng_urls[$code] = $lang_redirect;
}
$smarty->assign('lng_urls', $lng_urls);

if (count($all_languages)==1) {
    define('ONLY_ONE_LANGUAGE', true);
}

register_shutdown_function("cw_langvars_cache");
