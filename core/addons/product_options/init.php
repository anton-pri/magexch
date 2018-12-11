<?php
# kornev, the product options are build on the attributes
# kornev, the product option - it's attribute, which have got the 'product_options' in the addon
# kornev, the option (as a class) is not assigned to a product - the values are assigned to the product


# kornev, the attribute -> product relation
$tables['product_options'] = 'cw_product_options';
$tables['product_options_lng'] = 'cw_product_options_lng';

$tables['product_options_values'] = 'cw_product_options_values';
$tables['product_options_values_lng'] = 'cw_product_options_values_lng';

$tables['product_variants'] = 'cw_product_variants';
$tables['product_variant_items'] = 'cw_product_variant_items';


$tables['products_options_ex'] = 'cw_products_options_ex';
$tables['product_options_js'] = 'cw_product_options_js';
$tables['products_images_var'] = 'cw_products_images_var';

cw_include('addons/product_options/include/func.product_options.php');
cw_include('addons/product_options/include/hooks.php', INCLUDE_NO_GLOBALS);

cw_addons_set_controllers(
    array('post', 'include/products/modify.php', 'addons/product_options/include/products/modify-options.php'),
    array('post', 'include/products/modify.php', 'addons/product_options/include/products/modify-variants.php'),

    array('replace', 'customer/popup_product_options.php', 'addons/product_options/customer/popup_product_options.php'),
    array('post', 'customer/product.php', 'addons/product_options/customer/product.php')
);

cw_addons_set_hooks(
    array('post', 'cw_tabs_js_abstract', 'cw_product_options_tabs_js_abstract'),
    array('pre', 'cw_product_build_flat', 'cw_product_options_product_build_flat'),
    array('post', 'cw_product_build_flat', 'cw_product_options_product_build_flat_post'),
    array('pre', 'cw_product_check_avail', 'cw_product_options_product_check_avail')
);

cw_addons_set_template(
    array('pre', 'customer/products/product-amount.tpl', 'addons/product_options/customer/products/product-amount.tpl'),
    array('replace', 'common/product_image.tpl', 'addons/product_options/customer/products/product_image.tpl'),
    array('post', 'customer/products/products-info.tpl', 'addons/product_options/customer/products/product-variant-selector.tpl'),
    array('pre', 'customer/products/products.tpl', 'addons/product_options/customer/products/products-prepare.tpl')
);

cw_set_hook('cw_delete_product', 'cw_product_options_delete_product', EVENT_PRE);
cw_set_hook('cw_warehouse_recalculate', 'cw_on_warehouse_recalculate', EVENT_POST);
cw_set_hook('cw_product_clone', 'cw_product_options_clone', EVENT_POST);

cw_event_listen('on_prepare_products_found', 'cw_product_options_prepare_products_found');
