<?php
set_time_limit(86400);
print("<h1>Loading updated POS data to hub</h1><br />");

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


if (!empty($pos_fi_import_profile)) { 

    require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');

    SWE_store_feed::SWE_store_import_and_update(true);

    cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos]");
    cw_csvxc_logged_query("INSERT INTO $tables[datahub_pos] SELECT * FROM pos"); 

    cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos] WHERE COALESCE(`Alternate Lookup`,0)!=0");
    cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos] WHERE `Department Name`='Hard Cider'");
    cw_csvxc_logged_query("DELETE $tables[datahub_pos].* FROM $tables[datahub_pos] INNER JOIN item_store2 ON item_store2.store_sku=$tables[datahub_pos].`Item Number`"); 

    //loading data to import buffer
    $parsed_file = cw_flexible_import_run_profile($pos_fi_import_profile['id'], array());

    cw_datahub_clean_buffer_by_blacklist();

    if(isset($parsed_file['err']))
       $res_str = "ERROR: ".$parsed_file['err'];// cw_add_top_message($parsed_file['err'], 'E');
    else
       $res_str = "done";

} else {
    $res_str = "ERROR: POS Update profiles are not set up"; 
}

print("<h3>$res_str...</h3><a href='index.php?target=datahub_buffer_match'>Return to buffer listing page</a>");
die;
