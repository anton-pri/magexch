<?php
/* =================================
 * Category
 *
 * =================================
 */
namespace Category;

function get($id) {
    global $tables;
    return cw_query_first('SELECT * FROM '.$tables['categories'].' WHERE category_id="'.intval($id).'"');
// TODO: fill/use cache
}

function add($data) {
    return cw_array2insert('categories',$data);
}

function delete($category_id) {
    global $tables;

    $category_id = intval($category_id);

    cw_event('on_category_delete',array($category_id)); // event triggered before category deletion in case handlers need some additional info about category

    return db_query('DELETE FROM '.$tables['categories'].' WHERE category_id="'.$category_id.'"');

// TODO: flush product cache
}


function update($category_id, $data) {

    // ....

    cw_event('on_category_update',array($category_id)); // event triggered after product update
// TODO: flush product cache
}

function getField($id,$field) {
    $data = get($id);
    return $data[$field];
}

function isAvailable($id) {
    return getField($id,'avail') > 0;
}
