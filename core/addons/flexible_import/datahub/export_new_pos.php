<?php

//require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');
//error_reporting(E_ERROR);
if ($old_export == 'Y') $step12passed='Y';

if ($step12passed != 'Y') {
    cw_datahub_delay_autoupdate("target=datahub_export_new_pos");
    cw_header_location("index.php?target=datahub_step_pos_update&redirect_target=datahub_export_new_pos");
}
cw_datahub_delay_autoupdate_release_lock();

db_query("drop table if exists cw_datahub_pos_export_new");

db_query("create table cw_datahub_pos_export_new as select p.* from pos p inner join xfer_products_SWE xps on xps.catalogid=CAST(p.`Alternate Lookup` as SIGNED) and xps.hide=0");
db_query("delete p.* from cw_datahub_pos_export_new p, cw_qbwc_pos_items_buffer ib where ib.alu=CAST(p.`Alternate Lookup` as SIGNED)");
db_query("delete p.* from cw_datahub_pos_export_new p, cw_qbwc_pos_items_buffer ib where ib.ITEMNUMBER=p.`Item Number`");

db_query("drop table if exists cw_datahub_pos_export_new_incorrect_items");

db_query("create table cw_datahub_pos_export_new_incorrect_items as select pne.* from cw_datahub_pos_export_new pne inner join item i on i.id = cast(`Alternate Lookup` as signed) where REPLACE(pne.`Item Description`, '&amp;', '&')!=CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR))");

db_query("delete pne.* from cw_datahub_pos_export_new pne  inner join item i on i.id = cast(`Alternate Lookup` as signed) where REPLACE(pne.`Item Description`, '&amp;', '&')!=CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR))");

$bad_items = cw_query_column("select `Alternate Lookup` from cw_datahub_pos_export_new_incorrect_items");
if (!empty($bad_items)) {
    print("<h2>The following items have been removed from export new feed:</h2><br>".implode(', ',$bad_items)." <br> Please run <a href='index.php?target=datahub_calc_output'>Transfer to live</a> once again in order to try fix them<br><br>");
}

$new_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_export_new");
if ($new_pos_count) {
    if (function_exists('qbwc_pos_export_new') && $old_export!='Y') {
        call_user_func('qbwc_pos_export_new');
        cw_datahub_delay_autoupdate_release_lock(); 
    } else { 
        $ts =  date('Y-m-d__H-i-s');
        cw_datahub_exportMysqlToXls('cw_datahub_pos_export_new', "new_$ts.xls", "select * from cw_datahub_pos_export_new where `Alternate Lookup`!='' and `Alternate Lookup`!=0");
        cw_datahub_delay_autoupdate_release_lock();
        exit;
    }  
} else {
    echo 'no new records to export';
    cw_datahub_delay_autoupdate_release_lock();
    exit;
}
