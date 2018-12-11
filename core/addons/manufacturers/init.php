<?php
cw_include('addons/manufacturers/include/func.manufacturer.php');

cw_addons_set_controllers(
    array('replace', 'admin/manufacturers.php', 'addons/manufacturers/admin/manufacturers.php'),
    array('replace', 'customer/manufacturers.php', 'addons/manufacturers/customer/manufacturers.php')
);

cw_addons_set_hooks(
    array('post', 'cw_product_search', 'cw_manufacturers_product_search'),
    array('post', 'cw_product_get', 'cw_manufacturers_product_get'),
    array('post', 'cw_core_get_last_title_part', 'cw_manufacturers_get_last_title_part')
);
cw_set_hook('cw_doc_get_extras_data', 'cw_manufacturers_doc_get_extras_data', EVENT_POST);

cw_event_listen('on_prepare_search_orders', 'cw_manufacturers_prepare_search_orders');
cw_event_listen('on_prepare_search_users', 'cw_manufacturers_prepare_search_users');

// CMS addon
cw_event_listen('on_cms_check_restrictions','cw_manufacturers_on_cms_check_restrictions_M');

cw_addons_set_template(
# kornev, add manufacturer selector
    array('post', 'main/attributes/default_types.tpl', 'addons/manufacturers/main/attributes/manufacturer-selector.tpl'),
# redefine the search in the admin/customer area
    array('post', 'customer/products/search_form_adv.tpl', 'addons/manufacturers/customer/products/search_form_adv.tpl'),
    array('post', 'main/products/search_form.tpl', 'addons/manufacturers/main/products/search_form.tpl'),

    array('pre', 'customer/products/product-fields.tpl', 'addons/manufacturers/customer/products/product-fields.tpl'),
    array('replace', 'main/attributes/show.tpl', 'addons/manufacturers/main/attributes/show.tpl', 'cw_manufacturers_is_manufacturer_attribute'),

    array('post', 'main/docs/extras.tpl', 'addons/manufacturers/main/attributes/extras.tpl'),
    array('post', 'main/docs/extras_title.tpl', 'addons/manufacturers/main/attributes/extras.tpl'),
    array('pre', 'main/docs/additional_search_field.tpl', 'addons/manufacturers/main/attributes/additional_manufacturer_search_field.tpl'),
    array('post', 'main/users/search_form.tpl@user_search_by_orders', 'addons/manufacturers/main/users/search_form.tpl')


);

if (APP_AREA == 'customer') {
    cw_addons_set_controllers(
        array('post', 'customer/product.php', 'addons/manufacturers/customer/product.php')
    );
}

cw_addons_add_js('jquery/jquery-listnav-2.2.min.js');
