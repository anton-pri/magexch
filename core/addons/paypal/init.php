<?php
cw_include('addons/paypal/include/func.paypal.php');

cw_addons_set_controllers(
    array('replace', 'customer/paypal.php', 'addons/paypal/customer/paypal.php')
);

cw_addons_set_template(
    array('post', 'admin/docs/notes.tpl@doc_process_other', 'addons/paypal/admin/doc_process_data.tpl')
);

cw_addons_set_hooks(
# usual hook for all of the payments
    array('post', 'cw_payment_get_methods', 'cw_payment_paypal_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_paypal_run_processor')
);
