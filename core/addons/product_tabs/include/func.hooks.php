<?php
function cw_pt_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $_pt_addon_tables, $tables, $config;

    if ($delete_all == true) {
        db_query('TRUNCATE TABLE ' . $tables[$_pt_addon_tables['product']]);
    } else {
        $product_id = (int)$product_id;
        if (!empty($product_id)) {
            db_query('DELETE FROM ' . $tables[$_pt_addon_tables['product']] . ' WHERE product_id = \'' . $product_id . '\'');
        }
    }
}

/**
 * Clone tabs when product cloned
 * POST hook
 * @see include/func/cw.product.php: cw_product_clone()
 * 
 */
function cw_pt_product_clone($product_id) {
	global $_pt_addon_tables;
    $new_product_id = cw_get_return();
    if (!empty($new_product_id))
		cw_core_copy_tables($_pt_addon_tables['product'], 'product_id', $product_id, $new_product_id);
    return $new_product_id;    
}


function cw_pt_tabs_js_abstract($params, $return) {
    global $pt_is_tabs, $pt_tabs;

    if ($return['name'] == 'product_data' && AREA_TYPE == 'A') {
        if (!isset($return['js_tabs']['product_tabs'])) {
            $return['js_tabs']['product_tabs'] = array(
                'title' => cw_get_langvar_by_name('lbl_pt_product_tabs'),
                'template' => 'addons/product_tabs/admin/main.tpl',
            );
        }
    }

    if ($return['name'] == 'product_data_customer' && (isset($pt_is_tabs) && $pt_is_tabs == true) && AREA_TYPE == 'C') {
    	if (!isset($pt_tabs) || !is_array($pt_tabs)) return $return;

    	foreach ($pt_tabs as $key => $tab) {

	        if (!isset($return['js_tabs']['ptab-' . $key])) {
	            $return['js_tabs']['ptab-' . $key] = array(
	                'title' => $tab['title'],
	                'template' => 'addons/product_tabs/customer/tab_content.tpl'
	            );
	        }
    	}
    }

    return $return;
}
