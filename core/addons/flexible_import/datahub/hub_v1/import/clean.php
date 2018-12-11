<?php
die('no access');
require_once('header.php');
//
//angels_share_feed -> Price
//Domaine_feed -> NY LIST PRICE
// EBD_feed -> CASE	BOTTLE	`PO CS`	`PO BTL`	SAVE
//Polaner_feed -> `Sug Retail Price`	`FL Price`
// triage_feed -> `Base Price`
//vias_feed ->	`Line`

//bof
//
////will be removing this
//$sql = "  ALTER TABLE `BWL_feed` CHANGE `RATING` `WINE RATING` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;";
//mysql_query($sql) or die($sql);
//
////will be removing this
//$sql = "  ALTER TABLE `Cordon_feed` CHANGE `Score` `Scores` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL  ";
//mysql_query($sql) or die($sql);
//
////will be removing this
//$sql = " ALTER TABLE `feeds_item_compare` ADD `supplier_id` INT NULL AFTER `qty_in_stock` ;";
//mysql_query($sql) or die($sql);
//$sql = " ALTER TABLE `feeds_item_compare` ADD INDEX ( `supplier_id` ) ;";
//mysql_query($sql) or die($sql);
//
////will be removing this
//$sql = " ALTER TABLE `Compare_last` ADD `supplier_id` INT NULL AFTER `qty_in_stock` ;";
//mysql_query($sql) or die($sql);
//$sql = " ALTER TABLE `Compare_last` ADD INDEX ( `supplier_id` ) ;";
//mysql_query($sql) or die($sql);
//
//
////will be removing this
//$sql = " ALTER TABLE `item_xref` ADD `supplier_id` INT NULL AFTER `store_id` ;";
//mysql_query($sql) or die($sql);
//$sql = " ALTER TABLE `item_xref` ADD INDEX ( `supplier_id` ) ;";
//mysql_query($sql) or die($sql);
//
////will be removing this
//$sql = " ALTER TABLE `xfer_products_DWB` ADD UNIQUE (`catalogid`);";
//mysql_query($sql) or die($sql);
//
////will be removing this
//$sql = ' ALTER TABLE `UP_VPR` ADD INDEX ( prod_id  )  ';
//mysql_query($sql) or die($sql);
//$sql = ' ALTER TABLE `UP_VPR` ADD INDEX ( prod_item  )  ';
//mysql_query($sql) or die($sql);
//
//
//$sql = "Delete from bear_feed";			
//mysql_query($sql) or die($sql);
//
//eof

//for final

$sql = "ALTER TABLE  `item_xref` ADD  `supplier_id` INT NULL ;";			
mysql_query($sql) or die($sql);

$sql = "ALTER TABLE  `item_xref` ADD INDEX (  `supplier_id` ) ;";
mysql_query($sql) or die($sql);
include('clean_money.php');
echo 'done';































