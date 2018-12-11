<?php
const seller_addon_name = 'seller';
const seller_area_letter = 'V';

$cw_allowed_tunnels[] = 'cw_seller_get_info';

cw_include('addons/' . seller_addon_name . '/include/func.seller.php');

cw_set_hook('cw_product_update_status', 'cw_seller_product_update_status', EVENT_PRE);

// Replace warehouse_id to seller customer id
cw_addons_set_hooks(array('post', 'cw_cart_summarize', 'cw_seller_cart_summarize'));

cw_event_listen('on_doc_change_status_emails_send', 'cw_seller_on_doc_change_status_emails_send');

/*
cw_addons_set_template(
    array('replace', 'mail/docs/seller_subj.tpl', 'addons/' . seller_addon_name . '/mail/docs/seller_subj.tpl'),
    array('replace', 'mail/docs/seller.tpl', 'addons/' . seller_addon_name . '/mail/docs/seller.tpl'),
    array('replace', 'mail/docs/status_changed_seller_subj.tpl', 'addons/' . seller_addon_name . '/mail/docs/status_changed_seller_subj.tpl'),
    array('replace', 'mail/docs/status_changed_seller.tpl', 'addons/' . seller_addon_name . '/mail/docs/status_changed_seller.tpl')
);
*/

cw_set_hook('cw_doc_order_status_emails', 'cw_seller_doc_order_status_emails', EVENT_POST);

if (APP_AREA == 'customer') {
    cw_addons_set_controllers(
        array('pre', APP_AREA . '/search.php', 'addons/' . seller_addon_name . '/core/search.php')
    );
    
    cw_event_listen('on_build_order_hash', 'cw_seller_on_build_order_hash');

    cw_addons_set_hooks(
        array('post', 'cw_product_get', 'cw_seller_product_get')
    );

    cw_addons_set_template(
        array('pre', 'customer/products/additional_data.tpl', 'addons/' . seller_addon_name . '/products/seller_owner.tpl'),
        array('pre', 'customer/products/search.tpl', 'addons/' . seller_addon_name . '/products/seller_section.tpl')
    );
}

if (APP_AREA == 'admin') {

    cw_set_controller(APP_AREA . '/user_V.php', 'addons/' . seller_addon_name . '/core/user_V.php', EVENT_REPLACE);

    cw_addons_set_hooks(
        array('post', 'cw_product_get', 'cw_seller_product_get')
    );

    cw_addons_set_template(
        array('pre', 'main/products/product/details.tpl', 'addons/' . seller_addon_name . '/products/seller.tpl'),
        array('post', 'admin/main/order_statuses.tpl@order_statuses_edit', 'addons/' . seller_addon_name . '/admin/main/order_statuses_edit.tpl'),
        array('post', 'admin/main/preview_order_emails.tpl', 'addons/' . seller_addon_name . '/admin/main/preview_order_emails.tpl')
/*
        array('replace', 'mail/docs/seller_subj.tpl', 'addons/' . seller_addon_name . '/mail/docs/seller_subj.tpl'),
        array('replace', 'mail/docs/seller.tpl', 'addons/' . seller_addon_name . '/mail/docs/seller.tpl'),
        array('replace', 'mail/docs/status_changed_seller_subj.tpl', 'addons/' . seller_addon_name . '/mail/docs/status_changed_seller_subj.tpl'),
        array('replace', 'mail/docs/status_changed_seller.tpl', 'addons/' . seller_addon_name . '/mail/docs/status_changed_seller.tpl')
*/
    );
}

if (APP_AREA == 'seller') {
    
    /* Map controllers from seller area to their real location under addons/seller/core */
    if (!file_exists($app_main_dir.'/seller')) {
        
        // There is no symlink /core/seller, map all files explicitly
        if (!($addon_hooks = cw_cache_get('seller','addon_hooks')) || defined('DEV_MODE')) {
            $addon_hooks = array();
            $files = cw_files_get_dir($app_main_dir.'/addons/seller/core',1,false);
            foreach ($files as $file) {
                if (strpos($file,'.php')!==false) {
                    $file = str_replace($app_main_dir.'/addons/seller/core/','',$file);
                    $addon_hooks[] = $file;
                }
            }
            cw_cache_save($addon_hooks, 'seller', 'addon_hooks');
        }
        
        foreach ($addon_hooks as $h) {
            cw_set_controller("seller/$h", 'addons/' . seller_addon_name . "/core/$h", EVENT_REPLACE);
        }

    } else {
        
        // Symlink is used, everything mapped by filesystem

    }
    
    
    cw_set_controller(APP_AREA . '/products_clients.php', 'admin/products_clients.php', EVENT_REPLACE);
    cw_set_controller(APP_AREA . '/products.php', 'addons/' . seller_addon_name . '/core/search.php', EVENT_PRE);
    
    cw_addons_set_hooks(
        array('pre', 'cw_product_search', 'cw_seller_product_search'),
        array('replace', 'cw_auth_check_security_targets', 'cw_seller_auth_check_security_targets')
    );
    
    cw_set_hook('cw_doc_order_status_emails', 'cw_seller_doc_order_status_emails', EVENT_POST);

    cw_addons_set_template(
        array('replace', APP_AREA . '/main/main.tpl', 'addons/' . seller_addon_name . '/main/main.tpl'),
        array('replace', 'elements/auth_admin.tpl', 'addons/' . seller_addon_name . '/elements/auth.tpl'),
        array('replace', 'main/products/search_form.tpl', 'addons/' . seller_addon_name . '/products/search_form.tpl'),
        array('replace', 'menu/items.tpl', 'addons/' . seller_addon_name . '/menu/items.tpl'),
        array('replace', APP_AREA . '/products/product_modify.tpl', 'addons/' . seller_addon_name . '/products/product_modify.tpl'),
        array('replace', APP_AREA . '/products/product_add.tpl', 'addons/' . seller_addon_name . '/products/product_add.tpl'),
        array('replace', APP_AREA . '/products/search.tpl', 'addons/' . seller_addon_name . '/products/search.tpl'),
        array('replace', APP_AREA . '/products/customers.tpl', 'admin/products/customers.tpl')
    );
    
    // Orders
    cw_addons_set_template(
        array('replace', APP_AREA . '/orders/orders.tpl', 'admin/orders/orders.tpl'),
        array('replace', APP_AREA . '/orders/document.tpl', 'admin/orders/document.tpl'),
        array('replace', APP_AREA . '/main/order_print.tpl', 'admin/orders/order_print.tpl'),
        array('replace', 'main/docs/notes.tpl', 'addons/' . seller_addon_name . '/orders/notes.tpl'),
        array('replace', 'admin/docs/notes.tpl', 'addons/' . seller_addon_name . '/orders/notes.tpl')
        );
    // Profile
    cw_addons_set_template(
        array('replace', 'main/users/sections/basic.tpl', 'addons/' . seller_addon_name . '/sections/basic.tpl'),
        array('replace', APP_AREA . '/acc_manager/acc_manager.tpl', 'addons/' . seller_addon_name . '/acc_manager/acc_manager.tpl'),
        array('replace', APP_AREA . '/acc_manager/modify.tpl', 'addons/' . seller_addon_name . '/acc_manager/modify.tpl'),
      array()
    );
     if ($target=='products' && $mode == 'details') 
		cw_addons_set_template(array('replace', 'main/select/availability.tpl','addons/' . seller_addon_name . '/main/select/availability.tpl', 'cw_seller_product_is_pending'));

    
   
}
