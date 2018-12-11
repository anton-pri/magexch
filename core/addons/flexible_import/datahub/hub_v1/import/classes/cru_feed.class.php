<?php
//store 2
class cru_feed extends feed {
	public static $table = 'cru_feed';
	public static $feed_file = 'cru.txt';
	public static $supplier_id = '1010';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() { 
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	
	public static function add_vintage() {
		$sql = "UPDATE " . self::$table . "
						SET Vintage = IF(COALESCE(Vintage, '') <> '', Vintage, 'NV')";		
		mysql_query($sql) or sql_error($sql);				
		
	}
	
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE LEFT(COALESCE(i.xref, ''), 4) = 'CRU-'";
		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN " . self::table_name() . " AS f ON i.xref = CONCAT('CRU-' , f.`Product Code`) 
						SET i.qty_avail = IF(CAST(f.inventory AS SIGNED) > 0, f.inventory, '0'), 
						i.min_price = '0', 
						i.bot_per_case = NULL, 
						i.cost_per_bottle = IF(CAST(COALESCE(f.`post off price`, '')  as DECIMAL(10,2)) > CAST(f.Cost  as DECIMAL(10,2)),  ROUND(CAST(TRIM(COALESCE(f.`post off price`,'0')) as DECIMAL(10,2)),2), ROUND(CAST(TRIM(COALESCE(f.Cost,'0')) as DECIMAL(10,2)),2)), 
						i.cost_per_case = 0";					
		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_product_code() {
		$sql = "UPDATE " . self::$table . "
						SET `Product Code` = CONCAT(`Item #` , '-' ,  IF(COALESCE(Vintage, '') <> '', right(Vintage, 2), 'NV'));";
		mysql_query($sql) or sql_error($sql);				
	}		
	
	public static function delete_bad_records() {

		//now delete records with no price or negative cost
		$sql = 'DELETE FROM ' . self::$table . "
						WHERE COALESCE(TRIM(`Cost`), '') = '' OR  COALESCE(TRIM(`Cost`), 0) = 0";
		mysql_query($sql) or sql_error($sql);	
		
		$sql = 'DELETE FROM ' . self::$table . "
						WHERE COALESCE(TRIM(`Item #`), '') = ''";
		mysql_query($sql) or sql_error($sql);		
		// delete Item #s with spaces
		$sql = 'DELETE FROM ' . self::$table . "
						WHERE `Item #` LIKE '% %'";
		mysql_query($sql) or sql_error($sql);								
		
	}
	
	public static function clean_money_fields() {
		$sql = "UPDATE " . self::table_name() . "
						SET `Cost` = TRIM(REPLACE(REPLACE(`Cost`, '$', ''), ',', '')),
						`post off price` = TRIM(REPLACE(REPLACE(`post off price`, '$', ''), ',', ''))";	
		mysql_query($sql) or sql_error($sql);		
	}		
	
	//will probably have to update this once larger sizes come in
	public static function special_rules_import() {
		$sql = "UPDATE  " . self::table_name() . "
						SET `Item Size` = CONCAT(`Item Size`, 'ml')";
		mysql_query($sql) or sql_error($sql);			
		
		$sql = "UPDATE  " . self::table_name() . "
						SET `inventory` = '120'";
		mysql_query($sql) or sql_error($sql);				
						
	}
		
	
//Equivalent:  import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::clean_money_fields();			
			self::delete_bad_records();			
			self::add_vintage();
			self::update_product_code();
			self::special_rules_import();					
			self::update_only();	
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
 
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}
	
 	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, description, store_id, supplier_id )
						SELECT 'Feed_Cru' AS Source, 
						u.Brand AS Producer, 
						NULL AS Wine, 
						u.Description AS Name, 
						Trim(u.Vintage) AS Vintage, 
						u.`Item Size` AS size, 
						CONCAT('CRU-' , Trim(u.`Product Code`)) AS xref, 
						NULL AS bottles_per_case, 
						NULL AS catalogid, 
						region AS Region, 
						u.`country` AS country, 
						null AS varietal, 
						null AS Appellation, 
						NULL AS `sub-appellation`, 
						IF(CAST(COALESCE(u.`post off price`, '')  as DECIMAL(10,2)) > CAST(u.Cost  as DECIMAL(10,2)),  ROUND(CAST(TRIM(COALESCE(u.`post off price`,'0')) as DECIMAL(10,2)),2), ROUND(CAST(TRIM(COALESCE(u.Cost,'0')) as DECIMAL(10,2)),2)) AS cost, 
						NULL AS parker_rating, 
						NULL AS Parker_review, 
						NULL AS Spectator_rating, 
						NULL AS Spectator_review, 
						NULL AS Tanzer_rating, 
						NULL AS Tanzer_review, 
						NULL, '2', '" . self::get_supplier_id() . "'
						FROM cru_feed AS u LEFT JOIN item_xref AS i ON CONCAT('CRU-' , Trim(u.`Product Code`)) = i.xref
						WHERE isNull(i.xref)";

		mysql_query($sql) or sql_error($sql);					
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				

	}	
	
	
}