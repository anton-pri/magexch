<?php
/*
 * Vendor: CW
 * addon: custom_magazineexchange
 */ 

const magazineexchange_addon_name = 'custom_magazineexchange';

$cw_allowed_tunnels[] = 'cw_category_get';
$cw_allowed_tunnels[] = 'magexch_get_attribute_value';
$cw_allowed_tunnels[] = 'magexch_get_section_category_id';
$cw_allowed_tunnels[] = 'magexch_get_cms_by_tab_content_id';
$cw_allowed_tunnels[] = 'magexch_get_breadcrumbs';
$cw_allowed_tunnels[] = 'magexch_get_prev_next_category_ids';
$cw_allowed_tunnels[] = 'magexch_select_subcategories';
$cw_allowed_tunnels[] = 'magexch_get_extra_categories';
$cw_allowed_tunnels[] = 'magexch_load_custom_template_content';
$cw_allowed_tunnels[] = 'magexch_filter_categories_by_vendor';
$cw_allowed_tunnels[] = 'magexch_seller_feedback_customer_info';
$cw_allowed_tunnels[] = 'magexch_seller_total_rating';
$cw_allowed_tunnels[] = 'magexch_shopfront_feedbacks';
$cw_allowed_tunnels[] = 'magexch_get_admin_customer_id';

$tables['categories_extra'] = 'cw_categories_extra';
$tables['magexch_sellers_feedback'] = 'cw_magexch_sellers_feedback';

cw_include('addons/'.magazineexchange_addon_name.'/include/func.php');

cw_addons_set_hooks(
    array('replace', 'cw_replace_email_with_test', 'magexch_replace_email_with_test'),
    array('post', 'cw_user_search_get_register_fields', 'magexch_user_search_get_register_fields')
);

if (APP_AREA == 'customer') {

    cw_set_controller('init/post_init.php', 'addons/' . magazineexchange_addon_name . '/post_init.php', EVENT_POST);
    cw_addons_set_controllers(
        array('replace', 'customer/classifieds.php', 'addons/' . magazineexchange_addon_name . '/customer/classifieds.php'),
        array('replace', 'customer/magexch_seller_feedback.php', 'addons/' . magazineexchange_addon_name . '/customer/seller_feedback.php'), 
        array('post', 'customer/docs_O.php', 'addons/' . magazineexchange_addon_name . '/customer/docs_O.php'),
        array('post', 'customer/index.php', 'addons/' . magazineexchange_addon_name . '/customer/category_custom_fields.php'),
        array('post', 'addons/mobile/init/mobile.php', 'addons/' . magazineexchange_addon_name . '/customer/mobile.php')
    );

    cw_addons_set_hooks(
        array('pre', 'cw_product_search', 'magexch_sort_by_month_time_of_year'),
        array('pre', 'cw_category_search', 'magexch_category_search'),
        array('post', 'cw_product_get', 'magexch_product_get'),
        array('post', 'top_menu_smarty_init', 'magexch_top_menu_smarty_init'),
        array('post', 'cw_ppd_tabs_js_abstract', 'magexch_ppd_tabs'),
        array('post', 'cw_core_get_html_page_url', 'magexch_get_vendor_html_page_url'),
        array('pre', 'cw_product_search', 'magexch_filter_products_by_vendor'),
        array('post', 'cw_seller_get_info', 'magexch_seller_get_info')
    );

    cw_event_listen('on_prepare_search_products','magexch_on_prepare_search_products');

    cw_addons_set_template(
        array('replace', 'customer/top_menu.tpl', 'customer/empty.tpl'),
        array('replace', 'customer/head.tpl@minicart', 'customer/empty.tpl'),
        array('replace', 'customer/head.tpl@top_auth', 'customer/empty.tpl'),
        array('post', 'customer/head.tpl@top_categories', 'customer/top_image.tpl'),
        array('pre', 'customer/search.tpl', 'customer/phone.tpl'),
        array('pre', 'customer/head.tpl@top_categories', 'customer/top_links.tpl'),
        array('replace', 'customer/products/subcategories.tpl@products_list', 'customer/products/products_tabs.tpl'),
        array('replace', 'customer/products/subcategories.tpl@products_top', 'customer/empty.tpl'),
        array('replace', 'customer/products/products_gallery.tpl@list_price', 'customer/empty.tpl'),
        array('replace', 'customer/products/products_gallery.tpl@our_price', 'customer/empty.tpl'),
        array('replace', 'customer/products/products_gallery.tpl@in_stock', 'customer/products/rollover.tpl'),
        array('replace', 'customer/products/product.tpl@product_tabs', 'customer/empty.tpl'),
        array('replace', 'customer/products/product.tpl@products_in_category', 'customer/empty.tpl'),
        array('replace', 'customer/products/product.tpl@product_description', 'customer/empty.tpl'),
        array('replace', 'customer/products/product.tpl@product_actions', 'customer/empty.tpl'),
        array('replace', 'customer/products/availability.tpl', 'customer/empty.tpl'),
        array('replace', 'customer/products/product.tpl@send_to_friend', 'customer/empty.tpl'),
        array('post', 'customer/products/product.tpl@product_dialog', 'customer/products/extra_blocks.tpl'),
        array('pre',  'customer/products/product.tpl@product_dialog', 'customer/prev_page.tpl'),
        array('post', 'customer/products/product.tpl@product_name', 'customer/products/custom_product_tabs.tpl'),
        array('replace', 'customer/cart/cart.tpl@discount_coupon', 'customer/empty.tpl'),
        array('post', 'customer/cart/cart.tpl@cart_content', 'customer/cart/totals.tpl'),
        array('replace', 'customer/cart/cart.tpl@cart_buttons', 'customer/empty.tpl'),
        array('replace', 'customer/cart/cart.tpl@cart_note', 'customer/empty.tpl'),
        array('replace', 'customer/cart/totals.tpl@vat', 'customer/empty.tpl'),
        array('replace', 'customer/products/subcategories.tpl@category_select_subcategories', 'customer/select_subcategories.tpl'),
        array('post', 'addons/cms/customer/display_static_content.tpl', 'customer/close_button.tpl'),
        array('post', 'main/users/fields/fax.tpl', 'addons/'.magazineexchange_addon_name.'/main/users/sections/address_modify_post.tpl'),
        array('pre', 'customer/checkout/create.tpl@checkout_reg_form', 'addons/'.magazineexchange_addon_name.'/customer/checkout/checkout_reg_form_pre.tpl'),
        array('post', 'customer/checkout/create.tpl@checkout_reg_form', 'addons/'.magazineexchange_addon_name.'/customer/checkout/checkout_reg_form_post.tpl'),
        array('post', 'customer/checkout/address.tpl',  'addons/'.magazineexchange_addon_name.'/customer/checkout/address_post.tpl'),
        array('post', 'customer/checkout/place.tpl', 'addons/'.magazineexchange_addon_name.'/customer/checkout/place_post.tpl') 
    );

    if ($vendorid) {
        cw_addons_set_template(array('post', 'customer/main/location.tpl', 'addons/'.magazineexchange_addon_name.'/customer/vendor_head.tpl'));
        cw_addons_add_css('addons/'.magazineexchange_addon_name.'/vendor.css');
    } 

    if (in_array($target,['acc_manager', 'docs_O', 'message_box'])) {
        cw_addons_set_controllers(
            array('post', 'customer/auth.php', 'addons/'.magazineexchange_addon_name.'/customer/enable_left_bar.php') 
        );

        cw_addons_set_template(
            array('replace', 'customer/menu/menu_sections.tpl', 'addons/'.magazineexchange_addon_name.'/customer/menu/profile_menu_sections.tpl')
        ); 
    }
    if ($target == 'message_box') {
        cw_addons_add_js('js/custom_checkbox.js');
        cw_addons_add_css('css/custom_checkbox.css');
        if ($action == 'show') {
            cw_addons_set_template(
                array('replace', 'common/section.tpl', 'customer/wrappers/no_box.tpl')
            ); 
        } else {
            cw_addons_set_template(
                array('replace', 'common/section.tpl', 'customer/wrappers/jablock.tpl')
            );
        }
    }

} elseif (APP_AREA == 'admin') {
    cw_addons_set_controllers(
        array('replace', 'admin/magexch_after_import.php', 'addons/' . magazineexchange_addon_name . '/magexch_after_import.php'),
        array('replace', 'admin/magexch_pages_import.php', 'addons/' . magazineexchange_addon_name . '/magexch_pages_import.php'),
        array('replace', 'admin/magexch_categories_recals.php', 'addons/' . magazineexchange_addon_name . '/magexch_categories_recals.php'),
        array('pre', 'include/categories/modify.php', 'addons/' . magazineexchange_addon_name . '/save_extra_categories.php'),

        array('replace', 'admin/magexch_transfer_passwords.php', 'addons/' . magazineexchange_addon_name . '/magexch_transfer_passwords.php'),
        array('replace', 'admin/magexch_transfer_orders.php', 'addons/' . magazineexchange_addon_name . '/magexch_transfer_orders.php'),

        array('replace', 'admin/magexch_images_transfer.php', 'addons/' . magazineexchange_addon_name . '/magexch_images_transfer.php'),

        array('replace', 'admin/magexch_xccw.php', 'addons/' . magazineexchange_addon_name . '/magexch_xccw.php')

    );

    cw_addons_set_template(
        array('replace', 'admin/products/category/modify.tpl@category_location_select', 'addons/' . magazineexchange_addon_name . '/admin/extra_parent_categories_select.tpl') 
    );
    cw_addons_set_template(
        array('replace', 'elements/favicon.tpl', 'addons/' . magazineexchange_addon_name . '/admin/elements/favicon.tpl')
    );

    cw_addons_set_template(
        array('replace', 'addons/flexible_import/flexible_import.tpl@extra_flexible_import_controls', 'addons/' . magazineexchange_addon_name . '/admin/after_import.tpl')
    );
   
}

if (APP_AREA != 'customer') {
    cw_set_hook('cw_core_get_config',       'magexch_core_get_config',            EVENT_POST);
}
