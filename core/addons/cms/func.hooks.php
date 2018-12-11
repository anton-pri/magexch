<?php
namespace cms;

/* Events handlers */
function on_object_delete($object_id, $object_type) {
    global $tables;

    db_query('DELETE FROM ' . $tables['cms_restrictions'] . ' WHERE object_id = "' . $object_id . '" AND object_type = "' . $object_type . '"');
}

function on_product_delete($product_id) {
	
	on_object_delete($product_id, 'P');

}

function on_category_delete($category_id) {

	on_object_delete($category_id, 'C');

}

function on_manufacturer_delete($manufacturer_id) {

	on_object_delete($manufacturer_id, 'M');

}

function on_cms_check_restrictions_C($data) {
	global $tables, $cat;
	
	if (isset($cat)) {
		$allowed_categoryids = cw_ab_get_cms_categories($data['contentsection_id']);
		if (!empty($allowed_categoryids) && is_array($allowed_categoryids)) {
			if (!in_array(intval($cat), $allowed_categoryids)) return false;
		}
	}
	return true;
}

function on_cms_check_restrictions_P($data) {
	global $tables, $product_id;

	if (isset($product_id)) {
		$allowed_product_ids = cw_ab_get_cms_products($data['contentsection_id']);
		$allowed_product_ids = array_column($allowed_product_ids,'id');
		if (!empty($allowed_product_ids) && is_array($allowed_product_ids)) {
			if (!in_array(intval($product_id), $allowed_product_ids)) return false;
		}
	}
	return true;
}

/* Hooks */
function cw_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables;

    if ($delete_all === true) {
        db_query('DELETE FROM ' . $tables['cms_restrictions'].' WHERE object_type="P"');
    }
}

function tabs_js_abstract($params, $return) {

    if ($return['name'] == 'product_data') {
    	if (AREA_TYPE != 'A') return $return;

        if (!isset($return['js_tabs']['cms']))
            $return['js_tabs']['cms'] = array(
                'title' => cw_get_langvar_by_name('lbl_cs_content_sections'),
                'template' => 'addons/cms/product_banners.tpl',
            );
    }
    return $return;
}
