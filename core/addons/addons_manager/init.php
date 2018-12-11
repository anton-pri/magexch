<?php
if (!defined('APP_START')) die('Access denied');

if (APP_AREA == 'admin') {

    cw_addons_set_controllers(
        array('replace', 'admin/addons_manager.php', 'addons/addons_manager/addons_manager.php'),
        array('replace', 'admin/installmod.php', 'addons/addons_manager/installmod.php')
    );

    cw_addons_set_template(
        array('replace', 'admin/main/addons_manager.tpl', 'addons/addons_manager/addons_manager.tpl'),
        array('replace', 'admin/configuration/addons_manager.tpl', 'addons/addons_manager/addons_manager.tpl')
    );
}

    cw_addons_set_controllers(
        array('replace', 'customer/version.php', 'addons/addons_manager/version.php'),
        array('replace', 'admin/version.php', 'addons/addons_manager/version.php')
    );


