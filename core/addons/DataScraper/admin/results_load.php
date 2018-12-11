<?php
$curr_site_id = &cw_session_register('curr_site_id',0);

if ($curr_site_id) {

    $search_prefilled = array();

    $search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
    $search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
    $search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
    $search_prefilled['page']           = ($page ? $page : 1);
    $search_prefilled['unserialize_fields'] = true;

    $all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

    foreach($all_fi_profiles as $fi_prof) {
        if ($fi_prof['dbtable_src'] == $tables['datascraper_result_values'].$curr_site_id && $fi_prof['import_src_type'] == 'T')
            $pos_fi_import_profile = $fi_prof;
    }


    if (!empty($pos_fi_import_profile)) {
        $parsed_file = cw_flexible_import_run_profile($pos_fi_import_profile['id'], array());
        cw_add_top_message("Parsed items have been loaded to the datahub import buffer",'I');
    } else {
        cw_add_top_message("Import profile suitable for current scraped site is not found.<br>", 'E');
    }

}
cw_header_location("index.php?target=datascraper_results");
die;
