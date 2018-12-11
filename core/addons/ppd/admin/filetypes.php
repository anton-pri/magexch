<?php

if (!defined('APP_START')) { die('The software application has not been initialized.'); }

do {
	
    if (!isset($addons['ppd'])) {
        break;
    }
    
	if (AREA_TYPE != 'A') {
    	break;
    }

    require_once $app_main_dir . '/addons/ppd/func.php';


    $addon_actions = array(
        'ppd_filetype_update'   => 'ppd_filetype_update',
        'ppd_filetype_delete'   => 'ppd_filetype_delete',
        'ppd_filetype_add'      => 'ppd_filetype_add'
    );

    
    global $action;
    
    if (!isset($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        $action = isset($_POST['action']) ? $_POST['action'] : $action;
    }
    $action = (string) $action;


    global $available_fields, $optional_fields, $skip_striptags_fields;

    $available_fields = array(
        'type_id' => 'int',
        'type' => 'string',
        'extension' => 'string',
        'fileicon' => 'string'
    );
    $optional_fields = array('fileicon');
    $skip_striptags_fields = array();

    $location[] = array(cw_get_langvar_by_name('lbl_ppd_manage_filetypes'), '');
    $smarty->assign('main', 'filetypes');
    $smarty->assign('current_main_dir', 'addons');
    $smarty->assign('current_section_dir', 'ppd/admin');


    if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            ppd_filetype_show();
        }
        break;
    }

    $smarty->assign('action', $action);

    call_user_func($addon_actions[$action]);
    
} while (0);



function ppd_filetype_show() {
    global $available_fields, $smarty, $tables, $config, $target;
    global $app_catalogs;


    global $page;
    
    if (!isset($page)) {
    	$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    }
    $page = (int)$page;

    
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        ppd_filetype_redirect();
    }

    
    $_new_types = &cw_session_register('_new_types');

    if (!empty($_new_types)) {
        $smarty->assign('_new_types', $_new_types);
        cw_session_unregister('_new_types');
    }
    

    $limit_types = null;
    $number_types = cw_query_first_cell('SELECT COUNT(type_id) FROM ' . $tables['ppd_types']);

    $navigation = cw_core_get_navigation($target, $number_types, $page);
    $navigation['script'] = "$app_catalogs[admin]/index.php?target=$target";

    $smarty->assign('navigation', $navigation);

    if (isset($navigation['first_page']) && isset($navigation['objects_per_page'])) {
        $limit_types = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    }

    $types = array();
    if (!empty($number_types)) {
        $types = cw_query('SELECT `' . implode('`, `', array_keys($available_fields)) . '` FROM ' . $tables['ppd_types'] . $limit_types);
    }

    if (empty($types) || !is_array($types)) {
        return;
    }

    foreach ($types as $key => $file) {
        $types[$key]['fileicon_exists'] = true;
        $types[$key]['fileicon_url'] = ppd_get_url_fileicon($types[$key]['fileicon']);
        if (empty($types[$key]['fileicon_url'])) {
            $types[$key]['fileicon_exists'] = false;
        }
    }

    $smarty->assign('ppd_types', $types);
}



function ppd_filetype_update() {
    global $tables, $top_message, $smarty, $available_fields;
    global $optional_fields, $skip_striptags_fields;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_filetype_redirect();
    }

    global $type_ids, $ppd_types;
    
    if (!isset($type_ids)) {
    	$type_ids = isset($_POST['type_ids']) ? $_POST['type_ids'] : array();
    }
    
    if (!isset($ppd_types)) {
    	$ppd_types = isset($_POST['ppd_types']) ? $_POST['ppd_types'] : array();
    }

    if (empty($type_ids) || empty($ppd_types) || !is_array($ppd_types)) {
        ppd_filetype_redirect();
    }

    $type_ids = array_unique(array_map('ppd_filetype_process_ids', array_keys($type_ids)));
    $type_ids_query = implode('\', \'', $type_ids);

    $type_ids = cw_query_column('SELECT `type_id` FROM ' . $tables['ppd_types'] . ' WHERE type_id IN (\'' . $type_ids_query . '\')');


    if (empty($type_ids) || !is_array($type_ids)) {
        ppd_filetype_redirect();
    }


    if (isset($available_fields['type_id'])) {
        unset($available_fields['type_id']);
    }

    $error = null;

    foreach ($type_ids as $type_id) {

        $data = array();
        $additional_lang_data = array();

        if (!isset($ppd_types[$type_id])) {
            continue;
        }

        foreach ($available_fields as $field => $field_type) {

            if (isset($ppd_types[$type_id][$field])) {

                $result = settype($ppd_types[$type_id][$field], $field_type);
                if ($result === false) {
                    $error = 'msg_ppd_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field . ' type ID: ' . $type_id);
                    break(2);
                }

                if (empty($ppd_types[$type_id][$field])) {
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    }
                } else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                        $ppd_types[$type_id][$field] = cw_strip_tags($ppd_types[$type_id][$field]);
                    }
                    $data[$field] = & $ppd_types[$type_id][$field];
                }
            } else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                }
            }
        }

        if (!empty($data)) {
            cw_array2update($tables['ppd_types'], $data, 'type_id = \'' . $type_id . '\'');
        }
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_ppd_filetypes_updated_succes'), 'type' => 'I');

    if (!empty($error)) {
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
    }

    ppd_filetype_redirect();
}



function ppd_filetype_delete() {
    global $tables, $top_message;

    global $type_ids;
    
    if (!isset($type_ids)) {
    	$type_ids = isset($_POST['type_ids']) ? $_POST['type_ids'] : array();
    }

    if (empty($type_ids) || !is_array($type_ids) || $_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_filetype_redirect();
    }

    $type_ids = array_unique(array_map('ppd_filetype_process_ids', array_keys($type_ids)));

    $type_ids_query = implode('\', \'', $type_ids);
    $table = $tables['ppd_types'];

    db_query('DELETE FROM ' . $table . ' WHERE type_id IN (\'' . $type_ids_query . '\')');
    $top_message['content'] = cw_get_langvar_by_name('msg_ppd_filetypes_deleted');

    ppd_filetype_redirect();
}



function ppd_filetype_add() {
    global $tables, $top_message, $smarty, $available_fields;
    global $optional_fields, $skip_striptags_fields;

    global $new_types;
    
    if (!isset($new_types)) {
	    $new_types = null;
	    if (isset($_POST['new_types'])) {
	        $new_types = & $_POST['new_types'];
	    }
    }

    if (empty($new_types) || !is_array($new_types) || $_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_filetype_redirect();
    }


    if (isset($available_fields['type_id'])) {
        unset($available_fields['type_id']);
    }


    $error_description = null;
    $number_types = 0;

    foreach ($new_types as $new_type_key => $new_type) {

        $data = array();
        $error = null;
        $additional_lang_data = array();
        $_additional_lang_data = array('number' => ++$number_types);

        if (isset($new_type['extension']) && !empty($new_type['extension'])) {
            $_file_exists = cw_query_first_cell('SELECT type_id FROM ' . $tables['ppd_types'] . ' WHERE extension = \'' . addslashes($new_type['extension']) . '\'');
            if (!empty($_file_exists)) {
                $error = 'msg_ppd_exts_exists';
                $error_description .= cw_get_langvar_by_name('lbl_ppd_filetype_skipped', $_additional_lang_data) . ' ';
                $error_description .= cw_get_langvar_by_name($error) . '<br />';
                continue;
            }
        }

        foreach ($available_fields as $field => $field_type) {

            if (!isset($new_type[$field])) {
                if (in_array($field, $optional_fields)) {
                    continue;
                } else {
                    $error = 'msg_ppd_empty_fields';
                    break;
                }
            } else {

                $result = settype($new_type[$field], $field_type);
                if ($result === false) {
                    $error = 'msg_ppd_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field);
                    break;
                }

                if (empty($new_type[$field])) {
                    if (in_array($field, $optional_fields)) {
                        continue;
                    } else {
                        $error = 'msg_ppd_empty_fields';
                        break;
                    }
                }

                if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                    $new_type[$field] = cw_strip_tags($new_type[$field]);
                }
                $data[$field] = & $new_type[$field];
            }
        }


        if (!empty($error)) {
            $error_description .= cw_get_langvar_by_name('lbl_ppd_filetype_skipped', $_additional_lang_data) . ' ';
            $error_description .= cw_get_langvar_by_name($error, $additional_lang_data) . '<br />';
        }

        if (empty($error) && !empty($data)) {
            cw_array2insert($tables['ppd_types'], $data);
            unset($new_types[$new_type_key]);
        }
    }


    if (!empty($new_types)) {
        $new_types = array_values($new_types);
        $GLOBALS['_new_types'] = & $new_types;
        cw_session_register('_new_types');
    }

    
    if (!empty($error_description)) {
        $top_message = array('content' => $error_description, 'type' => 'E');
        ppd_filetype_redirect(array('mode'=>'add'));
    } else {
        $top_message = array('content' => cw_get_langvar_by_name('msg_ppd_filetypes_updated_succes'), 'type' => 'I');
        cw_session_unregister('_new_types');
    }

    ppd_filetype_redirect();
}



function ppd_filetype_redirect($params=array()) {
    global $app_catalogs, $target;

    cw_header_location("$app_catalogs[admin]/index.php?target=$target".(!empty($params)?'&'.http_build_query($params):''));
}



function ppd_filetype_process_ids($id) {
    return (int) $id;
}

?>
