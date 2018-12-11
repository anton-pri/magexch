<?php
/*
 * Vendor: cw
 * addon: flat_attributes_search
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\flat_attributes_search;

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'flat_attributes_search';       

// New tables definition
// Table flat_attributes_search will have dynamic structure
$tables['flat_attributes_search'] = 'cw_flat_attributes_search';

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');

/** FEATURES **/
/* 
   Please group your hooks/controllers/handlers definitions by features
   Use is_feature(<feature_name>) to controll hardcoded availability of feature. 
*/

/**
 * FEATURE product_search: flat attributes for products
 */
if (is_feature('product_search')) {

    cw_event_listen('on_prepare_search_products','cw\\'.addon_name.'\\on_prepare_search_products');

    // Cron handlers. See docs/core.cron.txt and core/cron/cron.php
    cw_event_listen('on_cron_weekly','cw\\'.addon_name.'\\on_cron_rebuild');
    
    cw_addons_set_hooks(
        array('post', 'cw_product_build_flat', 'cw\\'.addon_name.'\\cw_product_build_flat')
    );

} // FEATURE product_search.


/** SERVICE FUNCTION **/
// List of hardcoded enabled features
function is_feature($feature) {
    $features = array(
        'product_search' => 1,
    );

    return $features[$feature];
}

