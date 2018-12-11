<?php

print("<h1>Vias data import script</h1><br />");

global $is_interim;
/*
function cw_datahub_vias_load($new_only, $is_interim=true) {
    global $tables;

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = '_interim';


    $search_prefilled = array();

    $search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
    $search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
    $search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 1000);
    $search_prefilled['page']           = ($page ? $page : 1);
    $search_prefilled['unserialize_fields'] = true;

    $all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

    $vias_prof = array('weekly'=>array(), 'monthly'=>array()); 

    foreach ($vias_prof as $period=>$vp_v) {

        $_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles, $tables['datahub_vias_'.$period]));

        $vias_prof[$period] = reset($_profiles);
        if (empty($vias_prof[$period])) {
            cw_csvxc_logged_query('', "Error: vias $period profile is not found!"); return 0;
        }   

        $vias_prof[$period]['recurring_import_path'] = cw_flexible_import_find_server_file($vias_prof[$period]['recurring_import_path'], $interim_ext);

        if (!file_exists($vias_prof[$period]['recurring_import_path'])) {
            cw_csvxc_logged_query('', "Error: vias $period file ".$vias_prof[$period]['recurring_import_path']." is not found!"); 
            return 0;  
        } 

    } 



    if ($new_only) {
        $all_files_are_loaded_before = true;

        foreach ($vias_prof as $period=>$vp_v) {
            $vias_prof[$period]['file_hash'] = md5_file($vias_prof[$period]['recurring_import_path']);
            $is_file_loaded_already = cw_query_first_cell("SELECT COUNT(*) FROM $tables[flexible_import_loaded_files_hash] WHERE profile_id='".$vias_prof[$period]['id']."' AND hash='".$vias_prof[$period]['file_hash']."'");

            if (!$is_file_loaded_already)
                $all_files_are_loaded_before = false;

        }
        if ($all_files_are_loaded_before) {
            cw_csvxc_logged_query('', "Warning: there are no new Vias files to load");
            return 0;
        }
    }   

    foreach ($vias_prof as $period=>$vp_v) {
        cw_csvxc_logged_query("delete from ".$tables['datahub_vias_'.$period]);
        cw_flexible_import_run_profile($vp_v['id'], array($vp_v['recurring_import_path']));

        if (empty($vp_v['file_hash']))
            $vp_v['file_hash'] = md5_file($vp_v['recurring_import_path']);

        cw_array2update("flexible_import_profiles", array('recurring_last_run_date'=>time()), "id='$vp_v[id]'");
        cw_array2insert('flexible_import_loaded_files_hash', array('profile_id'=>$vp_v['id'], 'hash'=>$vp_v['file_hash'], 'date_loaded'=>time()));
    }


    cw_csvxc_logged_query("delete from $tables[datahub_vias_combined]");
    cw_csvxc_logged_query("INSERT INTO $tables[datahub_vias_combined] (`No.`,Description,`Bottle size`,`Case pack`,Vintage,`FDL Available`,Region,`BATF No.`, `UPC Code`,Line,`Metro 2 Case`,`Metro 3 Case`,`Metro 4 Case`,`Metro 5 Case`,`Metro 6 Case`,`Metro 10 Case`,`Metro 15 Case`,`Metro 20 Case`,`Metro 25 Case`,`Metro 28 Case`,`Metro 30 Case`,`Metro 50 Case`,`Metro 56 Case`,`Metro 100 Case`) SELECT w.`No.`,w.Description,w.`Bottle size`,w.`Case pack`,w.Vintage,w.Available, m.Region, m.`BATF No.`, m.`UPC Code`, m.Line, m.`Metro 2 Case`, m.`Metro 3 Case`, m.`Metro 4 Case`, m.`Metro 5 Case`, m.`Metro 6 Case`, m.`Metro 10 Case`, m.`Metro 15 Case`, m.`Metro 20 Case`, m.`Metro 25 Case`, m.`Metro 28 Case`, m.`Metro 30 Case`, m.`Metro 50 Case`, m.`Metro 56 Case`, m.`Metro 100 Case` FROM $tables[datahub_vias_monthly] m INNER JOIN  $tables[datahub_vias_weekly] w ON w.`No.`=m.`Item No.`");


    foreach($all_fi_profiles as $fi_prof) {
        if ($fi_prof['dbtable_src'] == $tables['datahub_vias_combined'] && $fi_prof['import_src_type'] == 'T') {

            if ($is_interim && strpos($fi_prof["name"], 'interim') !== false) {
                $vias_import_profile = $fi_prof; break;
            } 

            if (!$is_interim && strpos($fi_prof["name"], 'interim') === false) {            
                $vias_import_profile = $fi_prof; break;
            } 

        } 
    }

    cw_flexible_import_run_profile($vias_import_profile['id'], array());

}
*/
cw_datahub_vias_load(0, $is_interim);

$interim_ext = '';
if ($is_interim)
    $interim_ext = 'interim_';

print("<h3>$res_str...</h3><a href='index.php?target=datahub_".$interim_ext."buffer_match'>Return to buffer listing page</a>");
//cw_header_location("index.php?target=datahub_".$interim_ext."buffer_match");

die;
