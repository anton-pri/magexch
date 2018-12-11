<?php
//store 2
class vinum_feed extends feed {
	public static $table = 'vinum_feed';
	public static $feed_file = 'vinum.txt';
	public static $supplier_id = '1009';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() {//Equivalent:  vinum_delete_table
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	
	public static function add_vintage() {
		$sql = "UPDATE vinum_feed
						SET Vintage = IF(COALESCE(Vintage, '') <> '', Vintage, 'NV')";		
		mysql_query($sql) or sql_error($sql);				
		
	}
	
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE LEFT(coalesce(i.xref,'     '),5) = 'VINM-'";
		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_item_xref() {//Equivalent:  vinum_update_item_xref
		$sql = "UPDATE item_xref AS i 
						INNER JOIN " . self::table_name() . " AS f ON i.xref = CONCAT('VINM-' , f.`Product Code`) 
						SET i.qty_avail = IF(CAST(Available AS SIGNED) > 0, Available, '0'), 
						i.min_price = '0', 
						i.bot_per_case = NULL, 
						i.cost_per_bottle = ROUND(CAST(TRIM(COALESCE(`Base Price`,'0')) as DECIMAL(10,2)),2), 
						i.cost_per_case = 0";					
		$result = mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_product_code() {
		$sql = "UPDATE " . self::$table . "
						SET `Product Code` = CONCAT(`Name` , '-' ,  IF(COALESCE(Vintage, '') <> '', right(Vintage, 2), 'NV'));";
		mysql_query($sql) or sql_error($sql);				
	}		
	
	public static function delete_bad_records() {
		//delete wines with no real names
		$sql = 'SELECT * from ' . self::$table	;
		$result = mysql_query($sql) or sql_error($sql);	
		while ($row = mysql_fetch_array($result)) {
			$row['Name'] = trim($row['Name']);		
			if((int)$row['Name'] != 0 || empty($row['Name'])) {
				$sql = 'DELETE FROM ' . self::$table . "
								WHERE Name = '{$row['Name']}'";
				mysql_query($sql) or sql_error($sql);				
			}
		}		
		//now delete records with no price or negative price
		$sql = 'DELETE FROM ' . self::$table . "
						WHERE COALESCE(TRIM(`Base Price`), '') = '' OR LEFT(COALESCE(TRIM(`Base Price`), ''), 1) = '-'";
		mysql_query($sql) or sql_error($sql);	

		//now delete records with no qty
		$sql = 'DELETE FROM ' . self::$table . "
						WHERE COALESCE(TRIM(`Available`), '') <= 0";
		mysql_query($sql) or sql_error($sql);						
		
	}
		
	
//Equivalent:  import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::delete_bad_records();
			self::add_vintage();
			self::update_product_code();
			self::update_only();	
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
//Equivalent:  vinum_update_only (macro)		
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}
	
//Equivalent:  vinum_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, description, store_id, supplier_id )
						SELECT 'Feed_Vinum' AS Source, 
						u.Description AS Producer, 
						NULL AS Wine, 
						u.Description AS Name, 
						Trim(u.Vintage) AS Vintage, 
						u.Size AS size, 
						CONCAT('VINM-' , Trim(u.`Product Code`)) AS xref, 
						NULL AS bottles_per_case, 
						NULL AS catalogid, 
						Region AS Region, 
						u.`Country of Origin` AS country, 
						u.Varietal AS varietal, 
						u.Appelation AS Appellation, 
						NULL AS `sub-appellation`, 
						u.`Base Price` AS cost, 
						NULL AS parker_rating, 
						NULL AS Parker_review, 
						NULL AS Spectator_rating, 
						NULL AS Spectator_review, 
						NULL AS Tanzer_rating, 
						NULL AS Tanzer_review, 
						NULL, '2', '" . self::get_supplier_id() . "'
						FROM vinum_feed AS u LEFT JOIN item_xref AS i ON CONCAT('VINM-' , Trim(u.`Product Code`)) = i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);					
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				

	}	
	
	
}