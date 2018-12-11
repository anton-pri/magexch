<?php
$tables['paypal_adaptive_doc_accounts'] = 'cw_paypal_adaptive_doc_accounts';

cw_include('addons/paypal_adaptive/include/func.paypal_adaptive.php');

cw_addons_set_controllers(
    array('replace', 'customer/paypal_adaptive.php', 'addons/paypal_adaptive/customer/paypal_adaptive.php')
);

cw_addons_set_hooks(
# usual hook for all of the payments
    array('post', 'cw_payment_get_methods', 'cw_payment_paypal_adaptive_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_paypal_adaptive_run_processor')
);
