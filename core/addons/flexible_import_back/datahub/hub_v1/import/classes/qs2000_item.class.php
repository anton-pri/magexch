<?php
/**
for ID, drop the index and define it as a pk
 */

class qs2000_item extends feed {
	public static $table = 'qs2000_item';
	public static $feed_file = 'qs2000_item.txt';
	
	public static function table_name() {
		return self::$table;
	}

	public static function delete_table() {
		$sql = "DELETE  FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  pricing_update_pos_cost	
	public static function pricing_update_pos_cost() {
		$sql = "UPDATE qs2000_item AS qs 
						INNER JOIN pos_cost_temp AS ct ON qs.ID = ct.ID 
						SET qs.Cost = COALESCE(ct.cost, qs.cost)
						WHERE qs.quantity <= 0";
 		mysql_query($sql) or sql_error($sql);					
	}
		
	public static function key_to_varchar() {
		$sql = "ALTER TABLE " . self::$table . " CHANGE `ID` `ID` VARCHAR( 11 ) NOT NULL";
 		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function key_to_id() {
		$sql = "ALTER TABLE " . self::$table . " CHANGE `ID` `ID` INT( 11 ) NOT NULL AUTO_INCREMENT";
 		mysql_query($sql) or sql_error($sql);			
	}	

//Equivalent:  hub_update_POS-BinLocation
	public static function hub_update_POS_BinLocation() {
		self::key_to_varchar();
		$sql = "UPDATE qs2000_item AS q 
						LEFT JOIN item_store2 AS i ON i.store_sku = q.ID and i.store_id = 1 
						SET q.BinLocation = i.item_id";
		mysql_query($sql) or sql_error($sql);		
		self::key_to_id();				
	}
	
//Equivalent:  hub_update_POS_notes	
	public static function hub_update_POS_notes() {
		$sql = "UPDATE qs2000_item AS qi 
						INNER JOIN item AS i ON qi.BinLocation = i.ID
						SET qi.Notes = CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR))";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  hub_set_POS_supplier
	public static function hub_set_POS_supplier() {
	/**
	 * @todo change this to the query below
	 */
//UPDATE qs2000_item INNER JOIN
//(SELECT qi.ID, supplierid, x.xref, 
//IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '') as comp, left(COALESCE(x.xref,'    '),4) as sup, IF(left(COALESCE(x.xref,'    '),4) = 'TOUT',27,
//IF(left(COALESCE(x.xref,'    '),4) = 'BEAR',148,
//IF(left(COALESCE(x.xref,'    '),4) = 'VISN',133,
//IF(left(COALESCE(x.xref,'    '),4) = 'BOWL',150,
//IF(left(COALESCE(x.xref,'  '),2) = 'VS',115,
//IF(left(COALESCE(x.xref,'    '),4) ='SKUR',103,IF(left(COALESCE(x.xref,'    '),4) ='POLA',48,IF(left(COALESCE(x.xref,'     '),5)='ANGEL',69,IF(IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '')='COLO',10,IF(IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '')='LAUB',11,IF(IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '')='MSCO',24,IF(IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '')='OPIC',83,IF(IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '')='SWS ',63,IF(IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '')='WILD',66,IF(IF(left(COALESCE(f.companies,'    '),4) <> '    ',left(f.companies,4), '')='WINB',13,supplierid))))))))))))))) as supid, f.xref, f.companies
//from ((qs2000_item qi
//inner join item_store2 si on qi.ID = si.store_sku)
//inner join item_xref x on (si.item_id = x.item_id and x.store_id = 1))
//left join BevAccessFeeds f on x.xref = f.xref
//where (si.store_id = 1 and Trim(COALESCE(x.xref,'')) <> ''))  AS a ON qs2000_item.ID = a.ID SET qs2000_item.supplierid = a.supid	
		$sql = "SELECT qi.ID, supplierid, x.xref as x_xref, f.xref as f_xref, f.companies, 
						IF( left( COALESCE( f.companies, '    ' ) , 4 ) <> '    ', left( f.companies, 4 ) , '' ) AS comp, 
						LEFT( COALESCE( x.xref, '    ' ) , 4 ) AS sup
						FROM ((qs2000_item qi
						INNER JOIN item_store2 si ON qi.ID = si.store_sku)
						INNER JOIN item_xref x ON (si.item_id = x.item_id and x.store_id = 1))
						LEFT JOIN BevAccessFeeds as f ON x.xref = f.xref
						WHERE (si.store_id = 1 AND Trim(COALESCE(x.xref,'')) <> '') order by qi.ID";
		
		$result = mysql_query($sql) or sql_error($sql);		
		while ($row = mysql_fetch_array($result)) {
		//print_r($row);die;
		//IF(sup = 'TOUT',27,
		//IF(sup = 'BEAR',148,
		//IF(sup = 'VISN',133,
		//IF(sup = 'BOWL',150,
		//IF(sup='SKUR',103,IF(sup='POLA',48,IF(left(COALESCE(x.xref,'     '),5)='ANGEL',69,IF(comp='COLO',10,IF(comp='LAUB',11,IF(comp='MSCO',24,IF(comp='OPIC',83,IF(comp='SWS ',63,IF(comp='WILD',66,IF(comp='WINB',13,supplierid))))))))))))))
			//echo $row['comp'] . ' ' . $row['sup'] . '<br>';
			$sup = $row['sup'];
			$comp = $row['comp'];
			$supplierid = $row['supplierid'];
		
			if($sup == 'TOUT') {
				$supid = 27;
			}
			elseif($sup == 'BEAR') {
				$supid = 148;	
			}
			elseif($sup == 'VISN') {
				$supid = 133;	
			}	
			elseif($sup == 'BOWL') {
				$supid = 150;	
			}		
			elseif($sup == 'SKUR') {
				$supid = 103;	
			}		
			elseif($sup == 'POLA') {
				$supid = 48;	
			}		
			elseif(left($sup, 2) == 'VS') {
				$supid = 115;	
				//die('vias');
			}		
			elseif(left($row['x_xref'], 5) == 'ANGEL') {
				$supid = 69;	
				//die('angel');
			}		
			elseif($comp == 'COLO') {
				$supid = 10;	
			}			
			elseif($comp == 'LAUB') {
				$supid = 11;	
			}		
			elseif($comp == 'MSCO') {
				$supid = 24;	
			}		
			elseif($comp == 'OPIC') {
				$supid = 83;	
			}		
			elseif($comp == 'SWS') {
				$supid = 63;	
			}		
			elseif($comp == 'WILD') {
				$supid = 66;	
			}		
			elseif($comp == 'WINB') {
				$supid = 66;	
			}			
			else {
				$supid = $supplierid;	
			}
			$sql = "UPDATE qs2000_item 				
							SET qs2000_item.supplierid = '{$supid}'
							WHERE ID = '{$row['ID']}'";	
		//ON qs2000_item.ID = a.ID 
			mysql_query($sql) or sql_error($sql);
		}	
	}
	
	/**
	 * @todo completed import
	 *	just use sql file for now
	 */
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			//self::transfer_text(self::$feed_file, self::table_name());
			system("mysql --host=" . HOST . " --user=" . DBUSER . " --password=" . DBPASS . " " .  STORE_UPDATES . " < " . FEED_FILE . self::$table . ".sql");		
			
//			self::add_vintage();
//			self::update_only(); 
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
}//end class