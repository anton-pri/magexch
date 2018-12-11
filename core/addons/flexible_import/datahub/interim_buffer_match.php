<?php

set_time_limit(86400);

$interim_buffer_match_pre_save = cw_dh_session_register('interim_buffer_match_pre_save', array());

if ($REQUEST_METHOD == 'POST') {
    if ($action == "clean_buffer_data") {
        $interim_buffer_search = &cw_session_register('interim_buffer_search', array());
        $interim_buffer_search = array();
        cw_dh_session_save('interim_buffer_match_pre_save', array());
        cw_dh_session_save('interim_manual_sel_items', array());

        db_query("drop table if exists item_xref_buffer_feeds_interim");

        db_query("delete from $tables[datahub_interim_import_buffer]");
        cw_add_top_message("The interim import buffer table has been emptied",'I'); 
    } elseif ($action == 'update_linked_data') {

        $ix_table_name = cw_flexible_import_ix_table_name(true);

        if ($updated_items_count = cw_query_first_cell("select count(*) from $tables[datahub_interim_import_buffer] ib inner join $ix_table_name ix on ix.xref=ib.item_xref inner join cw_datahub_main_data md on md.ID=ix.item_id")/*cw_datahub_update_linked_data(array())*/) {
            cw_header_location("index.php?target=datahub_interim_buffer_match&action=update_linked_data&uld_step=1");
          //  cw_add_top_message("$updated_items_count items have been updated in main data table",'I');
        } else {
            cw_add_top_message("No items found for update, please make sure there are import items with match links in interim buffer",'E');
        }
    } elseif ($action == 'apply_update_nonstock_match') {

        foreach ($interim_buffer_match_pre_save as $buffer_item_id => $sel) {

            if (!cw_query_first_cell("select count(*) from $tables[datahub_interim_import_buffer] where table_id='$buffer_item_id'")) {
                unset($interim_buffer_match_pre_save[$buffer_item_id]);
                continue;
            }

            if ($sel > 0) {
                $_check_id = cw_datahub_save_update_nonstock_match_link($buffer_item_id, $sel, true);

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
        foreach ($interim_buffer_match_pre_save as $buffer_item_id => $sel) {

            if (!cw_query_first_cell("select count(*) from $tables[datahub_interim_import_buffer] where table_id='$buffer_item_id'")) {
                unset($interim_buffer_match_pre_save[$buffer_item_id]);
                continue;
            }

            if ($sel == -1) {
                //add new to main data and remove from buffer 
                //$newID = cw_datahub_add_buffer_to_main ($buffer_item_id, true);

                //unset($interim_buffer_match_pre_save[$buffer_item_id]);

                //print("buffer_item_id $buffer_item_id newID $newID |");    
                //if ($newID) $new_added_cnt++;

                $new_added_cnt++;

            } elseif ($sel == -2) {
                //move to blacklist  
                $bl_table_id = cw_datahub_move_to_blacklist($buffer_item_id, true); 
                unset($interim_buffer_match_pre_save[$buffer_item_id]);
                if ($bl_table_id) $bl_added_cnt++;
            } elseif ($sel == -3) {
                $restored_table_id = cw_datahub_restore_from_blacklist($buffer_item_id, true);
                unset($interim_buffer_match_pre_save[$buffer_item_id]);
                if ($restored_table_id) $restored_cnt++;  
            } elseif ($sel > 0) {
                $_check_id = cw_datahub_save_match_link($buffer_item_id, $sel, true);
                if ($_check_id)
                    $top_message_text .= "The buffer item #$buffer_item_id has been mapped to hub item #$_check_id \n";

                unset($interim_buffer_match_pre_save[$buffer_item_id]);
            }
        }
  //print_r($interim_buffer_match_pre_save); die;
        cw_dh_session_save('interim_buffer_match_pre_save', $interim_buffer_match_pre_save);

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
        $interim_buffer_search = &cw_session_register('interim_buffer_search', array()); 
 
        foreach ($adv_filter as $search_key => $filter_val) { 
            $interim_buffer_search[$search_key] = $filter_val;
        }
    } elseif ($action == "move2working_buffer_data") {
        $_buffer_search = &cw_session_register('interim_buffer_search', array());

        $buffer_search = array();
        if (!empty($_buffer_search['source']))
             $buffer_search['source'] = $_buffer_search['source'];  

        //$buffer_search = $_buffer_search;

        $int_buffer_tbl_fields = cw_check_field_names(array(), $tables['datahub_interim_import_buffer']);

        $table_id_col_id = array_search();

        if(($table_id_col_id = array_search('table_id', $int_buffer_tbl_fields)) !== false) {
           unset($int_buffer_tbl_fields[$table_id_col_id]);
        }

        $filtered_query = cw_datahub_import_buffer_query($tables['datahub_interim_import_buffer'], $buffer_search, array('table_id'));
        cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS cw_datahub_import_buffer_temp_ids (table_id int(11) not null default 0, PRIMARY KEY (table_id))");
        cw_csvxc_logged_query("truncate table cw_datahub_import_buffer_temp_ids");
        cw_csvxc_logged_query("insert into cw_datahub_import_buffer_temp_ids ($filtered_query)");

        cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS cw_datahub_import_buffer_temp_ids_linked (table_id int(11) not null default 0, PRIMARY KEY (table_id))");
        cw_csvxc_logged_query("truncate table cw_datahub_import_buffer_temp_ids_linked");
        cw_csvxc_logged_query("insert into cw_datahub_import_buffer_temp_ids_linked (table_id) select ib.table_id from $tables[datahub_interim_import_buffer] ib inner join item_xref ix on ix.xref=ib.item_xref inner join cw_datahub_main_data md on md.ID=ix.item_id");
        cw_csvxc_logged_query("delete cw_datahub_import_buffer_temp_ids.* from cw_datahub_import_buffer_temp_ids, cw_datahub_import_buffer_temp_ids_linked where cw_datahub_import_buffer_temp_ids.table_id=cw_datahub_import_buffer_temp_ids_linked.table_id");

        $filtered_query = "SELECT table_id FROM cw_datahub_import_buffer_temp_ids";

        $moved_sources = cw_query_column("SELECT DISTINCT(Source) FROM $tables[datahub_interim_import_buffer] WHERE table_id IN ($filtered_query)");
        $src2codes = array('Feed_Vias'=>'CWVIAS-', 'Feed_Touton'=>'TOUT-', 'Feed_Opici'=>'CWOPC-', 'Feed_Polaner'=>'POLA-', 'Feed_Wildman'=>'WDMN-', 'Feed_Verity'=>'VERITY-');
//print_r($moved_sources);
        foreach ($moved_sources as $b_source) { 
//           cw_csvxc_logged_query("delete from $tables[datahub_import_buffer] where Source='$b_source'");
            //print("delete from $tables[datahub_import_buffer] where Source='$b_source'"); 
/*
            if ($b_source == 'Feed_BEVA') {
               //print("UPDATE item_xref SET qty_avail = '0' WHERE CAST(Left(coalesce(item_xref,'xxx'),3) as SIGNED) > 0"); 
               cw_csvxc_logged_query("UPDATE item_xref SET qty_avail = '0' WHERE CAST(Left(coalesce(xref,'xxx'),3) as SIGNED) > 0");
            } elseif (in_array($b_source, array_keys($src2codes))) {
               //print("UPDATE item_xref AS i SET qty_avail = '0' WHERE xref like '".$src2codes[$b_source]."%'");
               cw_csvxc_logged_query("UPDATE item_xref AS i SET qty_avail = '0' WHERE xref like '".$src2codes[$b_source]."%'");
            }
*/
        }  

        cw_csvxc_logged_query("REPLACE INTO $tables[datahub_import_buffer] (`".implode("`, `", $int_buffer_tbl_fields)."`) SELECT `".implode("`, `", $int_buffer_tbl_fields)."` FROM $tables[datahub_interim_import_buffer] WHERE table_id IN ($filtered_query)");

        $processed_table_ids = cw_query_column("SELECT table_id FROM cw_datahub_import_buffer_temp_ids");

        $interim_buffer_match_pre_save = cw_dh_session_register('interim_buffer_match_pre_save', array()); 
        $interim_manual_sel_items = cw_dh_session_register('interim_manual_sel_items', array()); 

        $buffer_match_pre_save = cw_dh_session_register('buffer_match_pre_save', array());
        $manual_sel_items = cw_dh_session_register('manual_sel_items', array());

        foreach ($interim_buffer_match_pre_save as $i_tab_id => $i_val) {

            if (!cw_query_first_cell("select count(*) from $tables[datahub_interim_import_buffer] where table_id='$i_tab_id'")) {
                unset($interim_buffer_match_pre_save[$i_tab_id]);
                continue;
            }

            if (!in_array($i_tab_id, $processed_table_ids)) continue;
            $i_item_xref = cw_query_first_cell("SELECT item_xref FROM $tables[datahub_interim_import_buffer] WHERE table_id=$i_tab_id");
            $w_tab_id = cw_query_first_cell("SELECT table_id FROM $tables[datahub_import_buffer] WHERE item_xref='$i_item_xref'");
            unset($interim_buffer_match_pre_save[$i_tab_id]);     
            $buffer_match_pre_save[$w_tab_id] = $i_val;
        }  

        foreach ($interim_manual_sel_items as $i_tab_id => $i_val) {
            if (!in_array($i_tab_id, $processed_table_ids)) continue;
            $i_item_xref = cw_query_first_cell("SELECT item_xref FROM $tables[datahub_interim_import_buffer] WHERE table_id=$i_tab_id");
            $w_tab_id = cw_query_first_cell("SELECT table_id FROM $tables[datahub_import_buffer] WHERE item_xref='$i_item_xref'");
            unset($interim_manual_sel_items[$i_tab_id]);
            $manual_sel_items[$w_tab_id] = $i_val;
        }  
        cw_dh_session_save('interim_buffer_match_pre_save', $interim_buffer_match_pre_save);
        cw_dh_session_save('interim_manual_sel_items', $interim_manual_sel_items);
        cw_dh_session_save('buffer_match_pre_save', $buffer_match_pre_save);
        cw_dh_session_save('manual_sel_items', $manual_sel_items);


        cw_csvxc_logged_query("DELETE FROM $tables[datahub_interim_import_buffer] WHERE table_id IN ($filtered_query)");

        unset($_buffer_search['source']);
        cw_add_top_message("Interim import buffer items have been moved to live",'I');
    } elseif ($action == "enable_background_matches_search") {
        db_query("UPDATE $tables[config] SET value='Y' WHERE name='fi_sheduled_generate_matches'");
        cw_add_top_message("Enabled background matches search for interim buffer",'I');
    } elseif ($action == "disable_background_matches_search") {
        db_query("UPDATE $tables[config] SET value='N' WHERE name='fi_sheduled_generate_matches'");
        cw_add_top_message("Disabled background matches search for interim buffer",'I');
    }
    cw_header_location("index.php?target=datahub_interim_buffer_match");
} else {
    if ($action == 'update_linked_data') {
        cw_datahub_delay_autoupdate("target=datahub_interim_buffer_match&action=update_linked_data&uld_step=$uld_step");

        $ix_table_name = cw_flexible_import_ix_table_name(true);

        $mapped_table_ids = cw_query_column("select ib.table_id from $tables[datahub_interim_import_buffer] ib inner join $ix_table_name ix on ix.xref=ib.item_xref inner join cw_datahub_main_data md on md.ID=ix.item_id limit 100");
        if (!empty($mapped_table_ids) && $uld_step<500) {
            cw_flush("<h1>Updating mapped items...</h1><br />");
            cw_flush(implode(", ", $mapped_table_ids));
            cw_datahub_update_linked_data($mapped_table_ids, true);
            $uld_step++;
            cw_datahub_delay_autoupdate_release_lock();
            cw_header_location("index.php?target=datahub_interim_buffer_match&action=update_linked_data&uld_step=$uld_step");
        } else {
            cw_datahub_delay_autoupdate_release_lock();
            cw_add_top_message("All items have been updated in main data table",'I');
            cw_header_location("index.php?target=datahub_interim_buffer_match");
        }
    }
}


if ($reload_profiles_if_new == "Y") {
    $loaded_profiles = cw_flexible_import_recurring_imports_interim($time);
    if (!empty($loaded_profiles)) {
        $ld_prof_names = array();   
        foreach ($loaded_profiles as $ld_prof) {
            $ld_prof_names[] = $ld_prof['name'];  
        }
        cw_add_top_message("New files are found and loaded for profile(s): ".implode("', '", $ld_prof_names),'I'); 
        cw_header_location("index.php?target=datahub_interim_buffer_match");
    } else {
        cw_add_top_message("No new files are found in active recurring profiles' folders",'I');
        cw_header_location("index.php?target=datahub_interim_buffer_match");
    }
}

//cw_datahub_prepare_buffer_matches(true);

$interim_buffer_search = &cw_session_register('interim_buffer_search', array());

$smarty->assign('buffer_search', $interim_buffer_search);

$erased_black_list_items_count = cw_datahub_erase_blacklisted_items();

$default_hidden_buffer_columns = array('Source', 'Wine', 'ITEMID', 'country', 'varietal', 'sub-appellation','Appellation' ,'feed_short_name');

$smarty->assign('pre_hide_columns', cw_datahub_load_hide_columns('buffer', $default_hidden_buffer_columns));

$smarty->assign('buffer_tbl_fields', cw_datahub_get_buffer_table_fields(cw_datahub_import_buffer_view_table_name(true)));

$smarty->assign('dh_buffer_table_fields', $dh_buffer_table_fields);

$buffer_ids_count = cw_query_first_cell("select count(*) from $tables[datahub_interim_import_buffer]");
if ($buffer_ids_count) { 
    $buffer_ids_count_no_match = cw_query_first_cell("select count(*) from $tables[datahub_interim_import_buffer] where `Match Items` = '' and item_xref not in (select xref from item_xref)");
    $smarty->assign('buffer_ids_count_no_match', $buffer_ids_count_no_match);
    $smarty->assign('portion2gen_match',round(100*$buffer_ids_count_no_match/$buffer_ids_count,2));
}
$smarty->assign('buffer_ids_count', $buffer_ids_count);


$lock_file_path = $config['flexible_import']['flex_import_files_folder'].$interim_ext.'/gen_matches.lock';
if (file_exists($lock_file_path)) {
    $smarty->assign('last_gen_match_start', file_get_contents($lock_file_path));
    $smarty->assign('gen_match_runs', time() - intval($last_gen_match_start));
}

$smarty->assign('current_match_items_limit', cw_datahub_max_display_matches());
$smarty->assign('match_items_limit_options', array(4, 8, 12, 20));


$smarty->assign('interim_ext', 'interim_');
$smarty->assign('main', 'datahub_interim_buffer_match');
