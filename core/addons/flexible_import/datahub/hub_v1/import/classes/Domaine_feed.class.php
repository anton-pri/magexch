<?php
/**
	ALTER TABLE  `Domaine_feed` DROP INDEX  `ID` , ADD PRIMARY KEY (  `ID` );
	ALTER TABLE  `Domaine_feed` ADD INDEX (  `Sku` );
 */

/**
 * notes on feed file:
 * make sure bottom is cleared of any extra text like 'Total'
 *
 */

class Domaine_feed extends feed {
	public static $table = 'Domaine_feed';
	public static $feed_file = 'Domaine_Inventory.csv';
	public static $ignore_fields = array();	
        public static $supplier_id = '31';	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  Domaine_delete_table	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}	

        public static function get_supplier_id() {
                return self::$supplier_id;
        }

        public static function set_qty_0() {
                $sql = "UPDATE item_xref AS i SET qty_avail = '0' WHERE i.xref LIKE 'DOMS-%'";
                mysql_query($sql) or sql_error($sql);
        }


//Equivalent:  Domaine_update_item_xref
        public static function update_item_xref() {
                $sql = "UPDATE item_xref AS i 
                                                INNER JOIN Domaine_feed AS f ON i.xref=CONCAT(CONCAT('DOMS-' , f.`Product Code`), CONCAT('-',Right(f.Vintage,2))) 
                                                SET i.qty_avail = IF(Not isnull(f.`Product Code`) And Trim(Coalesce(f.`Product Code`,' '))<>'',9999,0), 
                                                i.min_price = '0', 
                                                i.bot_per_case = CAST(COALESCE(f.`Case Pack`,'12') as SIGNED), 
                                                i.cost_per_bottle = CAST(COALESCE(f.`Bottle Price`,0) as DECIMAL(10,2)), 
                                                i.cost_per_case = CAST(COALESCE(f.`Case Price`,0) as DECIMAL(10,2)), 
                                                i.split_case_charge = 0.55";
                mysql_query($sql) or sql_error($sql);
        } 

	public static function remove_char() {
                $sql = "UPDATE " . self::table_name() . "
                                                SET `Vintage` = 'NV' WHERE `Vintage` = '#N/A'";
                mysql_query($sql) or sql_error($sql);
                $sql = "UPDATE " . self::table_name() . "
                                                SET `Case Price` = CAST(REPLACE(TRIM(REPLACE(`Case Price`, ',', '.')),'$','') as DECIMAL(10,2))";
                mysql_query($sql) or sql_error($sql);           

                $sql = "UPDATE " . self::table_name() . "
                                                SET `Bottle Price` = CAST(REPLACE(TRIM(REPLACE(`Bottle Price`, ',', '.')),'$','') as DECIMAL(10,2))";
                mysql_query($sql) or sql_error($sql);

/*
		$sql = "UPDATE " . self::table_name() . "
						SET `Bottle Cost` = REPLACE(`Bottle Cost`, ',', '')";
		mysql_query($sql) or sql_error($sql);		
		$sql = "UPDATE " . self::table_name() . "
						SET `Bottle Cost` = REPLACE(`Bottle Cost`, '$', '')";
                mysql_query($sql) or sql_error($sql);
                $sql = "UPDATE " . self::table_name() . "
                                                SET `Case One Price` = REPLACE(`Case One Price`, ',', '')";
                mysql_query($sql) or sql_error($sql);           
                $sql = "UPDATE " . self::table_name() . "
                                                SET `Case One Price` = REPLACE(`Case One Price`, '$', '')";
		mysql_query($sql) or sql_error($sql);				
*/		
	}	
	
	//delete anything with a price less than $5 inclusive
	public static function delete_min() {
/*		$sql = "DELETE FROM " . self::$table . "
						WHERE CAST(`NY LIST PRICE` as DECIMAL(10,2)) <= 5";
		mysql_query($sql) or sql_error($sql);	*/
	}
	//Equivalent:  Domaine_import_and_update (macro)
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table, ',');

			self::delete_empty_rows();

			self::remove_char();


//                        self::update_product_code();
//                        self::delete_no_item_code();

                        self::update_only();

			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}	

//Equivalent:  Cordon_update_only (macro)
        public static function update_only() {
                self::set_qty_0();
                self::update_item_xref();
                echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';
        }


	//Equivalent:  Domaine_delete_empty_rows
	public static function delete_empty_rows() {
		$sql = "DELETE 
						FROM " . self:: table_name() . "
						WHERE COALESCE(`Product Code`, '') = '' or Length(`Product Code`) < 12";
		mysql_query($sql) or sql_error($sql);		
	}

        public static function insert_compare() {
                $sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, 
                                                Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, 
                                                description, store_id, supplier_id )
                                                SELECT 'Feed_Domaine' AS Source, 
                                                Trim(u.`Producer`) AS Producer, 
                                                Trim(u.`Product Name`) AS Wine, 
                                                Trim(u.`Product Name`) AS Name, 
                                                Trim(u.Vintage) AS vintage, 
                                                IF(IsNull(u.`Size`) Or u.`Size`='0','',
                                                  IF(CAST(u.`Size` as DECIMAL(10,2))>=1,CONCAT(u.`Size`,'Ltr'),
                                                    IF(u.`Size`='0.72','720ml',
                                                      IF(u.`Size`='0.3','300ml',
                                                        IF(u.`Size`='0.375','375ml',
                                                          IF(u.`Size`='0.5','500ml',
                                                            IF(u.`Size`='0.75','750ml','Other')
                                                            )
                                                          )
                                                        )
                                                      )
                                                    )
                                                  ) AS nsize, 
                                                CONCAT(CONCAT('DOMS-' , u.`Product Code`), CONCAT('-',Right(u.Vintage,2))) AS xref, 
                                                CAST(COALESCE(u.`Case Pack`,'12') as SIGNED) AS bottles_per_case, 
                                                Null AS catalogid, 
                                                Null AS Region, 
                                                NULL AS country, 
                                                NULL AS Varietal, 
                                                NULL AS Appelation, 
                                                NULL AS `sub-appellation`, 
                                                Round(CAST(Trim(Coalesce(u.`Bottle Price`,'0')) as DECIMAL(10,2)),2) AS cost, 
                                                null AS parker_rating, null AS Spectator_rating, null AS Tanzer_rating, 
                                                NULL, '1', 
                                                '" . self::get_supplier_id() . "'
                                                FROM Domaine_feed AS u LEFT JOIN item_xref AS i 
                                                ON i.xref=CONCAT(CONCAT('DOMS-' , u.`Product Code`), CONCAT('-',Right(u.Vintage,2)))
                                                WHERE isNull(i.xref)";
//print("$sql<br/>");
                mysql_query($sql) or sql_error($sql);
                echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';
        }

        public static function get_supplier_name() {
                $sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
                $result = mysql_query($sql) or sql_error($sql);
                $row = mysql_fetch_array($result);
                return $row['SupplierName'];
        }

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
                                                WHERE (c.store_id = 1 and Left(c.xref,4) = 'DOMS' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
                mysql_query($sql) or sql_error($sql);
                pos::binlocation_to_varchar();
        }



//Equivalent:  TransferText
        public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
                $row = 1;
                $fields = '';
                $sanitize_count = count($sanitize);
                $field_count_insert = '';
                $field_count = '';
                if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                                        $values = '';
                      $num = count($data);
        
                      for ($c = 0; $c < $num; $c++) {
                                                $data[$c] = trim($data[$c]);

                                                if($row == 1) {
                                                        $data[$c] = trim($data[$c]);
                                                        $fields .= "`$data[$c]`,";
                                                        $field_count = $num;
                                                }
                                                else {

                                                    if ($c == 1 || $c == 2) {   
                                                        $data[$c] = ucwords(strtolower($data[$c]));
                                                    }
                                                        $data[$c] = trim($data[$c]);
                                                        if($sanitize_count > 0) {
                                                                $data[$c] = sanitizer::strip($data[$c], $sanitize);
                
                                                        }
                                                        $field_count_insert = $num;
                                                        $data[$c] = mysql_real_escape_string($data[$c]);
                                                        //acme_add_vintage
                                                        $values .= "'$data[$c]',";
                                                }
                
                                        }
                                        while ($field_count > $field_count_insert) {
                                                        $values .= "'',";
                                                        $field_count_insert++;
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

}
