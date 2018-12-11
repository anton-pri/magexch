<?php
/*
 * Vendor: CW
 * addon: Orders Extra Features
 */

const orders_extra_features_addon_name = 'orders_extra_features';

if (APP_AREA == 'admin') {
    cw_addons_set_controllers(
        array('replace', 'admin/report_cost_history.php', 'addons/' . orders_extra_features_addon_name . '/admin/report_cost_history.php'),
        array('replace', 'admin/profit_reports.php', 'addons/' . orders_extra_features_addon_name . '/admin/profit_reports.php')
    );

    cw_addons_set_template(
        array('replace', 'admin/orders/report_cost_history.tpl', 'addons/' . orders_extra_features_addon_name . '/admin/report_cost_history.tpl'),
        array('replace', 'admin/orders/profit_reports.tpl', 'addons/' . orders_extra_features_addon_name . '/admin/profit_reports.tpl'),
        array('post', 'main/orders/search.tpl@after_product_field', 'addons/' . orders_extra_features_addon_name . '/admin/excl_by_product_name.tpl')
    );
}
