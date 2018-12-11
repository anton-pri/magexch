<?php
/**
ALTER TABLE  `pos_cost_temp` ADD PRIMARY KEY (  `ID` );
 */

class pos_cost_temp extends feed {
	public static $table = 'pos_cost_temp';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  pricing_del_pos_cost_temp
	public static function delete_table() {
		$sql = "DELETE  FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  pricing_insert_pos_cost_temp
////changed from si.store_sku = CHAR(q.ID) to q.ID
	public static function pricing_insert_pos_cost_temp() {
		$sql = "INSERT INTO pos_cost_temp ( ID, cost )
						SELECT qs.`Item Number`, xf.bot_cost
						FROM (pos AS qs INNER JOIN item_store2 AS si ON si.store_sku = qs.`Item Number` and si.store_id = 1) 
						INNER JOIN (select x.item_id, min(x.cost_per_bottle + IF(cost_per_bottle > ps.SWE_cost_threshold or COALESCE(i.bot_per_case,x.bot_per_case) > ps.SWE_min_qty_under_cost_threshold,x.split_case_charge,0)) as bot_cost 
						FROM (item_xref x INNER JOIN item i on x.item_id = i.ID) 
						INNER JOIN hub_price_settings ps on x.store_id = ps.store_id
						WHERE x.store_id = 1 group by x.item_id) AS xf ON si.item_id = xf.item_id";
 		mysql_query($sql) or sql_error($sql);					
	}
	


}//end class
