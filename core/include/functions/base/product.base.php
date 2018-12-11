<?php
/* =================================
 * Product
 *
 * =================================
 */
namespace Product;

function get($id) {
    global $tables;
    return cw_query_first('SELECT * FROM '.$tables['products'].' WHERE product_id="'.intval($id).'"');
// TODO: fill/use cache
}

function add($data) {
    return cw_array2insert('products',$data);
}

function delete($id) {
    global $tables;

    $id = intval($id);

    cw_event('on_product_delete',array($id)); // event triggered before product deletion in case handlers need some additional info about product

    return db_query('DELETE FROM '.$tables['products'].' WHERE product_id="'.$id.'"');

// TODO: flush product cache
}


function update($id, $data) {

    // ....

    cw_event('on_product_update',array($id)); // event triggered after product update
// TODO: flush product cache
}

function getField($id,$field) {
    $data = get($id);
    return $data[$field];
}

function isAvailable($id) {
    return getField($id,'avail') > 0;
}




/* =================================
 * Product\Category
 *
 * =================================
 */
namespace Product\Category;

/* ---------------------------------
 * Events handlers
 * ---------------------------------
 */

cw_event_listen('on_product_delete','\Product\Category\on_product_delete');
cw_event_listen('on_category_delete','\Product\Category\on_category_delete');

function on_product_delete($product_id) {
    cw_call('Product\Category\delete',array($product_id,null));
}

function on_category_delete($category_id) {
    cw_call('Product\Category\delete',array(null,$category_id));
}

/* ---------------------------------
 * Interface
 * ---------------------------------
 */

function add($product_id, $category_id, $main = true) {
    // TODO: Implement
    return cw_array2insert();
}

// see also detach for proper maintenance of product main cat
function delete($product_id = null, $category_id = null) {
    global $tables;
    $where = (!is_null($product_id)?' AND product_id = "'.intval($product_id).'"':'').(!is_null($category_id)?' AND category_id = "'.intval($category_id).'"':'');
    return db_query('DELETE FROM '.$tables['products_categories'].' WHERE 1 '.$where);
}

function get($product_id = null, $category_id = null, $main = null) {
    global $tables;
    $where = (!is_null($product_id)?' AND product_id = "'.intval($product_id).'"':'').(!is_null($category_id)?' AND category_id = "'.intval($category_id).'"':'').(!is_null($main)?' AND category_id = "'.intval($main).'"':'');
    $result = cw_query('SELECT * FROM '.$tables['products_categories'].' WHERE 1 '.$where);

    // if certain prod<=>cat link requested, then return only this row
    if (!is_null($product_id) && (!is_null($category_id) || !is_null($main)) && count($result)==1) return array_pop($result);
    // otherwise return array of rows
    return $result;
}

function isMain($product_id, $category_id) {
    $prod_cat = Product\Category\get($product_id, $category_id);
    return ($prod_cat['main'] == 1);
}

// Set certain or first available assigned category as main for product
function setMain($product_id, $category_id = null) {

    if (empty($product_id)) return false;

    $data = array('main'=>0);
    cw_array2update('products_categories',$data,'product_id = "'.intval($product_id).'"');

    $data = array('main'=>1);
    cw_array2update('products_categories',$data,'product_id = "'.intval($product_id).'"'.(!is_null($category_id)?' AND category_id = "'.intval($category_id).'"':'').' LIMIT 1');
}

// Detach category from product and assign new main category if necessary
function detach($product_id, $category_id) {

    if (empty($product_id)) return false;

    global $tables;
    $is_main = Product\Category\isMain($product_id, $category_id);
    Product\Category\delete($product_id, $category_id);
    if ($is_main) Product\Category\setMain($product_id);
}

// Get main category_id
function getMainCategoryId($product_id) {
    if (empty($product_id)) return false;
    $prod_cat = Product\Category\get($product_id, null, 1);
    return $prod_cat['category_id'];
}
