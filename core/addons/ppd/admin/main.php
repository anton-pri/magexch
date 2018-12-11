<?php

if (!defined('APP_START')) { die('The software application has not been initialized.'); }


do {

    if (!isset($addons['ppd'])) {
        break;
    }
    
	if (AREA_TYPE != 'A') {
    	break;
    }
    
    global $product_id, $mode, $action;
    
    if (!isset($product_id)) {
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : $product_id;
    }
    $product_id = (int) $product_id;
    
    
    if (!isset($mode)) {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : null;
        $mode = isset($_POST['mode']) ? $_POST['mode'] : $mode;
    }
    $mode = (string) $mode;
    
    
    if (!isset($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        $action = isset($_POST['action']) ? $_POST['action'] : $action;
    }
    $action = (string) $action;
    

    if (empty($product_id)) {
        break;
    }

    require_once $app_main_dir . '/addons/ppd/func.php';


    $addon_actions = array(
        'ppd_update'    => 'ppd_update',
        'ppd_details'   => 'ppd_details',
        'ppd_modify'    => 'ppd_modify',
        'ppd_delete'    => 'ppd_delete',
        'ppd_add'       => 'ppd_add',
        'ppd_clean'     => 'ppd_clean'
    );


    global $available_fields, $optional_fields, $skip_striptags_fields, $extra_fields;

    $available_fields = array(
        'file_id' => 'int',
        'title' => 'string',
        'filename' => 'string',
        'size' => 'int',
        'type_id' => 'int',
        'active' => 'bool',
        'number' => 'int',
        'perms_all' => 'int',
        'perms_owner' => 'int'
    );
    $optional_fields = array('active', 'number');
    $skip_striptags_fields = array();

    $extra_fields = array(
        'perms_all' => array('vis', 'acc'),
        'perms_owner' => array('vis', 'acc'),
    );


    if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            ppd_show($product_id);
        }
        break;
    }

    $smarty->assign('action', $action);
    call_user_func_array($addon_actions[$action], array($product_id));
    
} while (0);



function ppd_show($product_id) {
    global $available_fields, $smarty, $tables, $config, $target;
    global $app_catalogs, $mode;


	$product_id = (int) $product_id;

    if (empty($product_id)) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        ppd_redirect($product_id);
    }
    
    
    global $page;
    
    if (!isset($page)) {
    	$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    }
    
    $_new_files = &cw_session_register('_new_files');
    
    if (!empty($_new_files)) {
        $smarty->assign('_new_files', $_new_files);
        cw_session_unregister('_new_files');
    }
    

    $limit_files = null;
    $number_files = cw_query_first_cell('SELECT COUNT(file_id) FROM ' . $tables['ppd_files'] . ' WHERE product_id = \'' . $product_id . '\'');

    $navigation = cw_core_get_navigation($target, $number_files, $page);
    $navigation['script'] = "$app_catalogs[admin]/index.php?target=$target&mode=$mode&js_tab=ppd&product_id=$product_id";

    $smarty->assign('navigation', $navigation);

    if (isset($navigation['first_page']) && isset($navigation['objects_per_page'])) {
        $limit_files = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    }

    unset($available_fields['type_id']);

    $files = array();

    if (!empty($number_files)) {
        $files = cw_query('SELECT `' . implode('`, `', array_keys($available_fields)) . '`, `type`, `fileicon` FROM ' . $tables['ppd_files'] . ' AS files LEFT JOIN ' . $tables['ppd_types'] . ' AS types ON files.type_id = types.type_id WHERE product_id = \'' . $product_id . '\' ORDER BY number' . $limit_files);
    }

    if (empty($files) || !is_array($files)) {
        return;
    }

    foreach ($files as $key => $file) {

        $files[$key]['fileicon'] = ppd_get_url_fileicon($files[$key]['fileicon']);

        if (substr($files[$key]['filename'], 0, 7) != 'aws3://')
            $_real_path = ppd_check_path($files[$key]['filename']);
        else {
            $_real_path = $files[$key]['filename'];
        }    

        $files[$key]['is_deleted'] = empty($_real_path) ? true : false;

        $files[$key]['fileicon'] = empty($files[$key]['filename']) ? null : $files[$key]['fileicon'];

        $files[$key]['perms_owner'] = ppd_permissions($files[$key]['perms_owner']);
        $files[$key]['perms_all'] = ppd_permissions($files[$key]['perms_all']);
        $files[$key]['size'] = ppd_convertfrom_bytes($files[$key]['size']);
        
        $files[$key]['month_stats'] = ppd_get_stats($files[$key]['file_id'], $product_id, mktime(0, 0, 0, date('m'), 1, date('Y')), mktime(23, 59, 59, date('m')+1, 0, date('Y')));
        $files[$key]['year_stats'] = ppd_get_stats($files[$key]['file_id'], $product_id, mktime(0, 0, 0, 1, 1, date('Y')), mktime(23, 59, 59, 12, 31, date('Y')));
    }

    $smarty->assign('ppd_files', $files);
}



function ppd_details($product_id) {
    global $tables, $top_message, $smarty, $available_fields;

    $product_id = (int) $product_id;
    
    global $file_id;
    
    if (!isset($file_id)) {
    	$file_id = isset($_GET['file_id']) ? (int) $_GET['file_id'] : 0;
    }
    $file_id = (int)$file_id;
    

    if (empty($file_id) || $_SERVER['REQUEST_METHOD'] != 'GET') {
        ppd_redirect($product_id);
    }

    $table = $tab_type_tables[$tab_type];

    unset($available_fields['type_id']);

    $file_data = cw_query_first('SELECT `' . implode('`, `', array_keys($available_fields)) . '`, `type`, `fileicon` FROM ' . $tables['ppd_files'] . ' AS files LEFT JOIN ' . $tables['ppd_types'] . ' AS types ON files.type_id = types.type_id WHERE product_id = \'' . $product_id . '\' AND file_id = \'' . $file_id . '\'');

    if (empty($file_data) || !is_array($file_data)) {
        ppd_redirect($product_id);
    }


    $file_data['fileicon'] = ppd_get_url_fileicon($file_data['fileicon']);
    $file_data['filename'] = ppd_get_pathto_file($file_data['filename']);

    $file_data['fileicon'] = empty($file_data['filename']) ? null : $file_data['fileicon'];


    if (!empty($file_data['size'])) {
        $file_data['size'] = ppd_convertfrom_bytes($file_data['size']);
        $additional_lang_data = array('size' => $file_data['size']);
        $file_data['size'] = cw_get_langvar_by_name('lbl_ppd_detailed_filesize', $additional_lang_data);
    }

    $file_data['perms_owner'] = ppd_permissions($file_data['perms_owner']);
    $file_data['perms_all'] = ppd_permissions($file_data['perms_all']);

    $smarty->assign('file_data', $file_data);

    $smarty->assign('types', ppd_get_filetypes());
}



function ppd_update($product_id) {
    global $tables, $top_message, $smarty, $available_fields;
    global $extra_fields, $optional_fields, $skip_striptags_fields;

    $product_id = (int) $product_id;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_redirect($product_id);
    }

    
    global $file_ids, $ppd_files;
    
    if (!isset($file_ids)) {
    	$file_ids = isset($_POST['file_ids']) ? $_POST['file_ids'] : array();
    }
    
    if (!isset($ppd_files)) {
    	$ppd_files = isset($_POST['ppd_files']) ? $_POST['ppd_files'] : array();
    }

    if (empty($file_ids) || empty($ppd_files) || !is_array($ppd_files)) {
        ppd_redirect($product_id);
    }

    $file_ids = array_unique(array_map('ppd_process_ids', array_keys($file_ids)));
    $file_ids_query = implode('\', \'', $file_ids);


    $file_ids = cw_query_column('SELECT `file_id` FROM ' . $tables['ppd_files'] . ' WHERE product_id = \'' . $product_id . '\' AND file_id IN (\'' . $file_ids_query . '\')');


    if (empty($file_ids) || !is_array($file_ids)) {
        ppd_redirect($product_id);
    }


    if (isset($available_fields['file_id'])) {
        unset($available_fields['file_id']);
    }

    $error = null;

    foreach ($file_ids as $file_id) {

        $data = array();
        $additional_lang_data = array();

        if (!isset($ppd_files[$file_id])) {
            continue;
        }

        foreach ($available_fields as $field => $field_type) {

            if (isset($ppd_files[$file_id][$field])) {

                if (isset($extra_fields[$field]) && is_array($extra_fields[$field])) {
                    $_total_value = $_value = null;
                    foreach ($extra_fields[$field] as $extra_field) {
                        $_value = isset($ppd_files[$file_id][$field][$extra_field]) ? $ppd_files[$file_id][$field][$extra_field] : null;
                        @settype($_value, $field_type);
                        $_total_value += $_value;
                    }
                    $ppd_files[$file_id][$field] = $_total_value;
                }
                $result = settype($ppd_files[$file_id][$field], $field_type);
                if ($result === false) {
                    $error = 'msg_ppd_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field . ' file ID: ' . $file_id);
                    break(2);
                }

                if (empty($ppd_files[$file_id][$field])) {
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    }
                } else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                        $ppd_files[$file_id][$field] = cw_strip_tags($ppd_files[$file_id][$field]);
                    }
                    $data[$field] = & $ppd_files[$file_id][$field];
                }
            } else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                }
                if (isset($extra_fields[$field])) {
                    $data[$field] = 0;
                }
            }
        }

        $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';
        if (!empty($data)) {
            cw_array2update($tables['ppd_files'], $data, $product_id_condition . 'file_id = \'' . $file_id . '\'');
        }
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_ppd_updated_succes'), 'type' => 'I');

    if (!empty($error)) {
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
    }

    ppd_redirect($product_id);
}



function ppd_modify($product_id) {
    global $tables, $top_message, $smarty, $available_fields;
    global $extra_fields, $optional_fields, $skip_striptags_fields;

    $product_id = (int) $product_id;
    
    global $file_id;
    
    if (!isset($file_id)) {
    	$file_id = isset($_POST['file_id']) ? (int) $_POST['file_id'] : 0;
    }
    $file_id = (int)$file_id;

    
    global $file_data;
    
    if (!isset($file_data)) {
    	$file_data = null;
    	if (isset($_POST['file_data'])) {
	        $file_data = & $_POST['file_data'];
	    }
    }

    if (empty($file_id) || empty($file_data) || !is_array($file_data) || $_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_redirect($product_id);
    }


    $table = $tables['ppd_files'];

    $file_id = cw_query_first_cell('SELECT file_id FROM ' . $table . ' WHERE file_id = \'' . $file_id . '\'');

    if (empty($file_id)) {
        ppd_redirect($product_id);
    }


    $error = null;
    $data = array();

    if (isset($available_fields['file_id'])) {
        unset($available_fields['file_id']);
    }


    $additional_lang_data = array();

    foreach ($available_fields as $field => $field_type) {

        if (isset($file_data[$field])) {

            if (isset($extra_fields[$field]) && is_array($extra_fields[$field])) {
                $_total_value = $_value = null;
                foreach ($extra_fields[$field] as $extra_field) {
                    $_value = isset($file_data[$field][$extra_field]) ? $file_data[$field][$extra_field] : null;
                    @settype($_value, $field_type);
                    $_total_value += $_value;
                }
                $file_data[$field] = $_total_value;
            }

            $result = settype($file_data[$field], $field_type);
            if ($result === false) {
                $error = 'msg_ppd_incorrect_field_type';
                $additional_lang_data = array('field_name' => $field);
                break;
            }

            if (empty($file_data[$field])) {
                if (in_array($field, $optional_fields)) {
                    $data[$field] = null;
                }
            } else {
                if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                    $file_data[$field] = cw_strip_tags($file_data[$field]);
                }
                $data[$field] = & $file_data[$field];
            }
        } else {
            if ($field_type == 'bool') {
                $data[$field] = 0;
            }
            if (isset($extra_fields[$field])) {
                $data[$field] = 0;
            }
        }
    }

    if (!empty($error)) {
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
        ppd_redirect($product_id, $file_id);
    }

    if (empty($data)) {
        $error = 'msg_ppd_nothing_to_update';
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
        ppd_redirect($product_id, $file_id);
    }


    if (isset($data['type_id']) && !empty($data['type_id'])) {
        $_type_id = cw_query_first_cell('SELECT type_id FROM ' . $tables['ppd_types'] . ' WHERE type_id = \'' . $data['type_id'] . '\'');
        if ($_type_id != $data['type_id']) {
            $error = 'msg_ppd_incorrect_filetype';
            $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
            ppd_redirect($product_id, $file_id);
        }
    }

    if (isset($data['type_id']) && empty($data['type_id'])) {
        unset($data['type_id']);
    }


    $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';

    cw_array2update($table, $data, $product_id_condition . 'file_id = \'' . $file_id . '\'');
    $top_message = array('content' => cw_get_langvar_by_name('msg_ppd_updated_succes'), 'type' => 'I');

    ppd_redirect($product_id);
}



function ppd_delete($product_id) {
    global $tables, $top_message;

    $product_id = (int) $product_id;
    
    global $file_ids;
    
    if (!isset($file_ids)) {
    	$file_ids = isset($_POST['file_ids']) ? $_POST['file_ids'] : array();
    }

    if (empty($file_ids) || !is_array($file_ids) || $_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_redirect($product_id);
    }

    $file_ids = array_unique(array_map('ppd_process_ids', array_keys($file_ids)));

    $ppd_ids_query = implode('\', \'', $file_ids);
    $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';

    db_query('DELETE FROM ' . $tables['ppd_files'] . ' WHERE ' . $product_id_condition . 'file_id IN (\'' . $ppd_ids_query . '\')');
    db_query('DELETE FROM ' . $tables['ppd_downloads'] . ' WHERE ' . $product_id_condition . 'file_id IN (\'' . $ppd_ids_query . '\')');
    db_query('DELETE FROM ' . $tables['ppd_stats'] . ' WHERE ' . $product_id_condition . 'file_id IN (\'' . $ppd_ids_query . '\')');
    $top_message['content'] = cw_get_langvar_by_name('msg_ppd_deleted');

    ppd_redirect($product_id);
}



function ppd_add($product_id) {
    global $tables, $top_message, $smarty, $available_fields;
    global $extra_fields, $optional_fields, $skip_striptags_fields;

    $product_id = (int) $product_id;

    global $new_files;
    
    if (!isset($new_files)) {
	    $new_files = null;
	    if (isset($_POST['new_files'])) {
	        $new_files = & $_POST['new_files'];
	    }
    }

    if (empty($new_files) || !is_array($new_files) || $_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_redirect($product_id);
    }

    if (isset($available_fields['file_id'])) {
        unset($available_fields['file_id']);
    }


    array_push($optional_fields, 'size');
    array_push($optional_fields, 'type_id');

    $error_description = null;


    $number_files = 0;

    foreach ($new_files as $new_file_key => $new_file) {

        $data = array();
        $error = null;
        $additional_lang_data = array();
        $_additional_lang_data = array('number' => ++$number_files);

        if (substr($new_file['path'], 0, 7) != 'aws3://')
            $_real_path = ppd_check_path($new_file['filename']);
        else {
            $_real_path = $new_file['path'];
            $new_file['filename'] = $_real_path;
        }

        $new_file['size'] = null;

        if (empty($_real_path)) {
            $new_file['filename'] = null;
        }

        if (!empty($new_file['filename'])) {

            $new_file['size'] = ppd_get_filesize($_real_path);
            if (empty($new_file['size'])) {
                $error = 'msg_ppd_file_is_empty';
                $error_description .= cw_get_langvar_by_name('lbl_ppd_skipped_element', $_additional_lang_data) . ' ';
                $error_description .= cw_get_langvar_by_name($error) . '<br />';
                continue;
            }

            $_data = array();
            $_mime_type_by_ext = array();

            $_file_mime_type = ppd_get_mime_type($_real_path);
            $_file_extension = ppd_get_file_extension($_real_path);

            if (!empty($_file_mime_type)) {

                if (!empty($_file_extension)) {
                    $_mime_type_by_ext = cw_query_first('SELECT type_id, type FROM ' . $tables['ppd_types'] . ' WHERE extension = \'' . addslashes($_file_extension) . '\'');
                }

                $new_file['type_id'] = cw_query_first_cell('SELECT type_id FROM ' . $tables['ppd_types'] . ' WHERE type = \'' . addslashes($_file_mime_type) . '\'');

                if (isset($_mime_type_by_ext['type_id']) && $_mime_type_by_ext['type_id'] != $new_file['type_id']) {
                    $new_file['type_id'] = $_mime_type_by_ext['type_id'];
                }


                if (empty($new_file['type_id'])) {
                    $_data['type'] = $_file_mime_type;
                    $_data['extension'] = $_file_extension;
                    $_data['fileicon'] = null;
                    if (!empty($_file_extension)) {
                        $_data['fileicon'] = 'icon_' . $_file_extension . '.gif';
                    }
                    $new_file['type_id'] = cw_array2insert($tables['ppd_types'], $_data);
                }
            }
        }

        foreach ($available_fields as $field => $field_type) {

            if (!isset($new_file[$field])) {
                if (in_array($field, $optional_fields)) {
                    continue;
                } elseif (isset($extra_fields[$field])) {
                    $data[$field] = 0;
                } else {
                    $error = 'msg_ppd_empty_fields';
                    break;
                }
            } else {

                if (isset($extra_fields[$field]) && is_array($extra_fields[$field])) {
                    $_total_value = $_value = null;
                    foreach ($extra_fields[$field] as $extra_field) {
                        $_value = isset($new_file[$field][$extra_field]) ? $new_file[$field][$extra_field] : null;
                        @settype($_value, $field_type);
                        $_total_value += $_value;
                    }
                    $new_file[$field] = $_total_value;
                }

                $result = settype($new_file[$field], $field_type);
                if ($result === false) {
                    $error = 'msg_ppd_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field);
                    break;
                }

                if (empty($new_file[$field])) {
                    if (in_array($field, $optional_fields)) {
                        continue;
                    } else {
                        $error = 'msg_ppd_empty_fields';
                        break;
                    }
                }

                if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                    $new_file[$field] = cw_strip_tags($new_file[$field]);
                }
                $data[$field] = & $new_file[$field];
            }
        }


        if (!empty($error)) {
            $error_description .= cw_get_langvar_by_name('lbl_ppd_skipped_element', $_additional_lang_data) . ' ';
            $error_description .= cw_get_langvar_by_name($error, $additional_lang_data) . '<br />';
        }

        if (empty($error) && !empty($data)) {

            $file_exists = cw_query_first_cell('SELECT file_id FROM ' . $tables['ppd_files'] . ' WHERE filename = \'' . addslashes($data['filename']) . '\' AND product_id = \'' . $product_id . '\'');

            if ($file_exists) {
                $error = 'msg_ppd_file_already_exists';
                $additional_lang_data = array('file' => $data['filename']);
                $error_description .= cw_get_langvar_by_name('lbl_ppd_skipped_element', $_additional_lang_data) . ' ';
                $error_description .= cw_get_langvar_by_name($error, $additional_lang_data) . '<br />';
                continue;
            }

            $data['product_id'] = $product_id;
            cw_array2insert($tables['ppd_files'], $data);
            unset($new_files[$new_file_key]);
        }
    }


    if (!empty($new_files)) {
    	$new_files = array_values($new_files);
        $GLOBALS['_new_files'] = & $new_files;
        cw_session_register('_new_files');
    }


    if (!empty($error_description)) {
        $top_message = array('content' => $error_description, 'type' => 'E');
    } else {
        $top_message = array('content' => cw_get_langvar_by_name('msg_ppd_updated_succes'), 'type' => 'I');
        cw_session_unregister('_new_files');
    }

    ppd_redirect($product_id);
}



function ppd_clean($product_id) {
    global $available_fields, $smarty, $tables, $top_message;

    $product_id = (int) $product_id;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        ppd_redirect($product_id);
    }

    $files = cw_query('SELECT `file_id`, `filename` FROM ' . $tables['ppd_files'] . ' WHERE `product_id` = \'' . $product_id . '\'');

    if (empty($files) || !is_array($files)) {
        ppd_redirect($product_id);
    }

    $product_id_condition = 'product_id = \'' . $product_id . '\' AND ';

    foreach ($files as $key => $file) {

        if (substr($file['filename'], 0, 7) != 'aws3://')
            $_real_path = ppd_check_path($file['filename']);
        else {
            $_real_path = $file['filename'];
        }

        if (empty($_real_path)) {
            $to_delete[] = $file['file_id'];
        }
    }

    if (!empty($to_delete) && is_array($to_delete)) {
        $files_ids_query = implode('\', \'', $to_delete);
        db_query('DELETE FROM ' . $tables['ppd_files'] . ' WHERE ' . $product_id_condition . 'file_id IN (\'' . $files_ids_query . '\')');
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_ppd_updated_succes'), 'type' => 'I');
    ppd_redirect($product_id);
}



function ppd_redirect($product_id = 0, $file_id = 0) {
    global $app_catalogs, $target, $mode;

    $productid_url_param = null;

    if (!empty($product_id)) {
        $product_id = (int) $product_id;
        $productid_url_param = '&product_id=' . $product_id;
    }

    $file_url_param = null;
    if (!empty($file_id)) {
        $file_id = (int) $file_id;
        $file_url_param = '&file_id=' . $file_id;
    }

    cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode&js_tab=ppd$productid_url_param$file_url_param");
}



function ppd_process_ids($id) {
    return (int) $id;
}

?>
