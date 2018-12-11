<?php

function cw_csvxc_duplicate_table($tbl_src, $tbl_dest, $copy_content) {

    cw_csvxc_logged_query("DROP TABLE IF EXISTS `$tbl_dest`");
    cw_csvxc_logged_query("CREATE TABLE `$tbl_dest` LIKE `$tbl_src`");

    if ($copy_content)
        cw_csvxc_logged_query("INSERT INTO `$tbl_dest` SELECT * FROM `$tbl_src`");
}

//$tab_name - full table name, no db name
function cw_csvxc_get_table_fields($tab_name) {
    $_fields_arr = cw_query("show fields from `$tab_name`");
    $fields_arr = array();
    foreach ($_fields_arr as $fdata) {
        $fields_arr[] = $fdata['Field'];
    }
    return $fields_arr;
}

//$tab_name, $src_tab_name - full table names
function cw_csvxc_get_fields_combination($tab_name, $fields_arr, $src_tab_name) {

    $l_join_flds_arr = array();

    foreach ($fields_arr as $fld_name) {
        $fld = cw_csvxc_get_mapped_by_tbl_field($fld_name, $src_tab_name, $tab_name);
        if (!empty($fld)) {
            $l_join_flds_arr[] = $fld;
        } else
            $l_join_flds_arr[] = "$tab_name.`$fld_name`";
    }
    return $l_join_flds_arr;
}

//$src_table, $ref_table, $dest_table - full table names
function cw_csvxc_make_update_fields_arr($src_table, $ref_table, $src_key_field, $dest_table='') {

    if (empty($dest_table)) 
        $dest_table = $ref_table;

    $fields_arr = cw_csvxc_get_table_fields($dest_table);
    $res_array = array();

    if (is_array($src_key_field))
        $src_key_fields = $src_key_field;
    else 
        $src_key_fields = array($src_key_field);

    $avail_source_fields = cw_csvxc_get_table_fields($src_table);

    foreach ($fields_arr as $fld_name) {
        $fld = cw_csvxc_get_mapped_by_tbl_field($fld_name, $src_table, $ref_table); 
        if (!empty($fld) && !in_array($fld, $src_key_fields) && in_array($fld, $avail_source_fields)) {
            $res_array[] = "$dest_table.`$fld_name`=$src_table.`$fld`";
        }
    }

    return $res_array;
}

//$tbl_name
//$src_tbl_name - full table names
function cw_csvxc_get_columns4insert($tbl_name, $src_tbl_name, $tmp_tbl_name='') {
    global $csvxc_fmap;
    $ins_into_fields = array();
    $select_fields = array();

    if (empty($tmp_tbl_name)) 
      $tmp_tbl_name = $tbl_name;

    $avail_ins_fields = cw_csvxc_get_table_fields($tmp_tbl_name);
    $avail_src_fields = cw_csvxc_get_table_fields($src_tbl_name);

    foreach ($csvxc_fmap[$src_tbl_name] as $load_field=>$load_f_data) {
        if ($load_f_data['table'] == $tbl_name && in_array($load_f_data['field'], $avail_ins_fields) && in_array($load_field, $avail_src_fields)) {
            $ins_into_fields[] = "$tmp_tbl_name.`$load_f_data[field]`";
            $select_fields[] = "$src_tbl_name.`$load_field`";
            //$select_fields[] = (!empty($load_f_data['transform'])?$load_f_data['transform']:("$src_tbl_name.`$load_field`"));
        } elseif (isset($load_f_data['alter'])) {
            if ($load_f_data['alter']['table'] == $tbl_name && 
                in_array($load_f_data['alter']['field'], $avail_ins_fields) && in_array($load_field, $avail_src_fields)) {
                $ins_into_fields[] = $tmp_tbl_name.'.`'.$load_f_data['alter']['field'].'`';
                $select_fields[] = "$src_tbl_name.`$load_field`";
                //$select_fields[] = (!empty($load_f_data['transform'])?$load_f_data['transform']:("$src_tbl_name.`$load_field`"));
            }
        }
    }
    return array($ins_into_fields, $select_fields);
}

function cw_csvxc_logged_query($query, $comment = '') {
    global $cw_fi_preview_mode;

    if (empty($comment) && !$cw_fi_preview_mode) 
        cw_flush("<b>Running query</b>: $query <br>");

    db_query($query);

    cw_log_add('flexible_import'.(($cw_fi_preview_mode)?'_preview':''), array($query, $comment));

    if (!$cw_fi_preview_mode) { 
        if (empty($comment)) 
            cw_flush("..done<br>"); 
        else 
            cw_flush($comment); 
    }  
}

//$tblname - allowed both full name and index from $tables array
function cw_csvxc_get_mapped_by_tbl_field($tblfield, $section='tmp_load_PRODUCTS', $tblname = 'products') {
    global $csvxc_fmap, $fi_tables;

    if (!empty($fi_tables[$tblname])) 
        $tblname = $fi_tables[$tblname];

    $arr2check = $csvxc_fmap[$section];
   
    foreach ($arr2check as $src_fname=>$mapdata) {
        if ($mapdata['table'] == $tblname && $mapdata['field'] == $tblfield) 
            return $src_fname;
        if (!empty($mapdata['alter'])) {
            if ($mapdata['alter']['table'] == $tblname && $mapdata['alter']['field'] == $tblfield) 
                return $src_fname;     
        }      
    }
}

//$tblname - allowed both full name and index from $tables array
function cw_csvxc_get_mapped_by_tbl_field2($tblfield, $section='tmp_load_PRODUCTS', $tblname = 'products') {
    global $csvxc_fmap, $fi_tables;

    if (!empty($fi_tables[$tblname]))
        $tblname = $fi_tables[$tblname];

    $arr2check = $csvxc_fmap[$section];

    foreach ($arr2check as $src_fname=>$mapdata) {
        if ($mapdata['table'] == $tblname && $mapdata['field'] == $tblfield)
            return (!empty($mapdata['transform'])?$mapdata['transform']:($section.'.`'.$src_fname.'`'));
        if (!empty($mapdata['alter'])) {
            if ($mapdata['alter']['table'] == $tblname && $mapdata['alter']['field'] == $tblfield)
                return (!empty($mapdata['alter']['transform'])?$mapdata['alter']['transform']:($section.'.`'.$src_fname.'`'));
        }
    }
}

function cw_csvxc_get_table_name_parts($tbl_name, $trim_tab_name = true) {
    global $app_config_file;

    if (strpos($tbl_name,'.') !== false) { 
        $name_parts = explode('.', $tbl_name);
        $tbl_name = $name_parts[1];
        $dbname = $name_parts[0];
    } else {
        $dbname = $app_config_file['sql']['db'];
    }
    if ($trim_tab_name) $tbl_name = trim($tbl_name, '`'); 
    return array($dbname, $tbl_name);
}

//$tbl_name - full table name, db name allowed
function cw_csvxc_is_table_exists($tbl_name) {
    
    list($dbname, $tbl_name) = cw_csvxc_get_table_name_parts($tbl_name);

    return cw_query_first_cell("SELECT count(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = '$tbl_name'");
}

function cw_csvxc_set_col_exist_flag($section) {
    global $csvxc_fmap;
    global $tmp_fields_arr; 

    if (!isset($tmp_fields_arr[$section])) {
        $_fields_arr = cw_query("show fields from $section");
        $tmp_fields_arr[$section] = array();
        foreach ($_fields_arr as $fdata) {
            $tmp_fields_arr[$section][] = $fdata['Field'];
        }
    }

    if (is_array($csvxc_fmap[$section]))
        foreach ($csvxc_fmap[$section] as $load_field=>$load_f_data) {
            $csvxc_fmap[$section][$load_field]['exists_column'] = in_array($load_field, $tmp_fields_arr[$section]);
        }

    cw_csvxc_backup_accessed_tables($section);
}

function cw_csvxc_update_linked_table($src_table, $src_field, $section = '') {
    global $csvxc_fmap;

    $fields_sel = array();
    $fields_ins = array();

    if (empty($section)) 
        $section = $src_table;

    $field_data = $csvxc_fmap[$section][$src_field];
    if (empty($field_data))
        return;
 
    $replace_fields = $field_data['replace_fields'];
   
    if (empty($replace_fields) && !empty($field_data['alter'])) {
        $replace_fields = $field_data['alter']['replace_fields'];
        $field_data = $field_data['alter'];  
    }   

    $dest_table = $field_data['table'];
    
    $fields_ins[] = $field_data['field'];
    $fields_sel[] = $src_field;

    if (!empty($replace_fields)) {
        foreach ($replace_fields as $f_ins => $f_sel) {
            if (in_array($f_sel, array_keys($csvxc_fmap[$section])) && !$csvxc_fmap[$section][$f_sel]['exists_column']) {
                continue; 
            } 
            $fields_ins[] = $f_ins;
            $fields_sel[] = $f_sel;
        }
    }

    if ($src_table == 'tmp_load_PRODUCTS' && $src_field != 'CATEGORYID')  
      $WHERE_COND = " WHERE PRODUCT!=''";

    if ($dest_table == 'cw_attributes_values') {
        $item_id_field = $replace_fields['item_id'];
        $attribute_id = $replace_fields['attribute_id'];
        $item_type = $replace_fields['item_type'];
        cw_csvxc_logged_query("DELETE cw_attributes_values.* FROM cw_attributes_values, $src_table WHERE cw_attributes_values.item_id=$src_table.$item_id_field AND cw_attributes_values.attribute_id=$attribute_id AND cw_attributes_values.item_type=$item_type"); 
    } 

    cw_csvxc_logged_query("REPLACE INTO $dest_table (".implode(",", $fields_ins).") SELECT ".implode(",", $fields_sel)." FROM $src_table $WHERE_COND");
}

function cw_csvxc_delete_old_images($qry, $img_cw_type) {

    global $app_dir;

    $images_arr = cw_query($qry);

    if (empty($images_arr)) return;

    cw_flush("<b>Deleting old images $img_cw_type</b> found ".count($images_arr)." to delete <br>");

    $flush_step = intval(count($images_arr)/100);
    $flush_cnt = 0;
    foreach ($images_arr as $img_data) {

        if (ltrim($img_data['img_full_path'], '.') == '') { cw_flush(" "); continue; }

        $full_fname = $app_dir.ltrim($img_data['img_full_path'], '.');

        if (is_dir($full_fname)) { print_r($img_data); cw_flush(" "); continue;}

        if (file_exists($full_fname)) 
            unlink($full_fname);
        else
            cw_flush(' '); 

        db_query("DELETE FROM cw_$img_cw_type WHERE id=$img_data[img_key_fld]");

        $flush_cnt++;
        if ($flush_cnt >= $flush_step) {
           cw_flush(". ");  
           $flush_cnt = 0;
        }   
    }
    cw_flush("<b>Done</b><br>");
}

function cw_csvxc_transfer_import_images($src_table, $src_field, $key_field, $alt_field='') {

    global $src_images_path, $dst_images_path, $csvxc_images_translate_path, $cw_images_subdir;

    if (empty($csvxc_images_translate_path[$src_table."_".$src_field]))
        return;

    if (!empty($alt_field)) 
        $alt_field_qry = ", `$alt_field`";

    $imgs_list = cw_query($qry="SELECT `$src_field`, `$key_field`$alt_field_qry FROM `$src_table` WHERE `$src_field`!='' AND `$key_field`!=0");
    cw_log_add("flexible_import", $qry);

    $img_cw_type = $csvxc_images_translate_path[$src_table."_".$src_field]['dir'];

    cw_flush("<b>Importing new images $img_cw_type</b> found ".count($imgs_list)." to copy <br>");
    cw_log_add("flexible_import", "<b>Importing new images $img_cw_type</b> found ".count($imgs_list)." to copy <br>");

    $flush_step = intval(count($imgs_list)/100);
    $flush_cnt = 0;

    if (empty($imgs_list))
        return;

    if (!isset($csvxc_images_translate_path[$src_table."_".$src_field]['table']))  
        $csvxc_images_translate_path[$src_table."_".$src_field]['table'] = 'cw_'.$img_cw_type;

    cw_flush("Taking images from $src_images_path <br>");
    cw_log_add("flexible_import", "Taking images from $src_images_path");

    $img_exts = array(1=>'gif', 2=>'jpg', 3=>'png');
    foreach ($imgs_list as $img_data) {

        if (strpos($img_data[$src_field], "http:") !== false) {

            $image_type = 2;
            foreach ($img_exts as $im_type=>$im_ext) {
                if (strpos($img_data[$src_field], ".$im_ext") !== false) {
                    $image_type = $im_type; break;
                } 
            } 
            $path_parts = parse_str($img_data[$src_field]);  
            $image_ins_array = array(
                'id' => $img_data[$key_field],
                'image_path' => $img_data[$src_field],
                'image_type' => $image_type,
                'image_x' => 0,
                'image_y' => 0,
                'image_size' => 0,
                'filename' => addslashes(ltrim($path_parts['path'], '/')),
                'date' => time(),
                'alt' => (isset($img_data[$alt_field])?$img_data[$alt_field]:''),
                'avail' => 1,
                'orderby' => 0,
                'md5' => '' 
            );
            cw_flush("Inserted web file ".$img_data[$src_field]." <br>");

            cw_log_add("flexible_import", "Inserted web file ".$img_data[$src_field]);

            cw_array2insert($csvxc_images_translate_path[$src_table."_".$src_field]['table'], $image_ins_array, 'true');

            $flush_cnt++;
            if ($flush_cnt >= $flush_step) {
               //cw_flush("+ ");
               $flush_cnt = 0;
            }
            continue;

/*
            cw_flush("Loading web file $img_data[$src_field]<br>");
            //continue;
  
            $web_file_content = file_get_contents($img_data[$src_field]);
            if ($web_file_content) {
                $temp_image_name = $src_images_path.'/'.md5($img_data[$src_field]);
                @unlink($temp_image_name); 
                if (file_put_contents($temp_image_name, $web_file_content)) {
                    $size_info = getimagesize($temp_image_name);
                    if (isset($img_exts[$size_info[2]])) {
                        $new_temp_image_name = $temp_image_name.".".$img_exts[$size_info[2]];
                        rename($temp_image_name, $new_temp_image_name);
                        if (file_exists($new_temp_image_name)) {
                            $img_data[$src_field] = md5($img_data[$src_field]).".".$img_exts[$size_info[2]]; 
                        } 
                    }
                }
            }  
*/
        }

        $src_path = $src_images_path.'/'.ltrim($img_data[$src_field], '/');

        if (!file_exists($src_path)) {  
            cw_flush("Cant find file $src_path <br>");
            cw_log_add("flexible_import", "Cant find file $src_path");
            //cw_flush("- "); 
            continue;
        }

        $flush_cnt++;
        if ($flush_cnt >= $flush_step) {
           //cw_flush("+ ");
           $flush_cnt = 0;
        }

        
        $subdir_fname = $img_cw_type;  

        $dest_filename = end(explode('/', $img_data[$src_field]));

        $dest_path = $dst_images_path.'/'.$subdir_fname.'/'.$dest_filename;

        $src_file_md5 = md5_file($src_path); 
 
        $fname_c = 1;
        while(file_exists($dest_path) && $fname_c<10 && strpos($src_path,"no_image.jpg")===false && md5_file($dest_path) != $src_file_md5) {
            $path_parts = pathinfo($dest_path);
            $dest_filename = $path_parts['filename'].'_'.$fname_c.'.'.$path_parts['extension'];
            $dest_path = $dst_images_path.'/'.$subdir_fname.'/'.$dest_filename;
            $fname_c++;
        }

        if (md5_file($dest_path) == $src_file_md5) 
            $copy_success = true;  
        else
            $copy_success = copy($src_path, $dest_path);  

        if ($copy_success) {
            list($image_x, $image_y, $image_type) = getimagesize($dest_path);
            $path_parts = pathinfo($dest_path);
            $image_ins_array = array(
                'id' => $img_data[$key_field],  
                'image_path' => addslashes('.'.$cw_images_subdir.'/'.$subdir_fname.'/'.$dest_filename), 
                'image_type' => $image_type,
                'image_x' => $image_x,
                'image_y' => $image_y,
                'image_size' => filesize($dest_path),    
                'filename' => addslashes($path_parts['basename']),
                'date' => time(),
                'alt' => (isset($img_data[$alt_field])?$img_data[$alt_field]:''),
                'avail' => 1,
                'orderby' => 0, 
                'md5' => md5_file($dest_path)         
            ); 
            cw_flush("Inserted file $dest_path copied from $src_path <br>");
            cw_log_add("flexible_import", "Inserted file $dest_path copied from $src_path");
            cw_array2insert($csvxc_images_translate_path[$src_table."_".$src_field]['table'], $image_ins_array, 'true'); 
        } else {
            cw_flush("Cant copy image $src_path to $dest_path");
            cw_log_add("flexible_import", "Cant copy image $src_path to $dest_path");
        }
    }
    cw_flush("<b>Done</b><br>");
}

function cw_csvxc_backuptable($tabname) {

    static $backedup_tables = array();

    if (!isset($backedup_tables[$tabname])) {
        if (cw_csvxc_is_table_exists($tabname)) {
            $backedup_tables[$tabname] = 1;
            cw_csvxc_logged_query("DROP TABLE IF EXISTS `csvxcbak_$tabname`");
            cw_csvxc_logged_query("CREATE TABLE `csvxcbak_$tabname` LIKE `$tabname`"); 
            cw_csvxc_logged_query("INSERT INTO `csvxcbak_$tabname` SELECT * FROM `$tabname`");
        } 
    }
}

function cw_csvxc_restoretable($tabname) {
    if (cw_csvxc_is_table_exists("csvxcbak_$tabname")) {
        cw_csvxc_logged_query("DELETE FROM `$tabname`"); 
        cw_csvxc_logged_query("INSERT INTO `$tabname` SELECT * FROM `csvxcbak_$tabname`");
        cw_csvxc_logged_query("DROP TABLE IF EXISTS `csvxcbak_$tabname`");
    }
}

function cw_csvxc_restoretables_all() {
    $backed_up_tables = cw_query_column("SHOW TABLES LIKE 'csvxcbak_%'");
    foreach ($backed_up_tables as $bck_name) {
        $org_name = str_replace('csvxcbak_', '', $bck_name);
        cw_csvxc_restoretable($org_name);
    }
}

function cw_csvxc_backup_accessed_tables($section) {
    global $csvxc_fmap;
    $section_data = $csvxc_fmap[$section];
    foreach ($section_data as $f_name=>$f_data) {
        if (!empty($f_data['table'])) 
            cw_csvxc_backuptable($f_data['table']);
 
        if (!empty($f_data['alter'])) 
            if (!empty($f_data['alter']['table']))   
                cw_csvxc_backuptable($f_data['alter']['table']);
    }   
}

?>
