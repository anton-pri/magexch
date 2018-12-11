<?php
/**
ALTER TABLE `item_store2` ADD INDEX ( `item_id` );
ALTER TABLE `item_store2` ADD INDEX ( `store_id` );
ALTER TABLE `item_store2` ADD INDEX ( `store_sku` );
ALTER TABLE `item_store2` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */
//this table has a unique index for item_id + store_id
//as a result, we're using insert ignore
class item_store2 extends feed {
	public static $table = 'item_store2';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}	
	
//Equivalent:  Compare_update_item_id_with_dup
	public static function compare_update_item_id_with_dup() {
		$sql = "UPDATE " . self::table_name() . " AS si 
						INNER JOIN item AS i ON si.item_id = i.id 
						SET si.item_id = i.dup_catid
						WHERE COALESCE(i.dup_catid,0) > 0";
		mysql_query($sql) or sql_error($sql);	
	}
//Equivalent:  hub_update_POS-PriceA
//changed from CHAR(q.ID) to q.ID
	public static function hub_update_POS_PriceA() {
		$sql = "UPDATE (item_store2 AS si 
						INNER JOIN pos AS q ON si.store_sku = q.`Item Number`) 
						INNER JOIN item_price AS pr ON si.item_id = pr.item_id and pr.store_id = 1 
						SET q.`MSRP` = pr.price
						WHERE si.store_id = 1 and q.`MSRP`=0";	
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  compare_add_item_store_SWE_suppfeeds
	public static function compare_add_item_store_SWE_suppfeeds() {
	//March 10, 2011, changed from SELECT COALESCE(c.catalogid,i.ID) to
		$sql = "INSERT IGNORE INTO item_store2 ( item_id, store_id, store_sku )
						SELECT IF(COALESCE(c.catalogid, 0) > 0, c.catalogid,i.ID), c.store_id, qs.`Item Number`
						FROM (feeds_item_compare AS c INNER JOIN pos AS qs ON c.xref = qs.`Custom Field 5`)
						LEFT JOIN item AS i ON (CONCAT(c.source , c.xref) = i.initial_xref OR c.xref=i.initial_xref)
						WHERE c.Source <> 'Hub' and c.Source <> 'Feed_SWE_store'
						and c.store_id = 1
						and (COALESCE(catalogid,0) <> 0 or COALESCE(c.Add_to_hub, 0) > 0)";
		mysql_query($sql) or sql_error($sql);			
	}

//Equivalent:  compare_add_item_store_SWE_storefeed
	public static function compare_add_item_store_SWE_storefeed() {
		pos::key_to_varchar();
	//March 10, 2011, changed from SELECT COALESCE(c.catalogid,i.ID) to
		$sql = "INSERT IGNORE INTO item_store2 ( item_id, store_id, store_sku )
						SELECT IF(COALESCE(c.catalogid, 0) > 0, c.catalogid,i.ID), c.store_id, qs.`Item Number`
						FROM (feeds_item_compare AS c INNER JOIN pos AS qs ON c.xref = qs.`Custom Field 5`) 
						LEFT JOIN item AS i ON (CONCAT(c.source , c.xref) = i.initial_xref OR c.xref=i.initial_xref)
						WHERE c.Source = 'Feed_SWE_store'
						and (COALESCE(catalogid,0) <> 0 or COALESCE(c.Add_to_hub, 0) > 0)";
		
		mysql_query($sql) or sql_error($sql);		
		pos::key_to_varchar();				
	}
//Equivalent:  compare_add_item_store_DWB_storefeed
	public static function compare_add_item_store_DWB_storefeed() {
	//March 10, 2011, changed from SELECT COALESCE(c.catalogid,i.ID) to
		$sql = "INSERT IGNORE INTO item_store2 ( item_id, store_id, store_sku )
						SELECT IF(COALESCE(c.catalogid, 0) > 0, c.catalogid,i.ID), c.store_id, c.xref
						FROM feeds_item_compare AS c LEFT JOIN item AS i ON (CONCAT(c.source , c.xref) = i.initial_xref OR c.xref=i.initial_xref) 
						WHERE c.Source = 'Feed_DWB_store'
						and (COALESCE(catalogid,0) <> 0 or COALESCE(c.Add_to_hub, 0) > 0)";
		mysql_query($sql) or sql_error($sql);				
	}

}//end class
