<?php
cw_include('addons/detailed_product_images/func.hooks.php');

cw_addons_set_controllers(
	array('pre', 'include/products/modify.php', 'addons/detailed_product_images/product_images_modify.php'),
	array('pre', 'include/products/modify.php', 'addons/detailed_product_images/product_images.php'),
	array('post', 'customer/product.php', 'addons/detailed_product_images/product_images.php')
);

cw_set_controller('admin/settings.php', 'addons/detailed_product_images/admin/settings.php', EVENT_PRE);

cw_addons_set_template(
	array('replace', 'customer/products/thumbnail.tpl', 'addons/detailed_product_images/popup_image.tpl', 'images')
);


cw_addons_set_hooks(
    array('post', 'cw_tabs_js_abstract', 'cw_dpi_tabs_js_abstract')
);

cw_set_hook('cw_delete_product','cw_dpi_delete_product',EVENT_POST);

if (APP_AREA == 'customer') {
	cw_addons_add_css('addons/detailed_product_images/css/dpi.css');
}

