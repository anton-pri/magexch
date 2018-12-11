<?php

set_time_limit(86400);

cw_datahub_filltemp_hub_config();

cw_datahub_filltemp_feeds_item_compare();

//cw_datahub_filltemp_item_xref();

print('<h2>Calculating transfer tables</h2><br><hr>');

function cw_dh_make_copy_table($src_table, $copy_table) {
    db_query("DROP TABLE IF EXISTS $copy_table");
    db_query("CREATE TABLE IF NOT EXISTS $copy_table LIKE $src_table");
    db_query("INSERT INTO $copy_table SELECT * FROM $src_table");
}

require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');


item_last::compare_del_item_last();
item_last::compare_insert_item_last();

item_store2_last::compare_del_item_store2_last();
item_store2_last::compare_insert_item_store2_last();

item_xref_last::compare_del_item_xref_last();
item_xref::compare_insert_item_xref();
//--
pos_last::delete_table();
pos_last::compare_insert_pos_last();

feeds_item_compare::compare_insert_hub();

db_query("UPDATE $tables[datahub_main_data] dhmd INNER JOIN item i ON i.dhmd_id=dhmd.ID AND dhmd.catalog_id=0 SET dhmd.catalog_id=i.ID");
db_query("UPDATE item i INNER JOIN $tables[datahub_main_data_images] dhmd_i ON dhmd_i.item_id=i.dhmd_id AND i.dhmd_id!=0 SET i.cimageurl=dhmd_i.filename");


item::compare_update_item();

item_xref::compare_insert_item_xref_update();//instead of just an insert, use a INSERT IGNORE

/**
 * pos inserts for new items
 */
cw_dh_make_copy_table('cw_datahub_BevAccessFeeds', 'BevAccessFeeds');
cw_dh_make_copy_table('cw_datahub_beva_typetbl', 'BevA_Typetbl');
cw_dh_make_copy_table('cw_datahub_beva_company_supplierid_map', 'BevA_company_supplierID_map');



BevAccessFeeds::insert_pos();
Skurnik_feed::insert_pos();
Touton_feed::insert_pos();
Polaner_feed::insert_pos();
Domaine_feed::insert_pos();
angels_share_feed::insert_pos();
bear_feed::insert_pos();
vision_feed::insert_pos();
bowler_feed::insert_pos();
vias_feed::insert_pos();
verity_feed::insert_pos();
wildman_feed::insert_pos();
cw_import_feed::insert_pos();


pos::update_item_name();//tack on the Item Number to Item Name if it's not there already

/**
 * ask about BevA_insert_POS_SupplierList which uses pos SupplierList table
 * answer:  as of now, it doesn't need to done for new pos
 */

/**
 * ask about pos_update_supplier_costs() which uses pos SupplierList table
 * answer:  doesn't need to be done for new pos
 */
item_store2::compare_add_item_store_SWE_suppfeeds();
item_store2::compare_add_item_store_SWE_storefeed();
item_store2::compare_add_item_store_DWB_storefeed();

block_xref_from_compare::compare_add_to_block_list();
item_store2::compare_update_item_id_with_dup();
item_xref::Compare_update_item_xref_with_dup();

BevAccessFeeds::update_supplier_id_item_xref();//added Feb 15, 2012
pos::hub_update_POS_BinLocation();
pos::hub_update_POS_notes();
pos::hub_update_POS_fields();

//item::update_longdesc();

Compare_last::compare_del_compare_last();
Compare_last::Compare_copy_compare_last();
feeds_item_compare::delete_table();
//--end-of-step-6-Feeds-Add-Update-from-Compare---------------

//step 7 update_only
//cw_datahub_filltemp_item_xref();

//step 8 update prices

cw_dh_make_copy_table('cw_datahub_price_settings', 'hub_price_settings');
cw_dh_make_copy_table('cw_datahub_pricing_aipo', 'pricing_aipo');
cw_dh_make_copy_table('cw_datahub_pricing_IPO_avg_price', 'pricing_IPO_avg_price');


SWE_store_feed::SWE_update_pos_cost();

item_price::pricing_del_item_price();
item_price::pricing_calc_SWE();
item_price_twelve_bottle::build_twelve_bottle_price();//added Jan 11
item::pricing_apply_surcharges();
item_price::pricing_set_BWL_max_markup();
item_price::pricing_round_to_4();
item_price::pricing_set_manual_price_SWE();
item_store2::hub_update_POS_PriceA();//this updates MSRP(price) but not xref

//
db_query("update cw_datahub_main_data dmd inner join item_price ip on dmd.catalog_id=ip.item_id and ip.store_id=1 SET dmd.price=ip.price");
db_query("update cw_datahub_main_data dmd inner join item_price_twelve_bottle ip on dmd.catalog_id=ip.item_id and ip.store_id=1 SET dmd.twelve_bot_price=ip.price");


//step 9 prepare site update tables
item::hub_copy_images_from_similar_wines();
SWE_store_feed::SWE_store_import_and_update();

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
item::set_image_to_no_image_if_blank();
xfer_products_SWE::hub_delete_xfer_products_SWE();

xfer_products_SWE::hub_insert_xfer_from_store_SWE();
xfer_temp::hub_del_xfer_temp();
xfer_temp::hub_insert_xfer_from_feeds_SWE();
xfer_products_SWE::hub_update_xfer_from_feeds_SWE();
xfer_products_SWE::hub_update_xfer_from_item_SWE();


xfer_products_SWE::hub_set_hide_true_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_0_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_1_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_2_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_3_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_4_SWE();


xfer_products_SWE::hub_set_hide_true_for_certain_products_SWE();
xfer_products_SWE::hub_SWE_hide_no_vintage_if_similar_with_vintage();
xfer_products_SWE::hub_hide_price_less_than_3_SWE();
xfer_products_SWE::hub_set_min_qty_price_threshold_SWE();


xfer_products_SWE::hub_set_domaine_min_3_SWE();
xfer_products_SWE::hub_set_bnp_min_6_SWE();
xfer_products_SWE::hub_set_polaner_min_3();
xfer_products_SWE::hub_set_angels_min();
xfer_products_SWE::hub_set_bear_min();
xfer_products_SWE::hub_set_vision_min();
xfer_products_SWE::hub_set_vias_min();
xfer_products_SWE::hub_set_verity_min();
xfer_products_SWE::hub_set_wildman_min();
xfer_products_SWE::hub_set_cw_import_min();
//map supplier id to xfer products
xfer_products_SWE::set_supplierid();
xfer_products_SWE::update_images();

xfer_products_SWE::update_twelve_bot_price_SWE();
xfer_products_SWE::update_meta_description();
xfer_products_SWE::update_keywords();
xfer_products_SWE::in_stock_sale_swe();
xfer_products_SWE::set_twelvebot_price_to_zero();
xfer_products_SWE::set_twelvebot_cost_to_zero();


print("<h3>done...</h3><a href='index.php?target=datahub_run_cw_update'>Update cw database</a>");


die;
