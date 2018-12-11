<?php
//mysql --host=localhost --user=cartDBa --password=fhgJfH760Jsd swe < item_xref.sql

//mysql --host=localhost --user=cartDBa --password=fhgJfH760Jsd store_updates < other.txt
/**
 * this has table_id
 * this can be run on create
 * ALTER TABLE `acme_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */
//store 2
class acme_feed extends feed {
	public static $table = 'acme_feed';
	public static $feed_file = 'acme.txt';
	public static $supplier_id = '1000';	
	public static $store_id = '2';		
	
	public static function get_store_id() {
		return self::$store_id;
	}	 	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() {//Equivalent:  acme_delete_table
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	
//Equivalent:  acme_add_vintage	
	public static function add_vintage() {
		$sql = 'SELECT * FROM ' . self::table_name();
		$result = mysql_query($sql) or sql_error($sql);	
		while ($row = mysql_fetch_array($result)) {
			$four = substr($row['Description'], 0, 4); 
			if(is_numeric($four)){
			//				$row['Description'] = ucwords(strtolower($row['Description']));
				$sql = "UPDATE " . self::$table . "
								SET Vintage = '$four', 
								Description = '" .  ucwords(strtolower(trim(ltrim($row['Description'], $four)))) . "',
								Quantity = '12'
								WHERE `Item Code` = '{$row['Item Code']}'";		
			}
			else {
				$two = substr($row['Description'], 0, 2); 			
				if(strtolower($two) == 'nv') {
					$sql = "UPDATE " . self::$table . "
									SET Vintage = 'NV',
									Description = '" .  ucwords(strtolower(trim(ltrim($row['Description'], $two)))) . "',
									Quantity = '12'																	
									WHERE `Item Code` = '{$row['Item Code']}'";					
				}
				else {
					$sql = "UPDATE " . self::$table . "
									SET Vintage = 'NV',								
									Description = '" .  ucwords(strtolower($row['Description'])) . "',									 
									Quantity = '12'															
									WHERE `Item Code` = '{$row['Item Code']}'";									
				}
			}
			mysql_query($sql) or sql_error($sql);				
		}
	}
	
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE LEFT(coalesce(i.xref,'     '),5) = 'ACME-'";
		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_item_xref() {//Equivalent:  acme_update_item_xref
		$sql =  "UPDATE item_xref AS i 
						INNER JOIN " . self::$table . " AS f ON i.xref = CONCAT('ACME-',Trim(f.`Item Code`))
						SET 
						i.qty_avail = If(Not isnull(f.`Item Code`) And Trim(coalesce(f.`Item Code`,' '))<>'' 
						And Cast(Trim(coalesce(`Quantity`,'0')) as DECIMAL(10,2))>0,Cast(Trim(coalesce(`Quantity`,'0')) as DECIMAL(10,2)),'0'), 
						i.min_price = '0', 
						i.bot_per_case = null,
						i.cost_per_bottle = Round(Cast(Trim(coalesce(`Cost`,'0')) as DECIMAL(10,2)),2), 
						i.cost_per_case = '0'";
		$result = mysql_query($sql) or sql_error($sql);	
	}
	
//Equivalent:  ACME_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();	
			self::transfer_text(self::$feed_file, self::$table, "\t", array("'"));			
			self::add_vintage();
			self::update_only(); 
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
//Equivalent:  ACME_update_only (macro)		
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}
	
//Equivalent:  acme_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, 
						bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`,
						cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, 
						Tanzer_review, description, store_id, supplier_id )
						SELECT 'Feed_ACME' AS Source, 
						u.Producer AS Producer, 
						u.`Description` AS Wine, 
						u.`Description` AS Name, 
						Trim(u.Vintage) AS Vintage, null AS nsize, 
						CONCAT('ACME-',Trim(u.`Item Code`)) AS xref, 
						NULL AS bottles_per_case, 
						NULL AS catalogid, 
						u.Region AS Region, 
						u.Country AS country, 
						NULL AS varietal, 
						Appellation AS Appellation, 
						NULL AS `sub-appellation`, 
						Round(Cast(Trim(coalesce(u.`Cost`,'0')) as DECIMAL(10,2)),2) AS cost, 
						u.`RP Score` AS parker_rating, 
						u.`RP Description` AS Parker_review, 
						u.`WS Score` AS Spectator_rating, 
						u.`WS Description` AS Spectator_review, 
						u.`IWC score` AS Tanzer_rating, 
						u.`IWC Description` AS Tanzer_review, 
						NULL, 
						'" . self::get_store_id() . "', 
						'" . self::get_supplier_id() . "'
						FROM " . self::$table . " AS u LEFT JOIN item_xref AS i ON CONCAT('ACME-',Trim(u.`Item Code`)) = i.xref
						WHERE isNull(i.xref);";

		mysql_query($sql) or sql_error($sql);	
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	

}//end class