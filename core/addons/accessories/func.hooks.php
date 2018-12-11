<?php
function cw_ac_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables;

    if ($delete_all == true) {
        db_query('TRUNCATE TABLE ' . $tables['linked_products']);
    } else {
        $product_id = (int) $product_id;
        if (!empty($product_id)) {
            $product_id_condition = 'product_id = "' . $product_id . '"';
            db_query('DELETE FROM ' . $tables['linked_products'] . ' WHERE ' . $product_id_condition);
        }
    }

}

/**
 * Clone accessories when product cloned
 * POST hook
 * @see include/func/cw.product.php: cw_product_clone()
 * 
 */
function cw_ac_product_clone($product_id) {
    $new_product_id = cw_get_return();
    if (!empty($new_product_id))
		cw_core_copy_tables('linked_products', 'product_id', $product_id, $new_product_id);
    return $new_product_id;
}

function cw_ac_tabs_js_abstract($params, $return) {

    if ($return['name'] == 'product_data') {
        if (!isset($return['js_tabs']['accessories'])) {
            $return['js_tabs']['accessories'] = array(
                'title' => cw_get_langvar_by_name('lbl_ac_accessories'),
                'template' => 'addons/accessories/product_modify_accessories.tpl',
            );
        }
        if (!isset($return['js_tabs']['upselling'])) {
            $return['js_tabs']['upselling'] = array(
                'title' => cw_get_langvar_by_name('lbl_upselling_links'),
                'template' => 'addons/accessories/product_modify_upselling.tpl',
            );
        }
    }
    if ($return['name'] == 'product_data_customer') {
        global $product_accessories, $recommended_products;

        if (!isset($return['js_tabs']['accessories']) && !empty($product_accessories)) {
            $return['js_tabs']['accessories'] = array(
                'title' => cw_get_langvar_by_name('lbl_ac_accessories'),
                'template' => 'addons/accessories/product_accessories_list.tpl',
            );
        }
/*
        if (!isset($return['js_tabs']['accessories_rec']) && !empty($recommended_products)) {
            $return['js_tabs']['accessories_rec'] = array(
                'title' => cw_get_langvar_by_name('lbl_ac_recommended_products'),
                'template' => 'addons/accessories/product_recommended_list.tpl',
            );
        }
*/
    }

    return $return;
}
