<?php
/**
this has table_id
can run this sql after data import
ALTER TABLE `Polaner_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE  `Polaner_feed` ADD INDEX (  `Item Code` );
*/

//store 1
class Polaner_feed extends feed {
	public static $table = 'Polaner_feed';
	public static $feed_file = 'Polaner NY Inventory.txt';
	public static $ignore_fields = array('Wine Type', 'Closure', 'Organic', 'Biodynamic', 'Burghound', 'View from the Cellar','UPC Code', 'On Order ETA', 'Other Press');	
	public static $supplier_id = '48';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}		
	
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  Polaner_insert_POS
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
						WHERE (c.store_id = 1 and Left(c.xref,4) = 'POLA' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  polaner_delete_table	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}		

//Equivalent:  Polaner_set_qty_0	
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,'     '),5)='POLA-'";
		mysql_query($sql) or sql_error($sql);		
	}
	
//Equivalent:  Polaner_update_item_xref
	public static function update_item_xref() {
//		$sql = "UPDATE (item_xref AS i 
//						INNER JOIN " . self::table_name() . " AS f ON i.xref=CONCAT('POLA-' , f.`Item Code`)) 
//						LEFT JOIN item AS i2 ON i.item_id = i2.ID 
//						SET i.qty_avail = IF(Not isnull(f.`Item Code`) And Trim(COALESCE(f.`Item Code`,' '))<>'' 
//						And CAST(Trim(COALESCE(`On Hand`,'0')) as DECIMAL(10,2))>0.999,round(CAST(f.`Bottles / CS` as SIGNED)* CAST(COALESCE(f.`On Hand`,'0') as DECIMAL(10,2)),0),0), 
//						i.min_price = CAST(COALESCE(f.`Sug Retail Price`,0) as DECIMAL(10,2)), 
//						i.bot_per_case = CAST(COALESCE(f.`Bottles / CS`,'12') as SIGNED), 
//						i.cost_per_case = Round(CAST(Trim(COALESCE(`FL Price`,'0')) as  DECIMAL(10,2)),2), 
//						i.cost_per_bottle = Round(CAST(Trim(COALESCE(`FL Price`,'0')) as DECIMAL(10,2))/COALESCE(i2.bot_per_case,CAST(COALESCE(f.`Bottles / CS`,12) as SIGNED)),2)";

	//changed March 1, 2011
	//cost_per_bottle was calculation was changed since 0 was being returned in 
	//IF(COALESCE(i2.bot_per_case, 0) > 0, i2.bot_per_case,  CAST(COALESCE(f.`Bottles / CS`,12) as SIGNED)),2)";
	//ms access has null values so it works there but not here
//		$sql = "UPDATE (item_xref AS i 
//						INNER JOIN " . self::table_name() . " AS f ON i.xref=CONCAT('POLA-' , f.`Item Code`)) 
//						LEFT JOIN item AS i2 ON i.item_id = i2.ID 
//						SET i.qty_avail = IF(Not isnull(f.`Item Code`) And Trim(COALESCE(f.`Item Code`,''))<>'' 
//						And CAST(Trim(COALESCE(`On Hand`,'0')) as DECIMAL(10,2))>0.999,round(CAST(f.`Bottles / CS` as SIGNED)* CAST(COALESCE(f.`On Hand`,'0') as DECIMAL(10,2)),0),0), 
//						i.min_price = CAST(COALESCE(f.`Sug Retail Price`,0) as DECIMAL(10,2)), 
//						i.bot_per_case = CAST(COALESCE(f.`Bottles / CS`,'12') as SIGNED), 
//						i.cost_per_case = Round(CAST(Trim(COALESCE(`FL Price`,'0')) as  DECIMAL(10,2)),2), 
//						i.cost_per_bottle = Round(CAST(Trim(COALESCE(`FL Price`,'0')) as DECIMAL(10,2))/IF(COALESCE(i2.bot_per_case, 0) > 0, i2.bot_per_case,  CAST(COALESCE(f.`Bottles / CS`,12) as SIGNED)),2)";

		
		$sql = "UPDATE (item_xref AS i 
						INNER JOIN   Polaner_feed  AS f ON i.xref=CONCAT('POLA-' , f.`Item Code`)) 					
						SET i.qty_avail = IF(Not isnull(f.`Item Code`) And Trim(COALESCE(f.`Item Code`,''))<>'' 
						And CAST(Trim(COALESCE(`On Hand`,'0')) as DECIMAL(10,2))>0.999,round(CAST(f.`Bottles / CS` as SIGNED)* CAST(COALESCE(f.`On Hand`,'0') as DECIMAL(10,2)),0),0), 
						i.min_price = CAST(COALESCE(f.`Sug Retail Price`,0) as DECIMAL(10,2)), 
						i.bot_per_case = CAST(COALESCE(f.`Bottles / CS`,'12') as SIGNED), 
						i.cost_per_case = Round(CAST(Trim(COALESCE(`FL Price`,'0')) as  DECIMAL(10,2)),2), 
						i.cost_per_bottle = Round(CAST(Trim(COALESCE(`FL Price`,'0')) as DECIMAL(10,2))/IF(CAST(COALESCE(f.`Bottles / CS`,0) as SIGNED) > 0,  CAST(f.`Bottles / CS` as SIGNED),  12),2)";		
		
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  hub_apply_splitcase_to_cost_polaner	
	public static function hub_apply_splitcase_to_cost_polaner() {
//		$sql = "UPDATE item_xref AS i SET split_case_charge = .5
//						WHERE left(COALESCE(i.xref,'    '),4) = 'POLA'";
		$sql = "UPDATE item_xref AS i SET split_case_charge = 0
						WHERE left(COALESCE(i.xref,'    '),4) = 'POLA'";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  Polaner_insert_compare
	public static function insert_compare() {
		$sql = "	INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
							SELECT 'Feed_Polaner' AS Source, 
							Trim(ucwords( IF(instr(Vendor,',') > 0, CONCAT(Right(Trim(Vendor),Length(Trim(Vendor))-InStr(Vendor,','))  , ' ' , Left(Trim(Vendor),InStr(Vendor,',')-1)),Vendor))) AS Producer, 
							Left(CONCAT(Left(Trim(ucwords( IF(instr(Vendor,',') > 0, CONCAT(Right(Trim(Vendor),Length(Trim(Vendor))-InStr(Vendor,','))  , ' ' , Left(Trim(Vendor),InStr(Vendor,',')-1)),Vendor))) ,5) , Left(`Description`,19) , Right(Vintage,2) , '-' , Left(`Bottle Size`,3)),30) AS Wine, 
							Left(ucwords(`Description`),50) AS Name, Trim(u.Vintage) AS vintage, IF(`Bottle Size`='1 L','1Ltr',IF(`Bottle Size`='1.5 L','1.5Ltr',IF(`Bottle Size`='3 L','3Ltr',Trim(ucwords(Replace(`Bottle Size`,' ','')))))) AS `size`, 
							CONCAT('POLA-' , Trim(u.`Item Code`)) AS xref, 
							Trim(`Bottles / CS`) AS bottles_per_case, Null AS catalogid, ucwords(Trim(u.Region)), ucwords(Trim(u.Country)), ucwords(Trim(u.Varietal)), ucwords(Trim(u.Appellation)), null AS `sub-appellation`, 
							Round(CAST(COALESCE(Trim(`FL Price`),'0') as DECIMAL(10,2))/CAST(Trim(`Bottles / CS`) as DECIMAL(10,2)),2) AS cost, 
							Trim(`Wine Advocate`) AS parker_rating, Trim(`Wine Spectator`) AS Spectator_rating, Trim(`Internatinal Wine Cellar`) AS Tanzer_rating, Null, '1', '" . self::get_supplier_id() . "'
							FROM " . self::table_name() . " AS u LEFT JOIN item_xref AS i ON CONCAT('POLA-' , Trim(u.`Item Code`))=i.xref
							WHERE isNull(i.xref) And CAST(Trim(COALESCE(`On Hand`,'0')) as DECIMAL(10,2))>0.999";
		mysql_query($sql) or sql_error($sql);			
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
	}

//Equivalent:  Polaner_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text();
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
//Equivalent:  Polaner_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_polaner();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
	
	
//Equivalent:  TransferText	
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$row = 1;
		$fields = '';	
		if (($handle = fopen(FEED_FILE . self::$feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
					$values = '';    
		      $num = count($data);
		      $field_count = $num;
					$field_count_insert = 0;
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
							if(!empty(self::$ignore_fields) && in_array($data[$c], self::$ignore_fields)) {
								$ignore_index[] = $c;
								$field_count--;
								continue;
							}						
							if(preg_match('/Wine Cellar/i', $data[$c])) {
								$data[$c] = 'Internatinal Wine Cellar';
							}
								$fields .= "`$data[$c]`,";
						}  
						else {
							//if($ignore_index == $c) {
							if(in_array($c, $ignore_index)) {							
								continue;
							}				
								
							//$data[$c] = sanitizer::strip($data[$c], array());	
							$data[$c] = mysql_real_escape_string($data[$c]);
							//acme_add_vintage			
							$values .= "'$data[$c]',";
							$field_count_insert++;
						}     
		
					}      
					while (18 > $field_count_insert) {						
							$values .= "'',";
							$field_count_insert++;
					}
					$fields = rtrim($fields, ',');    
					$values = rtrim($values, ',');  		
		
					if($row > 1) {
						$sql = "INSERT INTO " . self::$table . " ($fields)
										VALUES ($values)";
						//echo $sql . '<br>';
						mysql_query($sql) or sql_error($sql);
					}
		      $row++;        
		    }
		    fclose($handle);
		}
		/**
		 * @todo stripping out commas and $ here, may want to do that in single query
		 */
		$sql = "SELECT * FROM " . self::table_name();
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			if(!empty($row['FL Price'])) {
				$row['FL Price'] = str_ireplace('$', '', $row['FL Price']);
				$row['FL Price'] = str_ireplace(',', '', $row['FL Price']);				
				$sql = "UPDATE " . self::table_name() . "
								SET `FL Price` = {$row['FL Price']}
								WHERE table_id = '{$row['table_id']}'";
				mysql_query($sql);
			}
		}
		return $row;	
	}	
}//end class	
