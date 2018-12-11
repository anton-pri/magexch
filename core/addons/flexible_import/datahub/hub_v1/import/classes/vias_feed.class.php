<?php
/**
 * this has table_id
run after data has loaded
ALTER TABLE `vias_feed` ADD INDEX ( `Product Code` ) ;
ALTER TABLE `vias_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 *

 */

/*


CREATE TABLE IF NOT EXISTS `vias_feed` (
  `table_id` int(11) NOT NULL auto_increment,
  `ITEM CODE` varchar(50) default NULL,
  `DESCRIPTION` varchar(255) default NULL,
  `Vintage` varchar(50) default NULL,
  `Pack` varchar(50) default NULL,
  `Size` varchar(50) default NULL,
  `Front Line Price` varchar(255) default NULL,
  `Product Code` varchar(50) default NULL,
  PRIMARY KEY  (`table_id`),
  KEY `Product Code` (`Product Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

*/

//store 1
class vias_feed extends feed {
	public static $table = 'vias_feed';
        public static $feed_file = 'Vias_feed.txt';
	public static $supplier_id = '115';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  Vias_share_insert_POS	
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
						WHERE (c.store_id = 1 and Left(c.xref,3) = 'VS-' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}	
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  delete_vias_feed
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}
//Equivalent:  vias_delete_empties
	public static function remove_bad_records() {
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(`Item No`, '') = ''";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  vias_feed_update_vintage
	public static function update_vintage() {
		//select only records that have 4 successive numbers in description
		$sql = "SELECT * from " . self::table_name() . "
						WHERE Description REGEXP '[0-9]{4}';";
		$result = mysql_query($sql) or sql_error($sql);	
		$pattern = '/[0-9]{4}/';	
		$characters = 4; 			
		while ($row = mysql_fetch_array($result)) {
			$description = $row['Description'];
			//strip out 4 digits
			preg_match($pattern, $description, $matches);		
			$count = count($matches);
			if($count > 0) {
				foreach ($matches as $k => $v) {					
					$length = strlen($description); 
					$start = $length - $characters; 
					$last_four = substr($description, $start, $characters); 
					//if it's the last four digits, might be the bottle size so ignore
					if($last_four == $v)	continue;
					//sometimes a description might look like
					//BERTAGNOLLI 1870 RISERVA NV 4 /750
					//it will pick 1870 as the vintage, so let's say vintage has to be greater than 1950
						if($v > 1950) {
							$sql = "UPDATE " . self::table_name() . "
											SET Vintage = '$v'
										  WHERE table_id = '{$row['table_id']}'";			
							mysql_query($sql) or sql_error($sql);								
						}
			
				}			
			}		
		}	
		//otherwise it's NV
		$sql = "UPDATE vias_feed 
						SET Vintage = 'NV'
						WHERE COALESCE(Vintage, '0') = '0'
						OR COALESCE(Vintage, '') = ''";	
		mysql_query($sql) or sql_error($sql);			
	}
	
	//Description typically ends with Pack /Bottle Size
	//so if no '/', delete it as we need pack and bottle size	
	public static function delete_no_slash() {
		$sql = "DELETE  from " . self::table_name() . "
						WHERE Description NOT LIKE '%/%'";
		mysql_query($sql) or sql_error($sql);		
	}
	
	
	public static function update_size_pack() {
		$sql = "SELECT * from " . self::table_name();
		$result = mysql_query($sql) or sql_error($sql);			
		while ($row = mysql_fetch_array($result)) {
			$description = $row['Description'];		
			//grab text that's before the slash
			$suffix = strrchr($description, "/"); 
			$pos = strpos($description, $suffix); 
			$root = substr_replace($description, "", $pos);
			//get rid of extra characters that may cause it not to be a true number
			$suffix = str_replace(array('/', ',', ' '), '', trim($suffix));
			if($suffix > 999) {
				$suffix = $suffix / 1000;
				//1.0Ltr should be 1Ltr etc
				$suffix = str_replace('.0', '', $suffix);
				
				$sql = "UPDATE " . self::table_name() . "
								SET Size = '{$suffix}Ltr'
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);		
			}
			else {
				$sql = "UPDATE " . self::table_name() . "
								SET Size = '{$suffix}ml'
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);		
			}	
			//now update Pack
			
			//with what's before the slash, that will have the pack
			$root = trim($root);
			$temp = explode(' ', $root);	
			//pack should be the last entry
			$idx = count($temp) - 1;
			if($temp[$idx] > 0) {
				$sql = "UPDATE " . self::table_name() . "
								SET Pack = '$temp[$idx]'
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);					
			}
			else {
				$sql = "UPDATE " . self::table_name() . "
								SET Pack = NULL
								WHERE table_id = '{$row['table_id']}'";			
				mysql_query($sql) or sql_error($sql);						
			}
		}	
	}

	public static function special_rules_import() {
                self::clean_size_field();
                self::clean_qty_on_hand();
                self::update_vintage_NV();
// 		self::delete_no_slash();	
//		self::update_size_pack();
//		self::update_vintage();
	}

        public static function clean_size_field() {
                $sql = "UPDATE vias_feed SET `Size` = TRIM(REPLACE(`Size`, ' ', ''))";
                mysql_query($sql) or die($sql);
        }
	public static function clean_qty_on_hand() {
               $sql = "update vias_feed set `Qty On Hand` = CAST(`Qty On Hand` as SIGNED)"; 
               mysql_query($sql) or die($sql);
        }
        public static function update_vintage_NV() {
               $sql = "update vias_feed set `Vintage` = 'NV' where `Vintage`=0";
               mysql_query($sql) or die($sql);
        }
//Equivalent:  vias_feed_update_product_code	
	public static function update_product_code() {
		$sql = "UPDATE vias_feed SET `Item No` = CONCAT(`Item No`, '-',RIGHT(`Vintage`,2))";
		mysql_query($sql) or sql_error($sql);					
                $sql = "UPDATE vias_feed  SET `Product Code` = `Item No` WHERE COALESCE(`Product Code`, '') = ''";
                mysql_query($sql) or sql_error($sql);
	}
	

//Equivalent:  vias_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,\"   \"),3)=\"VS-\"";
		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  vias_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN vias_feed AS f ON i.xref= CONCAT(\"VS-\" , f.`Product Code`) 
						SET i.qty_avail = CAST(f.`Qty On Hand` as SIGNED)*COALESCE(CAST(f.`Pack Size` as SIGNED), 6), 
						i.min_price = '0', 
						i.bot_per_case = f.`Pack Size`, 
						i.cost_per_case = f.`Line`, 
						i.cost_per_bottle = Round(CAST(f.`Line` as DECIMAL(10,2)) / COALESCE(CAST(f.`Pack Size` as SIGNED), 12), 2)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_apply_splitcase_to_cost_vias
	public static function hub_apply_splitcase() {
		$sql = "UPDATE item_xref 
						SET split_case_charge = '0'
						WHERE left(COALESCE(xref,\"   \"),3) = \"VS-\"";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  vias_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT \"Feed_Vias\" AS Source, 
						NULL AS Producer, 
						NULL AS Wine, 
						ucwords(u.`Wine Name`) AS Name, 
						u.Vintage, 
						u.Size AS `size`, 
						CONCAT(\"VS-\" , u.`Product Code`) AS xref, 
						u.`Pack Size` AS bottles_per_case, 
						NULL AS catalogid, 
						NULL, 
						NULL, 
						NULL,
						NULL, 
						NULL AS `sub-appellation`, 
						Round(CAST(u.`Line` as DECIMAL(10,2)) / COALESCE(CAST(u.`Pack Size` as SIGNED), 12),2) AS cost, 						
						NULL, 
						NULL, 
						NULL, 
						NULL, 
						'1',
						'" . self::get_supplier_id() . "'
						FROM vias_feed AS u LEFT JOIN item_xref AS i ON CONCAT(\"VS-\" , u.`Product Code`) = i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}
	
	public static function clean_money_fields() {
		$sql = "UPDATE vias_feed
						SET `Line` = TRIM(REPLACE(REPLACE(`Line`, '$', ''), ',', '.'))";
		mysql_query($sql) or die($sql);		
                $metro_line_fields = array(2,3,4,5,6,10,15,20,28,30,56,100); 
                foreach ($metro_line_fields as $m_id) {
                    $field_name = "Metro $m_id Case";  
                    $sql = "UPDATE vias_feed SET `$field_name` = TRIM(REPLACE(REPLACE(`$field_name`, '$', ''), ',', '.'))";
                    mysql_query($sql) or die($sql);
                }
	}
	
//Equivalent:  vias_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name(), ";", array('.'));
			self::clean_money_fields();
			self::remove_bad_records();
                        self::special_rules_import();
                        self::update_product_code();
 		        self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}		

//Equivalent:  vias_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase();
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

					$num--;//delete for table id since we added that
					$num--;//delete for Product Code since we added that
/*
					$num--;//delete for Vintage since we added that			
					$num--;//delete for Pack since we added that		
					$num--;//delete for Size since we added that														
*/
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
							if($sanitize_count > 0) {
								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
							}						
								$fields .= "`$data[$c]`,";
						}  
						else {
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

	
}	//end class
