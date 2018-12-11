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
	public static $store_id = '2';

	
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
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  cellar_feed
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}

	public static function remove_bad_records() {
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(`ITEM #`, '') = '' OR AVAILABLE <= 0 OR `UNIT PRICE` <= 0";
		mysql_query($sql) or sql_error($sql);			
	}
	

	public static function update_vintage() {
		//select only records that have 4 successive numbers in description
		$sql = "SELECT * from " . self::table_name() . "
						WHERE `LONG DESCRIPTION` REGEXP '[0-9]{4}';";
		$result = mysql_query($sql) or sql_error($sql);	
		$pattern = '/[0-9]{4}/';				
		while ($row = mysql_fetch_array($result)) {
			$description = $row['LONG DESCRIPTION'];
			//strip out 4 digits
			preg_match($pattern, $description, $matches);		
			$count = count($matches);
			if($count > 0) {
				foreach ($matches as $k => $v) {			
					//sometimes a description might look like
					//BERTAGNOLLI 1870 RISERVA NV 4 /750
					//it will pick 1870 as the vintage, so let's say vintage has to be greater than 1930
						if($v > 1930) {
							$sql = "UPDATE " . self::table_name() . "
											SET Vintage = '$v'
										  WHERE table_id = '{$row['table_id']}'";			
							mysql_query($sql) or sql_error($sql);								
						}
			
				}			
			}		
		}	
		//otherwise it's NV
		$sql = "UPDATE " . self::table_name() . "
						SET Vintage = 'NV'
						WHERE COALESCE(Vintage, '0') = '0'
						OR COALESCE(Vintage, '') = ''";	

		mysql_query($sql) or sql_error($sql);			
	}
	
	//Description typically ends with Pack /Bottle Size
	//so if no '/', delete it as we need pack and bottle size	
	public static function delete_no_slash() {
		$sql = "DELETE  from " . self::table_name() . "
						WHERE `PK SIZE` NOT LIKE '%/%'";
		mysql_query($sql) or sql_error($sql);		
	}
	
	
	public static function update_size_pack() {
		$sql = "SELECT * from " . self::table_name();
		$result = mysql_query($sql) or sql_error($sql);			
		while ($row = mysql_fetch_array($result)) {
			$description = $row['PK SIZE'];		
			//grab text that's before the slash
			$suffix = strrchr($description, "/"); 
			$pos = strpos($description, $suffix); 
			$root = substr_replace($description, "", $pos);
			//get rid of extra characters that may cause it not to be a true number
			$suffix = str_replace(array('/', ',', ' '), '', trim($suffix));
			if($suffix > 999) {
				$suffix = $suffix / 1000;
				//1.0Ltr should be 1Ltr etc
				$suffix = str_replace('.0', '', $suffix);
				
				$sql = "UPDATE " . self::table_name() . "
								SET Size = '{$suffix}Ltr'
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);		
			}
			elseif($suffix < 10) {
				//sometimes they do put in the size eg 1L
				$suffix = str_ireplace(array('l', '.0'), '', $suffix);
				$sql = "UPDATE " . self::table_name() . "
								SET Size = '{$suffix}Ltr'
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);					
			}
			else {
				$sql = "UPDATE " . self::table_name() . "
								SET Size = '{$suffix}ml'
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);		
			}	
			//now update Pack
			
			//with what's before the slash, that will have the pack
			$root = trim($root);
			$temp = explode(' ', $root);	
//			print_r($temp);
//			die;
			//pack should be the last entry
			$idx = count($temp) - 1;
			if($temp[$idx] > 0) {
				$sql = "UPDATE " . self::table_name() . "
								SET `PK SIZE` = '$temp[$idx]'
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);					
			}
			else {
				$sql = "UPDATE " . self::table_name() . "
								SET `PK SIZE` = NULL
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);						
			}
		}	
	}

	public static function special_rules_import() {
 		self::delete_no_slash();	
		self::update_size_pack();
		self::update_vintage();
		self::update_availability();
	}
	
	public static function update_availability() {
		$sql = "UPDATE " . self::table_name() . "
						SET `AVAILABLE` = FLOOR(CAST(AVAILABLE as DECIMAL(10,2)))";
		mysql_query($sql) or sql_error($sql);			
		self::remove_bad_records();		
	}	
	

	public static function update_product_code() {
		$sql = "UPDATE " . self::table_name() . "
						SET `Product Code` = CONCAT(`ITEM #`, '-', `Vintage`)
						WHERE COALESCE(`Product Code`, '') = ''";
		mysql_query($sql) or sql_error($sql);					
	}
	

//Equivalent:  vias_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(Coalesce(i.xref,'     '),5)='CELL-'";
		mysql_query($sql) or sql_error($sql);					
	}

	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i INNER JOIN " . self::table_name() . " AS f 
						ON i.xref = CONCAT('CELL-' , f.`Product Code`) 
						SET i.qty_avail = CAST(CAST(f.`PK SIZE` as SIGNED) * CAST(f.AVAILABLE as SIGNED) as SIGNED),
						i.min_price = '0', 
						i.bot_per_case = f.`PK SIZE`, 
						i.cost_per_case = f.`CASE PRICE`, 
						i.cost_per_bottle = f.`UNIT PRICE`";
		
		mysql_query($sql) or sql_error($sql);				
	}


	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, `W&S_rating`, `W&S_review`, description, store_id, supplier_id )
						SELECT 'Feed_CELL' AS Source, 
						NULL AS Producer, 
						NULL AS Wine, 
						ucwords(u.`LONG DESCRIPTION`) AS Name, 
						u.`Vintage` AS Vintage, 
						u.Size AS size, 
						CONCAT('CELL-' , Trim(u.`Product Code`)) AS xref, 
						u.`PK SIZE` AS bottles_per_case, 
						NULL AS catalogid, 
						NULL AS Region, 
						NULL AS country, 
						NULL AS varietal, 
						NULL AS Appellation, 
						NULL AS `sub-appellation`, 
						CAST(u.`UNIT PRICE` as DECIMAL(10,2)) AS cost, 
						NULL AS parker_rating, 
						NULL AS Parker_review, 
						NULL AS Spectator_rating, 
						NULL AS Spectator_review, 
						NULL AS Tanzer_rating, 
						NULL AS Tanzer_review, 
						NULL AS `W&S_rating`, 
						NULL AS `W&S_review`, 
						NULL, 
						'" . self::get_store_id() . "', 
						'" . self::get_supplier_id() . "'
						FROM " . self::table_name() . " AS u LEFT JOIN item_xref AS i ON CONCAT('CELL-' , Trim(u.`Product Code`))=i.xref
						WHERE isNull(i.xref)";		
		
		mysql_query($sql) or sql_error($sql);
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}
	
	public static function clean_money_fields() {
		$sql = "UPDATE " . self::table_name() . "
						SET `UNIT PRICE` = TRIM(REPLACE(REPLACE(`UNIT PRICE`, '$', ''), ',', '')),
						`CASE PRICE` = TRIM(REPLACE(REPLACE(`CASE PRICE`, '$', ''), ',', ''))";
		mysql_query($sql) or die($sql);	
	
	}
	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name(), "\t", array('.'));
			self::clean_money_fields();
			self::remove_bad_records();
			self::special_rules_import();
			self::update_product_code();
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';		

		
		}	
	}		

	public static function update_only() {
		cellar_feed::set_qty_0();
		cellar_feed::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
	}	
	
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$row = 1;
		$fields = '';	
		$sanitize_count = count($sanitize);
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					$values = '';    
		      //$num = count($data);
					$sql = "DESCRIBE " . self::table_name();
					$result = mysql_query($sql) or sql_error($sql);		
					$num = mysql_num_rows($result);	
					$num--;//delete for table id since we added that
					$num--;//delete for Product Code since we added that
					$num--;//delete for Vintage since we added that				
					$num--;//delete for Size since we added that														
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
							if($sanitize_count > 0) {
								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
							}						
								$fields .= "`$data[$c]`,";
						}  
						else {
							$data[$c] = mysql_real_escape_string($data[$c]);	
							$values .= "'$data[$c]',";
						}     
		
					}      
					$fields = rtrim($fields, ',');    
					$values = rtrim($values, ',');  		
		
					if($row > 1) {
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

	
}	//end class