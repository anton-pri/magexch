<?php
namespace cw\custom_magazineexchange_external_links;

/** =============================
 ** Addon functions, API
 ** =============================
 **/

/**
 * Get link by id
 * 
 * @param int id - link id
 * @return array - table row
 */
function get_by_id($id) {
    global $tables;
    $id = intval($id);
    $entry = cw_query_first("SELECT * FROM $tables[magazine_external_links] WHERE id='$id'");
    return $entry;
}

/**
 * Get links by product id
 * 
 * @param int product_id - product id
 * @return array - table rows
 */
function get_by_product_id($product_id) {
    global $tables;
    $product_id = intval($product_id);
    $entries = cw_query("SELECT * FROM $tables[magazine_external_links] WHERE product_id='$product_id'");
    return $entries;
}

/**
 * Update/Create product link
 * 
 * @param array $data - table row
 * @return array - created/updated table row
 */
function update($data) {
    
    $id = $data['id'];
    unset($data['id']);
    
    if (empty($id)) {
        // Create
        $id = cw_array2insert('magazine_external_links', $data);
    } else {
        // Update
        $external_link = get_by_id($id);
        if (empty($external_link['id'])) return error('Link ID does not exist. [cw\custom_magazineexchange_external_links\update()]');
        
        cw_array2update('magazine_external_links', $data,"id = '$id'");
    }
    
    return get_by_id($id);
}

/**
 * Delete link by id
 * 
 * @param int id - link id
 * @return null
 */
function delete($id) {
    global $tables;
    $id = intval($id);
    db_query("DELETE FROM $tables[magazine_external_links] WHERE id='$id'");
}

/** =============================
 ** Hooks
 ** =============================
 **/

/**
 * Delete external links when product deleted
 * POST hook
 * @see include/func/cw.product.php: cw_delete_product()
 */
function cw_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables;

    if ($delete_all == true) {
        db_query('TRUNCATE TABLE ' . $tables['magazine_external_links']);
    } else {
        $product_id = (int) $product_id;
        if (!empty($product_id)) {
            $product_id_condition = 'product_id = "' . $product_id . '"';
            db_query('DELETE FROM ' . $tables['magazine_external_links'] . ' WHERE ' . $product_id_condition);
        }
    }
}

/**
 * Clone links when product cloned
 * POST hook
 * @see include/func/cw.product.php: cw_product_clone()
 * 
 */
function cw_product_clone($product_id) {
    $new_product_id = cw_get_return();
    if (!empty($new_product_id))
        cw_core_copy_tables('magazine_external_links', 'product_id', $product_id, $new_product_id);
    return $new_product_id;
}

/**
 * Tab for product page
 * old POST hook
 * 
 */
function cw_tabs_js_abstract($params, $return) {

    if ($return['name'] == 'product_data') {
        if (!isset($return['js_tabs']['accessories'])) {
            $return['js_tabs']['external_links'] = array(
                'title' => cw_get_langvar_by_name('lbl_external_links'),
                'template' => 'addons/'.addon_name.'/admin/product.tpl',
            );
        }
    }
    return $return;
}


/** =============================
 ** Events handlers
 ** =============================
 **/
 
/**
 * Add export/import table
 * 
 * @see include/import/expdata.php
 */
function on_export_tables_list(&$tables) {
    $tables[] = 'magazine_external_links';
}
