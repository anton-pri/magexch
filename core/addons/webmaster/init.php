<?php
/*
 * Vendor: cw
 * addon: webmaster
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\webmaster;

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'webmaster';       
const addon_target  = 'webmaster'; // Main target of addon, useful but of course addon can handle several targets

// Include functions
cw_include_once('addons/'.addon_name.'/include/func.php');

/**
 * FEATURE core: main addon mechanism to be extended by other addons
 */
if (is_feature('core')) {

    // Controller show and handle edit form
    cw_set_controller(APP_AREA.'/'.addon_target.'.php','addons/'.addon_name.'/admin/'.addon_target.'.php', EVENT_REPLACE);
    
    // Create separate compiled templates dir
    cw_set_controller('init/smarty.php','addons/'.addon_name.'/init/smarty.php', EVENT_POST);
    

} // FEATURE core.

if (APP_AREA == 'admin') {
    cw_set_controller('include/check_useraccount.php','addons/'.addon_name.'/include/check_useraccount.php', EVENT_POST);
}

if (is_feature('langvar')) {
    cw_event_listen('on_webmaster_view_langvar', 'cw\\'.addon_target.'\webmaster_view_langvar');
    cw_event_listen('on_webmaster_modify_langvar', 'cw\\'.addon_target.'\webmaster_modify_langvar');
}

if (is_feature('cms')) {
    cw_event_listen('on_webmaster_view_cms', 'cw\\'.addon_target.'\webmaster_view_cms');
    cw_event_listen('on_webmaster_modify_cms', 'cw\\'.addon_target.'\webmaster_modify_cms');
}

if (is_feature('attribute_name')) {
    cw_event_listen('on_webmaster_view_attribute_name', 'cw\\'.addon_target.'\webmaster_view_attribute_name');
    cw_event_listen('on_webmaster_modify_attribute_name', 'cw\\'.addon_target.'\webmaster_modify_attribute_name');   
}

if (is_feature('attribute_value')) {
    cw_event_listen('on_webmaster_view_attribute_value', 'cw\\'.addon_target.'\webmaster_view_attribute_value');
    cw_event_listen('on_webmaster_modify_attribute_value', 'cw\\'.addon_target.'\webmaster_modify_attribute_value');
}

if (is_feature('custom_facet_desc')) {
    cw_event_listen('on_webmaster_view_custom_facet_desc', 'cw\\'.addon_target.'\webmaster_view_custom_facet_desc');
    cw_event_listen('on_webmaster_modify_custom_facet_desc', 'cw\\'.addon_target.'\webmaster_modify_custom_facet_desc');
}

if (is_feature('custom_facet_title')) {
    cw_event_listen('on_webmaster_view_custom_facet_title', 'cw\\'.addon_target.'\webmaster_view_custom_facet_title');
    cw_event_listen('on_webmaster_modify_custom_facet_title', 'cw\\'.addon_target.'\webmaster_modify_custom_facet_title');
}

if (is_feature('products_images_det')) {
    cw_event_listen('on_webmaster_view_products_images_det', 'cw\\'.addon_target.'\webmaster_view_images');
    cw_event_listen('on_webmaster_modify_products_images_det', 'cw\\'.addon_target.'\webmaster_modify_images');
}

if (is_feature('products_images_thumb')) {
    cw_event_listen('on_webmaster_view_products_images_thumb', 'cw\\'.addon_target.'\webmaster_view_images');
    cw_event_listen('on_webmaster_modify_products_images_thumb', 'cw\\'.addon_target.'\webmaster_modify_images');
}

if (is_feature('cms_images')) {
    cw_event_listen('on_webmaster_view_cms_images', 'cw\\'.addon_target.'\webmaster_view_images');
    cw_event_listen('on_webmaster_modify_cms_images', 'cw\\'.addon_target.'\webmaster_modify_images');
}

if (is_feature('product_fulldescr')) {
    cw_event_listen('on_webmaster_view_product_fulldescr', 'cw\\'.addon_target.'\webmaster_view_product_fulldescr');
    cw_event_listen('on_webmaster_modify_product_fulldescr', 'cw\\'.addon_target.'\webmaster_modify_product_fulldescr');
}

if (is_feature('product_descr')) {
    cw_event_listen('on_webmaster_view_product_descr', 'cw\\'.addon_target.'\webmaster_view_product_descr');
    cw_event_listen('on_webmaster_modify_product_descr', 'cw\\'.addon_target.'\webmaster_modify_product_descr');
}

if (is_feature('manufacturer_descr')) {
    cw_event_listen('on_webmaster_view_manufacturer_descr', 'cw\\'.addon_target.'\webmaster_view_manufacturer_descr');
    cw_event_listen('on_webmaster_modify_manufacturer_descr', 'cw\\'.addon_target.'\webmaster_modify_manufacturer_descr');
}

if (is_feature('magazine_review')) {
    cw_event_listen('on_webmaster_view_magazine_review', 'cw_magazines_webmaster_view_magazine_review');
    cw_event_listen('on_webmaster_modify_magazine_review', 'cw_magazines_webmaster_modify_magazine_review');
}

if (is_feature('magazine_rating')) {
    cw_event_listen('on_webmaster_view_magazine_rating', 'cw_magazines_webmaster_view_magazine_rating');
    cw_event_listen('on_webmaster_modify_magazine_rating', 'cw_magazines_webmaster_modify_magazine_rating');
}

// Add addon CSS style
cw_addons_add_css('addons/'.addon_name.'/webmaster.css');
// Add addon JS
cw_addons_add_js('addons/'.addon_name.'/webmaster.js');


/** SERVICE FUNCTION **/
// List of hardcoded enabled features
function is_feature($feature) {
    $features = array(
        'core' => 1,
    );

    $features['langvar'] = $features['core'] && 1;
    $features['cms'] = $features['core'] && 1;
    $features['attribute_name'] = $features['core'] && 1;
    $features['attribute_value'] = $features['core'] && 1;
    $features['custom_facet_desc'] = $features['core'] && 1;
    $features['custom_facet_title'] = $features['core'] && 1;
    $features['products_images_det'] = $features['core'] && 1;
    $features['products_images_thumb'] = $features['core'] && 1;
    $features['cms_images'] = $features['core'] && 1;
    $features['product_fulldescr'] = $features['core'] && 1;
    $features['product_descr'] = $features['core'] && 1;
    $features['manufacturer_descr'] = $features['core'] && 1;
    $features['magazine_review'] = $features['core'] && 1;
    $features['magazine_rating'] = $features['core'] && 1;

    return $features[$feature];
}
