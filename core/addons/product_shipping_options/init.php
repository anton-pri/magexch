<?php

const addon_name = 'product_shipping_options';

$tables['product_shipping_options_values'] = 'cw_product_shipping_options_values';

$cw_allowed_tunnels[] = 'cw_product_shipping_get_options';

// Include functions
cw_include('addons/product_shipping_options/include/func.php');

cw_addons_set_template(
    array('post', 'main/attributes/object_modify.tpl', 'addons/product_shipping_options/main/attributes/shipping-selector.tpl')
);

cw_addons_set_controllers(
    array('pre', 'include/products/modify.php', 'addons/product_shipping_options/include/products/modify-shipping-options.php')
);

cw_addons_set_template(
  array('post', 'customer/cart/product-shipping-options.tpl','addons/product_shipping_options/customer/cart/product-shipping-options.tpl')
);

cw_addons_set_hooks(
    array('post', 'cw_shipping_get_rates', 'cw_product_shipping_option_shipping_get_rates')
);

cw_set_hook('cw_doc_prepare_doc_item_extra_data', 'cw_product_shipping_option_extra_data', EVENT_POST);

if (APP_AREA == 'customer') {
    cw_set_hook('cw_web_get_product_layout_elements', 'cw_product_shipping_option_get_product_layout_elements');
    cw_set_hook('cw_doc_get', 'cw_product_shipping_option_doc_get');
    cw_set_hook('cw_web_get_layout_by_id', 'cw_product_shipping_option_get_layout');
    cw_set_hook('cw_web_get_layout', 'cw_product_shipping_option_get_layout');
}


cw_event_listen('on_product_from_scratch', 'cw_product_shipping_option_default');
cw_event_listen('on_cart_productindexes_update', 'cw_product_shipping_option_update_cart');
