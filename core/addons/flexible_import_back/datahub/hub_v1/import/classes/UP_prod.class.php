<?php
/**
 * UP_prod has an index but no pk so run:
 * ALTER TABLE  `UP_prod` DROP INDEX  `ID` , ADD PRIMARY KEY (  `ID` )
 * 
 * #need this as queries with xref in UP_prod are slow
		ALTER TABLE  `UP_prod` ADD INDEX (  `xref` )
		 ALTER TABLE `UP_prod` ADD INDEX ( `skus` )  
 */


class UP_prod extends feed {
	public static $table = 'UP_prod';
	public static $feed_file = 'UP_prod.CSV';
	public static $ignore_fields = array(
																		'rose'
																	);	
	public static function table_name() {
		return self::$table;
	}
	public static function feed_file() {
		return self::$feed_file;
	}	
	
	public static function delete_UP_prod() {//Equivalent: BevA_delete_UP_prod
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	
//Equivalent:  TransferText	
	public static function transfer_text() {
		$row = 1;
		$fields = '';	
		if (($handle = fopen(FEED_FILE . self::$feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
					$values = '';    
		      $num = count($data);
		
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
							if(!empty(self::$ignore_fields) && in_array($data[$c], self::$ignore_fields)) {
								$ignore_index = $c;
								continue;
							}						
								$fields .= "`$data[$c]`,";
						}  
						else {
							if($ignore_index == $c) {
								continue;
							}						
							$data[$c] = sanitizer::strip($data[$c], sanitizer::$generic);	
							$data[$c] = mysql_real_escape_string($data[$c]);
							//acme_add_vintage			
							$values .= "'$data[$c]',";
						}     
		
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
		return $row;	
	}
	public static function x() {
	//$i = 0;
		$sql = "SELECT * FROM UP_prod";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			$temp = explode(' ', $row['skus']);
			$text = "0" . $row['skus'];
			$sql = "SELECT * FROM up_prod_xrefs where skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
			$res = mysql_query($sql);
			if(mysql_num_rows($res) == 0) {
				$sql = "SELECT * FROM up_prod_xrefs where skus = '{$text}' AND prod_id = '{$row['prod_id']}'";
				$res = mysql_query($sql);			
				if(mysql_num_rows($res) > 0) {
					$sql = "UPDATE UP_prod SET skus = '$text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
					//echo "$sql<br>";
					mysql_query($sql);				
					//$i++;
				}
				//else {
					$new_text = "0" . $text;
					$sql = "SELECT * FROM up_prod_xrefs where skus = '{$new_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_prod SET skus = '$new_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}		
					$third_text = "0" . $new_text;
					$sql = "SELECT * FROM up_prod_xrefs where skus = '{$third_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_prod SET skus = '$third_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}		

					$fourth_text = "0" . $third_text;
					$sql = "SELECT * FROM up_prod_xrefs where skus = '{$fourth_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_prod SET skus = '$fourth_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}		

					$fifth_text = "0" . $fourth_text;
					$sql = "SELECT * FROM up_prod_xrefs where skus = '{$fifth_text}' AND prod_id = '{$row['prod_id']}'";
					$res = mysql_query($sql);		
					if(mysql_num_rows($res) > 0) {
						$sql = "UPDATE UP_prod SET skus = '$fifth_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";					
						mysql_query($sql);				
						//$i++;
					}										
				//}

			}
		}
		//echo $i.'<br>';
	}
	
	//Equivalent: update_up_prod_wildman
	//this had InStrRev
//	public static function update_up_prod_wildman() {
//		$sql = 'UPDATE ' . self::$table . ' AS up 
//						INNER JOIN wildman_feed AS wld 
//						ON Mid(up.xref,InStr(up.xref,"-")+1) = wld.`Product #` SET up.confstock = "Y"
//						WHERE coalesce(wld.`On Hand Cases`, "0") <> "0";';
//		mysql_query($sql) or sql_error($sql);	
//	}

	// public static function update_up_prod_wildman() {
		// $sql = 'UPDATE ' . self::$table . ' AS up 
						// INNER JOIN wildman_feed AS wld 
						// ON Mid(up.xref,InStr(up.xref,"-")+1) = wld.`Item No` SET up.confstock = "Y"
						// WHERE coalesce(wld.`Cases Available`, "0") <> "0";';
		// mysql_query($sql) or sql_error($sql);	
	// }	
	
	//Equivalent: bevA_UP_prod_set_xref 	
	public static function prod_set_xref() {
		$sql = 'UPDATE ' . self::$table . '
						SET xref = CONCAT(rtrim(ltrim(coalesce(prod_id," "))) , "-" , trim(Left(CONCAT(coalesce(skus," ") , " "),InStr(CONCAT(coalesce(skus," ") , " ")," "))));';
		
		mysql_query($sql) or sql_error($sql);			
	}	
	
	public static function block_wholesaler($wholesalers = array())	{
				if(count($wholesalers) == 0 || !is_array($wholesalers)) return;
		foreach ($wholesalers as $v) {
			$sql = "DELETE FROM " . self::table_name() . "
							WHERE companies LIKE '%$v%'";
 			mysql_query($sql)or sql_error($sql);			
		}
	}	

	
}