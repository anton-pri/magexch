<?php
/**
#probably will need but run after loading data
#ALTER TABLE  `feeds_item_compare` ADD  `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */

//SWE_store_insert_compare

class feeds_item_compare extends feed {
	public static $table = 'feeds_item_compare';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  Compare_delete
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);
	}
		
//Equivalent:  compare_clean_name_field
	public static function compare_clean_name_field()	{
		$sql = "UPDATE " . self::table_name() . "
						SET Name = replace(replace(replace(COALESCE(Name,''),'\"\"','\"'),'\"\"','\"'),'\"\"','\"'), 
						Producer = replace(replace(replace(COALESCE(Producer,''),'\"\"','\"'),'\"\"','\"'),'\"\"','\"')";	
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  Compare_mark_for_deletion	
	public static function compare_mark_for_deletion() {
		$sql = "UPDATE " . self::table_name() . " AS c 
						INNER JOIN block_xref_from_compare AS b ON c.source = b.feed and c.xref = b.xref 
						SET c.xref = 'delete_me'";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  Compare_delete_marked
	public static function compare_delete_marked() {
		$sql = "DELETE FROM " . self::table_name() . "  
						WHERE xref = 'delete_me'";
		mysql_query($sql) or sql_error($sql);		
	}
//Equivalent:  Compare_set_producer_to_wine	
	public static function compare_set_producer_to_wine() {
		$sql = "UPDATE " . self::table_name() . " SET Producer = COALESCE(Wine,Name)
						WHERE Trim(COALESCE(Producer,'')) = ''";
		mysql_query($sql) or sql_error($sql);				
	}
	
//Equivalent:  Compare_insert_hub	
	public static function compare_insert_hub() {
	//changed feb 28, 2011 from isnull(c.catalogid) to COALESCE(c.catalogid, 0) = 0  as when it gets loaded back in from desktop, nulls become 0's
//		$sql = "INSERT INTO item ( TareWeight, Vintage, Producer, `Size`, varietal, initial_xref, name, region, country, appellation, `sub-appellation`, `RP Rating`, `RP Review`, `WS Rating`, `WS Review`, `ST Rating`, `ST Review`, LongDesc, bot_per_case )
//						SELECT IF(c.size=\"6.0Ltr\",24,IF(c.size=\"5.0Ltr\",20,IF(c.size=\"3.0Ltr\",12,IF(c.size=\"1.5Ltr\",6,IF(c.size=\"375ml\",1.5,IF(c.size=\"187ml\" or c.size=\"200ml\",.75,3)))))), c.vintage, c.producer, c.size, c.varietal AS Expr11, CONCAT(c.source , c.xref), c.Name, c.region, c.country, c.appellation, c.`sub-appellation`, c.Parker_rating, c.Parker_review, c.Spectator_rating, c.Spectator_review, c.Tanzer_rating, c.Tanzer_review, c.description, c.bottles_per_case
//						FROM feeds_item_compare AS c
//						WHERE c.add_to_hub=true and isnull(c.catalogid) and c.Source <> 'Hub'";

		$sql = "INSERT INTO item (id, TareWeight, Vintage, Producer, `Size`, varietal, initial_xref, name, region, country, appellation, `sub-appellation`, `RP Rating`, `RP Review`, `WS Rating`, `WS Review`, `ST Rating`, `ST Review`, LongDesc, bot_per_case, dhmd_id)
						SELECT c.dhmd_id, IF(c.size=\"6.0Ltr\",24,IF(c.size=\"5.0Ltr\",20,IF(c.size=\"3.0Ltr\",12,IF(c.size=\"1.5Ltr\",6,IF(c.size=\"375ml\",1.5,IF(c.size=\"187ml\" or c.size=\"200ml\",.75,IF(c.size=\"500ml\",2,IF(c.size=\"15.0Ltr\",60,IF(c.size=\"1.0Ltr\",4,3))))))))), c.vintage, c.producer, c.size, c.varietal AS Expr11, CONCAT(c.source , c.xref), c.Name, c.region, c.country, c.appellation, c.`sub-appellation`, c.Parker_rating, c.Parker_review, c.Spectator_rating, c.Spectator_review, c.Tanzer_rating, c.Tanzer_review, c.description, c.bottles_per_case, c.dhmd_id 
						FROM feeds_item_compare AS c
						WHERE c.add_to_hub=true and COALESCE(c.catalogid, 0) = 0 and c.Source <> 'Hub'";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  delete_nocost
	public static function delete_nocost() {
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(cost, 0) < 3
						AND trim(Source) <> 'Hub' 
						AND trim(Source) <> 'Feed_SWE_store' 
						AND trim(Source) <> 'Feed_DWB_store'";
		mysql_query($sql) or sql_error($sql);			
	}

	//eg. 4.000Ltr becomes 4Ltr
	public static function modify_size() {
		$sql = "SELECT * FROM " . self::table_name() . " WHERE size  LIKE '%ltr%'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				if (preg_match("/\.0/i", $row['size'])) {
					$row['size'] = str_replace('10.', 'replace.', $row['size']);		
					$row['size'] = str_replace('0', '', $row['size']);		
					$row['size'] = str_replace('replace.', '10.', $row['size']);			
					$row['size'] = str_replace('.', '.0', $row['size']);										
					$sql = "UPDATE " . self::table_name() . "
									SET size = '{$row['size']}'
									WHERE table_id = '{$row['table_id']}'";
					mysql_query($sql) or sql_error($sql);				
				}				
			}
		}	
	}



	public static function clean_data() {
		$sql = "SELECT * FROM " . self::table_name() . "
						WHERE Source <> 'Hub'";
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result)) { 
			$sql = "UPDATE  feeds_item_compare
							SET Wine = '" . mysql_real_escape_string(self::remove_accents(self::utf8dec($row['Wine']))) . "',
							Producer = '" . mysql_real_escape_string(self::remove_accents(self::utf8dec($row['Producer']))) . "',
							Name = '" . mysql_real_escape_string(self::remove_accents(self::utf8dec($row['Name']))) . "'
							WHERE table_id = '{$row['table_id']}'";		
			mysql_query($sql) or sql_error($sql);		
		}		
	}

}//end class
