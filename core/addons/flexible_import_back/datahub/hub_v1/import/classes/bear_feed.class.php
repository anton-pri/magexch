<?php
/**
 * this has table_id
 * can run this sql on create
 * ALTER TABLE `bear_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 * ALTER TABLE  `bear_feed` ADD INDEX (  `SKU` );
 */


/**
 * @todo 
 * Bear_insert_POS
 *
 */


//store 1
class bear_feed extends feed {
	public static $table = 'bear_feed';
	public static $feed_file = 'bear.txt';
	public static $supplier_id = '148';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  Bear_insert_POS
	public static function insert_pos() {
		pos::binlocation_to_id();	
		$sql = "INSERT IGNORE INTO pos ( 
						`Item Number`,
						`Item Name`,
						`Item Description`,						
						`Attribute`,
						`Size`,
						`Average Unit Cost`,
						`Regular Price`,
						`Department Name`,
						`Vendor Name`,
						`Custom Field 1`,
						`Custom Field 2`,
						`Custom Field 3`,
						`Custom Field 5`	
						)
						SELECT DISTINCT
						 '' AS Expr1,
						Replace(CONCAT(Left(Trim(c.Producer),10) , TRIM(CONCAT(Left(CONCAT(c.name , \" \"),14) , Right(CONCAT(\" \" , c.Vintage),2) , \".\" , Left(CONCAT(c.Size , \" \"),3)))),\" \",\"\") AS Expr2, 
						'' AS Expr3,					
						c.Vintage AS Expr4,
						c.size,
						Round(c.cost,2) AS Expr5,
						Round(CAST(c.cost as DECIMAL(19,4))*1.5,0)-0.01,
						c.country,
						'" . mysql_real_escape_string(self::get_supplier_name()) . "',
						c.Producer,
						c.varietal,
						c.bottles_per_case,
						c.xref
						FROM feeds_item_compare AS c LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0) = qi.`Alternate Lookup`
						WHERE (c.store_id = 1 and Left(c.xref,4) = 'BEAR' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  bear_delete_table	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	


	//Equivalent:  	bear_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE (item_xref AS i INNER JOIN " . self::$table . " AS f 
						ON i.xref=CONCAT(\"BEAR-\", CAST(f.SKU as CHAR))) 
						LEFT JOIN item AS i2 ON i.item_id = i2.ID 
						SET i.qty_avail = (f.`Qty Per Case` * f.Cases), 
						i.min_price = '0', 
						i.bot_per_case = CAST(f.`Qty per Case` as SIGNED), 
						i.cost_per_case = f.Cost, i.cost_per_bottle = Round(CAST(f.Cost as DECIMAL(10,2)) / CAST(f.`Qty per Case` as SIGNED),2)";
		mysql_query($sql) or sql_error($sql);				
	}

	public static function hub_apply_splitcase_to_cost_bear() {
		$sql = "UPDATE item_xref SET split_case_charge = '0'
						WHERE left(coalesce(xref,'    '),4) = \"BEAR\"";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  Bear_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::update_bear_cases();
			self::delete_bear_cases();
			self::update_qty_size();
	
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
//Equivalent:  Bear_update_only (macro)		
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_bear();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}
	
//Equivalent:  bear_insert_compare
	public static function insert_compare() {
	//ucwords(strtolower($bar))
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT \"Feed_Bear\" AS Source, u.Producer AS Producer, null AS Wine, ucwords(u.`Producers/Wine`) AS Name, u.Vintage, If(u.Size=\"1.5L\",\"1.5Ltr\",
						If(u.Size=\"750ML\" or u.Size=\"750M\" ,\"750ml\",
						If(u.Size=\"500ML\" or u.Size=\"500M\" ,\"500ml\",
						If(u.Size=\"375ML\" or u.Size=\"375M\" ,\"375ml\",
						\"Other\")))) AS `size`, \"BEAR-\" & CAST(u.SKU as CHAR) AS xref, u.`Qty per Case` AS bottles_per_case, Null AS catalogid, u.`Region A`, u.Country, u.Varietal, u.Appellation, u.`Suba` AS `sub-appellation`, Round(CAST(u.Cost as DECIMAL(10,2)) /u.`Qty per Case`,2) AS cost, u.`wa r`, u.`ws r`, null, CONCAT(If(coalesce(u.`wa r`, '') <> '', CONCAT('Rated ' , u.`wa r` , ' by Robert Parker.  '), null) , 
						If(coalesce(u.`ws r`, '') <> '', CONCAT('Rated ' , u.`ws r` , ' by Wine Spectator.  '), null) ,
						If(coalesce(u.`we r`, '') <> '', CONCAT('Rated ' , u.`we r` , ' by Wine Enthusiast.'), null)), '1', '" . self::get_supplier_id() . "'
						FROM " . self::$table . " AS u LEFT JOIN item_xref AS i ON \"BEAR-\" & CAST(u.SKU as CHAR)=i.xref
						WHERE isNull(i.xref)
						AND CAST(u.Cases as SIGNED) > 0";
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
	//Equivalent:  update_bear_cases
	public static function update_bear_cases() {
		$sql = 'UPDATE ' . self::$table . ' SET Cases = Left(Cases,LENGTH(trim(Cases))-3)';
		mysql_query($sql) or sql_error($sql);		
	}
	
	//Equivalent:  delete_bear_cases	
	public static function delete_bear_cases() {
		$sql = "DELETE 
						FROM " . self::$table . "
						WHERE Cases = '0' or
						coalesce(Cases, '') = ''"	;
		mysql_query($sql) or sql_error($sql);				
	}
	//Equivalent:  bear_update_qty_size
	public static function update_qty_size() {
		$sql = 'UPDATE ' . self::$table . ' 
						SET `Size` = UPPER(Right(Trim(`Unit`),LENGTH(Trim(`Unit`))-InStr(`Unit`,"/"))), 
						`Qty per Case` = Left(`Unit`,InStr(`Unit`,"/")-1)';
		mysql_query($sql) or sql_error($sql);			

	}
	//Equivalent:  	bear_set_qty_0
	public static function set_qty_0() {
		$sql = 'UPDATE item_xref AS i SET qty_avail = "0"
						WHERE Left(coalesce(i.xref,"     "),5)="BEAR-"';
		mysql_query($sql) or sql_error($sql);		
	}
	



	
}
