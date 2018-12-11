<?php
/**
	#need this as to speed up queries
	ALTER TABLE  `splitcase_charges` ADD INDEX (  `company` );
 */


class splitcase_charges extends feed {
	public static $table = 'splitcase_charges';
	public static $feed_file = '';
	public static $ignore_fields = array();	
	
	public static function table_name() {
		return self::$table;
	}
	
}