<?php

function cw_datahub_add_log_entry($event) {

   if (empty($event)) {
       cw_log_add('datahub_log_entries', array('type'=>'ERROR, empty event record attempt', '$event'=>$event));
   }

   global $customer_id;

   $log_id = cw_array2insert('datahub_log', array(
                      'date' => time(),
                      'event_type' => empty($event['type'])?'I':$event['type'],  
                      'event_message' => addslashes($event['message']),
                      'event_source' => addslashes($event['source']),
                      'customer_id' => $customer_id 
   ));

   return $log_id;
}

function cw_datahub_get_log_entry($log_id) {
   global $tables; 

   $result = cw_query_first("SELECT * FROM $tables[datahub_log] WHERE log_id = '".intval($log_id)."'");
   return $result;
}

function cw_datahub_search_log_entries($data) {
   global $tables, $event_type_names;

   $data['sort_direction'] = !empty($data['sort_direction'])?$data['sort_direction']:'DESC';

   $data['limit'] = !empty($data['limit'])?intval($data['limit']):'100';
   $limit_query = "LIMIT $data[limit]"; 

   $result = cw_query("SELECT * FROM $tables[datahub_log] ORDER BY log_id $data[sort_direction] $limit_query");

   foreach ($result as $res_k => $res_v) {

       if (in_array($res_v['event_type'], array_keys($event_type_names))) 
           $result[$res_k]['event_type_name'] = $event_type_names[$res_v['event_type']];
       else
           $result[$res_k]['event_type_name'] = 'Event';
   }

   return $result; 
}

function cw_datahub_filter_profiles($profiles, $tables_datahub_import_buffer = '') {
    global $tables;  
    $result = array();

    if ($tables_datahub_import_buffer == '') 
        $tables_datahub_import_buffer = $tables['datahub_import_buffer'];

//    $tables_datahub_import_buffer = 'cw_import_feed';

    foreach ($profiles as $p) {
        if (empty($p['mapping_data']) || !is_array($p['mapping_data'])) continue; 
        foreach ($p['mapping_data'] as $tbl_name => $fields_map) {
            if (strpos($tbl_name, "`$tables_datahub_import_buffer`") !== false || $tables_datahub_import_buffer == $tbl_name) {
                $result[] = $p;
                break;
            } 
        }
    }

    return $result;
}

function cw_datahub_search_buffer_data() {
    global $tables;
    $result = cw_query("SELECT * FROM $tables[datahub_import_buffer]");

    return $result;
}

function cw_datahub_table_filter_fields($fields, $filter_field, $filter_value) {
    $result = array();

    foreach ($fields as $field_name => $field_data) {
        if ($field_data[$filter_field] == $filter_value)
            $result[] = $field_name;
    }

    return $result;
}

function cw_datahub_buffer_table_filter_fields($filter_field, $filter_value = true) {
    global $dh_buffer_table_fields;
/*
    $result = array();

    foreach ($dh_buffer_table_fields as $field_name => $field_data) {
        if ($field_data[$filter_field] == $filter_value)
            $result[] = $field_name;
    }
*/
    return cw_datahub_table_filter_fields($dh_buffer_table_fields, $filter_field, $filter_value);
}

function cw_datahub_main_table_filter_fields($filter_field, $filter_value = true) {
    global $dh_main_table_fields;
    return cw_datahub_table_filter_fields($dh_main_table_fields, $filter_field, $filter_value);
}

function cw_datahub_buffer_item_quick_display($buffer_item) {

    $fields2display = array();
    $fields2display[] = '#'.$buffer_item['table_id'];
    unset($buffer_item['table_id']);

    if (strpos($buffer_item['Wine'], $buffer_item['Producer']) !== false)
        unset($buffer_item['Producer']);  
 
    foreach ($buffer_item as $fname => $fval) {
        if (!empty($fval)) {
            $fields2display[] = $fval; 
        }
    }
    $display = implode(' ', $fields2display);
    return $display;
}

function cw_datahub_buffer_items_to_edit() {

    $buffer_table_quick_preview_fields = cw_datahub_buffer_table_filter_fields('quick_preview');
    if (!in_array('table_id', $buffer_table_quick_preview_fields)) 
        $buffer_table_quick_preview_fields[] = 'table_id';

    $buffer_view_table = cw_datahub_import_buffer_view_table_name();

    $buffer_items = cw_query("SELECT `".implode('`,`', $buffer_table_quick_preview_fields)."` from $buffer_view_table order by table_id"); 

    foreach ($buffer_items as $bi_k => $bi_v) {
        $buffer_items[$bi_k]['display'] = cw_datahub_buffer_item_quick_display($bi_v);
    }

    return $buffer_items;
}

/* prepares columns for ssp class of DataTable jquery plugin
// returns:
$columns = array(
    array( 'db' => 'Source', 'dt' => 0 ),
    array( 'db' => 'Wine',   'dt' => 1 ),
    array( 'db' => 'Name',   'dt' => 2 ),
    array( 'db' => 'Vintage', 'dt' => 3 ),
);
*/
function cw_datahub_get_buffer_table_fields($table = '') {
    global $tables;

    $buffer_table_excluded_fields = cw_datahub_buffer_table_filter_fields('excl_list');

    if ($table == '')
        $table = $tables['datahub_import_buffer']; 

    $buffer_tbl_fields = cw_check_field_names(array(), $table);

    $columns = array();
    $cnt = 0;
    foreach ($buffer_tbl_fields as $field_id => $field_name) {
        if (is_array($buffer_table_excluded_fields))  
            if (in_array($field_name, $buffer_table_excluded_fields)) continue;
        $new_column = array('db'=>$field_name, 'dt'=>$cnt++);

        if ($field_name == 'size') $new_column['width'] = '10';
        if ($field_name == 'Vintage') $new_column['width'] = '5';
        if ($field_name == 'Region') $new_column['width'] = '10';
        if ($field_name == 'Match Items') $new_column['width'] = '40';

        $columns[] = $new_column;
    }

    return $columns;
}

function cw_datahub_get_main_table_fields() {
    global $tables, $dh_main_table_fields;

    $main_tbl_fields = cw_check_field_names(array(), $tables['datahub_main_data']);

    $columns = array();

    foreach ($main_tbl_fields as $field_name) {
        if (in_array($field_name, array_keys($dh_main_table_fields))) {
            if ($dh_main_table_fields[$field_name]['disabled']) continue;
            $new_column = $dh_main_table_fields[$field_name];
            $new_column['field'] = $field_name;
        } else { 
            $new_column = array(
                'field' => $field_name
            );
        }  
        $columns[] = $new_column;

/*         
        if (in_array($field_name, $main_table_enabled_edit_fields)) 
        $columns[] = array(
           'field' => $field_name,
           'main_display' => in_array($field_name, $main_table_display_fields)  
        );
*/
    } 
/* 
    $columns[] = array(
        'field' => 'rp_rating',
        'main_display' => 0
    );
*/
 
    return $columns;
}

function cw_datahub_get_update_nonstock_preview_fields() {
    global $tables, $dh_uns_table_fields;

    $_uns_tbl_fields = cw_check_field_names(array(), $tables['datahub_update_nonstock_preview']);

    if (!isset($dh_uns_table_fields)) 
        $dh_uns_table_fields = array();

    $uns_tbl_fields = array();
    $dt = 0;
    foreach ($_uns_tbl_fields as $field_name) {
        if (in_array($field_name, array_keys($dh_uns_table_fields))) {
            if ($dh_uns_table_fields[$field_name]['disabled']) continue;
            $new_column = $dh_uns_table_fields[$field_name];
            $new_column['field'] = $field_name;
        } else {
            $new_column = array(
                'field' => $field_name
            );

            if (strpos($field_name, 'Dest_') !== false) {
                $field_name = '>> '.$field_name;
                $new_column['type'] = 'readonly';
            }    

            if (strpos($field_name, 'Src_') !== false)   
                $field_name = $field_name.' >>';

            if (strpos($field_name, 'image') !== false)  
                $new_column['is_image'] = true;
    
            $new_column['title'] = str_replace('_', ' ', str_replace('Dest_', '', str_replace('Src_','',$field_name)));
        }
        $new_column['dt'] = $dt++;
        $uns_tbl_fields[] = $new_column;
    }

    return $uns_tbl_fields;
}



function cw_datahub_prepare_main_columns_popup($_columns) {
    global $tables;

    $dh_columns_config = array();
    $cfg_name = 'datahub_columns_popup_main';
    $cols_cfg = cw_query_first_cell("select value from $tables[config] where name='$cfg_name'");
    if (!empty($cols_cfg)) {
        $dh_columns_config = unserialize($cols_cfg);
    }


    $cols_hide_popup = array('cimageurl');

    $columns = array();
    $col_id = 0;
    foreach ($_columns as $col) {
        if (in_array($col['field'], $cols_hide_popup) || strpos($col['field'], '_Review')!==false || strpos($col['field'], '_Rating')!==false) continue;
        $col['dt'] = $col_id++;
        $col['db'] = $col['field'];

        if (isset($dh_columns_config[$col['db']])) 
            $col['popup_main_display'] = ($dh_columns_config[$col['db']] == 'true');
        else
            $col['popup_main_display'] = $col['main_display'];

        $columns[] = $col;
    }

    return $columns;
}


function cw_datahub_sel_item($table_id, $key, $linked_ids, $is_interim) {
    $interim_ext = '';
    if ($is_interim)
        $interim_ext = 'interim_';

    $buffer_match_pre_save = cw_dh_session_register($interim_ext.'buffer_match_pre_save', array());

    $result = '';
    if (isset($buffer_match_pre_save[$table_id])) {
        if ($buffer_match_pre_save[$table_id] == $key) 
            $result = " checked ";
    } else {
        if ($key == 0) {
            $result = " checked "; 
        } 
    }
    return $result;
}

function cw_datahub_man_sel_item($table_id, $is_interim, $get_text = false) {
    global $tables;

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = 'interim_';

    $preview_columns = cw_datahub_main_table_filter_fields('buffer_match_preview');
//array('ID','Producer','name','Vintage','size','country','Region','Appellation','sub_appellation');

    $manual_sel_items = cw_dh_session_register($interim_ext.'manual_sel_items', array());
    $result = '';

    if (isset($manual_sel_items[$table_id])) {
        if (!$get_text) {
            $result = $manual_sel_items[$table_id]; 
        } else {
            $_match_item = cw_query_first("SELECT `".implode("`,`", $preview_columns)."` from $tables[datahub_main_data] WHERE ID = '".$manual_sel_items[$table_id]."'");
            $match_item = array();  
            foreach ($_match_item as $mi) {
                $mi = trim($mi);  
                if (!empty($mi)) 
                    $match_item[] = $mi;
            } 
            $result = '#'.implode(' ', $match_item); 
        } 
    }     

    return $result;
}

function cw_datahub_match_items_preview($match_ids, $preview_columns) {
    global $tables;
    $match_data  = cw_query_hash("SELECT `".implode("`,`", $preview_columns)."` from $tables[datahub_main_data] WHERE ID IN ('".implode("', '",$match_ids)."')", 'ID', false);
    $result_arr = array();
    foreach ($match_ids as $match_id) {
        if (!empty($match_data[$match_id])) 
             $result_arr[] = '#' . $match_id . ' ' . implode(' ',$match_data[$match_id]);
    }
    return implode('<br />', $result_arr);
}

function cw_datahub_get_preview_columns($item_xref) {
    global $config;

    $preview_columns = cw_datahub_main_table_filter_fields('buffer_match_preview');

    $preview_columns = array('ID','Producer','name','Vintage','Size','country', 'Region', 'Appellation', 'price', 'cost');

    if (!empty($config['flexible_import']['fi_datahub_main_columns_preview_on_buffer'])) {
        $columns_prev_packed = explode("/n", $config['flexible_import']['fi_datahub_main_columns_preview_on_buffer']);  
        foreach ($columns_prev_packed as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $line_parts = explode(";", $line);
            if (!empty($line_parts[0]) && !empty($line_parts[1])) {
                if (strpos($item_xref, $line_parts[0]) !== false) {
                    $preview_columns = explode(',',$line_parts[1]); 
                } 
            }  
        }
    } 
    return $preview_columns;
}

function cw_datahub_max_display_matches() {
    global $config;

    $max_display_matches = $config['flexible_import']['match_items_default_count'];

    $max_display_matches_current = &cw_session_register('max_display_matches_current',0);
    if ($max_display_matches_current)
        $max_display_matches = $max_display_matches_current;

    return $max_display_matches;
}

function cw_datahub_color_active_item($item_id) {
    static $active_catalogids;

    if (!$item_id) return '';

    if (empty($active_catalogids)) {
        $active_catalogids = cw_query_column("select catalogid from xfer_products_SWE where hide='0'", 'catalogid');
    }
    
    $colors = array(0=>'red', 1=>'green');

    return "style='color:".$colors[in_array($item_id, $active_catalogids)]."'";
}


function cw_datahub_prepare_display_buffer($sent_data, $column_ids, $is_interim=false) {

    global $tables;

    $match_items_column_id = $column_ids['Match Items'];
    $image_column_id = $column_ids['image'];

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = 'interim_';

    $max_display_matches = cw_datahub_max_display_matches(); 

    $std_item_options = array('0'=>'None');

    if (!$is_interim) {
        $std_item_options[-1] = 'New';
        $std_item_options[-2] = 'BlackList';
    }

    $buffer_search = &cw_session_register($interim_ext.'buffer_search', array());
    if ($buffer_search['map'] == 'blacklist') {
        $std_item_options = array('0'=>'None', '-3' => 'Remove from BlackList');
    }

    if (is_array($sent_data['data'])) {
        foreach ($sent_data['data'] as $row_id => $row_vals) {
            $preview_columns = cw_datahub_get_preview_columns($row_vals[1]);

            foreach($row_vals as $rv_k=>$rv_v) 
                $row_vals[$rv_k] = utf8_encode($rv_v);

            $table_id = $row_vals[0];
            $buff_table = $tables['datahub_'.$interim_ext.'import_buffer'];
            $linked_ids = cw_query_column("select dhml.item_id from item_xref dhml inner join $buff_table dhib on dhib.item_xref = dhml.xref and dhib.table_id='$table_id' inner join $tables[datahub_main_data] dhmd on dhmd.ID=dhml.item_id", "item_id");

            $non_stock_link = false;
            if (empty($linked_ids)) {
                $linked_ids = cw_query_column("select dhml.catalog_id from cw_datahub_match_links dhml inner join $buff_table dhib on dhib.item_xref = dhml.item_xref and dhib.table_id='$table_id' inner join $tables[datahub_main_data] dhmd on dhmd.ID=dhml.catalog_id", "catalog_id");
                if (!empty($linked_ids)) 
                    $non_stock_link = true;
            }


            $std_options_html = array();
            foreach ($std_item_options as $sio_k => $sio_title) {
                if ($sio_k == 0 && !empty($linked_ids)) {
                    $sio_title = "Keep mapped to <b><span class='lng_tooltip' title=\"".cw_datahub_match_items_preview($linked_ids, $preview_columns)."\">#".implode(", #",$linked_ids)."</span>".($non_stock_link?'<span title="Non-stock data link">*</span>':'')."</b>";
                } 
                $std_options_html[] = "<nobr><input id='matchassign_".$table_id."_$sio_k' type='radio' name='match_assign_$table_id' value=$sio_k onclick='javascript: dh_save_match($table_id, $sio_k);' ".cw_datahub_sel_item($table_id, $sio_k, $linked_ids, $is_interim)."><label class='narrow' ".cw_datahub_color_active_item($sio_k)." for='matchassign_".$table_id."_$sio_k'>&nbsp;$sio_title&nbsp;</label></nobr>&nbsp;";
            }
            $match_items_display_array = array(implode('&nbsp;', $std_options_html));

            if ($buffer_search['map'] != 'blacklist') {
                if (!empty($row_vals[$match_items_column_id])) {
                    $match_ids = explode(",", $row_vals[$match_items_column_id]);
                    $match_assign_items_array = array();
                    foreach ($match_ids as $match_k => $match_id) {
                                 
                        $mi_string = cw_datahub_match_items_preview(array($match_id), $preview_columns); 

                        if (isset($max_display_matches))
                            if ($match_k >= $max_display_matches) continue;   
  
                        $mi_string = str_replace("#$match_id", "<b>#$match_id</b>", $mi_string);

                        $match_assign_items_array[] = "<nobr><input id='matchassign_".$table_id."_".$match_id."' type='radio' name='match_assign_$table_id' value='$match_id' onclick='javascript: dh_save_match($table_id, $match_id);' ".cw_datahub_sel_item($table_id, $match_id, $linked_ids, $is_interim)."><label ".cw_datahub_color_active_item($match_id)." for='matchassign_".$table_id."_".$match_id."'>$mi_string</label></nobr>";
                    }   

                    if (!empty($match_assign_items_array)) {
                        $match_assign_items = "<div id='match_items_list_$table_id' style='height:124px;overflow-y:scroll;min-width:95%;width:95%;margin-bottom:-15px'>".implode("<br />", $match_assign_items_array)."</div>"; 
                        $match_items_display_array[] = $match_assign_items;
                    }  
                }

                $manual_sel_k = 9999999;

                $match_items_display_array[] = "<nobr><input id='matchassign_".$table_id."_$manual_sel_k' type='radio' name='match_assign_$table_id' value='$manual_sel_k' onclick=\"javascript: dh_item_select_popup($table_id);\" ".cw_datahub_sel_item($table_id, $manual_sel_k, $linked_ids, $is_interim)."><label ".cw_datahub_color_active_item($manual_sel_k)." for='matchassign_".$table_id."_$manual_sel_k'><input type='text' size=7 class='dh_manual_select_item' id='manual_select_text_$table_id' value='".cw_datahub_man_sel_item($table_id, $is_interim, 1)."' readonly='readonly' onclick='javascript: dh_item_select_popup($table_id);' /></label></nobr>";
            }

            $row_vals[$match_items_column_id] = implode('<br />',$match_items_display_array);

            if (!empty($row_vals[$image_column_id])) {
                $image_url = str_replace('https://', 'http://',$row_vals[$image_column_id]);
                $row_vals[$image_column_id] = "<div class='import_buffer_image_box'><a class='import_buffer_image_preview' onclick='javascript: popup_import_buffer_image_preview(this)'><img class='import_buffer_image_tmbn' src='$image_url' alt=''  title='Click to popup' /></a></div>";
//                $row_vals[$image_column_id] = "<div class='import_buffer_image_box'><a class='import_buffer_image_preview'><img class='import_buffer_image_tmbn' src='$image_url' alt=''  title='Click to popup' /></a></div>";
            } 

            if (!empty($row_vals[$column_ids['competitor_site']])) {
                $orig_site_url = $row_vals[$column_ids['competitor_site']];
                if (strpos($row_vals[$column_ids['competitor_site']], 'http')===false)
                    $row_vals[$column_ids['competitor_site']] = 'http://'.$row_vals[$column_ids['competitor_site']];
                $row_vals[$column_ids['competitor_site']] = "<a target='_blank' href='".$row_vals[$column_ids['competitor_site']]."'>$orig_site_url</a>"; 
            }

            $sent_data['data'][$row_id] = $row_vals;  
        } 
    }

    return $sent_data;
}

function cw_datahub_prepare_buffer_matches($debug) {
    global $tables; 

    $table_ids2update = cw_query_column($s = "select table_id from $tables[datahub_import_buffer] where `Match Items` = ''",'table_id');
    foreach ($table_ids2update as $table_id) {
        $random_ids = cw_query_column("select ID from $tables[datahub_main_data] order by rand() limit 5");
        cw_array2update('datahub_import_buffer',array('Match Items' => implode(',',$random_ids)),"table_id='$table_id'");
        if ($debug) {
            echo "table_id: $table_id, Match Items: ".implode(',',$random_ids)." <br />";  
        }
    }
}

function cw_datahub_sync_pos_to_main() {
    global $tables;

    $pos_config = cw_query_hash("select * from $tables[datahub_pos_update_config]", 'mfield', false);

    foreach ($pos_config as $mfield => $pu_cfg) {
        if (!empty($pu_cfg['custom_sql']))
            $pos_config[$mfield]['custom_sql'] = base64_decode($pu_cfg['custom_sql']);

        if ($pu_cfg['update_cond'] == 'E') 
            $extra_cond = " AND dmd.`$mfield`='' AND dp.`$pu_cfg[pfield]` != ''";
        else
            $extra_cond = ''; 

        cw_csvxc_logged_query("UPDATE $tables[datahub_main_data] dmd, $tables[datahub_pos] dp SET dmd.`$mfield`=dp.`$pu_cfg[pfield]` WHERE dmd.ID=dp.`Alternate Lookup` $extra_cond");

    }

}


function cw_datahub_add_buffer_to_main ($buffer_item_id, $remove_added = false) {
    global $tables;

    $copy_cfg = cw_query_hash("select * from $tables[datahub_buffer_match_config]", 'mfield', false);

    $insert_keys = array();
    $insert_vals = array();

    $next_ID = cw_query_first_cell("SELECT max(ID) + 1 FROM $tables[datahub_main_data]");
    if (empty($next_ID)) return 0;

    $insert_keys[] = 'ID';
    $insert_vals[] = $next_ID;

    foreach ($copy_cfg as $mfield => $bfield_data) {

        $insert_keys[] = '`'.$mfield.'`';
        if (!empty($bfield_data['custom_sql'])) 
            $insert_vals[] = base64_decode($bfield_data['custom_sql']);
        else
            $insert_vals[] = '`'.$bfield_data['bfield'].'`';   
    }

    $insert_query = "INSERT INTO $tables[datahub_main_data] (".implode(', ',$insert_keys).") SELECT ".implode(', ',$insert_vals)." FROM $tables[datahub_import_buffer] WHERE table_id='$buffer_item_id'";

    db_query($insert_query);
    
    $insert_id = cw_query_first_cell("SELECT ID FROM $tables[datahub_main_data] WHERE ID = '$next_ID'");

    if ($insert_id) {

        db_query("update cw_datahub_main_data inner join cw_datahub_BevAccessFeeds b on b.xref=initial_xref and cw_datahub_main_data.supplier_id=0 and cw_datahub_main_data.ID='$insert_id' set Source='Feed_BEVA'");
        db_query("update cw_datahub_main_data inner join cw_datahub_BevAccessFeeds b on b.xref=initial_xref and cw_datahub_main_data.supplier_id=0 and cw_datahub_main_data.ID='$insert_id' set cw_datahub_main_data.wholesaler=b.wholesaler");

        db_query("update cw_datahub_main_data set supplier_id = 
             IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'COLONY', '88', 
               IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'LAUBER', '11',            
                 IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'MSCOTT', '24',                             
                   IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'OPICI', '83',                                       
                     IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'SWS', '63',                                           
                       IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'WINBOW', '13',                                    
                         IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'BNP', '19',                        
                           IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'BOWL', '150', 
                             IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'SKUR' OR SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'SKURN', '103',
                               IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'TOUTON', '27',                                             
                                 IF(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'DOMAIN', '31',                                        
                                   supplier_id                           
                                   )                                
                                 )                                                                                                     
                               )                                                   
                             )         
                           )
                         )                                    
                       )                                                                             
                     )               
                   ) 
                 )                          
               ) WHERE source = 'Feed_BEVA' and supplier_id=0 and wholesaler!='' and ID='$insert_id'");



        cw_datahub_save_match_link($buffer_item_id, $insert_id, true); 
        $added_item_xref = cw_query_first_cell("select item_xref from $tables[datahub_import_buffer] where table_id='$buffer_item_id'"); 

        $buffer_item_image = cw_query_first("select * from $tables[datahub_import_buffer_images] where item_xref='$added_item_xref'");
        if (!empty($buffer_item_image)) {
            unset($buffer_item_image['item_xref']);
            unset($buffer_item_image['id']);   
            $buffer_item_image['item_id'] = $insert_id;
            db_query("delete from $tables[datahub_main_data_images] where item_id='$insert_id'");
            $image_id = cw_array2insert('datahub_main_data_images', $buffer_item_image);
            cw_array2update('datahub_main_data', array('cimageurl'=>$image_id), "ID='$insert_id'");
        }
    } 

    if ($insert_id && $remove_added) db_query("DELETE FROM $tables[datahub_import_buffer] WHERE table_id='$buffer_item_id'"); 

    return $insert_id;
}

function cw_datahub_save_update_nonstock_match_link($table_id, $ID, $is_interim=false) {

    if ($is_interim)
        $interim_ext = "interim_";

    global $tables;

    if ($ID == 9999999) {
        $manual_sel_items = cw_dh_session_register($interim_ext.'manual_sel_items', array());
        $ID = $manual_sel_items[$table_id];

//        unset($manual_sel_items[$table_id]);
//        cw_dh_session_save($interim_ext.'manual_sel_items', $manual_sel_items);
    }

    $item_xref = cw_query_first_cell("SELECT item_xref FROM ".$tables['datahub_'.$interim_ext.'import_buffer']." WHERE table_id='$table_id'");
    db_query("DELETE FROM cw_datahub_match_links WHERE item_xref='$item_xref'");
    db_query("REPLACE INTO cw_datahub_match_links (catalog_id, item_xref) VALUES ('$ID', '$item_xref')");

    cw_datahub_update_buffer_items_likeness($item_xref, $ID);

    return $ID;
}

function cw_datahub_update_buffer_items_likeness($item_xref, $ID) {
    db_query("UPDATE cw_datahub_buffer_items_likeness SET is_selected=0 WHERE item_xref='$item_xref'");

    if (cw_query_first_cell("select count(*) from cw_datahub_buffer_items_likeness WHERE item_xref='$item_xref' AND item_id='$ID'"))
        db_query("UPDATE cw_datahub_buffer_items_likeness SET is_selected=1 WHERE item_xref='$item_xref' AND item_id='$ID'");
    else
        db_query("INSERT INTO cw_datahub_buffer_items_likeness (item_xref, item_id, likeness) VALUES ('$item_xref', '$ID', -1.00)");

}

function cw_datahub_save_match_link($table_id, $ID, $del_old = false) {
    global $tables;
    $item_xref = cw_query_first_cell("SELECT item_xref FROM $tables[datahub_import_buffer] WHERE table_id='$table_id'");

    $item_xref_data = cw_query_first("SELECT * FROM $tables[datahub_import_buffer] WHERE table_id='$table_id'");

    if (!empty($item_xref)) {
        if ($del_old) { 
            db_query("DELETE FROM item_xref WHERE xref='$item_xref'"); 
            //db_query("DELETE FROM cw_datahub_match_links WHERE item_xref='$item_xref'"); 
        } 

        if ($ID == 9999999) {
            $manual_sel_items = cw_dh_session_register('manual_sel_items', array());
            $ID = $manual_sel_items[$table_id];

            unset($manual_sel_items[$table_id]);
            cw_dh_session_save('manual_sel_items', $manual_sel_items);
        }  

        if (strtolower(substr($item_xref,0,4)) != 'swe-' || 1) { //never add swe- like xrefs to item_xref!
            db_query($s = "REPLACE INTO item_xref (item_id, xref, qty_avail, min_price, bot_per_case, cost_per_case, cost_per_bottle, split_case_charge, store_id, supplier_id) VALUES ('$ID', '$item_xref', '$item_xref_data[item_xref_qty_avail]', '$item_xref_data[item_xref_min_price]', '$item_xref_data[item_xref_bot_per_case]', '$item_xref_data[item_xref_cost_per_case]', '$item_xref_data[item_xref_cost_per_bottle]', '$item_xref_data[split_case_charge]','1', '$item_xref_data[supplier_id]')");
//        } else {
            //db_query("REPLACE INTO cw_datahub_match_links (catalog_id, item_xref) VALUES ('$ID', '$item_xref')");
        } 

        cw_datahub_update_buffer_items_likeness($item_xref, $ID);

        db_query("UPDATE $tables[datahub_main_data] SET initial_xref='$item_xref', supplier_id='$item_xref_data[supplier_id]', split_case_charge='$item_xref_data[split_case_charge]',cost='$item_xref_data[item_xref_cost_per_bottle]', cost_per_case='$item_xref_data[item_xref_cost_per_case]', stock='$item_xref_data[item_xref_qty_avail]', min_price='$item_xref_data[item_xref_min_price]', bot_per_case='$item_xref_data[item_xref_bot_per_case]' WHERE ID='$ID'"); 


    }

    return $ID;
}

function cw_datahub_update_linked_data($selected_table_ids, $is_interim=false) {

    cw_log_add(__FUNCTION__, array($selected_table_ids, $is_interim));

    global $tables, $config;
    $updated_items = 0;

    $interim_ext = $_interim_ext = '';
    if ($is_interim) {
        $interim_ext = 'interim_';
        $_interim_ext = '_interim';  
    }   

    $table_datahub_import_buffer = $tables['datahub_'.$interim_ext.'import_buffer'];

    $item_xref_tbl_name = 'item_xref';

    $ix_copy_table = "item_xref_buffer_feeds$_interim_ext";
    $ix_table_exists = cw_query_first_cell("show tables like '$ix_copy_table'");
    if ($ix_table_exists) {
        $item_xref_tbl_name = $ix_copy_table;
    }   



 //   $selected_table_ids = cw_query_column("select table_id from $tables[datahub_import_buffer] order by table_id desc limit 1",'table_id');

    if (!empty($selected_table_ids)) 
      $where_qry = " where ib.table_id in ('".implode("','",$selected_table_ids)."') ";

    $buffer_items = cw_query($s="select ib.*, md.ID, md.cost as linked_item_cost, md.stock as linked_item_stock from $table_datahub_import_buffer ib inner join $item_xref_tbl_name ix on ix.xref=ib.item_xref inner join $tables[datahub_main_data] md on md.ID=ix.item_id $where_qry limit 5000");

    print("<br>");
    $copy_cfg = cw_query_hash("select * from $tables[datahub_buffer_match_config]", 'mfield', false);
//print_r($buffer_items); print_r($copy_cfg);
    $logged_queries = array();

    foreach ($buffer_items as $b) {
        //update linked item_xref unconditionally

        $extra_cols_arr = array();
        for ($i=2; $i<=6; $i++) {
            $extra_cols_arr[] = "bot_qty$i = '".$b["bot_qty$i"]."'";
            $extra_cols_arr[] = "cost_per_case$i = '".$b["cost_per_case$i"]."'";
            $extra_cols_arr[] = "cost_per_bottle$i = '".$b["cost_per_bottle$i"]."'";
        }
        $extra_cols_qry = implode(', ', $extra_cols_arr);

        db_query("UPDATE $item_xref_tbl_name SET 
                    qty_avail = '$b[item_xref_qty_avail]', 
                    min_price = '$b[item_xref_min_price]',
                    bot_per_case = '$b[item_xref_bot_per_case]', 
                    cost_per_case = '$b[item_xref_cost_per_case]', 
                    cost_per_bottle = '$b[item_xref_cost_per_bottle]', 
                    split_case_charge = '$b[split_case_charge]', 
		    supplier_id = '$b[supplier_id]',

                    $extra_cols_qry 

                  WHERE xref='$b[item_xref]'"); 

        $copy_supplier_data_allowed = true;

        if ($b['qty_in_stock'] <= 0) 
            $copy_supplier_data_allowed = false;  
/*
        $linked_item_cost = cw_query_first_cell("SELECT cost FROM $tables[datahub_main_data] where ID = '$b[ID]'"); 
        $linked_item_stock = cw_query_first_cell("SELECT stock FROM $tables[datahub_main_data] where ID = '$b[ID]'"); 
*/
        if ($b['item_xref_cost_per_bottle'] >= $b['linked_item_cost'] && $b['linked_item_stock'] > 0)
            $copy_supplier_data_allowed = false;

        if ($copy_supplier_data_allowed) {
            foreach ($copy_cfg as $mfield => $bfield_data) {
                $check_not_minus_one = '';
                if (!empty($bfield_data['custom_sql']))
                    $src_field = base64_decode($bfield_data['custom_sql']);
                else {
                    $src_field = "$table_datahub_import_buffer.`" . $bfield_data['bfield']. "`";
                    $check_not_minus_one = " and $src_field != -1";
                }

                if ($bfield_data['update_cond'] == 'A') { 

                    db_query($q = "update $tables[datahub_main_data] md, $table_datahub_import_buffer set md.`$mfield`=$src_field where md.ID = '$b[ID]' and $table_datahub_import_buffer.table_id='$b[table_id]' $check_not_minus_one");
                    $logged_queries[] = $q;
                } elseif ($bfield_data['update_cond'] == 'E')  {
                    $dest_val = cw_query_first_cell("select `$mfield` from $tables[datahub_main_data] where ID = '$b[ID]'");
                    $logged_queries[] = "$mfield = $dest_val ".gettype($dest_val);

                    $src_val = cw_query_first_cell("select $src_field from $table_datahub_import_buffer where table_id = '$b[table_id]'"); 
                    if ($dest_val == '' && !empty($src_val)) {
                        $q = "update $tables[datahub_main_data] set `$mfield` = '".addslashes($src_val)."' where ID = '$b[ID]'";
                        db_query($q);
                        $logged_queries[] = $q;
                    } 
                }
            }
        }

        if (!empty($b['competitor_site'])) { 
            db_query($q = "REPLACE INTO $tables[datahub_competitor_prices] (itemid, url, price, date_updated) SELECT '$b[ID]', '$b[competitor_site]', '$b[competitor_price]', ".time());  
            $logged_queries[] = $q; 
        } 
        if ($copy_supplier_data_allowed || $config['flexible_import']['flex_import_delete_linked_failed']=='Y') {
            $q = "delete from $table_datahub_import_buffer where table_id = '$b[table_id]'";   
            cw_csvxc_logged_query($q);
            $logged_queries[] = $q;
        }

        $updated_items++;
    }

    if ($item_xref_tbl_name == $ix_copy_table) {
     
        $remained_items_count = cw_query_first_cell("select count(*) from $table_datahub_import_buffer ib inner join $item_xref_tbl_name ix on ix.xref=ib.item_xref inner join cw_datahub_main_data md on md.ID=ix.item_id");
        if (!$remained_items_count) {
            $extra_cols_arr = array();
            for ($i=2; $i<=6; $i++) {
                $extra_cols_arr[] = "ix.bot_qty$i = ixc.bot_qty$i";
                $extra_cols_arr[] = "ix.cost_per_case$i = ixc.cost_per_case$i";
                $extra_cols_arr[] = "ix.cost_per_bottle$i = ixc.cost_per_bottle$i";
            }
            $extra_cols_qry = implode(', ', $extra_cols_arr);
            cw_csvxc_logged_query("DELETE FROM $ix_copy_table WHERE feed_code=''");

            cw_csvxc_logged_query("UPDATE item_xref ix INNER JOIN $ix_copy_table ixc ON ix.xref=ixc.xref AND ixc.feed_code!=''
                    SET 
                    ix.qty_avail = ixc.qty_avail, 
                    ix.min_price = ixc.min_price,
                    ix.bot_per_case = ixc.bot_per_case, 
                    ix.cost_per_case = ixc.cost_per_case, 
                    ix.cost_per_bottle = ixc.cost_per_bottle, 
                    ix.split_case_charge = ixc.split_case_charge, 
                    ix.supplier_id = ixc.supplier_id,

                    $extra_cols_qry "); 

            cw_csvxc_logged_query("drop table $ix_copy_table"); 
        }  
    }

    return $updated_items;
}

function cw_datahub_get_blacklist_table() {
    global $tables;
    return $tables['datahub_import_buffer'].'_blacklist';
}

function cw_datahub_clean_buffer_by_blacklist($is_interim = false) {
    global $tables;

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = 'interim_';

    $buff_table = $tables['datahub_'.$interim_ext.'import_buffer'];

    $blacklist_table = cw_datahub_get_blacklist_table();
    cw_csvxc_logged_query("DROP TABLE IF EXISTS cw_datahub_blacklist_tmp");
    cw_csvxc_logged_query("CREATE TABLE cw_datahub_blacklist_tmp AS SELECT `$buff_table`.item_xref FROM `$buff_table` LEFT JOIN `$blacklist_table` ON `$buff_table`.item_xref=`$blacklist_table`.item_xref WHERE `$blacklist_table`.item_xref IS NOT NULL"); 

    $curr_items2bl = cw_query_first_cell("SELECT COUNT(*) FROM cw_datahub_blacklist_tmp");
    if ($curr_items2bl) { 
        //cw_csvxc_logged_query("DELETE $blacklist_table.* FROM $blacklist_table INNER JOIN cw_datahub_blacklist_tmp ON cw_datahub_blacklist_tmp.item_xref=$blacklist_table.item_xref");
        //cw_csvxc_logged_query("INSERT INTO $blacklist_table SELECT $buff_table.* FROM $buff_table INNER JOIN cw_datahub_blacklist_tmp ON cw_datahub_blacklist_tmp.item_xref=$buff_table.item_xref");
        $upd_cols = cw_csvxc_get_table_fields($buff_table);
       
        $upd_sql = array();   
        foreach ($upd_cols as $colname) {
            if ($colname == 'table_id')  continue;
            $upd_sql[] = "`$blacklist_table`.`$colname` = `$buff_table`.`$colname`"; 
        } 
 
        cw_csvxc_logged_query("UPDATE `$blacklist_table` INNER JOIN `$buff_table` ON `$buff_table`.item_xref=`$blacklist_table`.item_xref SET ".implode($upd_sql, ', '));

    }
    //cw_csvxc_logged_query("DROP TABLE IF EXISTS cw_datahub_blacklist_tmp");

    cw_csvxc_logged_query("DELETE `$buff_table`.* FROM `$buff_table` LEFT JOIN `$blacklist_table` ON `$buff_table`.item_xref=`$blacklist_table`.item_xref WHERE `$blacklist_table`.item_xref IS NOT NULL");

}


function cw_datahub_move_to_blacklist($buffer_item_id, $remove_added = false) {
    global $tables;

    $blacklist_tbl = cw_datahub_get_blacklist_table();
  
    db_query("create table if not exists $blacklist_tbl like $tables[datahub_import_buffer]");

    db_query($insq = "insert into $blacklist_tbl select * from $tables[datahub_import_buffer] where table_id = '$buffer_item_id'");

    $bl_table_id = db_insert_id();

    if ($remove_added) 
        db_query($delq = "delete from $tables[datahub_import_buffer] where table_id = '$buffer_item_id'");
   

    return $bl_table_id; 
}

function cw_datahub_restore_from_blacklist($blacklist_buffer_item_id, $remove_added = false) {
    global $tables;

    $blacklist_tbl = cw_datahub_get_blacklist_table();

    db_query("insert into $tables[datahub_import_buffer] select * from $blacklist_tbl where table_id = '$blacklist_buffer_item_id'");

    $table_id = cw_query_first_cell("select table_id from $tables[datahub_import_buffer] where table_id='$blacklist_buffer_item_id'"); 

    if ($remove_added)
        db_query("delete from $blacklist_tbl where table_id = '$blacklist_buffer_item_id'");

    return $table_id;
}

function cw_datahub_erase_blacklisted_items() {
    global $tables;

    $blacklist_tbl = cw_datahub_get_blacklist_table();

    $bl_table_ids = cw_query_column("select $tables[datahub_import_buffer].table_id from $tables[datahub_import_buffer] inner join $blacklist_tbl on $blacklist_tbl.item_xref=$tables[datahub_import_buffer].item_xref", 'table_id');
    db_query("delete from $tables[datahub_import_buffer] where table_id in ('".implode("','", $bl_table_ids)."')");


    return count($bl_table_ids);
}

function cw_datahub_load_hide_columns($cfg_area, $hidden_cols) {
    global $tables;

    $cfg_name = 'datahub_columns_'.$cfg_area;
    $cols_cfg = cw_query_first_cell("select value from $tables[config] where name='$cfg_name'");
    if (!empty($cols_cfg)) {
        $dh_columns_config = unserialize($cols_cfg);
        foreach ($dh_columns_config as $column => $flag) {
            if ($flag == 'true' && in_array($column, $hidden_cols)) {
                $k = array_search($column, $hidden_cols);
                unset($hidden_cols[$k]);
            } elseif ($flag == 'false' && !in_array($column, $hidden_cols)) { 
                $hidden_cols[] = $column;
            }     
        }
    }  

    return $hidden_cols;
}

function cw_datahub_import_buffer_query($src_buffer_table, $buffer_search, $select_fields=array()) {
    global $tables;

    $buffer_where = array(1);
    $buffer_join = array();

    if (!empty($buffer_search['map'])) {
        if ($buffer_search['map'] == 'not_mapped') {
            $buffer_join[] = " left join item_xref on $src_buffer_table.item_xref=item_xref.xref";
            $buffer_join[] = " left join $tables[datahub_match_links] on $src_buffer_table.item_xref=$tables[datahub_match_links].item_xref";
            $buffer_where[] = "(item_xref.item_id is null and $tables[datahub_match_links].catalog_id is null)";
        } elseif ($buffer_search['map'] == 'mapped') {
            $buffer_join[] = " left join item_xref on $src_buffer_table.item_xref=item_xref.xref";
            $buffer_join[] = " left join $tables[datahub_match_links] on $src_buffer_table.item_xref=$tables[datahub_match_links].item_xref";
            $buffer_where[] = "(item_xref.item_id is not null or $tables[datahub_match_links].catalog_id is not null)";
//                $buffer_join[] = " left join $tables[datahub_main_data] on $tables[datahub_match_links].catalog_id=$tables[datahub_main_data].ID";
//                $buffer_where[] = "$tables[datahub_main_data].ID is not null";
        } elseif ($buffer_search['map'] == 'new') {
            $buffer_match_pre_save = cw_dh_session_register('buffer_match_pre_save', array());
            $new_table_ids = array_keys($buffer_match_pre_save, -1);
            $buffer_where[] = "$src_buffer_table.table_id IN ('".implode("','", $new_table_ids)."')";
        } elseif ($buffer_search['map'] == 'blacklist') {
            $src_buffer_table = cw_datahub_get_blacklist_table();
            db_query("create table if not exists $src_buffer_table like $tables[datahub_import_buffer]");
        }
    }

    if (!empty($buffer_search['text_search'])) {
        $text_search = trim($buffer_search['text_search']);
        $search_words = explode(' ', $text_search);
        $search_columns = cw_datahub_buffer_table_filter_fields('no_text_search', false);
        $all_words_sql = array();
        foreach ($search_words as $word) {
            $word_sql = array();
            foreach ($search_columns as $col_name) {
                $word_sql[] = "$src_buffer_table.`$col_name` LIKE '%" . addslashes($word) . "%'";
            }
            $all_words_sql[] = implode(' OR ', $word_sql);
        }
        $buffer_where[] = '('.implode(') AND (', $all_words_sql).')';
    }
    if (!empty($buffer_search['source'])) {
        $buffer_where[] = "$src_buffer_table.`Source` = '$buffer_search[source]'";
    }

    $hide_columns = cw_datahub_load_hide_columns('buffer', array());

    if ($buffer_search['map'] == 'not_mapped' && !in_array('Max Likeness', $hide_columns)) { 
        $buffer_join[] = "left join $tables[datahub_buffer_items_likeness] on $tables[datahub_buffer_items_likeness].item_xref=`$src_buffer_table`.item_xref"; 
    }

    $buffer_where_sql = implode(' AND ', $buffer_where);
    $buffer_join_sql = implode(' ', $buffer_join);
 
    if (!empty($select_fields)) {

        $sel_fields_arr = array();
        foreach ($select_fields as $f_name) {
            $sel_fields_arr[] = "$src_buffer_table.`$f_name`";  
        } 
        $sel_fields = implode(", ", $sel_fields_arr);

    } else {
        $sel_fields = "$src_buffer_table.*"; 
    }

    if ($buffer_search['map'] == 'not_mapped') { 
        if (!in_array('Max Likeness', $hide_columns))
            $sel_fields .= ", IFNULL(max($tables[datahub_buffer_items_likeness].likeness),'-1.00') as `Max Likeness`";
        else 
            $sel_fields .= ", 0 as `Max Likeness`"; 
    } 

    return "SELECT $sel_fields FROM $src_buffer_table $buffer_join_sql WHERE $buffer_where_sql GROUP BY `$src_buffer_table`.item_xref";

}

function cw_datahub_import_buffer_view_table_name($is_interim = false) {
    global $tables;
    global $dh_buffer_table_fields;

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = 'interim_';

    $table = $tables['datahub_'.$interim_ext.'import_buffer'];

    $src_buffer_table = $tables['datahub_'.$interim_ext.'import_buffer'];

    //db_query("DELETE from $tables[datahub_match_links] WHERE catalog_id NOT IN (SELECT ID FROM $tables[datahub_main_data])");

    $def_buffer_search = array();
    if (!$is_interim) 
        $def_buffer_search = array('map'=>'not_mapped');

    $buffer_search = &cw_session_register($interim_ext.'buffer_search', $def_buffer_search);

    if (!empty($buffer_search)) {
        db_query("drop table if exists cw_view_datahub_".$interim_ext."import_buffer");
        db_query("drop view if exists cw_view_datahub_".$interim_ext."import_buffer");

        $filtered_query = cw_datahub_import_buffer_query($src_buffer_table, $buffer_search);
        db_query("drop table if exists cw_view_datahub_".$interim_ext."import_buffer");
        db_query("drop view if exists cw_view_datahub_".$interim_ext."import_buffer");
        db_query("create view cw_view_datahub_".$interim_ext."import_buffer as ($filtered_query)");

        // DB table to use
        $table = "cw_view_datahub_".$interim_ext."import_buffer";
    }

    return $table;
}


function cw_datahub_prepare_transfer($transfer_tbl, $is_test=false) {
    global $tables, $app_dir, $config;

    //create current fingerprint
    cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS cw_dh_fingerprint_current (ID int(11) not null default 0, fingerprint varchar(32), PRIMARY KEY (ID)) ");
    cw_csvxc_logged_query("truncate cw_dh_fingerprint_current");

    $main_tbl_fields = cw_check_field_names(array(), $tables['datahub_main_data']);

    cw_csvxc_logged_query("insert into cw_dh_fingerprint_current (ID, fingerprint) select ID, md5(concat(coalesce(".implode(",''), coalesce(", $main_tbl_fields).",''))) from $tables[datahub_main_data] WHERE ID=dup_catid OR dup_catid=0");

    cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS cw_dh_fingerprint_old (ID int(11) not null default 0, fingerprint varchar(32), PRIMARY KEY (ID))");

    cw_csvxc_logged_query("delete fpc from cw_dh_fingerprint_current fpc left join cw_dh_fingerprint_old fpo on fpo.ID = fpc.ID and fpo.fingerprint = fpc.fingerprint where fpo.ID IS NOT NULL");

    cw_csvxc_logged_query("drop table if exists $transfer_tbl");
    cw_csvxc_logged_query("create table if not exists $transfer_tbl like $tables[datahub_main_data]");
    //cw_csvxc_logged_query("truncate table $transfer_tbl");
    cw_csvxc_logged_query("insert into $transfer_tbl select dhmd.* from $tables[datahub_main_data] dhmd inner join cw_dh_fingerprint_current fpc on fpc.ID=dhmd.ID WHERE dhmd.ID=dhmd.dup_catid OR dhmd.dup_catid=0");

    cw_csvxc_logged_query("alter table $transfer_tbl add column cimageurl_path varchar(255) not null default 0");

    cw_csvxc_logged_query("update $transfer_tbl dmdt, $tables[datahub_main_data_images] dmdi set dmdt.cimageurl_path=REPLACE(dmdi.filename,'$app_dir','') where dmdt.cimageurl=dmdi.id");

    cw_csvxc_logged_query("update $transfer_tbl set hide = 1"); 
    cw_csvxc_logged_query("update $transfer_tbl s SET s.hide = '0', s.avail_code = '1' WHERE (s.avail_code = '0' and s.stock > 0)");
    cw_csvxc_logged_query("UPDATE $transfer_tbl s SET s.hide = '0', s.minimumquantity = Null WHERE (s.avail_code = '1' and s.stock > 0)");
    cw_csvxc_logged_query("UPDATE $transfer_tbl s SET s.hide = '0' WHERE s.avail_code IN ('2','3','4')");
    cw_csvxc_logged_query("UPDATE $transfer_tbl x SET x.hide = IF(Instr('".$config['flexible_import']['bottle_size_do_not_display']."', x.Size) OR COALESCE(x.Producer,'') = '' or COALESCE(x.name,'') = '', True, x.hide)");

    cw_csvxc_logged_query("UPDATE $transfer_tbl AS x 
                           INNER JOIN (SELECT Producer, name, Varietal, Size 
                               FROM $transfer_tbl 
                               WHERE (Trim(COALESCE(Vintage,'')) <> '' and hide = 0)
                           ) AS y 
                           ON (x.Producer = y.Producer) AND (x.name = y.name) AND (x.Varietal = y.Varietal) AND (x.Size = y.Size) 
                           SET x.hide = 1 
                           WHERE (isnull(x.Vintage) or Trim(x.Vintage) = '')");

    cw_csvxc_logged_query("UPDATE $transfer_tbl SET hide = 1 WHERE price < 3 or Cost = '0'");


    cw_csvxc_logged_query("UPDATE (select * from $tables[datahub_price_settings] where store_id = '1') AS s, $transfer_tbl AS x 
                           SET x.minimumquantity = IF(x.cost <= s.SWE_cost_threshold and x.avail_code = '2' and x.stock <= 0, SWE_min_qty_under_cost_threshold,Null)");

    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = IF(minimumquantity < 3, '3',minimumquantity) WHERE initial_xref LIKE '%DOMAIN%' AND stock<=3");
    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = IF(minimumquantity < 6, '6',minimumquantity) WHERE initial_xref LIKE '%BNP%' AND stock<=6");
    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = IF(minimumquantity < 1, '1',minimumquantity) WHERE initial_xref LIKE '%POLA-%' AND stock <= 3");
    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = IF(bot_per_case = 3 and stock= 0, '3', IF((bot_per_case = 6 OR bot_per_case = 24)  AND stock = 0, '6', IF(bot_per_case = 12 and stock = 0, '3', minimumquantity))) WHERE initial_xref LIKE '%ANGEL%'");

    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = '0' WHERE initial_xref LIKE '%WDMN%'");

    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = IF(bot_per_case < 12 and COALESCE(stock, 0) = 0, IF(COALESCE(bot_per_case,0) = 0,'12',bot_per_case), IF(bot_per_case >= 12 and COALESCE(stock, 0) = 0, '12', IF(COALESCE(minimumquantity,0) = 0,'12', minimumquantity))) WHERE initial_xref LIKE '%VISN%'");

    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = IF(bot_per_case < 6 and COALESCE(stock, 0) = 0, bot_per_case, IF(bot_per_case >= 6 and COALESCE(stock, 0) = 0, '6', minimumquantity)) WHERE initial_xref LIKE 'VS-%'");

    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = IF(stock > 0, '1', bot_per_case) WHERE initial_xref LIKE 'BEAR%'");

    cw_csvxc_logged_query("UPDATE $transfer_tbl SET minimumquantity = '0' WHERE initial_xref LIKE 'VERITY%'");

    cw_csvxc_logged_query("UPDATE $transfer_tbl x INNER JOIN cw_datahub_pos as p ON p.`Item Number` = x.store_sku
                           SET x.supplier_id = p.`Vendor Code`
                           WHERE coalesce(x.store_sku, '') <> ''
                           AND x.hide = 0 AND COALESCE(x.supplier_id, 0) = 0");

    cw_csvxc_logged_query("UPDATE $transfer_tbl x     
                             SET x.meta_description  =  
                                 CONCAT(
                                    'Buy ',
                                    IF(COALESCE(Producer, '') <> '', CONCAT(Producer, ' '),  ''),
                                    IF(COALESCE(name, '') <> '', CONCAT(name, ' '),  ''),
                                    IF(COALESCE(Vintage, '') <> '', CONCAT(Vintage, ' '),  ''), 
                                    IF(COALESCE(Size, '') <> '', CONCAT(Size, ' '),  ''),
                                    'from ',
                                    IF(COALESCE(`sub_appellation`, '') <> '', CONCAT(`sub_appellation`, ' '),  ''),     
                                    IF(COALESCE(Appellation, '') <> '', CONCAT(Appellation, ' '),  ''),         
                                    IF(COALESCE(Region, '') <> '', CONCAT(Region, ' '),  ''),           
                                    IF(COALESCE(Country, '') <> '', CONCAT(Country, ' '),  ''), 
                                    'at discount prices on www.saratogwine.com'
                                  )
                           WHERE x.hide = 0 AND x.meta_description=''");
/* should be already implemented
$sql = "UPDATE xfer_products_SWE as x
                                                INNER JOIN SWE_store_feed as s
                                                ON s.sku = x.sku
                                                SET x.cprice = IF(COALESCE(s.manual_price,0) > 0, s.manual_price, x.cprice)
                                                WHERE COALESCE(x.sku, '') <> ''";
*/
    cw_csvxc_logged_query("UPDATE $transfer_tbl SET cost_per_case = 0 WHERE price <= 0 OR twelve_bot_price <= 0");

    if ($is_test) 
        return; 

    //create fingerprint copy for next time
    cw_csvxc_logged_query("truncate cw_dh_fingerprint_old");
    cw_csvxc_logged_query("insert into cw_dh_fingerprint_old (ID, fingerprint) select ID, md5(concat(coalesce(".implode(",''), coalesce(", $main_tbl_fields).",''))) from $tables[datahub_main_data] WHERE ID=dup_catid OR dup_catid=0");

}

function cw_datahub_on_product_modify_end() {
    global $product_id;

global $attributes;
if (!empty($attributes['manual_status']) && $product_id) {
//print('<pre>');
//print_r($attributes['manual_status']);

    $selected_key = '';
    if ($attributes['manual_status']['value']) {
//print($attributes['manual_status']['value']);
        foreach ($attributes['manual_status']['default_value'] as $ms_def_option) {
            if ($ms_def_option['attribute_value_id'] == $attributes['manual_status']['value'])
                $selected_key = $ms_def_option['value_key'];
        }
    }
//print($selected_key);
    if ($selected_key == 'no') {
        cw_array2update('products', array('status'=>0), "product_id='$product_id'");
    }
//print('</pre>');

}


    $dh_product_saved_values = &cw_session_register('dh_product_saved_values', array());


    if (empty($dh_product_saved_values)) 
        return;

    cw_datahub_test_product_values(array($product_id));

}

function cw_datahub_get_product_value_by_name($product_id, $dh_field) {
    global $dh_product_to_hub;

    $field_def = array();
    foreach ($dh_product_to_hub as $_field_def) {
        if ($_field_def['dh_field'] == $dh_field) {
            $field_def = $_field_def;
        }   
    }

    if (!empty($field_def)) 
        return cw_datahub_get_product_value($product_id, $field_def, true);

    return '';
}

function cw_datahub_get_product_saved_value_by_name($product_id, $dh_field) {
    global $dh_product_to_hub;

    $field_id = 0; $field_def = array();
    $value = '';

    foreach ($dh_product_to_hub as $_field_id => $_field_def) {
        if ($_field_def['dh_field'] == $dh_field) {
            $field_id = $_field_id; $field_def = $_field_def;
        }
    }

    if (!$field_id)
        return $value;

    $dh_product_saved_values = &cw_session_register('dh_product_saved_values', array());
    if (isset($dh_product_saved_values[$product_id])) { 
        $value = $dh_product_saved_values[$product_id][$field_id];

        if ($field_def['cw_table'] == 'attributes_values') {
            $value = cw_datahub_get_product_attribute_value($product_id, $value, $field_def);
        }
    } 

    return $value; 
}

function cw_datahub_get_product_attribute_value($product_id, $value, $field_def) {
    global $tables;

    if ($field_def['dh_field'] == 'Producer') {
        $value = cw_query_first_cell("select manufacturer from $tables[manufacturers] where manufacturer_id='$value'");
    } else {
        $attribute_id = cw_query_first_cell("select attribute_id from $tables[attributes_values] where `$field_def[cw_id_field]`='$product_id' ".(!empty($field_def['cw_extra_cond'])?" AND $field_def[cw_extra_cond]":''));
        $attribute_value = cw_query_first("select * from $tables[attributes_default] where attribute_id='$attribute_id' AND attribute_value_id='".addslashes($value)."'");
        if (!empty($attribute_value))
            $value = $attribute_value['value'];
    }

    return $value;

}

function cw_datahub_get_product_value($product_id, $field_def, $real_value=false) {
    global $tables;
    $value = cw_query_first_cell("select `$field_def[cw_field]` from ".$tables[$field_def['cw_table']]." where `$field_def[cw_id_field]`='$product_id' ".(!empty($field_def['cw_extra_cond'])?" AND $field_def[cw_extra_cond]":''));

    if ($field_def['cw_table'] == 'attributes_values' && $real_value) {
        $value = cw_datahub_get_product_attribute_value($product_id, $value, $field_def);
    }

    return $value;
}

function cw_datahub_update_hub_data($product_id, $value, $field_def) {

    global $tables;

    if (cw_query_first_cell("select count(*) from $tables[datahub_main_data] WHERE ID='$product_id'") == 0)
        return;

    //convert passed value to real value used to update hub
    switch ($field_def['dh_field']) {
        case 'name': 
        //value 
        //cw_product = concat(Producer,' ',REPLACE(name,Producer,''),' ',Vintage,' ',Size)
            $producer = cw_datahub_get_product_value_by_name($product_id, 'Producer');
            $old_producer = cw_datahub_get_product_saved_value_by_name($product_id, 'Producer'); 

            $vintage = cw_datahub_get_product_value_by_name($product_id, 'Vintage');
            $old_vintage = cw_datahub_get_product_saved_value_by_name($product_id, 'Vintage'); 

            $size = cw_datahub_get_product_value_by_name($product_id, 'Size');
            $old_size = cw_datahub_get_product_saved_value_by_name($product_id, 'Size');
 
            $value = trim(str_replace(array($producer, $old_producer, $vintage, $old_vintage, $size, $old_size), '', $value));
        break; 

        case 'cimageurl':
             $value = cw_datahub_get_moved_dh_image($product_id, $field_def);
        break;

        default:
            if ($field_def['cw_table'] == 'attributes_values') {
                $value = cw_datahub_get_product_attribute_value($product_id, $value, $field_def); 
            }
    }     

    db_query("UPDATE $tables[datahub_main_data] SET `$field_def[dh_field]`='".addslashes($value)."' WHERE ID='$product_id'");

}

function cw_datahub_get_moved_dh_image($product_id, $field_def) {
    global $tables;

    $new_hub_image_record = 0;

    global $app_dir;
    $app_path = $app_dir.'/';

    $cw_image_info = cw_query_first("select * from ".$tables[$field_def['cw_table']]." where `$field_def[cw_id_field]`='$product_id' ".(!empty($field_def['cw_extra_cond'])?" AND $field_def[cw_extra_cond]":''));

    if (empty($cw_image_info) || !file_exists($app_path.$cw_image_info['image_path'])) 
        return $new_hub_image_record; 

    $hub_images_path = 'images/';

    $hub_img_path = $app_path.$hub_images_path.$cw_image_info['filename']; 
    $safety_cnt = 50;
    while (file_exists($hub_img_path) && $safety_cnt > 0) { 
        $hub_img_path = $app_path.$hub_images_path.$product_id.'_'.rand(1,100).'_'.$cw_image_info['filename'];
        $safety_cnt--;
    }
   
    if (copy($app_path.$cw_image_info['image_path'], $hub_img_path)) {
        $short_hub_img_path = str_replace($app_path, '', $hub_img_path);
        db_query("delete from $tables[datahub_main_data_images] where item_id='$product_id'");
        $new_hub_image_record = cw_array2insert('datahub_main_data_images', array('filename'=>$short_hub_img_path, 'filesize'=>0, 'web_path'=>$short_hub_img_path, 'system_path'=>$short_hub_img_path, 'item_id'=>$product_id)); 
    }

    return $new_hub_image_record;

}

function cw_datahub_test_product_values($product_ids) {

    global $dh_product_to_hub;

    $dh_product_saved_values = &cw_session_register('dh_product_saved_values', array());

    foreach($product_ids as $product_id) {
        if (isset($dh_product_saved_values[$product_id])) {
            $saved_product_data = $dh_product_saved_values[$product_id];
            foreach ($saved_product_data as $field_id => $value) {
                $new_value = cw_datahub_get_product_value($product_id, $dh_product_to_hub[$field_id]);              
                if ($new_value != $value) {
                    cw_datahub_update_hub_data($product_id, $new_value, $dh_product_to_hub[$field_id]);
                } 
            }
        }  
    }  

    foreach ($product_ids as $product_id)  
        unset($dh_product_saved_values[$product_id]);

}


function cw_datahub_save_product_values($product_ids) {
    global $dh_product_to_hub;

    $dh_product_saved_values = &cw_session_register('dh_product_saved_values', array());

    foreach ($product_ids as $product_id) {
        $saved_product_data = array();
        foreach ($dh_product_to_hub as $field_id => $field_def) {
            $saved_product_data[$field_id] = cw_datahub_get_product_value($product_id, $field_def);
        }
        $dh_product_saved_values[$product_id] = $saved_product_data;
    } 
}

function cw_datahub_exportMysqlToXls($table,$filename = 'export.xls', $sql) {
    $skip_index = array(7, 18, 24);//we're skipping Regular Price, Qty 1 and Custom Field 4 
    $result = cw_query("SHOW COLUMNS FROM ".$table);
    $csv_output = '';
    /* Fetch The Attributes of Each Field In Table */

    $i = 0;
    if (count($result) > 0) {
        while ($row = $result[$i]) {
            if(!in_array($i, $skip_index)) {
                $csv_output .= $row['Field']."\t";
            }
            $i++;
        }
    }

    $csv_output .= "\n";

    /* Select All Fields Within Table and Print Them */

    $values = cw_query($sql);
    $q_idx = 0;
    while ($row = $values[$q_idx]) {
        $j = 0;
        foreach ($row as $f_name => $f_value) {   
        /**
         * @todo change using a hard coded number for the final field
         */

            if(!in_array($j, $skip_index)) {
                if($j == 25) {
                    $pos = strpos($row[$f_name], '-');
                    if ($pos !== false) {
                        $row[$f_name] = preg_replace('/-/', '- ', $row[$f_name], 1);//add a space after the first -
                    }
                }
                $csv_output .= $row[$f_name]."\t";
            }
            $j++;
        }
        $csv_output .= "\n";
        $q_idx++;
    }
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    header("Content-type: application/x-msexcel");
    header("Content-Length: " . strlen($csv_output));
    header("Content-disposition: attachment; filename=".$filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $csv_output;
    return;
}
/**
 * Update native hub config table by settings from our store at admin/index.php?target=datahub_configuration
 * 
 * @global type $config
 */

function cw_datahub_filltemp_hub_config() {
    $hc_sql = "
CREATE TABLE IF NOT EXISTS `hub_config` (
  `display_feed_items_SWE` tinyint(1) DEFAULT '0',
  `display_feed_items_DWB` tinyint(1) DEFAULT '0',
  `bottle_size_do_not_display` varchar(90) DEFAULT NULL,
  `BevA_wholesalers_exclude` varchar(50) DEFAULT NULL,
  `BevA_wholesalers_always_on` varchar(50) DEFAULT NULL,
  `BevA_do_not_import` varchar(50) DEFAULT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";
   cw_csvxc_logged_query($hc_sql);

   global $config;

   $replace_config = array(
        'display_feed_items_SWE' => 1,
        'display_feed_items_DWB' => 1,
        'bottle_size_do_not_display' => $config['flexible_import']['bottle_size_do_not_display'],
        'BevA_wholesalers_exclude' => $config['flexible_import']['BevA_wholesalers_exclude'],
        'BevA_wholesalers_always_on' => $config['flexible_import']['BevA_wholesalers_always_on'],
        'BevA_do_not_import' => $config['flexible_import']['BevA_do_not_import'],
        'store_id' => 1 
   );
   cw_csvxc_logged_query("REPLACE INTO hub_config (`".implode("`, `",array_keys($replace_config))."`) VALUES ('".implode("', '",$replace_config)."')");  

}

/**
 * Fill native HUB table feeds_item_compare by our data from main table cw_datahub_main_data. So internal logic of HUB can still work as supposed
 * 
 * @global type $tables
 */
function cw_datahub_filltemp_feeds_item_compare() {
global $tables;

$dhmd2fic = array(
'Source' => 'Source',
'Wine' => "''",
'wholesaler' => 'wholesaler',
'Producer' => 'Producer',
'Name' => 'name',
'Vintage' => 'Vintage',
'size' => 'Size',
'block' => 0,
'add_to_hub' => 0, 
'xref' => 'initial_xref',
'catalogid' => 'catalog_id',
'dup_catid' => 'dup_catid',
'bottles_per_case' => 'bot_per_case',
'cost' => 'cost',
'country' => 'country',
'Region' => 'Region',
'varietal' => 'varietal',
'Appellation' => 'Appellation',
'sub-appellation' => 'sub_appellation',
'Parker_rating' => 'RP_Rating',
'Parker_review' => 'RP_Review',
'Spectator_rating' => 'W_S_Rating',
'Spectator_review' => 'W_S_Review',
'Tanzer_rating' => 'ST_Rating',
'Tanzer_review' => 'ST_Review',
'W&S_rating' => 'W_S_Rating',
'W&S_review' => 'W_S_Review',
'Description' => 'LongDesc',
'store_id' => '1',
'qty_in_stock' => 'stock',
'supplier_id' => 'supplier_id',
'dhmd_id' => 'ID'
);

db_query("update cw_datahub_main_data set Source='Feed_Touton' where initial_xref like 'TOUT%'");
db_query("update cw_datahub_main_data set Source='Feed_Opici' where initial_xref like 'CWOPC%'");
db_query("update cw_datahub_main_data set Source='Feed_BEVA' WHERE CAST(Left(coalesce(initial_xref,'xxx'),3) as SIGNED) > 0");
db_query("update cw_datahub_main_data set Source='Feed_Verity' where initial_xref like 'VERITY%'");
db_query("update cw_datahub_main_data set Source='Feed_Wildman' where initial_xref like 'WDMN%'");
db_query("update cw_datahub_main_data set Source='Feed_Polaner' where initial_xref like 'POLA%'");
db_query("update cw_datahub_main_data set Source='Feed_Vias' where initial_xref like 'CWVIAS%'");

db_query("DELETE FROM feeds_item_compare");

db_query("INSERT INTO feeds_item_compare (`".implode("`, `", array_keys($dhmd2fic))."`) SELECT ".implode(", ", $dhmd2fic)." FROM $tables[datahub_main_data]");
db_query("UPDATE feeds_item_compare SET Source='Hub' WHERE catalogid!=0");
db_query("UPDATE feeds_item_compare SET add_to_hub=1 WHERE catalogid=0");

}

function cw_datahub_filltemp_item_xref($clean_item_xref = true) {
    global $tables;
/*
    $dhmd2ix = array(
        'item_id' => 'catalog_id',
        'xref' => 'initial_xref',
        'qty_avail' => 'stock',
        'min_price' => 'min_price',
        'bot_per_case' => 'bot_per_case',
        'cost_per_case' => 'cost_per_case',
        'cost_per_bottle' => 'cost',
        'split_case_charge' => 'split_case_charge',
        'store_id' => '1',
        'supplier_id' => 'supplier_id'
    ); 

    if ($clean_item_xref)
        cw_csvxc_logged_query("DELETE FROM item_xref");

    cw_csvxc_logged_query("INSERT IGNORE INTO item_xref (`".implode("`, `", array_keys($dhmd2ix))."`) SELECT ".implode(", ", $dhmd2ix)." FROM $tables[datahub_main_data] WHERE catalog_id!=0");
*/
}

function cw_datahub_filltemp_price_params() {

}

function cw_dh_session_register($var_name, $def_value = array()) {
    global $tables;

    $dh_config = $def_value;
    $_cfg = cw_query_first_cell("select value from $tables[config] where name='$var_name'");
    if (!empty($_cfg)) {
        $dh_config = unserialize($_cfg);
    }

    return $dh_config;
}

function cw_dh_session_save($var_name, $value) {
    global $tables;

    cw_array2insert('config', array('name'=>$var_name, 'value'=>serialize($value)), true);
}


function sw_save_hub_image($type, $image_data) {

    if (!in_array($type, array('products_images_det', 'products_images_thumb'))) {
        cw_log_add('sw_save_hub_image', "ERROR: BAD TYPE $type");
        return;
    }

    if (!file_exists($image_data['image_path'])) {
        cw_log_add('sw_save_hub_image', "ERROR: uploaded file not found ".$image_data['image_path']);
        return;
    }

    global $app_dir, $tables;
    $image_path_info = pathinfo($image_data['image_path']);

    $hub_image_filename = $image_path_info['basename'];
    $hub_image_path = $app_dir.'/images/'.$hub_image_filename;

    $cnt = 1;
    while (file_exists($hub_image_path) && $cnt < 10000) {
        $hub_image_filename = $image_path_info['filename'].'-'.$cnt.'.'.$image_path_info['extension'];
        $hub_image_path = $app_dir.'/images/'.$hub_image_filename;
        $cnt++;
    } 

    @copy($image_data['image_path'], $hub_image_path);
    if (!file_exists($hub_image_path)) {
        cw_log_add('sw_save_hub_image', array("ERROR: cant save file for new hub item image", $image_data, $hub_image_path)); 
        return;
    }

    db_query("UPDATE item SET cimageurl = 'images/$hub_image_filename', extendedimage = 'images/$hub_image_filename' WHERE ID='$image_data[id]'");
    db_query("DELETE dh_mdi.* FROM cw_datahub_main_data_images dh_mdi INNER JOIN cw_datahub_main_data dh_md ON dh_mdi.id=dh_md.cimageurl AND dh_md.catalog_id='$image_data[id]'");
    db_query("DELETE dh_mdi.* FROM cw_datahub_main_data_images dh_mdi INNER JOIN cw_datahub_main_data dh_md ON dh_mdi.item_id=dh_md.id AND dh_md.catalog_id='$image_data[id]'");
    $dh_mdid = cw_query_first_cell("SELECT ID FROM cw_datahub_main_data WHERE catalog_id='$image_data[id]'");
    if (!$dh_mdid) {
        cw_log_add('sw_save_hub_image', "ERROR: hub item catalog_id=$image_data[id] is not found");
        return;
    } 

    db_query("INSERT INTO cw_datahub_main_data_images (filename, filesize, web_path, system_path, item_id) VALUES ('images/$hub_image_filename', '$image_data[image_size]', 'images/$hub_image_filename', 'images/$hub_image_filename', '$dh_mdid')");
    $new_image_id = db_insert_id();
    //$new_image_id = cw_query_first_cell("select id from cw_datahub_main_data_images where item_id='$dh_mdid' order by id desc limit 1");
    if (!$new_image_id) {
        cw_log_add('sw_save_hub_image', array("ERROR: cant save db entry for new hub item image", $image_data, $dh_mdid, $hub_image_filename));
        return; 
    }

    db_query("UPDATE cw_datahub_main_data SET cimageurl='$new_image_id' WHERE ID='$dh_mdid' AND catalog_id='$image_data[id]'");
    

}


function cw_datahub_get_buffer_sources($is_interim) {

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = 'interim_'; 

    $sources = cw_query_column("select distinct Source from cw_datahub_".$interim_ext."import_buffer order by Source");
    $result = array();
    foreach ($sources as $scode) {
        $result[] = array('code'=>$scode, 'name'=>str_replace('Feed_','',$scode));  
    }

    return $result;
}

function cw_datahub_vias_load($new_only, $is_interim=true) {
    global $tables;

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = '_interim';


    $search_prefilled = array();

    $search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
    $search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
    $search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 1000);
    $search_prefilled['page']           = ($page ? $page : 1);
    $search_prefilled['unserialize_fields'] = true;

    $all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

    $vias_prof = array('weekly'=>array(), 'monthly'=>array());

    foreach ($vias_prof as $period=>$vp_v) {

        $_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles, $tables['datahub_vias_'.$period]));

        $vias_prof[$period] = reset($_profiles);
        if (empty($vias_prof[$period])) {
            //cw_csvxc_logged_query('', "Error: vias $period profile is not found!"); 
            return 0;
        }

        $vias_prof[$period]['recurring_import_path'] = cw_flexible_import_find_server_file($vias_prof[$period]['recurring_import_path'], $interim_ext);

        if (!file_exists($vias_prof[$period]['recurring_import_path'])) {
            //cw_csvxc_logged_query('', "Error: vias $period file ".$vias_prof[$period]['recurring_import_path']." is not found!");
            return 0;
        }

    }

    if ($new_only) {
        $all_files_are_loaded_before = true;

        foreach ($vias_prof as $period=>$vp_v) {
            $vias_prof[$period]['file_hash'] = md5_file($vias_prof[$period]['recurring_import_path']);
            $is_file_loaded_already = cw_query_first_cell("SELECT COUNT(*) FROM $tables[flexible_import_loaded_files_hash] WHERE profile_id='".$vias_prof[$period]['id']."' AND hash='".$vias_prof[$period]['file_hash']."'");

            if (!$is_file_loaded_already)
                $all_files_are_loaded_before = false;

        }
        if ($all_files_are_loaded_before) {
            //cw_csvxc_logged_query('', "Warning: there are no new Vias files to load");
            return 0;
        }
    }

    foreach ($vias_prof as $period=>$vp_v) {
        cw_csvxc_logged_query("delete from ".$tables['datahub_vias_'.$period]);
        cw_flexible_import_run_profile($vp_v['id'], array($vp_v['recurring_import_path']));

        if (empty($vp_v['file_hash']))
            $vp_v['file_hash'] = md5_file($vp_v['recurring_import_path']);

        cw_array2update("flexible_import_profiles", array('recurring_last_run_date'=>time()), "id='$vp_v[id]'");
        cw_array2insert('flexible_import_loaded_files_hash', array('profile_id'=>$vp_v['id'], 'hash'=>$vp_v['file_hash'], 'date_loaded'=>time()));
    }


    cw_csvxc_logged_query("delete from $tables[datahub_vias_combined]");
    cw_csvxc_logged_query("INSERT INTO $tables[datahub_vias_combined] (`No.`,Description,`Bottle size`,`Case pack`,Vintage,`FDL Available`,Region,`BATF No.`, `UPC Code`,Line,`Metro 2 Case`,`Metro 3 Case`,`Metro 4 Case`,`Metro 5 Case`,`Metro 6 Case`,`Metro 10 Case`,`Metro 15 Case`,`Metro 20 Case`,`Metro 25 Case`,`Metro 28 Case`,`Metro 30 Case`,`Metro 50 Case`,`Metro 56 Case`,`Metro 100 Case`) SELECT w.`No.`,w.Description,w.`Bottle size`,w.`Case pack`,w.Vintage,w.Available, m.Region, m.`BATF No.`, m.`UPC Code`, m.Line, m.`Metro 2 Case`, m.`Metro 3 Case`, m.`Metro 4 Case`, m.`Metro 5 Case`, m.`Metro 6 Case`, m.`Metro 10 Case`, m.`Metro 15 Case`, m.`Metro 20 Case`, m.`Metro 25 Case`, m.`Metro 28 Case`, m.`Metro 30 Case`, m.`Metro 50 Case`, m.`Metro 56 Case`, m.`Metro 100 Case` FROM $tables[datahub_vias_monthly] m INNER JOIN  $tables[datahub_vias_weekly] w ON w.`No.`=m.`Item No.`");


    foreach($all_fi_profiles as $fi_prof) {
        if ($fi_prof['dbtable_src'] == $tables['datahub_vias_combined'] && $fi_prof['import_src_type'] == 'T') {

            if ($is_interim && strpos($fi_prof["name"], 'interim') !== false) {
                $vias_import_profile = $fi_prof; break;
            }

            if (!$is_interim && strpos($fi_prof["name"], 'interim') === false) {
                $vias_import_profile = $fi_prof; break;
            }

        }
    }
    cw_flexible_import_run_profile($vias_import_profile['id'], array());
    return 1;
}

function cw_datahub_delay_autoupdate($return_location) {
    global $tables, $var_dirs;

    $is_import_locked = file_get_contents($var_dirs['flexible_import'].'/datahub.lock');

    $is_import_locked_data = array();

    if ($is_import_locked) {
        $is_import_locked_data = explode('|', $is_import_locked);
    }

    if (empty($is_import_locked_data)) {
        global $target;  

        if (!file_exists($var_dirs['flexible_import'].'/datahub.lock'))   
            cw_datahub_add_log_entry(array('message'=>"Placed lock file datahub.lock to block automated update (<a href='index.php?target=datahub_release_lock'>clear lock</a>)", 'source'=>"Manually run script target=$target"));

        file_put_contents($var_dirs['flexible_import'].'/datahub.lock', "manual_script_lock|".time()."|$return_location");
        return 1;//cw_header_location("index.php?$return_location");
    } else {
        if ($is_import_locked_data[0] == 'autoupdate_script_lock') {
            $dh_import_stage = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_import_stage'"));
            $dh_import_step = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_import_step'"));
            print("<h2>Please wait, autoupdate is in progress, stage #$dh_import_stage/step #$dh_import_step is running</h2>");
        } else {
            print("<h2>Please wait, update of the hub tables is started by another process. This script will continue after completion of that process</h2>");
        }

        print("
<div id='prc_update_display'></div>
<script type='text/javascript'>
var ts_count = 7;
var ts_interval_id = setInterval(function(){ 
if (ts_count<=0) {
clearInterval(ts_interval_id);
window.location.href = 'index.php?$return_location';
} else {
ts_count--;
document.getElementById('prc_update_display').innerHTML = document.getElementById('prc_update_display').innerHTML+' .';
} 
 }, 1000);
</script>");
        die; 
    } 
}

function cw_datahub_delay_autoupdate_release_lock() {
    global $target, $var_dirs; 
    if (file_exists($var_dirs['flexible_import'].'/datahub.lock')) {
        cw_datahub_add_log_entry(array('message'=>"Released lock file datahub.lock", 'source'=>"Manually run script $target"));
        @unlink($var_dirs['flexible_import'].'/datahub.lock'); 
    }
}

function cw_datahub_monthly_prices_update($web_mode=true) {

if ($web_mode)
    cw_display_service_header("Running monthly price routine");

if ($web_mode)
    echo '<h2>Running monthly price update routine</h2><br>';

cw_csvxc_logged_query("DELETE FROM cw_datahub_pricing_IPO_avg_price");

$step1_query =
"INSERT INTO cw_datahub_pricing_IPO_avg_price
(SELECT
    x.product_id AS product_id,
    avg_items_per_order AS avg_items_per_order,
    avg_price AS avg_price,
    avg_line_items AS avg_line_items_per_order
FROM (
      SELECT ip.product_id,
             avg(te.amount) AS avg_items_per_order,
             avg(te.price) AS avg_price
      FROM (cw_docs_items te INNER JOIN cw_products ip ON te.product_id = ip.product_id)
        INNER JOIN cw_docs AS t ON te.doc_id = t.doc_id
      WHERE DateDiff(now(), FROM_UNIXTIME(t.date)) < 140 AND t.status = 'C'
     GROUP BY ip.product_id
     ) AS x
INNER JOIN (
         SELECT product_id,
                avg(tot_line_items) as avg_line_items
         FROM (
               SELECT distinct  ip1.product_id,
                      te.doc_id,
                      t.tot_line_items
               FROM (cw_docs_items AS te INNER JOIN cw_products AS ip1 ON te.product_id = ip1.product_id)
                 INNER JOIN (
                      select t2.doc_id as tnum,
                             t2.date,
                             tli.tot_line_items
                      from cw_docs t2
                        INNER JOIN (
                                    select te2.doc_id,
                                          count(*) as tot_line_items
                                    FROM cw_docs_items as te2
                                      INNER JOIN cw_products AS ip2 ON te2.product_id = ip2.product_id                                                                                    group by te2.doc_id
                                   ) tli on t2.doc_id = tli.doc_id
                      WHERE DateDiff(now(), FROM_UNIXTIME(t2.date)) < (select order_days from cw_datahub_price_settings where store_id = 1)
                               AND t2.status = 'C'
                      ) t ON te.doc_id = t.tnum
               ) as mine group by product_id
           ) AS a ON x.product_id = a.product_id)";

cw_csvxc_logged_query($step1_query);


$cnt_pricing_IPO_avg_price = cw_query_first_cell("select count(*) from cw_datahub_pricing_IPO_avg_price");

if ($web_mode)
    print("<h3>Updated table pricing_IPO_avg_price, new records count: $cnt_pricing_IPO_avg_price</h3><br>");


cw_csvxc_logged_query("DELETE FROM cw_datahub_pricing_aipo");
$step2_query =
"INSERT INTO cw_datahub_pricing_aipo
SELECT b.ID AS item_id,
       avg(a.aaipo) AS aipo
FROM (
      select i.ID,
             avg(i.cost + COALESCE(i.split_case_charge,0)) as acost
      from cw_datahub_main_data i
      group by i.ID
     ) AS b
LEFT JOIN (
           select acost,
                  avg(aipo) as aaipo
           from (
                select round(avg_price,0) as acost,
                       avg_items_per_order * avg_line_items_per_order as aipo
                from cw_datahub_pricing_IPO_avg_price
                ) as x
           group by acost
           ) AS a ON (b.acost < a.acost*1.3) AND (b.acost > a.acost*.7)
WHERE COALESCE(b.acost,0) > 0
GROUP BY b.ID";

cw_csvxc_logged_query($step2_query);

if ($web_mode)
    $cnt_cw_datahub_pricing_aipo = cw_query_first_cell("select count(*) from cw_datahub_pricing_aipo");

if ($web_mode)
    print("<h3>Updated table cw_datahub_pricing_aipo, new records count: $cnt_cw_datahub_pricing_aipo</h3><br>");

cw_datahub_add_log_entry(array('message'=>"Monthly price update script run", 'source'=>($web_mode?'Manual run':'Cron')));

}

function cw_flexible_import_run_scheduled_monthly_update($time) {
    global $tables, $config;

    if (intval($config['flexible_import']['fi_datahub_monthly_price_update_period']) == 0) return; 

    $last_time_run = intval(cw_query_first_cell("select value from $tables[config] where name='fi_monthly_prices_update_last_run'"));
  
    if (time()-$last_time_run > intval($config['flexible_import']['fi_datahub_monthly_price_update_period'])*24*3600) {
         cw_datahub_monthly_prices_update(false);   
         cw_array2insert('config', array('name'=>'fi_monthly_prices_update_last_run', 'value'=>time()), true);
    }

}
/**
 * Try to resolve some DB inconsistency which possibly caused by interrupted script
 * 
 * @global type $config
 */
function cw_datahub_clean_alus_for_correction() {
    global $config;

    $fi_clean_rebuild_alus = array();
    if (!empty($config['flexible_import']['fi_clean_rebuild_alus'])) {
        $_fi_clean_rebuild_alus = explode(",", trim($config['flexible_import']['fi_clean_rebuild_alus'])); 
        foreach ($_fi_clean_rebuild_alus as $alu) {
            $alu2add = intval(trim($alu));
            if ($alu2add) 
                $fi_clean_rebuild_alus[] = $alu2add;
        } 
        cw_array2update('config', array('value'=>''), "name='fi_clean_rebuild_alus'");   
    }

    //$del_ids = cw_query_column("select `Alternate Lookup` from cw_datahub_pos_export_new where `Item Description`=''");
    //$del_ids=array(798907);
    //$del_ids = cw_query_column("select catalogid from cw_datahub_pos_lost_items");

    cw_csvxc_logged_query("drop table if exists cw_datahub_pos_lost_alu_temp");
    cw_csvxc_logged_query("create table cw_datahub_pos_lost_alu_temp as select catalogid from xfer_products_SWE where hide=0 and catalogid not in (select alu from cw_qbwc_pos_items_buffer) and ccode not in (select replace(`X-REF`,' ','') from cw_qbwc_pos_items_buffer)");

    cw_csvxc_logged_query("drop table if exists cw_datahub_lost_item_store");
    cw_csvxc_logged_query("create table cw_datahub_lost_item_store  as select item_id from item_price where store_sku is NULL and item_id in (select catalogid from xfer_products_SWE where hide=0)");

    $del_ids = array_merge(
        $lost_alu = cw_query_column("select catalogid from cw_datahub_pos_lost_alu_temp"),
        $lost_item_store = cw_query_column("select item_id from cw_datahub_lost_item_store"),   
        $dhmd = array(), 
        //cw_query_column("select id from cw_datahub_main_data where catalog_id=0 and catalog_id not in (447353, 449968, 450070, 450165)"), 
        $pne = cw_query_column("select `Alternate Lookup` from cw_datahub_pos_export_new where `Item Description`=''"), 
        $step12err = cw_query_column("select `Alternate Lookup` from cw_datahub_step12alus"), 
        $manual = array(), 
        $incorrect_new = cw_query_column("select `Alternate Lookup` from cw_datahub_pos_export_new_incorrect_items"), 
        $empty_sku = cw_query_column("select catalogid from xfer_products_SWE where  hide=0 and sku is null"),
        $fi_clean_rebuild_alus
    );

    if (!empty($del_ids)) {
        cw_log_add("datahub_lost_items_repair", array('datahub_main_data'=>$dhmd, 'pos_new_export'=>$pne, 'step12alus'=>$step12err, 'manual'=>$manual, 'incorrect_new'=>$incorrect_new, 'fi_clean_rebuild_alus'=>$fi_clean_rebuild_alus, 'lost_alu'=>$lost_alu, 'lost_item_store'=>$lost_item_store,'empty_sku'=>$empty_sku, 'TOTAL'=>$del_ids));
        cw_csvxc_logged_query("delete from item_store2 where item_id in (".implode(",", $del_ids).")");
        cw_csvxc_logged_query("delete from item where id in (".implode(",", $del_ids).")");
        cw_csvxc_logged_query("update cw_datahub_main_data set catalog_id=0 where id in (".implode(",", $del_ids).")");

        if (!empty($step12err))
            db_query("delete from cw_datahub_step12alus");
    }

    cw_csvxc_logged_query("delete item_store2.* from item_store2 left join pos p on cast(p.`Alternate Lookup` as signed)=item_store2.item_id and p.`Item Number`=item_store2.store_sku where p.`Item Number` is null");
    cw_csvxc_logged_query("drop table if exists cw_datahub_pos_lost_alu_temp");
    cw_csvxc_logged_query("drop table if exists cw_datahub_lost_item_store");
}

/**
 * Correct images in cw_datahub_main_data_images. 
 * ?? Take image from analogue if product has no own image
 */
function cw_datahub_correct_images() {
    cw_csvxc_logged_query("drop table if exists cw_datahub_images_correct");
    cw_csvxc_logged_query("create table cw_datahub_images_correct (id int(11) not null default 0, producer varchar(50), name varchar(225), image_id int(11) not null default 0, imageurl varchar(100) not null default '', PRIMARY KEY (id))");
    cw_csvxc_logged_query("insert into cw_datahub_images_correct (id, producer, name, image_id, imageurl) select dh.id, dh.producer, dh.name, dhi.id, dhi.web_path from cw_datahub_main_data dh left join cw_datahub_main_data_images dhi on dh.cimageurl=dhi.id and dh.id=dhi.item_id");
    cw_csvxc_logged_query("alter table cw_datahub_images_correct add key dhic_name (name)");
    cw_csvxc_logged_query("alter table cw_datahub_images_correct add key dhic_producer (Producer)");
    cw_csvxc_logged_query("UPDATE cw_datahub_images_correct SET image_id=0, imageurl='' WHERE imageurl='images/no_image.jpg'");
    cw_csvxc_logged_query("UPDATE cw_datahub_images_correct AS i INNER JOIN cw_datahub_images_correct AS i2 ON i.Producer = i2.Producer and i.name = i2.name and i.ID <> i2.ID and (i2.image_id = 0 or i2.imageurl='images/no_image.jpg' or i2.imageurl='') and (i.imageurl <> 'images/no_image.jpg' and i.imageurl <> '') SET i2.imageurl = i.imageurl WHERE Trim(COALESCE(i.Producer,'')) <> '' and Trim(COALESCE(i.name,'')) <> ''");
    cw_csvxc_logged_query("alter table cw_datahub_images_correct add column is_corrected int(1) not null default 0");
    cw_csvxc_logged_query("update cw_datahub_images_correct set is_corrected=1 where image_id=0 and imageurl!=''");

    cw_csvxc_logged_query("delete from cw_datahub_main_data_images where item_id in (select id from cw_datahub_images_correct where is_corrected=1)");
    cw_csvxc_logged_query("insert into cw_datahub_main_data_images (filename, web_path, system_path, item_id) select imageurl, imageurl,imageurl,id from cw_datahub_images_correct where is_corrected=1");
    cw_csvxc_logged_query("update cw_datahub_images_correct dhic inner join cw_datahub_main_data_images dhdi on dhic.is_corrected=1 and dhic.id=dhdi.item_id set dhic.image_id=dhdi.id");

    cw_csvxc_logged_query("update cw_datahub_main_data dhmd inner join cw_datahub_images_correct dhic on dhic.is_corrected=1 and dhic.id=dhmd.id set dhmd.cimageurl=dhic.image_id");

    cw_csvxc_logged_query("update xfer_products_SWE xps inner join cw_datahub_main_data dhmd on xps.catalogid=dhmd.catalog_id inner join cw_datahub_images_correct dhic on dhic.id=dhmd.id set xps.cimageurl=dhic.imageurl, xps.extendedimage=dhic.imageurl where dhic.is_corrected=1");

    cw_csvxc_logged_query("drop table if exists cw_datahub_images_correct");
}

function cw_datahub_save_product_data_snapshot() {
    global $tables;

    cw_csvxc_logged_query("drop table if exists cw_products_status_snapshot"); 
    cw_csvxc_logged_query("create table cw_products_status_snapshot (product_id int(11) not null default 0, status int(1) not null default 0, PRIMARY KEY (product_id))");  
    cw_csvxc_logged_query("insert into cw_products_status_snapshot select product_id, status from $tables[products]");
//---------------------
    cw_csvxc_logged_query("drop table if exists cw_products_price_snapshot");
    cw_csvxc_logged_query("create table cw_products_price_snapshot like $tables[products_prices]");
    cw_csvxc_logged_query("insert into cw_products_price_snapshot select * from $tables[products_prices]");

}


function cw_datahub_compare_product_data_snapshot() {
    global $tables;

    if (!empty(cw_query_first_cell("show tables like 'cw_products_status_snapshot'"))) {
        cw_csvxc_logged_query("replace into $tables[history_products_status] (product_id, status, date) select p.product_id, p.status, unix_timestamp(now()) from $tables[products] p left join cw_products_status_snapshot ps on p.product_id=ps.product_id where p.status!=ps.status OR ps.status IS NULL");

        cw_csvxc_logged_query("drop table if exists cw_products_status_snapshot");
    }

    if (!empty(cw_query_first_cell("show tables like 'cw_products_price_snapshot'"))) {
        cw_csvxc_logged_query("insert into cw_history_products_price select pp.product_id, pp.variant_id, pp.membership_id, pp.quantity, pp.price, pp.list_price, unix_timestamp(now()) from $tables[products_prices] pp left join cw_products_price_snapshot pps on pps.product_id=pp.product_id and pps.variant_id=pp.variant_id and pps.membership_id=pp.membership_id and pps.price=pp.price and pps.list_price=pp.list_price where pps.product_id is null");

        cw_csvxc_logged_query("drop table if exists cw_products_price_snapshot");
    }
}

function cw_datahub_private_collector_correct() {
    global $tables;

    cw_csvxc_logged_query("drop table if exists cw_datahub_private_collector_correction");
    cw_csvxc_logged_query("create table cw_datahub_private_collector_correction as select pib.alu, dmd.initial_xref, dmd.supplier_id, pib.departmentcode, pib.QUANTITYONHAND, pib.ONHANDSTORE01, ix.xref from cw_datahub_main_data dmd inner join cw_qbwc_pos_items_buffer pib on dmd.catalog_id=pib.alu and pib.alu!=0 inner join item_xref ix on ix.item_id=pib.alu and ix.xref=dmd.initial_xref where pib.vendorcode=57 and dmd.supplier_id!=57 and pib.departmentcode=3");


   cw_csvxc_logged_query("drop table if exists item_xref_private_collector_correction");
   cw_csvxc_logged_query("create table item_xref_private_collector_correction as select ix.* from item_xref ix inner join cw_datahub_private_collector_correction pcc on ix.item_id=pcc.alu and ix.xref=pcc.xref");

   cw_csvxc_logged_query("delete ix.* from item_xref ix inner join cw_datahub_private_collector_correction pcc on pcc.alu=ix.item_id");

   cw_csvxc_logged_query("alter table cw_datahub_private_collector_correction add column itemnumber int(11) not null default 0 after alu");

   cw_csvxc_logged_query("update cw_datahub_private_collector_correction pcc inner join cw_qbwc_pos_items_buffer pib on pib.alu=pcc.alu and pib.alu!=0 set pcc.itemnumber=pib.itemnumber");

  cw_csvxc_logged_query("alter table cw_datahub_private_collector_correction add column new_xref varchar(50) not null default ''");
  cw_csvxc_logged_query("update cw_datahub_private_collector_correction set new_xref=xref where xref like 'swe-%'");
  cw_csvxc_logged_query("update cw_datahub_private_collector_correction set new_xref=concat('swe-',itemnumber) where new_xref=''");
  cw_csvxc_logged_query("update cw_datahub_private_collector_correction pcc set new_xref = concat('swe-',itemnumber,'-',CAST(100*rand() as SIGNED)) where new_xref in (select xref from item_xref)");

  cw_csvxc_logged_query("update item_xref_private_collector_correction ixpcc inner join cw_datahub_private_collector_correction pcc on pcc.alu=ixpcc.item_id and pcc.xref=ixpcc.xref set ixpcc.supplier_id=57, ixpcc.qty_avail=pcc.QUANTITYONHAND, ixpcc.xref=pcc.new_xref");
  cw_csvxc_logged_query("insert into item_xref select * from item_xref_private_collector_correction");

}

function cw_datahub_creation_date_correction() {

  cw_csvxc_logged_query("update cw_products_system_info psi inner join cw_qbwc_pos_items_buffer pib on pib.alu=psi.product_id and pib.TIMECREATED != '' set psi.creation_date=unix_timestamp(pib.TIMECREATED)");

  cw_csvxc_logged_query("drop table if exists cw_products_system_info_cdc");

  cw_csvxc_logged_query("create table cw_products_system_info_cdc like cw_products_system_info");
  cw_csvxc_logged_query("insert into cw_products_system_info_cdc select * from cw_products_system_info");
  cw_csvxc_logged_query("update cw_products_system_info psi set creation_date =(select creation_date from cw_products_system_info_cdc where product_id < psi.product_id and creation_date>0 order by product_id desc limit 1) where creation_date=0");

  cw_csvxc_logged_query("drop table if exists cw_products_system_info_cdc");

}

function cw_datahub_history_cost_correction() {

    cw_csvxc_logged_query("drop table if exists cw_history_cost_incorrect");
    cw_csvxc_logged_query("create table cw_history_cost_incorrect as select di.doc_id, item_id, product_id, product, price, history_cost, amount, from_unixtime(d.date), status  from cw_docs_items di inner join cw_docs d on d.doc_id=di.doc_id and d.date > unix_timestamp(now())-3600*24 where price<=history_cost order by doc_id desc limit 10");

    cw_csvxc_logged_query("alter table cw_history_cost_incorrect add column correct_cost decimal(12,2) not null default 0.00");

    cw_csvxc_logged_query("update cw_history_cost_incorrect hci set correct_cost=(select list_price from cw_products_prices where product_id=hci.product_id and quantity<=hci.amount and quantity!=1 order by quantity desc limit 1)");

    cw_csvxc_logged_query("update cw_history_cost_incorrect hci set correct_cost=(select cost from cw_products where cost<hci.price and product_id=hci.product_id) where correct_cost=0");
    cw_csvxc_logged_query("update cw_docs_items di inner join cw_history_cost_incorrect hci on hci.item_id=di.item_id and hci.correct_cost>0 and hci.price>hci.correct_cost set di.history_cost=hci.correct_cost");
    cw_csvxc_logged_query("drop table if exists cw_history_cost_incorrect"); 


    cw_csvxc_logged_query("create table lost_prices_tmp as select di.product_id, di.amount as quantity, di.price, p.status from cw_docs_items di inner join cw_docs d on d.doc_id = di.doc_id inner join cw_products p on p.product_id=di.product_id where di.product_id not in (select product_id from cw_products_prices)");

    if (cw_query_first_cell("select count(*) from lost_prices_tmp"))
        cw_log_add('lost_prices_tmp', cw_query_column("select product_id from lost_prices_tmp"));

    cw_csvxc_logged_query("insert into cw_products_prices (product_id, quantity, price) select product_id, 1, price from lost_prices_tmp group by product_id");
    cw_csvxc_logged_query("drop table lost_prices_tmp");

}

function cw_datahub_live_items_dept_correction () {
    global $tables;

    $tables['docs_items_mc'] = 'cw_docs_items_mc';
    db_query("drop table if exists $tables[docs_items_mc]");
    db_query("create table $tables[docs_items_mc] (item_id int(11) not null default 0, PRIMARY KEY(item_id))");
    db_query("insert into $tables[docs_items_mc] (item_id) select a.item_id from (select di.item_id, min(di.amount-pp.quantity) as delta from $tables[docs_items] di left join $tables[products_prices] pp on di.product_id=pp.product_id AND pp.quantity>1 and pp.quantity<=di.amount group by di.item_id having delta is not null) as a");


    $dpt_attr_id = cw_query_first_cell("select attribute_id from $tables[attributes] where field='departmentname' and item_type='P'");
    if (empty($dpt_attr_id)) return;

    cw_csvxc_logged_query("drop table if exists av_dept");
    cw_csvxc_logged_query("create table av_dept as select ' ' as dbl, pib.ITEMNUMBER, p.product_id, $dpt_attr_id as attribute_id, avd.attribute_value_id, 'EN' as code, 'P' as item_type, pib.timemodified,unix_timestamp(pib.timemodified) as date_mod from cw_products p inner join cw_qbwc_pos_items_buffer pib on pib.alu=p.product_id inner join cw_attributes_default avd on avd.attribute_id=$dpt_attr_id and avd.value=pib.DEPARTMENTNAME");
    cw_csvxc_logged_query("update av_dept set dbl='d' where product_id in (select dp.product_id from (select product_id , count(*) as c from av_dept group by product_id having c > 1) as dp)");
    cw_csvxc_logged_query("update av_dept a inner join pos p on p.`Item Number`=a.itemnumber and cast(p.`Alternate Lookup` as signed)=a.product_id set a.dbl='c' where a.dbl='d'");
    cw_csvxc_logged_query("delete from av_dept where dbl = 'd'");
    cw_csvxc_logged_query("delete avt.* from cw_attributes_values avt inner join av_dept a on a.product_id=avt.item_id and a.attribute_id=avt.attribute_id"); 
    cw_csvxc_logged_query("insert into cw_attributes_values select product_id, attribute_id, attribute_value_id, code, item_type from av_dept");
    cw_csvxc_logged_query("drop table if exists av_dept");

    cw_csvxc_logged_query("insert into cw_attributes_values (item_id, attribute_id, value, code, item_type) select product_id, $dpt_attr_id, ad.attribute_value_id, 'EN', 'P' from cw_products p inner join cw_datahub_main_data dhmd on dhmd.catalog_id = p.product_id inner join cw_attributes_default ad on ad.attribute_id=$dpt_attr_id and ad.value=dhmd.drysweet where product_id not in (select x.item_id from (select item_id from cw_attributes_values where attribute_id=$dpt_attr_id) as x)");

  
    cw_csvxc_logged_query("drop table if exists supplier_compare_temp");
    cw_csvxc_logged_query("create table supplier_compare_temp as select product_id, supplier_customer_id from cw_products_system_info");
    cw_csvxc_logged_query("alter table supplier_compare_temp add column hub_supplier_id int(11) not null default 0");
    cw_csvxc_logged_query("update supplier_compare_temp sct inner join cw_register_fields_values rfv on rfv.customer_id = sct.supplier_customer_id and rfv.field_id=143 set hub_supplier_id=rfv.value");
    cw_csvxc_logged_query("alter table supplier_compare_temp add column pos_supplier_id int(11) not null default 0");

    cw_csvxc_logged_query("update supplier_compare_temp sct inner join cw_qbwc_pos_items_buffer pib on pib.alu=sct.product_id set pos_supplier_id=VENDORCODE");

    cw_csvxc_logged_query("update supplier_compare_temp sct inner join cw_datahub_main_data dhmd on sct.product_id = dhmd.catalog_id and sct.pos_supplier_id=0 and sct.supplier_customer_id=0 set sct.pos_supplier_id = dhmd.supplier_id");

    cw_csvxc_logged_query("update supplier_compare_temp sct inner join pos p on cast(`Alternate Lookup` as signed) = sct.product_id and sct.pos_supplier_id=0 and sct.supplier_customer_id=0 set sct.pos_supplier_id = p.`vendor code`");

    cw_csvxc_logged_query("alter table supplier_compare_temp add column pos_supplier_customer_id int(11) not null default 0");

    cw_csvxc_logged_query("update supplier_compare_temp sct inner join cw_register_fields_values rfv on rfv.value = sct.pos_supplier_id and rfv.field_id=143 set pos_supplier_customer_id=rfv.customer_id");
    cw_csvxc_logged_query("update supplier_compare_temp sct inner join cw_register_fields_values rfv on rfv.value = sct.pos_supplier_id and rfv.field_id=145 set pos_supplier_customer_id=rfv.customer_id where pos_supplier_customer_id=0");
    cw_csvxc_logged_query("update cw_products_system_info psi inner join supplier_compare_temp sct on sct.product_id=psi.product_id and sct.pos_supplier_customer_id!=0 and psi.supplier_customer_id=0 set psi.supplier_customer_id=sct.pos_supplier_customer_id");
    cw_csvxc_logged_query("drop table if exists supplier_compare_temp");
  
}

/**
 * Correct price calced in pricing_calc_SWE()
 * Add surcharge depending on cost
 * 
 * @global type $tables
 * @global type $config
 * @param type $tbl_name - item_price, item_price_twelve_bottle, item_price_bottle_level_X
 */
function cw_datahub_apply_price_levels($tbl_name = 'item_price') {
    global $tables, $config;

    $price_levels = cw_query("select * from $tables[datahub_price_levels] order by cost asc");
    foreach ($price_levels as $pl) {
        cw_csvxc_logged_query("UPDATE $tbl_name SET price=cost*(1+$pl[surcharge]/100) WHERE cost>$pl[cost]");
    }

/*
    $disc7 = $config['flexible_import']['fi_datahub_first_mc_default_discount'];
    if (empty($disc7)) $disc7 = 7;

    //apply discount of 7% to all mc levels, excluding 12 bottles prices
    if (strpos($tbl_name, 'item_price_bottle_lvl')!==false) {
        cw_csvxc_logged_query("UPDATE $tbl_name SET price=price*(1-$disc7/100) WHERE bot_qty>12");
    }
*/

    // Debug 
    $tmp_pl_tbl_name = $tbl_name."_price_levels";
    db_query("drop table if exists $tmp_pl_tbl_name");
    db_query("create table $tmp_pl_tbl_name like $tbl_name");
    db_query("insert into $tmp_pl_tbl_name select * from $tbl_name");

    // Pull up price if it is lower than boundary and price is not fixed
    if ($config['flexible_import']['fi_datahub_low_price_limit'] > 0 && $tbl_name == 'item_price') {
        cw_csvxc_logged_query("UPDATE $tbl_name itp LEFT JOIN SWE_store_feed ssf on ssf.sku=itp.store_sku AND (ssf.manual_price>0 or ssf.twelve_bot_manual_price>0) SET itp.price='".$config['flexible_import']['fi_datahub_low_price_limit']."' WHERE ssf.sku IS NULL AND itp.price<'".$config['flexible_import']['fi_datahub_low_price_limit']."'");   
//        cw_csvxc_logged_query("UPDATE $tbl_name itp SET itp.price='".$config['flexible_import']['fi_datahub_low_price_limit']."' WHERE itp.price<'".$config['flexible_import']['fi_datahub_low_price_limit']."'");
    }

}

function cw_datahub_clean_dbl_pricing() {
    cw_csvxc_logged_query("drop table if exists cw_datahub_clean_dbl_pricing_temp");
    cw_csvxc_logged_query("create table cw_datahub_clean_dbl_pricing_temp as select product_id, quantity, max(price_id) as price_id, count(*) c from cw_products_prices pp group by product_id, quantity having c > 1");
    cw_csvxc_logged_query("delete ppt.* from cw_products_prices ppt inner join cw_datahub_clean_dbl_pricing_temp dpt on ppt.product_id = dpt.product_id and ppt.quantity = dpt.quantity and ppt.price_id != dpt.price_id");
}


function cw_datahub_make_prices_descent() {
    cw_csvxc_logged_query("drop table if exists cw_pp_prices_diff");
    cw_csvxc_logged_query("create temporary table cw_pp_prices_diff as select p.*, p2.price-p.price as p_diff, p2.quantity-p.quantity as q_diff, p2.price as p2_price, p2.quantity as p2_quantity from cw_products_prices p inner join cw_products_prices p2 on p.product_id=p2.product_id and p2.quantity<p.quantity having p_diff<=0");
    cw_csvxc_logged_query("delete pp.* from cw_products_prices pp inner join cw_pp_prices_diff pd on pp.price_id=pd.price_id");
}


function cw_datahub_set_selling_case_size() {
    global $config;

    $single_item_lim = $config['flexible_import']['fi_datahub_low_price_single_bottle_limit'];
    if (empty($single_item_lim)) $single_item_lim = 14.94;

    cw_csvxc_logged_query("delete av.* from cw_attributes_values av inner join xfer_products_SWE xps on xps.hide=0 and xps.catalogid=av.item_id and av.item_type='P' and av.attribute_id = (select attribute_id from cw_attributes where field='selling_case_size')");

    cw_csvxc_logged_query("drop table if exists cw_datahub_selling_case_size_temp");

    cw_csvxc_logged_query("create table cw_datahub_selling_case_size_temp as select xps.catalogid, xps.cprice, xps.sku, IF(coalesce(ssf.manual_price,0)>0 OR coalesce(ssf.twelve_bot_manual_price,0)>0,'Y','N') as manual_price, ix.bot_per_case from xfer_products_SWE xps left join SWE_store_feed ssf on ssf.sku=xps.sku left join item_xref ix on ix.item_id = xps.catalogid and ix.xref=xps.ccode where xps.hide=0 and xps.cprice<$single_item_lim");

    cw_csvxc_logged_query("insert into cw_attributes_values (item_id, attribute_id, value, item_type) select catalogid, (select attribute_id from cw_attributes where field='selling_case_size'), 12, 'P' from cw_datahub_selling_case_size_temp");

    //cw_csvxc_logged_query("insert into cw_attributes_values (item_id, attribute_id, value, item_type) select catalogid, (select attribute_id from cw_attributes where field='selling_case_size'), 6, 'P' from cw_datahub_selling_case_size_temp where manual_price='Y'");

    cw_csvxc_logged_query("update cw_products p inner join xfer_products_SWE xps on xps.hide=0 and xps.catalogid=p.product_id set p.min_amount=0");

    cw_csvxc_logged_query("update cw_products p inner join xfer_products_SWE xps on xps.hide=0 and xps.catalogid=p.product_id inner join cw_attributes_values av on av.attribute_id = (select attribute_id from cw_attributes where field='selling_case_size') and av.item_type='P' and av.item_id = p.product_id set p.min_amount=cast(av.value as signed)");
}


function cw_datahub_back_tbl($tbl_name, $ext) {
/*
cw_csvxc_logged_query("drop table if exists $tbl_name$ext");
cw_csvxc_logged_query("create table $tbl_name$ext like $tbl_name");
cw_csvxc_logged_query("insert into $tbl_name$ext select * from $tbl_name");
*/
}

/**
 * Correct wholesale prices on site side
 * 
 * 
 * @global type $config
 */
function cw_datahub_add_extra_cases_discounts() {
    global $config, $tables;

cw_datahub_back_tbl('cw_products_prices','_temp1');

    cw_datahub_clean_dbl_pricing();

cw_datahub_back_tbl('cw_products_prices','_temp2');

    cw_datahub_make_prices_descent();

cw_datahub_back_tbl('cw_products_prices','_temp3');

    //delete prices for more than 5 cases
    cw_csvxc_logged_query("delete pp.* from cw_products_prices pp inner join xfer_products_SWE xps on xps.catalogid = pp.product_id and xps.hide=0 inner join item_xref ix on ix.item_id=pp.product_id and ix.xref=xps.ccode where pp.quantity>ix.bot_per_case*5 and ix.bot_per_case>1");

    //calculation of 12 bottles 5% discounted price level
    cw_csvxc_logged_query("drop table if exists cw_datahub_discounted_cases_items_temp");

    cw_csvxc_logged_query("create table cw_datahub_discounted_cases_items_temp as select xps.catalogid, xps.cprice, xps.sku, xps.cost, xps.cstock as quantity from xfer_products_SWE xps left join SWE_store_feed ssf on ssf.sku=xps.sku and (ssf.manual_price>0 or ssf.twelve_bot_manual_price>0) where ssf.sku is null and xps.hide=0");

    cw_csvxc_logged_query("alter table cw_datahub_discounted_cases_items_temp add index (catalogid)");

    cw_csvxc_logged_query("update cw_datahub_discounted_cases_items_temp set quantity=1");

    cw_csvxc_logged_query("update cw_datahub_discounted_cases_items_temp ddci set cprice=(select price from cw_products_prices where product_id = ddci.catalogid and quantity <= 12 order by quantity desc limit 1), quantity=(select quantity from cw_products_prices where product_id = ddci.catalogid and quantity <= 12 order by quantity desc limit 1)");

    cw_csvxc_logged_query("update cw_datahub_discounted_cases_items_temp ddci inner join cw_products_prices pp on pp.product_id = ddci.catalogid and pp.quantity=ddci.quantity and pp.price=ddci.cprice set ddci.cost=pp.list_price where ddci.quantity>1");

    //calculation of 24 bottles 7% discounted price level
    cw_csvxc_logged_query("drop table if exists cw_datahub_discounted_dbl_cases_items_temp");

    cw_csvxc_logged_query("create table cw_datahub_discounted_dbl_cases_items_temp as select xps.catalogid, xps.cprice, xps.sku, xps.cost, xps.cstock as quantity from xfer_products_SWE xps left join SWE_store_feed ssf on ssf.sku=xps.sku and (ssf.manual_price>0 or ssf.twelve_bot_manual_price>0) where ssf.sku is null and xps.hide=0");

    cw_csvxc_logged_query("alter table cw_datahub_discounted_dbl_cases_items_temp add index (catalogid)");

    cw_csvxc_logged_query("update cw_datahub_discounted_dbl_cases_items_temp set quantity=1");

    cw_csvxc_logged_query("update cw_datahub_discounted_dbl_cases_items_temp dddci set cprice=(select price from cw_products_prices where product_id = dddci.catalogid and quantity <= 24 order by quantity desc limit 1), quantity=(select quantity from cw_products_prices where product_id = dddci.catalogid and quantity <= 24 order by quantity desc limit 1)");

    cw_csvxc_logged_query("update cw_datahub_discounted_dbl_cases_items_temp dddci inner join cw_products_prices pp on pp.product_id = dddci.catalogid and pp.quantity=dddci.quantity and pp.price=dddci.cprice set dddci.cost=pp.list_price where dddci.quantity>1");

    $disc5 = $config['flexible_import']['fi_datahub_twelve_bottle_default_discount'];
    if (empty($disc5)) $disc5 = 5;

    $disc7 = $config['flexible_import']['fi_datahub_first_mc_default_discount'];
    if (empty($disc7)) $disc7 = 7;

cw_datahub_back_tbl('cw_products_prices','_temp4');

    //application of discounted 5% and 7% price levels
    cw_csvxc_logged_query("insert into cw_products_prices (product_id, quantity, price, list_price) select catalogid, 12, cprice*(1-$disc5/100), cost from cw_datahub_discounted_cases_items_temp");

cw_datahub_back_tbl('cw_products_prices','_temp5');

    cw_csvxc_logged_query("insert into cw_products_prices (product_id, quantity, price, list_price) select catalogid, 24, cprice*(1-$disc7/100), cost from cw_datahub_discounted_dbl_cases_items_temp");

    // temp patch for a product
    cw_csvxc_logged_query("delete from cw_products_prices where product_id=799531 and quantity=6");
     cw_csvxc_logged_query("insert into cw_products_prices (product_id, quantity, price, list_price) 
         select product_id, 12, price*0.95, list_price from cw_products_prices where product_id=799531 and quantity=1");
    cw_csvxc_logged_query("insert into cw_products_prices (product_id, quantity, price, list_price) 
         select product_id, 24, price*0.93, list_price from cw_products_prices where product_id=799531 and quantity=1");

    cw_datahub_set_selling_case_size();

cw_datahub_back_tbl('cw_products_prices','_temp6');

    //delete price levels between 1 and 12 and between 12 and 24
    cw_csvxc_logged_query("delete pp.* from cw_products_prices pp inner join xfer_products_SWE xps on xps.catalogid = pp.product_id and xps.hide=0 inner join item_xref ix on ix.item_id=pp.product_id and ix.xref=xps.ccode where (pp.quantity>12 and pp.quantity<24) and ix.bot_per_case>1 and pp.quantity>1");

cw_datahub_back_tbl('cw_products_prices','_temp7');

    $single_item_lim = $config['flexible_import']['fi_datahub_low_price_single_bottle_limit'];
    if (empty($single_item_lim)) $single_item_lim = 14.94;
    cw_csvxc_logged_query("drop table if exists cw_products_prices_below_threshold");
    cw_csvxc_logged_query("create temporary table cw_products_prices_below_threshold as select product_id from cw_products_prices where quantity=1 and price<$single_item_lim");

//    cw_csvxc_logged_query("create temporary table cw_products_prices_below_threshold as select product_id from cw_products_prices where quantity=1");
    
    cw_csvxc_logged_query("delete pp.* from cw_products_prices pp inner join cw_products_prices_below_threshold ppbt on pp.product_id=ppbt.product_id inner join xfer_products_SWE xps on xps.catalogid = pp.product_id and xps.hide=0 inner join item_xref ix on ix.item_id=pp.product_id and ix.xref=xps.ccode where (pp.quantity>1 and pp.quantity<12) and ix.bot_per_case>1 and pp.quantity>1");


cw_datahub_back_tbl('cw_products_prices','_temp8');

    //decrease by 7% all prices after 7%
    cw_csvxc_logged_query("update cw_products_prices pp inner join xfer_products_SWE xps on xps.catalogid = pp.product_id and xps.hide=0 set price=price*(1-$disc7/100) where quantity>24");

cw_datahub_back_tbl('cw_products_prices','_temp9');

    cw_datahub_make_prices_descent();
    cw_datahub_clean_dbl_pricing();

    $low_price_limit = floatval($config['flexible_import']['fi_datahub_low_price_limit']);
    if ($low_price_limit > 0.01 ) {

        cw_csvxc_logged_query("update cw_products_prices pp inner join xfer_products_SWE xps on xps.catalogid=pp.product_id and xps.hide=0 left join SWE_store_feed ssf on ssf.sku=xps.sku and (ssf.manual_price>0 or ssf.twelve_bot_manual_price>0) set pp.price=$low_price_limit where pp.quantity=12 and pp.price<$low_price_limit and ssf.sku is null");

        cw_csvxc_logged_query("delete pp.* from cw_products_prices pp inner join xfer_products_SWE xps on xps.catalogid=pp.product_id and xps.hide=0 left join SWE_store_feed ssf on ssf.sku=xps.sku and (ssf.manual_price>0 or ssf.twelve_bot_manual_price>0) where ssf.sku is null and pp.quantity>12 and pp.price<$low_price_limit");  

    }


    cw_csvxc_logged_query("update cw_products_prices pp inner join xfer_products_SWE xps on xps.catalogid=pp.product_id inner join SWE_store_feed ssf on ssf.sku=xps.sku and ssf.min_price>0 set pp.list_price=pp.price, pp.price=ssf.min_price where pp.quantity=1 and pp.list_price=0");
cw_datahub_back_tbl('cw_products_prices','_temp10');

}


function cw_datahub_update_swe_xrefs_stock() {

    db_query("UPDATE item_xref ix INNER JOIN pos p on CAST(p.`Alternate Lookup` AS SIGNED)=ix.item_id AND p.`Custom Field 5`=ix.xref AND COALESCE(p.`Alternate Lookup`,0)>0 SET ix.qty_avail=0 WHERE ix.xref like 'swe-%'");

}
