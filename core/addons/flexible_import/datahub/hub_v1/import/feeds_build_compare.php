<?php
//ini_set("display_errors","2");
//ERROR_REPORTING(E_ALL);
require_once('header.php');
include('clean_money.php');


feeds_item_compare::delete_table();

BWL_feed::insert_compare();
BevAccessFeeds::insert_compare_union_feeds_item();

BevAccessFeeds::update_supplier_id(); //adds supplier ids for bev including domaine.  This also converts colony from supplier_id 10 to 88
Cordon_feed::insert_compare();
DWB_store_feed::store_insert_compare();
Domaine_feed::insert_compare();
EBD_feed::insert_compare();
Polaner_feed::insert_compare();
Skurnik_feed::insert_compare();
Touton_feed::insert_compare();
acme_feed::insert_compare();
angels_share_feed::insert_compare();
bear_feed::insert_compare();
bowler_feed::insert_compare();
cellar_feed::insert_compare();
noble_feed::insert_compare();
triage_feed::insert_compare();
vehr_feed::insert_compare();
vision_feed::insert_compare();
vias_feed::insert_compare();
grape_feed::insert_compare();
vinum_feed::insert_compare();
verity_feed::insert_compare();
cru_feed::insert_compare();
cavatappi_feed::insert_compare();
wildman_feed::insert_compare();
cw_import_feed::insert_compare();

SWE_store_feed::SWE_store_insert_compare();//SWE_store_insert_compare?//probably will be local but back online for now

//

feeds_item_compare::compare_clean_name_field();
feeds_item_compare::compare_mark_for_deletion();
feeds_item_compare::compare_delete_marked();
feeds_item_compare::compare_set_producer_to_wine();
feeds_item_compare::delete_nocost();
feeds_item_compare::modify_size();
feeds_item_compare::clean_data();
echo "Done. On shop comp or locally, edit feeds item compare table<br />";
