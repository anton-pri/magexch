<?php

$cw_allowed_tunnels[] = 'top_menu_smarty_init';
$cw_allowed_tunnels[] = 'cw_category_get_location';

// @TODO: remove second condition after ajax product_filter rework
if (
	APP_AREA == 'customer' && (
        !defined('IS_AJAX') && $target != 'image'
        ||
	    isset($ajax_filter) && $ajax_filter && defined('IS_AJAX') && $target != 'image'
    )
) {
	cw_include('addons/top_menu/func.php');
	cw_set_controller('customer/auth.php', 'addons/top_menu/cust.php', EVENT_POST);
	cw_addons_set_template(array('replace', 'customer/top_categories.tpl', 'addons/top_menu/menu.tpl'));

	cw_addons_add_js('jquery/jquery.ui.potato.menu.js');
	cw_addons_add_css('jquery/jquery.ui.potato.menu.css');
	cw_addons_add_css('menu.css');
	cw_addons_add_css('addons/top_menu/menu.css');
}

if (APP_AREA == 'admin' && $target == 'top_menu') {
	cw_include('addons/top_menu/func.php');
	cw_set_controller('admin/top_menu.php', 'addons/top_menu/admin/top_menu.php', EVENT_REPLACE);
	cw_addons_set_template(array('replace', 'admin/products/top_menu.tpl', 'addons/top_menu/admin_main.tpl'));
}

$tables['top_menu_user_categories'] = 'cw_top_menu_user_categories';

// TODO: register on_category_delete handler, otherwise cw_top_menu_user_categories will have orphaned links
