<?php
cw_include('addons/payment_ch/include/func.payment_ch.php');

cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods', 'cw_payment_ch_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_ch_run_processor')
);
