<?php
/**
 * ALTER TABLE `Cordon_feed` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 *  ALTER TABLE `Cordon_feed` ADD INDEX ( `Item No.` )  ;
 *   ALTER TABLE `Cordon_feed` ADD INDEX ( `Variant` );
 * ALTER TABLE  `Cordon_feed` ADD  `Product Code` VARCHAR( 255 ) NOT NULL ;
		ALTER TABLE  `Cordon_feed` ADD INDEX (  `Product Code` )
		 ALTER TABLE `Cordon_feed` CHANGE `Score` `Scores` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
 * might need this:   ALTER TABLE `Cordon_feed` CHANGE `Item No` `Item No.` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
 */


/**
 * 
   CREATE TABLE IF NOT EXISTS `Cordon_feed` (
  `table_id` int(11) NOT NULL auto_increment,
  `Item No.` varchar(255) default NULL,
  `Description` varchar(255) default NULL,
  `Wholesale Price` varchar(50) default NULL,
  `Brand` varchar(255) default NULL,
  `Variant` varchar(255) default NULL,
  `Vintage` varchar(50) default NULL,
  `Bottle Size` varchar(50) default NULL,
  `Sales Unit of Measure` varchar(255) default NULL,
  `Qty Available` varchar(50) default NULL,
  `Varietal` varchar(255) default NULL,
  `Origin` varchar(255) default NULL,
  `Region` varchar(255) default NULL,
  `Appelation` varchar(255) default NULL,
  `Style` varchar(255) default NULL,
  `Scores` varchar(255) default NULL,
  `Product Code` varchar(255) NOT NULL,
  PRIMARY KEY  (`table_id`),
  KEY `Variant` (`Variant`),
  KEY `Item No.` (`Item No.`),
  KEY `Product Code` (`Product Code`)
) ENGINE=MyISAM;
 */
//store 2
class Cordon_feed extends feed {
	public static $table = 'Cordon_feed';
	public static $feed_file = 'Cordon_feed.txt';
	public static $supplier_id = '1001';	
	public static $rename_fields = array('Item No.' => 'Item No');		
	public static $store_id = '2';	
	
	
	public static function get_store_id() {
		return self::$store_id;
	}		
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}
		
	public static function table_name() {
		return self::$table;
	}
	//Equivalent:  Cordon_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}
	
//Equivalent:  Cordon_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE Left(Coalesce(i.xref,'     '),5)='CORD-'";
		mysql_query($sql) or sql_error($sql);			
	}	
//Equivalent:  Cordon_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN Cordon_feed AS f ON i.xref=CONCAT('CORD-' , f.`Product Code`) 
						SET i.qty_avail = IF(Not isnull(f.`Product Code`) And Trim(Coalesce(f.`Product Code`,' '))<>'' And CAST(Trim(Coalesce(`Qty Available`,'0')) 
						as DECIMAL(10,2))>0,CAST(Trim(Coalesce(`Qty Available`,'0')) as DECIMAL(10,2)),0), 
						i.min_price = '0', 
						i.bot_per_case = null, 
						i.cost_per_bottle = Round(CAST(Trim(Coalesce(`Wholesale Price`,'0'))as DECIMAL(10,2)),2), 
						i.cost_per_case = '0'";
		mysql_query($sql) or sql_error($sql);			
	}
	
	//delete anything without an item code or bottle size
	public static function delete_no_item_code() {
		$sql = "DELETE FROM " . self::$table . " WHERE Coalesce(`Bottle Size`, '') = '' OR Coalesce(`Bottle Size`, 0) = 0";
		mysql_query($sql) or sql_error($sql);		
		
		$sql = "DELETE FROM " . self::$table . " WHERE LEFT(`Product Code`, 1) = '-'";
		mysql_query($sql) or sql_error($sql);				
	}	
	
//	public static function update_product_code() {
//		$sql = "UPDATE " . self::$table . "
//						SET `Product Code` = CONCAT(`Item No` , '-' ,  
//						IF(COALESCE(Vintage, '') <> '' OR COALESCE(Vintage, '') = 'NV', right(Vintage, 4), 'NV'));";
//		mysql_query($sql) or sql_error($sql);				
//	}	

	public static function update_product_code() {
		$sql = "UPDATE " . self::$table . "
						SET `Product Code` = CONCAT(
																				`Item No` , 
																				'-' ,  
																				IF(COALESCE(Vintage, '') <> '', right(Vintage, 2), 'NV'),
																				'-' , 
																				`Bottle Size`
						
															);";
		mysql_query($sql) or sql_error($sql);				
		$sql = 'OPTIMIZE table ' . self::table_name();
		mysql_query($sql) or sql_error($sql);							
	}		
	
	
	
//Equivalent:  Cordon_import_and_update (macro)
	public static function import_and_update() {	
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);		
			self::update_product_code();
			self::delete_no_item_code();		
			self::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';							
		}
	}
//Equivalent:  Cordon_update_only (macro)
	public static function update_only() {	
		Cordon_feed::set_qty_0();
		Cordon_feed::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';					
	}
	
//Equivalent:  Cordon_insert_compare		
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, 
						Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, 
						description, store_id, supplier_id )
						SELECT 'Feed_Cordon' AS Source, ucwords(Trim(Brand)) AS Producer, ucwords(u.Description) AS Wine, ucwords(Trim(Replace(u.Description,u.Brand,''))) AS Name, Trim(u.Vintage) AS vintage, IF(IsNull(u.`Bottle Size`) Or u.`Bottle Size`='0','',IF(u.`Bottle Size`='1500','1.5Ltr',IF(u.`Bottle Size`='3000','3.0Ltr',IF(CAST(u.`Bottle Size` as DECIMAL(10,2)) <1000,CONCAT(u.`Bottle Size` , 'ml'),IF(CAST(u.`Bottle Size` as DECIMAL(10,2))>=1000,CONCAT(CAST(Round(CAST(u.`Bottle Size` as DECIMAL(10,2))/1000,2) as CHAR) , 'Ltr'),'Other'))))) AS nsize, 
						CONCAT('CORD-' , u.`Product Code`) AS xref, 
						Null AS bottles_per_case, Null AS catalogid, Trim(Right(Coalesce(u.Origin,'-   '),Length(Coalesce(Origin,'-   '))-InStr(Coalesce(Origin,'-   '),'-'))) AS Region, Left(Coalesce(u.Origin,'-   '),InStr(Coalesce(u.Origin,'-   '),'-')-1) AS country, u.Varietal, ucwords(Trim(u.Appelation)), u.Region AS `sub-appellation`, Round(CAST(Trim(Coalesce(`Wholesale Price`,'0')) as DECIMAL(10,2)),2) AS cost, null AS parker_rating, null AS Spectator_rating, null AS Tanzer_rating, null, '2', '" . self::get_supplier_id() . "'
						FROM Cordon_feed AS u LEFT JOIN item_xref AS i ON CONCAT('CORD-' , u.`Product Code`)=i.xref
						WHERE isNull(i.xref) And CAST(Trim(Coalesce(`Qty Available`,'0')) as DECIMAL(10,2))>0";	
		mysql_query($sql) or sql_error($sql);			
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
	}
	
	
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
							//@todo need this in master class
							if(!empty(self::$rename_fields) && in_array($data[$c], array_keys(self::$rename_fields))) {
								$data[$c] = self::$rename_fields[$data[$c]];
							}							
							$fields .= "`$data[$c]`,";
							$field_count = $num;
						}  
						else {
							$data[$c] = trim($data[$c]);
							if($sanitize_count > 0) {
								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
							}
							$field_count_insert = $num;
							$data[$c] = mysql_real_escape_string($data[$c]);	
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
	
	

	
}//end class