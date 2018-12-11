<?php
cw_include('addons/paypal_express/include/func.paypal_express.php');

cw_addons_set_template(
    array('post', 'customer/cart/buttons.tpl', 'addons/paypal_express/customer/buttons.tpl')
);

cw_addons_set_controllers(
    array('replace', 'customer/paypal_express.php', 'addons/paypal_express/customer/paypal_express.php')
);

cw_addons_set_hooks(
    array('post', 'cw_payment_search', 'cw_paypal_express_payment_search'),
    array('pre', 'cw_payment_get_label', 'cw_paypal_express_payment_get_label')
);
