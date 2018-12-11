<?php

$presel_buffer_item = &cw_session_register('presel_buffer_item', 0);

if ($mode == 'view') {

    $presel_buffer_item = $table_id;

    $buffer_view_table = cw_datahub_import_buffer_view_table_name();

    $buffer_item = cw_query_first("SELECT * from $buffer_view_table where table_id='$table_id'");

    $buffer_table_quick_preview_fields = cw_datahub_buffer_table_filter_fields('quick_preview');

    if (!in_array('table_id', $buffer_table_quick_preview_fields))
        $buffer_table_quick_preview_fields[] = 'table_id';

    $buffer_item_display = array();
    foreach ($buffer_item as $fname=>$fval) {
        if (in_array($fname, $buffer_table_quick_preview_fields)) 
            $buffer_item_display[$fname] = $fval;
    }

    $buffer_item['display'] = cw_datahub_buffer_item_quick_display($buffer_item_display);

    $buffer_item['image'] = cw_query_first("select * from $tables[datahub_import_buffer_images] where item_xref='$buffer_item[item_xref]' limit 1");

    if (!empty($buffer_item['image'])) { 
        foreach (array('filename', 'web_path', 'system_path') as $img_fld) {
            $buffer_item['image'][$img_fld] = "/".ltrim($buffer_item['image'][$img_fld], "/");
        }  
    }

    $smarty->assign('buffer_item', $buffer_item);

    $match_items_column_id = 'Match Items';

    $match_ids = array();

    if (!empty($buffer_item[$match_items_column_id])) {
        $match_ids = explode(",", $buffer_item[$match_items_column_id]);
    }

    $linked_ids = cw_query_column("select dhml.catalog_id from $tables[datahub_match_links] dhml inner join $tables[datahub_import_buffer] dhib on dhib.item_xref = dhml.item_xref and dhib.table_id='$table_id' inner join $tables[datahub_main_data] dhmd on dhmd.ID=dhml.catalog_id", "catalog_id");
    if (!empty($linked_ids))
        $match_ids = array_merge($linked_ids, $match_ids);

    if (!empty($match_ids)) {

        $manual_sel_items = cw_dh_session_register('manual_sel_items', array());
        if ($manual_sel_items[$table_id])
            $match_ids[] = $manual_sel_items[$table_id];

        cw_dh_session_save('manual_sel_items', $manual_sel_items);   

        $merge_src = array();
        //$preview_columns = cw_datahub_main_table_filter_fields('buffer_match_preview');
        $preview_columns = cw_datahub_get_preview_columns('');
        foreach ($match_ids as $match_k => $match_id) {
            $mi_string = cw_datahub_match_items_preview(array($match_id), $preview_columns);
            $mi_string = str_replace("#$match_id", "<b>#$match_id</b>", $mi_string);
            $merge_src[$match_id] = $mi_string;
        }
        $smarty->assign('merge_src', $merge_src);
    }

    $smarty->assign('table_id', $table_id);

    $_dh_buffer_table_fields = $dh_buffer_table_fields;
    $_dh_buffer_table_fields['image'] = array('title'=>'Image', 'edit_type'=>'image');

    $smarty->assign('dh_buffer_table_fields', $_dh_buffer_table_fields);

    $bmerge_config = cw_query_hash("select * from $tables[datahub_buffer_merge_config]", 'bfield', false);
    $bmerge_config['image'] = array('mfield'=>'image');
    $smarty->assign('bmerge_config', $bmerge_config);

    $smarty->assign('pre_hide_columns', cw_datahub_load_hide_columns('buffer_edit', array()));

    cw_add_ajax_block(array(
        'id'        => 'dh_buffer_edit_area',
        'action'    => 'update',
        'template'  => 'addons/flexible_import/datahub/edit_buffer_item.tpl'
    ));
}
