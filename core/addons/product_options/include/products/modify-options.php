<?php
cw_load('cart');
$top_message = &cw_session_register('top_message', array());


#
# Copy product option to another product
#
function cw_copy_class($product_option_id, $product_id = false) {
    global $tables, $ge_id;

    $data['class'] = cw_query_first("SELECT * FROM $tables[product_options] WHERE product_option_id = '$product_option_id'");
    $data['product_options_values'] = cw_query("SELECT * FROM $tables[product_options_values] WHERE product_option_id = '$product_option_id'");
    $data['product_options_lng'] = cw_query("select * from $tables[product_options_lng] where product_option_id = '$product_option_id'");
    $data['product_options_values_lng'] = cw_query("SELECT $tables[product_options_values_lng].* FROM $tables[product_options_values_lng], $tables[product_options_values] WHERE $tables[product_options_values_lng].option_id = $tables[product_options_values].option_id AND $tables[product_options_values].product_option_id = '$product_option_id'");

    if (empty($product_id))
    while($pid = cw_group_edit_each($ge_id, 1, $product_id))
        cw_add_class_data($data, $pid);
    else {
        if (!is_array($product_id))
            $product_id = array($product_id);

        foreach ($product_id as $pid)
            cw_add_class_data($data, $pid);
    }
}

#
# Add packed class data to product
#
function cw_add_class_data($data, $product_id) {
    global $tables;

    # Update class data
    $comp = $data['class'];
    $comp['product_id'] = $product_id;
    cw_unset($comp, "product_option_id");
    $comp = cw_addslashes($comp);

    $product_option_id = cw_query_first_cell("SELECT product_option_id FROM $tables[product_options] WHERE class = '$comp[class]' AND product_id = '$comp[product_id]'");
    $is_new = empty($product_option_id);
    if (!empty($product_option_id)) {
        cw_array2update("product_options", $comp, "product_option_id = '$product_option_id'");
    } else {
        $product_option_id = cw_array2insert("product_options", $comp);
    }

    # Update class multilanguage data
    db_query("DELETE FROM $tables[product_options_lng] WHERE product_option_id = '$product_option_id'");
    foreach ($data['product_options_lng'] as $v) {
        $v['product_option_id'] = $product_option_id;
        $v = cw_addslashes($v);
        cw_array2insert("product_options_lng", $v, true);
    }
    # Update class options
    $ids = array();
    foreach ($data['product_options_values'] as $k => $opt) {
        $opt['product_option_id'] = $product_option_id;
        $old_option_id = $opt['option_id'];
        cw_unset($opt, "option_id");
        $opt = cw_addslashes($opt);
        $option_id = cw_query_first_cell("SELECT option_id FROM $tables[product_options_values] WHERE product_option_id = '$product_option_id' AND name = '$opt[name]'");
        if (empty($option_id)) {
            $option_id = cw_array2insert("product_options_values", $opt);
        } else {
            cw_array2update("product_options_values", $opt, "option_id = '$option_id'");
        }
        $ids[$old_option_id] = $option_id;
    }

    # Update class option multilanguage data
    db_query("DELETE FROM $tables[product_options_values_lng] WHERE option_id = '$option_id'");
    foreach ($data['product_options_values_lng'] as $v) {
        if (!isset($ids[$v['option_id']]))
            continue;

        $v['option_id'] = $ids[$v['option_id']];
        $v = cw_addslashes($v);
        cw_array2insert("product_options_values_lng", $v, true);
    }

    # Detect and delete old product option class options
    $ids = cw_query_column("SELECT option_id FROM $tables[product_options_values] WHERE product_option_id = '$product_option_id' AND option_id NOT IN ('".implode("','", $ids)."')");
    if (!empty($ids)) {
        db_query("DELETE FROM $tables[product_options_values] WHERE product_option_id = '$product_option_id' AND option_id IN ('".implode("','", $ids)."')");
        db_query("DELETE FROM $tables[product_options_values_lng] WHERE option_id IN ('".implode("','", $ids)."')");
        db_query("DELETE FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $ids)."')");
    }
}

if ($REQUEST_METHOD == "POST") {

    $class_names = cw_query_hash("SELECT product_option_id, field FROM $tables[product_options] WHERE product_id = '$product_id'", "product_option_id", false, true);
    $refresh = $rebuild = $rebuild_quick = false;

    #
    # Add/Update product options properties
    #
    if ($action == 'product_options_modify' && $po_classes) {
        $old_avails = cw_query_hash("SELECT product_option_id, avail FROM $tables[product_options] WHERE product_option_id IN ('".implode("','", array_keys($po_classes))."') AND type = ''", "product_option_id", false, true);
        foreach ($po_classes as $k => $v) {
            db_query("UPDATE $tables[product_options] SET orderby = '$v[orderby]', avail = '$v[avail]' WHERE product_option_id = '$k'");
            if (isset($old_avails[$k]) && $v['avail'] != $old_avails[$k])
                $rebuild = true;
            if ($ge_id && $fields['option_field'][$k])
                cw_copy_class($k);
        }

        $rebuild_quick = true;
        $top_message['content'] = cw_get_langvar_by_name("msg_adm_product_options_upd");
        $top_message['type'] = "I";
        $refresh = true;
    }
    elseif ($action == 'product_options_delete' && $to_delete) {
        #
        # Delete class
        #
        $to_delete = array_keys($to_delete);
        foreach ($to_delete as $cid) {
            $field = cw_query_first_cell("SELECT field FROM $tables[product_options] WHERE product_option_id = '$cid'");
            $fields_to_delete[] = cw_call('cw_attributes_cleanup_field', array('field' => $field));
            cw_delete_po_class($cid);
            if ($ge_id && $fields['option_field'][$cid]) {
                while($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
                    $cid0 = cw_query_first_cell("SELECT product_option_id FROM $tables[product_options] WHERE product_id = '$pid' AND class = '".addslashes($class_names[$cid])."'");
                    if (empty($cid0))
                        continue;

                    cw_delete_po_class($cid0);
                }
            }
        }
        $product_options_fields = cw_query("SELECT DISTINCT field FROM $tables[product_options]");
        foreach($product_options_fields as $k => $v)
                        $product_options_fields[$k] = cw_call('cw_attributes_cleanup_field', array('field' => $v['field']));

        foreach($fields_to_delete as $f){
            if(!in_array($f, $product_options_fields)) {
                $attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('product_options_'.$f));
                cw_call('cw_attributes_delete', array($attribute_id));
            }
        }

        $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_option_del'), 'type' => 'I');
        $refresh = $rebuild = $rebuild_quick = true;
    }
}
if($action == 'product_options_add') {
    if ($add['type'] == 'T') unset($list, $new_list, $new_list_as_text);

    if (empty($add['field']) || empty($add['name']))
        cw_refresh($product_id, 'product_options', '&product_option_id='.$product_option_id);

    $add['product_id'] = $product_id;
    if (!$product_option_id) $product_option_id = cw_array2insert('product_options', $add, array('class', 'name', 'product_id'));

    $lng_info = $add;

    if ($edited_language != $config['default_admin_language']) unset($add['name']);
    $attr_field = cw_query_first_cell("SELECT field FROM $tables[product_options] WHERE product_option_id = '$product_option_id'");
    $attr_field = cw_call('cw_attributes_cleanup_field', array('field' => 'product_options_'.$attr_field));
    cw_array2update('product_options', $add, "product_option_id = '$product_option_id'");

    if ($ge_id && !empty($fields)) {
        foreach ($field_to_update as $k => $v)
            if (!isset($fields[$k])) {
                $k = array_search($k, $field_to_update);
                unset($field_to_update[$k]);
            }

        while($pid = cw_group_edit_each($ge_id, 1, $product_id))
            cw_array2update('product_options', $add, "product_id = '$pid' AND class = '".addslashes($class_names[$product_option_id])."'", $field_to_update);
    }

    $lng_info['product_option_id'] = $product_option_id;
    $lng_info['code'] = $edited_language;
    cw_array2insert('product_options_lng', $lng_info, 1);

    if ($add['type'] == 'T')
        cw_delete_product_options_values($product_option_id);

    if ($ge_id && ($fields['option_field'] || $fields['option_name'])) {
        if (!$fields['option_field']) unset($add['class']);
        if (!$fields['option_name']) unset($add['name']);

        while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
            $add['product_option_id'] = cw_query_first_cell("SELECT product_option_id FROM $tables[product_options] WHERE product_id = '$pid' AND class = '".addslashes($class_names[$product_option_id])."'");
            if ($add['product_option_id']) cw_array2insert('product_options_lng', $add, 1);
        }
    }

    if ($ge_id && $fields['option_type'])
        while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
            $upd_product_option_id = cw_query_first_cell("SELECT product_option_id FROM $tables[product_options] WHERE product_id = '$pid' AND class = '".addslashes($class_names[$product_option_id])."'");
            cw_delete_product_options_values($upd_product_option_id);
        }

    if ($add['type'] != 'T') {
        //cw_log_add('variants_new_list', $new_list);

        if (!empty($new_list_as_text)) {
            $_new_list_lines = explode("\n", $new_list_as_text);
            $last_orderby = 0;

            if (!empty($list)) {
                foreach ($list as $_l) {
                    if ($_l['orderby'] > $last_orderby)
                        $last_orderby = $_l['orderby'];
                }
            }

            if (!empty($new_list)) {
                foreach ($new_list as $_nl) {
                    if ($_nl['orderby'] > $last_orderby)    
                        $last_orderby = $_nl['orderby'];
                }
            } else {
                $new_list = array();
            }  
            foreach ($_new_list_lines as $new_list_line) {
                $new_list_line = trim($new_list_line);
                if (!empty($new_list_line)) { 
                    $last_orderby++;
                    $new_list[] = array('name'=>$new_list_line, 'orderby'=>$last_orderby, 'avail'=>1);
                }
            }
        } 
        //cw_log_add('variants_new_list', array($new_list_as_text, $new_list));

        if ($new_list)
        foreach($new_list as $v) {
            if (!$v['name']) continue;
            $v['product_option_id'] = $product_option_id;

            $option_id = cw_array2insert('product_options_values', $v, false, array('name', 'product_option_id'));
            $list[$option_id] = $v;
        }

        if ($list)
        foreach ($list as $k=>$v) {
            if ($add['type'] != 'Y') {
                $list[$k]['price_modifier'] = 0;
                $list[$k]['modifier_type'] = 0;
                $list[$k]['cost_modifier'] = 0;
                $list[$k]['cost_modifier_type'] = 0;
            }
            $v['price_modifier'] = cw_convert_number($v['price_modifier']);
            $v['cost_modifier'] = cw_convert_number($v['cost_modifier']);

            $v['code'] = $edited_language;
            $v['option_id'] = $k;
            $v['name'] = $v['name'];

            $fields_to_update = array('orderby', 'avail', 'price_modifier', 'modifier_type', 'cost_modifier', 'cost_modifier_type');
            if ($edited_language == $config['default_admin_language'])
                $fields_to_update[] = 'name';

            cw_array2update('product_options_values', $v, "option_id = '$k'", $fields_to_update);
            cw_array2insert('product_options_values_lng', $v, true, array('code', 'option_id', 'name'));
        }

        if ($ge_id && $fields['options'])
        while ($pid = cw_group_edit_each($ge_id, 1, $product_id))
        foreach ($list as $k => $v) {
            $k1 = cw_query_first_cell("SELECT o1.option_id FROM $tables[product_options] as c0, $tables[product_options_values] as o0, $tables[product_options] as c1, $tables[product_options_values] as o1 WHERE c0.product_option_id = o0.product_option_id AND o0.option_id = '$k' AND c1.class = c0.class AND c1.product_option_id = o1.product_option_id AND o0.name = o1.name AND c1.product_id = '$pid'");
            if (empty($k1)) continue;

            $v['price_modifier'] = cw_convert_number($v['price_modifier']);
            $v['cost_modifier'] = cw_convert_number($v['cost_modifier']);
            $query_data = array(
                "code" => $edited_language,
                "option_id" => $k1,
                "name" => $v['name']
            );
            if ($edited_language != $config['default_admin_language'])
                unset($v['name']);

            cw_array2update('product_options_values', $v, "option_id = '$k1'");

            if (!empty($query_data['name']))
                cw_array2insert("product_options_values_lng", $query_data, true);
        }
    }
    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_option_upd'), 'type' => 'I');
    $refresh = $rebuild = $rebuild_quick = true;
}
elseif ($action == 'product_option_delete' && $product_option_id && $to_delete) {
        #
        # Delete class option(s)
        #
        $url_anchor = "#modify_class";
        $to_delete = array_keys($to_delete);

        if ($ge_id && $fields['options']) {
            $name_where = cw_query_hash("SELECT $tables[product_options].class, $tables[product_options_values].name FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].option_id IN ('".implode("','", $to_delete)."')", "class", true, true);
            foreach ($name_where as $cn => $opts) {
                $name_where[$cn] = "($tables[product_options].class = '".addslashes($cn)."' AND $tables[product_options_values].name IN ('".implode("','", cw_addslashes($opts))."'))";
            }

            $name_where = " AND ".implode(" OR ", $name_where);

            while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
                $opts = cw_query_column("SELECT option_id FROM $tables[product_options_values], $tables[product_options] WHERE $tables[product_options_values].product_option_id = $tables[product_options].product_option_id AND $tables[product_options].product_id = '$pid'".$name_where);

                db_query("DELETE FROM $tables[product_options_values] WHERE option_id IN ('".implode("','", $opts)."')");
                db_query("DELETE FROM $tables[product_options_values_lng] WHERE option_id IN ('".implode("','", $opts)."')");
                db_query("DELETE FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $opts)."')");
            }
        }

        db_query("DELETE FROM $tables[product_options_values] WHERE option_id IN ('".implode("','", $to_delete)."')");
        db_query("DELETE FROM $tables[product_options_values_lng] WHERE option_id IN ('".implode("','", $to_delete)."')");
        db_query("DELETE FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $to_delete)."')");

        $top_message['content'] = cw_get_langvar_by_name("msg_adm_option_del");
        $top_message['type'] = "I";

        $refresh = $rebuild = $rebuild_quick = true;
}
elseif ($action == 'products_options_ex_delete' && $to_delete) {
        #
        # Delete exception(s)
        #
        $url_anchor = "#exceptions";
        $to_delete = array_keys($to_delete);

        if ($ge_id && !empty($fields['exceptions'])) {
            foreach ($to_delete as $eid) {
                if (!$fields['exceptions'][$eid])
                    continue;

                $name_where = cw_query_hash("SELECT $tables[product_options].class, $tables[product_options_values].name FROM $tables[products_options_ex], $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].option_id = $tables[products_options_ex].option_id AND $tables[products_options_ex].exception_id = '$eid'", "class", true, true);
                foreach ($name_where as $cn => $opts) {
                    $name_where[$cn] = "($tables[product_options].class = '".addslashes($cn)."' AND $tables[product_options_values].name IN ('".implode("','", cw_addslashes($opts))."'))";
                }

                $name_where = " AND ".implode(" OR ", $name_where);

                while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
                    $opts = cw_query_column("SELECT option_id FROM $tables[product_options_values], $tables[product_options] WHERE $tables[product_options_values].product_option_id = $tables[product_options].product_option_id AND $tables[product_options].product_id = '$pid'".$name_where);
                    if (empty($opts))
                        continue;

                    $eid = cw_query_first_cell("SELECT $tables[products_options_ex].exception_id, COUNT($tables[products_options_ex].exception_id) as cnt, COUNT(ex.exception_id) as cnt0 FROM $tables[products_options_ex] LEFT JOIN $tables[products_options_ex] as ex ON $tables[products_options_ex].exception_id = ex.exception_id WHERE option_id IN ('".implode("','", $opts)."') GROUP BY exception_id HAVING cnt = cnt0 ORDER BY cnt DESC LIMIT 1");
                    if (!empty($eid))
                        db_query("DELETE FROM $tables[products_options_ex] WHERE exception_id = '$eid'");
                }
            }
        }

        db_query("DELETE FROM $tables[products_options_ex] WHERE exception_id IN ('".implode("','", $to_delete)."')");

        $top_message['content'] = cw_get_langvar_by_name("msg_adm_product_option_exc_del");
        $top_message['type'] = "I";
        $refresh = $rebuild_quick = true;
}
elseif ($action == 'products_options_ex_add' && $new_exception) {
        #
        # Add new exception
        #
        $url_anchor = "#exceptions";
        foreach ($new_exception as $k => $v) {
            if (empty($v)) {
                unset($new_exception[$k]);
            }
        }

        $is_exist = (cw_query_first_cell("SELECT COUNT(*) as count FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $new_exception)."') GROUP BY exception_id ORDER BY count DESC") == count($new_exception));
        if (!$is_exist && count($new_exception) > 0) {
            $exception_id = cw_query_first_cell("SELECT MAX(exception_id) FROM $tables[products_options_ex]")+1;
            foreach ($new_exception as $v) {
                cw_array2insert("products_options_ex", array("exception_id" => $exception_id, "option_id" => $v));
            }

            if ($ge_id && $fields['new_exception']) {
                $name_where = cw_query_hash("SELECT $tables[product_options].class, $tables[product_options_values].name FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].option_id IN ('".implode("','", $new_exception)."')", "class", true, true);
                foreach ($name_where as $cn => $opts) {
                    $name_where[$cn] = "($tables[product_options].class = '".addslashes($cn)."' AND $tables[product_options_values].name IN ('".implode("','", cw_addslashes($opts))."'))";
                }
                $name_where = empty($name_where) ? "" : (" AND ".implode(" OR ", $name_where));

                $names = cw_query("SELECT product_option_id, name FROM $tables[product_options_values] WHERE option_id IN ('".implode("','", $new_exception)."')");
                if ($names) {
                    while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
                        $is_exist = (cw_query_first_cell("SELECT COUNT(*) as count FROM $tables[products_options_ex], $tables[product_options], $tables[product_options_values] WHERE $tables[products_options_ex].option_id = $tables[product_options_values].option_id AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options].product_id = '$pid' $name_where GROUP BY $tables[products_options_ex].exception_id ORDER BY count DESC") == count($new_exception));
                        if (!$is_exist) {
                            $opts = cw_query_column("SELECT $tables[product_options_values].option_id FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options].product_id = '$pid'".$name_where);
                            foreach($opts as $v) {
                                cw_array2insert("products_options_ex", array("exception_id" => $exception_id, "option_id" => $v));
                            }
                        }
                    }
                }
            }

            $top_message['content'] = cw_get_langvar_by_name("msg_adm_product_options_exc_add");
            $top_message['type'] = "I";
        }
        else {
            $top_message['content'] = cw_get_langvar_by_name("msg_adm_product_options_exc_no_add");
            $top_message['type'] = "E";
        }

        $refresh = $rebuild_quick = true;
}
elseif ($action == 'product_options_js_update') {
    if ($ge_id && $fields['js'])
        while ($pid = cw_group_edit_each($ge_id, 1))
            cw_array2insert("product_options_js", array("product_id" => $pid, "javascript_code" => $js_code), true);
    else
        cw_array2insert("product_options_js", array("product_id" => $product_id, "javascript_code" => $js_code), true);

    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_options_js_upd'), 'type' => 'I');
    $refresh = true;
}

# arteml, not reliable condition based on $section or $js_tab, but otherwise it rebuilds variants after submit of any product modify tab
if ($action && ($section=='options' || $js_tab=='product_options')) {
    if ($ge_id)
    while ($pid = cw_group_edit_each($ge_id, 1)) {
        if ($rebuild)
            cw_rebuild_variants($pid);
        if ($rebuild_quick)
            cw_func_call('cw_product_build_flat', array('product_id' => $pid));
    }
    cw_rebuild_variants($product_id);
    cw_func_call('cw_product_build_flat', array('product_id' => $product_id, 'attribute_field'=>$attr_field));

    if ($refresh) {
        $added = '';
        if (!empty($product_option_id))
            $added = "&product_option_id=$product_option_id";

        cw_refresh($product_id, 'product_options', $added);
    }
}

# Get the product options list
$product_options = cw_call('cw_get_product_classes', array($product_id, null, null, $edited_language));
$products_options_ex = cw_call('cw_get_product_exceptions', array($product_id));
$product_options_js = cw_call('cw_get_product_js_code', array($product_id));
if ($product_options && !empty($product_option_ids)) {
    foreach ($product_options as $k => $v) {
        if ($product_option_ids[$v['product_option_id']])
            $product_options[$k]['multi'] = 'Y';
    }
}

if ($product_option_id && $product_options)
    foreach ($product_options as $v) {
        if ($v['product_option_id'] == $product_option_id) {
            $product_option = $v;
            break;
        }
    }

if (!empty($product_options) && !empty($products_options_ex)) {
    $options = array();
    foreach($product_options as $c) {
        if ($c['avail'] != 'Y')
            continue;

        if ($c['type'] == 'T') {
            $options[$c['product_option_id']] = '';

        } elseif (!empty($c['options'])) {
            foreach ($c['options'] as $oid => $o) {
                if ($o['avail'] == 'Y') {
                    $options[$c['product_option_id']] = $oid;
                    break;
                }
            }

        }
    }

    if (!empty($options) && !cw_check_product_options($product_id, $options))
        $smarty->assign('def_options_failure', true);
}

$smarty->assign('product_options', $product_options);
$smarty->assign('products_options_ex', $products_options_ex);
$smarty->assign('product_options_js', $product_options_js);

if ($product_option && empty($product_option['options']))
    cw_unset($product_option, 'options');
$smarty->assign('product_option', $product_option);

$smarty->assign('product_option_lng', $product_option_lng);

$smarty->assign('submode', $submode);
