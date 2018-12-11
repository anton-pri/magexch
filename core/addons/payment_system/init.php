<?php
$cw_allowed_tunnels[] = 'cw_payment_is_authorized';

cw_include('addons/payment_system/include/func.payment.php');

cw_addons_set_template(
    array('post', 'customer/cart/buttons.tpl', 'addons/payment_system/customer/buttons.tpl')
);

cw_addons_set_controllers(
    array('replace', 'admin/payments.php', 'addons/payment_system/admin/payments.php'),
    array('replace', 'customer/place_order.php', 'addons/payment_system/customer/place_order.php')
);

cw_set_hook('cw_checkout_login_prepare', 'cw_payment_checkout_login_prepare', EVENT_POST);
cw_set_hook('cw_checkout_prepare', 'cw_payment_checkout_prepare', EVENT_POST);


// Capture/Decline button for Authorized orders
cw_set_controller('admin/docs_O.php','addons/payment_system/admin/docs_O.php', EVENT_PRE);

cw_addons_set_template(
    array('post', 'admin/docs/notes.tpl@doc_process_buttons', 'addons/payment_system/admin/doc_process_buttons.tpl')
);

cw_event_listen('on_sessions_delete','cw_payment_data_delete');
