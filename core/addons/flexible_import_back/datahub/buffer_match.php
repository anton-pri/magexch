<?php

$buffer_match_pre_save = &cw_session_register('buffer_match_pre_save', array());

if ($REQUEST_METHOD == 'POST') {
    if ($action == "clean_buffer_data") {
        db_query("delete from $tables[datahub_import_buffer]");
        cw_add_top_message("The import buffer table has been emptied",'I'); 
    } elseif ($action == 'update_linked_data') {
        if ($updated_items_count = cw_datahub_update_linked_data(array())) {
            cw_add_top_message("$updated_items_count items have been updated in main data table",'I');
        } else {
            cw_add_top_message("No items found for update, please make sure there are import items with match links in buffer",'E'); 
        }
    } elseif ($action == 'apply_match') {
        $new_added_cnt = 0;
        $bl_added_cnt = 0;
        $linked_cnt = 0;
        $restored_cnt = 0;
        $top_message_text = '';
/*
$manual_sel_items = &cw_session_register('manual_sel_items', array());
print_r($manual_sel_items);
print_r($buffer_match_pre_save); die;
*/
        foreach ($buffer_match_pre_save as $buffer_item_id => $sel) {
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
  //print_r($buffer_match_pre_save); die;
        if ($new_added_cnt) { 
            if ($new_added_cnt > 1) 
                $top_message_text .= "$new_added_cnt new items were added to the main data table \n";   
            else
                $top_message_text .= "New item was added to the main data table \n"; 
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
        $buffer_search = &cw_session_register('buffer_search', array()); 
 
        foreach ($adv_filter as $search_key => $filter_val) { 
            $buffer_search[$search_key] = $filter_val;
        }
        if ($is_edit == "Y")
           cw_header_location("index.php?target=datahub_buffer_match_edit"); 
    }
    cw_header_location("index.php?target=datahub_buffer_match");
}

$loaded_profiles = cw_flexible_import_recurring_imports($time);
if (!empty($loaded_profiles)) {
    cw_header_location("index.php?target=datahub_buffer_match");
}

//cw_datahub_prepare_buffer_matches(true);

$buffer_search = &cw_session_register('buffer_search', array());

$smarty->assign('buffer_search', $buffer_search);

$erased_black_list_items_count = cw_datahub_erase_blacklisted_items();

$default_hidden_buffer_columns = array('Source', 'Wine', 'ITEMID', 'country', 'varietal', 'sub-appellation','Appellation' ,'feed_short_name');

$smarty->assign('pre_hide_columns', cw_datahub_load_hide_columns('buffer', $default_hidden_buffer_columns));

$smarty->assign('buffer_tbl_fields', cw_datahub_get_buffer_table_fields());

$smarty->assign('dh_buffer_table_fields', $dh_buffer_table_fields);

$smarty->assign('main', 'datahub_buffer_match');
