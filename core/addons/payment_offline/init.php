<?php
cw_include('addons/payment_offline/include/func.payment_offline.php');

cw_addons_set_hooks(
    array('post', 'cw_payment_run_processor', 'cw_payment_offline_run_processor')
);
