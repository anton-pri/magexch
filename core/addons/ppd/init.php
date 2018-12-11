<?php

$cw_allowed_tunnels[] = 'cw_ppd_as3_real_url';

cw_include('addons/ppd/include/func.hooks.php');

cw_addons_set_controllers(
    array('replace', 'admin/filetypes.php', 'addons/ppd/admin/filetypes.php'),
    array('replace', 'customer/getfile.php', 'addons/ppd/customer/getfile.php'),
    array('pre', 'include/products/modify.php', 'addons/ppd/admin/main.php'),
    array('pre', 'customer/product.php', 'addons/ppd/customer/main.php')
);

cw_addons_set_template(
    array('replace', 'admin/main/filetypes.tpl', 'addons/ppd/admin/filetypes.tpl')
);

cw_addons_set_hooks(
    array('post', 'cw_doc_change_status_C', 'cw_ppd_doc_change_status_C'),
    array('post', 'cw_doc_change_status_P', 'cw_ppd_doc_change_status_C'),
    array('post', 'cw_doc_change_status_D', 'cw_ppd_doc_change_status_D'),
    array('post', 'cw_doc_delete', 'cw_ppd_doc_delete'),
    array('pre', 'cw_user_delete', 'cw_ppd_user_delete'),
    array('post', 'cw_tabs_js_abstract', 'cw_ppd_tabs_js_abstract')
);

cw_set_hook('cw_delete_product','cw_ppd_delete_product',EVENT_POST);
cw_set_hook('cw_product_clone','cw_ppd_product_clone',EVENT_POST);


if (APP_AREA == 'admin') {

	cw_addons_add_css('addons/ppd/admin/main.css');
}

if (APP_AREA == 'seller' || APP_AREA == 'admin') {
    cw_addons_add_js('addons/ppd/js/popup_files.js'); 
    cw_addons_set_template(
        array('post', 'main/attributes/default_types.tpl', 'addons/ppd/types/file-selector.tpl')
    );
}

if (APP_AREA == 'customer') {
	cw_addons_add_css('addons/ppd/customer/main.css');
}


$_addon_tables = array('ppd_files', 'ppd_types', 'ppd_downloads', 'ppd_stats');

foreach ($_addon_tables as $_table) {
    $tables[$_table] = 'cw_' . $_table;
}
