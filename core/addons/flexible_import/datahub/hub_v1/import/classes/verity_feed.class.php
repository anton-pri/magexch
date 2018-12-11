<?php
//store 1
class verity_feed extends feed {
	public static $table = 'verity_feed';
	public static $feed_file = 'verity.txt';
	public static $supplier_id = '155';	
	public static $store_id = '1';	
	
	
	public static function get_store_id() {
		return self::$store_id;
	}	 
 
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  Verity_insert_POS
	public static function insert_pos() {
                global $config;
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
                                                `Custom Field 1`,
						`Vendor Name`,
						`Manufacturer`,
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
                                                IF((INSTR('".$config['flexible_import']['fi_spirit_varietals']."',c.varietal) AND c.varietal!=''),'Spirit','Wine'),
						c.country,
						'" . mysql_real_escape_string(self::get_supplier_name()) . "',
						c.Producer,
						c.varietal,
						c.bottles_per_case,
						c.xref
						FROM feeds_item_compare AS c LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0) = qi.`Alternate Lookup`
						WHERE (c.store_id = 1 and LEFT(c.xref,6) = 'VERITY' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
		
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  verity_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}

//Equivalent:  verity_update_bottles
	public static function update_bottles() {
		$sql = "UPDATE verity_feed
						SET Size = IF(LEFT(Size, 2) = '0.', CONCAT(CAST(CAST(SUBSTRING_INDEX(Size, ' ', 1) AS DECIMAL(10,3))  * 1000 AS SIGNED), 'ml'), REPLACE(REPLACE(Size, 'Liter', 'Ltr'), ' ', ''))";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  verity_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,'     '),7)='VERITY-'";
		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  verity_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN " . self::table_name() . " AS f ON i.xref= CONCAT(\"VERITY-\" , f.`Product Code`) 
						SET i.qty_avail = '9999', 
						i.min_price = '0', 
						i.bot_per_case = f.`Pack`, 
						i.cost_per_case = f.`On 1`, 
						i.cost_per_bottle = Round(CAST(f.`On 1` as DECIMAL(10,2)) / COALESCE(CAST(f.`Pack` as SIGNED), 12), 2)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_apply_splitcase_to_cost_verity
	public static function hub_apply_splitcase() {
		$sql = "UPDATE item_xref SET split_case_charge = 0.8
						WHERE left(COALESCE(xref,'      '),6) = 'VERITY'";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  verity_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, Description, store_id, supplier_id )
						SELECT 'Feed_Verity' AS Source, 
						ucwords(u.`Producer`) AS Producer, 
						ucwords(u.`Description`) AS Wine, 
						ucwords(u.`Description`) AS Name, 
						u.`Vintage`, 
						Size AS `size`, 
						CONCAT('VERITY-' , u.`Product Code`) AS xref, 
						u.`Pack` AS bottles_per_case, 
						NULL AS catalogid, 
						Region, 
						Country, 
						Varietal, 
						Appellation, 
						`Sub-Appellation` AS `sub-appellation`, 
						Round(CAST(u.`On 1` as DECIMAL(10,2)) / CAST(u.Pack as DECIMAL(10,2)),2) AS cost, 
						NULL, 
						NULL, 
						NULL, 
						u.`Score/Rating`, 
						'" . self::get_store_id() . "', 
						'" . self::get_supplier_id() . "'
						FROM " . self::table_name() . " AS u LEFT JOIN item_xref AS i ON CONCAT('VERITY-' , u.`Product Code`) = i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}
	

	
	public static function remove_bad_records() {
	//delete if there's no cost
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(TRIM(`On 1`), '') = '' OR `On 1` = '0'";
		mysql_query($sql) or sql_error($sql);	
	//delete if there's no sku	
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(TRIM(`Sku`), '') = '' OR Sku = '0'";
		mysql_query($sql) or sql_error($sql);						
	}
	//numerous money fields but we're only using `On 1`
	public static function clean_money_fields() {
		$sql = "UPDATE " . self::table_name() . "
						SET `On 1` = TRIM(REPLACE(REPLACE(`On 1`, '$', ''), ',', ''))";	
		mysql_query($sql) or sql_error($sql);		
	}	
	
	//Equivalent:  verity_update_product_code
	public static function update_product_code() {
		$sql = "UPDATE " . self::table_name() . " 
						SET Vintage = IF(LEFT(TRIM(Vintage), 2) = 'NV', 'NV', LEFT(Vintage, 4))";
		mysql_query($sql) or sql_error($sql);
				
		$sql = "UPDATE " . self::table_name() . " 
						SET `Product Code` = IF(TRIM(Vintage) = '' OR TRIM(Vintage) = 0, CONCAT(Sku, \"-\", 'NV'), CONCAT(Sku, \"-\" ,  RIGHT(TRIM(Vintage), 2)))";					
		mysql_query($sql) or sql_error($sql);		
	}	
		// 					
	public static function special_rules_import() {
		$sql = "UPDATE " . self::table_name() . " 		
						SET Varietal = IF(TRIM(Varietal) = '0', '', TRIM(Varietal)),
						Country = IF(TRIM(Country) = '0', '', TRIM(Country)),
						Region = IF(TRIM(Region) = '0', '', TRIM(Region)),
						`Sub-Appellation` = IF(TRIM(`Sub-Appellation`) = '0', '', TRIM(`Sub-Appellation`)),		
						Appellation = IF(TRIM(Appellation) = '0', '', TRIM(Appellation)),	
						Color = IF(TRIM(Color) = '0', '', TRIM(Color)),
						`Score/Rating` = IF(TRIM(`Score/Rating`) = '0', '', TRIM(`Score/Rating`))";
		mysql_query($sql) or sql_error($sql);		
	}		
	
//Equivalent:  verity_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name());
			self::clean_money_fields();
			self::remove_bad_records();
			self::update_bottles();	
			self::special_rules_import();		
			self::update_product_code();			
			self::update_only();			
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}		

//Equivalent:  verity_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
	
	

	
}	//end class
