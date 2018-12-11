<?php
/**
 * this has table_id
run after data has loaded
 ALTER TABLE `vehr_feed` ADD INDEX ( `ID` ) ;
ALTER TABLE `vehr_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE  `vehr_feed` ADD  `Product Code` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `vehr_feed` ADD INDEX (  `Product Code` );
 *
 * changed feed file format, inform Matt
 */

//store 2
class vehr_feed extends feed {
	public static $table = 'vehr_feed';
	public static $feed_file = 'Vehr.txt';
	public static $supplier_id = '1007';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}		
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  vehr_delete
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function delete_empties() {
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(ID, '') = ''";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  vehr_update_size
	public static function update_size() {
		$sql = "UPDATE " . self::table_name() . " 
						SET `Size` = IF(Right(trim(Size), 1) = 'L' or Right(trim(Size), 1) = 'l', 
						Replace(Size, ' ML', 'ml'), Replace(Size, ' LT', 'Ltr'))";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  vehr_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,'     '),5)='VEHR-'";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  vehr_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN " . self::table_name() . " AS f ON i.xref=CONCAT('VEHR-' , f.`Product Code`) 
						SET i.qty_avail = '12', 
						i.min_price = '0', 
						i.bot_per_case = null, 
						i.cost_per_bottle = Round(CAST(Trim(COALESCE(f.`PO Bttl`,f.`FL Bttl`)) as DECIMAL(10,2)),2), 
						i.cost_per_case = 0";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  vehr_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, description, store_id, supplier_id )
						SELECT 'Feed_VEHR' AS Source, Producer AS Producer, NULL AS Wine, Name AS Name, Trim(u.Vintage) AS Vintage, Size AS nsize, 
						CONCAT('VEHR-' , Trim(u.`Product Code`)) AS xref, null AS bottles_per_case, Null AS catalogid, Region AS Region, Country AS country, null AS varietal, null AS Appellation, null AS `sub-appellation`, IF(COALESCE(u.`PO Bttl`, '') <> '', Round(CAST(Trim(u.`PO Bttl`) as DECIMAL(10,2)),2), Round(CAST(Trim(COALESCE(u.`FL Bttl`,'0')) as DECIMAL(10,2)),2)) AS cost, null AS parker_rating, null AS Parker_review, null AS Spectator_rating, null AS Spectator_review, null AS Tanzer_rating, null AS Tanzer_review, null, '2', '" . self::get_supplier_id() . "'
						FROM vehr_feed AS u LEFT JOIN item_xref AS i ON CONCAT('VEHR-' , Trim(u.`Product Code`))=i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);					
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
	}
	
	//delete anything without an item code
	public static function delete_no_item_code() {
		$sql = "delete from " . self::$table . " WHERE LEFT(`Product Code`, 1) = '-'";
		mysql_query($sql) or sql_error($sql);				
	}	
	
	public static function update_product_code() {
		$sql = "UPDATE " . self::$table . "
						SET `Product Code` = CONCAT(`ID` , \"-\" ,  IF(COALESCE(Vintage, '') <> '',right(Vintage, 2), 'NV'));";
		mysql_query($sql) or sql_error($sql);				
	}			
	
//Equivalent:  vehr_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::delete_empties();
			self::update_product_code();
			self::delete_no_item_code();				
			self::update_size();	
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
//Equivalent:  vehr_update_only (macro)	
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}	
	
}	//end class