<?php
/*
 * Vendor: cw
 * addon: product_video
 */

namespace cw\product_video;

// Constants definition
const addon_name = 'product_video';       

// New tables definition
$tables['product_video'] = 'cw_product_video';

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');

/*
// You can define different hooks depending on area or $target or use common init sequence. 
if (APP_AREA == 'admin') {
    // Define own controller which does not exists yet using EVENT_REPLACE
     cw_set_controller(APP_AREA.'/'.addon_target.'.php','addons/'.addon_name.'/admin/'.addon_target.'.php', EVENT_REPLACE);
}
if (APP_AREA == 'customer') {
    // Add own controller to existing one using EVENT_POST or EVENT_PRE
     cw_set_controller(APP_AREA.'/index.php','addons/'.addon_name.'/customer/index.php', EVENT_POST);
}
*/
cw_set_controller('include/products/modify.php', 'addons/'.addon_name.'/admin/product.php', EVENT_PRE);
cw_set_controller('customer/product.php', 'addons/'.addon_name.'/customer/product.php', EVENT_PRE);


/*
// Event handlers
cw_event_listen('on_login','cw\\'.addon_name.'\\on_login'); // specify full function name for event handlers including namespace
*/

// Function hooks
cw_set_hook('cw_delete_product',  'cw\\'.addon_name.'\\cw_delete_product', EVENT_POST);
cw_set_hook('cw_product_clone',  'cw\\'.addon_name.'\\cw_product_clone', EVENT_POST);

cw_addons_set_hooks(
    array('post', 'cw_tabs_js_abstract', 'cw\\'.addon_name.'\\cw_tabs_js_abstract')
);

/*
// Hook templates
cw_addons_set_template(
    array('replace','admin/main/'.addon_target.'.tpl', 'addons/'.addon_name.'/admin/'.addon_target.'.tpl'),
    array('post', 'elements/bottom.tpl', 'addons/'.addon_name.'/customer/my_bottom.tpl'),
    array('pre', 'elements/bottom_admin.tpl', 'addons/'.addon_name.'/admin/my_bottom_admin.tpl')
);
*/

/*
// Add addon CSS style
cw_addons_add_css('addons/'.addon_name.'/my_addon.css');
// Add addon JS
cw_addons_add_js('addons/'.addon_name.'/my_addon.js');
*/
