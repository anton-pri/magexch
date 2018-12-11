<?php

cw_display_service_header("Running hub items transfer");

$hub_transfer_table = "$tables[datahub_main_data]_transfer";

cw_datahub_prepare_transfer($hub_transfer_table);

$items2transfer_count = cw_query_first_cell("SELECT COUNT(*) FROM $hub_transfer_table");

if (!$items2transfer_count) {
    cw_add_top_message("None of hub items have been changed since last transfer", 'E');
    cw_header_location("index.php?target=datahub_main_edit"); 
}

if ($items2transfer_count == 1) 
    print("<br><br><h2>$items2transfer_count item has been detected as updated:</h2><br />");
else
    print("<br><br><h2>$items2transfer_count items have been detected as updated.</h2><br />");

$hub_transfer_items = cw_query("SELECT ID FROM $hub_transfer_table ORDER BY ID ASC");
$preview_items_fields = cw_datahub_main_table_filter_fields('buffer_match_preview');
foreach ($hub_transfer_items as $hub_item) {
    $hub_item_data = cw_query_first("SELECT * FROM $hub_transfer_table WHERE ID = '$hub_item[ID]'");
    $preview_arr = array();
    foreach ($hub_item_data as $hi_field => $hi_value) {
         if ($hi_field == 'ID') 
             $hi_value = '<b>#'.$hi_value.'</b>';

         if (in_array($hi_field, $preview_items_fields))   
             $preview_arr[] = $hi_value;
    }
    print(implode(' ', $preview_arr).'<br />');
}

$transfer_profile_id = cw_query_first_cell("SELECT id FROM $tables[flexible_import_profiles] WHERE import_src_type='T' AND dbtable_src='$hub_transfer_table' LIMIT 1");

//die;

print("<br><br><h2>Transfer hub items profile: #$transfer_profile_id</h2>");

if ($transfer_profile_id) {

    $parsed_file = cw_flexible_import_run_profile($transfer_profile_id, array());

    if($parsed_file['err'])
        cw_add_top_message($parsed_file['err'], 'E');
    else
        cw_add_top_message(cw_get_langvar_by_name('lbl_import_success'), 'I');

} 


print("<h3>Done...<a href='index.php?target=datahub_main_edit'>Return to main edit page</a></h3>");
die;
