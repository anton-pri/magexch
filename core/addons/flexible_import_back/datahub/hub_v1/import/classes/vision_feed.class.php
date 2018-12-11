<?php
/**
 * this has table_id
run after data has loaded
 ALTER TABLE `vision_feed` ADD INDEX ( `Item No` ) ;
ALTER TABLE `vision_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 *

 */

//store 1
class vision_feed extends feed {
	public static $table = 'vision_feed';
	public static $feed_file = 'Vision.txt';
	public static $supplier_id = '133';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  Vision_insert_POS
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
						WHERE (c.store_id = 1 and Left(c.xref,4) = 'VISN' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
		
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  vision_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}
//Equivalent:  vision_delete_empties
	public static function delete_empties() {
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(`Item No`, '') = ''";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  vision_update_bottles
	public static function update_bottles() {
//		$sql = "UPDATE " . self::table_name() . " SET 
//						`Bot Size` = IF(CAST(ucwords(Right(Trim(`Btls/Size`),Length(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/'))) as SIGNED) > 999,
//						CONCAT(Left(CAST(ucwords(Right(Trim(`Btls/Size`),Length(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/'))) as SIGNED),1) , 'Ltr'),
//						CONCAT(CAST(ucwords(Right(Trim(`Btls/Size`),Length(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/'))) as SIGNED) , 'ml')
//						), `Bots Per Case` = CAST(Left(`Btls/Size`,InStr(`Btls/Size`,'/')-1) as SIGNED), Vintage = IF(COALESCE(`Vintage`, '0') = '0', 'NV', `Vintage`)";
		$sql = "UPDATE " . self::table_name() . "
						SET `Bot Size` = IF(CAST(Right(Trim(`Btls/Size`),LENGTH(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/')) as SIGNED) > 999,
						IF(CAST(Left(Right(Trim(`Btls/Size`),LENGTH(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/') -1),1) as SIGNED) > 0,  
							CONCAT(Left(CAST(Right(Trim(`Btls/Size`),LENGTH(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/')) as SIGNED),1) 
							, '.' ,  CAST(Left(Right(Trim(`Btls/Size`),LENGTH(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/') -1),1) as SIGNED) , 'Ltr'),
						CONCAT(Left(CAST(Right(Trim(`Btls/Size`),LENGTH(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/')) as SIGNED),1) , 'Ltr')
						)
						,
						CONCAT(CAST(Right(Trim(`Btls/Size`),LENGTH(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/')) as SIGNED) , 'ml')
						
						), `Bots Per Case` = CAST(Left(`Btls/Size`,InStr(`Btls/Size`,'/')-1) as SIGNED), Vintage = IF(COALESCE(`Vintage`, '0') = '0', 'NV', `Vintage`)";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  vision_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,'     '),5)='VISN-'";
		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  vision_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE (item_xref AS i INNER JOIN vision_feed AS f ON i.xref=CONCAT('VISN-' , f.`Item No`)) LEFT JOIN item AS i2 ON i.item_id = i2.ID SET i.qty_avail = '9999', i.min_price = IF(COALESCE(f.`Minimum Price`, '') <> '', f.`Minimum Price`, '0'), i.bot_per_case = CAST(f.`Bots Per Case` as SIGNED), 
						i.cost_per_case = CAST(f.`Bottle Net Cost` as DECIMAL(10,2)), 
						i.cost_per_bottle = Round(CAST(CAST(f.`Bottle Net Cost` as DECIMAL(10,2)) / CAST(f.`Bots Per Case` as SIGNED) as DECIMAL(10,2)),2)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_apply_splitcase_to_cost_vision
	public static function hub_apply_splitcase_to_cost_vision() {
		$sql = "UPDATE item_xref SET split_case_charge = '0'
						WHERE left(COALESCE(xref,'    '),4) = 'VISN'";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  vision_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT 'Feed_Vision' AS Source, ucwords(u.`Product Description`) AS Producer, ucwords(u.`Product Description`) AS Wine, ucwords(u.`Product Description`) AS Name, u.`Vintage`, IF(COALESCE(u.`Bot Size`, '') <> '', u.`Bot Size`,'Other') AS `size`, CONCAT('VISN-' , CAST(u.`Item No` as CHAR)) AS xref, u.`Bots Per Case` AS bottles_per_case, Null AS catalogid, null, null, null, null, null AS `sub-appellation`, Round(CAST(CAST(u.`Bottle Net Cost` as DECIMAL(10,2)) / CAST(u.`Bots Per Case` as SIGNED) as DECIMAL(10,2)),2) AS cost, null, null, null, null, '1'
						, '" . self::get_supplier_id() . "'
						FROM vision_feed AS u LEFT JOIN item_xref AS i ON CONCAT('VISN-' , u.`Item No`)=i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}
	
	public static function remove_char() {
		$sql = "UPDATE " . self::table_name() . "
						SET `Btls/Size` = REPLACE(`Btls/Size`, ',', '')";
		mysql_query($sql) or sql_error($sql);		
	}
	
//Equivalent:  vision_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name());
			self::remove_char();
			self::delete_empties();
			self::update_bottles();
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}		

//Equivalent:  vision_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_vision();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
	
	
	//April 26, 2011, commented out function transfer_text	
	
//	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
//		$row = 1;
//		$fields = '';	
//		$sanitize_count = count($sanitize);
//		$field_count = '';
//		$field_count_insert = '';
//		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
//		    while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
//					$values = '';    
//		      $num = count($data);
//		
//		      for ($c = 0; $c < $num; $c++) {
//						$data[$c] = trim($data[$c]);		      
//						if($row == 1) {
//								$fields .= "`$data[$c]`,";
//								$field_count = $num;
//						}  
//						else {
//							if($sanitize_count > 0) {
//								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
//							}
//							$field_count_insert = $num;
//							$data[$c] = mysql_real_escape_string($data[$c]);
//							//acme_add_vintage			
//							$values .= "'$data[$c]',";
//						}     
//		
//					}      
////					if($field_count != $field_count_insert) {
////						$values .= "'',";
////					}
//					while ($field_count != $field_count_insert) {						
//							$values .= "'',";
//							$field_count_insert++;
//					}
//					$fields = rtrim($fields, ',');    
//					$values = rtrim($values, ',');  		
//
//					if($row > 1 && $values != "''") {
//						$sql = "INSERT INTO " . $table . " ($fields)
//										VALUES ($values)";
//						//echo $sql . '<br>';
//						mysql_query($sql) or sql_error($sql);
//					}
//		      $row++;        
//		    }
//		    fclose($handle);
//		}
//		return $row;	
//	}
	
}	//end class
