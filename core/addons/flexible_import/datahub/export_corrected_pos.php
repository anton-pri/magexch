<?php

/*
if ($step12passed != 'Y' ) {
    cw_datahub_delay_autoupdate("target=datahub_export_new_pos"); 
    cw_header_location("index.php?target=datahub_step_pos_update&redirect_target=datahub_export_changed_pos"); 
}
cw_datahub_delay_autoupdate_release_lock();
*/

//cw_datahub_pos_export_new_incorrect_items

db_query("drop table if exists cw_datahub_pos_export_changed");
db_query("create table cw_datahub_pos_export_changed as select p.* from pos p inner join cw_datahub_pos_export_new_incorrect_items pneic on pneic.`Alternate Lookup`=p.`Alternate Lookup` and p.`Alternate Lookup`!=0");
db_query("delete pce.* from cw_datahub_pos_export_changed pce  inner join item i on i.id = cast(`Alternate Lookup` as signed) where pce.`Item Description`!=CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR))");


db_query("alter table cw_datahub_pos_export_changed add index (`Item Number`)");
db_query("alter table cw_datahub_pos_export_changed  change column `Alternate Lookup` `Alternate Lookup` int(11) not null default 0");
db_query("alter table cw_datahub_pos_export_changed add index (`Alternate Lookup`)");

//print('<pre>');print_r(cw_query("select * from cw_datahub_pos_export_changed"));print('</pre>');die;

$changed_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_export_changed");
if ($changed_pos_count) {
    if (function_exists('qbwc_pos_export_changed') && $old_export!='Y') {

        global $action;
        $action = 'proceed';

        call_user_func('qbwc_pos_export_changed');
        cw_datahub_delay_autoupdate_release_lock();
    } else { 
        $ts =  date('Y-m-d__H-i-s');
        cw_datahub_exportMysqlToXls('cw_datahub_pos_export_changed', "changed_$ts.xls", "select * from cw_datahub_pos_export_changed");
        cw_datahub_delay_autoupdate_release_lock();
        die;   
    }
} else {
    echo 'no correct records to export';
    cw_datahub_delay_autoupdate_release_lock();
    die;
}
