<?php
cw_include('addons/paypal_pro/include/func.paypal_pro.php');

cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods', 'cw_payment_paypalpro_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_paypalpro_run_processor')
);
