<?php
/**
 * this has table_id
 * can run this sql on create
 * ALTER TABLE `bowler_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 * 
 *  ALTER TABLE `bowler_feed` ADD INDEX ( `Product ID` )  
 *
 */

//store one
class bowler_feed extends feed {
	public static $table = 'bowler_feed';
	public static $feed_file = 'bowler.txt';
	public static $supplier_id = '150';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}		
	
//Equivalent:  Bowler_share_insert_POS	
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
						WHERE (c.store_id = 1 and Left(c.xref,4) = 'BOWL' and isnull(qi.`Alternate Lookup`) and (c.add_to_hub=true or not isnull(c.catalogid) ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
	
	public static function table_name() {
		return self::$table;
	}
	//Equivalent:  bowler_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
//Equivalent:  bowler_update_cost
	public static function update_cost() {
		$sql = "UPDATE " . bowler_feed::table_name() . "
						SET Cost = If(Coalesce(trim(Cost), '') <> '', REPLACE(trim(Cost),',',''), 
						If(Coalesce(trim(`Cost 1`), '') = '', 
						 REPLACE(trim(`Cost 2`),',',''), 
						 REPLACE(trim(`Cost 1`),',','')));";
		mysql_query($sql) or sql_error($sql);		
	}
//Equivalent:  bowler_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE Left(Coalesce(i.xref,\"     \"),5)=\"BOWL-\"";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  bowler_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE (item_xref AS i 
						INNER JOIN " . bowler_feed::table_name() . " AS f 
						ON i.xref=CONCAT(\"BOWL-\" , CAST(f.`Product ID` as CHAR))) 
						LEFT JOIN item AS i2 ON i.item_id = i2.ID 
						SET i.qty_avail = '9999', 
						i.min_price = '0', 
						i.bot_per_case = CAST(Coalesce(i2.bot_per_case,12) as SIGNED), 
						i.cost_per_case = f.Cost, 
						i.cost_per_bottle = Round(CAST(f.Cost as DECIMAL(10,2)) /Coalesce(i2.bot_per_case,12),2)";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  //hub_apply_splitcase_to_cost_bowler	
	public static function hub_apply_splitcase_to_cost() {
		$sql = "UPDATE item_xref SET split_case_charge = '1'
						WHERE left(Coalesce(xref,'    '),4) = \"BOWL\"";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  bowler_import_and_update (macro)
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			bowler_feed::delete_table();		
			self::transfer_text(self::$feed_file, self::$table);
			bowler_feed::update_cost();			
			bowler_feed::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
		}
	}
	
//Equivalent:  bowler_update_only (macro)		
	public static function update_only() {
		bowler_feed::set_qty_0();
		bowler_feed::update_item_xref();
		bowler_feed::hub_apply_splitcase_to_cost();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
	
//Equivalent:  bowler_insert_compare		
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT \"Feed_Bowler\" AS Source, ucwords(u.`Description`) AS Producer, null AS Wine, ucwords(u.`Description`) AS Name, u.`Vintage`, null AS `size`, CONCAT(\"BOWL-\" , CAST(u.`Product ID` as CHAR)) AS xref, null AS bottles_per_case, Null AS catalogid, u.Region AS Region, u.Country AS country, null, null, null AS `sub-appellation`, Round((u.`Cost` / 12),2) AS cost, null, null, null, null, '1', '" . self::get_supplier_id() . "'
						FROM bowler_feed AS u LEFT JOIN item_xref AS i ON CONCAT(\"BOWL-\" , CAST(u.`Product ID` as CHAR))=i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);			
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';					
	
	}
	
	
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$row = 1;
		$fields = '';	
		$sanitize_count = count($sanitize);
		$field_count = '';
		$field_count_insert = '';
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
					$values = '';    
		      $num = count($data);
		
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
								$fields .= "`$data[$c]`,";
								$field_count = $num;
						}  
						else {
							if($sanitize_count > 0) {
								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
							}
							$field_count_insert = $num;
							$data[$c] = mysql_real_escape_string($data[$c]);
							//acme_add_vintage			
							$values .= "'$data[$c]',";
						}     
		
					}      
					if($field_count != $field_count_insert) {
						$values .= "'',";
					}
					$fields = rtrim($fields, ',');    
					$values = rtrim($values, ',');  		

					if($row > 1 && $values != "''") {
						$sql = "INSERT INTO " . $table . " ($fields)
										VALUES ($values)";
						//echo $sql . '<br>';
						mysql_query($sql) or sql_error($sql);
					}
		      $row++;        
		    }
		    fclose($handle);
		}
		return $row;	
	}	
}//end class
