<?php
/**
ALTER TABLE `item_store2_last` ADD INDEX ( `item_id` );
ALTER TABLE `item_store2_last` ADD INDEX ( `store_id` );
ALTER TABLE `item_store2_last` ADD INDEX ( `store_sku` );
ALTER TABLE `item_store2_last` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
 */

class item_store2_last extends feed {
	public static $table = 'item_store2_last';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	
//Equivalent:  compare_del_item_store2_last
	public static function compare_del_item_store2_last() {
                $sql = "CREATE TABLE IF NOT EXISTS " . self::table_name() . " LIKE item_store2";
                mysql_query($sql) or sql_error($sql);

		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);		
	}	
	
//Equivalent:  compare_insert_item_store2_last
	public static function compare_insert_item_store2_last() {
                $sql = "CREATE TABLE IF NOT EXISTS " . self::table_name() . " LIKE item_store2";
                mysql_query($sql) or sql_error($sql);

		$sql = "INSERT INTO " . self::table_name() . "
						SELECT *
						FROM item_store2";
		mysql_query($sql) or sql_error($sql);				
	}

}//end class
