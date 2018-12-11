<?php
//need to load this data
//INSERT INTO `hub_price_settings` () VALUES (1, NULL, 5, 0, 0, 40, 40, .05, .24, .3, 120);
//INSERT INTO `hub_price_settings` () VALUES (2, NULL, 5, 0, 0, 40, 40, .05, .2, .28, 120);
# 2 records
 //ALTER TABLE `hub_price_settings` ADD INDEX ( `store_id` ) ;

class hub_price_settings extends feed {
	public static $table = 'hub_price_settings';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	

}//end class