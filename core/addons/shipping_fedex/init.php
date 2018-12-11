<?php
cw_include('addons/shipping_fedex/include/func.fedex.php');

cw_set_controller('admin/settings.php', 'addons/shipping_fedex/admin/settings.php', EVENT_PRE);

/*
cw_addons_set_template(
    array('pre', 'admin/settings/settings.tpl', 'addons/shipping_fedex/admin/configuration/addon_settings.tpl')
);
*/

cw_addons_set_hooks(
    array('post', 'cw_shipping_get_rates', 'cw_fedex_shipping_get_rates')
);
