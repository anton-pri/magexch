<?php
cw_include('addons/discount_coupons/include/func.discount_coupons.php');

cw_addons_set_controllers(
    array('pre', 'customer/cart.php', 'addons/discount_coupons/customer/cart.php'),
    array('replace', 'admin/coupons.php', 'addons/discount_coupons/admin/coupons.php')
);

cw_addons_set_hooks(
    array('post', 'cw_cart_calc_discounts', 'cw_discount_coupons_cart_calc_discounts')
);

