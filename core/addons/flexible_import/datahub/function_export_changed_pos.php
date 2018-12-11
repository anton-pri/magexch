<?php

function datahub_import_export_changed_pos($dh_update_step, $is_web_mode=false) {
    global $tables, $config, $step12passed;

if ($is_web_mode)
if ($step12passed != 'Y') 
    cw_header_location("index.php?target=datahub_step_pos_update&redirect_target=datahub_export_changed_pos"); 

$checked_fields = array('Average Unit Cost', 'MSRP', 'Size','Department Name', 'Manufacturer', 'Custom Field 1', 'Custom Field 2','Custom Field 3','Custom Price 1', 'Regular Price', 'Vendor Code', 'Custom Field 5');

foreach ($checked_fields as $fld) {
    $what_fields_changed[] = "IF(trim(p.`$fld`) <> trim(l.`$fld`),CONCAT('$fld: ',trim(l.`$fld`),'->',trim(p.`$fld`),', '),'')";
}

$changed_fields_column = ",TRIM(TRAILING ', ' FROM CONCAT(".implode(",", $what_fields_changed).")) as changed_fields";
$changed_fields_column = '';

$sql = "select p.* $changed_fields_column
from pos as p
inner join pos_snap_shot as l ON l.`Item Number` = p.`Item Number`
inner join  xfer_products_SWE as x ON x.catalogid = CAST(p.`Alternate Lookup` as signed)
WHERE (
trim(p.`Alternate Lookup`) <> trim(l.`Alternate Lookup`) 
OR trim(p.`Average Unit Cost`) <> trim(l.`Average Unit Cost`) 
OR trim(p.`MSRP`) <> trim(l.`MSRP`)
OR trim(p.`Size`) <> trim(l.`Size`)
OR trim(p.`Department Name`) <> trim(l.`Department Name`)
OR trim(p.`Manufacturer`) <> trim(l.`Manufacturer`)
OR trim(p.`Custom Field 1`) <> trim(l.`Custom Field 1`)
OR trim(p.`Custom Field 2`) <> trim(l.`Custom Field 2`)
OR trim(p.`Custom Field 3`) <> trim(l.`Custom Field 3`)

OR trim(p.`Custom Price 1`) <> trim(l.`Custom Price 1`)
OR trim(p.`Regular Price`) <> trim(l.`Regular Price`)
OR trim(p.`Vendor Code`) <> trim(l.`Vendor Code`)
OR trim(p.`Custom Field 5`) <> trim(l.`Custom Field 5`)
) AND x.hide = 0";
/*
$sql = "select p.* 
from pos as p 
inner join pos_snap_shot as l ON l.`Item Number` = p.`Item Number` 
inner join cw_datahub_main_data_sherry_sake x ON x.catalog_id = CAST(p.`Alternate Lookup` as signed)
WHERE (
trim(p.`Alternate Lookup`) <> trim(l.`Alternate Lookup`) 
OR trim(p.`Average Unit Cost`) <> trim(l.`Average Unit Cost`) 
OR trim(p.`MSRP`) <> trim(l.`MSRP`)
OR trim(p.`Size`) <> trim(l.`Size`)
OR trim(p.`Department Name`) <> trim(l.`Department Name`)
OR trim(p.`Manufacturer`) <> trim(l.`Manufacturer`)
OR trim(p.`Custom Field 1`) <> trim(l.`Custom Field 1`)
OR trim(p.`Custom Field 2`) <> trim(l.`Custom Field 2`)
OR trim(p.`Custom Field 3`) <> trim(l.`Custom Field 3`)

OR trim(p.`Custom Price 1`) <> trim(l.`Custom Price 1`)
OR trim(p.`Regular Price`) <> trim(l.`Regular Price`)
OR trim(p.`Vendor Code`) <> trim(l.`Vendor Code`)
OR trim(p.`Custom Field 5`) <> trim(l.`Custom Field 5`)
) ";
*/
/*
$sql = "select p.* 
from pos as p 
inner join cw_datahub_main_data_sherry_sake x ON x.catalog_id = CAST(p.`Alternate Lookup` as signed)";
*/

//$sql = 'select p.* from pos as p inner join pos_wrong_dep pwd ON pwd.itemnumber=p.`Item Number`';

db_query("drop table if exists cw_datahub_pos_export_changed");
db_query("create table cw_datahub_pos_export_changed as $sql");

// SW-539. Fix issue: New products from QB pos which are already in hub did not get ALU from hub.
// These products are already in hub and were not changed during hub calculation so they have no difference in tables pos and pos_snap_shot.
// We have to add such products explicitly.
db_query("insert into cw_datahub_pos_export_changed 
            select p.* 
            from pos as p 
            inner join cw_qbwc_pos_items_buffer pib on p.`Item Number` = pib.ITEMNUMBER and pib.ALU=0 
            left join cw_qbwc_pos_items_buffer pib2 on pib2.ALU=CAST(p.`Alternate Lookup` as signed) 
            where  CAST(p.`Alternate Lookup` as signed)>0 and pib2.buffer_id IS NULL");

db_query("alter table cw_datahub_pos_export_changed add index (`Item Number`)");
db_query("alter table cw_datahub_pos_export_changed  change column `Alternate Lookup` `Alternate Lookup` int(11) not null default 0");
db_query("alter table cw_datahub_pos_export_changed add index (`Alternate Lookup`)");


$changed_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_export_changed");
if ($changed_pos_count) {
    if (function_exists('qbwc_pos_export_changed')) {
        qbwc_pos_export_changed($is_web_mode);
    } else { 
        if ($is_web_mode) {
            $ts =  date('Y-m-d__H-i-s');
            cw_datahub_exportMysqlToXls('cw_datahub_pos_export_changed', "changed_$ts.xls", "select * from cw_datahub_pos_export_changed");
            die;   
        }
    }
} else {
    echo 'no changed records to export';
    if ($is_web_mode) {
        die;
    }
}

    return -1;
}
