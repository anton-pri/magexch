<?php

function cw_datahub_dbg_gen_ts() {
    return microtime();//mt_rand();
}

function cw_datahub_dbg_save_snapshot($id, $type, $print=false) {
    global $tables, $var_dirs;    

$dbg_def = array(
    'item_id' => array(
         array('item_store2', "item_id='###'"),  
         array('item', "ID='###'"),
         array('pos', "cast(`Alternate Lookup` as signed)='###'"),
         array('xfer_products_SWE', "catalogid='###'"),
         array('cw_datahub_main_data', "id='###'"),
         array('feeds_item_compare', "dhmd_id='###'")  
    ), 
    'sku' => array(
         array('item_store2', "store_sku='###'"), 
         array('pos', "`Item Number`='###'"),   
    ),
    'xref' => array(
         array('pos', "`Custom Field 5`='###'")
    )
);

    if (!empty($type))
        if (!isset($dbg_def[$type])) return;

    if (!empty($type))
        $types[] = $type;
    else 
        $types[] = array_keys($dbg_def); 

    $entry_mt = cw_datahub_dbg_gen_ts();

    $tables['datahub_dbg_sessions'] = 'cw_datahub_dbg_sessions';
    db_query("CREATE TABLE IF NOT EXISTS cw_datahub_dbg_sessions (sess_id int(11) not null auto_increment, date int(11) not null default 0, session_key varchar(128) not null default '', dbg_ts varchar(64) not null default '', backtrace text not null default '', PRIMARY KEY (sess_id), UNIQUE KEY(session_key, dbg_ts))");
 
    $session_key = file_get_contents($var_dirs['flexible_import'].'/datahub.lock');
    if (empty($session_key))
        $session_key = "test_dbg|".time();

    $session_key .= "|".$id."|".$type;

    cw_array2insert('cw_datahub_dbg_sessions', 
        array('date'=>time(), 'session_key'=>$session_key, 'dbg_ts'=>$entry_mt, 'backtrace'=>implode("\n\t", cw_get_backtrace(1)))
    );

    $orig_id = $id;

    foreach ($types as $_type) {
        if ($_type == 'sku' && $type!='sku') {
            $id = cw_query_first_cell("select store_sku from item_store2 where item_id='$orig_id'");
            if (!$id) 
                $id = cw_query_first_cell("select `Item Number` from pos where `Alternate Lookup`='$orig_id'"); 
        }
        if (!$id) continue;
        foreach ($dbg_def[$_type] as $dd) {
            $orig_tbl = $dd[0]; $where_cond = str_replace('###',$id,$dd[1]);

            $dbg_tbl = cw_datahub_dbg_setup_table($orig_tbl); 

            db_query("insert into `$dbg_tbl` select *, '$entry_mt' as dbg_ts, '$_type' as dbg_type from `$orig_tbl` where $where_cond"); 

            if ($print) {
                print('<pre>');   
//print_r(array($orig_tbl, $where_cond, $dbg_tbl, "select * from `$dbg_tbl` where '$entry_mt' = dbg_ts and '$_type' = dbg_type"));                  
                print_r(cw_query_first("select * from `$dbg_tbl` where '$entry_mt' = dbg_ts and '$_type' = dbg_type"));
                print('</pre>');
            }
        } 
    }

    

}

function cw_datahub_dbg_setup_table($orig_tbl) {
    $dbg_tbl = $orig_tbl."_dh_dbg";

    if (!cw_query_first_cell("SHOW TABLES LIKE '$dbg_tbl'")) {
        db_query("create table `$dbg_tbl` as select *,'".cw_datahub_dbg_gen_ts()."' as dbg_ts, '0123456' as dbg_type from `$orig_tbl`");     
        db_query("truncate table `$dbg_tbl`");   
    }

    return $dbg_tbl;
}
