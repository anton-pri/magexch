<?php

if ($REQUEST_METHOD == "POST") {

    if ($action == "price_levels_modify") {
        foreach ($datahub_price_levels as $level_id=>$dpl) {
            if ($dpl['cost'] == 0 && $dpl['surcharge'] == 0) continue;
            if ($level_id == 0) {
                db_query("delete from $tables[datahub_price_levels] where cost = '$dpl[cost]'");
                cw_array2insert('datahub_price_levels', $dpl);
            } else {
                db_query("delete from $tables[datahub_price_levels] where cost = '$dpl[cost]' or level_id='$level_id'");
                $dpl['level_id'] = $level_id;
                cw_array2insert('datahub_price_levels', $dpl);
            }
        }
        cw_add_top_message("Price calculation levels have been updated successfully",'I');
    } else {
        foreach ($datahub_price_levels as $level_id=>$dpl) {
            if ($dpl['del']) 
                db_query("delete from $tables[datahub_price_levels] where level_id='$level_id'");
        } 
        cw_add_top_message("Selected price calculation levels have been deleted successfully",'I');
    }

    cw_header_location("index.php?target=datahub_price_levels");
} 

$smarty->assign('price_levels', cw_query("select * from $tables[datahub_price_levels] order by cost asc"));
$smarty->assign('main', 'datahub_price_levels');
