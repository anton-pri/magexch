<?php
/**
ALTER TABLE `DWB_store_feed` ADD INDEX ( `Item #` ) ;
ALTER TABLE `DWB_store_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
#need these 2 changed for pricing_calc_DWB.  May have to change Cost as well
ALTER TABLE `DWB_store_feed` CHANGE `Avail Qty` `Avail Qty` INT( 11 ) NULL DEFAULT NULL  
ALTER TABLE `DWB_store_feed` CHANGE `DWB Min Price` `DWB Min Price` FLOAT( 8, 2 ) NULL DEFAULT NULL  
 */

/*
feed file must be adjusted
must add table_id, Field3, Field5, Field7, Field9, Field11, Field13, Field15, Field17, Field19
to fields
*/

//store 1
class DWB_store_feed extends feed {
	public static $table = 'DWB_store_feed';
	public static $feed_file = 'DWB_feed.csv';
	
	public static function table_name() {
		return self::$table;
	}
	
//Equivalent:  DWB_store_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}	

//Equivalent:  DWB_store_insert_compare	
	public static function store_insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, qty_in_stock )
						SELECT 'Feed_DWB_store' AS Source, Null AS Producer, `Product Name` AS Wine, Null AS Name, Null AS vintage, Null AS `size`, Trim(u.`Item #`) AS xref, Null AS bottles_per_case, Null AS catalogid, Null, Null, Trim(u.`Dept Name`), null, null AS `sub-appellation`, u.Cost, null, null, null, Null, '2', u.`Avail Qty`
						FROM DWB_store_feed AS u LEFT JOIN item_store2 AS i ON 
						u.`Item #`=i.store_sku 
						and i.store_id = '2'
						WHERE isNull(i.store_sku) and Trim(COALESCE(u.`Item #`,'')) <> ''";
		mysql_query($sql) or sql_error($sql);		
	}
	
	public static function delete_empties() {
		$sql = "DELETE FROM DWB_store_feed WHERE COALESCE(`Item #`, 0) = 0";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  DWB_store_import (macro)			
/**
 * @todo delete any records with no `Item #`
 * @update it's done (delete_empties) but not sure if it should be kept
 *
 */
	public static function store_import() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table, ",");
			self::delete_empties();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
}//end class	