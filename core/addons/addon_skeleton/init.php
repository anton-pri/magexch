<?php
/*
 * Vendor: cw
 * addon: addon_skeleton
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\addon_skeleton;

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'addon_skeleton';       
const addon_target  = 'addon_main_target'; // Main target of addon, useful but of course addon can handle several targets

// New tables definition
$tables['addon_skeleton'] = 'cw_addon_skeleton';

// Register function which is allowed for call from smarty via {tunnel}
$cw_allowed_tunnels[] = 'cw\addon_skeleton\get_available_entries_list';

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');

// Sometimes some part of initialization must be after all addons init - in post_init.php
cw_set_controller('init/post_init.php', 'addons/'.addon_name.'/post_init.php', EVENT_POST);


/** FEATURES **/
/* 
   Please group your hooks/controllers/handlers definitions by features
   Use is_feature(<feature_name>) to controll hardcoded availability of feature. 
*/

/**
 * FEATURE A: External links for products
 */
if (is_feature('feature_A')) {

// You can define different hooks depending on area or $target or use common init sequence. 
if (APP_AREA == 'admin') {
    // Define own controller which does not exists yet using EVENT_REPLACE
    /* Place comment here with description of functionality provided by this additional controller */
     cw_set_controller(APP_AREA.'/'.addon_target.'.php','addons/'.addon_name.'/admin/'.addon_target.'.php', EVENT_REPLACE);
}
if (APP_AREA == 'customer') {
    // Add own controller to existing one using EVENT_POST or EVENT_PRE
    /* Place comment here with description of functionality provided by this additional controller */
     cw_set_controller(APP_AREA.'/index.php','addons/'.addon_name.'/customer/index.php', EVENT_POST);
}

// Event handlers
/* Place comment here with description of functionality provided by this event handler */
cw_event_listen('on_login','cw\\'.addon_name.'\\on_login'); // specify full function name for event handlers including namespace

// Cron handlers. See docs/core.cron.txt and core/cron/cron.php
cw_event_listen('on_cron_daily','cw\\'.addon_name.'\\on_cron_daily');

// Function hooks. Note you can use same function name under scope of addon's namespace
/* Place comment here with description of functionality provided by this hook or how it alters default function */
cw_set_hook('cw_products_in_cart',  'cw\\'.addon_name.'\\cw_products_in_cart', EVENT_POST);

// Hook templates
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
function is_feature($feature) {
    $features = array(
        'feature_A' => 1,
    );

    return $features[$feature];
}

