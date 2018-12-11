<?php
/**
 ALTER TABLE `xfer_temp` ADD INDEX ( `catalogid` );
 */

class xfer_temp extends feed {
	public static $table = 'xfer_temp';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent: hub_del_xfer_temp
	public static function hub_del_xfer_temp() {
		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  hub_insert_xfer_from_feeds_SWE
	public static function hub_insert_xfer_from_feeds_SWE() {
		self::build_xref_item(1);
		self::build_item_cost(1);
		//original query works but slow
//		$sql = "INSERT INTO xfer_temp ( catalogid, cstock, cost, xref )
//						SELECT xref_item.item_id, xref_item.xqty_avail AS cstock, item_cost.mcost, xref_item.sxref
//						FROM (select xf.item_id, xf.cost_per_bottle, min(xf.xref) as sxref, max(xf.qty_avail) as xqty_avail
//						from item_xref xf
//						where store_id = '1'
//						group by xf.item_id, xf.cost_per_bottle) AS xref_item INNER JOIN (select item_id, min(cost_per_bottle) as mcost
//						from item_xref
//						where store_id = '1'
//						group by item_id) AS item_cost ON xref_item.item_id = item_cost.item_id and xref_item.cost_per_bottle = item_cost.mcost";
//		mysql_query($sql) or sql_error($sql);				
		$sql = "INSERT INTO xfer_temp ( catalogid, cstock, cost, xref )
						SELECT xref_item.item_id, xref_item.xqty_avail AS cstock, ROUND(item_cost.mcost,2), xref_item.sxref
						FROM xref_item
						INNER JOIN item_cost
						ON xref_item.item_id = item_cost.item_id and xref_item.cost_per_bottle = item_cost.mcost";
		mysql_query($sql) or sql_error($sql);				
	}


//Equivalent:  hub_insert_xfer_from_feeds_DWB
	public static function hub_insert_xfer_from_feeds_DWB() {
//		$sql = "INSERT INTO xfer_temp ( catalogid, cstock, cost, xref )
//						SELECT xref_item.item_id, xref_item.xqty_avail AS cstock, ROUND(item_cost.mcost,2), xref_item.sxref
//						FROM (select xf.item_id, xf.cost_per_bottle, min(xf.xref) as sxref, max(xf.qty_avail) as xqty_avail
//						from item_xref xf
//						where store_id = '2'
//						group by xf.item_id, xf.cost_per_bottle) AS xref_item INNER JOIN (select item_id, min(cost_per_bottle) as mcost
//						from item_xref
//						where store_id = '2'
//						group by item_id) AS item_cost ON (xref_item.cost_per_bottle = item_cost.mcost) AND (xref_item.item_id = item_cost.item_id)";
//		mysql_query($sql) or sql_error($sql);			


		self::build_xref_item(2);
		self::build_item_cost(2);	
		$sql = "INSERT INTO xfer_temp ( catalogid, cstock, cost, xref )
						SELECT xref_item.item_id, xref_item.xqty_avail AS cstock, ROUND(item_cost.mcost,2), xref_item.sxref
						FROM xref_item
						INNER JOIN item_cost
						ON xref_item.item_id = item_cost.item_id and xref_item.cost_per_bottle = item_cost.mcost";
		mysql_query($sql) or sql_error($sql);			
	}
	
	
	//builds temp table for hub_insert_xfer_from_feeds_x
	public static function build_xref_item($store_id) {
		$sql = "DROP TEMPORARY TABLE IF EXISTS xref_item";
		mysql_query($sql) or sql_error($sql);			
		$sql = "CREATE TEMPORARY TABLE xref_item (
						 `item_id` INT NOT NULL ,
						`cost_per_bottle` DECIMAL( 19, 4 ) NOT NULL ,
						`sxref` VARCHAR( 50 ) NOT NULL ,
						`xqty_avail` INT NOT NULL,
						INDEX ( `item_id`),
						INDEX (`cost_per_bottle`),
						INDEX ( `sxref`)
					) ENGINE = MYISAM COMMENT = 'temp table for hub_insert_xfer_from_feeds_x'";
		mysql_query($sql) or sql_error($sql);	
		//AND xf.cost_per_bottle is not null as compared to the original
		$sql = "INSERT INTO xref_item
						SELECT xf.item_id, xf.cost_per_bottle, min(xf.xref) as sxref, max(xf.qty_avail) as xqty_avail
						FROM item_xref xf
						WHERE store_id = '$store_id' AND xf.cost_per_bottle is not null
						GROUP BY xf.item_id, xf.cost_per_bottle";
		mysql_query($sql) or sql_error($sql);			
	}
	
	//builds temp table for hub_insert_xfer_from_feeds_x	
	public static function build_item_cost($store_id) {	
		$sql = "DROP TEMPORARY TABLE IF EXISTS item_cost";
		mysql_query($sql) or sql_error($sql);				
		$sql = "CREATE TEMPORARY TABLE item_cost (
						 `item_id` INT NOT NULL ,
						`mcost` DECIMAL( 19, 4 ) NOT NULL ,
						INDEX ( `item_id`),
						INDEX (`mcost`)
					) ENGINE = MYISAM COMMENT = 'temp table for hub_insert_xfer_from_feeds_x'";
		mysql_query($sql) or sql_error($sql);		
		//AND cost_per_bottle is not null as compared to the original
		$sql = "INSERT INTO item_cost
						SELECT item_id, min(cost_per_bottle) as mcost
						FROM item_xref
						WHERE store_id = '$store_id' AND cost_per_bottle is not null
						GROUP BY item_id";
		mysql_query($sql) or sql_error($sql);				
		
	}
	

}//end class