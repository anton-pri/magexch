<?php

class bevaccess_supplement extends feed {
	public static $table = 'bevaccess_supplement';
	public static $feed_file = 'UP_prod.XML';

	
	public static function table_name() {
		return self::$table;
	}
 
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}	
	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name(), "\t");
			self::prod_set_xref();
//			self::clean_money_fields();
//			self::remove_bad_records();
//			self::special_rules_import();
//			self::update_product_code();
//			self::update_only();
			echo self::$table .' table update is complete<br />';		

		
		}	
	}		
	

	public static function prod_set_xref() {
		$sql = 'UPDATE ' . self::$table . '
						SET xref = CONCAT(rtrim(ltrim(coalesce(prod_id," "))) , "-" , trim(Left(CONCAT(coalesce(skus," ") , " "),InStr(CONCAT(coalesce(skus," ") , " ")," "))));';
		
		mysql_query($sql) or sql_error($sql);			
	}		
	 
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
 
		if (file_exists(FEED_FILE . self::$feed_file)) {
		  $xml = simplexml_load_file(FEED_FILE . self::$feed_file);
			$count = count($xml->_tempprod);
			$sanitize_count = count($sanitize);
			for($i = 0; $i < $count; $i++) {
				$fields = '';
				$values = '';
				foreach($xml->_tempprod[$i] as $k => $v) {
					$k = trim($k);
					$fields .= "`$k`,";
					$v = trim($v);					
					if(
						$k == 'txt_rat' ||
						$k == 'txt_rev' ||
						$k == 'txt_des' ||
						$k == 'txt_wmn' ||		
						$k == 'txt_tek' ||	
						$k == 'txt_fpr' ||				
						$k == 'txt_rec' ||	
						$k == 'txt_txt' ||			
						$k == 'txt_tas'																																
					) 
					{
						//$v = htmlentities($v);
						//$v = html_entity_decode($v);
						$v = self::remove_accents(self::utf8dec($v));
						$v = strip_tags($v);				
						$v = preg_replace("/[^[:alnum:][:punct:][:blank:]]/", '', $v);							
					}

//					if(count($sanitize_count) > 0) {
//						$v = sanitizer::strip($v, $sanitize);//this strips out periods even when it's empty
//					}
					
					$v = mysql_real_escape_string($v);	
					$values .= "'$v',";
				}		
				$fields = rtrim($fields,',');
				$values = rtrim($values,',');
				$sql = "INSERT INTO " . $table . " ($fields)
								VALUES ($values)";
				mysql_query($sql) or sql_error($sql);
		//cw_flush(". ");
			}    
		}
 		return $i;

	}//transfer_text	

	public static function update_item_longdesc() {
		$sql = "SELECT * from item as i
						INNER JOIN item_xref as ix
						ON i.ID = ix.item_id
						INNER JOIN bevaccess_supplement as b
						ON b.xref = ix.xref
						WHERE COALESCE(i.longdesc, '') = ''						
						AND (
							TRIM(b.txt_rat) <> '' 
							OR TRIM(b.txt_tas) <> '' 
							OR TRIM(b.txt_des) <> '' 
							OR TRIM(b.txt_wmn) <> '' 						
							OR TRIM(b.txt_fpr) <> '' 
						)";


		$result = mysql_query($sql) or sql_error($sql);	
		$ids = '(';
		while ($row = mysql_fetch_array($result)) { 
			$string = array();
			if(!empty($row['txt_rat'])) {
				$string[] = $row['txt_rat'];
			}
			if(!empty($row['txt_tas'])) {
				$string[] = $row['txt_tas'];
			}	
			if(!empty($row['txt_des'])) {
				$string[] = $row['txt_des'];
			}		
			if(!empty($row['txt_wmn'])) {
				$string[] = $row['txt_wmn'];
			}							
			if(!empty($row['txt_fpr'])) {
				$string[] = $row['txt_fpr'];
			}	
			$string = array_unique($string);
			$longdesc = implode("\n\n", $string);	
		
			$sql = "UPDATE item
							SET LongDesc = '" . mysql_real_escape_string($longdesc) . "'
							WHERE ID = '{$row['ID']}'";		
			$ids .= $row['ID'] . ',';
			mysql_query($sql) or sql_error($sql);			
		}	
		$ids = rtrim($ids, ',')	;
		$ids .= ')';
		//mail('antonpribytov@gmail.com', 'item ids', $ids);		
		//echo $ids;
	}	
	

}

	
