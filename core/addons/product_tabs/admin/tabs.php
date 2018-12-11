<?php

do {

	if (!isset($addons['product_tabs'])) {
        break;
    }

    if (AREA_TYPE != 'A') {
    	break;
    }

    global $mode, $product_id, $tab_type, $action;

    if (!isset($mode)) {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : null;
        $mode = isset($_POST['mode']) ? $_POST['mode'] : $mode;
    }
    $mode = (string) $mode;

    if (!isset($product_id)) {
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : $product_id;
    }
    $product_id = (int) $product_id;

    if (!isset($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        $action = isset($_POST['action']) ? $_POST['action'] : $action;
    }
    $action = (string) $action;

    if (!isset($tab_type)) {
        $tab_type = isset($_GET['tab_type']) ? $_GET['tab_type'] : null;
        $tab_type = isset($_POST['tab_type']) ? $_POST['tab_type'] : $tab_type;
    }
    $tab_type = (string) $tab_type;

    if (empty($tab_type) && !empty($product_id)) {
        $tab_type = 'product';
    }

    if (empty($tab_type)) {
        $tab_type = 'global';
    }

	if ($tab_type == 'global') {
        $smarty->assign('main','product_tabs');
	}
    $available_tab_types = array(
        'global',
        'product'
    );

    if (!in_array($tab_type, $available_tab_types)) {
        break;
    }
    $smarty->assign('tab_type', $tab_type);

    $addon_tab_actions = array(
        'tabs_update' => 'tabs_update',
        'tabs_add' => 'tabs_add',
        'tabs_delete' => 'tabs_delete',
        'tabs_details' => 'tabs_details',
        'tabs_modify' => 'tabs_modify',
    );

    global $available_tab_fields, $optional_tab_fields, $skip_striptags_tab_fields;

    $available_tab_fields = array(
        'tab_id' => 'int',
        'number' => 'int',
        'parse' => 'bool',
        'active' => 'bool',
        'content' => 'string',
        'title' => 'string',
        'attributes' => 'array',
    );
    $optional_tab_fields = array('parse', 'active', 'number', 'attributes');
    $skip_striptags_tab_fields = array('content');

    require_once $app_main_dir . '/addons/product_tabs/func.php';

    if (
        empty($action)
        || !isset($addon_tab_actions[$action])
        || !function_exists($addon_tab_actions[$action])
    ) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            tabs_show($tab_type, $product_id);
        }
        break;
    }

    $smarty->assign('action', $action);
    cw_call($addon_tab_actions[$action], array($tab_type, $product_id));

} while (0);

function tabs_show($tab_type = 'product', $product_id = 0) {
    global $available_tab_fields, $optional_tab_fields, $tables, $smarty, $top_message;
    global $_pt_addon_tables;

    $product_tabs = array();

    $product_id = (int) $product_id;

    $product_id_condition = null;
    if ($tab_type == 'product') {
        if (empty($product_id)) {
            return;
        }
        $product_id_condition = ' WHERE product_id = \'' . $product_id . '\'';
    }

    $table = $_pt_addon_tables[$tab_type];

    $number_tabs = cw_query_first_cell('SELECT COUNT(tab_id) FROM ' . $tables[$table] . $product_id_condition);

    if (!empty($number_tabs)) {
        $product_tabs = cw_query('
            SELECT `' . implode('`, `', array_keys($available_tab_fields)) . '`
            FROM ' . $tables[$table] . $product_id_condition . '
            ORDER BY `number`' . $limit_tabs
        );
    }

    if ($tab_type == 'product') {
        $global_tabs = cw_query('
            SELECT `' . implode('`, `', array_keys($available_tab_fields)) . '`, 1=1 as global
            FROM ' . $tables[$_pt_addon_tables['global']] . '
            ORDER BY `number`
        ');

        if (!empty($global_tabs) && is_array($global_tabs)) {
            $product_tabs = array_merge($global_tabs, $product_tabs);
            usort($product_tabs, 'cw_pt_tabs_comparison');
        }
    }

    $_new_tab = &cw_session_register('_new_tab');

    if (!empty($_new_tab)) {
        $smarty->assign('_new_tab', $_new_tab);
        cw_session_unregister('_new_tab');
    }

    $smarty->assign('product_tabs', $product_tabs);
}

function tabs_add($tab_type = 'product', $product_id = 0) {
    global $available_tab_fields, $optional_tab_fields, $skip_striptags_tab_fields, $top_message;
    global $_pt_addon_tables;

    $product_id = (int) $product_id;

    if ($tab_type == 'product') {
        if (empty($product_id)) {
            tabs_redirect();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	tabs_redirect($product_id);
    }

    $table = $_pt_addon_tables[$tab_type];

    global $new_tab;

    if (!isset($new_tab)) {
    	$new_tab = null;
	    if (isset($_POST['new_tab'])) {
        	$new_tab = & $_POST['new_tab'];
    	}
    }

    if (empty($new_tab) || !is_array($new_tab)) {
        tabs_redirect($product_id);
    }

    $error = null;
    $data = array();

    if (isset($available_tab_fields['tab_id'])) {
        unset($available_tab_fields['tab_id']);
    }

    $additional_lang_data = array();

    foreach ($available_tab_fields as $field => $field_type) {
        if (!isset($new_tab[$field])) {
            if (in_array($field, $optional_tab_fields)) {
                continue;
            } else {
                $error = 'msg_pt_empty_fields';
                break;
            }
        }
        else {
            $result = settype($new_tab[$field], $field_type);
            if ($result === false) {
                $error = 'msg_pt_incorrect_field_type';
                $additional_lang_data = array('field_name' => $field);
                break;
            }

            if ($field == 'content') {
                if ($new_tab[$field] == '<p>&#160;</p>') {
                    $new_tab[$field] = null;
                }
            }

            if (empty($new_tab[$field])) {
                if (in_array($field, $optional_tab_fields)) {
                    continue;
                } else {
                    $error = 'msg_pt_empty_fields';
                    break;
                }
            }

            if ($field_type == 'string' && !in_array($field, $skip_striptags_tab_fields)) {
                $new_tab[$field] = cw_strip_tags($new_tab[$field]);
            }
            $data[$field] = & $new_tab[$field];
        }
    }

    $GLOBALS['_new_tab'] = & $new_tab;
    cw_session_register('_new_tab');

    $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');

    if (empty($error) && !empty($data)) {

        if (!empty($product_id) && $tab_type == 'product') {
            $data['product_id'] = $product_id;
        }
        cw_array2insert($table, cw_addslashes($data));

        $top_message = array('content' => cw_get_langvar_by_name('msg_pt_updated_succes'), 'type' => 'I');
        cw_session_unregister('_new_tab');
    }

    tabs_redirect($product_id);
}

function tabs_update($tab_type = 'product', $product_id = 0) {
    global $tables, $top_message, $available_tab_fields, $optional_tab_fields, $skip_striptags_tab_fields;
    global $_pt_addon_tables;

    $product_id = (int) $product_id;

	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	tabs_redirect($product_id);
    }

    $product_id_condition = null;
    if ($tab_type == 'product') {
        if (empty($product_id)) {
            tabs_redirect();
        }
        $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';
    }

    global $tab_ids, $pt_tabs;

    if (!isset($tab_ids)) {
    	$tab_ids = isset($_POST['tab_ids']) ? $_POST['tab_ids'] : array();
    }

    if (!isset($pt_tabs)) {
        $pt_tabs = isset($_POST['tabs']) ? $_POST['tabs'] : array();
    }

    $table = $_pt_addon_tables[$tab_type];

    if (empty($pt_tabs) || !is_array($pt_tabs) || empty($tab_ids)) {
        tabs_redirect($product_id);
    }

    $tab_ids = array_unique(array_map('tabs_process_ids', array_keys($tab_ids)));
    $tab_ids_query = implode('\', \'', $tab_ids);

    $tab_ids = cw_query_column('
        SELECT `tab_id`
        FROM ' . $tables[$table] . '
        WHERE ' . $product_id_condition . 'tab_id IN (\'' . $tab_ids_query . '\')
    ');

    if (empty($tab_ids)) {
        tabs_redirect($product_id);
    }

    if (isset($available_tab_fields['tab_id'])) {
        unset($available_tab_fields['tab_id']);
    }

    $error = null;

    foreach ($tab_ids as $tab_id) {
        $data = array();
        $additional_lang_data = array();

        if (!isset($pt_tabs[$tab_id])) {
            continue;
        }

        foreach ($available_tab_fields as $field => $field_type) {
            if (isset($pt_tabs[$tab_id][$field])) {
                $result = settype($pt_tabs[$tab_id][$field], $field_type);

                if ($result === false) {
                    $error = 'msg_pt_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field . ' tab ID: ' . $tab_id);
                    break(2);
                }

                if (empty($pt_tabs[$tab_id][$field])) {
                    if (in_array($field, $optional_tab_fields)) {
                        $data[$field] = null;
                    }
                }
                else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_tab_fields)) {
                        $pt_tabs[$tab_id][$field] = cw_strip_tags($pt_tabs[$tab_id][$field]);
                    }
                    $data[$field] = & $pt_tabs[$tab_id][$field];
                }
            }
            else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                }
            }
        }

        if (!empty($data)) {
            cw_array2update($table, cw_addslashes($data), $product_id_condition . 'tab_id = \'' . $tab_id . '\'');
        }
    }
    $top_message = array('content' => cw_get_langvar_by_name('msg_pt_updated_succes'), 'type' => 'I');

    if (!empty($error)) {
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
    }

    tabs_redirect($product_id);
}

function tabs_delete($tab_type = 'product', $product_id = 0) {
    global $tables, $top_message;
    global $_pt_addon_tables;

	$product_id = (int) $product_id;

	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	tabs_redirect($product_id);
    }

	global $tab_ids;

    if (!isset($tab_ids)) {
    	$tab_ids = isset($_POST['tab_ids']) ? $_POST['tab_ids'] : array();
    }

    $product_id_condition = null;
    if ($tab_type == 'product') {
        if (empty($product_id)) {
            tabs_redirect();
        }
        $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';
    }

    $table = $_pt_addon_tables[$tab_type];

    if (empty($tab_ids) || !is_array($tab_ids)) {
        tabs_redirect($product_id);
    }

    $tab_ids = array_unique(array_map('tabs_process_ids', array_keys($tab_ids)));
    $tab_ids_query = implode('\', \'', $tab_ids);

    db_query('DELETE FROM ' . $tables[$table] . ' WHERE ' . $product_id_condition . 'tab_id IN (\'' . $tab_ids_query . '\')');
    $top_message['content'] = cw_get_langvar_by_name('msg_pt_deleted');

    tabs_redirect($product_id);
}

function tabs_details($tab_type = 'product', $product_id = 0) {
    global $tables, $top_message, $smarty, $available_tab_fields;
    global $_pt_addon_tables;

    $product_id = (int) $product_id;

	if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        tabs_redirect($product_id);
    }

    global $tab_id;

    if (!isset($tab_id)) {
    	$tab_id = isset($_GET['tab_id']) ? (int) $_GET['tab_id'] : 0;
    }
    $tab_id = (int)$tab_id;

	if (empty($tab_id)) {
        tabs_redirect($product_id);
    }

    $product_id_condition = null;
    if ($tab_type == 'product') {
        if (empty($product_id)) {
            tabs_redirect();
        }
        $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';
    }

    $table = $_pt_addon_tables[$tab_type];

    $tab_data = cw_query_first('SELECT `' . implode('`, `', array_keys($available_tab_fields)) . '` FROM ' . $tables[$table] . ' WHERE ' . $product_id_condition . 'tab_id = \'' . $tab_id . '\'');

    if (empty($tab_data) || !is_array($tab_data))
        tabs_redirect($product_id);

    $tab_data['attributes'] = unserialize($tab_data['attributes']);
    $smarty->assign('tab_data', $tab_data);
}

function tabs_modify($tab_type = 'product', $product_id = 0) {
    global $tables, $top_message, $smarty, $available_tab_fields, $optional_tab_fields, $skip_striptags_tab_fields;
    global $_pt_addon_tables;

    $product_id = (int) $product_id;

	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	tabs_redirect($product_id);
    }

    global $tab_id;

    if (!isset($tab_id)) {
    	$tab_id = isset($_POST['tab_id']) ? (int) $_POST['tab_id'] : 0;
    }
    $tab_id = (int)$tab_id;

    $product_id_condition = null;
    if ($tab_type == 'product') {
        if (empty($product_id)) {
            tabs_redirect();
        }
        $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';
    }

    global $tab_data;

    if (!isset($tab_data)) {
	    $tab_data = null;
	    if (isset($_POST['tab_data'])) {
	        $tab_data = & $_POST['tab_data'];
	    }
    }

    $table = $_pt_addon_tables[$tab_type];

    if (empty($tab_id) || empty($tab_data) || !is_array($tab_data)) {
        tabs_redirect($product_id);
    }

    $tab_id = cw_query_first_cell('SELECT tab_id FROM ' . $tables[$table] . ' WHERE tab_id = \'' . $tab_id . '\'');

    if (empty($tab_id)) {
        tabs_redirect($product_id);
    }

    $error = null;
    $data = array();

    if (isset($available_tab_fields['tab_id'])) {
        unset($available_tab_fields['tab_id']);
    }

    $additional_lang_data = array();

    foreach ($available_tab_fields as $field => $field_type) {
        if (isset($tab_data[$field])) {
            $result = settype($tab_data[$field], $field_type);
            if ($result === false) {
                $error = 'msg_pt_incorrect_field_type';
                $additional_lang_data = array('field_name' => $field);
                break;
            }

            if (empty($tab_data[$field])) {
                if (in_array($field, $optional_tab_fields)) {
                    $data[$field] = null;
                }
            }
            else {
                if ($field_type == 'string' && !in_array($field, $skip_striptags_tab_fields)) {
                    $tab_data[$field] = cw_strip_tags($tab_data[$field]);
                }
                $data[$field] = & $tab_data[$field];
            }
        }
        else {
            if ($field_type == 'bool') {
                $data[$field] = 0;
            }
        }
    }

    if (!empty($error)) {
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
        tabs_redirect($product_id, $tab_id);
    }

    if (empty($data)) {
        $error = 'msg_pt_nothing_to_update';
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
        tabs_redirect($product_id, $tab_id);
    }

    $data['attributes'] = serialize($data['attributes']);
    cw_array2update($table, $data, $product_id_condition . 'tab_id = \'' . $tab_id . '\'');
    $top_message = array('content' => cw_get_langvar_by_name('msg_pt_updated_succes'), 'type' => 'I');

    tabs_redirect($product_id);
}

function tabs_process_ids($id) {
    return (int) $id;
}

function tabs_redirect($product_id = 0, $tab_id = 0) {
    global $app_catalogs, $target, $mode;

    $productid_url_param = null;

    if (!empty($product_id)) {
        $product_id = (int) $product_id;
        $productid_url_param = '&product_id=' . $product_id;
    }

    $tab_url_param = null;

    if (!empty($tab_id)) {
        $tab_id = (int) $tab_id;
        $tab_url_param = '&tab_id=' . $tab_id;
    }

    cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode&js_tab=product_tabs$productid_url_param$tab_url_param");
}
