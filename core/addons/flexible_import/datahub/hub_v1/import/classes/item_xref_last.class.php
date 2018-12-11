<?php
/**
 * may have added pk to this table
 *
 */
class item_xref_last extends feed {
	public static $table = 'item_xref_last';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}

//Equivalent:  compare_del_item_xref_last
	public static function compare_del_item_xref_last() {
                $sql = "CREATE TABLE IF NOT EXISTS " . self::table_name() . " LIKE item_xref";
                mysql_query($sql) or sql_error($sql);

		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);				
	}
	
//Equivalent:  compare_insert_item_last	
	public static function compare_insert_item_xref_last() {
                $sql = "CREATE TABLE IF NOT EXISTS " . self::table_name() . " LIKE item_xref";  
                mysql_query($sql) or sql_error($sql);

		$sql = "INSERT INTO " . self::table_name() . "
						SELECT *
						FROM item_xref";
		mysql_query($sql) or sql_error($sql);				
	}	

}//end class
