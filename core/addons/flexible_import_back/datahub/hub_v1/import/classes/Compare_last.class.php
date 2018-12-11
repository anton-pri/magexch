<?php
/**
ALTER TABLE  `Compare_last` ADD  `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */
class Compare_last extends feed {
	public static $table = 'Compare_last';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  Compare_del_compare_last
	public static function compare_del_compare_last() {
                $sql = "CREATE TABLE IF NOT EXISTS ". self::table_name() ." LIKE feeds_item_compare"; 
                mysql_query($sql) or sql_error($sql); 

		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  Compare_copy_compare_last	
	public static function compare_copy_compare_last() {
                $sql = "CREATE TABLE IF NOT EXISTS ". self::table_name() ." LIKE feeds_item_compare";
                mysql_query($sql) or sql_error($sql);

		$sql = "INSERT INTO " . self::table_name() . "
						SELECT *
						FROM feeds_item_compare";
		mysql_query($sql) or sql_error($sql);			
	}
}//end class
