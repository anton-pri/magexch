<?php
cw_include('addons/payment_cc/include/func.payment_cc.php');

cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods', 'cw_payment_cc_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_cc_run_processor')
);
