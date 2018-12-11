<?php

cw_display_service_header("Checking updated items");
print("<h1>Checking updated items</h1><br><br>");

$hub_transfer_table = "$tables[datahub_main_data]_transfer";

cw_datahub_prepare_transfer($hub_transfer_table, true);

$items2transfer_count = cw_query_first_cell("SELECT COUNT(*) FROM $hub_transfer_table");

if (!$items2transfer_count) {
    print("<h3>None of hub items have been changed since last transfer</h3>");
}

if ($items2transfer_count == 1) 
    print("<br><br><h3>$items2transfer_count item has been detected as updated:</h3><br />");
elseif ($items2transfer_count > 1)
    print("<br><br><h3>$items2transfer_count items have been detected as updated.</h3><br />");

$hub_transfer_items = cw_query("SELECT ID FROM $hub_transfer_table ORDER BY ID ASC");
$preview_items_fields = cw_datahub_main_table_filter_fields('buffer_match_preview');
foreach ($hub_transfer_items as $hub_item) {
    $hub_item_data = cw_query_first("SELECT * FROM $hub_transfer_table WHERE ID = '$hub_item[ID]'");
    $preview_arr = array(); 
    foreach ($hub_item_data as $hi_field => $hi_value) {
         if ($hi_field == 'ID')
             $hi_value = '<b>#'.$hi_value.'</b>';

         if (in_array($hi_field, array('cost', 'price'))) 
             $hi_value = '$'.$hi_value;

         if (in_array($hi_field, $preview_items_fields))
             $preview_arr[] = $hi_value;
    }
    print(implode(' ', $preview_arr).'<br />');
}

print("<br/><br/><h3>End...</h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
die;
