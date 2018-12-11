<?php
require_once('header.php');
//do a db back up each time
$_GET['filename'] = 'feeds_compare';
require("db_bu.php");
include('clean_money.php');

item_last::compare_del_item_last();
item_last::compare_insert_item_last();

item_store2_last::compare_del_item_store2_last();
item_store2_last::compare_insert_item_store2_last();

item_xref_last::compare_del_item_xref_last();
item_xref::compare_insert_item_xref();
//should it be this instead of the above?  keep it as the original for now
//item_xref_last::compare_insert_item_xref_last();


//these 2 are local but for now back online
//delete qs2000_item_last
//insert qs2000_item_last

//changed to work with new pos
//qs2000_item_last::delete_table();
//qs2000_item_last::compare_insert_qs2000_item_last();
pos_last::delete_table();
pos_last::compare_insert_pos_last();

feeds_item_compare::compare_insert_hub();
item::compare_update_item();

//item_xref::compare_insert_item_xref();//this is the 2nd time for the query which is in the original, is it needed though?
item_xref::compare_insert_item_xref_update();//instead of just an insert, use a INSERT IGNORE


/**
 * pos inserts for new items
 */
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

//changed to work with new pos
//qs2000_item::hub_update_POS_BinLocation();
//qs2000_item::hub_update_POS_notes();
BevAccessFeeds::update_supplier_id_item_xref();//added Feb 15, 2012
pos::hub_update_POS_BinLocation();
pos::hub_update_POS_notes();
pos::hub_update_POS_fields();

item::update_longdesc();

Compare_last::compare_del_compare_last();
Compare_last::Compare_copy_compare_last();
feeds_item_compare::delete_table(); 
//bevaccess_supplement::update_item_longdesc(); //not ready yet

//Skurnik_Image_Import::update_item_images();
//no longer needed for new pos
//qs2000_item::hub_set_POS_supplier();
echo '<br /><br /><h2>Feeds_AddUpdate_from_Compare is complete</h2>';
?>
