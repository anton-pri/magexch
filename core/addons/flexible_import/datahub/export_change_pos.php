<?php

$sql = 'select p.* from pos as p
inner join pos_snap_shot as l ON l.`Item Number` = p.`Item Number`
inner join  xfer_products_SWE as x ON x.catalogid = p.`Alternate Lookup`
WHERE (
trim(p.`Average Unit Cost`) <> trim(l.`Average Unit Cost`) 
OR trim(p.`MSRP`) <> trim(l.`MSRP`)
OR trim(p.`Size`) <> trim(l.`Size`)
OR trim(p.`Department Name`) <> trim(l.`Department Name`)
OR trim(p.`Custom Field 1`) <> trim(l.`Custom Field 1`)
OR trim(p.`Custom Field 2`) <> trim(l.`Custom Field 2`)
OR trim(p.`Custom Field 3`) <> trim(l.`Custom Field 3`)

OR trim(p.`Custom Price 1`) <> trim(l.`Custom Price 1`)
OR trim(p.`Regular Price`) <> trim(l.`Regular Price`)
OR trim(p.`Vendor Code`) <> trim(l.`Vendor Code`)
OR trim(p.`Custom Field 5`) <> trim(l.`Custom Field 5`)
) AND x.hide = 0';

db_query("drop table if exists cw_datahub_pos_export_changed");
db_query("create table cw_datahub_pos_export_changed as $sql");

$changed_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_export_changed");
if ($changed_pos_count) {
    $ts =  date('Y-m-d__H-i-s');
    cw_datahub_exportMysqlToXls('cw_datahub_pos_export_changed', "changed_$ts.xls", "select * from cw_datahub_pos_export_changed");
} else {
    echo 'no changed records to export';
}

die;
