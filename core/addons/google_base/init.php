<?php
cw_include('addons/google_base/func.php');
    
if (APP_AREA == 'admin' && $target == 'google_base') {
    cw_addons_set_controllers(
        array('replace', 'admin/google_base.php', 'addons/google_base/google_base.php')
    );

    cw_addons_set_template(
        array('replace', 'admin/main/google_base.tpl', 'addons/google_base/google_base.tpl')
    );
    cw_addons_set_template(
        array('replace', 'admin/import_export/google_base.tpl', 'addons/google_base/google_base.tpl')
    );

}
if(APP_AREA =='admin'){
    cw_set_controller('admin/ajax_gb_category_select.php', 'addons/google_base/gb_attributes_modify.php', EVENT_REPLACE);

    cw_addons_set_template( array('post', 'admin/attributes/default_types.tpl', 'addons/google_base/types/google_base_product_category_selector.tpl'));

    cw_addons_add_js('jquery/dynatree-1.2.4/jquery.dynatree.min.js');
    cw_addons_add_css('jquery/dynatree-1.2.4/ui.dynatree.css');

}

if (APP_AREA == 'cron') {
    cw_set_controller('init/abstract.php','addons/google_base/abstract.php',EVENT_POST);
}

if (APP_AREA == 'customer') {
    // Add microdata to product page
    cw_addons_set_template( array('post','customer/products/additional_data.tpl','addons/google_base/product_microdata.tpl'));
    $cw_allowed_tunnels[] = 'cw_gb_product_microdata';
}
