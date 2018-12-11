<?php

//must alter the feed file by adding Field13, Field15 Field17, get rid of period in 'Item No.'
//period in 'Item No.' can stay

//store 1
class Touton_feed extends feed {
	public static $table = 'Touton_feed';
	public static $feed_file = 'touton.txt';
	public static $supplier_id = '27';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}		
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  Touton_insert_POS
	public static function insert_pos() {
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
						`Vendor Name`,
						`Custom Field 1`,
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
						c.country,
						'" . mysql_real_escape_string(self::get_supplier_name()) . "',
						c.Producer,
						c.varietal,
						c.bottles_per_case,
						c.xref
						FROM feeds_item_compare AS c LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0) = qi.`Alternate Lookup`
						WHERE (c.store_id = 1 and Left(c.xref,4) = 'TOUT' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}	
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  touton_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}
//Equivalent:  Touton_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,'     '),5)='TOUT-'";
		mysql_query($sql) or sql_error($sql);	
	}

        public static function case_size_sql() {
                $sql = "IF(
                          (((instr(f.`Item Description`,'6PK') > 0 or 
                          instr(f.`Item Description`,'6/') > 0 or 
                          instr(f.`Item Description`,'/6') > 0) and instr(f.`Item Description`,'6PKS') = 0 and instr(f.`Item Description`,'s-6pk') = 0 and instr(f.`Item Description`,'2x6/') = 0 and instr(f.`Item Description`,'2 x 6/') = 0 and instr(f.`Item Description`,'2x 6/') = 0 and instr(f.`Item Description`,'2 x6/') = 0) or 
                           ((instr(f.`Item No.`,'6PK') > 0 or 
                          instr(f.`Item No.`,'6/') > 0 or 
                          instr(f.`Item No.`,'/6') > 0) and instr(f.`Item No.`,'6PKS') = 0 and instr(f.`Item No.`,'s-6pk') = 0 and instr(f.`Item No.`,'2x6/') = 0 and instr(f.`Item No.`,'2 x 6/') = 0 and instr(f.`Item No.`,'2x 6/') = 0 and instr(f.`Item No.`,'2 x6/') = 0)),
                          6,
                          IF(
                            instr(f.`Item Description`,'1/') > 0 or 
                            instr(f.`Item Description`,'/1 ') > 0 or (instr(f.`Item Description`,'/1') > 0 and instr(f.`Item Description`,'/12') = 0 and instr(f.`Item Description`,'/15') = 0 and instr(f.`Item Description`,'/1L') = 0 and instr(f.`Item Description`,'/1.5L') = 0),
                            1,
                            IF(
                              instr(f.`Item Description`,'8/') > 0 or 
                              instr(f.`Item Description`,'/8') > 0,   
                              8,    
                              IF(
                                instr(f.`Item Description`,'15/') > 0 or 
                                instr(f.`Item Description`,'/15') > 0, 
                                15,
                                IF(
                                  instr(f.`Item Description`,'24/') > 0 or 
                                  instr(f.`Item Description`,'/24') > 0, 
                                  24,
                                  IF(
                                    instr(f.`Item Description`,'/48') > 0,
                                    48,
                                    IF(
                                      (instr(f.`Item Description`,'/3 ') > 0 or instr(f.`Item Description`,'3PK') > 0) or 
                                      (instr(f.`Item No.`,'3PK') > 0),
                                      3,   
                                      IF(
                                        instr(f.`Item No.`, 'A-') > 0,
                                        6, 
                                        12      
                                        )
                                      )       
                                    )       
                                  )
                                )
                              )
                            )
                          ) ";
                return $sql;
        }  

//Equivalent:  Touton_update_item_xref	
	public static function update_item_xref() {

//		$sql = "UPDATE (item_xref AS i INNER JOIN " . self::table_name() . " AS f ON i.xref=CONCAT('TOUT-' , f.`Item No.`)) LEFT JOIN item AS i2 ON i.item_id = i2.ID SET i.qty_avail = IF(Not isnull(f.`Item No.`) And Trim(COALESCE(f.`Item No.`,' '))<>''
//						And CAST(Trim(Left(Replace(COALESCE(f.Available,'999 '),'\"',''),Instr(Replace(COALESCE(f.Available,'999 '),'\"',''),' '))) as  SIGNED)>0
//						,CAST(
//						   Trim(
//						     Left(
//						      Replace(
//						        COALESCE(f.Available,'999 '),'\"',''
//						      ),
//						      Instr(Replace(COALESCE(f.Available,'999 '),'\"',''),' ')
//						     )
//						    )
//						  AS SIGNED)* IF(instr(f.`Item Description`,'6PK') > 0 or instr(f.`Item Description`,'6/') > 0 or instr(f.`Item Description`,'/6') > 0,6,12) + CAST(Mid(f.`Available`,Instr(f.`Available`,'`') + 1,Instr(f.`Available`,'`') - Instr(f.`Available`,'`')-1) as SIGNED),0), 
//						
//						i.min_price = '0', 
//						i.bot_per_case = COALESCE(i2.bot_per_case, IF(instr(f.`Item Description`,'6PK') > 0 or instr(f.`Item Description`,'6/') > 0 or instr(f.`Item Description`,'/6') > 0,6,12)), 
//						i.cost_per_case = Round(CAST(Replace(COALESCE(Field13,'0'),'\"','') as DECIMAL(10,2)),2), 
//						i.cost_per_bottle = Round(CAST(Replace(COALESCE(Field13,'0'),'\"','') as DECIMAL(10,2))/ 
//						IF(COALESCE(i2.bot_per_case, 0) > 0,  i2.bot_per_case, IF(instr(f.`Item Description`,'6PK') > 0 or instr(f.`Item Description`,'6/') > 0 or instr(f.`Item Description`,'/6') > 0,6,12)),2)";
//May 16, 2012  get rid of left join to item table.  See above commented out query for details
		$sql = "UPDATE (item_xref AS i INNER JOIN " . self::table_name() . " AS f ON i.xref=CONCAT('TOUT-' , f.`Item No.`)) 						
						SET i.qty_avail = IF(Not isnull(f.`Item No.`) And Trim(COALESCE(f.`Item No.`,' '))<>''
						And CAST(Trim(Left(Replace(COALESCE(f.Available,'999 '),'\"',''),Instr(Replace(COALESCE(f.Available,'999 '),'\"',''),' '))) as  SIGNED)>0
						,CAST(
						   Trim(
						     Left(
						      Replace(
						        COALESCE(f.Available,'999 '),'\"',''
						      ),
						      Instr(Replace(COALESCE(f.Available,'999 '),'\"',''),' ')
						     )
						    )
						AS SIGNED)*". self::case_size_sql() ."
                                                + CAST(Mid(f.`Available`,Instr(f.`Available`,'`') + 1,Instr(f.`Available`,'`') - Instr(f.`Available`,'`')-1) as SIGNED),0), 
						i.min_price = '0', 
						i.bot_per_case = ". self::case_size_sql() .",
						i.cost_per_case = Round(CAST(Replace(COALESCE(Field13,'0'),'\"','') as DECIMAL(10,2)),2), 
						i.cost_per_bottle = Round(CAST(Replace(COALESCE(Field13,'0'),'\"','') as DECIMAL(10,2))/". self::case_size_sql() .",2)";		
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  hub_apply_splitcase_to_cost_touton
	public static function hub_apply_splitcase_to_cost_touton() {
		$sql = "UPDATE item_xref AS i SET split_case_charge = .5
						WHERE left(COALESCE(i.xref,'    '),4) = 'TOUT'";
		mysql_query($sql) or sql_error($sql);				
	}
	
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT \"Feed_Touton\" AS Source, ucwords(`Item Description`) AS Producer, null AS Wine, ucwords(`Item Description`) AS Name, 
						
						IF(
						Left(IF(Mid(COALESCE(f.`Item No.`,\"9999\"),LENGTH(COALESCE(f.`Item No.`,\"9999\"))-2,1)=\"-\",
						CONCAT(Right(COALESCE(f.`Item No.`,\"9999\"),2),\"\") , \"00\"),1)=\"9\",
						
						CONCAT(\"19\" , IF(Mid(COALESCE(f.`Item No.`,\"9999\"),LENGTH(COALESCE(f.`Item No.`,\"9999\"))-2,1)=\"-\",
						Right(COALESCE(f.`Item No.`,\"9999\"),2),\"\")),
						IF(Left(IF(Mid(COALESCE(f.`Item No.`,\"9999\"),LENGTH(COALESCE(f.`Item No.`,\"9999\"))-2,1)=\"-\",
						CONCAT(Right(COALESCE(f.`Item No.`,\"9999\"),2),\"\") , \"99\"),1)=\"0\",
						CONCAT(\"20\" , IF(Mid(COALESCE(f.`Item No.`,\"9999\"),LENGTH(COALESCE(f.`Item No.`,\"9999\"))-2,1)=\"-\",
						Right(COALESCE(f.`Item No.`,\"9999\"),2),\"\")),\"\")
						) AS vintage, 
                                                IF(
                                                  instr(f.`Item No.`,'C') > 0 or
                                                  instr(f.`Item Description`,' 375 `') > 0 or
                                                  instr(f.`Item Description`,'375/') > 0 or 
                                                  instr(f.`Item Description`,'/375') > 0 or 
                                                  instr(f.`Item Description`,'375ml'),
                                                  '375ml',
                                                  IF(
                                                    instr(f.`Item Description`,' 500 ') > 0 or
                                                    instr(f.`Item Description`,'500/') > 0 or 
                                                    instr(f.`Item Description`,'/500') > 0 or 
                                                    instr(f.`Item Description`,'/500ml') > 0 or
                                                    instr(f.`Item Description`,'500ml/'),
                                                    '500ml',
                                                    IF(
                                                      instr(f.`Item Description`,' 1.5 L') > 0 or
                                                      instr(f.`Item Description`,' 1.5L ') > 0 or 
                                                      instr(f.`Item Description`,'1.5L/') > 0 or 
                                                      instr(f.`Item Description`,'/1.5L') > 0,   
                                                      '1.5Ltr',    
                                                      IF(
                                                        instr(f.`Item No.`,'DM') > 0 or
                                                        instr(f.`Item Description`,'3L/') > 0 or 
                                                        instr(f.`Item Description`,'/3L') > 0, 
                                                        '3Ltr',
                                                        IF(
                                                          instr(f.`Item No.`,'J') > 0 or
                                                          instr(f.`Item Description`,'5L/') > 0 or 
                                                          instr(f.`Item Description`,'/5L') > 0, 
                                                          '5Ltr',
                                                          IF (
                                                            instr(f.`Item No.`,'I') > 0 or
                                                            instr(f.`Item Description`,'6L/') > 0 or 
                                                            instr(f.`Item Description`,'/6L') > 0,
                                                            '6Ltr',  
                                                            IF (
                                                              instr(f.`Item Description`,'200ml/') > 0, 
                                                              '200ml', 
                                                              IF ( 
                                                                instr(f.`Item Description`,'1 LITER') > 0,
                                                                '1.0Ltr',
                                                                  IF (
                                                                  instr(f.`Item No.`,'A-') > 0,
                                                                  '1.5Ltr',   
                                                                  '750ml'
                                                                )
                                                              ) 
                                                            )     
                                                          )
                                                        )
                                                      )
                                                    )
                                                  )
                                                ) AS `size`, 
						CONCAT(\"TOUT-\" , Trim(COALESCE(f.`Item No.`,\"9999\"))) AS xref, 
                                                " . self::case_size_sql() . " AS bottles_per_case, 
                                                Null AS catalogid, 
                                                null AS Region, 
                                                null AS country, 
                                                null AS varietal, 
                                                null AS appellation, 
                                                \"\" AS `sub-appellation`, 
						Round(CAST(Replace(COALESCE(Field13,\"0\"),'\"',\"\") as DECIMAL(10,2))/". self::case_size_sql() .",2) AS cost, 
                                                Null, Null, Null, Null, '1', '" . self::get_supplier_id() . "'
						FROM Touton_feed AS f LEFT JOIN item_xref AS i ON CONCAT(\"TOUT-\" , Trim(COALESCE(f.`Item No.`,\"9999\")))=i.xref
						WHERE isNull(i.xref) And CAST(Trim(Left(Replace(COALESCE(Available,\"999 \"),'\"',''),Instr(Replace(COALESCE(Available,\"999 \"),'\"',''),\" \"))) as SIGNED)>0";
		mysql_query($sql) or sql_error($sql);	
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}
	
//Equivalent:  touton_delete_AB
	public static function touton_delete_AB() {
	//delete * from Touton_feed where LCASE(left([Item No], 2)) = 'ab'
		$sql = "DELETE from " . self::$table . " where LCASE(left(`Item No.`, 2)) = 'ab'";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  Touton_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			self::touton_delete_AB();
			self::update_only(); 
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
	
//Equivalent:  Touton_update_only (macro)		
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_touton();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}	
	
	
//Equivalent:  TransferText	
	public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$row = 1;
		$fields = '';	
		$sanitize_count = count($sanitize);
		$field_count_insert = '';
		$field_count = '';		
		$skip = array();
		$skip_count = 0;
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					$values = '';    
		      //$num = count($data);
					$sql = "DESCRIBE " . self::table_name();
					$result = mysql_query($sql) or sql_error($sql);		
					$num = mysql_num_rows($result);		
					$num--;	      
		     
//					if($row == 1) {		 
//						for ($c = $num-1; $c > 0; $c--) {
//							$data[$c] = trim($data[$c]);
//							if(empty($data[$c])) {
//								$skip[] = $c;
//							}
//						}
//						$skip_count = count($skip);
//					}    

		      for ($c = 0; $c < $num; $c++) {
//		      	if(!empty($skip_count)) {
//		      		if (in_array($c, $skip)) {
//		      			continue;
//		      		}
//		      	}
		      	
						if(isset($data[$c])) {
							$data[$c] = trim($data[$c]);		      
						}	      
						if($row == 1) {
								$fields .= "`$data[$c]`,";
								//$field_count = $num;
						}  
						else {
//							if($sanitize_count > 0) {
//								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
//							}
//							//$field_count_insert = $num;
//							$data[$c] = mysql_real_escape_string($data[$c]);
//							//acme_add_vintage			
//							$values .= "'$data[$c]',";

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
//					while ($field_count > $field_count_insert) {						
//							$values .= "'',";
//							$field_count_insert++;
//					}					 
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
