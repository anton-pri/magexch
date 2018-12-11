<?php

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

$export_count = cw_query_first_cell("SELECT count(*) FROM xfer_products_SWE");

print("<h3>Export table contains $export_count items</h3>");

die;

$search_prefilled = array();

$search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
$search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
$search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
$search_prefilled['page']           = ($page ? $page : 1);
$search_prefilled['unserialize_fields'] = true;

$all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

foreach($all_fi_profiles as $fi_prof) {
    if ($fi_prof['dbtable_src'] == 'xfer_products_SWE' && $fi_prof['import_src_type'] == 'T')
        $cw_import_profile = $fi_prof;
}

if (!empty($cw_import_profile)) {
    $parsed_file = cw_flexible_import_run_profile($cw_import_profile['id'], array());
}


die;
