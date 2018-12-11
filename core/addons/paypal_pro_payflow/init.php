<?php
cw_include('addons/paypal_pro_payflow/include/func.paypal_pro_payflow.php');

cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods',   'cw_payment_paypalpro_payflow_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_paypalpro_payflow_run_processor')
);

cw_addons_set_template(
    array('post', 'admin/docs/notes.tpl@doc_process_other', 'addons/paypal_pro_payflow/admin/doc_process_data.tpl')
);


cw_set_hook('cw_payment_do_capture',    'cw_paypal_pro_payflow_do_capture', EVENT_POST);
cw_set_hook('cw_payment_do_void',       'cw_paypal_pro_payflow_do_void',    EVENT_POST);
