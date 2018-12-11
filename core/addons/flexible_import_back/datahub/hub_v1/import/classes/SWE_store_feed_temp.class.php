<?php
/**
 ALTER TABLE `SWE_store_feed_temp` ADD PRIMARY KEY ( `sku` ) ;
 */
// don't think is needed anymore since most of the work is done on mysql
//keep for now
class SWE_store_feed_temp extends feed {
	public static $table = 'SWE_store_feed_temp';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}	
	
	public static function remove_char() {
		$sql = "UPDATE " . self::table_name() . "
						SET `manual_price` = REPLACE(`manual_price`, ',', '')";
		mysql_query($sql) or sql_error($sql);		
		$sql = "UPDATE " . self::table_name() . "
						SET `manual_price` = REPLACE(`manual_price`, '$', '')";
		mysql_query($sql) or sql_error($sql);				
		
		$sql = "UPDATE " . self::table_name() . "
						SET `min_price` = REPLACE(`min_price`, ',', '')";
		mysql_query($sql) or sql_error($sql);		
		$sql = "UPDATE " . self::table_name() . "
						SET `min_price` = REPLACE(`min_price`, '$', '')";
		mysql_query($sql) or sql_error($sql);		

		$sql = "UPDATE " . self::table_name() . "
						SET `cost` = REPLACE(`cost`, ',', '')";
		mysql_query($sql) or sql_error($sql);		
		$sql = "UPDATE " . self::table_name() . "
						SET `cost` = REPLACE(`cost`, '$', '')";
		mysql_query($sql) or sql_error($sql);					
	}		
	
	public static function load_SWE_store_feed() {
		$sql = "INSERT INTO SWE_store_feed SELECT * FROM SWE_store_feed_temp";
		mysql_query($sql) or sql_error($sql);
	}		

}//end class	