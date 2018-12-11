<?php
/**
 * this has table_id
 * run this sql after data dump
ALTER TABLE `grape_feed` ADD INDEX ( `Product Code` ) ;
ALTER TABLE `grape_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */


//store 2
class grape_feed extends feed {
	public static $table = 'grape_feed';
	public static $feed_file = 'grape_feed.txt';
	public static $supplier_id = '1006';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}
		
	
	public static function table_name() {
		return self::$table;
	}
	
//Equivalent:  delete_grape_feed	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}	
	//Equivalent:  grape_update_product_code
	public function grape_update_product_code() {
		$sql = "UPDATE grape_feed 
						SET `Product Code` = CONCAT(item , \"-\" ,  RIGHT(vintage, 2))";
		mysql_query($sql) or sql_error($sql);		
	}
	
		//Equivalent:  grape_set_qty_0
	public  static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,\"     \"),5)=\"GRPE-\"";
		mysql_query($sql) or sql_error($sql);				
	}
	
		//Equivalent:  grape_update_item_xref
	public static function update_item_xref() {
	//March 9, 2011, changed Round(CAST(Trim(COALESCE(f.`Post-off`,f.Price)) as DECIMAL(10,2)),2)
	// to  Round(CAST(Trim(if(COALESCE(f.`Post-off`,0) > 0, f.`Post-off`, f.Price)) as DECIMAL(10,2)),2) 
	
//		$sql = "UPDATE item_xref AS i 
//						INNER JOIN grape_feed AS f ON i.xref= CONCAT(\"GRPE-\" , f.`Product Code`)
//						SET i.qty_avail = '12', 
//						i.min_price = '0', 
//						i.bot_per_case = null, 
//						i.cost_per_bottle = Round(CAST(Trim(COALESCE(f.`Post-off`,f.Price)) as DECIMAL(10,2)),2), 
//						i.cost_per_case = '0'";

		$sql = "UPDATE item_xref AS i 
						INNER JOIN grape_feed AS f ON i.xref= CONCAT(\"GRPE-\" , f.`Product Code`)
						SET i.qty_avail = '12', 
						i.min_price = '0', 
						i.bot_per_case = null, 
						i.cost_per_bottle =  Round(CAST(Trim(if(COALESCE(f.`Post-off`,0) > 0, f.`Post-off`, f.Price)) as DECIMAL(10,2)),2) , 
						i.cost_per_case = '0'";
		mysql_query($sql) or sql_error($sql);			
	}
	//Equivalent:  grape_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT 'Feed_Grape' AS Source, producer AS Producer, ucwords(u.`wine`) AS Wine, 
						ucwords(u.`wine`) AS Name, 
						u.vintage, 
						IF(RIGHT(u.Size,2) = 'mL', LOWER( u.Size),  CONCAT(u.Size , 'tr')) AS `size`, 
						CONCAT('GRPE-' , u.`Product Code`) AS xref, 
						null AS bottles_per_case, 
						Null AS catalogid, 
						u.region, u.Country, 
						null, 
						null, 
						null AS `sub-appellation`, 
						IF(COALESCE(u.`Post-off`, '') <> '', u.`Post-off`, u.Price) AS cost, 
						u.wa AS Parker_rating, 
						u.WS AS Spectator_rating, 
						u.Tanzer AS Tanzer_rating, 
						null, 
						'2',
						'" . self::get_supplier_id() . "'
						FROM grape_feed AS u LEFT JOIN item_xref AS i ON CONCAT('GRPE-' , u.`Product Code`) = i.xref
						WHERE isNull(i.xref) and COALESCE(u.item, '') <> ''";//this different from old hub but needed  since something like GRPE-09 will be more likely to repeat
		mysql_query($sql) or sql_error($sql);				
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
	}
	
	/**
	 * deprecated
	 *
	 */
	public static function clean() {
		$sql = "SELECT * FROM " . self::table_name();
		$result = mysql_query($sql) or sql_error($sql);		
		while ($row = mysql_fetch_array($result)) {
			if(!empty($row['Product Code'])) {
				$row['wine'] = mysql_real_escape_string(self::englishize($row['wine']));
				$row['producer'] = mysql_real_escape_string(self::englishize($row['producer']));			
				$sql = "UPDATE grape_feed
								SET wine = '{$row['wine']}',
								producer = '{$row['producer']}'
								WHERE `Product Code` = '{$row['Product Code']}'";
				mysql_query($sql) or sql_error($sql);				
			}			
		}	
	}
	
	//delete anything without an item code
	public static function delete_no_item_code() {
		$sql = "delete from " . self::$table . " WHERE LEFT(`Product Code`, 1) = '-'";
		mysql_query($sql) or sql_error($sql);				
	}
	
	
//Equivalent:  grape_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::grape_update_product_code();
			self::delete_no_item_code();
			//self::clean();			
			self::update_only(); 
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	

//Equivalent:  grape_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
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
					$num--;
					//$num--;
					//echo $num;
		      for ($c = 0; $c < $num; $c++) {
						if(isset($data[$c])) {
							$data[$c] = trim($data[$c]);		      
						}
						if($row == 1) {
								$fields .= "`$data[$c]`,";
						}  
						else {
							if(isset($data[$c])) {
								if($sanitize_count > 0) {
									$data[$c] = sanitizer::strip($data[$c], $sanitize);							
								}
								
								$data[$c] = mysql_real_escape_string($data[$c]);
								//acme_add_vintage			
								$values .= "'$data[$c]',";							
							}
							else {
								$values .= "'',";
							}

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
}	