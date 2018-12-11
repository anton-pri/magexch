<?php
/**
 run this before or after data dump
 ALTER TABLE `Skurnik_feed` ADD PRIMARY KEY ( `MSW Code` )  
 */

//store 2
//Need to manipulate feed file to get it right
class Skurnik_feed extends feed {
	public static $table = 'Skurnik_feed';
	public static $feed_file = 'skurnik_inventory.txt';
	public static $ignore_fields = array('Web URL');		
	public static $supplier_id = '103';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}		
	
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		

        public static function get_splitcase_charge() {
                $sql = "SELECT * FROM splitcase_charges WHERE company = 'SKUR'";
                $result = mysql_query($sql) or sql_error($sql);
                $row = mysql_fetch_array($result);
                return $row['charge'];
        }

	
//Equivalent:  Skurnik_insert_POS
	public static function insert_pos() {
                global $config; 
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
                                                `Custom Field 1`,  
						`Vendor Name`,
						`Manufacturer`,
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
                                                IF((INSTR('".$config['flexible_import']['fi_spirit_varietals']."',c.varietal) AND c.varietal!=''),'Spirit','Wine'),
						c.country,
						'" . mysql_real_escape_string(self::get_supplier_name()) . "',
						c.Producer,
						c.varietal,
						c.bottles_per_case,
						c.xref
						FROM feeds_item_compare AS c LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0) = qi.`Alternate Lookup`
						WHERE (c.store_id = 1 and Left(c.xref,4) = 'SKUR' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);	
		pos::binlocation_to_varchar();			
	}
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  skurnik_delete_table	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
//Equivalent:  Skurnik_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,'     '),5) = 'SKUR-'";
		mysql_query($sql) or sql_error($sql);		
	}
//Equivalent:  Skurnik_update_item_xref	
	public static function update_item_xref() {
//		$sql = "UPDATE (item_xref AS i INNER JOIN Skurnik_feed AS f ON i.xref = CONCAT('SKUR-' , f.`MSW Code`)) LEFT JOIN item AS i2 ON i.item_id = i2.ID SET i.qty_avail = IF(not isnull(f.`MSW Code`) and Trim(COALESCE(f.`MSW Code`,' ')) <> '','9999','0'), 
//						i.min_price = CAST(COALESCE(f.`Set Price Request`,'0') as DECIMAL(10,2)), i.bot_per_case = CAST(COALESCE(f.`Pack`,12) as SIGNED), i.cost_per_case = Round(IF(IsNull(`f`.`1cs`) or f.`1cs` = 0,0, CAST(`f`.`1cs` as DECIMAL(10,2))),2), 
//						i.cost_per_bottle = Round(IF(IsNull(`f`.`1cs`) or f.`1cs` = 0,0,CAST(`f`.`1cs` as DECIMAL(10,2)))/COALESCE(i2.bot_per_case,CAST(COALESCE(f.`Pack`,12) as SIGNED)),2)";
			
	//changed March 1, 2011
	//cost_per_bottle was calculation was changed since 0 was being returned in 
	//IF(COALESCE(i2.bot_per_case, 0) > 0, i2.bot_per_case, CAST(COALESCE(f.`Pack`,12) as SIGNED)),2)";
	//ms access has null values so it works there but not here
	
//		$sql = "UPDATE (item_xref AS i INNER JOIN Skurnik_feed AS f ON i.xref = CONCAT('SKUR-' , f.`MSW Code`)) LEFT JOIN item AS i2 ON i.item_id = i2.ID SET i.qty_avail = IF(not isnull(f.`MSW Code`) and Trim(COALESCE(f.`MSW Code`,' ')) <> '','9999','0'), 
//						i.min_price = CAST(COALESCE(f.`Set Price Request`,'0') as DECIMAL(10,2)), i.bot_per_case = CAST(COALESCE(f.`Pack`,12) as SIGNED), i.cost_per_case = Round(IF(IsNull(`f`.`1cs`) or f.`1cs` = 0,0, CAST(`f`.`1cs` as DECIMAL(10,2))),2), 
//						i.cost_per_bottle = Round(IF(IsNull(`f`.`1cs`) or f.`1cs` = 0,0,CAST(`f`.`1cs` as DECIMAL(10,2)))/IF(COALESCE(i2.bot_per_case, 0) > 0, i2.bot_per_case, CAST(COALESCE(f.`Pack`,12) as SIGNED)),2)";
		
		$sql = "UPDATE (
						item_xref AS i INNER JOIN Skurnik_feed AS f ON i.xref = CONCAT('SKUR-' , f.`MSW Code`)) 						
						SET i.qty_avail = IF(not isnull(f.`MSW Code`) and Trim(COALESCE(f.`MSW Code`,' ')) <> '','9999','0'), 
						i.min_price = CAST(COALESCE(f.`Set Price Request`,'0') as DECIMAL(10,2)), 
						i.bot_per_case = CAST(COALESCE(f.`Pack`,12) as SIGNED), 
						i.cost_per_case = Round(IF(IsNull(`f`.`1cs`) or f.`1cs` = 0,0, CAST(`f`.`1cs` as DECIMAL(10,2))),2), 
						i.cost_per_bottle = 
						Round(IF(IsNull(`f`.`1cs`) or f.`1cs` = 0,0,CAST(`f`.`1cs` as DECIMAL(10,2)))/ CAST(COALESCE(f.`Pack`,12) as SIGNED),2)";
		mysql_query($sql) or sql_error($sql);				
	}

//Equivalent:  Skurnik_insert_compare	
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT \"Feed_Skurnik\" AS Source, 
						
						Trim(Replace(Replace(Replace(Replace(Right(Trim(u.Description),LENGTH(Trim(u.Description))-
						(
						length(trim( u.Description) ) - length( right( trim(u.Description), instr( reverse(trim( u.Description )) , \",\" ) -1 ) )
						)
						
						),\"\"\"\",\"\"),\"(\",\"\"),\")\",\"\"),\"'\",\"\")) AS producer, 
						
						
						
						Left(CONCAT(Trim(Replace(Replace(Replace(Replace(Right(Trim(u.Description),LENGTH(Trim(u.Description))-
						(
						IF(INSTR(trim( Description ), \",\") > 0,
						length(trim( Description) ) - length( right( trim(Description), instr( reverse(trim( Description )) , \",\" ) -1 ) )
						, 0)
						)
						
						),\"\"\"\",\"\"),\"(\",\"\"),\")\",\"\"),\"'\",\"\")) , \" \" , Trim(Replace(Replace(Replace(Replace(Replace(Replace(Replace(Left(Trim(u.Description),IF(InStr(u.Description,\",\") > 0,
						
						
						(
						length(trim( u.Description) ) - length( right( trim(u.Description), instr( reverse(trim( u.Description )) , \",\" ) -1 ) )
						)
						
						,LENGTH(u.Description))),\"\"\"\",\"\"),\"(\",\"\"),\"',\",\"\"),\")\",\"\"),\" '\",\" \"),\",\",\"\"),\"' \",\" \"))),30) AS Wine, 
						
						
						Trim(Replace(Replace(Replace(Replace(Replace(Replace(Replace(Left(Trim(u.Description),IF(InStr(u.Description,\",\"),
						(
						length(trim( u.Description) ) - length( right( trim(u.Description), instr( reverse(trim( u.Description )) , \",\" ) -1 ) )
						)
						,LENGTH(u.Description))),\"\"\"\",\"\"),\"(\",\"\"),\"',\",\"\"),\")\",\"\"),\" '\",\" \"),\",\",\"\"),\"' \",\" \")) AS Name, 
						
						
						IF(LENGTH(u.vintage)<>4,\"NV\",CAST(u.vintage  as CHAR)) AS vintage, 
						
						CAST(IF(IsNull(u.size) Or u.size=\"0\",\"\",IF(u.size=\"1500\",\"1.5Ltr\",IF(u.size=\"3000\",\"3.0Ltr\",IF(CAST(u.size as  DECIMAL(10,2)) <1000,CONCAT(u.size , \"ml\"),IF(CAST(u.size as DECIMAL(10,2))>=1000,
						
						IF(CAST(right(u.size, length(u.size) - 1) as CHAR)='000', CONCAT(LEFT(u.size, 1), \"Ltr\"), CONCAT(CAST(Round(CAST(u.size as DECIMAL(10,2))/1000,1) as CHAR) , \"Ltr\"))
						
						,\"Other\"))))) as CHAR), 
						
						CONCAT(\"SKUR-\" , u.`MSW Code`) AS xref, 
						CAST(`u`.`Pack` as SIGNED), 
						Null AS catalogid, ucwords(COALESCE(u.`Region(Origin)`,\" \")) AS Region, 
						IF(u.Country<>\"USA\",ucwords(COALESCE(u.Country,\"\")),CAST(COALESCE(u.Country,\"\") as CHAR)), ucwords(u.Varietal), ucwords(u.Appellation) AS appellation, 
						\"\" AS `sub-appellation`, 
						Round(CAST(`u`.`1cs` as DECIMAL(10,2))/CAST(`u`.`Pack` as DECIMAL(10,2)),2) AS cost, 
						IF(InStr(u.Parker,\"-\"),Right(Trim(IF(InStr(u.Parker,\"+\"),Replace(u.Parker,\"+\",\"\"),u.Parker)),2),IF(InStr(u.Parker,\"+\"),Replace(u.Parker,\"+\",\"\"),u.Parker)), IF(InStr(u.Spectator,\"-\"),Right(Trim(IF(InStr(u.Spectator,\"+\"),Replace(u.Spectator,\"+\",\"\"),u.Spectator)),2),IF(InStr(u.Spectator,\"+\"),Replace(u.Spectator,\"+\",\"\"),u.Spectator)), IF(not Instr( IF(InStr(u.Tanzer,\"-\"),Right(Trim(IF(InStr(u.Tanzer,\"+\"),Replace(u.Tanzer,\"+\",\"\"),u.Tanzer)),2),IF(InStr(u.Tanzer,\"+\"),Replace(u.Tanzer,\"+\",\"\"),u.Tanzer)),\"star\"),
						
						 IF(InStr(u.Tanzer,\"-\"),Right(Trim(IF(InStr(u.Tanzer,\"+\"),Replace(u.Tanzer,\"+\",\"\"),u.Tanzer)),2),IF(InStr(u.Tanzer,\"+\"),Replace(u.Tanzer,\"+\",\"\"),u.Tanzer)),\"\"), 
						
						CONCAT(IF(COALESCE(trim(u.Parker), '') <> '',CONCAT(\"Robert Parker rating: \" , u.Parker , \" \"),\"\") , IF(COALESCE(trim(u.Spectator), '') <> '',CONCAT(\"Wine Spectator rating: \" , u.Spectator , \" \"),\"\") , IF(COALESCE(trim(u.Tanzer), '') <> '',CONCAT(\"Tanzer rating: \" , u.Tanzer , \" \"),\"\")), 
						'1', '" . self::get_supplier_id() . "'
						FROM Skurnik_feed AS u LEFT JOIN item_xref AS i ON CONCAT(\"SKUR-\" , Trim(u.`MSW Code`)) = i.xref
WHERE isNull(i.xref) and `u`.`1cs` > 0 and `u`.`Pack` > 0";
		mysql_query($sql) or sql_error($sql);		
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
	}

//Equivalent:  hub_apply_splitcase_to_cost_skur
	public static function hub_apply_splitcase_to_cost_skur() {
                $skurnik_split_case_charge = self::get_splitcase_charge();        

                if (!empty($skurnik_split_case_charge))              
                    $sql = "UPDATE item_xref AS i SET split_case_charge = $skurnik_split_case_charge
                                                WHERE left(COALESCE(i.xref,'    '),4) = 'SKUR'";
                else         
    		    $sql = "UPDATE item_xref AS i SET split_case_charge = COALESCE(0*IF(COALESCE(i.bot_per_case,12) > 1,12/COALESCE(i.bot_per_case,12),0),0)
						WHERE left(COALESCE(i.xref,'    '),4) = 'SKUR'";
		mysql_query($sql) or sql_error($sql);				
	}
	
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$row = 1;
		$fields = '';	
		$sanitize_count = count($sanitize);
		$field_count = '';
		$field_count_insert = '';
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
					$values = '';    
		      $num = count($data);
		
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
							if(!empty(self::$ignore_fields) && in_array($data[$c], self::$ignore_fields)) {
								$ignore_index[] = $c;
								continue;
							}							
							$fields .= "`$data[$c]`,";
							$field_count = $num;
						}  
						else {
							if(in_array($c, $ignore_index)) {							
								continue;
							}								
							if($sanitize_count > 0) {
								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
							}
							$field_count_insert = $num;
//							if(preg_match("/\$/i", $data[$c]) && preg_match("/\.00/i", $data[$c])) {
//								$data[$c] = str_ireplace('$', '', $data[$c]);
//								$data[$c] = str_ireplace(',', '', $data[$c]);								
//							}
							if($c > 4 && $c < 13) {
								$data[$c] = str_ireplace('$', '', $data[$c]);
								$data[$c] = str_ireplace(',', '', $data[$c]);								
							}							
							$data[$c] = mysql_real_escape_string($data[$c]);								
							$values .= "'$data[$c]',";
						}     
		
					}      
					if($field_count != $field_count_insert) {
						$values .= "'',";
					}
					$fields = rtrim($fields, ',');    
					$values = rtrim($values, ',');  		

					if($row > 1 && $values != "''") {
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
	
//Equivalent:  Skurnik_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}		
	
//Equivalent:  Skurnik_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_skur();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	

}//end class

//insert compare, clean
//INSERT INTO feeds_item_compare ( Source, producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id )
//SELECT "Feed_Skurnik" AS Source, 
//
//Trim(Replace(Replace(Replace(Replace(Right(Trim(u.Description),LENGTH(Trim(u.Description))-
//(
//length(trim( u.Description) ) - length( right( trim(u.Description), instr( reverse(trim( u.Description )) , "," ) -1 ) )
//)
//
//),"""",""),"(",""),")",""),"'","")) AS producer, 
//
//
//
//Left(CONCAT(Trim(Replace(Replace(Replace(Replace(Right(Trim(u.Description),LENGTH(Trim(u.Description))-
//(
//IF(INSTR(trim( Description ), ",") > 0,
//length(trim( Description) ) - length( right( trim(Description), instr( reverse(trim( Description )) , "," ) -1 ) )
//, 0)
//)
//
//),"""",""),"(",""),")",""),"'","")) , " " , Trim(Replace(Replace(Replace(Replace(Replace(Replace(Replace(Left(Trim(u.Description),IF(InStr(u.Description,",") > 0,
//
//
//(
//length(trim( u.Description) ) - length( right( trim(u.Description), instr( reverse(trim( u.Description )) , "," ) -1 ) )
//)
//
//,LENGTH(u.Description))),"""",""),"(",""),"',",""),")","")," '"," "),",",""),"' "," "))),30) AS Wine, 
//
//
//Trim(Replace(Replace(Replace(Replace(Replace(Replace(Replace(Left(Trim(u.Description),IF(InStr(u.Description,","),
//(
//length(trim( u.Description) ) - length( right( trim(u.Description), instr( reverse(trim( u.Description )) , "," ) -1 ) )
//)
//,LENGTH(u.Description))),"""",""),"(",""),"',",""),")","")," '"," "),",",""),"' "," ")) AS Name, 
//
//
//IF(LENGTH(u.vintage)<>4,"NV",CAST(u.vintage  as CHAR)) AS vintage, 
//
//CAST(IF(IsNull(u.size) Or u.size="0","",IF(u.size="1500","1.5Ltr",IF(u.size="3000","3.0Ltr",IF(CAST(u.size as  DECIMAL(10,2)) <1000,CONCAT(u.size , "ml"),IF(CAST(u.size as DECIMAL(10,2))>=1000,
//
//IF(CAST(right(u.size, length(u.size) - 1) as CHAR)='000', CONCAT(LEFT(u.size, 1), "Ltr"), CONCAT(CAST(Round(CAST(u.size as DECIMAL(10,2))/1000,1) as CHAR) , "Ltr"))
//
//,"Other"))))) as CHAR), 
//
//CONCAT("SKUR-" , u.`MSW Code`) AS xref, 
//CAST(`u`.`Pack` as SIGNED), 
//Null AS catalogid, ucwords(COALESCE(u.`Region(Origin)`," ")) AS Region, 
//IF(u.Country<>"USA",ucwords(COALESCE(u.Country,"")),CAST(COALESCE(u.Country,"") as CHAR)), ucwords(u.Varietal), ucwords(u.Appellation) AS appellation, 
//"" AS `sub-appellation`, 
//Round(CAST(`u`.`1cs` as DECIMAL(10,2))/CAST(`u`.`Pack` as DECIMAL(10,2)),2) AS cost, 
//IF(InStr(u.Parker,"-"),Right(Trim(IF(InStr(u.Parker,"+"),Replace(u.Parker,"+",""),u.Parker)),2),IF(InStr(u.Parker,"+"),Replace(u.Parker,"+",""),u.Parker)), IF(InStr(u.Spectator,"-"),Right(Trim(IF(InStr(u.Spectator,"+"),Replace(u.Spectator,"+",""),u.Spectator)),2),IF(InStr(u.Spectator,"+"),Replace(u.Spectator,"+",""),u.Spectator)), IF(not Instr( IF(InStr(u.Tanzer,"-"),Right(Trim(IF(InStr(u.Tanzer,"+"),Replace(u.Tanzer,"+",""),u.Tanzer)),2),IF(InStr(u.Tanzer,"+"),Replace(u.Tanzer,"+",""),u.Tanzer)),"star"),
//
// IF(InStr(u.Tanzer,"-"),Right(Trim(IF(InStr(u.Tanzer,"+"),Replace(u.Tanzer,"+",""),u.Tanzer)),2),IF(InStr(u.Tanzer,"+"),Replace(u.Tanzer,"+",""),u.Tanzer)),""), 
//
//CONCAT(IF(COALESCE(trim(u.Parker), '') <> '',CONCAT("Robert Parker rating: " , u.Parker , " "),"") , IF(COALESCE(trim(u.Spectator), '') <> '',CONCAT("Wine Spectator rating: " , u.Spectator , " "),"") , IF(COALESCE(trim(u.Tanzer), '') <> '',CONCAT("Tanzer rating: " , u.Tanzer , " "),"")), 
//'1'
//FROM Skurnik_feed AS u LEFT JOIN item_xref AS i ON CONCAT("SKUR-" , Trim(u.`MSW Code`)) = i.xref
//WHERE isNull(i.xref) and `u`.`1cs` > 0 and `u`.`Pack` > 0;
