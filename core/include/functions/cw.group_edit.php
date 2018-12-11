<?php
function cw_group_edit_copy_category($product_id, $is_main=true) {
    global $ge_id, $tables;

    if (!$ge_id) return;

    $main = $is_main?1:0;
    $current_records = cw_query("select * from ".$tables['products_categories']." where product_id='$product_id' and main=$main");
    if (is_array($current_records))
    while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
        db_query("delete from $tables[products_categories] where product_id='$pid' and main=$main");
        foreach($current_records as $current_record) {
            $current_record['product_id'] = $pid;
            cw_array2insert('products_categories', $current_record, true);
        }
    }
}

function cw_group_edit_copy_system_info($product_id, $data) {
    global $ge_id;

    if (!$ge_id) return;
    while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) 
        cw_product_update_system_info($pid, $data);
}

function cw_group_edit_copy_memberships($membership_ids) {
    global $ge_id;

    if (!$ge_id) return;
    while($pid = cw_group_edit_each($ge_id, 1, $product_id))
        cw_membership_update('products', $pid, $membership_ids, 'product_id');
}

function cw_group_edit_copy_product_status($status) {
    global $ge_id;

    if (!$ge_id) return;
    while($pid = cw_group_edit_each($ge_id, 1, $product_id))
        cw_call('cw_product_update_status', array($pid, $status));
}

function cw_group_edit_update_category($ge_id, $cat, $fields, $data) {
    if (!$ge_id || !$cat || !count($fields)) return;

    global $tables, $config, $edited_language;

    $query_fields = cw_query_column("desc $tables[categories]", 'Field');
    $to_update = array_intersect($query_fields, array_keys($fields));
# kornev, add attributes to update
    if ($edited_language != $config['default_admin_language'])
        cw_unset($to_update, 'category', 'description');

    if ($to_update)
        cw_group_edit_copy($ge_id, 'categories', 'category_id', $cat, $to_update);

    if ($fields['membership_ids'])
    while($id = cw_group_edit_each($ge_id, 1, $cat))
        cw_membership_update('categories', $id, $data['membership_ids'], 'category_id');


    if ($fields['avail']) {
        while($id = cw_group_edit_each($ge_id, 1, $cat))
            cw_category_update_status($id, $data['avail']);
    }

    if ($fields['category'] || $fields['description']) {
        $to_update = array_intersect(array('category', 'description'), array_keys($fields));
        cw_group_edit_copy($ge_id, 'categories_lng', 'category_id', $cat, $to_update, "code='$edited_language'");
    }

    if($fields['image'])
        cw_group_edit_copy($ge_id, 'categories_images_thumb', 'id', $cat);

    cw_attributes_group_update($ge_id, $cat, 'C', $fields);
}

#
# General functions
#
function cw_group_edit_end($obj_id) {
    global $ge_id;
    
    if (!$ge_id) return;
    while ($pid = cw_group_edit_each($ge_id, 100, $obj_id))
        cw_func_call('cw_product_build_flat', array('product_id' => $pid));
}

function cw_group_edit_copy($ge_id, $tbl, $field, $value, $fields = array(), $add_cond = '') {
    global $tables;

    if (!$ge_id) return;

    if (!$fields)
        $fields = cw_query_column("show columns from ".$tables[$tbl]." where Extra != 'auto_increment'", 'Field');

    if ($add_cond) $add_cond = ' and '.$add_cond;
    $current_record = cw_addslashes(cw_query_first("select * from ".$tables[$tbl]." where $field='$value'".$add_cond));
    unset($current_record[$field]);
    
    while ($id = cw_group_edit_each($ge_id, 1, $value)) {
        $count = cw_query_first_cell("select count(*) from ".$tables[$tbl]." where $field = '$id'".$add_cond);
        if (!$count) {
            $current_record[$field] = $id;
            $fields[] = $field;
            cw_array2insert($tbl, $current_record, 1, $fields);
        }
        else
            cw_array2update($tbl, $current_record, "$field = '$id'".$add_cond, $fields);
    }
}

function cw_group_edit_delete($tbl, $obj_id) {
    global $ge_id, $tables;

    if (!$ge_id) return;
    while ($id = cw_group_edit_each($ge_id, 100, $obj_id))
        db_query("delete from ".$tables[$tbl]." where obj_id in ('".implode("','", $id)."')");
}


function cw_group_edit_count($ge_id) {
    global $tables;

    return cw_query_first_cell("select count(*) from $tables[group_editing] where ge_id = '$ge_id'");
}

function cw_group_edit_add($data, $ge_id = false) {
    global $tables, $APP_SESS_ID;

    if (!$ge_id)
        $ge_id = md5(uniqid(rand(0, time())));

    if (!is_array($data))
        $data = array($data);

    $query_data = array(
        'sess_id' => $APP_SESS_ID,
        'ge_id' => $ge_id
    );

    foreach ($data as $pid) {
        if (empty($pid))
            continue;
        $query_data['obj_id'] = $pid;
        cw_array2insert('group_editing', $query_data);
    }
    return $ge_id;
}

function cw_group_edit_each($ge_id, $limit = 1, $obj_id = 0) {
    global $__ge_res, $tables;

    if (!is_bool($__ge_res) && (!is_resource($__ge_res) || strpos(@get_resource_type($__ge_res), "mysql ") !== 0))
        $__ge_res = false;

    if ($__ge_res === true) {
        $__ge_res = false;
        return false;
    }
    elseif ($__ge_res === false) {
        $__ge_res = db_query("select obj_id from $tables[group_editing] where ge_id = '$ge_id'");
        if (!$__ge_res) {
            $__ge_res = false;
            return false;
        }
    }

    $res = true;
    $ret = array();
    $limit = intval($limit);
    if ($limit <= 0)
        $limit = 1;

    $orig_limit = $limit;
    while (($limit > 0) && ($res = db_fetch_row($__ge_res))) {
        if ($obj_id == $res[0])
            continue;
        $ret[] = $res[0];
        $limit--;
    }

    if (!$res) {
        cw_group_edit_reset($ge_id);
        $__ge_res = !empty($ret);
    }

    if (empty($ret))
        return false;

    return ($orig_limit == 1) ? $ret[0] : $ret;
}

function cw_group_edit_check($ge_id, $id) {
    global $tables;

    return (cw_query_first_cell("select count(*) FROM $tables[group_editing] WHERE ge_id = '$ge_id' AND obj_id = '$id'") > 0);
}

function cw_group_edit_reset($ge_id) {
    global $__ge_res;

    if ($__ge_res !== false)
        @db_free_result($__ge_res);

    $__ge_res = false;
}
