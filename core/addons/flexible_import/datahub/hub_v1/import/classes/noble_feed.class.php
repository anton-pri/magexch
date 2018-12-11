<?php
/**
 * run this after data import
ALTER TABLE `noble_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE  `noble_feed` ADD INDEX (  `Product Code` );
*/

//store 2
class noble_feed extends feed {
	public static $table = 'noble_feed';
	public static $feed_file = 'noble.txt';
	public static $ignore_fields = array();	
	public static $supplier_id = '1002';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}		
	
	public static function table_name() {
		return self::$table;
	}
	
//Equivalent:  noble_delete	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}		
	
//Equivalent:  noble_delete_exceptions
	public static function delete_exceptions() {
		$sql = "DELETE 
						FROM " . self::table_name() . "
						WHERE `btl size` = 'CASE'
						OR UCASE(Left(`Item description`, 2))  = 'ZZ'";
		mysql_query($sql) or sql_error($sql);		
	}
	
//Equivalent:  noble_update_vintage
	public static function update_vintage() {
		$sql = "UPDATE " . self::table_name() . " 
						SET vintage = 'NV'
						WHERE Coalesce(vintage, '') = '' or
						vintage = '0'";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  noble_update_product_code
	public static function update_product_code() {
		$sql = "UPDATE " . self::table_name() . " 
						SET `Product Code` = CONCAT(`Item #` , '-' , vintage)
						WHERE Coalesce(`Product Code`, '') = ''";
		mysql_query($sql) or sql_error($sql);				
	}
	
//Equivalent:  noble_update_btl_size_1 and noble_update_btl_size_2
	public static function update_btl_size() {
		$sql = 'UPDATE ' . self::table_name() . ' 
						SET `btl size` = IF(IsNull(`btl size`) Or `btl size`="0","",
						IF(`btl size`="1.5L","1.5Ltr",
						IF(`btl size`="100ML","100ml",
						IF(`btl size`="10L","10Ltr",
						IF(`btl size`="15L","15Ltr",
						IF(`btl size`="187ML","187ml",
						IF(`btl size`="1L","1Ltr",
						IF(`btl size`="24Z","24Ltr",
						IF(`btl size`="28L","28Ltr",
						IF(`btl size`="375ML","375ml",
						IF(`btl size`="3L","3Ltr",
						IF(`btl size`="500ML","500ml",
						IF(`btl size`="5L","5Ltr",
						IF(`btl size`="6L","6Ltr",
						IF(`btl size`="750ML","750ml",
						IF(`btl size`="9L","9Ltr",
						If(`btl size`="CASE","CASE",
						`btl size`)))))))))))))))));';
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  noble_set_qty_0
	public static function set_qty_0() {
		$sql = 'UPDATE item_xref AS i 
						SET qty_avail = "0"
						WHERE LEFT(COALESCE(i.xref,"     "),5)="NOBL-"';
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  noble_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN " . self::table_name() . " AS f 
						ON i.xref=CONCAT('NOBL-' , f.`Product Code`) 
						SET i.qty_avail = IF(Not isnull(f.`Product Code`) And Trim(COALESCE(f.`Product Code`,' '))<>'' And CAST(Trim(COALESCE(`inventory`,'0')) as SIGNED)>0,
						CAST(Trim(COALESCE(`inventory`,'0')) as SIGNED),0), 
						i.min_price = '0', 
						i.bot_per_case = null, 
						i.cost_per_bottle = IF(Round(CAST(Trim(COALESCE(f.`btl p/o`,'0')) as DECIMAL(10,2)),2) > 0, Round(CAST(Trim(COALESCE(f.`btl p/o`,'0')) as DECIMAL(10,2)),2), Round(CAST(Trim(COALESCE(f.`btl price`,'0')) as DECIMAL(10,2)),2)), 
						i.cost_per_case = '0'";
		mysql_query($sql) or sql_error($sql);				
	}
	
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT 'Feed_NOBL' AS Source,ucwords(u.`Item description`) AS Producer, 
						ucwords(u.`Item description`) AS Wine, 
						ucwords(u.`Item description`) AS Name, 
						Trim(u.vintage) AS Vintage, 
						u.`btl size` AS nsize, 
						CONCAT('NOBL-' , Trim(u.`Product Code`)) AS xref, 
						u.`case size` AS bottles_per_case, Null AS catalogid, 
						null AS Region, null AS country, 
						null AS varietal, null, null AS `sub-appellation`, 
						IF(Round(CAST(Trim(COALESCE(u.`btl p/o`,'0')) as DECIMAL(10,2)),2) > 0, Round(CAST(Trim(COALESCE(u.`btl p/o`,'0')) as DECIMAL(10,2)),2), Round(CAST(Trim(COALESCE(u.`btl price`,'0')) as DECIMAL(10,2)),2)) AS cost, null AS parker_rating, null AS Spectator_rating, null AS Tanzer_rating, null, '2', '" . self::get_supplier_id() . "'
						FROM noble_feed AS u LEFT JOIN item_xref AS i ON CONCAT('NOBL-' , Trim(u.`Product Code`))=i.xref
						WHERE isNull(i.xref)  And COALESCE(Trim(u.`inventory`),'0')<>'0'";
		mysql_query($sql) or sql_error($sql);				
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
	
//Equivalent:  Noble_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name());
			self::delete_exceptions();
			self::update_vintage();
			self::update_product_code();
			self::update_btl_size();
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
//Equivalent:  Noble_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
}//end class	