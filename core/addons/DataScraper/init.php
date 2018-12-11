<?php
/*
 * Vendor: cw
 * addon: DataScraper
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\DataScraper;

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'DataScraper';       

// New tables definition
$tables['datascraper_sites_config'] = 'cw_datascraper_sites_config';
$tables['datascraper_attributes'] = 'cw_datascraper_attributes';
$tables['datascraper_result_values'] = 'cw_datascraper_result_values_';


global $WGET_DOWNLOADS_PATH;
$WGET_DOWNLOADS_PATH = $app_dir."/files/DataScraper/wget_rec";

// Register function which is allowed for call from smarty via {tunnel}
//$cw_allowed_tunnels[] = 'cw\DataScraper\get_available_entries_list';

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');

// Sometimes some part of initialization must be after all addons init - in post_init.php
//cw_set_controller('init/post_init.php', 'addons/'.addon_name.'/post_init.php', EVENT_POST);


/** FEATURES **/
/* 
   Please group your hooks/controllers/handlers definitions by features
   Use is_feature(<feature_name>) to controll hardcoded availability of feature. 
*/

/**
 * FEATURE A: External links for products
 */
//if (is_feature('feature_A')) {

// You can define different hooks depending on area or $target or use common init sequence. 
if (APP_AREA == 'admin') {
    // Define own controller which does not exists yet using EVENT_REPLACE
    /* Place comment here with description of functionality provided by this additional controller */
    cw_set_controller(APP_AREA.'/datascraper_sites.php','addons/'.addon_name.'/admin/sites.php', EVENT_REPLACE);
    cw_set_controller(APP_AREA.'/datascraper_attributes.php','addons/'.addon_name.'/admin/attributes.php', EVENT_REPLACE);
    cw_set_controller(APP_AREA.'/datascraper_results.php','addons/'.addon_name.'/admin/results.php', EVENT_REPLACE);

    cw_set_controller(APP_AREA.'/datascraper_tables_rebuild.php','addons/'.addon_name.'/admin/tables_rebuild.php', EVENT_REPLACE);
    cw_set_controller(APP_AREA.'/datascraper_wget_install.php','addons/'.addon_name.'/admin/wget_install.php', EVENT_REPLACE);
    cw_set_controller(APP_AREA.'/datascraper_results_clean.php','addons/'.addon_name.'/admin/results_clean.php', EVENT_REPLACE);
    cw_set_controller(APP_AREA.'/datascraper_parse_reset.php', 'addons/'.addon_name.'/admin/parse_reset.php', EVENT_REPLACE);

    cw_set_controller(APP_AREA.'/datascraper_results_load.php','addons/'.addon_name.'/admin/results_load.php', EVENT_REPLACE);

    cw_addons_set_controllers(
        array('replace', 'admin/ds_sites.php', 'addons/'.addon_name.'/admin/sites_ajax.php'),
        array('replace', 'admin/ds_attributes.php', 'addons/'.addon_name.'/admin/attributes_ajax.php'),
        array('replace', 'admin/ds_results.php', 'addons/'.addon_name.'/admin/results_ajax.php')
    );

    cw_addons_set_template(
        array('replace','admin/main/datascraper_sites.tpl', 'addons/'.addon_name.'/admin/sites.tpl'),
        array('replace','admin/main/datascraper_attributes.tpl', 'addons/'.addon_name.'/admin/attributes.tpl'),
        array('replace','admin/main/datascraper_results.tpl', 'addons/'.addon_name.'/admin/results.tpl')
    );

    if (in_array($target, array('datascraper_sites', 'datascraper_attributes', 'datascraper_results'))) {
        cw_addons_add_js('addons/'.addon_name.'/javascript/jquery.dataTables.v2.min.js');
        cw_addons_add_js('addons/'.addon_name.'/javascript/dataTables.buttons.min.js');
        cw_addons_add_js('addons/'.addon_name.'/javascript/dataTables.select.min.js');
        cw_addons_add_js('addons/'.addon_name.'/javascript/dataTables.editor.min.js');

        cw_addons_add_css('addons/'.addon_name.'/css/alt.datatables.css');
        cw_addons_add_css('addons/'.addon_name.'/css/jquery.dataTables.min.css');
        cw_addons_add_css('addons/'.addon_name.'/css/buttons.dataTables.min.css');
        cw_addons_add_css('addons/'.addon_name.'/css/select.dataTables.min.css');
        cw_addons_add_css('addons/'.addon_name.'/css/editor.dataTables.min.css');
        cw_addons_add_css('addons/'.addon_name.'/css/base.css');
    }
    global $ds_table_fields;

    $ds_table_fields['sites_config'] = array(
        'siteid' => array('title'=>'#'),   
        'test_url' => array('title'=>'Test URL'),   
        'active' => array('title'=>'Download enabled', 'type'=>'radio', 'is_bool'=>1),
        'wget_path_list' => array('title'=>'Path to downloaded files'),
        'wget_run_hrs' => array('title'=>'Download time'), 
        'wget_run_day' => array('title'=>'Download day of week', 'type'=>'select', 'is_dow'=>1),
        'parsing_active' => array('title'=>'Parsing is enabled', 'type'=>'radio', 'is_bool'=>1) 
    );

    $ds_table_fields['attributes'] = array(
        'ds_attribute_id' => array('title'=>'#','main_display' => 1), 
        'site_id' => array('title'=>'Site', 'type'=>'select','main_display' => 1, 'is_site_select'=>1),
        'name' => array('title'=>'Field name','main_display' => 1),
        'pattern' => array('title'=>'Pattern','type' => 'textarea','main_display' => 0),
        'type' => array('title'=>'data type','main_display' => 1, 'is_type_select'=>1, 'type'=>'select'),
        'table_field' => array('main_display' => 1),
        'table_field_hub' => array('main_display' => 0),
        'mandatory' => array('title'=>'Mandatory on parsed page','main_display' => 1, 'type'=>'radio', 'is_bool'=>1)
    );

}

if (APP_AREA == 'customer') {
    // Add own controller to existing one using EVENT_POST or EVENT_PRE
    /* Place comment here with description of functionality provided by this additional controller */
//     cw_set_controller(APP_AREA.'/index.php','addons/'.addon_name.'/customer/index.php', EVENT_POST);
    cw_set_controller(APP_AREA.'/datascraper_parse.php','addons/'.addon_name.'/parse.php', EVENT_REPLACE);
}

// Event handlers
/* Place comment here with description of functionality provided by this event handler */
//cw_event_listen('on_login','cw\\'.addon_name.'\\on_login'); // specify full function name for event handlers including namespace

// Cron handlers. See docs/core.cron.txt and core/cron/cron.php
//cw_event_listen('on_cron_daily','cw\\'.addon_name.'\\on_cron_daily');

// Function hooks. Note you can use same function name under scope of addon's namespace
/* Place comment here with description of functionality provided by this hook or how it alters default function */
//cw_set_hook('cw_products_in_cart',  'cw\\'.addon_name.'\\cw_products_in_cart', EVENT_POST);

// Hook templates
/*
cw_addons_set_template(
    array('replace','admin/main/'.addon_target.'.tpl', 'addons/'.addon_name.'/admin/'.addon_target.'.tpl'),
    array('post', 'elements/bottom.tpl', 'addons/'.addon_name.'/customer/my_bottom.tpl'),
    array('pre', 'elements/bottom_admin.tpl@label', 'addons/'.addon_name.'/admin/my_bottom_admin.tpl')
);
} // FEATURE A.

// Add addon CSS style
cw_addons_add_css('addons/'.addon_name.'/my_addon.css');
// Add addon JS
cw_addons_add_js('addons/'.addon_name.'/my_addon.js');


/** SERVICE FUNCTION **/
// List of hardcoded enabled features
/*
function is_feature($feature) {
    $features = array(
        'feature_A' => 1,
    );

    return $features[$feature];
}
*/
