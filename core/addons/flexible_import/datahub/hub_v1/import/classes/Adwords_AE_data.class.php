<?php
/**
import note:  file must be saved as a tab delimited text file and named with .txt
the originial format is unicode text and won't work
*/
class Adwords_AE_data extends feed {
	public static $table = 'Adwords_AE_data';
	public static $feed_file = 'adwords_data.txt';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  hub_delete_adwords_PP_keydata_AE	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}

//Equivalent:  AdWords import AE data only (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table);
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
}