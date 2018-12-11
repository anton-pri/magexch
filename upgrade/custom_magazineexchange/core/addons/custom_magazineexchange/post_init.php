<?php

if (APP_AREA == 'customer') {

    cw_addons_set_template(
      array('post', 'customer/service_js.tpl', 'customer/custom_js.tpl')

    );

    cw_addons_unset_template(
        array('pre', 'customer/cart/totals.tpl', 'addons/shipping_system/customer/cart/totals.tpl'),
        array('post', 'customer/cart/cart.tpl', 'addons/accessories/cart_recently_viewed_products.tpl'),
        array('post', 'customer/cart/cart.tpl', 'addons/accessories/cab_products_list.tpl')
    );

}
