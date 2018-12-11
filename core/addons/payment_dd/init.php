<?php
cw_include('addons/payment_dd/include/func.payment_dd.php');

cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods', 'cw_payment_dd_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_dd_run_processor')
);
