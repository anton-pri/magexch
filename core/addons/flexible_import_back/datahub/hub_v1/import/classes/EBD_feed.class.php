<?php
/**
	ALTER TABLE `ebd_feed_temp` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
	ALTER TABLE `EBD_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 have to do a search and replace on ’ for \' for the data before inserting data
 */

//feed file needs "On Hand" to change to 'Quantity On Hand'
//store 2
class EBD_feed extends feed {
	public static $table = 'EBD_feed';
	public static $feed_file = 'EBD_Inventory.txt';
	public static $supplier_id = '1003';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
		
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function get_temp_table_name() {
		return 'ebd_feed_temp';
	}	
	
//Equivalent:  ebd_feed_set_qty_0
	public static function set_qty_0() {
		$sql = 'UPDATE ' . self::table_name() . ' SET `Quantity On Hand`  = "0"';
		mysql_query($sql) or sql_error($sql);		
	}
	
//Equivalent:  ebd_delete_temp
	public static function delete_temp() {
		$sql = 'DELETE FROM ' . self::get_temp_table_name();
		mysql_query($sql) or sql_error($sql);		
	}	
	
//Equivalent:  ebd_delete_nonv_from_temp
	public static function delete_nonv_from_temp() {
		$sql = 'DELETE FROM ' . self::get_temp_table_name() . '
						WHERE INSTR(Item,"v") =  0';
		mysql_query($sql) or sql_error($sql);		
	}		
	
//Equivalent:  ebd_update_feed_from_temp
	public static function update_feed_from_temp() {
		$sql = 'UPDATE ' . self::table_name() . ' AS feed 
						INNER JOIN ' . self::get_temp_table_name() . ' AS temp
						ON feed.`Product Code` = temp.`Item` 
						SET feed.Description = temp.`Description`, 
						feed.BOTTLE = temp.`Price`, 
						feed.`Quantity On Hand` = temp.`Quantity On Hand`';
		mysql_query($sql) or sql_error($sql);		
	}	
	
//Equivalent:  ebd_insert_from_temp
	public static function insert_from_temp() {
		$sql = 'INSERT INTO ' . self::table_name() . ' ( Description, `ITEM #`, BOTTLE, `Quantity on Hand` )
						SELECT temp.`Description`, temp.`Item`, temp.`Price`, temp.`Quantity on Hand`
						FROM ' . self::get_temp_table_name() . ' as temp
						WHERE temp.`Item`
						NOT IN (SELECT ' . self::table_name() . '.`Product Code` FROM ' . self::table_name() . ')';
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  ebd_update_item_code_new_wines
	public static function update_item_code_new_wines() {
		$sql = 'UPDATE ' . self::table_name() . ' AS T1 
						INNER JOIN ' . self::table_name() . ' AS T2 ON T1.`Item #` = T2.`Item #` 
						SET T1.`Item Code` = IF(InStr(`T2`.`Item #`,"n") > 0, mid(`T2`.`Item #`,1,(instr(`T2`.`Item #`,"n"))-1),IF(InStr(`T2`.`Item #`,"v") > 0,    mid(`T2`.`Item #`,1,(instr(`T2`.`Item #`,"v"))-1), `T2`.`Item #` ))
						WHERE Coalesce( T1.`Product Code`, "") = ""
						and  Coalesce( T2.`Product Code`, "") = ""';
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  ebd_update_new_wines
	public static function update_new_wines() {
		$sql = "UPDATE " . self::table_name() . " AS t1 INNER JOIN 
						" . self::table_name() . " AS t2 
						ON t1.`Item Code` = t2.`Item Code` 
						SET t1.Producer = `t2`.`Producer`, 
						t1.`Product Name` = `t2`.`Product Name`, 
						t1.Varietal = t2.`Varietal`, 
						t1.Country = `t2`.`Country`, 
						t1.Region = `t2`.`Region`, 
						t1.Appelation = `t2`.`Appelation`, 
						t1.`Sub-Appelation` = `t2`.`Sub-Appelation`, 
						t1.`PK/SIZE` = `t2`.`PK/SIZE`
						WHERE Coalesce(t1.`Product Code`, '') = '' 
						AND  Coalesce(t2.`Product Code`, '') <>  ''";
		mysql_query($sql) or sql_error($sql);				
	}
	
//Equivalent:  ebd_update_product_code
	public static function update_product_code() {
		$sql = "UPDATE " . self::table_name() . "
						SET `Product Code` = IF( Coalesce(Vintage,'')  =  '',  
						IF(Instr(`ITEM #`, 'v')  > 0,  `ITEM #`, CONCAT(`ITEM #` , 'nv')),						
						IF(Instr(`ITEM #`, 'v')  > 0,  `ITEM #`,  						
						IF(Instr(`Vintage`, 'n')  > 0, CONCAT(`ITEM #` , 'nv'),						
						CONCAT(`ITEM #` , 'v' , Right(Vintage, 2)))))";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  ebd_set_qty_0
	public static function ebd_set_qty_0() {
		$sql = 'UPDATE item_xref AS i SET qty_avail = "0"
						WHERE Left(Coalesce(i.xref,"    "),4)="EBD-"';
		mysql_query($sql) or sql_error($sql);			
	}

//Equivalent:  ebd_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN " . self::table_name() . " AS f 
						ON i.xref=CONCAT(\"EBD-\" , f.`Product Code`)
						SET i.qty_avail = IF(Not isnull(f.`Product Code`) And Trim(Coalesce(f.`Product Code`,\" \"))<>'' 
						AND CAST(Trim(Coalesce(`Quantity On Hand`,'0')) as DECIMAL(10,2))>0,
						CAST(Trim(Coalesce(`Quantity On Hand`,'0')) as DECIMAL(10,2)),0), 
						i.min_price = '0', 
						i.bot_per_case = null, 
						i.cost_per_bottle = Round(CAST(Trim(Coalesce(`BOTTLE`,\"0\")) as DECIMAL(10,2)),2), 
						i.cost_per_case = '0'";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  EBD_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::get_temp_table_name())) {
			self:: set_qty_0();
			self::delete_temp();
			self::transfer_text(self::$feed_file, self::get_temp_table_name());
			self::delete_nonv_from_temp();
			self::update_feed_from_temp();
			self::insert_from_temp();
			self::update_item_code_new_wines();
			self::update_new_wines();
			self::update_product_code();
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
//Equivalent:  EBD_update_only (macro)
	public static function update_only() {
		self::ebd_set_qty_0();
		self::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
	
//Equivalent:  ebd_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT 'Feed_EBD' AS Source, IF(Coalesce(u.`Producer`, '') = '', ucwords(u.`Description`), u.`Producer`) AS Producer, ucwords(u.`Description`) AS Wine, IF(Coalesce(u.`Product Name`, '') = '', ucwords(u.`Description`), u.`Product Name`) AS Name, Trim(u.Vintage) AS Vintage, IF(Mid(`Item Code`, InStr(`Item Code`,'.') + 1, 1) ='1','750ml',
						IF(Mid(`Item Code`, InStr(`Item Code`,'.') + 1, 1) ='5','375ml',
						IF(Mid(`Item Code`, InStr(`Item Code`,'.') + 1, 1) ='4','500ml',
						IF(Mid(`Item Code`, InStr(`Item Code`,'.') + 1, 1) ='2','1.5Ltr',
						IF(Mid(`Item Code`, InStr(`Item Code`,'.') + 1, 1) ='9','3Ltr',
						'Other'
						))))) AS nsize, CONCAT('EBD-' , Trim(u.`Product Code`)) AS xref, IF(InStr(u.`PK/SIZE`,'-') > 0,  mid(u.`PK/SIZE`,1,(instr(u.`PK/SIZE`,'-'))-1), Null ) AS bottles_per_case, Null AS catalogid, u.Region AS Region, u.Country AS country, u.Varietal AS varietal, ucwords(Trim(u.Appelation)), ucwords(Trim(u.`Sub-Appelation`)) AS `sub-appellation`, Round(CAST(Trim(Coalesce(`BOTTLE`,'0')) as DECIMAL(10,2)),2) AS cost, null AS parker_rating, null AS Spectator_rating, null AS Tanzer_rating, null, '2', '" . self::get_supplier_id() . "'
						FROM " . self::table_name() . " AS u LEFT JOIN item_xref AS i ON CONCAT('EBD-' , Trim(u.`Product Code`))=i.xref
						WHERE isNull(i.xref) And CAST(Trim(Coalesce(u.`Quantity On Hand`,'0')) as SIGNED)>5";
		mysql_query($sql) or sql_error($sql);			
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
}//end class	