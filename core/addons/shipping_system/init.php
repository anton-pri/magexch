<?php
$cw_allowed_tunnels[] = 'cw_shipping_search';
$cw_allowed_tunnels[] = 'cw_shipping_doc_trackable';

cw_include('addons/shipping_system/include/func.shipping.php');
cw_addons_add_css('addons/shipping_system/css/main.css');

cw_addons_set_controllers(
    array('replace', 'admin/shipping_carriers.php', 'addons/shipping_system/admin/shipping_carriers.php'),
    array('replace', 'admin/cod_types.php', 'addons/shipping_system/admin/cod_types.php'),
    array('replace', 'admin/shipping_zones.php', 'addons/shipping_system/admin/shipping_zones.php'),
    array('replace', 'admin/shipping.php', 'addons/shipping_system/admin/shipping.php'),
    array('replace', 'admin/shipping_rates.php', 'addons/shipping_system/admin/shipping_rates.php'),

    array('post', 'include/orders/order_edit.php', 'addons/shipping_system/include/orders/order_edit.php'),
    array('replace', 'customer/popup-shipping.php', 'addons/shipping_system/customer/popup-shipping.php')
);

cw_set_hook('cw_checkout_login_prepare', 'cw_shipping_checkout_login_prepare', EVENT_POST);

cw_addons_set_hooks(
    array('pre', 'cw_cart_actions', 'cw_shipping_cart_actions'),
    array('post', 'cw_cart_get_warehouses_cart', 'cw_shipping_cart_get_warehouses_cart'),
    array('post', 'cw_cart_calc_single', 'cw_shipping_cart_calc_single'),
    array('pre', 'cw_cart_summarize', 'cw_shipping_cart_summarize'),
    array('post', 'cw_product_get',    'cw_shipping_product_get')
);

cw_addons_set_template(
    array('pre', 'customer/cart/totals.tpl', 'addons/shipping_system/customer/cart/totals.tpl'),
    array('post', 'customer/checkout/shipping_methods.tpl', 'addons/shipping_system/customer/checkout/shipping_methods.tpl'),

    array('post', 'customer/products/product-fields.tpl', 'addons/shipping_system/customer/products/product-fields.tpl'),
    array('replace', 'customer/products/estimate-fields.tpl', 'addons/shipping_system/customer/products/product-fields.tpl'),
    array('post', 'customer/products/products-info.tpl', 'addons/shipping_system/customer/products/shipping_estimator.tpl'),
    array('post', 'customer/products/our_price.tpl', 'addons/shipping_system/customer/products/free-shipping.tpl')
);

if (APP_AREA == 'customer') {
	cw_addons_add_js('addons/shipping_system/js/dialog.js');

    cw_set_controller('customer/shipping_estimator.php', 'addons/shipping_system/customer/shipping_estimator.php', EVENT_REPLACE);
    cw_set_controller('customer/order_tracking.php', 'addons/shipping_system/customer/order_tracking.php', EVENT_REPLACE);

	cw_addons_set_template(
	    array('post', 'customer/products/product.tpl', 'addons/shipping_system/customer/products/estimate.tpl'),
	    array('post', 'customer/products/subcategories.tpl', 'addons/shipping_system/customer/products/estimate.tpl')
	);
}
