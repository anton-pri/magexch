<?php
/*
 * Vendor: CW
 * addon: breadcrumbs
 */

const breadcrumbs_addon_name = 'breadcrumbs';

$tables['breadcrumbs'] = 'cw_breadcrumbs';

if (APP_AREA != 'customer') {

    cw_include('addons/' . breadcrumbs_addon_name . '/include/func.breadcrumbs.php');


    if (APP_AREA == 'admin') {
        cw_addons_set_controllers(
            array('replace', 'admin/breadcrumbs_management.php', 'addons/' . breadcrumbs_addon_name . '/admin/breadcrumbs_management.php'),
            array('replace', 'admin/select_breadcrumb.php', 'addons/' . breadcrumbs_addon_name . '/admin/select_breadcrumb.php')
        );

        cw_addons_set_template(
            array('replace', 'admin/main/breadcrumbs_management.tpl', 'addons/' . breadcrumbs_addon_name . '/admin/breadcrumbs_management.tpl'),
            array('replace', 'admin/main/select_breadcrumb.tpl', 'addons/' . breadcrumbs_addon_name . '/admin/select_breadcrumb.tpl')
        );
    }


    cw_addons_set_template(
        array('replace', 'admin/main/title.tpl', 'addons/' . breadcrumbs_addon_name . '/admin/title.tpl'),
        array('replace', 'admin/main/location.tpl', 'addons/' . breadcrumbs_addon_name . '/admin/location.tpl')
    );


    cw_set_controller('init/abstract.php', 'addons/' . breadcrumbs_addon_name . '/admin/breadcrumb.php', EVENT_POST);


}
