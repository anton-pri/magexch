<?php
/**
ALTER TABLE `item_xref` ADD INDEX ( `item_id` );
ALTER TABLE `item_xref` ADD INDEX ( `cost_per_bottle` );
 ALTER TABLE `item_xref` ADD INDEX ( `store_id` ) ;
 *
 */
class item_xref extends feed {
	public static $table = 'item_xref';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	
//Equivalent:  Compare_insert_item_xref
	public static function compare_insert_item_xref() {
	//changed to insert supplier_id April 11, 2011
//		$sql = "INSERT INTO " . self::table_name()  . " ( item_id, xref, store_id )
//						SELECT COALESCE(i1.ID,i2.ID), c.xref, c.store_id
//						FROM (feeds_item_compare AS c LEFT JOIN item AS i1 ON c.catalogid = i1.id) LEFT JOIN item AS i2 ON CONCAT(c.source , c.xref) = i2.initial_xref
//						WHERE right(CONCAT('xxx' , c.Source),5) <> 'store' and c.Source <> 'Hub' and (COALESCE(c.catalogid,0) > 0 or c.add_to_hub=true)";

//		$sql = "INSERT IGNORE INTO  " . self::table_name()  . " ( item_id, xref, store_id, supplier_id )
//						SELECT COALESCE(i1.ID,i2.ID), c.xref, c.store_id, c.supplier_id
//						FROM (feeds_item_compare AS c LEFT JOIN item AS i1 ON c.catalogid = i1.id) LEFT JOIN item AS i2 ON CONCAT(c.source , c.xref) = i2.initial_xref
//						WHERE right(CONCAT('xxx' , c.Source),5) <> 'store' and c.Source <> 'Hub' and (COALESCE(c.catalogid,0) > 0 or c.add_to_hub=true)";
		$sql = "INSERT IGNORE INTO  " . self::table_name()  . " ( item_id, xref, store_id, supplier_id )
						SELECT COALESCE(i1.ID,i2.ID), c.xref, c.store_id, c.supplier_id
						FROM (feeds_item_compare AS c LEFT JOIN item AS i1 ON c.catalogid = i1.id) LEFT JOIN item AS i2 ON CONCAT(c.source , c.xref) = i2.initial_xref
						WHERE right(CONCAT('xxx' , c.Source),5) <> 'store' and c.Source <> 'Hub' and (COALESCE(c.catalogid,0) > 0 or c.add_to_hub=true) 
						AND COALESCE(i1.ID,i2.ID) > 0 AND c.store_id > 0";
		mysql_query($sql) or sql_error($sql);		
//		$sql = "Delete from item_xref where item_id = '0' or store_id = '0'";			
//		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function compare_insert_item_xref_update() {
	//added ON DUPLICATE KEY UPDATE  feb 24, 2011
	
	
//		$sql = "INSERT INTO " . self::table_name()  . " ( item_id, xref, store_id )
//						SELECT COALESCE(i1.ID,i2.ID), c.xref, c.store_id
//						FROM (feeds_item_compare AS c LEFT JOIN item AS i1 ON c.catalogid = i1.id) LEFT JOIN item AS i2 ON CONCAT(c.source , c.xref) = i2.initial_xref
//						WHERE right(CONCAT('xxx' , c.Source),5) <> 'store' and c.Source <> 'Hub' and (COALESCE(c.catalogid,0) > 0 or c.add_to_hub=true)";

//		$sql = "INSERT INTO " . self::table_name()  . " ( item_id, xref, store_id, supplier_id )
//						SELECT COALESCE(i1.ID,i2.ID), c.xref, c.store_id, c.supplier_id
//						FROM (feeds_item_compare AS c LEFT JOIN item AS i1 ON c.catalogid = i1.id) LEFT JOIN item AS i2 ON CONCAT(c.source , c.xref) = i2.initial_xref
//						WHERE right(CONCAT('xxx' , c.Source),5) <> 'store' and c.Source <> 'Hub' and (COALESCE(c.catalogid,0) > 0 or c.add_to_hub=true)
//						ON DUPLICATE KEY UPDATE 
//						item_id = COALESCE(i1.ID,i2.ID),
//						xref = c.xref,
//						store_id = c.store_id,
//						supplier_id = COALESCE(c.supplier_id, item_xref.supplier_id)";		
//		mysql_query($sql) or sql_error($sql);		

//		$sql = "INSERT IGNORE INTO  " . self::table_name()  . " ( item_id, xref, store_id, supplier_id )
//						SELECT COALESCE(i1.ID,i2.ID), c.xref, c.store_id, c.supplier_id
//						FROM (feeds_item_compare AS c LEFT JOIN item AS i1 ON c.catalogid = i1.id) LEFT JOIN item AS i2 ON CONCAT(c.source , c.xref) = i2.initial_xref
//						WHERE right(CONCAT('xxx' , c.Source),5) <> 'store' and c.Source <> 'Hub' and (COALESCE(c.catalogid,0) > 0 or c.add_to_hub=true)";
		$sql = "INSERT IGNORE INTO  " . self::table_name()  . " ( item_id, xref, store_id, supplier_id )
						SELECT COALESCE(i1.ID,i2.ID), c.xref, c.store_id, c.supplier_id
						FROM (feeds_item_compare AS c LEFT JOIN item AS i1 ON c.catalogid = i1.id) LEFT JOIN item AS i2 ON CONCAT(c.source , c.xref) = i2.initial_xref
						WHERE right(CONCAT('xxx' , c.Source),5) <> 'store' and c.Source <> 'Hub' and (COALESCE(c.catalogid,0) > 0 or c.add_to_hub=true) 
						AND COALESCE(i1.ID,i2.ID) > 0 AND c.store_id > 0";
		mysql_query($sql) or sql_error($sql);		
//		$sql = "Delete from item_xref where item_id = '0' or store_id = '0'";			
//		mysql_query($sql) or sql_error($sql);		
	}	
	
	
//Equivalent:  Compare_update_item_xref_with_dup
	public static function compare_update_item_xref_with_dup() {
		$sql = "UPDATE " . self::table_name()  . " AS si 
						INNER JOIN item AS i ON si.item_id = i.id 
						SET si.item_id = i.dup_catid
						WHERE COALESCE(i.dup_catid,0) > 0";
		mysql_query($sql) or sql_error($sql);					
	}


}//end class