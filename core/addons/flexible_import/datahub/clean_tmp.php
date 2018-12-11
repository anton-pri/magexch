<?php

function cw_flexible_import_clean_tmp() {
    global $tables; 


    $tmp_tables = cw_query("show tables like '%cw_flexible_import_tmp_table%'");
    if (!empty($tmp_tables)) {
    foreach ($tmp_tables as $_tt) {
        $tmp_table_name = reset($_tt);
        $drop_sql  = "DROP TABLE IF EXISTS $tmp_table_name";
        print($drop_sql."<br>\n");
        db_query($drop_sql);
    }
    } else {
        print("<h2>No '%cw_flexible_import_tmp_table%' tables found</h2>");
    }

    $back_tables = cw_query("show tables like '%_back%'");
    if (!empty($back_tables)) {
    foreach ($back_tables as $_bt) {
        $back_table_name = reset($_bt);
        if (in_array($back_table_name, $tables)) continue;

        $drop_sql  = "DROP TABLE IF EXISTS $back_table_name";
        print($drop_sql."<br>\n");
        db_query($drop_sql);
    }
    } else {
        print("<h2>No '%_back%' tables found</h2>");
    }

    $tmp_tables = cw_query("show tables like '%items_buffer_149%'");
    if (!empty($tmp_tables)) {
    foreach ($tmp_tables as $_tt) {
        $tmp_table_name = reset($_tt);
        $drop_sql  = "DROP TABLE IF EXISTS $tmp_table_name";
        print($drop_sql."<br>\n");
        db_query($drop_sql);
    }
    } else {
        print("<h2>No '%items_buffer_149%' tables found</h2>");
    }

}

cw_flexible_import_clean_tmp();

die;
