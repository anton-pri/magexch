<?php
die('Not now!');
cw_datahub_delay_autoupdate("target=datahub_export_changed_pos_all"); 
/*
$sql = "select p.* 
from pos as p
inner join pos_backup171019 as l ON (l.`Item Number` = p.`Item Number`)
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
)";
*/
/*
db_query("drop table if exists cw_datahub_pos_export_changed_all");

$sql = "select p.* from pos p inner join cw_qwbc_deps_correct corr on (corr.alu=p.`Alternate Lookup` and corr.alu!=0) OR (corr.itemnumber=p.`item number` and corr.itemnumber!=0)";

db_query("create table cw_datahub_pos_export_changed_all as $sql");

db_query("alter table cw_datahub_pos_export_changed_all add index (`Item Number`)");
db_query("alter table cw_datahub_pos_export_changed_all  change column `Alternate Lookup` `Alternate Lookup` int(11) not null default 0");
db_query("alter table cw_datahub_pos_export_changed_all add index (`Alternate Lookup`)");
*/

//$changed_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_export_changed_all");
if ($changed_pos_count || 1) {

    require_once $app_main_dir . '/addons/qbwc/lib/QuickBooks.php';
/*
    qbwc_update_xml_from_hub('cw_datahub_pos_export_changed_all');

    db_query("update cw_qbwc_pos_items_buffer pib inner join cw_qbwc_departments qbd on qbd.departmentname=pib.departmentname set pib.departmentlistid=qbd.listid where pib.itemnumber in (select `item number` from cw_datahub_pos_export_changed_all)");

    qbwc_queue_transfer_items(QUICKBOOKS_MOD_INVENTORYITEM, 'cw_datahub_pos_export_changed_all');
*/
    qbwc_queue_transfer_items(QUICKBOOKS_MOD_INVENTORYITEM, 'cw_qbwc_pos_items_buffer_fixitems171020', 'ITEMNUMBER', 'ITEMNUMBER');

    echo '<h3>The task has been added to the queue and will be actioned within the next 30 mins</h3>';
    
} else {
    echo 'no changed records to export';
}
cw_datahub_delay_autoupdate_release_lock();
die;
