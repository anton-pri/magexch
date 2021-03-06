<?php
//die("The script is disabled for maintainance");
set_time_limit(86400);
error_reporting(E_ALL);

cw_include('addons/flexible_import/include/func.datahub_dbg.php');

function cw_dh_calc_tick($str) {
    static $timer;
    if (is_null($timer)) $timer = cw_core_microtime();
    cw_log_add('calc_output_timer', (cw_core_microtime()-$timer).' sec');
//    $test_item_id = cw_query_first_cell("select value from cw_config where name='datahub_import_dbg_item_id'");
//    if ($test_item_id)
//        cw_datahub_dbg_save_snapshot($test_item_id, 'item_id');
//    cw_datahub_dbg_save_snapshot(799531, 'item_id');
//    cw_datahub_dbg_save_snapshot(451734, 'item_id');
//    cw_datahub_dbg_save_snapshot(374389, 'sku');
//   cw_datahub_dbg_save_snapshot('CWVIAS-USMCGABAL75-15', 'xref');
//cw_log_add(__FUNCTION__, cw_query_first("select max(`Item Number`), min(`Item Number`), count(*) from pos"));
    cw_flush($str);
}

function dh_dbg(){
    cw_dh_calc_tick('');
}

function dh_dbg2(){
    cw_dh_calc_tick('');
}

function cw_dh_make_copy_table($src_table, $copy_table) {
    db_query("DROP TABLE IF EXISTS $copy_table");
    db_query("CREATE TABLE IF NOT EXISTS $copy_table LIKE $src_table");
    db_query("INSERT INTO $copy_table SELECT * FROM $src_table");
}


// Beva always contains 9999 in qty_avail and over 10K such records
if (cw_query_first_cell("select count(*) from item_xref where qty_avail=9999")<10000)
    die("ERROR! Low number of xref items with qty 9999. Make sure all is correct!");

//if ($updated_items_count = cw_query_first_cell("select count(*) from cw_datahub_import_buffer ib inner join item_xref ix on ix.xref=ib.item_xref inner join cw_datahub_main_data md on md.ID=ix.item_id"))
//    die("ERROR! Working import buffer contains $updated_items_count mapped items! Please update mapped items first");

// Prevent double processing. Wait while prev process ends
if (!isset($dh_calc_step))
cw_datahub_delay_autoupdate("target=datahub_calc_output");

print('<h2>Calculating transfer tables</h2><br><hr>');

// No step. Initial actions
if (!isset($dh_calc_step)) {
    cw_datahub_filltemp_hub_config(); // Update native hub config table by settings from our store at admin/index.php?target=datahub_configuration
    db_query("update cw_datahub_main_data dmd set initial_xref='' where initial_xref in (select xref from item_xref) and initial_xref NOT IN (select xref from item_xref where item_id=dmd.id)");

    $test_item_id = cw_query_first_cell("select ID from cw_datahub_main_data where catalog_id=0 and (initial_xref like '%VERITY%' or initial_xref like '%WDMN%' or initial_xref like '%TOUT%') order by ID desc limit 1");
    cw_array2insert('config', array('name'=>'datahub_import_dbg_item_id', 'value'=>$test_item_id), true);

    // Try to resolve some DB inconsistency which possibly caused by interrupted script
    cw_datahub_clean_alus_for_correction();

    // Fill native HUB table feeds_item_compare by our data from main table cw_datahub_main_data. So internal logic of HUB can still work as supposed
    cw_datahub_filltemp_feeds_item_compare();cw_dh_calc_tick(". ");
}

//cw_datahub_filltemp_item_xref();

// Load constants and classes
require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');
error_reporting(E_ERROR);

if (!isset($dh_calc_step)) {
    $dh_calc_step = 5;  // No idea why 5. It is practically first step. Maybe it was link 5 in old hub sequence.
}

// STEP 5
if ($dh_calc_step == 5) {
    print('<h3>Correction of datahub images started</h3><br>');
    cw_datahub_correct_images(); // Correct images in cw_datahub_main_data_images. 

    print("<h3>Correction of datahub images is done...</h3>");

    cw_header_location("index.php?target=datahub_calc_output&dh_calc_step=6");
}


// STEP 6.
if ($dh_calc_step == 6) {
print('<h3>Rebuilding the hub service table</h3><br>');
// Make snapshots of table item and item_store2
// item - main inner table of old HUB
cw_dh_calc_tick(". "); 
item_last::compare_del_item_last();
cw_dh_calc_tick(". ");
item_last::compare_insert_item_last();
// item_store2 - relation between item_id and SKU
cw_dh_calc_tick(". ");
item_store2_last::compare_del_item_store2_last();
cw_dh_calc_tick(". ");
item_store2_last::compare_insert_item_store2_last();
// item_xref 
dh_dbg2();
cw_dh_calc_tick(". ");
item_xref_last::compare_del_item_xref_last();
cw_dh_calc_tick(". ");
item_xref::compare_insert_item_xref(); // append to item_xref some rows. which? nobody knows
// pos
dh_dbg2();
cw_dh_calc_tick(". ");
pos_last::delete_table();
cw_dh_calc_tick(". ");
pos_last::compare_insert_pos_last();

dh_dbg2();
cw_dh_calc_tick(". ");
// Add products  into inner table item WHERE c.add_to_hub=true and COALESCE(c.catalogid, 0) = 0 and c.Source <> 'Hub'";
feeds_item_compare::compare_insert_hub();

// Update catalog_id in datahub_main_data where it is not defined yet
db_query("UPDATE $tables[datahub_main_data] dhmd INNER JOIN item i ON i.dhmd_id=dhmd.ID AND dhmd.catalog_id=0 SET dhmd.catalog_id=i.ID");

// Try to correct item_id by known catalog_id
db_query("update item_xref i inner join $tables[datahub_main_data] dm on dm.ID=i.item_id and dm.ID!=dm.catalog_id and dm.catalog_id!=0 left join cw_datahub_dhmd_ids_fixed f on i.item_id=f.dhmd_id set i.item_id=dm.catalog_id where f.dhmd_id is null");

// Fill cw_datahub_dhmd_ids_fixed (potential warning)
db_query("replace into cw_datahub_dhmd_ids_fixed select ID from $tables[datahub_main_data] where ID!=catalog_id and catalog_id!=0");

// Sync images and ids
db_query("delete from $tables[datahub_main_data_images] where web_path like '%images/no_image.jpg%'");

db_query("update $tables[datahub_main_data_images] di inner join $tables[datahub_main_data] md on md.cimageurl=di.id and md.cimageurl!=0 set di.item_id=md.id");

db_query("UPDATE item i INNER JOIN $tables[datahub_main_data_images] dhmd_i ON dhmd_i.item_id=i.dhmd_id AND i.dhmd_id!=0 SET i.cimageurl=dhmd_i.web_path");

db_query("UPDATE item i INNER JOIN $tables[datahub_main_data] dhmd ON i.dhmd_id=dhmd.ID AND i.dhmd_id!=0 INNER JOIN $tables[datahub_main_data_images] dhmd_i ON dhmd_i.id=dhmd.cimageurl SET i.cimageurl=dhmd_i.web_path");

db_query("UPDATE item i INNER JOIN $tables[datahub_main_data] dhmd ON i.dhmd_id=dhmd.id AND i.dhmd_id!=0 INNER JOIN $tables[datahub_main_data_images] dhmd_i ON dhmd_i.id=dhmd.cimageurl AND dhmd_i.item_id=dhmd.id SET i.cimageurl=dhmd_i.web_path");

db_query("UPDATE item set cimageurl = REPLACE(cimageurl, '/images/', 'images/') WHERE cimageurl LIKE '/images/%'");
//db_query("UPDATE item set extendedimage=cimageurl where (cimageurl not like '%no_image%') and (extendedimage like '%no_image%' or extendedimage is NULL)");
db_query("UPDATE item set extendedimage=cimageurl where COALESCE(cimageurl,'') not like '%no_image%'");
dh_dbg2();

cw_dh_calc_tick(". ");
item::compare_update_item(); // Fill rest of data in table item

cw_dh_calc_tick(". ");
item_xref::compare_insert_item_xref_update();// append to item_xref some rows. which? nobody knows

/**
 * pos inserts for new items
 */
// Copy our table to hub table
cw_dh_calc_tick(". ");
cw_dh_make_copy_table('cw_datahub_BevAccessFeeds', 'BevAccessFeeds');
cw_dh_calc_tick(". ");
cw_dh_make_copy_table('cw_datahub_beva_typetbl', 'BevA_Typetbl');
cw_dh_calc_tick(". ");
cw_dh_make_copy_table('cw_datahub_beva_company_supplierid_map', 'BevA_company_supplierID_map');

$lost_items_count = cw_query("SELECT count(*) FROM cw_datahub_pos_lost_items");
if ($lost_items_count && 0) {
    cw_csvxc_logged_query("UPDATE feeds_item_compare fic INNER JOIN cw_datahub_pos_lost_items li ON li.catalogid=fic.catalogid INNER JOIN $tables[datahub_main_data] dmd ON dmd.catalog_id=fic.catalogid SET fic.Source=dmd.Source");


    cw_csvxc_logged_query("UPDATE feeds_item_compare fic INNER JOIN cw_datahub_pos_lost_items li ON li.catalogid=fic.catalogid INNER JOIN xfer_products_SWE xps ON xps.catalogid=fic.catalogid SET fic.xref=xps.ccode");

    cw_csvxc_logged_query("UPDATE $tables[datahub_main_data] dmd INNER JOIN cw_datahub_pos_lost_items li ON li.catalogid=dmd.catalog_id INNER JOIN xfer_products_SWE xps ON xps.catalogid=dmd.catalog_id SET dmd.initial_xref=xps.ccode");


    cw_csvxc_logged_query("UPDATE feeds_item_compare fic INNER JOIN cw_datahub_pos_lost_items li ON li.catalogid=fic.catalogid SET fic.Source='Feed_BEVA' WHERE CAST(Left(coalesce(fic.xref,'xxx'),3) as SIGNED) > 0 AND COALESCE(fic.Source,'')=''");

    cw_csvxc_logged_query("UPDATE $tables[datahub_main_data] dmd INNER JOIN cw_datahub_pos_lost_items li ON li.catalogid=dmd.catalog_id SET dmd.Source='Feed_BEVA' WHERE CAST(Left(coalesce(dmd.initial_xref,'xxx'),3) as SIGNED) > 0 AND COALESCE(dmd.Source,'')=''");


    cw_csvxc_logged_query("DELETE FROM cw_datahub_pos_lost_items");
}
dh_dbg2();

// Add to pos from different feeds
cw_dh_calc_tick(". ");
BevAccessFeeds::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
Skurnik_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
Touton_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
Polaner_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
Domaine_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
angels_share_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
bear_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
vision_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
bowler_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
vias_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
verity_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
wildman_feed::insert_pos();dh_dbg2();
cw_dh_calc_tick(". ");
cw_import_feed::insert_pos();

cw_dh_calc_tick(". ");
pos::update_item_name();// append Item Number to Item Name if it's not there already

/**
 * ask about BevA_insert_POS_SupplierList which uses pos SupplierList table
 * answer:  as of now, it doesn't need to done for new pos
 */

/**
 * ask about pos_update_supplier_costs() which uses pos SupplierList table
 * answer:  doesn't need to be done for new pos
 */
cw_dh_calc_tick(". ");
item_store2::compare_add_item_store_SWE_suppfeeds();dh_dbg2();
cw_dh_calc_tick(". ");
item_store2::compare_add_item_store_SWE_storefeed();dh_dbg2();
cw_dh_calc_tick(". ");
item_store2::compare_add_item_store_DWB_storefeed();dh_dbg2();

cw_dh_calc_tick(". ");
// Work with records marked as block, but block is set as 0 for all rows filled from datahub_main_data. 
// Possibly it is rudimental method from old process where table could be exported to Excel in the middle and imported back between stages
block_xref_from_compare::compare_add_to_block_list();dh_dbg2(); 
// Handle dup_catid - refer to some duplicated products
cw_dh_calc_tick(". ");
item_store2::compare_update_item_id_with_dup();dh_dbg2();
cw_dh_calc_tick(". ");
item_xref::Compare_update_item_xref_with_dup();dh_dbg2();

cw_dh_calc_tick(". ");
BevAccessFeeds::update_supplier_id_item_xref();//added Feb 15, 2012 setup supplier_id in item_xref
dh_dbg2();

cw_dh_calc_tick(". ");
pos::hub_update_POS_BinLocation();
dh_dbg2();
cw_dh_calc_tick(". ");
pos::hub_update_POS_notes(); // Add Vintage and Size to description
dh_dbg2();
cw_dh_calc_tick(". ");
pos::hub_update_POS_fields();
dh_dbg2();
//item::update_longdesc();

// Make a last snapshot of feeds_item_compare and clen it up
cw_dh_calc_tick(". ");
Compare_last::compare_del_compare_last();
cw_dh_calc_tick(". ");
Compare_last::Compare_copy_compare_last();
cw_dh_calc_tick(". ");
feeds_item_compare::delete_table();

cw_dh_calc_tick(". ");
cw_header_location("index.php?target=datahub_calc_output&dh_calc_step=8");
}
//--end-of-dh_calc_step-6-Feeds-Add-Update-from-Compare---------------

//dh_calc_step 7 update_only
//cw_datahub_filltemp_item_xref();

//dh_calc_step 8 update prices
if ($dh_calc_step == 8) {
print('<h3>Prices recalculation</h3><br>');
cw_dh_calc_tick(". ");
cw_dh_make_copy_table('cw_datahub_price_settings', 'hub_price_settings');
cw_dh_calc_tick(". ");
cw_dh_make_copy_table('cw_datahub_pricing_aipo', 'pricing_aipo');
cw_dh_calc_tick(". ");
cw_dh_make_copy_table('cw_datahub_pricing_IPO_avg_price', 'pricing_IPO_avg_price');

cw_dh_calc_tick(". ");
SWE_store_feed::SWE_update_pos_cost();
cw_dh_calc_tick(". ");
item_price::pricing_del_item_price();

dh_dbg2();
xfer_temp::hub_del_xfer_temp();
cw_dh_calc_tick(". ");
xfer_temp::hub_insert_xfer_from_feeds_SWE();
cw_dh_calc_tick(". ");

db_query("UPDATE IGNORE pos qs INNER JOIN xfer_temp xt ON xt.catalogid=qs.`Alternate Lookup` INNER JOIN item_xref ix ON ix.xref=xt.xref SET qs.`Custom Field 5`=xt.xref, qs.`Average Unit Cost`=ix.cost_per_bottle+ix.split_case_charge WHERE qs.`Qty 1`<=0");
db_query("UPDATE SWE_store_feed sf INNER JOIN pos p ON p.`Item Number`=sf.sku SET sf.cost=p.`Average Unit Cost` WHERE p.`Qty 1`<=0");

cw_dh_calc_tick(". ");
item_price::pricing_calc_SWE();dh_dbg2(); // Price calculation
cw_datahub_apply_price_levels('item_price'); // Price correction
cw_dh_calc_tick(". ");
item_price_twelve_bottle::build_twelve_bottle_price();//added Jan 11 // Price correction for wholesale levels
dh_dbg2();
cw_dh_calc_tick(". ");
item::pricing_apply_surcharges();dh_dbg2();
cw_dh_calc_tick(". ");
item_price::pricing_set_BWL_max_markup();dh_dbg2();
cw_dh_calc_tick(". ");
item_price::pricing_round_to_4();dh_dbg2();
cw_dh_calc_tick(". ");
item_price::pricing_set_manual_price_SWE();dh_dbg2(); // Take manual prices as higher priority
cw_dh_calc_tick(". ");
item_store2::hub_update_POS_PriceA();//this updates MSRP(price) but not xref
dh_dbg2();

// Transfer price, cost, stock to cw_datahub_main_data just for reference 
cw_dh_calc_tick(". ");
db_query("update cw_datahub_main_data dmd inner join item_price ip on dmd.catalog_id=ip.item_id and ip.store_id=1 SET dmd.price=ip.price");
cw_dh_calc_tick(". ");
db_query("update cw_datahub_main_data dmd inner join item_price_twelve_bottle ip on dmd.catalog_id=ip.item_id and ip.store_id=1 SET dmd.twelve_bot_price=ip.price");

db_query("update cw_datahub_main_data dmd inner join xfer_temp xt on dmd.catalog_id=xt.catalogid inner join item_xref ix on ix.item_id=xt.catalogid and ix.xref=xt.xref set dmd.bot_per_case=ix.bot_per_case, dmd.initial_xref=ix.xref, dmd.cost=ix.cost_per_bottle, dmd.cost_per_case=ix.cost_per_case, dmd.stock=ix.qty_avail, dmd.supplier_id=ix.supplier_id, dmd.split_case_charge=ix.split_case_charge");

cw_header_location("index.php?target=datahub_calc_output&dh_calc_step=9");
}

if ($dh_calc_step == 9) {
print("<h3>Output table calculation</h3><br>");
//dh_calc_step 9 prepare site update tables
cw_dh_calc_tick(". ");
item::hub_copy_images_from_similar_wines();
cw_dh_calc_tick(". ");
SWE_store_feed::SWE_store_import_and_update();
dh_dbg2();
/*
BevAccessFeeds::update_item_xref();
Skurnik_feed::update_item_xref();
bowler_feed::update_item_xref();
Touton_feed::update_item_xref();
Polaner_feed::update_item_xref();
Domaine_feed::update_item_xref();
bear_feed::update_item_xref();
vision_feed::update_item_xref();
angels_share_feed::update_item_xref();
BWL_feed::update_item_xref();
EBD_feed::update_item_xref();
acme_feed::update_item_xref();
noble_feed::update_item_xref();
Cordon_feed::update_item_xref();
triage_feed::update_item_xref();
cellar_feed::update_item_xref();
vehr_feed::update_item_xref();
vias_feed::update_item_xref();
grape_feed::update_item_xref();
vinum_feed::update_item_xref();
verity_feed::update_item_xref();
wildman_feed::update_item_xref();
cw_import_feed::update_item_xref();
*/
//cw_datahub_filltemp_item_xref();
cw_dh_calc_tick(". ");
item::set_image_to_no_image_if_blank();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_delete_xfer_products_SWE(); // Full cleanup table
cw_dh_calc_tick(". ");

/*
xfer_temp::hub_del_xfer_temp();
cw_dh_calc_tick(". ");
xfer_temp::hub_insert_xfer_from_feeds_SWE();
cw_dh_calc_tick(". ");
*/
db_query("UPDATE IGNORE pos qs INNER JOIN xfer_temp xt ON xt.xref!=qs.`Custom Field 5` AND xt.catalogid=qs.`Alternate Lookup` SET qs.`Custom Field 5`=xt.xref, qs.`Average Unit Cost`=xt.cost WHERE qs.`Qty 1`<=0");
//*/

xfer_products_SWE::hub_insert_xfer_from_store_SWE();
cw_dh_calc_tick(". ");
/*
xfer_temp::hub_del_xfer_temp();
cw_dh_calc_tick(". ");
xfer_temp::hub_insert_xfer_from_feeds_SWE();
cw_dh_calc_tick(". ");
*/

xfer_products_SWE::hub_update_xfer_from_feeds_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_update_xfer_from_item_SWE();dh_dbg();

cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_hide_true_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_status_for_avail_code_0_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_status_for_avail_code_1_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_status_for_avail_code_2_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_status_for_avail_code_3_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_status_for_avail_code_4_SWE();dh_dbg();

cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_hide_true_for_certain_products_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_SWE_hide_no_vintage_if_similar_with_vintage();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_hide_price_less_than_3_SWE();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_min_qty_price_threshold_SWE();

cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_domaine_min_3_SWE();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_bnp_min_6_SWE();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_polaner_min_3();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_angels_min();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_bear_min();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_vision_min();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_vias_min();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_verity_min();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_wildman_min();
cw_dh_calc_tick(". ");
xfer_products_SWE::hub_set_cw_import_min();
dh_dbg();
//map supplier id to xfer products
cw_dh_calc_tick(". ");
xfer_products_SWE::set_supplierid_by_item_xref();dh_dbg();
xfer_products_SWE::set_supplierid_by_pos();dh_dbg();
xfer_products_SWE::set_supplierid_by_item_price();dh_dbg();
cw_dh_calc_tick(". ");
//xfer_products_SWE::update_images(); - bevaimages are disabled, see SW-339

cw_dh_calc_tick(". ");
xfer_products_SWE::update_twelve_bot_price_SWE();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::update_meta_description();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::update_keywords();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::in_stock_sale_swe();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::set_twelvebot_price_to_zero();dh_dbg();
cw_dh_calc_tick(". ");
xfer_products_SWE::set_twelvebot_cost_to_zero();dh_dbg();
cw_header_location("index.php?target=datahub_calc_output&dh_calc_step=10");
}

if ($dh_calc_step == 10) {
print("<h3>Updating prices and stock values in output table</h3><br>");
/*
db_query("update pos pt inner join cw_datahub_main_data cl on (cl.initial_xref=CONCAT('Feed_BEVA', pt.`Custom Field 5`)) and pt.`Vendor Code`=0 and pt.`Vendor Name`='System' left join Supplier s on s.supplier_id=cl.supplier_id set pt.`Vendor Code`=cl.supplier_id, pt.`Vendor Name`=s.SupplierName");

db_query("update pos pt inner join cw_datahub_main_data cl on (cl.initial_xref=pt.`Custom Field 5`) and pt.`Vendor Code`=0 and pt.`Vendor Name`='System' left join Supplier s on s.supplier_id=cl.supplier_id set pt.`Vendor Code`=cl.supplier_id, pt.`Vendor Name`=s.SupplierName");
*/

db_query("update pos p inner join Supplier s on s.SupplierName=p.`Vendor Name` and p.`Vendor Code`=0 set p.`Vendor Code`=s.supplier_id");dh_dbg();
db_query("update pos set `Custom Price 1`=MSRP where `Custom Price 1`=0 and MSRP> 0");dh_dbg();

db_query("update cw_datahub_main_data_images di inner join item i on i.ID=di.item_id and i.cimageurl not like '%no_image%' and di.filename like '%no_image%' set di.filename=i.cimageurl, di.web_path=i.cimageurl, di.system_path=i.cimageurl");

dh_dbg();
db_query("update xfer_products_SWE xps inner join cw_datahub_main_data dhmd on xps.catalogid=dhmd.catalog_id set xps.cprice=dhmd.manual_price where dhmd.manual_price != 0");dh_dbg();
db_query("update xfer_products_SWE xps inner join cw_datahub_main_data dhmd on xps.catalogid=dhmd.catalog_id set xps.ctwelvebottleprice=dhmd.twelve_bot_manual_price where dhmd.twelve_bot_manual_price != 0");dh_dbg();


//db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and (p.`Custom Field 4`=1 OR coalesce(`Custom Field 4`,'')='') and xps.hide=0 set xps.avail_code=1");
/*
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and (coalesce(p.`Custom Field 4`,0)=0 OR coalesce(p.`Custom Field 4`,'')='') and xps.hide=0 left join item_xref ix on ix.item_id=xps.catalogid and ix.xref=xps.ccode set xps.avail_code=0, xps.cstock=p.`Qty 1`, xps.pother1=p.`Qty 1`+IFNULL(ix.qty_avail,0)");

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and p.`Custom Field 4`=1 and xps.hide=0 set xps.avail_code=1, xps.cstock=p.`Qty 1`, xps.pother1=p.`Qty 1`");

db_query("update xfer_products_SWE set hide=1 where avail_code=1 and cstock<=0 and hide=0");

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and p.`Custom Field 4`=2 and xps.hide=0 set xps.avail_code=2, xps.cstock=p.`Qty 1`, xps.pother1='9999'");
db_query("update xfer_products_SWE set pother1='9999' where avail_code=2");

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and p.`Custom Field 4`=-1 set xps.avail_code=-1");
db_query("update xfer_products_SWE set hide=1 where avail_code=-1");
*/
dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Custom Field 5`=xps.ccode and xps.sku IS NULL and p.`Custom Field 4`=1 set xps.sku=p.`Item Number`, xps.cstock=p.`Qty 1`, xps.avail_code=1, p.`Alternate Lookup`=xps.catalogid");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Custom Field 5`=xps.ccode and xps.sku IS NULL and p.`Custom Field 4`=2 set xps.sku=p.`Item Number`, xps.cstock=p.`Qty 1`, xps.avail_code=2, p.`Alternate Lookup`=xps.catalogid");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Custom Field 5`=xps.ccode and xps.sku IS NULL set xps.sku=p.`Item Number`, xps.cstock=p.`Qty 1`, p.`Alternate Lookup`=xps.catalogid");


db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and (coalesce(p.`Custom Field 4`,0)=0 OR coalesce(p.`Custom Field 4`,'')='') set xps.avail_code=0");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.avail_code=0 left join item_xref ix on ix.item_id=xps.catalogid and ix.xref=xps.ccode set xps.cstock=p.`Qty 1`, xps.pother1=p.`Qty 1`+IFNULL(ix.qty_avail,0)");dh_dbg();

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and p.`Custom Field 4`=1 set xps.avail_code=1");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.avail_code=1 set xps.cstock=p.`Qty 1`, xps.pother1=p.`Qty 1`");

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and p.`Custom Field 4`=2 set xps.avail_code=2");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.avail_code=2 set xps.cstock=p.`Qty 1`, xps.pother1='9999'");

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and p.`Custom Field 4`=-1 set xps.avail_code=-1");dh_dbg();
db_query("update xfer_products_SWE set hide=1 where avail_code=-1");

db_query("drop table if exists pos_tmp");
db_query("create temporary table pos_tmp like pos"); 
db_query("insert into pos_tmp select * from pos"); 
db_query("alter table pos_tmp add column item_id int(11) not null default 0");
db_query("update pos_tmp set item_id=cast(`Alternate Lookup` as signed)"); 
db_query("alter table pos_tmp add index pst_itemid (item_id)");
db_query("update xfer_products_SWE xps left join pos_tmp p on p.item_id=xps.catalogid set xps.avail_code=0, xps.cstock=0, xps.pother1=9999 where p.item_id is null");dh_dbg();

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid left join item_xref ix on ix.item_id=xps.catalogid and ix.xref=xps.ccode set avail_code=0, cstock=0, pother1=9999 where IFNULL(ix.qty_avail,0)+p.`Qty 1`=0 and xps.avail_code!=-1 and xps.avail_code!=1 and xps.hide=0");dh_dbg();


//db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 3`>0 and p.`Custom Price 3`!=xps.cprice set xps.cprice=p.`Custom Price 3`");


//retail price set (list price in cw)
//db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 3`=0 set xps.retailprice=p.`Custom Price 1`");

/*disabled at 18-01-07 because new prices have been overwriten by old ones from pos 
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 3`=0 set xps.retailprice=xps.cprice");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 3`>0 set xps.retailprice=p.`Custom Price 3`");dh_dbg();

//selling price set (SW-262)
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 2`=0 set xps.cprice=xps.retailprice");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 2`>0 set xps.cprice=p.`Custom Price 2`");dh_dbg();



//retail price set (list price in cw)
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 3`=0 set xps.retailprice=p.`Custom Price 1`");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 3`>0 set xps.retailprice=p.`Custom Price 3`");dh_dbg();

//selling price set (SW-262)
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 2`=0 set xps.cprice=xps.retailprice");dh_dbg();
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 2`>0 set xps.cprice=p.`Custom Price 2`");dh_dbg();
*/

//db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 4`>0 and p.`Custom Price 4`!=xps.ctwelvebottleprice and p.`Custom Price 3` > 0 and xps.cstock >=12 and xps.ctwelvebottleprice>0 set xps.ctwelvebottleprice = p.`Custom Price 4`");
db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 4`>0 and p.`Custom Price 4`!=xps.ctwelvebottleprice set xps.ctwelvebottleprice=p.`Custom Price 4`");dh_dbg();

db_query("update xfer_products_SWE set ctwelvebottleprice=0 where ctwelvebottleprice=cprice");dh_dbg();

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and xps.hide=0 and p.`Custom Price 2`>0 set xps.ctwelvebottleprice=0");dh_dbg();

db_query("UPDATE xfer_products_SWE xps INNER JOIN cw_datahub_main_data dhmd ON xps.catalogid=dhmd.catalog_id AND xps.hide=0 SET
           xps.cdescription = dhmd.LongDesc,
           xps.RobertParkerRating = dhmd.RP_Rating,
           xps.RobertParkerReview = dhmd.RP_Review,
           xps.WineSpectatorRating = dhmd.WS_Rating,
           xps.WineSpectatorReview = dhmd.WS_Review, 
           xps.WineEnthusiaisRating = dhmd.WE_Rating,
           xps.WineEnthusiastReview = dhmd.WE_Review,
           xps.StephenTanzerRating = dhmd.ST_Rating,
           xps.StepehnTanzerReview = dhmd.ST_Review, 
           xps.DecanterRating = dhmd.DC_Rating,
           xps.DecanterReview = dhmd.DC_Review,
           xps.BeverageTastingInstituteRating = dhmd.BTI_Rating,
           xps.BeverageTastingInstituteReview = dhmd.BTI_Review,
           xps.MichaelJacksonRating = dhmd.MJ_Rating,
           xps.MichaelJacksonReview = dhmd.MJ_Review,
           xps.WineSpiritsRating = dhmd.W_S_Rating,
           xps.WineSpiritsReview = dhmd.W_S_Review,
           xps.WineryReview = dhmd.Winery_Review");

db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid set xps.cost=p.`Average Unit Cost`");dh_dbg();

db_query("update xfer_products_SWE set hide=1 where cast(pother1 as signed)<=0 and hide=0");dh_dbg();

//db_query("update xfer_products_SWE xps inner join cw_datahub_BevAccessFeeds beva on xps.ccode=beva.xref and beva.companies like '%MSCOTT%' inner join pos p on p.`Custom Field 5`=xps.ccode and p.`Qty 1` <= 0 set xps.hide=1");
db_query("update xfer_products_SWE xps inner join cw_datahub_BevAccessFeeds beva on beva.xref=xps.ccode and (beva.current!='Y' or beva.confstock!='Y') set xps.pother1=greatest(xps.cstock, cast(xps.pother1 as signed)-9999) where xps.hide=0 and xps.cstock>0");dh_dbg();

db_query("update xfer_products_SWE xps inner join cw_datahub_BevAccessFeeds beva on beva.xref=xps.ccode and (beva.current!='Y' or beva.confstock!='Y') set xps.hide=1 where xps.hide=0 and xps.cstock<=0");dh_dbg();

//db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and coalesce(p.`Custom Field 4`,'')='' and coalesce(p.`Qty 1`,0)>0 and xps.avail_code=2 and xps.hide=0 set xps.cstock=p.`Qty 1`, xps.avail_code=1");
//db_query("update xfer_products_SWE xps inner join pos p on p.`Alternate Lookup`=xps.catalogid and coalesce(p.`Custom Field 4`,'')='' and coalesce(p.`Qty 1`,0)=0 and xps.avail_code=2 and xps.hide=0 and xps.cstock=0 inner join item_xref ix on ix.item_id=xps.catalogid and ix.xref=xps.ccode and ix.qty_avail>0 and ix.qty_avail < 9999 set xps.cstock=ix.qty_avail, xps.avail_code=1");
cw_header_location("index.php?target=datahub_calc_output&dh_calc_step=11");
}

if ($dh_calc_step == 11) {

print("<h3>Updating multicase prices in output table</h3><br>");

cw_csvxc_logged_query("drop table if exists item_xref_temp");
cw_csvxc_logged_query("create table item_xref_temp like item_xref");
cw_csvxc_logged_query("insert into item_xref_temp select * from item_xref");

$lvl_min = 2;
$lvl_max = 6;

for ($i=$lvl_min; $i<=$lvl_max; $i++) {
    cw_csvxc_logged_query("update item_xref_temp set cost_per_bottle$i=cost_per_case$i/bot_per_case where bot_qty$i>0 and cost_per_case$i>0 and bot_per_case>0");
}

for ($i=$lvl_min; $i<=$lvl_max; $i++) {

cw_csvxc_logged_query("drop table if exists item_price_bottle_lvl$i");
cw_csvxc_logged_query("create table item_price_bottle_lvl$i like item_price_twelve_bottle");
cw_csvxc_logged_query("alter table item_price_bottle_lvl$i add column bot_qty int(11) not null default 0");

cw_csvxc_logged_query("
INSERT INTO
   item_price_bottle_lvl$i ( item_id, price, store_id, cost, bot_qty)
   SELECT
      id,
      IF(CAST(price as DECIMAL(10, 2)) < CAST(bminprice as DECIMAL(10, 2)), bminprice, price),
      1,
      bCost,
      bot_qty
   FROM
      (
         SELECT
            id,
            round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup), bCost * (1 + SWE_max_markup), calc_price)), 1) - 0.06 AS price,
            bminprice,
            bCost,
            bot_qty
         FROM
            (
               SELECT
                  ID,
                  ps.SWE_min_markup,
                  ps.SWE_max_markup,
                  IF(store_qty > 0, ps.SWE_min_order_profit_instock, ps.SWE_min_order_profit) as min_order_profit,
                  IF(bCost <=
                  (
                     select
                        SWE_cost_threshold
                     from
                        hub_price_settings
                     where
                        store_id = 1
                  ) 
                  , SWE_min_qty_under_cost_threshold, COALESCE(baipo, 9)) as items_per_order,
                  bCost + (IF(store_qty > 0, ps.SWE_min_order_profit_instock, ps.SWE_min_order_profit) / (COALESCE((IF(bCost <=
                  (
                     select
                        SWE_cost_threshold
                     from
                        hub_price_settings
                     where
                        store_id = 1
                  )
                  , SWE_min_qty_under_cost_threshold, COALESCE(baipo, 9))), 2) + .01)) AS calc_price,
                  bCost,
                  bminprice,
                  bot_qty
               FROM
                  hub_price_settings ps
                  inner join
                     (
                        select
                           i.ID,
                           xf.cost_per_bottle$i+(IF(xf.bot_qty$i<xf.bot_per_case,xf.split_case_charge,0)) as bcost,
                           Max(IF(COALESCE(xf.min_price, 0) = 0, COALESCE(sf.min_price, 0), COALESCE(xf.min_price, 0))) as bminprice,
                           Max(COALESCE(sf.qty, 0) + COALESCE(xf.qty_avail, 0)) as btqty,
                           Sum(sf.qty) as store_qty,
                           Avg(pa.aipo) as baipo,
                           1 as store_id,
                           xf.bot_qty$i as bot_qty
                        from
                           (
((item i
                              left join
                                 item_store2 si
                                 on (i.ID = si.item_id
                                 and si.store_id = 1))
                              left join
                                 SWE_store_feed sf
                                 on (si.store_sku = sf.sku
                                 and sf.qty > 0)) 
                              left join
                                 item_xref_temp xf
                                 on (i.ID = xf.item_id
                                 and xf.store_id = 1)
                           )
                           left join
                              pricing_aipo pa
                              on i.id = pa.item_id
                        where
                           COALESCE(xf.cost_per_bottle$i) > 0
                           AND COALESCE(sf.qty, 0) + COALESCE(xf.qty_avail, 0) > 0
                        group by
                           i.ID 
                     )
                     b
                     on b.store_id = ps.store_id
            )
            AS x
      )
      AS z
");

cw_datahub_apply_price_levels("item_price_bottle_lvl$i");

$oversize_surcharge_sizes = array('1.5Ltr','3.0Ltr','4Ltr','6Ltr','1.75Ltr','5Ltr','1.8Ltr','18Ltr','15Ltr','12Ltr','5.0Ltr' ,'9Ltr', '19Ltr', '3Ltr', '30Ltr', '1.8l', '20Ltr', '1.5Lltr', '2Ltr', '2.25Ltr', '19.8Ltr', '18.9Ltr', '1.5L', '6.0Ltr', '2.0Ltr', '15.0Ltr', '9.0Ltr', '12.0Ltr', '18.0Ltr', '19.800Ltr', '19.0Ltr', '1.750Ltr');

$sql = "UPDATE (hub_price_settings AS ps INNER JOIN item_price_bottle_lvl$i AS i ON ps.store_id = i.store_id) 
        INNER JOIN item AS i2 ON i.item_id = i2.ID 
        SET i.price = i.price + ps.oversize_surcharge
        WHERE i2.Size IN ('".implode("','", $oversize_surcharge_sizes)."')";

cw_csvxc_logged_query($sql);

}

//TODO:--some more filter procedures here
for ($i=$lvl_min; $i<=$lvl_max; $i++) {
     $sql = "UPDATE (xfer_products_SWE AS x INNER JOIN item ON x.catalogid = item.id) 
                    LEFT JOIN item_price_bottle_lvl$i AS pr ON item.id = pr.item_id and pr.store_id = '1' 
                    SET 
                    x.bot_qty$i = pr.bot_qty,
                    x.price_qty$i = COALESCE(pr.price,'0'),
                    x.cost_qty$i = COALESCE(pr.cost,'0')";
     cw_csvxc_logged_query($sql);

     $sql = "UPDATE xfer_products_SWE 
                   SET price_qty$i = 0
                   WHERE (cprice <= price_qty$i 
                   AND COALESCE(cprice, 0) > 0) OR price_qty$i<0";
     cw_csvxc_logged_query($sql);
}


cw_header_location("index.php?target=datahub_calc_output&dh_calc_step=12");
}

if ($dh_calc_step == 12) {
//check if there are lost items
    cw_csvxc_logged_query("DROP TABLE IF EXISTS cw_datahub_pos_lost_items");
    cw_csvxc_logged_query("CREATE TABLE cw_datahub_pos_lost_items (catalogid int(11) NOT NULL DEFAULT 0, possible_sku int(11) not null default 0, PRIMARY KEY (catalogid))");

    cw_csvxc_logged_query("INSERT IGNORE INTO cw_datahub_pos_lost_items (catalogid, possible_sku) SELECT xps.catalogid, is2.store_sku FROM xfer_products_SWE xps LEFT JOIN item_store2 is2 ON is2.item_id=xps.catalogid WHERE xps.catalogid NOT IN (select CAST(`Alternate Lookup` as signed) FROM pos) AND xps.hide=0");
    $lost_items = cw_query("SELECT li.catalogid, xps.ccode FROM cw_datahub_pos_lost_items li inner join xfer_products_SWE xps on xps.catalogid=li.catalogid");

    if ($lost_items && 0) {
        print("<h2>WARNING!!!</h2><h3>The following items (".count($lost_items).") have been detected as present in xfer_products_SWE and missing in hub pos tables</h3>");  
        foreach ($lost_items as $li) {  
            print("<p>Alternate Lookup: $li[catalogid], XREF: $li[ccode]".(!empty($li['possible_sku'])?(", Item_store2 sku: $li[possible_sku]"):(''))."</p>");   
        } 
        print("<h3>Its recommended to correct items by <a href='index.php?target=datahub_calc_output'>running hub script again</a> </h3><br>");
        print("<h3><a href='index.php?target=datahub_run_cw_update'>Ignore missing items and proceed with final Step: Update cw database (in this case all items missing in pos will be blocked on the live store as well)</a></h3>");
        cw_datahub_delay_autoupdate_release_lock();
        exit;  
    }
    cw_header_location("index.php?target=datahub_calc_output&dh_calc_step=13");
}

if ($dh_calc_step == 13) {
    print("<h2>Process update step is running</h2><br>");

    global $datahub_import_stop4manual;

    require('core/addons/flexible_import/datahub/function_step_pos_update.php');
    datahub_import_step_pos_update(0);


    if ($config['flexible_import']['fi_automated_export_pos'] == "Y") {

        if (!$datahub_import_stop4manual) {
            print("<hr><h2>Export new pos items step is running</h2><br>");
            require('core/addons/flexible_import/datahub/function_export_new_pos.php');
            datahub_import_export_new_pos(0);

            print("<hr><h2>Export changed pos items step is running</h2><br>");
            require('core/addons/flexible_import/datahub/function_export_changed_pos.php');
            datahub_import_export_changed_pos(0);
        } else {
            print("<h2>ERROR!!! Cannot run export new/changed pos items because of errors at Process Update step (step 12a)</h2>");
        }
    } else {
        print("<h2>Export new items to pos and Export changed items are disabled</h2><br>");
        if ($datahub_import_stop4manual)
            print("<h2>Detected errors at Process Update step (step 12a)</h2>");
    }
}

if ($config['flexible_import']['fi_manual_transition'] == "Y" || 1) {
    print("<h3>done...<br><a href='index.php?target=datahub_run_cw_update'>Final Step: Update cw database</a></h3>");
    cw_datahub_delay_autoupdate_release_lock();
} else {
    cw_datahub_delay_autoupdate_release_lock();
    cw_header_location('index.php?target=datahub_run_cw_update');
}


die;
