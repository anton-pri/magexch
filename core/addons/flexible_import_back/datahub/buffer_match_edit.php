<?php

$presel_buffer_item = &cw_session_register('presel_buffer_item', 0);

if ($mode == 'update') {

    if (!empty($edit_buffer_item[$saved_table_id])) {

        $new_image = ltrim($edit_buffer_item[$saved_table_id]['image'], "/");
        unset($edit_buffer_item[$saved_table_id]['image']);

        $item_xref = cw_query_first_cell("select item_xref from $tables[datahub_import_buffer] where table_id='$saved_table_id'");
        if (!empty($item_xref)) { 
            db_query("delete from $tables[datahub_import_buffer_images] where item_xref='$item_xref'");
            cw_array2insert('datahub_import_buffer_images', 
                array('filename'=>$new_image, 'filesize'=>0, 'web_path'=>$new_image, 'system_path'=>$new_image, 'item_xref'=>$item_xref), true
            );
        }

        cw_array2update('datahub_import_buffer', $edit_buffer_item[$saved_table_id] ,"table_id='$saved_table_id'");  
    
        if ($add_as_new == 'Y') {
            $buffer_match_pre_save = &cw_session_register('buffer_match_pre_save', array());
            //add new to main data and remove from buffer 
            $newID = cw_datahub_add_buffer_to_main ($saved_table_id, true);
            if (isset($buffer_match_pre_save[$saved_table_id])) 
                unset($buffer_match_pre_save[$saved_table_id]);

            if (isset($switch2next)) {
                $presel_buffer_item = intval($switch2next);
            }

            cw_add_top_message("Successfully added new item #$newID",'I');
        }
    }

    cw_add_ajax_block(array(
        'id'        => 'dh_buffer_edit_area',
        'action'    => 'update',
        'template'  => 'addons/flexible_import/datahub/edit_buffer_item_saved.tpl'
    ));
}

$buffer_search = &cw_session_register('buffer_search', array());
if ($buffer_search['map'] == 'blacklist') 
    cw_header_location('index.php?target=datahub_buffer_match');

$smarty->assign('buffer_search', $buffer_search);

$buffer_items = cw_datahub_buffer_items_to_edit();
$smarty->assign('buffer_items', $buffer_items);
$smarty->assign('buffer_items_count', count($buffer_items));

if (isset($presel_table_id))
    $presel_buffer_item = intval($presel_table_id);

$smarty->assign('presel_buffer_item', intval($presel_buffer_item));
$smarty->assign('dh_buffer_table_fields', $dh_buffer_table_fields);
$smarty->assign('main', 'datahub_buffer_match_edit');
