<?php
$tables['linked_products'] = 'cw_linked_products';
require $app_main_dir . '/addons/accessories/func.php';
require $app_main_dir . '/addons/accessories/func.hooks.php';

$cw_allowed_tunnels[] = 'cw_ac_get_recommended_smarty';

if (APP_AREA == 'admin') {
    cw_addons_set_controllers(
        array('pre', 'include/products/modify.php', 'addons/accessories/product_modify_accessories.php')
    );

    cw_addons_set_hooks(
        array('post', 'cw_tabs_js_abstract', 'cw_ac_tabs_js_abstract')
    );

    cw_set_hook('cw_product_clone', 'cw_ac_product_clone', EVENT_POST);
    cw_set_hook('cw_delete_product','cw_ac_delete_product',EVENT_POST);
}

if (APP_AREA == 'customer') {
    //cw_addons_add_js('addons/accessories/func.js');
    cw_addons_set_controllers(
        array('post', 'customer/product.php', 'addons/accessories/product_accessories.php')
    );
    cw_set_controller('customer/product.php', 'addons/accessories/rv_product.php', EVENT_POST);
    cw_set_controller('customer/cart.php', 'addons/accessories/rv_products_list.php', EVENT_POST);
    cw_set_controller('customer/cart.php', 'addons/accessories/cab_products_list.php', EVENT_POST);

    cw_addons_set_template(
        array('post', 'customer/cart/cart.tpl', 'addons/accessories/cart_recently_viewed_products.tpl'),
        array('post', 'customer/cart/cart.tpl', 'addons/accessories/cab_products_list.tpl')

    );

    cw_addons_set_hooks(
        array('post', 'cw_tabs_js_abstract', 'cw_ac_tabs_js_abstract')
    );

    // Integration with ajax_add2cart addon {
    if (defined('IS_AJAX') && constant('IS_AJAX')) {
		cw_event_listen('on_add_cart','cw_ac_on_add_to_cart');
		cw_addons_set_template(
			array('post', 'addons/ajax_add2cart/add2cart_popup.tpl', 'addons/accessories/add2cart_popup.tpl')
		);
	}
    // } Integration with ajax_add2cart addon

   cw_addons_add_css('addons/accessories/accessories.css');

}
