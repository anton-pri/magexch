<?php

function cw_dpi_tabs_js_abstract($params, $return) {

    if ($return['name'] == 'product_data') {
    	if (AREA_TYPE != 'A') return $return;

        if (!isset($return['js_tabs']['dpi']))
            $return['js_tabs']['dpi'] = array(
                'title' => cw_get_langvar_by_name('lbl_detailed_images'),
                'template' => 'addons/detailed_product_images/product_images_modify.tpl',
            );
    }
    
    return $return;	
}

function cw_dpi_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables;

    if ($delete_all == true) {
        db_query('TRUNCATE TABLE ' . $tables['products_detailed_images']);
    } else {
        $product_id = (int) $product_id;
        if (!empty($product_id)) {
			cw_image_delete_all('products_detailed_images', "id = '$product_id'");
        }
    }

}
