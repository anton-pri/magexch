<?php

//global $datahub_browse_tables;

function cw_dh_browser_get_current_table() {
    global $datahub_browse_tables;
 
    $dh_curr_browse_table = &cw_session_register('dh_curr_browse_table',0);
    global $_browse_table;
    if ($_browse_table)
        $dh_curr_browse_table = $_browse_table;

    if (!$dh_curr_browse_table)
        $dh_curr_browse_table = reset($datahub_browse_tables);


    return $dh_curr_browse_table;
}


function cw_dt_primary_key_dh_browser($table='') {
    if (!empty($table))
        $tbl = $table;
    else 
        $tbl = cw_dh_browser_get_current_table();

    $keys = cw_query_first("SHOW KEYS FROM `$tbl` WHERE Key_name = 'PRIMARY'");

    return $keys['Column_name'];
}
/*
function cw_dt_table_dh_browser() {
    return cw_dh_browser_get_current_table();
}
*/

function cw_dt_field_defs_dh_browser() {
    return array();
}

function cw_dh_browse_fields_by_table($table_name) {
    $result = array();

    global $dh_browse_linked_keys;

    foreach ($dh_browse_linked_keys as $k_group=>$_keys) {
        foreach ($_keys as $tbl_name=>$f_name) {
            if ($tbl_name == $table_name) {
                $result[] = $f_name;
            }
        }
    }

    return $result;
}

function cw_dt_postprocess_dh_browser($sent_data, $column_ids) {
    global $datatable_mode;
    global $dt_rsrc;
    global $datatable_resources_config;

    $browsed_table = $datatable_resources_config[$dt_rsrc]['table_name'];
    $browsed_table_fields = cw_dh_browse_fields_by_table($browsed_table);

    if (is_array($sent_data['data'])) {

        foreach ($sent_data['data'] as $row_id => $row_vals) {

            foreach($browsed_table_fields as $col_name) {

                if (!isset($column_ids[$col_name])) continue;

                $_col_id = $column_ids[$col_name];
                $_orig_val = $row_vals[$_col_id];

//                if ($_orig_val == 0 || $_orig_val == '') continue;

                if ($datatable_mode != 'export') {
                    $row_vals[$_col_id] = "<a target='_blank' href='index.php?target=datahub_browse_links&dh_table=$browsed_table&dh_column=$col_name&dh_value=$_orig_val'>$_orig_val</a>";
                }    

            }
            $sent_data['data'][$row_id] = $row_vals;
        }
    }

    return $sent_data;
}

function cw_dt_dh_browser_qpib() {
    global $tables;
    $result = $tables['qbwc_pos_items_buffer'].'_view';
    db_query("DROP TABLE IF EXISTS $result");
    db_query("CREATE TABLE IF NOT EXISTS $result AS SELECT qpib.*, REPLACE(`X-REF`,' ','') as `X-REFt` FROM $tables[qbwc_pos_items_buffer] qpib");
    return $result;
}

global $datatable_resources_config;

global $datahub_browse_tables;

foreach ($datahub_browse_tables as $br_table => $br_table_def) {
 
    if (is_array($br_table_def)) { 
        if (function_exists($br_table_def['table_func']) && !empty($br_table_def['table_func']))   
            $_br_table = cw_call($br_table_def['table_func'], array());
        else    
            $_br_table = $br_table;
    } else
        $_br_table = $br_table_def; 

    if (!empty($br_table_def['p_key']))
        $primary_key = $br_table_def['p_key'];
    else 
        $primary_key = cw_dt_primary_key_dh_browser($_br_table);

    if (is_array($br_table_def))
        $datahub_browse_tables[$br_table]['table_name'] = $_br_table;

    $datatable_resources_config['dh_browser_'.$_br_table] = array(
        'primary_key' => $primary_key, 
        //'table_name_func' => 'cw_dt_table_dh_browser',
        'table_name' => $_br_table, 
        'field_defs_func' => 'cw_dt_field_defs_dh_browser',
        'data_postprocess_func' => 'cw_dt_postprocess_dh_browser',
        'lengthMenu' => "20, 50, 100, 500, 1000, 5000, 10000"
    );
}
