<?php
/**
#run these AFTER loading data
ALTER TABLE `UP_VPR` ADD INDEX ( `xref` )  ;
ALTER TABLE `UP_VPR` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 *
 */
class UP_VPR extends feed {
	public static $table = 'UP_VPR';
	public static $feed_file = 'UP_VPR.CSV';
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function get_feed_file() {
		return self::$feed_file;
	}	
	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	
//Equivalent:  bevA_UP_VPR_set_xref
	public static function bevA_UP_VPR_set_xref() {
		$sql = 'UPDATE UP_VPR 
						SET xref = CONCAT(trim(prod_id) , "-" , trim(prod_item))';
		mysql_query($sql) or sql_error($sql);				
	}
	
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$row = 1;
		$fields = '';	
		$sanitize_count = count($sanitize);
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					$values = '';    
		      $num = count($data);
		
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
								$fields .= "`$data[$c]`,";
						}  
						else {
							if($sanitize_count > 0) {
								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
							}
							if($c == 13) { //prod_item column
								//$data[$c] = (int)$data[$c];
								$data[$c] = $data[$c];								
							}
							if($c == 9) { //date column
								$data[$c] = convert_datetime($data[$c]);
							}							
							$data[$c] = mysql_real_escape_string($data[$c]);
							//acme_add_vintage			
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
	
	public static function block_wholesaler($wholesalers = array())	{
		if(count($wholesalers) == 0 || !is_array($wholesalers)) return;
		foreach ($wholesalers as $v) {
			$sql = "DELETE FROM " . self::table_name() . "
							WHERE wholesaler LIKE '%$v%'";
 			mysql_query($sql)or sql_error($sql);			
		}
	}
	
	public static function x() {
	//$i = 0;
		$sql = "SELECT * FROM UP_VPR";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
		

				
			$text = "0" . $row['prod_item'];
			$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
			$res = mysql_query($sql);
			if(mysql_num_rows($res) == 0) {
				$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$text}' AND prod_id = '{$row['prod_id']}'";
				$res = mysql_query($sql);			
				if(mysql_num_rows($res) > 0) {
					$sql = "UPDATE UP_VPR SET prod_item = '$text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
					//echo "$sql<br>";
					mysql_query($sql);				
					//$i++;
				}
				//else {
					$new_text = "0" . $text;
					$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$new_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_VPR SET prod_item = '$new_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}		
					$third_text = "0" . $new_text;
					$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$third_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_VPR SET prod_item = '$third_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}				

					$fourth_text = "0" . $third_text;
					$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$fourth_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_VPR SET prod_item = '$fourth_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}			
					
					$fifth_text = "0" . $fourth_text;
					$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$fifth_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_VPR SET prod_item = '$fifth_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}		

					$sixth_text = $row['prod_item'] . 'NV1';
					$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$sixth_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_VPR SET prod_item = '$sixth_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}	

					if($row['prod_item'] == '0' && !empty($row['productid'])) {	
						$sql = "SELECT * FROM up_prod_xrefs where prod_item = '{$row['productid']}' AND prod_id = '{$row['prod_id']}'";
						$res = mysql_query($sql);		
						if(mysql_num_rows($res) > 0) {
							$sql = "UPDATE UP_VPR SET prod_item = '{$row['productid']}' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";					
							mysql_query($sql);				
							//$i++;
						}						
					}
					
//					if($row['prod_item'] == '21867') {
//					$sql = "SELECT * FROM up_prod_xrefs where skus = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
//					//SELECT * FROM up_prod_xrefs where skus = '021867' AND prod_id = '1032980'
//					echo $sql;
//					die('<br >done');
//					}
									
				//}

			}
		}
		//echo $i.'<br>';
	}
}//end class