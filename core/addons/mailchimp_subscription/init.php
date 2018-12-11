<?php
cw_include('addons/mailchimp_subscription/include/MCAPI.class.php');
cw_include('addons/mailchimp_subscription/include/func.mailchimp.php');

cw_set_controller('admin/settings.php', 'addons/mailchimp_subscription/admin/settings.php', EVENT_PRE);

cw_addons_set_hooks(
    array('pre', 'cw_payment_run_processor', 'cw_post_mailchimp_subscribe')
);

cw_addons_set_template(
    array('post', 'customer/checkout/notes.tpl', 'addons/mailchimp_subscription/mailchimp_subscription.tpl')
);
