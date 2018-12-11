<?php
/**
ALTER TABLE `block_xref_from_compare` CHANGE `xref` `xref` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `block_xref_from_compare` ADD INDEX ( `xref` );
ALTER TABLE `block_xref_from_compare` ADD INDEX ( `feed` );
 */

class block_xref_from_compare extends feed {
	public static $table = 'block_xref_from_compare';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  Compare_add_to_block_list
	public static function compare_add_to_block_list() {
		$sql = "INSERT INTO " . self::table_name() . " ( xref, feed ) 
						SELECT xref, source
						FROM feeds_item_compare
						WHERE block = '1'";
 		mysql_query($sql) or sql_error($sql);		
	}

}//end class