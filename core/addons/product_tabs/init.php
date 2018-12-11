<?php
cw_include('addons/product_tabs/include/func.hooks.php');

cw_addons_set_controllers(
    array('pre', 'include/products/modify.php', 'addons/product_tabs/admin/tabs.php'),
    array('pre', 'customer/product.php', 'addons/product_tabs/customer/tabs.php')
);

cw_addons_set_hooks(
    array('post', 'cw_tabs_js_abstract', 'cw_pt_tabs_js_abstract')
);

cw_set_hook('cw_delete_product','cw_pt_delete_product',EVENT_POST);
cw_set_hook('cw_product_clone','cw_pt_product_clone',EVENT_POST);

if (APP_AREA == 'admin') {
	cw_set_controller('admin/product_tabs.php','addons/product_tabs/admin/tabs.php', EVENT_REPLACE);
	cw_addons_set_template(
		array('replace','admin/products/product_tabs.tpl','addons/product_tabs/admin/main.tpl')
	);
	cw_addons_add_css('addons/product_tabs/admin/main.css');
}


global $_pt_addon_tables;

$_pt_addon_tables = array(
    'product'	=> 'product_tabs',
    'global'	=> 'tabs'
);

foreach ($_pt_addon_tables as $_table) {
    $tables[$_table] = 'cw_' . $_table;
}

