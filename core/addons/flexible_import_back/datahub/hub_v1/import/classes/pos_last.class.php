<?php
/**
for ID, drop the index and define it as a pk
 ALTER TABLE `pos_last` ADD PRIMARY KEY ( `ID` ) ;
  ALTER TABLE `pos_last` ADD INDEX ( `Custom Field 5` ) ;
   ALTER TABLE  `pos_last` ADD INDEX (  `Alternate Lookup` );
ALTER TABLE  `pos_last` ADD INDEX (  `Average Unit Cost` );
ALTER TABLE  `pos_last` ADD INDEX (  `MSRP` );     
 */

class pos_last extends feed {
	public static $table = 'pos_last';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  compare_del_qs2000_item_last
	public static function delete_table() {
                $sql = "CREATE TABLE IF NOT EXISTS ".self::table_name()." LIKE pos";
                mysql_query($sql) or sql_error($sql);

		$sql = "DELETE  FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  compare_insert_qs2000_item_last
	public static function compare_insert_pos_last() {
                $sql = "CREATE TABLE IF NOT EXISTS ".self::table_name()." LIKE pos";
                mysql_query($sql) or sql_error($sql);

		$sql = "INSERT INTO " . self::table_name() . "
						SELECT *
						FROM pos";
 		mysql_query($sql) or sql_error($sql);				
	}
}//end class
