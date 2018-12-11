<?php
/**
 *  ALTER TABLE `cellar_feed` ADD INDEX ( `ITEM #` )  
ALTER TABLE `cellar_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */

/**
CREATE TABLE IF NOT EXISTS `cellar_feed` (
  `table_id` int(11) NOT NULL auto_increment,
  `ITEM #` varchar(255) default NULL,
  `Winery` varchar(255) default NULL,
  `Vintage` varchar(255) default NULL,
  `Description` varchar(255) default NULL,
  `Bottle Size` varchar(255) default NULL,
  `Bottles in case` varchar(255) default NULL,
  `Inventory` varchar(50) default NULL,
  `Case` varchar(50) default NULL,
  `Bottle` varchar(50) default NULL,
  `Country` varchar(50) default NULL,
  `Region` varchar(255) default NULL,
  `State` varchar(255) default NULL,
  `WS` varchar(255) default NULL,
  `WA` varchar(255) default NULL,
  `WE` varchar(255) default NULL,
  `W&S` varchar(255) default NULL,
  `Product Code` varchar(255) default NULL,
  PRIMARY KEY  (`table_id`),
  KEY `ITEM #` (`ITEM #`),
  KEY `Product Code` (`Product Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;
 *
 */

//store 2
class cellar_feed extends feed {
	public static $table = 'cellar_feed';
	public static $feed_file = 'Cellar.txt';
	public static $supplier_id = '1005';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function table_name() {
		return self::$table;
	}
	//Equivalent:  cellar_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	

//Equivalent: Cellar_import_and_update (macro)
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			cellar_feed::delete_table();		
			self::transfer_text(self::$feed_file, self::$table);
			cellar_feed::delete_empty_rows();
			cellar_feed::update_product_code();			
			cellar_feed::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
		}
	}


//Equivalent:  Cellar_update_only (macro)		
	public static function update_only() {
		cellar_feed::set_qty_0();
		cellar_feed::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
	
//Equivalent: cellar_delete_empty_rows
	public static function delete_empty_rows() {
		$sql = "DELETE 
						FROM cellar_feed
						WHERE Coalesce(`ITEM #`,'') = ''";
		mysql_query($sql) or sql_error($sql);		
	}
	
//Equivalent:  cellar_update_product_code
	public static function update_product_code() {
		$sql = "UPDATE " . self::table_name() . " 
						SET `Product Code` = CONCAT(`Item #` , '-' , Vintage)
						WHERE Coalesce(`Product Code`, '') = ''";
		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  cellar_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i INNER JOIN cellar_feed AS f ON i.xref=CONCAT('CELL-' , f.`Product Code`) 
						SET i.qty_avail = CAST(CAST(f.`Bottles in case` as SIGNED) * CAST(f.Inventory as SIGNED) as SIGNED),
						i.min_price = '0', 
						i.bot_per_case = f.`Bottles in case`, 
						i.cost_per_case = f.Case, 
						i.cost_per_bottle = f.Bottle";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  cellar_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(Coalesce(i.xref,'     '),5)='CELL-'";
		mysql_query($sql) or sql_error($sql);				

	}
//Equivalent:  cellar_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, `W&S_rating`, `W&S_review`, description, store_id, supplier_id )
						SELECT 'Feed_CELL' AS Source, u.`Winery` AS Producer, ucwords(u.`Description`) AS Wine, ucwords(u.`Description`) AS Name, u.`Vintage` AS Vintage, IF(u.`Bottle Size` = 'L', CONCAT(u.`Bottle Size` , 'tr'), u.`Bottle Size`) AS nsize, CONCAT('CELL-' , Trim(u.`Product Code`)) AS xref, u.`Bottles in case` AS bottles_per_case, Null AS catalogid, u.Region AS Region, Country AS country, null AS varietal, Region AS Appellation, null AS `sub-appellation`, Round(CAST(Trim(COALESCE(u.`Bottle`,'0')) as DECIMAL(10,2)),2) AS cost, u.`WA` AS parker_rating, null AS Parker_review, u.`WS` AS Spectator_rating, null AS Spectator_review, null AS Tanzer_rating, null AS Tanzer_review, u.`W&S` AS `W&S_rating`, null AS `W&S_review`, null, '2', '" . self::get_supplier_id() . "'
						FROM cellar_feed AS u LEFT JOIN item_xref AS i ON CONCAT('CELL-' , Trim(u.`Product Code`))=i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);			
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
	}

	
}//end class