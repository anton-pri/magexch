<?php
set_time_limit(86400);
$buffer_match_pre_save = cw_dh_session_register('buffer_match_pre_save', array());

if ($REQUEST_METHOD == 'POST') {
    if ($action == "clean_buffer_data") {

        db_query("drop table if exists item_xref_buffer_feeds");

        db_query("delete from $tables[datahub_import_buffer]");
        cw_add_top_message("The import buffer table has been emptied",'I'); 
    } elseif ($action == 'update_linked_data') {

        $ix_table_name = cw_flexible_import_ix_table_name(false);

        if ($updated_items_count = cw_query_first_cell("select count(*) from cw_datahub_import_buffer ib inner join $ix_table_name ix on ix.xref=ib.item_xref inner join cw_datahub_main_data md on md.ID=ix.item_id")/*cw_datahub_update_linked_data(array())*/) {
            cw_header_location("index.php?target=datahub_buffer_match&action=update_linked_data&uld_step=1");
          //  cw_add_top_message("$updated_items_count items have been updated in main data table",'I');
        } else {
            cw_add_top_message("No items found for update, please make sure there are import items with match links in buffer",'E'); 
        }
    } elseif ($action == 'apply_update_nonstock_match') {

        foreach ($buffer_match_pre_save as $buffer_item_id => $sel) {

            if (!cw_query_first_cell("select count(*) from $tables[datahub_import_buffer] where table_id='$buffer_item_id'")) {
                unset($buffer_match_pre_save[$buffer_item_id]);
                continue;
            }

            if ($sel > 0) {
                $_check_id = cw_datahub_save_update_nonstock_match_link($buffer_item_id, $sel);

                if ($_check_id)
                    $top_message_text .= "The buffer item #$buffer_item_id has been mapped to hub item #$_check_id with non-stock link\n";

            }
        }

        if ($top_message_text != '')
            cw_add_top_message($top_message_text,'I');
        else
            cw_add_top_message('None of imported items has been changed','E');
  
    } elseif ($action == 'apply_match') {
        $new_added_cnt = 0;
        $bl_added_cnt = 0;
        $linked_cnt = 0;
        $restored_cnt = 0;
        $top_message_text = '';
        foreach ($buffer_match_pre_save as $buffer_item_id => $sel) {
            
            if ($sel != -3 && !cw_query_first_cell("select count(*) from $tables[datahub_import_buffer] where table_id='$buffer_item_id'")) {
                unset($buffer_match_pre_save[$buffer_item_id]);
                continue;
            }     

            if ($sel == -1) {
                //add new to main data and remove from buffer 
                //$newID = cw_datahub_add_buffer_to_main ($buffer_item_id, true);

                //unset($buffer_match_pre_save[$buffer_item_id]);

                //print("buffer_item_id $buffer_item_id newID $newID |");    
                //if ($newID) $new_added_cnt++;

                $new_added_cnt++;

            } elseif ($sel == -2) {
                //move to blacklist  
                $bl_table_id = cw_datahub_move_to_blacklist($buffer_item_id, true); 
                unset($buffer_match_pre_save[$buffer_item_id]);
                if ($bl_table_id) $bl_added_cnt++;
            } elseif ($sel == -3) {
                $restored_table_id = cw_datahub_restore_from_blacklist($buffer_item_id, true);
                unset($buffer_match_pre_save[$buffer_item_id]);
                if ($restored_table_id) $restored_cnt++;  
            } elseif ($sel > 0) {
                $_check_id = cw_datahub_save_match_link($buffer_item_id, $sel, true);
                if ($_check_id)
                    $top_message_text .= "The buffer item #$buffer_item_id has been mapped to hub item #$_check_id \n";

                unset($buffer_match_pre_save[$buffer_item_id]);
            }
        }
        cw_dh_session_save('buffer_match_pre_save', $buffer_match_pre_save);

        if ($new_added_cnt) { 
            if ($new_added_cnt > 1) 
                $top_message_text .= "$new_added_cnt items were marked as new \n";   
            else
                $top_message_text .= "1 item was marked as new \n"; 
        }

        if ($bl_added_cnt) {
            if ($bl_added_cnt > 1) 
                $top_message_text .= "$bl_added_cnt items were added to the black list table \n";
            else
                $top_message_text .= "The item was added to the black list table \n";
        } 

        if ($restored_cnt) {
            if ($restored_cnt > 1) 
                $top_message_text .= "$restored_cnt items were restored from the black list table \n";
            else
                $top_message_text .= "The item was restored from the black list table \n";
        }  

        if ($top_message_text != '')
            cw_add_top_message($top_message_text,'I');
        else
            cw_add_top_message('None of imported items has been changed','E');
    } elseif ($action == "apply_filter") {
        $buffer_search = &cw_session_register('buffer_search', array('map'=>'not_mapped')); 
 
        foreach ($adv_filter as $search_key => $filter_val) { 
            $buffer_search[$search_key] = $filter_val;
        }

        cw_session_save();

        if ($is_edit == "Y")
           cw_header_location("index.php?target=datahub_buffer_match_edit"); 
    }
    cw_header_location("index.php?target=datahub_buffer_match");
} else {
    if ($action == 'update_linked_data') {
        cw_datahub_delay_autoupdate("target=datahub_buffer_match&action=update_linked_data&uld_step=$uld_step");

        $ix_table_name = cw_flexible_import_ix_table_name(false);

        $mapped_table_ids = cw_query_column("select ib.table_id from cw_datahub_import_buffer ib inner join $ix_table_name ix on ix.xref=ib.item_xref inner join cw_datahub_main_data md on md.ID=ix.item_id limit 100"); 
        if (!empty($mapped_table_ids) && $uld_step<500) {
            cw_flush("<h1>Updating mapped items...</h1><br />");              
            cw_flush(implode(", ", $mapped_table_ids)); 
            cw_datahub_update_linked_data($mapped_table_ids);
            $uld_step++;
            cw_datahub_delay_autoupdate_release_lock(); 
            cw_header_location("index.php?target=datahub_buffer_match&action=update_linked_data&uld_step=$uld_step"); 
        } else {
            cw_datahub_delay_autoupdate_release_lock();
            cw_add_top_message("All items have been updated in main data table",'I');
            cw_header_location("index.php?target=datahub_buffer_match");
        }
    }
}
/*
$loaded_profiles = cw_flexible_import_recurring_imports($time);
if (!empty($loaded_profiles)) {
    cw_header_location("index.php?target=datahub_buffer_match");
}
*/
//cw_datahub_prepare_buffer_matches(true);
if ($reload_profiles_if_new == "Y") {
    $loaded_profiles = cw_flexible_import_recurring_imports($time);
    if (!empty($loaded_profiles)) {
        $ld_prof_names = array();
        foreach ($loaded_profiles as $ld_prof) {
            $ld_prof_names[] = $ld_prof['name'];
        }
        cw_add_top_message("New files are found and loaded for profile(s): ".implode("', '", $ld_prof_names),'I');
        cw_header_location("index.php?target=datahub_buffer_match");
    } else {
        cw_add_top_message("No new files are found in active recurring profiles' folders",'I');
        cw_header_location("index.php?target=datahub_buffer_match");
    }
}



$buffer_search = &cw_session_register('buffer_search', array('map'=>'not_mapped'));

$smarty->assign('buffer_search', $buffer_search);

$erased_black_list_items_count = cw_datahub_erase_blacklisted_items();

$default_hidden_buffer_columns = array('Source', 'Wine', 'ITEMID', 'country', 'varietal', 'sub-appellation','Appellation' ,'feed_short_name');

$smarty->assign('pre_hide_columns', cw_datahub_load_hide_columns('buffer', $default_hidden_buffer_columns));

$smarty->assign('buffer_tbl_fields', cw_datahub_get_buffer_table_fields(cw_datahub_import_buffer_view_table_name()));

$smarty->assign('dh_buffer_table_fields', $dh_buffer_table_fields);

$smarty->assign('current_match_items_limit', cw_datahub_max_display_matches());
$smarty->assign('match_items_limit_options', array(4, 8, 12, 20));

$smarty->assign('main', 'datahub_buffer_match');
