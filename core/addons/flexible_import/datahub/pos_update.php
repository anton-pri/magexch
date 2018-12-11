<?php
//print("Sorry, this script is being updated and it's not accessible now");
//die;

cw_datahub_delay_autoupdate("target=datahub_pos_update");

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
    if ($fi_prof['dbtable_src'] == 'cw_datahub_pos' && $fi_prof['import_src_type'] == 'T' && !empty($fi_prof['description'])) 
        $pos_fi_import_profile = $fi_prof;
}


if (!empty($pos_fi_import_profile)) { 

    require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');

    SWE_store_feed::SWE_store_import_and_update(true);

    cw_call("cw_datahub_update_swe_xrefs_stock", array());

    cw_call("cw_datahub_private_collector_correct", array());
    cw_call("cw_datahub_creation_date_correction", array());

    cw_csvxc_logged_query("DROP TEMPORARY TABLE IF EXISTS pos_update_tmp_copy");
    cw_csvxc_logged_query("CREATE TEMPORARY TABLE pos_update_tmp_copy LIKE pos");
    cw_csvxc_logged_query("INSERT INTO pos_update_tmp_copy SELECT * FROM pos");
    cw_csvxc_logged_query("ALTER TABLE pos_update_tmp_copy MODIFY COLUMN `Alternate Lookup` int(11) not null default 0");
    cw_csvxc_logged_query("UPDATE cw_datahub_main_data md INNER JOIN pos_update_tmp_copy p on md.catalog_id=p.`Alternate Lookup` SET md.drysweet=p.`Department Name`");
    cw_csvxc_logged_query("UPDATE item i INNER JOIN pos_update_tmp_copy p on i.id=p.`Alternate Lookup` SET i.drysweet=p.`Department Name`");

    cw_csvxc_logged_query("UPDATE cw_datahub_main_data md SET md.drysweet=IF((INSTR('".$config['flexible_import']['fi_spirit_varietals']."',md.varietal) AND md.varietal!=''),'Spirit','Wine') WHERE COALESCE(md.drysweet,'')=''");
    cw_csvxc_logged_query("UPDATE item i SET i.drysweet=IF((INSTR('".$config['flexible_import']['fi_spirit_varietals']."',i.varietal) AND i.varietal!=''),'Spirit','Wine') WHERE COALESCE(i.drysweet,'')=''");

    cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos]");
    cw_csvxc_logged_query("INSERT INTO $tables[datahub_pos] SELECT * FROM pos"); 

    cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos] WHERE COALESCE(`Alternate Lookup`,0)!=0");
//    cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos] WHERE `Department Name`='Hard Cider'");

    global $config;
    $locked_deps = array_map('trim',explode("\n",$config['qbwc']['qbwc_locked_deps']));
    cw_csvxc_logged_query("DELETE FROM $tables[datahub_pos] WHERE `Department Name` IN ('".implode("','", $locked_deps)."')");


    cw_csvxc_logged_query("DELETE $tables[datahub_pos].* FROM $tables[datahub_pos] INNER JOIN item_store2 ON item_store2.store_sku=$tables[datahub_pos].`Item Number` and item_store2.store_id=1"); 

    cw_csvxc_logged_query("DELETE $tables[datahub_pos].* FROM $tables[datahub_pos] INNER JOIN item_xref ON item_xref.xref=$tables[datahub_pos].`Custom Field 5` and $tables[datahub_pos].`Custom Field 5` != ''");

    //loading data to import buffer
    $parsed_file = cw_flexible_import_run_profile($pos_fi_import_profile['id'], array());

    cw_datahub_clean_buffer_by_blacklist(true);

    if(isset($parsed_file['err']))
       $res_str = "ERROR: ".$parsed_file['err'];// cw_add_top_message($parsed_file['err'], 'E');
    else
       $res_str = "done";

} else {
    $res_str = "ERROR: POS Update profiles are not set up"; 
}

print("<h3>$res_str...</h3><a href='index.php?target=datahub_buffer_match'>Return to buffer listing page</a>");

cw_datahub_delay_autoupdate_release_lock();

//cw_header_location('index.php?target=datahub_interim_buffer_match');

die;
