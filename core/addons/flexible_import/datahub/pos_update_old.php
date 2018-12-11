<?php
set_time_limit(86400);
print("<h1>Update pos script run</h1><br />");

$search_prefilled = array();

$search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
$search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
$search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
$search_prefilled['page']           = ($page ? $page : 1);
$search_prefilled['unserialize_fields'] = true;

$all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

//print_r($all_fi_profiles);
$pos_fi_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles, $tables['datahub_pos']));
$pos_fi_profile = reset($pos_fi_profiles);

foreach($all_fi_profiles as $fi_prof) {
    if ($fi_prof['dbtable_src'] == 'cw_datahub_pos' && $fi_prof['import_src_type'] == 'T') 
        $pos_fi_import_profile = $fi_prof;
}


if (!empty($pos_fi_profile) && !empty($pos_fi_import_profile)) { 

    if (file_exists($pos_fi_profile['recurring_import_path'])) {

        cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos]");
 
        $parsed_file = cw_flexible_import_run_profile($pos_fi_profile['id'], array($pos_fi_profile['recurring_import_path']));

        cw_csvxc_logged_query("DELETE $tables[datahub_pos].* FROM $tables[datahub_pos] LEFT JOIN $tables[datahub_item_store2] ON $tables[datahub_pos].`Item Number`=$tables[datahub_item_store2].store_sku AND $tables[datahub_item_store2].store_id!=1 WHERE $tables[datahub_item_store2].store_sku IS NOT NULL"); 

        cw_datahub_sync_pos_to_main();

        cw_csvxc_logged_query("insert ignore into $tables[datahub_item_store2] (item_id, store_id, store_sku) select `Alternate Lookup`, 1, `Item Number` from cw_datahub_pos_snapshot where coalesce(`Alternate Lookup`,0) != 0 AND coalesce(`Item Number`, 0) != 0");  

        $datahub_pos_snapshot = "$tables[datahub_pos]_snapshot";

        cw_csvxc_logged_query("DROP TABLE IF EXISTS $datahub_pos_snapshot"); 
        cw_csvxc_logged_query("CREATE TABLE $datahub_pos_snapshot LIKE $tables[datahub_pos]");
        cw_csvxc_logged_query("INSERT INTO $datahub_pos_snapshot SELECT * FROM $tables[datahub_pos]");

        cw_csvxc_logged_query("DELETE $datahub_pos_snapshot.* FROM $datahub_pos_snapshot LEFT JOIN $tables[datahub_main_data] ON $tables[datahub_main_data].ID=$datahub_pos_snapshot.`Alternate Lookup` WHERE $tables[datahub_main_data].ID IS NULL");


        $datahub_main_data_possnapshot = "$tables[datahub_main_data]_possnapshot";

        cw_csvxc_logged_query("DROP TABLE IF EXISTS $datahub_main_data_possnapshot");
        cw_csvxc_logged_query("CREATE TABLE $datahub_main_data_possnapshot LIKE $tables[datahub_main_data]");
        cw_csvxc_logged_query("INSERT INTO $datahub_main_data_possnapshot SELECT * FROM $tables[datahub_main_data]");

        cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos] WHERE coalesce(`Alternate Lookup`,'')!=''");

        $parsed_file = cw_flexible_import_run_profile($pos_fi_import_profile['id'], array());

        cw_datahub_clean_buffer_by_blacklist();

        $blacklist_table = cw_datahub_get_blacklist_table();

        cw_csvxc_logged_query("DELETE $tables[datahub_import_buffer].* FROM $tables[datahub_import_buffer] LEFT JOIN $blacklist_table ON $tables[datahub_import_buffer].store_sku=$blacklist_table.item_xref WHERE $blacklist_table.Source IN ('Feed_POS','Feed_SWE_Store') AND $blacklist_table.item_xref IS NOT NULL");

        if($parsed_file['err'])
           $res_str = "ERROR: ".$parsed_file['err'];// cw_add_top_message($parsed_file['err'], 'E');
        else
            $res_str = "done";

    } else {
        $res_str = "ERROR: Pos update file $pos_fi_profile[recurring_import_path] does not exist";
    }

} else {
    $res_str = "ERROR: POS Import and/or POS Update profiles are not set up"; 
}

print("<h3>$res_str...</h3><a href='index.php?target=datahub_buffer_match'>Return to buffer listing page</a>");
die;
