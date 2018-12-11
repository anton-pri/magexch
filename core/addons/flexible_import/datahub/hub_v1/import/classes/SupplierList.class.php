<?php
/**
 * ID was originally
 * INDEX (`ID`) but now PRIMARY KEY  (`ID`),
 */

class SupplierList extends feed {
	public static $table = 'SupplierList';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}

	public static function delete_table() {
		$sql = "DELETE  FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  pos_update_supplier_costs
	public static function pos_update_supplier_costs() {
		$sql = "UPDATE SupplierList AS s 
						INNER JOIN qs2000_item AS i ON (s.SupplierID = i.SupplierID) AND (s.itemid = i.ID) 
						SET s.Cost = i.Cost";
 		mysql_query($sql) or sql_error($sql);			
	}
}//end class