<?php

if (!isset($addons['deal_of_day'])) {
    return;
}

if (AREA_TYPE != 'A') {
    return;
}

global $mode, $generator_id, $bonus_id, $action;

$mode = (string)$mode;
$action = (string)$action;
$generator_id = (int)$generator_id;
$bonus_id = (int)$bonus_id;

$addon_modes = array(
);

//2 revision
$addon_actions = array(
    'update'        => 'dod_update',
    'add'           => 'dod_modify',
    'delete'        => 'dod_delete',
    'details'       => 'dod_details',
    'modify'        => 'dod_modify',
    'form'          => 'dod_form',
    'delete_image'  => 'dod_delete_image',
    'coupons'       => 'dod_get_coupons_ajax',
    'attributes'    => 'dod_get_attr_ajax'
);


global $available_fields, $optional_fields, $skip_striptags_fields, $date_fields;

$available_fields = array(
    'generator_id'      => 'int',
    'title'         => 'string',
    'description'   => 'string',
    'position'      => 'int',
    'startdate'     => 'int',
    'enddate'       => 'int',
    'dod_interval'  => 'int',
    'dod_interval_type'  => 'string', 
    'no_item_repeat'=> 'bool',
    'active'        => 'bool'
);

$optional_fields = array('active', 'position', 'dod_interval', 'dod_interval_type', 'no_item_repeat');
$date_fields = array('startdate', 'enddate');
$skip_striptags_fields = array('description');

$location[] = array(cw_get_langvar_by_name('lbl_dod_manage_generators'), '');

$smarty->assign('main', 'deal_of_day');
$smarty->assign('mode', $mode);

if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $smarty->assign('action', 'list');
        return cw_call('dod_show'); // default action
    }

}

$smarty->assign('action', $action);
return cw_call($addon_actions[$action], array($generator_id));

function dod_show() {
    global $available_fields, $optional_fields, $tables, $smarty, $top_message, $target;

    $generators = array();

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $from_tbls[] = 'dod_generators';
    $fields = array_keys($available_fields);
    $where[] = 1;
    $orderbys[] = 'position';
    $orderbys[] = 'generator_id';

    $search_query_count = cw_db_generate_query('count(generator_id)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    $total_items_res_id = db_query($search_query_count);
    $number_generators = db_num_rows($total_items_res_id);

    if (empty($number_generators)) {
        return null;
    }

    global $navigation, $page;

    $navigation = cw_core_get_navigation($target, $number_generators, $page);
    $limit_str = " LIMIT $navigation[first_page], $navigation[objects_per_page]";

    $generators = cw_query($search_query . $limit_str);

    if (empty($generators)) {
        return null;
    }

    $generators = array_map(
        create_function('$elm', '$elm["description"] = strip_tags($elm["description"]); return $elm;'),
        $generators
    );

    $smarty->assign('dod_generators', cw_stripslashes($generators));

    $navigation['script'] = 'index.php?target='.$target;

    $smarty->assign('navigation', $navigation);

}


function dod_update() {
    global $tables, $top_message, $available_fields, $optional_fields, $skip_striptags_fields;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        dod_redirect();
    }

    global $dod_generators;

    if (empty($dod_generators) || !is_array($dod_generators)) {
        dod_redirect();
    }


    $generator_ids = array_unique(array_map('dod_process_ids', array_keys($dod_generators)));
    $generator_ids_query = implode('\', \'', $generator_ids);


    $generator_ids = cw_query_column('SELECT `generator_id` FROM ' . $tables['dod_generators'] . ' WHERE ' . 'generator_id IN (\'' . $generator_ids_query . '\')');

    if (empty($generator_ids)) {
        dod_redirect();
    }


    if (isset($available_fields['generator_id'])) {
        unset($available_fields['generator_id']);
    }

    $error = null;

    foreach ($generator_ids as $generator_id) {
        $data = array();
        $additional_lang_data = array();

        if (!isset($dod_generators[$generator_id])) {
            continue;
        }

        foreach ($available_fields as $field => $field_type) {
            if (isset($dod_generators[$generator_id][$field])) {

                $result = settype($dod_generators[$generator_id][$field], $field_type);
                if ($result === false) {
                    $error = 'msg_dod_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field . ' generator ID: ' . $generator_id);
                    break(2);
                }

                if (empty($dod_generators[$generator_id][$field])) {
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    }
                } else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                        $dod_generators[$generator_id][$field] = cw_strip_tags($dod_generators[$generator_id][$field]);
                    }
                    $data[$field] = & $dod_generators[$generator_id][$field];
                }
            } else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                }
            }
        }
        if (!empty($data)) {
            cw_array2update($tables['dod_generators'], cw_addslashes($data), 'generator_id = \'' . $generator_id . '\'');
        }
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_dod_updated_succes'), 'type' => 'I');

    if (!empty($error)) {
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
    }

    dod_redirect();
}

function dod_delete() {
    global $tables, $top_message;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        dod_redirect();
    }

    global $generator_ids;

    if (empty($generator_ids) || !is_array($generator_ids)) {
        dod_redirect();
    }

    $generator_ids = array_unique(array_map('dod_process_ids', array_keys($generator_ids)));
    $generator_ids_query = implode('\', \'', $generator_ids);


    db_query("DELETE FROM $tables[dod_generators] WHERE generator_id IN ('" . $generator_ids_query . "')");
    db_query("DELETE FROM $tables[dod_bonuses] WHERE generator_id IN ('" . $generator_ids_query . "')");
    db_query("DELETE FROM $tables[dod_bonus_details] WHERE generator_id IN ('" . $generator_ids_query . "')");

    //cw_attributes_cleanup($generator_ids, dod_ATTR_ITEM_TYPE);

    $top_message['content'] = cw_get_langvar_by_name('msg_dod_deleted');

    dod_redirect();
}



function dod_details($generator_id) {
    global $tables, $top_message, $smarty, $available_fields;

    if (empty($generator_id)) {
        dod_redirect();
    }

    $generator_id = (int)$generator_id;

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        dod_redirect($generator_id);
    }

    $generator_data = cw_query_first('SELECT `' . implode('`, `', array_keys($available_fields)) . '` FROM ' . $tables['dod_generators'] . ' WHERE generator_id = \'' . $generator_id . '\'');

    if (empty($generator_data) || !is_array($generator_data)) {
        dod_redirect();
    }

    $smarty->assign('generator_data', cw_stripslashes($generator_data));


    // bonuses
    $sess_bonuses =& cw_session_register('_dod_bonus');
    $bonuses = dod_get_generator_bonuses($generator_id);

    if (!is_array($bonuses) || empty($bonuses)) {
        $bonuses = array();
    }
    if (!empty($sess_bonuses)) {
        if (is_array($sess_bonuses)) {
            $bonuses = array_merge($bonuses, $sess_bonuses);
            $smarty->assign('not_sav_bons', $sess_bonuses);
        }
        cw_session_unregister('_dod_bonus');
    }

    $smarty->assign('dod_bonus', $bonuses);

    $bonus_types = array_flip(array_keys($bonuses));

    $bonus_types = array_map(
        create_function('$elm', 'return 1;'),
        $bonus_types
    );

    if (empty($bonus_types)) {
        $bonus_types = array();
    }

    $sess_bonus_types =& cw_session_register('_dod_bonuses');

    if (!empty($sess_bonus_types)) {
        if (is_array($sess_bonus_types)) {
            $bonus_types = array_merge($bonus_types, $sess_bonus_types);
        }
        cw_session_unregister('_dod_bonuses');
    }

    $smarty->assign('dod_bonuses', $bonus_types);

    cw_load('attributes');
    $dod_attr = cw_call('cw_attributes_filter', array(array('addon'=>'','item_type' => 'P')));

    $smarty->assign('dod_attr', $dod_attr);
    $smarty->assign('dod_coupons', dod_get_coupons());
    $smarty->assign('dod_mans', dod_get_manufacturers());

    global $js_tab;
    if (isset($js_tab)) {
        $smarty->assign('js_tab', (string)$js_tab);
    }

    global $app_catalogs, $target, $mode;
    $smarty->assign('dod_url_get_coupons', "$app_catalogs[admin]/index.php?target=$target&mode=$mode&action=coupons&generator_id=$generator_id&js_tab=dod_generator_bonuses");
    $smarty->assign('dod_url_add_coupon', "$app_catalogs[admin]/index.php?target=coupons");

    //$smarty->assign('dod_url_add_zone', "$app_catalogs[admin]/index.php?target=shipping_zones&mode=add");


    //attributes
    global $attributes;
    global $edited_language;
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $generator_id, 'item_type' => dod_ATTR_ITEM_TYPE, 'prefilled' => $attributes));
    $smarty->assign('attributes', $attributes);
}

function dod_modify($generator_id) {
    global $tables, $top_message, $js_tab;

    $generator_id = (int)$generator_id;

    $default_tab = 'dod_generator_details';
    $_action = 'details';

    if (empty($generator_id)) {
        $_action = 'form';
    }

    if (empty($js_tab)) {
        $js_tab = $default_tab;
    }

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        dod_redirect($generator_id, $_action, $js_tab);
    }

    if (!empty($generator_id)) {
        $generator_id = cw_query_first_cell('SELECT generator_id FROM ' . $tables['dod_generators'] . ' WHERE generator_id = \'' . $generator_id . '\'');

        if (empty($generator_id)) {
            dod_redirect();
        }
    }


    $entities = array('details', 'bonuses');
    $cw_prefix = __FUNCTION__ . '_';

    $errors = array();

    foreach ($entities as $entity) {
        $cw_tocall = $cw_prefix . $entity;
        if (function_exists($cw_tocall)) {

            $errors[$entity] = cw_call($cw_tocall, array($generator_id));

            if ($entity == 'details' && is_numeric($errors[$entity][0]) && empty($generator_id)) {
                $generator_id = $errors[$entity][0];
                $_action = 'details';
            }
        } else {
            $errors[$entity] = array(false, 'A function "' . $cw_tocall . '" related to "' . $entity . '" is not defeined');
        }
    }

    $error = null;

    foreach ($errors as $entity => $error_data) {
        if ($error_data[0] != false) {
            continue;
        }

        if (!empty($error)) {
            $error .= "<br />\n";
        }
        $error .= $error_data[1];
    }


    if (!empty($error)) {
        $top_message = array('content' => $error, 'type' => 'E');
        dod_redirect($generator_id, $_action, $js_tab);
    }
    $top_message = array('content' => cw_get_langvar_by_name('msg_dod_updated_succes'), 'type' => 'I');

    dod_redirect($generator_id, $_action, $js_tab);
}

function dod_form() {
    global $smarty;

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        dod_redirect(0, 'form', 'details');
    }

    $_generator_data =& cw_session_register('_generator_data');
    if (!empty($_generator_data)) {
        $smarty->assign('generator_data', $_generator_data);
        cw_session_unregister('_generator_data');
    }

    $_dod_bonuses =& cw_session_register('_dod_bonuses');
    if (!empty($_dod_bonuses)) {
        $smarty->assign('dod_bonuses', $_dod_bonuses);
        cw_session_unregister('_dod_bonuses');
    }

    $_dod_bonus =& cw_session_register('_dod_bonus');
    if (!empty($_dod_bonus)) {
        $smarty->assign('dod_bonus', $_dod_bonus);
        cw_session_unregister('_dod_bonus');
    }


    $smarty->assign('dod_coupons', dod_get_coupons());
    $smarty->assign('dod_mans', dod_get_manufacturers());

    global $js_tab;
    if (isset($js_tab)) {
        $smarty->assign('dod_generator_js_tab', (string)$js_tab);
    }

    global $app_catalogs, $target, $mode;
    $smarty->assign('dod_url_get_coupons', "$app_catalogs[admin]/index.php?target=$target&mode=$mode&action=coupons&generator_id=&js_tab=dod_generator_bonuses");
    $smarty->assign('dod_url_add_coupon', "$app_catalogs[admin]/index.php?target=coupons");

    //attributes
    global $attributes;
    global $edited_language;
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => dod_ATTR_ITEM_TYPE, 'prefilled' => $attributes));
    $smarty->assign('attributes', $attributes);

}

function dod_process_ids($id) {
    return (int) $id;
}

function dod_redirect($generator_id = null, $action = null, $js_tab = null) {
    global $app_catalogs, $target, $mode;

    $generator_condition = null;
    if (!is_null($generator_id) && $generator_id === 0) {
        $generator_condition = '&generator_id=';
    }
    if (!empty($generator_id)) {
        $generator_condition = '&generator_id=' . (int)$generator_id;
    }

    $action_condition = null;
    if (!empty($action)) {
        $action_condition = '&action=' . (string)$action;
    }

    $js_tab_condition = null;
    if (!empty($js_tab)) {
        $js_tab_condition = '&js_tab=' . (string)$js_tab;
    }

    cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode$action_condition$generator_condition$js_tab_condition");

    //cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode");
}

function dod_modify_details($generator_id) {
    global $tables, $available_fields, $optional_fields, $skip_striptags_fields, $date_fields;

    /*if (empty($generator_id)) {
        return array(false, 'generator Id was not provided');
    }*/

    global $generator_data;

    if (empty($generator_data) || !is_array($generator_data)) {
        return array(true, null);
    }

    $error = null;
    $data = array();

    $excl_from_base_list = array('generator_id');
    foreach ($excl_from_base_list as $field) {
        if (isset($available_fields[$field])) {
            unset($available_fields[$field]);
        }
    }

    $additional_lang_data = array();

    foreach ($date_fields as $field) {
        if (isset($generator_data[$field]) && !empty($generator_data[$field])) {
            $generator_data[$field] = cw_core_strtotime($generator_data[$field]);
        }
    }
    foreach ($available_fields as $field => $field_type) {
        if (isset($generator_data[$field])) {

            $result = settype($generator_data[$field], $field_type);
            if ($result === false) {
                $error = 'msg_dod_incorrect_field_type';
                $additional_lang_data = array('field_name' => $field);
                break;
            }

            if ($field == 'description') {
                if ($generator_data[$field] == '<p>&#160;</p>') {
                    $generator_data[$field] = null;
                }
            }

            if (empty($generator_data[$field])) {
                if (in_array($field, $optional_fields)) {
                    $data[$field] = null;
                } else {
                    $error = 'msg_dod_empty_fields';
                    break;
                }
            } else {

                if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                    $generator_data[$field] = cw_strip_tags($generator_data[$field]);
                }
                $data[$field] = & $generator_data[$field];
            }
        } else {
            if ($field_type == 'bool') {
                $data[$field] = 0;
            } else {
                if (in_array($field, $optional_fields)) {
                    $data[$field] = null;
                } else {
                    $error = 'msg_dod_empty_fields';
                    break;
                }
            }
        }
    }

    $sess_generator_data = &cw_session_register('_generator_data');

    $GLOBALS['_generator_data'] = & $generator_data;
    cw_session_register('_generator_data');


    if (!empty($error)) {
        return array(false, cw_get_langvar_by_name($error, $additional_lang_data));
    }

    global $attributes;
    $data['attributes'] = $attributes;
    $error = cw_error_check($data, array(), dod_ATTR_ITEM_TYPE);
    //cw_attributes_check($array_to_check['attribute_class_id'], $array_to_check['attributes'], $attributes_type, $index)
    if (!empty($error)) {
        return array(false, $error);
    }

    global $file_upload_data;

    if (empty($generator_id)) {

        if (empty($data)) {
            return array(false, null);
        }

        $generator_id = cw_array2insert($tables['dod_generators'], cw_addslashes($data));

    } else {

        cw_array2update($tables['dod_generators'], cw_addslashes($data), 'generator_id = \'' . $generator_id . '\'');
    }

    cw_call('cw_attributes_save', array('item_id' => $generator_id, 'item_type' => dod_ATTR_ITEM_TYPE, 'attributes' => $attributes));

    cw_session_unregister('_generator_data');

    return array($generator_id, null);

}

function dod_modify_bonuses($generator_id) {
    global $tables, $bonus_names;
    global $dod_bonuses, $dod_bonus;

    if (empty($generator_id)) {
        $GLOBALS['_dod_bonuses'] = & $dod_bonuses;
        cw_session_register('_dod_bonuses');

        $GLOBALS['_dod_bonus'] = & $dod_bonus;
        cw_session_register('_dod_bonus');

        return array(true, null);
        //return array(false, 'generator Id was not provided');
    }

    db_query("DELETE FROM $tables[dod_bonuses] WHERE generator_id = '$generator_id'");
    db_query("DELETE FROM $tables[dod_bonus_details] WHERE generator_id = '$generator_id'");


    $available_fields = array(
        'bonus_id'      => 'int',
        'generator_id'  => 'int',
        'type'          => 'string',
        'apply'         => 'int',
        'coupon'        => 'string',
        'discount'      => 'float',
        'disctype'      => 'int'
    );

    $excl_from_base_list = array('bonus_id');
    foreach ($excl_from_base_list as $field) {
        if (isset($available_fields[$field])) {
            unset($available_fields[$field]);
        }
    }
    $optional_fields = array('discount', 'disctype');
    $date_fields = array();
    $skip_striptags_fields = array();

    if (empty($dod_bonuses[DOD_DISCOUNT])) {
        $dod_bonuses[DOD_DISCOUNT] = 1;
        $unused_dod_discount_bonus = 1;
    } else {
        $unused_dod_discount_bonus = 0;
    }

    if (empty($dod_bonuses) || !is_array($dod_bonuses)) {
        return array(true, null);
    }

    $available_btypes = array(DOD_DISCOUNT, DOD_FREE_PRODS, DOD_FREE_SHIP, DOD_COUPON);

    $bonuses = array();

    foreach ($dod_bonuses as $bonus_type => $trash) {
        if (!isset($dod_bonus[$bonus_type]) || empty($dod_bonus[$bonus_type]) || !in_array($bonus_type, $available_btypes)) {
            unset($dod_bonuses[$bonus_type]);
        } else {
            $bonuses[$bonus_type] = $dod_bonus[$bonus_type];
        }
    }

    unset($dod_bonus);

    if (empty($dod_bonuses) || empty($bonuses)) {
        return array(true, null);
    }

    $GLOBALS['_dod_bonuses'] = & $dod_bonuses;
    cw_session_register('_dod_bonuses');


    $errors = array();

    $tmp_optional_fields = $optional_fields;

    foreach ($bonuses as $bonus_type => $input_data) {

        $optional_fields = $tmp_optional_fields;

        $additional_lang_data = array();
        $pids = $cids = array();

        $input_data['generator_id'] = $generator_id;
        $input_data['type'] = $bonus_type;


        if ($bonus_type != DOD_COUPON) {
            $input_data['coupon'] = 1;

            if ($input_data['apply'] == DOD_APPLY_PRODS || $bonus_type == DOD_FREE_PRODS || ($bonus_type == DOD_DISCOUNT)) {

                if ((!isset($input_data['products']) && !isset($input_data['cats']) && !isset($input_data['mans']) && !isset($input_data['attr']))
                    || (empty($input_data['products']) && empty($input_data['cats']) && empty($input_data['mans']) && empty($input_data['attr']))) {
                    $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                    $errors[] = cw_get_langvar_by_name('msg_dod_bonus_incorrect', $additional_lang_data);
                    continue;
                }

                if (isset($input_data['products']) && !empty($input_data['products'])) {

                    $products_data = array();

                    foreach ($input_data['products'] as $product_data) {
                        $product_data['id'] = trim($product_data['id']);
                        $products_data[$product_data['id']] = $product_data['quantity'];
                    }

                    $pids = array_keys($products_data);
                    $pids = cw_query_column("SELECT product_id as id FROM $tables[products] WHERE product_id IN ('" . implode("','", $pids) . "')");
                 }


                if (isset($input_data['cats']) && !empty($input_data['cats'])) {

                    $cats_data = array();

                    foreach ($input_data['cats'] as $cat_data) {
                        $cat_data['id'] = trim($cat_data['id']);
                        $cats_data[$cat_data['id']] = $cat_data['quantity'];
                    }

                    $cids = array_keys($cats_data);
                    $cids = cw_query_column("SELECT category_id as id FROM $tables[categories] WHERE category_id IN ('" . implode("','", $cids) . "')");

                }

                if (isset($input_data['mans']) && !empty($input_data['mans'])) {
                    $mids = array_values($input_data['mans']);
                }   

                if (isset($input_data['attr'])) 

                // Prepare attributes data
                if (isset($input_data['attr']) && !empty($input_data['attr'])) {

                    $attr_data = array();

                    foreach ($input_data['attr'] as $a_data) {
                        $attr_data[trim($a_data['attribute_id'])] = array(
                            'quantity'=> $a_data['quantity'],
                            'value' => current($a_data['value']),
                            'operation' => $a_data['operation']
                        );
                    }

                    $attrids = array_keys($attr_data);
                    $attrids = cw_query_column("SELECT attribute_id as id FROM $tables[attributes] WHERE attribute_id IN ('" . implode("','", $attrids) . "')");
                }


                if (empty($pids) && empty($cids) && empty($mids) && empty($attrids)) {
                    $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                    $errors[] = cw_get_langvar_by_name('msg_dod_bonus_incorrect', $additional_lang_data);
                    continue;
                }
            }
        }
 
        if ($bonus_type != DOD_DISCOUNT && $bonus_type != DOD_FREE_SHIP) {
            $input_data['discount'] = $input_data['disctype'] = null;
        } elseif ($bonus_type == DOD_FREE_SHIP) {
            $input_data['disctype'] = null;
        } else {
            $optional_fields = array();
        }

        if (in_array($bonus_type, array(DOD_FREE_PRODS, DOD_COUPON))) {
            $optional_fields[] = 'apply';
        }


        $data = array();

        foreach ($date_fields as $field) {
            if (isset($input_data[$field]) && !empty($input_data[$field])) {
                $input_data[$field] = cw_core_strtotime($input_data[$field]);
            }
        }

        $error = null;

        foreach ($available_fields as $field => $field_type) {
            if (isset($input_data[$field])) {

                $result = settype($input_data[$field], $field_type);
                if ($result === false) {
                    $error = 'msg_dod_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field);
                    break;
                }

                if (empty($input_data[$field]) && !($bonus_type == DOD_DISCOUNT && $unused_dod_discount_bonus)) {
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    } else {
                        $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                        $error = 'msg_dod_bonus_incorrect';
                        break;
                    }
                } else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                        $input_data[$field] = cw_strip_tags($input_data[$field]);

                    }
                    $data[$field] = & $input_data[$field];
                }
            } else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                } else {

                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    } else {
                        $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                        $error = 'msg_dod_bonus_incorrect';
                        break;
                    }

                }
            }
        }

        if (!empty($error)) {
            $errors[] = cw_get_langvar_by_name($error, $additional_lang_data);
            continue;
        }


        if (empty($data)) {
            continue;
        }

        if ($data['type'] == DOD_DISCOUNT)  
            $data['unused'] = $unused_dod_discount_bonus;

        $bonus_id = cw_array2insert($tables['dod_bonuses'], cw_addslashes($data));

        if ($bonus_type == DOD_FREE_SHIP) {
            foreach ($input_data['methods'] as $trash=>$shipping_id) {
                $data = array(
                    'generator_id' => $generator_id,
                    'bonus_id' => $bonus_id,
                    'object_id' => $shipping_id,
                    'object_type' => DOD_OBJ_TYPE_SHIPPING
                );
                cw_array2insert($tables['dod_bonus_details'], cw_addslashes($data));
            }
        }

        if ($bonus_type != DOD_COUPON) {

            if ($input_data['apply'] == DOD_APPLY_PRODS || $bonus_type == DOD_FREE_PRODS || ($bonus_type == DOD_DISCOUNT)) {

                if (!empty($pids)) {

                    $data = array();

                    $data['generator_id'] = $generator_id;
                    $data['bonus_id'] = $bonus_id;

                    foreach ($pids as $pid) {
                        $data['object_id'] = $pid;
                        $data['object_type'] = DOD_OBJ_TYPE_PRODS;
                        $data['quantity'] = $products_data[$pid];
                        if (empty($data['quantity'])) {
                            $data['quantity'] = 1;
                        }
                        cw_array2insert($tables['dod_bonus_details'], cw_addslashes($data));
                    }
                }

                if (!empty($cids)) {

                    $data = array();

                    $data['generator_id'] = $generator_id;
                    $data['bonus_id'] = $bonus_id;

                    foreach ($cids as $cid) {
                        $data['object_id'] = $cid;
                        $data['object_type'] = DOD_OBJ_TYPE_CATS;
                        $data['quantity'] = $cats_data[$cid];
                        if (empty($data['quantity'])) {
                            $data['quantity'] = 1;
                        }
                        cw_array2insert($tables['dod_bonus_details'], cw_addslashes($data));
                    }

                }
                if (!empty($mids)) {
                    $data = array();
                    $data['generator_id'] = $generator_id;
                    $data['bonus_id'] = $bonus_id;
                    $data['quantity'] = 1;
                    foreach ($mids as $mid) {
                        $data['object_id'] = $mid;
                        $data['object_type'] = DOD_OBJ_TYPE_MANS;
                        cw_array2insert($tables['dod_bonus_details'], cw_addslashes($data)); 
                    } 
                }
                // Save attributes to condition details
                if (!empty($attrids)) {

                    $data = array();

                    $data['generator_id'] = $generator_id;
                    $data['bonus_id'] = $bonus_id;
                    $data['quantity'] = 1;

                    foreach ($attrids as $aid) {
                        $data['object_id'] = $aid;
                        $data['object_type'] = DOD_OBJ_TYPE_ATTR;
                        $data['param1'] = $attr_data[$aid]['value'];
                        $data['param2'] = $attr_data[$aid]['operation'];
                        cw_array2insert($tables['dod_bonus_details'], cw_addslashes($data));
                    }
                }
            }
        }

        unset($bonuses[$bonus_type]);

    }

    if (!empty($bonuses)) {
        $GLOBALS['_dod_bonus'] = & $bonuses;
        cw_session_register('_dod_bonus');
    }
    if (!empty($errors)) {
        $error = implode("<br />\n", $errors);
        return array(false, $error);
    }

    return array(true, null);

}


function dod_get_generator_bonuses($generator_id = null) {
    global $tables;

    $result = array();

    $generator_id = (int)$generator_id;

    if (empty($generator_id)) {
        return array();
    }

    $bonuses = cw_query("SELECT * FROM $tables[dod_bonuses] WHERE generator_id = '$generator_id'");
    $bonus_details = cw_query("SELECT * FROM $tables[dod_bonus_details] WHERE generator_id = '$generator_id'");

    $_bonus_details = array();

    if (!empty($bonus_details) && is_array($bonus_details)) {

        $products = array();
        $cats = array();
        $mans = array(); 
        $attr = array();

        foreach ($bonus_details as $details) {

            switch ($details['object_type']) {
                case DOD_OBJ_TYPE_PRODS:
                    $products[$details['object_id']] = null;
                    break;

                case DOD_OBJ_TYPE_CATS:
                    $cats[$details['object_id']] = null;
                    break;

                case DOD_OBJ_TYPE_MANS:
                    $mans[$details['object_id']] = null;
                    break;

                case DOD_OBJ_TYPE_ATTR:
                    $attr[$details['object_id']] = null;
                    break;

                default:
                break;
            }

        }

        if (!empty($products)) {
            $products = cw_query_hash("SELECT product_id, product FROM $tables[products] WHERE product_id IN ('" . implode("', '", array_keys($products)) . "')", 'product_id', false, true);
        }

        if (!empty($cats)) {
            $cats = cw_query_hash("SELECT category_id, category FROM $tables[categories] WHERE category_id IN ('" . implode("', '", array_keys($cats)) . "')", 'category_id', false, true);
        }

        if (!empty($mans)) {
            $mans = cw_query_hash("SELECT manufacturer_id, manufacturer FROM $tables[manufacturers] WHERE manufacturer_id IN ('" . implode("', '", array_keys($mans)) . "')", 'manufacturer_id', false, true);
        }


        foreach ($bonus_details as $details) {

            if (!isset($_bonus_details[$details['bonus_id']]['products'])) {
                $_bonus_details[$details['bonus_id']]['products'] = array();
            }

            if (!isset($_bonus_details[$details['bonus_id']]['cats'])) {
                $_bonus_details[$details['bonus_id']]['cats'] = array();
            }

            if (!isset($_bonus_details[$details['bonus_id']]['mans'])) {
                $_bonus_details[$details['bonus_id']]['mans'] = array();
            }

            if (!isset($_bonus_details[$details['bonus_id']]['attr'])) {
                $_bonus_details[$details['bonus_id']]['attr'] = array();
            }

            switch ($details['object_type']) {
                case DOD_OBJ_TYPE_PRODS:
                    $_bonus_details[$details['bonus_id']]['products'][] = array('id' => $details['object_id'], 'name' => $products[$details['object_id']], 'quantity' => $details['quantity']);
                    $products[$details['object_id']] = null;
                    break;

                case DOD_OBJ_TYPE_CATS:
                    $_bonus_details[$details['bonus_id']]['cats'][] = array('id' => $details['object_id'], 'name' => $cats[$details['object_id']], 'quantity' => $details['quantity']);
                    $cats[$details['object_id']] = null;
                    break;

                case DOD_OBJ_TYPE_MANS:
                    $_bonus_details[$details['bonus_id']]['mans'][] = array('id' => $details['object_id']); 
                    $mans[$details['object_id']] = null;
                    break;

                case DOD_OBJ_TYPE_ATTR:
                    $_bonus_details[$details['bonus_id']]['attr'][] = array('id' => $details['object_id'], 'quantity' => $details['quantity'], 'bd_id'=>$details['bd_id']);
                    $attr[$details['object_id']] = null;
                    break;

                case DOD_OBJ_TYPE_SHIPPING:
                    $_bonus_details[$details['bonus_id']]['methods'][$details['object_id']] = $details['object_id'];
                    break;

                default:
                break;
            }

        }

    }

    $bonus_details = $_bonus_details;
    unset($_bonus_details);


    if (empty($bonuses) || !is_array($bonuses)) {
        return array();
    }

    foreach ($bonuses as $bonus) {
        $result[$bonus['type']] = $bonus;
        if ($bonus['type'] == DOD_FREE_SHIP) {
            $result[$bonus['type']]['methods'] = $bonus_details[$bonus['bonus_id']]['methods'];
        }
        if ($bonus['type'] != DOD_COUPON && ($bonus['apply'] == DOD_APPLY_PRODS || $bonus['type'] == DOD_FREE_PRODS || ($bonus['type'] == DOD_DISCOUNT))) {
            $result[$bonus['type']]['products'] = $bonus_details[$bonus['bonus_id']]['products'];
            $result[$bonus['type']]['cats'] = $bonus_details[$bonus['bonus_id']]['cats'];
            $result[$bonus['type']]['mans'] = $bonus_details[$bonus['bonus_id']]['mans'];  
            $result[$bonus['type']]['attr'] = $bonus_details[$bonus['bonus_id']]['attr'];     
        }
    }

    return $result;
}


function dod_get_coupons() {
    global $tables, $addons;

    if (!isset($addons['discount_coupons']) || empty($addons['discount_coupons'])) {
        return array();
    }

    $coupons = cw_query("SELECT * FROM $tables[discount_coupons] WHERE status = 1");

    if (empty($coupons) || !is_array($coupons)) {
        return array();
    }

    return $coupons;
}



function dod_get_coupons_ajax() {
    global $smarty, $dod_type;

    $smarty->assign('dod_coupons', dod_get_coupons());
    $smarty->assign('dod_type', $dod_type);
    //$smarty->fetch('addons/deal_of_day/admin/coupons.tpl', null, null, true);
    cw_display('addons/deal_of_day/admin/coupons.tpl', $smarty);
    exit(0);
}

function dod_get_manufacturers() {
    global $tables, $addons;

    if (!isset($addons['manufacturers']) || empty($addons['manufacturers'])) {
        return array();
    }

    $manufacturers = cw_query("SELECT * FROM $tables[manufacturers] order by manufacturer asc");

    if (empty($manufacturers) || !is_array($manufacturers)) {
        return array();
    }

    return $manufacturers;
}

function dod_get_attr_ajax() {
    global $smarty, $tables;
    global $attribute_id, $bd_id;

    $value = $operation = $quantity = '';

    if (!empty($bd_id)) {
        // extract from DB certain condition details
        $bonus_details = cw_query_first("SELECT * FROM $tables[dod_bonus_details] WHERE bd_id='$bd_id'");
        $attribute_id = $bonus_details['object_id'];
        $quantity = $bonus_details['quantity'];
        $value = $bonus_details['param1'];
        $operation = $bonus_details['param2'];
    }
    if (empty($attribute_id)) return false;

    cw_load('attributes');
    $attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id'=>$attribute_id));
    $attribute['value'] = $value;
    $attribute['values'] = array($value);

    $smarty->assign(array(
        'attribute'=> $attribute,
        'index' => empty($bd_id)?time():'bd_id'.$bd_id,
        'quantity' => $quantity,
        'value' => $value,
        'operation' => $operation
    ));
    cw_ajax_add_block(array(
        'action' => 'append',
        'id' => 'dod_attributes',
        'template' => 'addons/deal_of_day/admin/attribute_row.tpl',
    ));

    return true;
}
