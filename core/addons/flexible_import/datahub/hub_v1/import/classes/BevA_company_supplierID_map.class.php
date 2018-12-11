<?php
/**
 ALTER TABLE `BevA_company_supplierID_map` ADD INDEX ( `pos_supplier_id` ) ;
 */

class BevA_company_supplierID_map extends feed {
	public static $table = 'BevA_company_supplierID_map';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}


}//end class