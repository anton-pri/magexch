<?php 
/**
 ALTER TABLE `SWE_store_feed` ADD PRIMARY KEY ( `sku` ) ;
 ALTER TABLE `SWE_store_feed` ADD INDEX ( `qty` )  
 did add sku_id as the end of the table as a type int for queries on qs2000 but not longer using, drop that column for now
 */

class SWE_store_feed extends feed {
	public static $table = 'SWE_store_feed';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}	

//Equivalent:  SWE_store_feed_qty_to_0
	public static function SWE_store_feed_qty_to_0() {
		$sql = "UPDATE " . self::table_name() . " 
						SET qty = 0, manual_price = 0, min_price = 0;";
		mysql_query($sql) or sql_error($sql);
	}	
	
//Equivalent:  	SWE_store_insert_compare
//ask Josh about new fields
	public static function SWE_store_insert_compare() {
                global $config; 
		pos::key_to_varchar();
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, 
						Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, qty_in_stock, supplier_id )
						SELECT \"Feed_SWE_store\" AS Source,
						u.`Manufacturer` AS Producer,
						CONCAT(Trim(u.`Item Description`) , u.`Item Name`) AS Wine,
						Null AS Name,
						u.Attribute AS vintage,
						u.Size AS `size`,
						CAST(u.`Item Number` as CHAR) AS xref,
						Null AS bottles_per_case,
						Null AS catalogid,
						null as  Region,
						u.`Custom Field 1` as country,
						u.`Custom Field 2` as varietal,
						null,
						null AS `sub-appellation`,
						CAST(COALESCE(u.`Average Unit Cost`,0) as DECIMAL(10,2)),
						null,
						null,
						null,
						Null,
						'1',
						u.`Qty 1`,
						u.`Vendor Code`
						FROM  pos AS u left JOIN item_store2 AS i ON (u.`Item Number` = i.store_sku) and (i.store_id = 1)
						WHERE isNull(i.store_sku)";
		mysql_query($sql) or sql_error($sql);		
		pos::key_to_id();			
	}	
	
	//Equivalent:  SWE_update_store_feed
	public static function SWE_update_store_feed() {
                $sql = "UPDATE SWE_store_feed sf SET avail_code=0 WHERE sf.sku IN (SELECT `Item Number` FROM pos)";
                mysql_query($sql) or sql_error($sql);

		pos::key_to_varchar();
		$sql = "INSERT INTO SWE_store_feed( sku, qty, cost, manual_price, min_price, twelve_bot_manual_price, avail_code)
						SELECT qi.`Item Number`, qi.`Qty 1`, qi.`Average Unit Cost`, 
						IF( COALESCE( qi.`Custom Price 3`, 0 ) = 0, sf.manual_price, qi.`Custom Price 3`), 
						IF( COALESCE( qi.`Custom Price 2`, 0 ) = 0, sf.min_price, qi.`Custom Price 2` ), 
						IF( COALESCE( qi.`Custom Price 4`, 0 ) = 0, twelve_bot_manual_price, qi.`Custom Price 4` ), 						
						IF( Trim( COALESCE( qi.`Custom Field 4`, '' ) ) = '', COALESCE( sf.avail_code, 0 ) , CAST( qi.`Custom Field 4` AS SIGNED ) ) 
						FROM SWE_store_feed AS sf
						RIGHT JOIN pos AS qi ON sf.sku = qi.`Item Number` 
						ON DUPLICATE KEY UPDATE sku = qi.`Item Number`,
						qty = qi.`Qty 1`,
						cost = qi.`Average Unit Cost`,
						manual_price = IF( COALESCE( qi.`Custom Price 3`, 0 ) =0, sf.manual_price, qi.`Custom Price 3` ),
						min_price = IF( COALESCE( qi.`Custom Price 2`, 0 ) =0, sf.min_price, qi.`Custom Price 2` ) ,
						twelve_bot_manual_price = IF( COALESCE( qi.`Custom Price 4`, 0 ) =0, 0, qi.`Custom Price 4` ),
						avail_code = IF( Trim( COALESCE( qi.`Custom Field 4`, '' ) ) = '', COALESCE( sf.avail_code, 0 ) , CAST( qi.`Custom Field 4` AS SIGNED ) ) ,
						sku = qi.`Item Number`";
		mysql_query($sql) or sql_error($sql);		
		pos::key_to_id();		
	}
//Equivalent:  SWE_update_pos_cost (macro)
	public static function  SWE_update_pos_cost() {
		pos_cost_temp::delete_table();
		pos_cost_temp::pricing_insert_pos_cost_temp();
		pos::pricing_update_pos_cost();
		//we aren't updating supplier stuff in the hub so probabably won't need the below
		//SupplierList::pos_update_supplier_costs();
		self::SWE_store_import_and_update();
		//echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
//Equivalent:  SWE_store_import_and_update (macro)		
	public static function SWE_store_import_and_update($qs = false) {
	//need to populate this table first time through
		if($qs) {
			//qs2000_item::import_and_update();
			pos::import_and_update();			
		}
		self::SWE_store_feed_qty_to_0();
		self::SWE_update_store_feed();
		//echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}	
}//end class	

