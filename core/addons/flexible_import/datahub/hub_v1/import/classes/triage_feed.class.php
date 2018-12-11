<?php
/**
 * this has table_id
 * run this sql after data dump
	ALTER TABLE `triage_feed` ADD INDEX ( `Item Code` ) ;
	ALTER TABLE `triage_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */


//store 2
class triage_feed extends feed {
	public static $table = 'triage_feed';
	public static $feed_file = 'Triage.txt';
	public static $supplier_id = '1004';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}			
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  triage_delete_table	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	
	public static function remove_char() {
		$sql = "UPDATE " . self::table_name() . "
						SET `Base Price` = REPLACE(`Base Price`, ',', '')";
		mysql_query($sql) or sql_error($sql);		
		$sql = "UPDATE " . self::table_name() . "
						SET `Base Price` = REPLACE(`Base Price`, '$', '')";
		mysql_query($sql) or sql_error($sql);				
		
	}	
//Equivalent:  triage_set_qty_0
	public static function set_qty_0() {
		$sql = 'UPDATE item_xref AS i SET qty_avail = "0"
						WHERE Left(COALESCE(i.xref,"     "),5)="TRIA-"';
		mysql_query($sql) or sql_error($sql);				
	}

//Equivalent:  triage_set_qty_0
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i INNER JOIN triage_feed AS f ON i.xref=CONCAT('TRIA-' , f.`Item Code`) SET 
						i.qty_avail = IF(Not isnull(f.`Item Code`) And Trim(COALESCE(f.`Item Code`,' '))<>'' And 
						CAST(Trim(COALESCE(`Total Bottles Available`,'0')) as DECIMAL(10,2))>0,CAST(Trim(COALESCE(`Total Bottles Available`,'0')) as DECIMAL(10,2)),0), 
						i.min_price = '0', i.bot_per_case = null, 
						i.cost_per_bottle = Round(CAST(Trim(COALESCE(`Base Price`,'0')) as DECIMAL(10,2)),2), i.cost_per_case = '0'";	
		mysql_query($sql) or sql_error($sql);				
	}	
	
//Equivalent:  triage_insert_compare	
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, description, store_id, supplier_id)
						SELECT 'Feed_TRIA' AS Source, 
						Trim(ucwords( IF(instr(Producer,',') > 0, CONCAT(Right(Trim(Producer),LENGTH(Trim(Producer))-InStr(Producer,','))  , ' ' , Left(Trim(Producer),InStr(Producer,',')-1)),Producer))), 
						ucwords(u.`Description`) AS Wine, 
						ucwords(u.`Description`) AS Name, 
						Trim(u.Vintage) AS Vintage, 
						IF(`Bottle Size`='5L','5Ltr', 
						IF(`Bottle Size`='3L','3Ltr',
						IF(`Bottle Size`='1L','1Ltr',
						IF(`Bottle Size`='750ml','750ml',
						IF(`Bottle Size`='500ml','500ml',
						IF(`Bottle Size`='375ml','375ml',
						'0')))))) AS `size`, CONCAT('TRIA-' , Trim(u.`Item Code`)) AS xref, 
						null AS bottles_per_case, 
						Null AS catalogid, 
						u.`Wine Region` AS Region, 
						u.`Country of Origin` AS country, 
						null AS varietal, 
						null AS Appellation, 
						null AS `sub-appellation`, 
						Round(CAST(Trim(COALESCE(u.`Base Price`,'0')) as DECIMAL(10,2)),2) AS cost, 
						u.`WA` AS parker_rating, 
						null AS Parker_review, 
						u.`WS` AS Spectator_rating, 
						null AS Spectator_review, 
						u.`IWC` AS Tanzer_rating, 
						null AS Tanzer_review,
						null as Description, 
						'2'
						, '" . self::get_supplier_id() . "'
						FROM triage_feed AS u LEFT JOIN item_xref AS i ON CONCAT('TRIA-' , Trim(u.`Item Code`))=i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);		
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}

//Equivalent:  triage_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::remove_char();
			self::update_only(); 
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
	
//Equivalent:  triage_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}		


}//end class