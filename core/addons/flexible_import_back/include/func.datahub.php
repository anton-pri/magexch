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

   $result = cw_query("SELECT * FROM $tables[datahub_log] ORDER BY date $data[sort_direction] $limit_query");

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
function cw_datahub_get_buffer_table_fields() {
    global $tables;

    $buffer_table_excluded_fields = cw_datahub_buffer_table_filter_fields('excl_list');

    $buffer_tbl_fields = cw_check_field_names(array(), $tables['datahub_import_buffer']);

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


function cw_datahub_sel_item($table_id, $key, $linked_ids) {
    $buffer_match_pre_save = &cw_session_register('buffer_match_pre_save', array());

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

function cw_datahub_man_sel_item($table_id, $get_text = false) {
    global $tables;
    $preview_columns = cw_datahub_main_table_filter_fields('buffer_match_preview');
//array('ID','Producer','name','Vintage','size','country','Region','Appellation','sub_appellation');

    $manual_sel_items = &cw_session_register('manual_sel_items', array());
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

function cw_datahub_prepare_display_buffer($sent_data, $match_items_column_id) {

    global $tables;

    //$preview_columns = cw_datahub_main_table_filter_fields('buffer_match_preview');
//array('ID','Producer','name','Vintage','size','country','Region','Appellation','sub_appellation');
    $std_item_options = array('0'=>'None', '-1' => 'New', '-2' => 'BlackList');

    $buffer_search = &cw_session_register('buffer_search', array());
    if ($buffer_search['map'] == 'blacklist') {
        $std_item_options = array('0'=>'None', '-3' => 'Remove from BlackList');
    }

    if (is_array($sent_data['data'])) {
        foreach ($sent_data['data'] as $row_id => $row_vals) {
/*
            if (empty($row_vals[$match_items_column_id])) continue;

            $match_ids = explode(",", $row_vals[$match_items_column_id]);

            $ext_match_items = cw_query("SELECT `".implode("`,`", $preview_columns)."` from $tables[datahub_main_data] WHERE ID IN ('".implode("','", $match_ids)."')");                   
*/
            $preview_columns = cw_datahub_get_preview_columns($row_vals[1]);

            $table_id = $row_vals[0];

            $linked_ids = cw_query_column("select dhml.item_id from item_xref dhml inner join $tables[datahub_import_buffer] dhib on dhib.item_xref = dhml.xref and dhib.table_id='$table_id' inner join $tables[datahub_main_data] dhmd on dhmd.ID=dhml.item_id", "item_id");

            $std_options_html = array();
            foreach ($std_item_options as $sio_k => $sio_title) {
                if ($sio_k == 0 && !empty($linked_ids)) {
                    $sio_title = "Keep mapped to <b><span class='lng_tooltip' title=\"".cw_datahub_match_items_preview($linked_ids, $preview_columns)."\">#".implode(", #",$linked_ids)."</span></b>";
                } 
                $std_options_html[] = "<nobr><input id='matchassign_".$table_id."_$sio_k' type='radio' name='match_assign_$table_id' value=$sio_k onclick='javascript: dh_save_match($table_id, $sio_k);' ".cw_datahub_sel_item($table_id, $sio_k, $linked_ids)."><label for='matchassign_".$table_id."_$sio_k'>&nbsp;$sio_title&nbsp;</label></nobr>&nbsp;";
            }
            $match_items_display_array = array(implode('&nbsp;', $std_options_html));

//            foreach ($ext_match_items as $match_k => $match_item) {
            if ($buffer_search['map'] != 'blacklist') {
                if (!empty($row_vals[$match_items_column_id])) {
                    $match_ids = explode(",", $row_vals[$match_items_column_id]);
                    foreach ($match_ids as $match_k => $match_id) {
                                 
                        $mi_string = cw_datahub_match_items_preview(array($match_id), $preview_columns); 

                    //if ($match_k > 3) continue;   
  
                        $mi_string = str_replace("#$match_id", "<b>#$match_id</b>", $mi_string);

                        $match_items_display_array[] = "<nobr><input id='matchassign_".$table_id."_".$match_id."' type='radio' name='match_assign_$table_id' value='$match_id' onclick='javascript: dh_save_match($table_id, $match_id);' ".cw_datahub_sel_item($table_id, $match_id, $linked_ids)."><label for='matchassign_".$table_id."_".$match_id."'>$mi_string</label></nobr>";
                    }   
                }

                $manual_sel_k = 9999999;

                $match_items_display_array[] = "<input id='matchassign_".$table_id."_$manual_sel_k' type='radio' name='match_assign_$table_id' value='$manual_sel_k' onclick=\"javascript: dh_item_select_popup($table_id);\" ".cw_datahub_sel_item($table_id, $manual_sel_k, $linked_ids)."><label for='matchassign_".$table_id."_$manual_sel_k'>Select:&nbsp;<input type='text' class='dh_manual_select_item' id='manual_select_text_$table_id' value='".cw_datahub_man_sel_item($table_id, 1)."' readonly='readonly' onclick='javascript: dh_item_select_popup($table_id);' /></label>";
            }

            $row_vals[$match_items_column_id] = implode('<br />',$match_items_display_array);
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
        cw_datahub_save_match_link($buffer_item_id, $insert_id, true); 
        $added_item_xref = cw_query_first_cell("select item_xref from $tables[datahub_import_buffer] where table_id='$buffer_item_id'"); 

        $buffer_item_image = cw_query_first("select * from $tables[datahub_import_buffer_images] where item_xref='$added_item_xref'");
        if (!empty($buffer_item_image)) {
            unset($buffer_item_image['item_xref']);
            unset($buffer_item_image['id']);   
            $buffer_item_image['item_id'] = $insert_id;
            $image_id = cw_array2insert('datahub_main_data_images', $buffer_item_image);
            cw_array2update('datahub_main_data', array('cimageurl'=>$image_id), "ID='$insert_id'");
        }
    } 

    if ($insert_id && $remove_added) db_query("DELETE FROM $tables[datahub_import_buffer] WHERE table_id='$buffer_item_id'"); 

    return $insert_id;
}

function cw_datahub_save_match_link($table_id, $ID, $del_old = false) {
    global $tables;
    $item_xref = cw_query_first_cell("SELECT item_xref FROM $tables[datahub_import_buffer] WHERE table_id='$table_id'");

    $item_xref_data = cw_query_first("SELECT * FROM $tables[datahub_import_buffer] WHERE table_id='$table_id'");

    if (!empty($item_xref)) {
        if ($del_old) 
            db_query("DELETE FROM item_xref WHERE xref='$item_xref'"); 

        if ($ID == 9999999) {
            $manual_sel_items = &cw_session_register('manual_sel_items', array());
            $ID = $manual_sel_items[$table_id];
        }  

        db_query("REPLACE INTO item_xref (item_id, xref, qty_avail, min_price, bot_per_case, cost_per_case, cost_per_bottle, split_case_charge, store_id, supplier_id) VALUES ('$ID', '$item_xref', '$item_xref_data[item_xref_qty_avail]', '$item_xref_data[item_xref_min_price]', '$item_xref_data[item_xref_bot_per_case]', '$item_xref_data[item_xref_cost_per_case]', '$item_xref_data[item_xref_cost_per_bottle]', '$item_xref_data[split_case_charge]','$item_xref_data[store_id]', '$item_xref_data[supplier_id]')");

    }

    return $ID;
}

function cw_datahub_update_linked_data($selected_table_ids) {
    global $tables, $config;
    $updated_items = 0;

 //   $selected_table_ids = cw_query_column("select table_id from $tables[datahub_import_buffer] order by table_id desc limit 1",'table_id');

    if (!empty($selected_table_ids)) 
      $where_qry = " where ib.table_id in ('".implode("','",$selected_table_ids)."') ";

    $buffer_items = cw_query("select ib.*, md.ID, md.cost as linked_item_cost, md.stock as linked_item_stock from $tables[datahub_import_buffer] ib inner join item_xref ix on ix.xref=ib.item_xref inner join $tables[datahub_main_data] md on md.ID=ix.item_id $where_qry");

    $copy_cfg = cw_query_hash("select * from $tables[datahub_buffer_match_config]", 'mfield', false);
//print_r($buffer_items); print_r($copy_cfg);
    $logged_queries = array();

    foreach ($buffer_items as $b) {
        //update linked item_xref unconditionally
        db_query("UPDATE item_xref SET 
                    qty_avail = '$b[item_xref_qty_avail]', 
                    min_price = '$b[item_xref_min_price]',
                    bot_per_case = '$b[item_xref_bot_per_case]', 
                    cost_per_case = '$b[item_xref_cost_per_case]', 
                    cost_per_bottle = '$b[item_xref_cost_per_bottle]', 
                    split_case_charge = '$b[split_case_charge]', 
		    supplier_id = '$b[supplier_id]'                 
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
                    $src_field = "$tables[datahub_import_buffer].`" . $bfield_data['bfield']. "`";
                    $check_not_minus_one = " and $src_field != -1";
                }

                if ($bfield_data['update_cond'] == 'A') { 

                    db_query($q = "update $tables[datahub_main_data] md, $tables[datahub_import_buffer] set md.`$mfield`=$src_field where md.ID = '$b[ID]' and $tables[datahub_import_buffer].table_id='$b[table_id]' $check_not_minus_one");
                    $logged_queries[] = $q;
                } elseif ($bfield_data['update_cond'] == 'E')  {
                    $dest_val = cw_query_first_cell("select `$mfield` from $tables[datahub_main_data] where ID = '$b[ID]'");
                    $logged_queries[] = "$mfield = $dest_val ".gettype($dest_val);

                    $src_val = cw_query_first_cell("select $src_field from $tables[datahub_import_buffer] where table_id = '$b[table_id]'"); 
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
            $q = "delete from $tables[datahub_import_buffer] where table_id = '$b[table_id]'";   
            db_query($q);
            $logged_queries[] = $q;
        }

        $updated_items++;
    }
    cw_log_add('datahub_update_items', array('buffer_items'=>$buffer_items, 'copy_cfg'=>$copy_cfg, 'logged_queries'=>$logged_queries, 'updated_items'=>$updated_items)); 
    return $updated_items;
}

function cw_datahub_get_blacklist_table() {
    global $tables;
    return $tables['datahub_import_buffer'].'_blacklist';
}

function cw_datahub_clean_buffer_by_blacklist() {
    global $tables;
 
    $blacklist_table = cw_datahub_get_blacklist_table();
    cw_csvxc_logged_query("DELETE $tables[datahub_import_buffer].* FROM $tables[datahub_import_buffer] LEFT JOIN $blacklist_table ON $tables[datahub_import_buffer].item_xref=$blacklist_table.item_xref WHERE $blacklist_table.item_xref IS NOT NULL");
}


function cw_datahub_move_to_blacklist($buffer_item_id, $remove_added = false) {
    global $tables;

    $blacklist_tbl = cw_datahub_get_blacklist_table();
  
    db_query("create table if not exists $blacklist_tbl like $tables[datahub_import_buffer]");

    db_query($insq = "insert into $blacklist_tbl select * from $tables[datahub_import_buffer] where table_id = '$buffer_item_id'");

    $bl_table_id = db_insert_id();

    if ($remove_added) 
        db_query($delq = "delete from $tables[datahub_import_buffer] where table_id = '$buffer_item_id'");
   
    cw_log_add('datahub_move2_blacklist', array($insq, $delq));

    return $bl_table_id; 
}

function cw_datahub_restore_from_blacklist($blacklist_buffer_item_id, $remove_added = false) {
    global $tables;

    $blacklist_tbl = cw_datahub_get_blacklist_table();

    db_query("insert into $tables[datahub_import_buffer] select * from $blacklist_tbl where table_id = '$blacklist_buffer_item_id'");

    $table_id = db_insert_id();

    if ($remove_added)
        db_query("delete from $blacklist_tbl where table_id = '$blacklist_buffer_item_id'");

    return $table_id;
}

function cw_datahub_erase_blacklisted_items() {
    global $tables;

    $blacklist_tbl = cw_datahub_get_blacklist_table();

    $bl_table_ids = cw_query_column("select $tables[datahub_import_buffer].table_id from $tables[datahub_import_buffer] inner join $blacklist_tbl on $blacklist_tbl.item_xref=$tables[datahub_import_buffer].item_xref", 'table_id');
    db_query("delete from $tables[datahub_import_buffer] where table_id in ('".implode("','", $bl_table_ids)."')");

    cw_log_add('datahub_erase_blacklist', array('blacklist_table_ids' =>$bl_table_ids));

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

function cw_datahub_import_buffer_view_table_name() {
    global $tables;
    global $dh_buffer_table_fields;

    $table = $tables['datahub_import_buffer'];

    $src_buffer_table = $tables['datahub_import_buffer'];

    //db_query("DELETE from $tables[datahub_match_links] WHERE catalog_id NOT IN (SELECT ID FROM $tables[datahub_main_data])");

    $buffer_search = &cw_session_register('buffer_search', array());

    if (!empty($buffer_search)) {

        db_query("drop view if exists cw_view_datahub_import_buffer");

        $buffer_where = array(1);
        $buffer_join = array();

        if (!empty($buffer_search['map'])) {
            if ($buffer_search['map'] == 'not_mapped') {
                $buffer_join[] = " left join item_xref on $src_buffer_table.item_xref=item_xref.xref";
                $buffer_where[] = "item_xref.item_id is null";
            } elseif ($buffer_search['map'] == 'mapped') {
                $buffer_join[] = " left join item_xref on $src_buffer_table.item_xref=item_xref.xref";
                $buffer_where[] = "item_xref.item_id is not null";
//                $buffer_join[] = " left join $tables[datahub_main_data] on $tables[datahub_match_links].catalog_id=$tables[datahub_main_data].ID";
//                $buffer_where[] = "$tables[datahub_main_data].ID is not null";
            } elseif ($buffer_search['map'] == 'new') {
                $buffer_match_pre_save = &cw_session_register('buffer_match_pre_save', array());
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

        $buffer_where_sql = implode(' AND ', $buffer_where);
        $buffer_join_sql = implode(' ', $buffer_join);

//        db_query("create table if not exists cw_view_datahub_import_buffer like $src_buffer_table");  
//        db_query("insert into cw_view_datahub_import_buffer (SELECT $src_buffer_table.* FROM $src_buffer_table $buffer_join_sql WHERE $buffer_where_sql)");
      db_query("drop view if exists cw_view_datahub_import_buffer");
      db_query("create view cw_view_datahub_import_buffer as (SELECT $src_buffer_table.* FROM $src_buffer_table $buffer_join_sql WHERE $buffer_where_sql)");

        // DB table to use
        $table = "cw_view_datahub_import_buffer";
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

    $dh_product_saved_values = &cw_session_register('dh_product_saved_values', array());

//    cw_log_add('datahub_on_product_modify_end', array('product_id' => $product_id, $dh_product_saved_values[$product_id]));

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
    //cw_log_add('datahub_update_hub_data', array($product_id, $value, $field_def));

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

    $app_path = '/home/devsarat/public_html/';

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

cw_csvxc_logged_query("DELETE FROM feeds_item_compare");

cw_csvxc_logged_query("INSERT INTO feeds_item_compare (`".implode("`, `", array_keys($dhmd2fic))."`) SELECT ".implode(", ", $dhmd2fic)." FROM $tables[datahub_main_data]");
cw_csvxc_logged_query("UPDATE feeds_item_compare SET Source='Hub' WHERE catalogid!=0");
cw_csvxc_logged_query("UPDATE feeds_item_compare SET add_to_hub=1 WHERE catalogid=0");

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

