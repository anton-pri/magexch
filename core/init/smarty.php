<?php
umask(0);

define('SMARTY_DIR', $app_main_dir.'/include/lib/smarty/');
ini_set('include_path', $app_main_dir . '/include/templater/'.PATH_SEPARATOR.SMARTY_DIR.PATH_SEPARATOR.ini_get('include_path'));

cw_include('include/templater/templater.php');

$cw_allowed_tunnels = array_merge((array)$cw_allowed_tunnels, array(
    'cw_attributes_get_all_classes_for_products', 
    'cw_attributes_get_all_for_products', 
    'cw_attributes_get_types', 
    'cw_barcode_get_templates', 
    'cw_category_get_short_list',
    'cw_get_all_categories_for_select', 
    'cw_clean_url_get_html_page_url',
    'cw_core_get_meta', 
    'cw_currency_get_list', 
    'cw_doc_get_order_status_color', 
    'cw_doc_get_order_status_email', 
    'cw_get_langvar_by_name', 
    'cw_import_smarty_layouts', 
    'cw_localization_get_list', 
    'cw_manufacturer_get', 
    'cw_manufacturer_get_list_smarty', 
    'cw_manufacturer_get_smarty', 
    'cw_map_get_counties_smarty', 
    'cw_map_get_regions_smarty', 
    'cw_map_get_states_smarty', 
    'cw_map_get_location_by_zip',
    'cw_md_get_domains', 
    'cw_pos_get_list_smarty', 
    'cw_product_classes_list', 
    'cw_product_get', 
    'cw_product_get_types', 
    'cw_pt_get_tab_content', 
    'cw_salesman_get_list_smarty', 
    'cw_user_get_addresses_smarty', 
    'cw_user_get_usertypes',
    'cw_warehouse_get_divisions', 
    'cw_web_get_layout_elements',
    'cw_web_get_product_layout_elements',
    'cw_config_advanced_search_attributes',
    'cw_attributes_get',
    'cw_admin_forms_display_get',
    'cw_check_if_breadcrumbs_enabled',
    'cw_user_search_get_register_fields',
    'cw_user_get_info'
));

global $smarty;
$smarty = new Templater;

// Redefine smarty properties
if (!empty($app_config_file['smarty'])) {
    foreach ($app_config_file['smarty'] as $param=>$value) {
        $smarty->$param = $value;
    }
}

$smarty->use_sub_dirs = false;
$smarty->request_use_auto_globals = false;
$smarty->template_dir = cw_func_call('cw_code_get_template_dir');

foreach ((array)$smarty->template_dir as $s) {
    cw_addons_scan_skin($s);
}

$skin_name = with_leading_slash_only(is_array($smarty->template_dir)?basename($smarty->template_dir[0]):basename($smarty->template_dir));
$var_dirs['templates']  .= $skin_name;
$var_dirs['cache']      .= $skin_name;
$var_dirs_web['cache']  .= $skin_name; 

$smarty->compile_dir = $var_dirs['templates'];
$smarty->config_dir = $app_dir.$app_config_file['web']['skin'];
$smarty->cache_dir = $var_dirs['cache'];
$smarty->secure_dir[] = $app_dir.$app_config_file['web']['skin'];
$smarty->secure_dir[] = $app_dir.'/upgrade';
$smarty->debug_tpl = 'file:debug/debug_templates.tpl';

$smarty->assign('ImagesDir', $app_web_dir.$app_config_file['web']['skin'].'/images');
$smarty->assign('SkinDir', $app_web_dir.$app_config_file['web']['skin']);
$smarty->assign('template_dir', $smarty->template_dir);

$smarty->assign('APP_SESS_NAME', APP_SESSION_NAME);
$smarty->assign('APP_SESS_ID', $APP_SESS_ID);
