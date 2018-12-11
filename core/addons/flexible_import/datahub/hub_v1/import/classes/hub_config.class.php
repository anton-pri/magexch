<?php
/*
need to load this data
INSERT INTO `hub_config` (`display_feed_items_SWE`, `display_feed_items_DWB`, `bottle_size_do_not_display`, `BevA_wholesalers_exclude`, `BevA_wholesalers_always_on`, `BevA_do_not_import`, `store_id`) VALUES
(1, 1, '187ml,3.0Ltr,4.0Ltr,4Ltr,5Ltr,6Ltr,9Ltr,18Ltr,19Ltr', NULL, NULL, 'DOMAIN', 1);
*/
// ALTER TABLE `hub_config` ADD INDEX ( `store_id` )  
class hub_config extends feed {
	public static $table = 'hub_config';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	

}//end class