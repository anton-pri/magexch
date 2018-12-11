<?php
/**
for Item Number, drop the index and define it as a pk
 ALTER TABLE `pos` ADD INDEX ( `Custom Field 5` ) ;
  ALTER TABLE  `pos` ADD INDEX (  `Alternate Lookup` );
ALTER TABLE  `pos` ADD INDEX (  `Average Unit Cost` );
ALTER TABLE  `pos` ADD INDEX (  `MSRP` );  
 */

class pos extends feed {
	public static $table = 'pos';
	public static $feed_file = 'pos.txt'; 
	public static $money_fields = array('Average Unit Cost', 'Regular Price',	'MSRP',	'Custom Price 1',	'Custom Price 2',	'Custom Price 3',	'Custom Price 4');
	
	public static function table_name() {
		return self::$table;
	}

	public static function delete_table() {
		$sql = "DELETE  FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  pricing_update_pos_cost	
	public static function pricing_update_pos_cost() {
		$sql = "UPDATE pos AS qs 
						INNER JOIN pos_cost_temp AS ct ON qs.`Item Number` = ct.ID 
						SET qs.`Average Unit Cost` = COALESCE(ct.cost, qs.`Average Unit Cost`)
						WHERE qs.`Qty 1` <= 0";
 		mysql_query($sql) or sql_error($sql);					
	}
		
	public static function key_to_varchar() {
		$sql = "ALTER TABLE " . self::$table . " CHANGE `Item Number` `Item Number` VARCHAR( 11 ) NOT NULL";
 		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function key_to_id() {
		$sql = "ALTER TABLE " . self::$table . " CHANGE `Item Number` `Item Number` INT( 11 ) NOT NULL AUTO_INCREMENT";
 		mysql_query($sql) or sql_error($sql);			
	}	
	
	public static function binlocation_to_varchar() {
		$sql = "ALTER TABLE " . self::$table . " CHANGE `Alternate Lookup` `Alternate Lookup` VARCHAR(255 ) NOT NULL";
 		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function binlocation_to_id() {
		$sql = "ALTER TABLE " . self::$table . " CHANGE `Alternate Lookup` `Alternate Lookup` INT( 11 ) NOT NULL";
 		mysql_query($sql) or sql_error($sql);			
	}		

//Equivalent:  hub_update_POS-BinLocation
	public static function hub_update_POS_BinLocation() {
		self::key_to_varchar();
		$sql = "UPDATE pos AS q 
						LEFT JOIN item_store2 AS i ON i.store_sku = q.`Item Number` and i.store_id = 1 
						SET q.`Alternate Lookup` = i.item_id";
		mysql_query($sql) or sql_error($sql);		
		self::key_to_id();				
	}
	
//Equivalent:  hub_update_POS_notes	
	public static function hub_update_POS_notes() {
		$sql = "UPDATE pos AS qi 
						INNER JOIN item AS i ON qi.`Alternate Lookup` = i.ID
						SET qi.`Item Description` = CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR))";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Attribute 	- item.vintage
//Size - item.size
//Department Name - item.country
//Vendor Name  -> xref
//Custom Field 1 -> Producer - item.Producer
//Custom Field 2 -> Varietal - item.varietal
//Custom Field 3 -> bottles per case - item_xref
//Custom Field 5 -> xref
	public static function hub_update_POS_fields() {
		$sql = "UPDATE pos AS qi 
						INNER JOIN item AS i ON qi.`Alternate Lookup` = i.ID
						SET 
						qi.`Attribute` = i.vintage,
						qi.`Size` = i.size,		
						qi.`Department Name` = i.country,				
						qi.`Custom Field 1`	= i.Producer,
						qi.`Custom Field 2`	= i.varietal";
		mysql_query($sql) or sql_error($sql);			
	}	
	
	//remove any spaces within Custom Field 5/xref
	//pos needs the extra space after the dash to do product searches
	public static function remove_dash_space() {
		$sql = "UPDATE pos 
						SET `Custom Field 5` = REPLACE(`Custom Field 5`, ' ', '')";
			mysql_query($sql) or sql_error($sql);		
	}
	
	public static function change_supplier($from, $to) {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '$to' LIMIT 1";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		$sql = "UPDATE pos SET 
						`Vendor Code` = '{$row['SupplierName']}',
						`Vendor Name` = '{$row['supplier_id']}'
						WHERE `Vendor Code` = '$from'";
		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function update_item_name() {
		$sql = "UPDATE pos
						SET `Item Name` = CONCAT(`Item Number` , ' ', `Item Name`)
						WHERE CAST(TRIM(`Item Number`) as CHAR)  <>  LEFT(TRIM(`Item Name`), LENGTH(`Item Number`))";
		mysql_query($sql) or sql_error($sql);				
	}
	

	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name());
			self::remove_dash_space();
			self::change_supplier(10, 88);
			
//$sql = "Delete
//FROM pos
//WHERE CAST( `Item Number` AS CHAR ) <> LEFT( TRIM( `Item Name` ) , LENGTH( `Item Number` ) )";
//mysql_query($sql) or sql_error($sql);						
                        $sql = "create table if not exists pos_snap_shot like pos";
                        mysql_query($sql) or sql_error($sql);	
			$sql = "delete from pos_snap_shot";
			mysql_query($sql) or sql_error($sql);
			$sql = "insert into pos_snap_shot select * from pos";
			mysql_query($sql) or sql_error($sql);
			$sql = 'OPTIMIZE table ' . self::table_name();
			mysql_query($sql) or sql_error($sql);					
			//system("mysql --host=" . HOST . " --user=" . DBUSER . " --password=" . DBPASS . " " .  STORE_UPDATES . " < " . FEED_FILE . self::$table . ".sql");		
			
//			self::add_vintage();
//			self::update_only(); 
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
	
	/**
	 * @todo evaluate the below
	 * 
	 * using "DESCRIBE " . self::table_name();
	 * to get the number of fields instead of fields in feed file
	 * which can be a problem since there can be empty spaces at the end
	 */
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$row = 1;
		$fields = '';	
		$sanitize_count = count($sanitize);
//		$sql = "DESCRIBE " . self::table_name();
//		$result = mysql_query($sql) or sql_error($sql);		
//		$num = mysql_num_rows($result);			
		$money_index = array();
		$ignore_index = array();
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					$values = '';
					if($row === 1) {
						$count = count($data);
						for($i = 0; $i < $count; $i++) {
				    	if(preg_match('/^field[0-9]{1,2}/i', $data[$i])) {
								$ignore_index[] = $i;							
							}							
						}
						//$data = array_values($data);							
					}
				    
		      //$num = count($data);
		      for ($c = 0; $c < $count; $c++) {
						if(isset($data[$c])) {
							$data[$c] = trim($data[$c]);		      
						}	  	      
						if($row == 1) {
							if(in_array($c, $ignore_index)) {							
								continue;
							}						
				    	foreach(self::$money_fields as $k => $v) {	
				    		if($data[$c] == $v) {
				    			$money_index[] = $c;
				    		}				    		
							}								
							$fields .= "`$data[$c]`,";
						}  
						else {	
							if(in_array($c, $ignore_index)) {							
								continue;
							}														
							if(isset($data[$c])) {
								if($sanitize_count > 0) {
									$data[$c] = sanitizer::strip($data[$c], $sanitize);							
								}
								if(in_array($c, $money_index)) {
									$data[$c] = str_replace(array('$', ','), '', $data[$c]);
								}
								$data[$c] = mysql_real_escape_string($data[$c]);		
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
					//seems to be alot of empty records coming in
					//this will prevent them from getting in
						if(empty($data[0])) {
							continue;
						}					
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
