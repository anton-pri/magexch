<?php
/*
 * Vendor: cw
 * addon: custom_magazineexchange_sellers
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\custom_magazineexchange_sellers;

// Constants definition
const addon_name    = 'custom_magazineexchange_sellers';       
const MAG_PAIDOUT_ORDER_STATUS = 'PO';

global $allowed_seller_display_order_statuses;
$allowed_seller_display_order_statuses = array('P','S','PO','C');

// New tables definition
// Magazine Sellers product data
$tables['magazine_sellers_product_data'] = 'cw_magazine_sellers_product_data';
$tables['magexch_sellers_shopfront'] = 'cw_magexch_sellers_shopfront';
$tables['magazine_sellers_pages']        = 'cw_magazine_sellers_pages';

// Register function which is allowed for call from smarty via {tunnel}
$cw_allowed_tunnels[] = 'cw\custom_magazineexchange_sellers\mag_membership_fees';
$cw_allowed_tunnels[] = 'cw\custom_magazineexchange_sellers\mag_product_seller_item_data';
$cw_allowed_tunnels[] = 'cw\custom_magazineexchange_sellers\mag_get_seller_digital_product_sale';
$cw_allowed_tunnels[] = 'cw\custom_magazineexchange_sellers\mag_check_digital_seller_product_in_cart';
$cw_allowed_tunnels[] = 'cw\custom_magazineexchange_sellers\mag_get_shopfront';
$cw_allowed_tunnels[] = 'cw\custom_magazineexchange_sellers\mag_order_owed';
$cw_allowed_tunnels[] = 'cw_seller_get_info';
$cw_allowed_tunnels[] = 'cw\custom_magazineexchange_sellers\mag_category_allowed_by_seller_memberships';

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');
cw_include('addons/'.addon_name.'/include/func.users.php');

// Part of initialization must be after all addons init - in post_init.php
cw_set_controller('init/post_init.php', 'addons/'.addon_name.'/post_init.php', EVENT_POST);
cw_set_controller('init/abstract.php',  'addons/'.addon_name.'/abstract.php', EVENT_POST);

// Replace seller name to seller username
cw_set_hook('cw_seller_get_info','cw\\'.addon_name.'\\cw_seller_get_info',EVENT_POST);

if (APP_AREA == 'admin') {
    // Seller info on order details
    cw_set_controller('include/orders/order.php',  'addons/' . addon_name . '/include/order.php', EVENT_POST);
}

cw_addons_set_template(
    array('pre','admin/docs/doc_layout.tpl','addons/'.addon_name.'/main/orders/seller_info.tpl','admin/docs/doc.tpl')
);
cw_addons_set_template(
    array('post','admin/docs/layout/top.tpl','addons/'.addon_name.'/main/orders/seller_info.tpl','admin/docs/doc_layout.tpl')
);

// Update seller stock when order status changed
cw_event_listen('on_accounting_update_stock','cw\\'.addon_name.'\\on_accounting_update_stock');

cw_event_listen('on_user_delete', 'cw\\'.addon_name.'\\cw_seller_delete_profile');


// You can define different hooks depending on area or $target or use common init sequence. 
if (APP_AREA == 'admin') {
    // Add percentage form to memberships management
    cw_set_controller('admin/memberships.php', 'addons/' . addon_name . '/admin/memberships.php', EVENT_POST);
    cw_addons_set_template(
        array('post','admin/memberships/membership_edit.tpl', 'addons/'.addon_name.'/admin/memberships/membership_edit.tpl')
    );

    cw_addons_set_template(
        array('replace', 'admin/products/category/modify.tpl@category_modify_form_end', 'addons/' .addon_name. '/admin/seller_membership_access.tpl') 
    );

    // Promotion pages field on seller profile
    cw_set_controller('admin/user_V.php', 'addons/' . addon_name . '/admin/user_V.php', EVENT_PRE);
    cw_addons_set_template(
        array('post','admin/users/sections/custom.tpl', 'addons/'.addon_name.'/admin/users/sections/custom.tpl', 'admin/users/sections/basic.tpl')
    );

    cw_set_controller('admin/magexch_flat_charges.php',    'addons/' . addon_name . '/admin/flat_charges.php', EVENT_REPLACE);
    cw_addons_set_template(
        array('replace', 'admin/main/magexch_flat_charges.tpl', 'addons/' . addon_name . '/admin/flat_charges.tpl')
    );

    cw_addons_add_css('addons/'.addon_name.'/admin/main.css');

}
if (APP_AREA == 'customer') {
    // Get additional sellers data on product page
    cw_set_controller('customer/product.php', 'addons/'.addon_name.'/customer/product.php', EVENT_POST);

    cw_addons_set_hooks(
        array('post','cw_auth_check_security_targets', 'cw\\'.addon_name.'\\cw_auth_check_security_targets')
    );

    cw_set_controller('customer/seller_getfile.php', 'addons/'.addon_name.'/include/seller_getfile.php', EVENT_REPLACE);    

 
    // Add correct price and seller_id to product in cart
    cw_set_hook('cw_add_to_cart', 'cw\\'.addon_name.'\\cw_add_to_cart', EVENT_PRE);
    
    // Update qty in cart basing on available amount of specific seller.
    cw_set_hook('cw_update_quantity_in_cart', 'cw\\'.addon_name.'\\cw_update_quantity_in_cart', EVENT_PRE);

    // when cart calculated restore price settled by seller
    cw_event_listen('on_product_from_scratch', 'cw\\'.addon_name.'\\on_product_from_scratch');
    
    // handle on_build_cart_product_hash() - hash should depend on seller_item_id passed with add2cart request
    cw_event_listen('on_build_cart_product_hash', 'cw\\'.addon_name.'\\on_build_cart_product_hash');
    // hash should depend on dynamic seller_id passed with add2cart request
    cw_event_listen('on_build_order_hash', 'cw\\'.addon_name.'\\on_build_order_hash');

    cw_set_hook('cw_doc_prepare_doc_item_extra_data', 'cw\\'.addon_name.'\\cw_doc_prepare_doc_item_extra_data', EVENT_POST);
   
}

if (APP_AREA == 'seller') {
    // Change products list layout
    cw_addons_set_template(array('replace','main/products/products.tpl', 'addons/'.addon_name.'/main/products/products.tpl'));
    
    // Add comissions to membership
    cw_addons_set_template(array('post',
        'main/select/membership.tpl', 'addons/'.addon_name.'/main/select/membership.tpl', 'addons/' . seller_addon_name . '/sections/basic.tpl')
    );

    // Switch status to complete when tracking number entered
    cw_set_controller('include/orders/order.php',  'addons/' . addon_name . '/seller/order.php', EVENT_PRE);
   
    // Block product modify page
    cw_set_controller('include/products/modify.php',  'addons/' . addon_name . '/seller/products.php', EVENT_PRE);

    cw_set_controller('seller/seller_getfile.php', 'addons/'.addon_name.'/include/seller_getfile.php', EVENT_REPLACE);
    
    // Handle products update request;
    // Add seller data to products list
    cw_set_controller('include/products/search.php',  'addons/' . addon_name . '/seller/products.php', EVENT_POST);
    cw_set_controller('include/products/process.php', 'addons/' . addon_name . '/seller/products.php', EVENT_PRE);
    cw_set_controller('seller/seller_add_product.php', 'addons/' . addon_name . '/seller/add_product.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_category_selector.php', 'addons/' . addon_name . '/seller/category_selector.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_product_images.php', 'addons/' . addon_name . '/seller/product_images.php', EVENT_REPLACE);

    // Seller page request
    cw_set_controller('seller/seller_newpage_req.php',      'addons/' . addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_add_bulk_issues.php',  'addons/' . addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_add_single_issue.php', 'addons/' . addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_about_title_basic.php','addons/' . addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_about_title_custom.php','addons/'. addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_payment_info.php',     'addons/' . addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_wanted_items.php', 'addons/' . addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);
    cw_set_controller('seller/seller_collections_available.php', 'addons/' . addon_name . '/seller/seller_external_pages.php', EVENT_REPLACE);

    // Support of unique username, which can be used for login
    cw_event_listen('on_register_validate',     'cw\\'.addon_name.'\on_register_validate');
//    cw_set_hook('cw_check_user_field_username', 'cw\\'.addon_name.'\cw_check_user_field_username', EVENT_REPLACE);
    cw_set_hook('cw_user_create_profile',       'cw\\'.addon_name.'\cw_user_create_profile', EVENT_POST);

    cw_set_hook('dashboard_get_sections_list', 'cw\\'.addon_name.'\dashboard_get_sections_list', EVENT_REPLACE);

    cw_set_controller('include/login.php',      'addons/' . addon_name . '/include/login.php', EVENT_PRE);
    cw_addons_set_template(
        array('replace', 'addons/seller/acc_manager/register.tpl', 'addons/'.addon_name.'/seller/acc_manager/register.tpl')
    );

    // Promotion pages
    $cw_trusted_variables[] = 'html_section_content';
    cw_set_controller('seller/cms.php',      'addons/' . addon_name . '/seller/cms.php', EVENT_REPLACE);

    /* See also post_init.php */

    cw_set_controller('seller/digital_products.php',        'addons/' . addon_name . '/seller/digital_products.php', EVENT_REPLACE);  

    cw_set_controller('seller/seller_shopfront.php',        'addons/' . addon_name . '/seller/seller_shopfront.php', EVENT_REPLACE);
    cw_addons_set_template(array('replace','seller/main/seller_shopfront.tpl', 'addons/'.addon_name.'/main/seller_shopfront.tpl'));
 
    cw_addons_set_template(array('replace','seller/products/digital_products_disabled.tpl', 'addons/'.addon_name.'/products/digital_products_disabled.tpl'));
    cw_set_controller('seller/popup_files.php', 'admin/popup_files.php', EVENT_REPLACE);
 
    cw_addons_add_css('addons/'.addon_name.'/seller/main.css');

    cw_set_hook('cw_get_file_storage_locations', 'cw\\'.addon_name.'\\cw_get_file_storage_locations', EVENT_POST);

    cw_addons_set_template(array('replace', 'main/products/search.tpl@product_search_form_title', 'addons/'.addon_name.'/products/search_form_title.tpl'));
    
    cw_set_controller('include/orders/orders.php',  'addons/' . addon_name . '/seller/orders_pre.php', EVENT_PRE);
    cw_event_listen('on_prepare_statuses_list','cw\\'.addon_name.'\\on_prepare_statuses_list');

    cw_event_listen('on_prepare_search_products','cw\\'.addon_name.'\\on_prepare_search_products');

    cw_addons_set_template(
        array('post', 'admin/products/search_form.tpl@search_in_params', 'addons/'.addon_name.'/products/my_products_search.tpl')
    );
   
}
/*
cw_addons_set_template(
    array('replace', 'mail/seller_modified_product_subj.tpl', 'addons/'.addon_name.'/mail/seller/modified_product_subj.tpl'),
    array('replace', 'mail/seller_modified_product.tpl', 'addons/'.addon_name.'/mail/seller/modified_product.tpl')
);
*/
if (APP_AREA == 'seller' || APP_AREA == 'admin') {

    cw_set_hook('cw_check_user_field_username', 'cw\\'.addon_name.'\cw_check_user_field_username', EVENT_REPLACE);

    // Add "owed" column to orders list
    cw_set_controller('include/orders/orders.php',  'addons/' . addon_name . '/seller/orders.php', EVENT_POST);

    cw_addons_set_template(
        array('post','main/docs/extras_title.tpl', 'addons/'.addon_name.'/main/orders/extras.tpl'),
        array('post','main/docs/extras.tpl', 'addons/'.addon_name.'/main/orders/extras.tpl'),
        array('post','main/orders/orders_list.tpl@orders_list_total', 'addons/'.addon_name.'/main/orders/orders_list.tpl@orders_list_total')
        
    );
}


