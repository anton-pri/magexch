<?php
/**
 ALTER TABLE `BWL_feed` ADD PRIMARY KEY ( `ITEM NUMBER` );  
 ALTER TABLE `BWL_feed` CHANGE `RATING` `WINE RATING` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
 *  
 */

//store 2
class BWL_feed extends feed {
	public static $table = 'BWL_feed';
	public static $feed_file = 'BWL_feed.csv';
	public static $supplier_id = '1008';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}			
	
	public static function table_name() {
		return self::$table;
	}
	//Equivalent:  BWL_delete_table
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}

//Equivalent:  BWL_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE Left(Coalesce(i.xref,'     '),4)='BWL-'";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  BWL_update_item_xref
	public static function update_item_xref() {
		$sql = "UPDATE item_xref AS i 
						INNER JOIN BWL_feed AS f ON i.xref=CONCAT('BWL-' , f.`ITEM NUMBER`)
						SET i.qty_avail = IF(Not isnull(f.`ITEM NUMBER`) And Trim(Coalesce(f.`ITEM NUMBER`,' '))<>'' And CAST(Trim(Coalesce(`avl qty`,'0')) as DECIMAL(10,2))>0,CAST(Trim(Coalesce(`avl qty`,'0')) as DECIMAL(10,2)),0), 
						i.min_price = '0', 
						i.bot_per_case = null, 
						i.cost_per_bottle = Round(CAST(Trim(Coalesce(`IS PRICE`,'0')) as DECIMAL(10,2)),2), 
						i.cost_per_case = '0';";
		mysql_query($sql) or sql_error($sql);			
	}
	

//Equivalent:  BWL_import_and_update (macro)
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			BWL_feed::delete_table();
			self::transfer_text(self::$feed_file, self::$table, ',');
			BWL_feed::update_only();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';				
		}
	}
	
//Equivalent:  BWL_update_only (macro)		
	public static function update_only() {
		BWL_feed::set_qty_0();
		BWL_feed::update_item_xref();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}
	
//Equivalent:  BWL_insert_compare		
	public static function insert_compare() {
		$sql = "INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT 'Feed_BWL' AS Source, 
						ucwords(u.Wine) AS Producer, 
						ucwords(u.Wine) AS Wine, 
						ucwords(Wine) AS Name, Trim(u.Year) AS vintage, 
						IF(`Size`='18.0L','18Ltr',IF(`Size`='1.5L','1.5Ltr',IF(`Size`='3.0L','3Ltr',Trim(ucwords(Replace(Replace(IF(Right(`Size`,1) = 'M',CONCAT(`Size` , 'L'),`Size`),'.0L','Ltr'),' ','')))))) AS nsize, 
						CONCAT('BWL-' , Trim(u.`ITEM NUMBER`)) AS xref, 
						Null AS bottles_per_case, Null AS catalogid, Null, Null, Null, ucwords(Trim(u.Appellation)), null AS `sub-appellation`, Round(CAST(Trim(Coalesce(`IS PRICE`,'0')) as DECIMAL(10,2)),2) AS cost, IF(instr(Coalesce(`WINE RATING`,' '),'RP') > 0, Replace(`WINE RATING`,'RP',''),'') AS parker_rating, IF(instr(Coalesce(`WINE RATING`,' '),'WS') > 0, Replace(`WINE RATING`,'WS',''),'') AS Spectator_rating, IF(instr(Coalesce(`WINE RATING`,' '),'ST') > 0, Replace(`WINE RATING`,'ST',''),'') AS Tanzer_rating, null, '2', '" . self::get_supplier_id() . "'
						FROM BWL_feed AS u LEFT JOIN item_xref AS i ON CONCAT('BWL-' , Trim(u.`Item Number`))=i.xref
						WHERE isNull(i.xref) And CAST(Trim(Coalesce(`AVL QTY`,'0')) as DECIMAL(10,2))>0";
				mysql_query($sql) or sql_error($sql);			
				echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';					
	}
	
	

}//end class