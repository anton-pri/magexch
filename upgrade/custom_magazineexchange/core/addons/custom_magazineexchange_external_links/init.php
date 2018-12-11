<?php
/*
 * Vendor: cw
 * addon: custom_magazineexchange_external_links
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\custom_magazineexchange_external_links;

/** COMMON **/

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'custom_magazineexchange_external_links';       

// New tables definition
$tables['magazine_external_links'] = 'cw_magazine_external_links';

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');


/** FEATURES **/


/**
 * FEATURE: External links for products
 */
if (get_custom_feature('external_links')) {

    if (APP_AREA == 'admin') {
        
        // Links management
        cw_set_controller('admin/products.php','addons/'.addon_name.'/admin/product.php', EVENT_PRE);

        // Add new tab on product modify
        cw_addons_set_hooks(
            array('post', 'cw_tabs_js_abstract', 'cw\\'.addon_name.'\\cw_tabs_js_abstract')
        );
    
        // Keep DB consistency
        cw_set_hook('cw_product_clone',     'cw\\'.addon_name.'\\cw_product_clone',   EVENT_POST);
        cw_set_hook('cw_delete_product',    'cw\\'.addon_name.'\\cw_delete_product', EVENT_POST);
        
        // Export/Import table to CSV
        cw_event_listen('on_export_tables_list', 'cw\\'.addon_name.'\\on_export_tables_list');

    }

    if (APP_AREA == 'customer') {
        // Get links for product page
        cw_set_controller('customer/product.php','addons/'.addon_name.'/customer/product.php', EVENT_POST);
    }

} // External links for products



/** SERVICE FUNCTION **/
// List of enabled features
function get_custom_feature($feature) {
    $features = array(
        'external_links' => 1,
    );

    return $features[$feature];
}
