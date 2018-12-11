<?php

cw_datahub_delay_autoupdate("target=datahub_run_cw_update");

cw_datahub_product_backup_create();

$lost_items_count = cw_query("SELECT count(*) FROM cw_datahub_pos_lost_items");
if ($lost_items_count) {
    cw_csvxc_logged_query("UPDATE xfer_products_SWE xps INNER JOIN cw_datahub_pos_lost_items li on li.catalogid=xps.catalogid SET xps.hide=1");
}

cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS xfer_products_SWE_snapshot LIKE xfer_products_SWE");

$sql = "SELECT xf.catalogid FROM xfer_products_SWE as xf
                                INNER JOIN xfer_products_SWE_snapshot as sn
                                ON xf.catalogid = sn.catalogid
                                WHERE (xf.hide = 1 AND sn.hide = 0)";

$cat_ids = cw_query_column("SELECT xf.catalogid FROM xfer_products_SWE as xf
                                INNER JOIN xfer_products_SWE_snapshot as sn
                                ON xf.catalogid = sn.catalogid
                                WHERE (xf.hide = 1 AND sn.hide = 0)", 'catalogid');

if (!empty($cat_ids)) {
    cw_csvxc_logged_query("DELETE FROM xfer_products_SWE WHERE xfer_products_SWE.hide = 1 and xfer_products_SWE.catalogid NOT IN (".implode(", ", $cat_ids).")");
} else {
    cw_csvxc_logged_query("DELETE FROM xfer_products_SWE WHERE xfer_products_SWE.hide = 1");
}

cw_csvxc_logged_query("DELETE FROM xfer_products_SWE_snapshot");
cw_csvxc_logged_query("INSERT INTO xfer_products_SWE_snapshot SELECT * FROM xfer_products_SWE");

//SW-404 - merge old and new wildman supplier items
cw_csvxc_logged_query("UPDATE xfer_products_SWE SET supplierid=66 WHERE supplierid=180");

// merge old and new angel supplier items
cw_csvxc_logged_query("UPDATE xfer_products_SWE SET supplierid=182 WHERE supplierid=69");

cw_call('cw_datahub_save_product_data_snapshot', array());

$export_count = cw_query_first_cell("SELECT count(*) FROM xfer_products_SWE");

print("<h3>Export table contains $export_count items</h3>");

$search_prefilled = array();

$search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
$search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
$search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
$search_prefilled['page']           = ($page ? $page : 1);
$search_prefilled['unserialize_fields'] = true;

$all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

foreach($all_fi_profiles as $fi_prof) {
    if ($fi_prof['dbtable_src'] == 'xfer_products_SWE' && $fi_prof['import_src_type'] == 'T' && $fi_prof['name']=='DataHub2CW')
        $cw_import_profile = $fi_prof;
}

if (!empty($cw_import_profile)) {
    $parsed_file = cw_flexible_import_run_profile($cw_import_profile['id'], array());

    $fi_run_msg = "Import profile '<b>$cw_import_profile[name]</b>' has been run. Data is copied from hub to CW.";
    cw_call('cw_system_messages_add', array('flexible_import', $fi_run_msg));
    cw_call('cw_system_messages_show', array('flexible_import'));

}
//https://www.saratogawine.com/admin/index.php?target=import&mode=swe_after_import

db_query("update $tables[products] set status=0 where status=1 and product_id not in (select catalogid from xfer_products_SWE where hide=0)");
//db_query("delete from $tables[products_prices] where quantity=12 and product_id in (select catalogid from xfer_products_SWE where hide=0 and ctwelvebottleprice=0)");
db_query("delete from $tables[products_prices] where quantity>1 and product_id in (select catalogid from xfer_products_SWE where hide=0)");

//db_query("update $tables[products] p inner join cw_datahub_main_data dm on dm.catalog_id = p.product_id and dm.catalog_id != dm.ID and dm.catalog_id != 0 set p.status=0");
if ($config['flexible_import']['fi_manual_transition'] == "Y") {
    print("<h3>Import into CW tables is done...</h3><a href='index.php?target=datahub_transfer_after_import'>Run After Import Script</a>");
    cw_datahub_delay_autoupdate_release_lock();
} else {
    cw_header_location('index.php?target=datahub_transfer_after_import');
}

die;
