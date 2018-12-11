<?php

function cw_flexible_import_validate_import_file($params, $rules){
    
    $error = cw_get_return();
    global $action;
    if ($action != "import_file") return $error;  
    $index = substr_count($error, "\n") +($error!=''? 1 : 0);
    if (isset($params['import_file']['error']) && $params['import_file']['error'] !=0 && $params['import_type']=='pc') {
        if($params['import_file']['error']==4)
            $error .= "\n".++$index.'b . '.cw_get_langvar_by_name('err_field_select_file');
        else
            $error .= "\n".++$index.'. '.cw_get_langvar_by_name('lbl_upld_err').' '.$params['import_file']['error'];

    } elseif (empty($params['import_file'])) {
        $error .= "\n".++$index.'c . '.cw_get_langvar_by_name('err_field_select_file');
    }
    $error  = str_replace('<br/>', '',$error);


    return $error;
}

#params
# -$sort_field
# -$sort_direction
function cw_flexible_import_get_profiles($params){
    global $tables;
    extract($params);
    $sort_field = ($sort_field && $sort_field!="" ? " ORDER BY ".$sort_field." " : "");
    $sort_direction = ($sort_direction!=0 ? " DESC " : " ASC ");
    $limit .=  'LIMIT '.($items_per_page * $page - $items_per_page).', '.$items_per_page;
    $profiles = cw_query("SELECT * FROM $tables[flexible_import_profiles] $sort_field $sort_direction  $limit");

    if ($unserialize_fields) {
        $fields2unser = array('options', 'mapping_data', 'parsed_columns', 'map_process');
        foreach ($profiles as $p_k => $p_v) {
            foreach ($fields2unser as $field) {   
                if (!empty($p_v[$field]))    
                    $profiles[$p_k][$field] = unserialize($p_v[$field]); 
            }  
        }
    } 

    return $profiles;
}

#params
# - id
function cw_flexible_import_get_profile($params) {

    global $tables, $var_dirs;
    extract($params);

    $profile = cw_query_first("SELECT * FROM $tables[flexible_import_profiles] WHERE id = $id");

    $options = @unserialize($profile['options']); 
    if (empty($options)) { 
        $options = unserialize((base64_decode($profile['options'])));
        $options = json_decode(stripslashes(json_encode($options)), true);
    }
    unset($profile['options']);
    $result = array_merge($profile, $options);

    if (!empty($result['test_import_file']['name'])) 
        $result['test_import_file_name'] = $var_dirs['flex_import_test'].'/'.$id.'/'.$result['test_import_file']['name'];

    return $result;

}

function cw_flexible_import_save_profile($params, $profile_type){
    global $tables, $var_dirs;

    $id    = $params['id'];
    $name  = $params['name'];
    $descr = $params['description'];

    $import_src_type = $params['import_src_type'];
    $dbtable_src = $params['dbtable_src'];

    $active_reccuring = $params['active_reccuring'];
    $recurring_import_path = $params['recurring_import_path'];
    $recurring_import_days = $params['recurring_import_days'];
    $recurring_import_hours = $params['recurring_import_hours'];

    $test_import_file_tmp = $params['test_import_file']['tmp_name'];

    $ser_parsed_columns = '';
    if (is_array($params['parsed_columns'])) 
        $ser_parsed_columns = serialize($params['parsed_columns']);

    if (!empty($params['test_import_file'])) {
        unset($params['test_import_file']['tmp_name'], $params['test_import_file']['error']);
    }

    unset($params['id'], $params['name'], $params['description'], $params['import_src_type'], $params['dbtable_src'], $params['type'], $params['active_reccuring'], $params['recurring_import_path'], $params['recurring_import_days'], $params['recurring_import_hours'], $params['parsed_columns']);

    $profile_options = serialize($params);
    if ($id)
        $res = cw_array2update($tables['flexible_import_profiles'], array('name'=>$name, 'description' => $descr, 'import_src_type' => $import_src_type, 'dbtable_src' => $dbtable_src, 'type'=>$profile_type, 'options'=> $profile_options, 'active_reccuring'=>$active_reccuring, 'recurring_import_path'=>$recurring_import_path, 'recurring_import_days'=>$recurring_import_days, 'recurring_import_hours'=>$recurring_import_hours, 'parsed_columns' => $ser_parsed_columns ) , "id='".$id."'");
    else 
        $res = cw_array2insert($tables['flexible_import_profiles'], array('name'=>$name, 'description' => $descr, 'import_src_type' => $import_src_type, 'dbtable_src' => $dbtable_src, 'type'=>$profile_type, 'options'=> $profile_options, 'active_reccuring'=>$active_reccuring, 'recurring_import_path'=>$recurring_import_path, 'recurring_import_days'=>$recurring_import_days, 'recurring_import_hours'=>$recurring_import_hours, 'parsed_columns' => $ser_parsed_columns )); 
    
    if (!empty($params['test_import_file']) && $res && file_exists($test_import_file_tmp)) {
        if ($id) 
            $test_import_file_path = $var_dirs['flex_import_test'].'/'.$id;
        else 
            $test_import_file_path = $var_dirs['flex_import_test'].'/'.$res;

        if (!file_exists($var_dirs['flex_import_test'])) 
            mkdir($var_dirs['flex_import_test']);

        if (!file_exists($test_import_file_path))  
            mkdir($test_import_file_path);

        if ($test_import_file_path.'/'.$params['test_import_file']['name'] != $test_import_file_tmp) {
            @unlink($test_import_file_path.'/'.$params['test_import_file']['name']); 
            rename($test_import_file_tmp, $test_import_file_path.'/'.$params['test_import_file']['name']); 
        }
    }
 
    return $res; 
}

function cw_flexible_import_delete_profile($profile_ids){
    global $tables;

    $ids = (is_array($profile_ids)? implode("','", $profile_ids) : $profile_ids );
        db_query("DELETE FROM $tables[flexible_import_profiles] WHERE id IN ('$ids')");

}


#params

# name
# descr
# type - tab, comma, semicolon, custom, advanced
# -custom params
#       delimiter
#       lines_terminate
#       enclose_char
#       escape_char

# -advanced params
#       num_columns
#       chars_to_tirm
#       enclose_char
#       escape_char
#       column => delimiter, column_type

# num_lines_to_skip
# col_names_line_id
# col_names_line_id
# file

function cw_flexible_import_parse_file($params, $is_test=false) {
    global $tables;

    if (get_magic_quotes_gpc()) {
        array_walk_recursive($params, 'cw_flexible_import_strip_input');
    }

    extract($params);

    $enclosure_char = '"';
    $escape_char = "\\";
    $line = 1;

    if(!isset($lines_terminate) || $lines_terminate =='')
        $lines_terminate = "\r\n";

    if($type=='tab')
        $delimiter = "\t";
    elseif($type == 'comma')
        $delimiter = ",";
    elseif($type == 'semicolon')
        $delimiter = ";";
    elseif($type == 'custom') {

        if($custom['delimiter']) 
            $delimiter = stripcslashes($custom['delimiter']);

        if($custom['lines'])     
            $lines_terminate = stripcslashes($custom['lines']);

        if($custom['enclosure']) 
            $enclosure_char = stripcslashes($custom['enclosure']);

        if($custom['escape'])    
            $escape_char = stripcslashes($custom['escape']);

    } elseif($type == 'advanced') {

        if($adv['enclosure']) 
            $enclosure_char = stripcslashes($adv['enclosure']);

        if($adv['escape']) 
            $escape_char = stripcslashes($adv['escape']);

    }

    $parsed_file = array();

    if($file_name) {

        $parsed_file['source_file_name'] = $file_name;

    } elseif($import_file) {

        $file_name = $import_file['tmp_name'];
        $parsed_file['source_file_name'] = $import_file['name'];

    }

    $handle=fopen($file_name, 'r');

    if(!$handle)
        return array('err' => cw_get_langvar_by_name('err_unable_to_open_file', array('file'=> $parsed_file['source_file_name'])));

    if($num_lines_to_skip) {
        for($i=1; $i<=$num_lines_to_skip; $i++) {
            fgets($handle);
        }
    }

    $line +=  $num_lines_to_skip;

    if ($type=='custom' || $type=='tab' || $type=='comma' || $type=='semicolon') {

        $col_names_line_id = ($col_names_line_id && is_numeric($col_names_line_id) ? $col_names_line_id : 1);

        $lines_limit = PHP_INT_MAX;

        while (!feof($handle)) {
            if ($lines_limit <= 0) break;  
            $line_data = fgets($handle);

            $line_data = explode($lines_terminate, $line_data);

            foreach ($line_data as $data) {
                if (trim($data)=="") {
                    $line++;
                    continue;
                }

                $data = explode($delimiter, trim($data));
                $field_data = cw_flexible_import_parse_line($data, $type, $enclosure_char, $escape_char);
                if (empty($parsed_file['fields']['values']) && !empty($field_data) && $col_names_line_id==$line) {
                    $field_data = str_replace(array("\n\r","\n",'#','?','"','\'','`'), '', $field_data);
                    $parsed_file['fields']['values'] = $field_data;
                    if (!$is_test) {
                        $tmp_table = cw_call('cw_flexible_import_create_temp_table', array('fields'=> $field_data));
                        if (!$tmp_table) {
                            $parsed_file['err'] .= cw_get_langvar_by_name('err_creating_temp_table').'<br>';
                            break;
                        }
                    }

                } else {

                    foreach($parsed_file['fields']['values'] as $k => $v) {

                        $tmp_field[$v] = $field_data[$k];

                        if (!$parsed_file['fields']['max_length'][$k] || $parsed_file['fields']['max_length'][$k] < strlen($field_data[$k]))
                            $parsed_file['fields']['max_length'][$k] = strlen($field_data[$k]);

                    }

                    if ($use_category_column) {

                        $num_fields = count($field_data);
                        $single_field_data = cw_flexible_import_check_single_field($field_data, $num_fields, $single_field_data);

                        if ($single_field_data['key']) {
                             if (!$additional_category_field) {
                                 $additional_category_field = 'category_name_'.rand(100,999);
                                 if (!$is_test)
                                     db_query("ALTER TABLE `$tmp_table` ADD `$additional_category_field` TEXT NOT NULL ");

                                 $parsed_file['fields']['values'][] = $additional_category_field;
                             }

                             $tmp_field[$additional_category_field] = $single_field_data['value'];
                        }

                        if ($single_field_data['is_single_field'])
                            continue;
                    }

                    if ($is_test) {

                        $parsed_file['data'][] = $tmp_field;

                        if($line >= 30) 
                            break 2;

                    } else {
                        $ins_fields = array();
                        $ins_values = array();
                        foreach ($tmp_field as $fld_name=>$fld_value) {
                            if (trim($fld_name) == '') continue;
                            $ins_values[$fld_name] = $fld_value;
                            $ins_fields[] = $fld_name;  
                        }
                        cw_array2insert($tmp_table, $ins_values, false , $ins_fields);
                    }
                }

                $line++;
            }
            $lines_limit--;
        }

        if (!$is_test && !$parsed_file['err'])
            cw_array2insert($tables["flexible_import_files"], array('file_name'=> $parsed_file['source_file_name'], 'table_name'=>$tmp_table, 'profile_id'=>$id));
 
        $parsed_file['tmp_table'] = $tmp_table;
        return $parsed_file;

    } elseif ($type=='advanced') {

        $single_field_data = array();
        $additional_category_field = false;

        foreach ($adv['column'] as $k => $column) {
            if (!$column['delimiter'])
                $adv['column'][$k]['delimiter'] = '\t';

            $adv['column'][$k]['delimiter'] = stripcslashes( $adv['column'][$k]['delimiter']);
        }

        if (!$col_names_line_id && $col_names_line_id=="") {
            foreach($adv['column'] as $column)
                $parsed_file['fields']['values'][] = $column['field_name'];

        } else {

            if ($col_names_line_id>$line) {
                $n = $col_names_line_id-$line;

                for($i=0; $i<$n; $n++)
                    fgets($handle);

            }

            $data = fgets($handle);
            $parsed_file['fields']['values'] = cw_flexible_import_parse_line($data, $type,$enclosure_char, $escape_char, $adv['column']);
        }

        $num_fields = count($parsed_file['fields']['values']);

        if (!$is_test) {

            $tmp_table = cw_call('cw_flexible_import_create_temp_table', array('fields'=> $parsed_file['fields']['values']));

            if (!$tmp_table) {

                $parsed_file['err'] .= cw_get_langvar_by_name('err_creating_temp_table').'<br>';
                $parsed_file['tmp_table'] = $tmp_table;
                return $parsed_file;
            }
        }

        while (!feof($handle)) {

            $data = trim(fgets($handle));
            if(trim($data)==""){
                $line++;
                continue;
            }

            $field_data =cw_flexible_import_parse_line($data, $type,$enclosure_char, $escape_char, $adv['column']);
            if(!$field_data){
                $parsed_file['err'] .= cw_get_langvar_by_name('err_parsing_error_at').' '.$line.'<br>';
                break;
            }

            foreach($parsed_file['fields']['values'] as $k => $v) {
                if ($adv['column'][$k]['mandatory'] && $field_data[$k-1]=="")
                    continue 2;

                if ($adv['column'][$k]['column_type']=="text") {
                    if(!is_string($field_data[$k-1])) {
                        $parsed_file['err'] .= cw_get_langvar_by_name('err_parsing_error_at').' '.$line.' '.cw_get_langvar_by_name('err_wrong_column_type').' <br>';
                        break 2;
                    }
                } elseif ($adv['column'][$k]['column_type']=="numeric") {
                    if (!is_numeric($field_data[$k-1])) {
                        $parsed_file['err'] .= cw_get_langvar_by_name('err_parsing_error_at').' '.$line.' '.cw_get_langvar_by_name('err_wrong_column_type').' <br>';
                            break 2;
                    }
                }
                if($adv['chars_to_trim'])
                    $field_data[$k] = trim($field_data[$k], $adv['chars_to_trim']);

                $tmp_field[$v] = $field_data[$k];
                if (!$parsed_file['fields']['max_length'][$k] || $parsed_file['fields']['max_length'][$k] < strlen($field_data[$k]))
                    $parsed_file['fields']['max_length'][$k] = strlen($field_data[$k]);
            }

            if ($use_category_column) {
                $single_field_data = cw_flexible_import_check_single_field($field_data,$num_fields, $single_field_data);

                if ($single_field_data['key']) {
                    if (!$additional_category_field) {
                        $additional_category_field = 'category_name_'.rand(100,999);

                        if (!$is_test)
                            db_query("ALTER TABLE `$tmp_table` ADD `$additional_category_field` TEXT NOT NULL ");

                        $parsed_file['fields']['values'][] = $additional_category_field;
                    }

                    $tmp_field[$additional_category_field] = $single_field_data['value'];
                }

                if ($single_field_data['is_single_field'])
                    continue;
            }

            if ($is_test) {

                $parsed_file['data'][] = $tmp_field;

                if ($line >= 10) 
                    break;
            } else {
                cw_array2insert($tmp_table, $tmp_field, false , $parsed_file['fields']['values']);
            }

            if ($parsed_file['err']!="" && !$is_test) {
                db_query("DROP TABLE IF EXISTS `$tmp_table`");
                break;
            }
            $line++;
        }

        if(!$is_test && !$parsed_file['err'])
            cw_array2insert($tables["flexible_import_files"], array('file_name'=> $parsed_file['source_file_name'], 'table_name'=>$tmp_table));

        $parsed_file['tmp_table'] = $tmp_table;
        return $parsed_file;
    }
}

function cw_flexible_import_add_prefix($prefix, $table_name) {
     if (strpos($table_name, '.') !== false) {
         $name_parts = explode('.', $table_name);
         $_tab_name = $prefix.trim($name_parts[1],'`');
         return $name_parts[0].'.`'.$_tab_name.'`'; 
     } else {
         $_tab_name = $prefix.trim($table_name,'`'); 
         return '`'.$_tab_name.'`';
     }
}

function cw_fi_safe_add_q($table_name) {
     $table_name = str_replace('`','',$table_name);
     if (strpos($table_name, '.') !== false) 
         return str_replace('.', '.`',$table_name).'`';
     else
         return '`'.$table_name.'`';
} 

function cw_flexible_import_run_profile($profile_id, $server_filenames, $uploaded_import_file = '') {

    global $csvxc_field_types, $csvxc_allowed_sections;

    global $preview_mode;

    $profile = cw_call('cw_flexible_import_get_profile', array('params'=>array('id'=> intval($profile_id))));

    if (!empty($profile['mapping_data']))
        $mapping_data = unserialize($profile['mapping_data']);

    if (!empty($profile['map_process'])) {
        $map_process = unserialize($profile['map_process']); 
        foreach ($map_process as $tbl_name => $mp_data) {
            if (!empty($mp_data['post_sql']))
                $map_process[$tbl_name]['post_sql'] = stripslashes(base64_decode($mp_data['post_sql'])); 
            if (!empty($mp_data['clean_table_condition']))
                $map_process[$tbl_name]['clean_table_condition'] = stripslashes(base64_decode($mp_data['clean_table_condition']));
        } 
    }  

    foreach ($mapping_data as $tbl_name => $tbl_fields) {
        foreach ($tbl_fields as $fld_name => $fld_mapping) {
            if (!empty($fld_mapping['custom_sql']))
                $mapping_data[$tbl_name][$fld_name]['custom_sql'] =
                    stripslashes(base64_decode($fld_mapping['custom_sql']));
        }
    }

    if($profile['import_src_type'] == 'T') {

        $parsed_file = array('tmp_table' => $profile['dbtable_src']);

    } else {

        if($server_filenames){
            foreach($server_filenames as $s_file) {
                if (!file_exists($s_file))   
                    $profile['file_name'] = fi_files_path.$s_file;
                else   
                    $profile['file_name'] = $s_file;  

                list($profile, $profile) = cw_flexible_import_convert_sheet(array('name'=>$profile['file_name'], 'tmp_name'=>$profile['file_name']), $profile, $profile);

                $parsed_file = cw_call('cw_flexible_import_parse_file',array($profile, false));

                if($parsed_file['err']) break;
            }
        }else{
            $profile['import_file'] = $uploaded_import_file;
 

            list($profile, $profile) = cw_flexible_import_convert_sheet(array('name'=>$uploaded_import_file['name'], 'tmp_name'=>$uploaded_import_file['tmp_name']), $profile, $profile);

            $parsed_file = cw_call('cw_flexible_import_parse_file',array($profile, false));
        }

    }
    cw_log_add("flexible_import", array("Import started", "parsed_file"=>$parsed_file, "profile"=>$profile, "mapping_data"=>$mapping_data));

    $load_data_tables_qry = array();
    $tmp_load_tables = array();

    $old_tmp_load_tables = cw_query_column("show tables like 'tmp_load_%'");
    $already_dropped_tmp_tables = array(); 
    foreach ($old_tmp_load_tables as $tmp_load_tbl) {
        cw_csvxc_logged_query("DROP TABLE `$tmp_load_tbl`");
        $already_dropped_tmp_tables[] = $tmp_load_tbl; 
    } 
    foreach ($mapping_data as $import_section_name => $tbl_map) {
        $table_name = cw_flexible_import_add_prefix("tmp_load_",$import_section_name);
        $tmp_load_tables[$table_name] = array('orig_name'=>$import_section_name, 'tmp_load_fields' => array(), 'update_keys' => array());

        list($import_section_name_db, $import_section_name_table) = cw_csvxc_get_table_name_parts($import_section_name);  
        $table_key_name = "id_".strtolower($import_section_name_table);

        if (!in_array($table_name, $already_dropped_tmp_tables)) 
            $load_data_tables_qry[] = "DROP TABLE IF EXISTS ".cw_fi_safe_add_q($table_name);

        $add_field_qry = array();
        $field_names = array();
        $tmp_fields = array();

        foreach ($tbl_map as $col_id => $col_data) {
            $_dname = $col_id;//$col_data['imp_field'];
            if (!empty($csvxc_field_types[$_dname])) {
                $add_field_qry[] = "`$_dname` ".$csvxc_field_types[$_dname];
            } else {
                if (isset($csvxc_field_types["default_$import_section_name_table"]))
                    $add_field_qry[] = "`$_dname` ".$csvxc_field_types["default_$import_section_name_table"];
                else
                    $add_field_qry[] = "`$_dname` ".$csvxc_field_types['default'];
            }

            if (!empty($col_data['imp_field'])) {
                $tmp_fields[] = "`".$col_data['imp_field']."`";
                $field_names[] = "`".$_dname."`";
            } elseif (!empty($col_data['custom_sql'])) {
                $tmp_fields[] = "(".$col_data['custom_sql'].")";
                $field_names[] = "`".$_dname."`";
            }
            $tmp_load_tables[$table_name]['tmp_load_fields'][] = $_dname;

            if (isset($col_data['is_update_key']))
                if ($col_data['is_update_key'])  
                    $tmp_load_tables[$table_name]['update_keys'][] = $_dname;
        }
        $load_data_tables_qry[] = "CREATE TABLE ".cw_fi_safe_add_q($table_name)." ($table_key_name int(11) NOT NULL AUTO_INCREMENT, ".implode(", ", $add_field_qry).", PRIMARY KEY `$table_key_name` (`$table_key_name`))ENGINE=MyISAM;";
        $load_data_tables_qry[] = "INSERT DELAYED INTO ".cw_fi_safe_add_q($table_name)." (".implode(", ", $field_names).") SELECT ".implode(", ", $tmp_fields)." FROM ".cw_fi_safe_add_q($parsed_file['tmp_table']);

        if (!empty($map_process[$import_section_name])) 
            if (!empty($map_process[$import_section_name]['post_sql'])) {
                $sql_lines = explode("\n",$map_process[$import_section_name]['post_sql']);
                foreach ($sql_lines as $sql_l) { 
                    $sql_l = trim($sql_l);
                    if (empty($sql_l)) continue;
                    $load_data_tables_qry[] = $sql_l; 
                } 
            }

    }

    if($profile['import_src_type'] != 'T') {
        //$load_data_tables_qry[] = "DROP TABLE IF EXISTS ".cw_fi_safe_add_q($parsed_file['tmp_table']);
    }

    foreach ($load_data_tables_qry as $lqry) {
        cw_csvxc_logged_query($lqry);
    }

    cw_include('addons/flexible_import/include/tmp_tables_load.php');
//cw_var_dump($tmp_load_tables); die;
    global $tables;
    foreach ($tmp_load_tables as $tmp_load_table => $tmp_load_table_data) {

        if (in_array($tmp_load_table, array_keys($csvxc_allowed_sections))) continue;

        $dest_table = $tmp_load_table_data['orig_name'];
        if ($preview_mode)
            $dest_table = cw_call('cw_flexible_import_create_preview_mode_table', array($dest_table));    

        if (!empty($map_process[$tmp_load_table_data['orig_name']])) { 
            if (isset($map_process[$tmp_load_table_data['orig_name']]['clean_table']))
            if ($map_process[$tmp_load_table_data['orig_name']]['clean_table']) {

                $clean_table_condition = "";
                if (!empty($map_process[$tmp_load_table_data['orig_name']]['clean_table_condition'])) 
                    $clean_table_condition = ' WHERE '.$map_process[$tmp_load_table_data['orig_name']]['clean_table_condition'];

                cw_csvxc_logged_query("DELETE FROM ".cw_fi_safe_add_q($dest_table).$clean_table_condition);
            }
        }

        if (cw_csvxc_is_table_exists($dest_table)) {
            $copy_fields_list = "`".implode("` , `", $tmp_load_table_data['tmp_load_fields'])."`";
            if (empty($tmp_load_table_data['update_keys'])) {
                cw_csvxc_logged_query("REPLACE INTO ".cw_fi_safe_add_q($dest_table)." ($copy_fields_list) SELECT $copy_fields_list FROM ".cw_fi_safe_add_q($tmp_load_table));
            } else {
                cw_csvxc_logged_query("ALTER TABLE ".cw_fi_safe_add_q($tmp_load_table)." ADD COLUMN is_new int(11) NOT NULL DEFAULT 1");
                $upd_key_fields = array(); 
                foreach ($tmp_load_table_data['update_keys'] as $upd_key_fld) {
                    $upd_key_fields[] = cw_fi_safe_add_q($tmp_load_table).".`$upd_key_fld`=".cw_fi_safe_add_q($dest_table).".`$upd_key_fld`";  
                }  

                cw_csvxc_logged_query("UPDATE ".cw_fi_safe_add_q($tmp_load_table).",".cw_fi_safe_add_q($dest_table)." SET ".cw_fi_safe_add_q($tmp_load_table).".is_new=0 WHERE ".implode(" AND ",$upd_key_fields));
                $linked_count = cw_query_first_cell("SELECT count(*) FROM ".cw_fi_safe_add_q($tmp_load_table)." WHERE is_new=0"); 
                if ($linked_count > 0) { 
                    foreach ($tmp_load_table_data['tmp_load_fields'] as $tmp_load_field) {
                        if (in_array($tmp_load_field, $tmp_load_table_data['update_keys'])) continue;
                        cw_csvxc_logged_query("UPDATE ".cw_fi_safe_add_q($dest_table).",".cw_fi_safe_add_q($tmp_load_table)." SET ".cw_fi_safe_add_q($dest_table).".`$tmp_load_field` = ".cw_fi_safe_add_q($tmp_load_table).".`$tmp_load_field` WHERE ".implode(" AND ",$upd_key_fields));
                    }
                }
                cw_csvxc_logged_query("INSERT INTO ".cw_fi_safe_add_q($dest_table)." ($copy_fields_list) SELECT $copy_fields_list FROM ".cw_fi_safe_add_q($tmp_load_table)." WHERE ".cw_fi_safe_add_q($tmp_load_table).".is_new=1");
 
            }
            if ($dest_table == $tables['datahub_import_buffer'])  {
                cw_csvxc_logged_query("DELETE $tables[datahub_import_buffer].* FROM $tables[datahub_import_buffer] INNER JOIN $tables[datahub_import_buffer]_blacklist bl ON bl.item_xref=$tables[datahub_import_buffer].item_xref");
                cw_csvxc_logged_query("delete from $tables[datahub_import_buffer] where COALESCE(item_xref_cost_per_bottle, 0) < 3 AND trim(Source) <> 'Hub' AND trim(Source) <> 'Feed_SWE_store' AND trim(Source) <> 'Feed_DWB_store'");
            }
        } 
    }

    return $parsed_file;
}

//$table_name - full table name
function cw_flexible_import_create_preview_mode_table($tbl_name) {
    $prev_mode_tbl_name = cw_flexible_import_add_prefix('prev_mode_', $tbl_name);
    cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS $prev_mode_tbl_name LIKE $tbl_name");
    $orig_table_count = cw_query_first_cell("SELECT COUNT(*) FROM $tbl_name");
    if ($orig_table_count) {
        $prev_mode_table_count = cw_query_first_cell("SELECT COUNT(*) FROM $prev_mode_tbl_name");  
        if (!$prev_mode_table_count) {
            cw_csvxc_logged_query("INSERT DELAYED INTO $prev_mode_tbl_name SELECT * FROM $tbl_name");    
        } 
    }  
    return $prev_mode_tbl_name;
}

function cw_flexible_import_check_single_field($field_data, $num_fields, $single_field_data){

    $cv = array_count_values($field_data);
    if (count($cv)==2 && $cv[""]==($num_fields-1)) {
        foreach ($field_data as $k => $v) {
            if ($v !="") {
                if ($single_field_data['key'] && $single_field_data['key']==$k || !$single_field_data['key']) {
                    $single_field_data['value'] = $v;
                    $single_field_data['key'] = $k;
                    $single_field_data['is_single_field'] = true;
                }
            }
        }
    } else {
        $single_field_data['is_single_field'] = false;
    }

    return $single_field_data;


}

function cw_flexible_import_parse_line($data, $type, $enclosure_char, $escape_char, $field_params=false) {

    $field_data = array();

    if ($type=="advanced") {
        foreach($field_params as $k => $column) {
            $tmp_data = explode($column['delimiter'], $data);
            $tmp_str ='';
            $tmp_v = trim($tmp_data[0]);

            if (substr($tmp_v, 0, 1) == $enclosure_char) {
                if (substr($tmp_v, -1, 1)== $enclosure_char) {
                    $field_data[]= cw_flexible_import_escape(str_replace($enclosure_char.$enclosure_char, $enclosure_char, substr(trim($tmp_data[0]), 1, -1)), $escape_char);
                    $data = substr($data, strlen($tmp_data[0].$column['delimiter']));

                 } else {
                    foreach ($tmp_data as $v) {
                        $tmp_str .= $v.$column['delimiter'];
                        if (substr(trim($v), -1, 1)==$enclosure_char)
                            break;
                    }
                    $data = substr($data, strlen($tmp_str));
//                  $field_data[]= cw_flexible_import_escape(str_replace($enclosure_char.$enclosure_char, $enclosure_char, substr(trim($tmp_str), 1, -1)), $escape_char);
                    $field_data[] = trim(cw_flexible_import_escape(str_replace($enclosure_char, '', str_replace($enclosure_char.$enclosure_char, $enclosure_char, substr(trim($tmp_str), 0, -1))), $escape_char));
                }

            } else {
                $field_data[] = cw_flexible_import_escape(str_replace($enclosure_char.$enclosure_char, $enclosure_char, $tmp_v), $escape_char);
                $data = substr($data, strlen($tmp_data[0].$column['delimiter']));

            }

            if (!$field_params[$k+1] && $data && $data !="") {
                return $false;
            }
        }
    } else {

        foreach($data as  $v) {
            $tmp_v = trim($v);
            if (substr($tmp_v, 0, 1) == $enclosure_char) {
                if (substr($tmp_v, -1, 1)== $enclosure_char)
                    $field_data[]= cw_flexible_import_escape(trim(str_replace($enclosure_char.$enclosure_char, $enclosure_char, substr(trim($v), 1, -1))), $escape_char);
                else
                    $tmp_row .= $v;
            } elseif (substr($tmp_v, -1, 1)== $enclosure_char) {
                $tmp_row .= $v;
                $field_data[]= cw_flexible_import_escape(substr(trim($tmp_row), 1, -1), $escape_char);
                $tmp_row = '';
            } elseif($tmp_row=='') {
                $field_data[] = cw_flexible_import_escape(trim($v), $escape_char);
            } else {
                $tmp_row .= $v;
            }
        }
        if(!$field_data)
            return false;
    }
    return $field_data;
}


function cw_flexible_import_escape($string, $escape_char){

    if($escape_char=='\\'){
        $string= addslashes($string);
    }else{
        $find= array("\\", "'", '"');
        $replace= array($escape_char."\\", $escape_char."'", $escape_char.'"');
        $string = str_replace($find,$replace, $string);
    }
    return $string;

}

function cw_flexible_import_create_temp_table($fields){
    global $tables;

    $last_id = cw_query_first_cell("SELECT MAX(id) from $tables[flexible_import_files]");
    $id = (!$last_id ? 1 : ++$last_id);
    $table = 'cw_flexible_import_tmp_table_'.$id.'_'.rand(100,999);

    $schema  = "CREATE TABLE `$table` (\n";
    $schema .= "`id` int(11) NOT NULL AUTO_INCREMENT,";
    foreach($fields as $k => $field){
        if (trim($field) == '') continue;
        $schema .=  "`$field` TEXT,\n";
    }
    $schema .="PRIMARY KEY (`id`)";
    $schema .=") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    if(db_query($schema))
        return $table;
    else
        return false;

}

function cw_flexible_import_files_dir () {
    $path=fi_files_path;
    if (!is_dir($path)) mkdir($path);
    if ($h=opendir($path)) {
        while (false !== ($fn=readdir($h))) if (is_file("$path/$fn")) $files[]=$fn;
        closedir($h);
    }
    if (isset($files) && is_array($files)) rsort($files);

    $cnt = 0; 
    foreach ($files as $f_k => $f_v) {
        if ($cnt > 6) {
            $fname = rtrim($path,'/')."/".ltrim($f_v,'/');
            if (file_exists($fname))
                if(@unlink($fname))
                    unset($files[$f_k]); 
        } 
        $cnt++;
    }

    return $files;
}

function cw_flexible_import_strip_input(&$input, $key){
    $input = stripslashes($input);
}


function cw_flexible_import_recurring_import_check_new_files () {
    global $tables, $config, $target;
    $result = array();
 
    cw_load('dev');
    $longenoughago = time() - 1;//3600;

    $recurring_profiles = cw_query("select * from $tables[flexible_import_profiles] 
                    where active_reccuring=1 and recurring_last_run_date<'$longenoughago' and recurring_import_path!=''");

    foreach ($recurring_profiles as $rec_prof) {

        if (!file_exists($rec_prof['recurring_import_path']))
            $rec_prof['recurring_import_path'] = rtrim($config['flexible_import']['flex_import_files_folder'],'/').'/'.$rec_prof['recurring_import_path'];

        if (!file_exists($rec_prof['recurring_import_path'])) continue;

        $rec_prof['file_hash'] = md5_file($rec_prof['recurring_import_path']);
        $is_file_loaded_already = cw_query_first_cell("SELECT COUNT(*) FROM $tables[flexible_import_loaded_files_hash] WHERE profile_id='$rec_prof[id]' AND hash='$rec_prof[file_hash]'");
        if ($is_file_loaded_already) {

            //if ($target == 'datahub_buffer_match')
            //    print("Skipped already imported file $rec_prof[recurring_import_path] <br />\n");

            continue;
        }

        if ($target == 'datahub_buffer_match')
            print("<h2>Importing new file $rec_prof[recurring_import_path]</h2> <br />\n");

        $result[] = $rec_prof;

    }

    return $result;
}

function cw_flexible_import_recurring_import_load_files($profiles_to_load) {
    global $config;

    foreach($profiles_to_load as $rec_prof) {
        $parsed_file = cw_flexible_import_run_profile($rec_prof['id'], array($rec_prof['recurring_import_path']));

        cw_array2update("flexible_import_profiles", array('recurring_last_run_date'=>time()), "id='$rec_prof[id]'");

        cw_array2insert('flexible_import_loaded_files_hash', array('profile_id'=>$rec_prof['id'], 'hash'=>$rec_prof['file_hash'], 'date_loaded'=>time()));

        if ($config['flexible_import']['flex_import_auto_update'] == 'Y') {
            cw_datahub_update_linked_data();
        }
    } 
}

function cw_flexible_import_recurring_imports($time) {
    $profiles_to_load = cw_flexible_import_recurring_import_check_new_files();
    if (!empty($profiles_to_load))
        cw_flexible_import_recurring_import_load_files($profiles_to_load);

    return $profiles_to_load;
}

function cw_flexible_import_xmlObjToArr($obj) {

/**
 * convert xml objects to array
 * function from http://php.net/manual/pt_BR/book.simplexml.php
 * as posted by xaviered at gmail dot com 17-May-2012 07:00
 * NOTE: return array() ('name'=>$name) commented out; not needed to parse xlsx
 */
        $namespace = $obj->getDocNamespaces(true);
        $namespace[NULL] = NULL;

        $children = array();
        $attributes = array();
        $name = strtolower((string)$obj->getName());

        $text = trim((string)$obj);
        if( strlen($text) <= 0 ) {
            $text = NULL;
        }

        // get info for all namespaces
        if(is_object($obj)) {
            foreach( $namespace as $ns=>$nsUrl ) {
                // atributes
                $objAttributes = $obj->attributes($ns, true);
                foreach( $objAttributes as $attributeName => $attributeValue ) {
                    $attribName = strtolower(trim((string)$attributeName));
                    $attribVal = trim((string)$attributeValue);
                    if (!empty($ns)) {
                        $attribName = $ns . ':' . $attribName;
                    }
                    $attributes[$attribName] = $attribVal;
                }

                // children
                $objChildren = $obj->children($ns, true);
                foreach( $objChildren as $childName=>$child ) {
                    $childName = strtolower((string)$childName);
                    if( !empty($ns) ) {
                        $childName = $ns.':'.$childName;
                    }
                    $children[$childName][] = cw_flexible_import_xmlObjToArr($child);
                }
            }
        }

        return array(
           // name not needed for xlsx
           // 'name'=>$name,
            'text'=>$text,
            'attributes'=>$attributes,
            'children'=>$children
        );
    }

function cw_flexible_import_fputcsv($handle, $fields, $delimiter = ',', $enclosure = '"', $escape = '\\') {
/**
 * write array to csv file
 * enhanced fputcsv found at http://php.net/manual/en/function.fputcsv.php
 * posted by Hiroto Kagotani 28-Apr-2012 03:13
 * used in lieu of native PHP fputcsv() resolves PHP backslash doublequote bug
 * !!!!!! To resolve issues with escaped characters breaking converted CSV, try this:
 * Kagotani: "It is compatible to fputcsv() except for the additional 5th argument $escape, 
 * which has the same meaning as that of fgetcsv().  
 * If you set it to '"' (double quote), every double quote is escaped by itself."
 */
  $first = 1;
  foreach ($fields as $field) {
    if ($first == 0) fwrite($handle, ",");

    $f = str_replace($enclosure, $enclosure.$enclosure, $field);
    if ($enclosure != $escape) {
      $f = str_replace($escape.$enclosure, $escape, $f);
    }
    $f = str_replace("\n\r",' ',$f);
    $f = str_replace("\n",' ',$f);
    if (strpbrk($f, " \t\n\r".$delimiter.$enclosure.$escape) || strchr($f, "\000")) {
      fwrite($handle, $enclosure.$f.$enclosure);
    } else {
      fwrite($handle, $f);
    }

    $first = 0;
  }
  fwrite($handle, "\n");
}

function cw_flexible_import_xml2csv($dir, $sheet_name, $newcsvfile) {
    $strings = array();
    $filename = $dir."/xl/sharedStrings.xml";
/**
 * XMLReader node-by-node processing improves speed and memory in handling large XLSX files
 * Hybrid XMLReader/SimpleXml approach 
 * per http://stackoverflow.com/questions/1835177/how-to-use-xmlreader-in-php
 * Contributed by http://stackoverflow.com/users/74311/josh-davis
 * SimpleXML provides easier access to XML DOM as read node-by-node with XMLReader
 * XMLReader vs SimpleXml processing of nodes not benchmarked in this context, but...
 * published benchmarking at http://posterous.richardcunningham.co.uk/using-a-hybrid-of-xmlreader-and-simplexml
 * suggests SimpleXML is more than 2X faster in record sets ~<500K
 */

    $z = new XMLReader;

    $z->open($filename);

    $doc = new DOMDocument;

    $csvfile = fopen($newcsvfile,"w");

    while ($z->read() && $z->name !== 'si');
        ob_start();

    while ($z->name === 'si') {
        // either one should work
        $node = new SimpleXMLElement($z->readOuterXML());
        // $node = simplexml_import_dom($doc->importNode($z->expand(), true));

        $result = cw_flexible_import_xmlObjToArr($node);
        $count = count($result['text']) ;

        if (isset($result['children']['t'][0]['text'])) {

            $val = $result['children']['t'][0]['text'];
            $strings[]=$val;

        };
        $z->next('si');
        $result=NULL;
    };

    ob_end_flush();
    $z->close($filename);

    $filename = $dir."/xl/worksheets/$sheet_name";
    $z = new XMLReader;
    $z->open($filename);

    $doc = new DOMDocument;

    $rowCount = "0";
    $doc = new DOMDocument;
    $sheet = array();
    $nums = array("0","1","2","3","4","5","6","7","8","9");
    while ($z->read() && $z->name !== 'row');
        ob_start();

    while ($z->name === 'row') {
        $thisrow = array();

        $node = new SimpleXMLElement($z->readOuterXML());
        $result = cw_flexible_import_xmlObjToArr($node);

        $cells = $result['children']['c'];
        $rowNo = $result['attributes']['r'];
        $colAlpha = "A";

        foreach ($cells as $cell) {

            if (array_key_exists('v',$cell['children'])) {

                $cellno = str_replace($nums,"",$cell['attributes']['r']);

                for ($col = $colAlpha; $col != $cellno; $col++) {
                    $thisrow[]=" ";
                    $colAlpha++;
                };

                if (array_key_exists('t',$cell['attributes'])&&$cell['attributes']['t']='s') {
                    $val = $cell['children']['v'][0]['text'];
                    $string = $strings[$val] ;
                    $thisrow[]=$string;
                } else {
                    $thisrow[]=$cell['children']['v'][0]['text'];
                }
            } else {
                $thisrow[]="";
            };
            $colAlpha++;
        };

        $rowLength = count($thisrow);
        $rowCount++;
        $emptyRow = array();

        while ($rowCount<$rowNo) {
            for($c=0;$c<$rowLength;$c++) {
                $emptyRow[]="";
            }

            if (!empty($emptyRow)) {
                cw_flexible_import_fputcsv($csvfile,$emptyRow);
            };
            $rowCount++;
        };

        cw_flexible_import_fputcsv($csvfile,$thisrow);

        if ($rowCount<$throttle||$throttle==""||$throttle=="0") {
            $z->next('row');
        } else {
            break;
        };

        $result=NULL; 
    };

    $z->close($filename);
    ob_end_flush();

}

function cw_flexible_import_cleanUp($dir) {
    $tempdir = opendir($dir);
    while(false !== ($file = readdir($tempdir))) {
        if($file != "." && $file != "..") {
             if(is_dir($dir.$file)) {
                chdir('.');
                cw_flexible_import_cleanUp($dir.$file.'/');
                rmdir($dir.$file);
            }
            else
                unlink($dir.$file);
        }
    }
    closedir($tempdir);
}

function cw_flexible_import_convert_sheet($test_import_file, $fi_profile, $prefilled_data) {

    global $app_main_dir, $var_dirs; 

    if (strtolower(substr($test_import_file['name'],-5)) == '.xlsx' || $test_import_file['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        include_once $app_main_dir.'/include/lib/PCLZip/pclzip.lib.php';
        $archive = new PclZip($test_import_file['tmp_name']);

        $xml_unpack_dir = $var_dirs['tmp'].'/'.substr(md5($test_import_file['tmp_name']),8);
        if (!file_exists($xml_unpack_dir)) {
            mkdir($xml_unpack_dir);
        }
        $list = $archive->extract(PCLZIP_OPT_PATH, $xml_unpack_dir);

        foreach ($list as $xml_part) {
            if (strpos($xml_part['stored_filename'], "xl/worksheets/") !== false && strtolower(substr($xml_part['stored_filename'],-4)) == '.xml') {
                $worksheet = str_replace("xl/worksheets/", '', $xml_part['stored_filename']);
                $worksheets[] = $worksheet;
            }
        }

        if (!empty($fi_profile['selected_sheet']) && in_array($fi_profile['selected_sheet'], $worksheets))
            $selected_sheet = $fi_profile['selected_sheet'];
        else
            $selected_sheet = $worksheets[0];

        $testcsvfile = $xml_unpack_dir."_".str_replace(".xml", "_xml.csv", $selected_sheet);

        if (file_exists($xml_unpack_dir."/xl/sharedStrings.xml")) { 
            cw_flexible_import_xml2csv($xml_unpack_dir, $selected_sheet, $testcsvfile);
            cw_flexible_import_cleanUp($xml_unpack_dir."/");
            //rmdir($xml_unpack_dir);
        } else {
            print($test_import_file['tmp_name'].' was not unzipped correctly <br>');
        }

        $prefilled_data['type'] = $fi_profile['type'] = 'comma';
        $prefilled_data['file_name'] = $fi_profile['file_name'] = $testcsvfile;
        $prefilled_data['sheets'] = $worksheets;
        $prefilled_data['selected_sheet'] = $selected_sheet;
   }
   return array($fi_profile, $prefilled_data);
}
