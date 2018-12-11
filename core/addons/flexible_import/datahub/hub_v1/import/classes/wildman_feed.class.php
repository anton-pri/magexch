<?php
/**
		
		
		ALTER TABLE  `wildman_feed` ADD INDEX (  `Product #` );
		ALTER TABLE `wildman_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */

//store 1
class wildman_feed extends feed {
	public static $table = 'wildman_feed';
	public static $feed_file = 'Wildman.txt';
	public static $supplier_id = '180';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	
	
	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  wildman_insert_POS
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
						WHERE (c.store_id = 1 and Left(c.xref,4) = 'WDMN' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
		
	
	public static function table_name() {
		return self::$table;
	}
	
//Equivalent:  wildman_delete_table	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}
//Equivalent:  wildman_delete_empties
	public static function delete_empties() {
		$sql = "DELETE FROM " . self::table_name() . "
						WHERE COALESCE(`Item No`, '') = ''";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  wildman_update_bottles
	public static function update_bottles() {
//		$sql = "UPDATE " . self::table_name() . " SET 
//						`Size` = IF(CAST(ucwords(Right(Trim(`Btls/Size`),Length(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/'))) as SIGNED) > 999,
//						CONCAT(Left(CAST(ucwords(Right(Trim(`Btls/Size`),Length(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/'))) as SIGNED),1) , 'Ltr'),
//						CONCAT(CAST(ucwords(Right(Trim(`Btls/Size`),Length(Trim(`Btls/Size`))-InStr(`Btls/Size`,'/'))) as SIGNED) , 'ml')
//						), `Bots Per Case` = CAST(Left(`Btls/Size`,InStr(`Btls/Size`,'/')-1) as SIGNED), Vintage = IF(COALESCE(`Vintage`, '0') = '0', 'NV', `Vintage`)";
/*
		$sql = "UPDATE " . self::table_name() . "
						SET `Size` =  IF( CAST( RIGHT( TRIM(  `Pack Size` ) , INSTR(  `Pack Size` ,  ' L' ) ) AS SIGNED ) >0,
			IF( INSTR(  `Pack Size` ,  '.' ),Right(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'Ltr')),LENGTH(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'Ltr')))-InStr(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'Ltr'),'-')),
				Right(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'.0Ltr')),LENGTH(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'.0Ltr')))-InStr(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'.0Ltr'),'-')))
				,CONCAT(CAST(Right(Trim(`Pack Size`),LENGTH(Trim(`Pack Size`))-InStr(`Pack Size`,'-')) as SIGNED) , IF( INSTR(  `Pack Size` ,  '.5' ),'.5Ltr',IF( INSTR(  `Pack Size` ,  '1 L' ),'1Ltr','ml'))) 
				) , `Pack` = CAST(Left(`Pack Size`,InStr(`Pack Size`,'-')-1) as SIGNED), Vintage = IF(COALESCE(`Vintage`, '0') = '0', 'NV', `Vintage`)";
*/
        $pack_sep = '/';

        $sql = "UPDATE " . self::table_name() . "
                        SET `Size` =  IF( CAST( RIGHT( TRIM(  `Pack Size` ) , INSTR(  `Pack Size` ,  ' L' ) ) AS SIGNED ) >0,
            IF( INSTR(  `Pack Size` ,  '.' ),Right(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'Ltr')),LENGTH(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'Ltr')))-InStr(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'Ltr'),'$pack_sep')),
                Right(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'.0Ltr')),LENGTH(Trim(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'.0Ltr')))-InStr(CONCAT(Trim(REPLACE(`Pack Size`,'L','')),'.0Ltr'),'$pack_sep')))
                ,CONCAT(CAST(Right(Trim(`Pack Size`),LENGTH(Trim(`Pack Size`))-InStr(`Pack Size`,'$pack_sep')) as SIGNED) , IF( INSTR(  `Pack Size` ,  '.5' ),'.5Ltr',IF( INSTR(  `Pack Size` ,  '1 L' ),'1Ltr','ml'))) 
                ) , `Pack` = CAST(Left(`Pack Size`,InStr(`Pack Size`,'$pack_sep')-1) as SIGNED), Vintage = IF(COALESCE(`Vintage`, '0') = '0', 'NV', `Vintage`)";

		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  wildman_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i 
						SET qty_avail = '0'
						WHERE Left(COALESCE(i.xref,'     '),5)='WDMN-'";
		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  wildman_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE (item_xref AS i INNER JOIN wildman_feed AS f ON i.xref=CONCAT('WDMN-' , f.`Item No`)) LEFT JOIN item AS i2 ON i.item_id = i2.ID SET i.qty_avail = '9999', i.min_price = IF(COALESCE(f.`Bottle Cost`, '') <> '', f.`Bottle Cost`, '0'), i.bot_per_case = CAST(f.`Pack` as SIGNED), 
						i.cost_per_case = CAST(f.`Case One Price` as DECIMAL(10,2)), 
						i.cost_per_bottle = Round(CAST(CAST(f.`Case One Price` as DECIMAL(10,2)) / CAST(f.`Pack` as SIGNED) as DECIMAL(10,2)),2)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_apply_splitcase_to_cost_vision
	public static function hub_apply_splitcase_to_cost_wildman() {
		$sql = "UPDATE item_xref SET split_case_charge = '.50'
						WHERE left(COALESCE(xref,'    '),4) = 'WDMN'";
		mysql_query($sql) or sql_error($sql);			
	}

	//Equivalent:  wildman_insert_compare
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT 'Feed_Wildman' AS Source, ucwords(u.`Description`) AS Producer, ucwords(u.`Description`) AS Wine, ucwords(u.`Description`) AS Name, u.`Vintage`, IF(COALESCE(u.`Size`, '') <> '', u.`Size`,'Other') AS `size`, CONCAT('WDMN-' , CAST(u.`Item No` as CHAR)) AS xref, u.`Pack` AS bottles_per_case, Null AS catalogid, null, null, null, null, null AS `sub-appellation`, Round(CAST(CAST(`Case One Price` as DECIMAL(10,2)) / CAST(`Pack` as SIGNED) as DECIMAL(10,2)),2) AS cost, null, null, null, null, '1'
						, '" . self::get_supplier_id() . "'
						FROM wildman_feed AS u LEFT JOIN item_xref AS i ON CONCAT('WDMN-' , u.`Item No`)=i.xref
						WHERE isNull(i.xref)";
		mysql_query($sql) or sql_error($sql);
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}
	
	public static function remove_char() {
		$sql = "UPDATE " . self::table_name() . "
						SET `Btls/Size` = REPLACE(`Btls/Size`, ',', '')";
		mysql_query($sql) or sql_error($sql);		
	}
	public static function update_item_no() {
		$sql = "UPDATE " . self::$table . "
						SET `Item No` = CONCAT(`Item No` , '-' ,  IF(COALESCE(Vintage, '') <> '', right(Vintage, 2), 'NV'));";
		mysql_query($sql) or sql_error($sql);				
	}	
	public static function clean_money_fields() {
		$sql = "UPDATE " . self::table_name() . "
						SET `Case One Price` = TRIM(REPLACE(REPLACE(`Case One Price`, '$', ''), ',', ''))";	
		mysql_query($sql) or sql_error($sql);		
	}
//Equivalent:  wildman_import_and_update (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::table_name())) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::table_name());
			wildman_feed::update_cases();	
			wildman_feed::update_item_no();
			// self::remove_char();
			self::delete_empties();
			self::update_bottles();
			self::clean_money_fields();
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	
//Equivalent:  wildman_update_cases
	public static function update_cases() {
		$sql = 'UPDATE ' . self::table_name() . '
						SET `Cases Available` = CAST(`Cases Available` as SIGNED);';
		mysql_query($sql) or sql_error($sql);		
	}
	
// public static function update_up_prod_wildman() {
		// $sql = 'UPDATE ' . self::$table . ' AS up 
						// INNER JOIN wildman_feed AS wld 
						// ON Mid(up.xref,InStr(up.xref,"-")+1) = wld.`Item No` SET up.confstock = "Y"
						// WHERE coalesce(wld.`Cases Available`, "0") <> "0";';
		// mysql_query($sql) or sql_error($sql);	
	// }		

//Equivalent:  wildman_update_only (macro)
	public static function update_only() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_wildman();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
	
	
	
	// public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		// $row = 1;
		// $fields = '';	
		// $sanitize_count = count($sanitize);
		// $field_count_insert = '';
		// $field_count = '';		
		// $skip = array();
		// $skip_count = 0;
		// if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    // while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					// $values = '';    
		      //$num = count($data);
					// $sql = "DESCRIBE " . self::table_name();
					// $result = mysql_query($sql) or sql_error($sql);		
					// $num = mysql_num_rows($result);		
					// $num--;//table_id isn't part of the feed file	      

		      // for ($c = 0; $c < $num; $c++) {
						// if(isset($data[$c])) {
							// $data[$c] = trim($data[$c]);		      
						// }	      
						// if($row == 1) {
								// $fields .= "`$data[$c]`,";
						//		$field_count = $num;
						// }  
						// else {
							// if(isset($data[$c])) {
								// if($sanitize_count > 0) {
									// $data[$c] = sanitizer::strip($data[$c], $sanitize);							
								// }
								
								// $data[$c] = mysql_real_escape_string($data[$c]);
								//acme_add_vintage			
								// $values .= "'$data[$c]',";							
							// }
							// else {
								// $values .= "'',";
							// }
							
						// }     
		
					// }     
					//while ($field_count > $field_count_insert) {						
						//	$values .= "'',";
							//$field_count_insert++;
	//				}					 
					// $fields = rtrim($fields, ',');    
					// $values = rtrim($values, ',');  		
		
					// if($row > 1) {
						// $sql = "INSERT INTO " . $table . " ($fields)
										// VALUES ($values)";
		//				echo $sql . '<br>';
						// mysql_query($sql) or sql_error($sql);
					// }
		      // $row++;        
		    // }
		    // fclose($handle);
		// }
		// return $row;	
	// }	
	//Equivalent:  Wildman_Import_Only 	
	public static function import_only() {
	
		wildman_feed::delete_table();
		wildman_feed::transfer_text(self::$feed_file, self::$table);
		wildman_feed::update_cases();	
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
	

}
