<?php
/**
ALTER TABLE `dwb_manual_price` ADD PRIMARY KEY ( `catalogid` );
UPDATE dwb_manual_price AS price INNER JOIN xfer_products_DWB AS xfer ON price.catalogid =  xfer.catalogid SET price.cstock = xfer.cstock, price.mfg = xfer.mfg, price.cvintage = xfer.cvintage, price.name = xfer.name, price.csize = xfer.csize;

*/

//store 2
class dwb_manual_price extends feed {
	public static $table = 'dwb_manual_price';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}

	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	
	}
	
//Equivalent:  update_dwb_price_from_xfer
	public static function  update_dwb_price_from_xfer() {
		$sql = "UPDATE " . self::table_name() . " AS price 
						INNER JOIN xfer_products_DWB AS xfer 
						ON price.catalogid =  xfer.catalogid 
						SET price.cstock = xfer.cstock, 
						price.mfg = xfer.mfg, 
						price.cvintage = xfer.cvintage, 
						price.name = xfer.name,
						price.csize = xfer.csize";
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  dwb_manual_price_insert
	public static function dwb_manual_price_insert() {
		//self::delete_table();//don't think we need to delete this as using insert ignore
		$sql = "INSERT IGNORE INTO dwb_manual_price ( catalogid, cstock, mfg, cvintage, name, csize, manual_price, avail_code, sku )
						SELECT x.catalogid, x.cstock, x.mfg, x.cvintage, x.name, x.csize, dwb.`DWB Min Price`, x.avail_code, x.sku
						FROM xfer_products_DWB AS x LEFT JOIN DWB_store_feed AS dwb ON x.sku =   dwb.`Item #`";
		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  update_dwb_manual_price_from_store_feed	
	public static function update_dwb_manual_price_from_store_feed() {
		$sql = "UPDATE dwb_manual_price AS price 
						INNER JOIN DWB_store_feed AS dwb 
						ON price.sku =   dwb.`Item #` 
						SET price.manual_price = dwb.`DWB Min Price`, 
						price.avail_code = dwb.`DWB Avail Code`";
		mysql_query($sql) or sql_error($sql);				
	}
	
//Equivalent:  dwb_manual_price_delete_stranded	
	public static function dwb_manual_price_delete_stranded() {
		$sql = "DELETE dwb_manual_price.*
						FROM dwb_manual_price LEFT JOIN xfer_products_DWB ON dwb_manual_price.catalogid = xfer_products_DWB.catalogid
						WHERE (((xfer_products_DWB.catalogid) Is Null) AND ((COALESCE(`dwb_manual_price`.`sku`,''))<>''))";
		mysql_query($sql) or sql_error($sql);				
	}
}	//end class