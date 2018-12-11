<?php
namespace cw\product_video;

/** =============================
 ** Addon functions, API
 ** =============================
 **/
 
/**
 * Get video by product
 * 
 * @param int $product_id
 * @return array of rows from table cw_product_video
 */
function get_product_video($product_id) {
	global $tables;
	$product_id = intval($product_id);
	$video = cw_query("SELECT * FROM $tables[product_video] WHERE product_id='$product_id' order by pos, title");
	return $video;
}


/** =============================
 ** Hooks
 ** =============================
 **/

/**
 * Delete videos when product deleted
 * 
 * @see POST hook for cw_delete_product
 * 
 */
function cw_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
	global $tables;

    if ($delete_all == true) {
        db_query('TRUNCATE TABLE ' . $tables['product_video']);
    } else {
        $product_id = (int)$product_id;
        if (!empty($product_id)) {
            db_query('DELETE FROM ' . $tables['product_video'] . ' WHERE product_id = \'' . $product_id . '\'');
        }
    }
}

/**
 * Clone videos when product cloned
 * POST hook
 * @see include/func/cw.product.php: cw_product_clone()
 * 
 */
function cw_product_clone($product_id) {
    $new_product_id = cw_get_return();
    if (!empty($new_product_id))
		cw_core_copy_tables('product_video', 'product_id', $product_id, $new_product_id);
    return $new_product_id;
}

/**
 * Declare product tabs
 * 
 * @see cw_tabs_js_abstract
 * 
 * @param OLD params notation
 */
function cw_tabs_js_abstract($params, $return) {
	global $product_video;

    if ($return['name'] == 'product_data' && AREA_TYPE == 'A') {
        if (!isset($return['js_tabs']['product_video'])) {
            $return['js_tabs']['product_video'] = array(
                'title' => cw_get_langvar_by_name('lbl_product_video'),
                'template' => 'addons/'.addon_name.'/admin/product.tpl',
            );
        }
    }

    if ($return['name'] == 'product_data_customer' && (!empty($product_video)) && AREA_TYPE == 'C') {

		$return['js_tabs']['product_data'] = array(
			'title' => cw_get_langvar_by_name('lbl_product_video'),
			'template' => 'addons/'.addon_name.'/customer/product.tpl'
		);

    }

    return $return;
}

/** =============================
 ** Events handlers
 ** =============================
 **/


