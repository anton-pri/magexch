<?php


class pos_stock extends feed {
	public static $table = 'pos_stock';
	public static $feed_file = 'pos_stock.txt';
	
	public static function table_name() {
		return self::$table;
	}

	public static function delete_table() {
		$sql = "DELETE  FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);					
	}


	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name());

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
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					$values = '';    
		      //$num = count($data);
					$sql = "DESCRIBE " . self::table_name();
					$result = mysql_query($sql) or sql_error($sql);		
					$num = mysql_num_rows($result);	
		      for ($c = 0; $c < $num; $c++) {
						if(isset($data[$c])) {
							$data[$c] = trim($data[$c]);		      
						}	  	      
						if($row == 1) {
								$fields .= "`$data[$c]`,";
						}  
						else {
//							if($c == 25) {
//								if(empty($data[$c])) {
//									$data[$c] = 'swe' . $data[0];
//								}	
//							}			

																	
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