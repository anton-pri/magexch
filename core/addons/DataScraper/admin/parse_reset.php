<?php

$curr_site_id = &cw_session_register('curr_site_id',0);

if ($curr_site_id) {

    global $WGET_DOWNLOADS_PATH;

    $site_data = cw_query_first("SELECT * FROM $tables[datascraper_sites_config] WHERE siteid='$curr_site_id'");
    $site_data['name'] = str_replace('http://', '', $site_data['name']);
    $dir_list_file_name = str_replace(array('www.','.com','@', '/', '%20'),'',$site_data['name']).".txt";
    $dir_list_file = $WGET_DOWNLOADS_PATH.'/'.$dir_list_file_name;
    $parse_pos_file = $dir_list_file.'.pos';
    if (file_exists($parse_pos_file)) {
        $res = unlink($parse_pos_file);
        if ($res) print("Reset parse flag for site #$curr_site_id, deleted file: $parse_pos_file");
    } 
}

cw_header_location("index.php?target=datascraper_results");
die;
