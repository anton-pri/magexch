<?php
/**
for ID, drop the index and define it as a pk
 ALTER TABLE `qs2000_item_last` ADD PRIMARY KEY ( `ID` ) ;
 */

class qs2000_item_last extends feed {
	public static $table = 'qs2000_item_last';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  compare_del_qs2000_item_last
	public static function delete_table() {
		$sql = "DELETE  FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  compare_insert_qs2000_item_last
	public static function compare_insert_qs2000_item_last() {
		$sql = "INSERT INTO " . self::table_name() . "
						SELECT *
						FROM qs2000_item";
 		mysql_query($sql) or sql_error($sql);				
	}
}//end class