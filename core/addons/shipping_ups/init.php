<?php
define('UPS_LICENCE', 'EBA2F47A37670E96');

cw_include('addons/shipping_ups/include/func.ups.php');

cw_addons_set_controllers(
    array('pre', 'admin/settings.php', 'addons/shipping_ups/admin/settings.php')
);

cw_addons_set_template(
    array('pre', 'admin/configuration/addon_settings.tpl', 'addons/shipping_ups/admin/configiration/addon_settings.tpl')
);

cw_addons_set_hooks(
    array('post', 'cw_shipping_get_rates', 'cw_ups_shipping_get_rates')
);
