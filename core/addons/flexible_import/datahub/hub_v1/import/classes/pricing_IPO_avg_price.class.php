<?php
/**
  //this table will evenutally need store_id
*/
class pricing_IPO_avg_price extends feed {
	public static $table = 'pricing_IPO_avg_price';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() {
		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);		
	}	
	
//Equivalent:  pricing_insert_IPO_avg_price	
/**
 * @todo make this for the other stores
 *
 */
	public static function pricing_insert_IPO_avg_price($store_id) {
	//the first query is using the current cost which isn't what we really want
	
	
//		$sql = "INSERT INTO pricing_IPO_avg_price
//						SELECT x.productid AS productid, avg_items_per_order AS avg_items_per_order, avg_price AS avg_price, avg_line_items AS avg_line_items_per_order
//						FROM (SELECT ip.productid, avg(te.amount) AS avg_items_per_order, avg((SELECT value FROM xcart_extra_field_values WHERE productid = ip.productid AND fieldid = 17)) AS avg_price
//						     FROM (xcart_order_details te INNER JOIN xcart_products ip ON te.productid = ip.productid) 
//						     INNER JOIN xcart_orders AS t ON te.orderid = t.orderid
//						     WHERE DateDiff(FROM_UNIXTIME(t.date),now()) < (select order_days from hub_price_settings where store_id = 1) 
//						     GROUP BY ip.productid) AS x INNER JOIN (SELECT productid, avg(tot_line_items) as avg_line_items 
//						          from (SELECT distinct  ip1.productid, te.orderid, t.tot_line_items
//						                   FROM (xcart_order_details AS te 
//						                               INNER JOIN xcart_products AS ip1 ON te.productid = ip1.productid) 
//						                               INNER JOIN (select t2.orderid as tnum, t2.date, tli.tot_line_items 
//						                                                    from xcart_orders t2 
//						                                                    INNER JOIN (select te2.orderid, count(*) as tot_line_items 
//						                                                                         FROM xcart_order_details as te2  
//						                                                                         INNER JOIN xcart_products AS ip2 ON te2.productid = ip2.productid                                                                          
//						                                                                         group by te2.orderid
//						                                                                         ) tli on t2.orderid = tli.orderid
//						                                                   WHERE DateDiff(FROM_UNIXTIME(t2.date),now()) < (select order_days from hub_price_settings where store_id = 1) 
//						                                                    ) t ON te.orderid = t.tnum) as mine
//						                                            group by productid
//						      ) AS a ON x.productid = a.productid";

		//this is using the cost from the cost history table.
		/**
		 * @todo add store_id's
		 */
	
		$result = self::get_ipo($store_id);
		open_connection();//have to re-open connection
		while ($row = mysql_fetch_array($result)) {
			$sql = "INSERT INTO pricing_IPO_avg_price VALUES('{$row['productid']}', '{$row['avg_items_per_order']}', '{$row['avg_price']}', '{$row['avg_line_items_per_order']}')";
			mysql_query($sql) or sql_error($sql);				
		}
		//changed April 18, 2011
		//this changes the productid's from the x-cart to store ID's
		$sql = "update  pricing_IPO_avg_price as p
						 inner join qs2000_item as q on q.ID = p.ID
						set p.ID = q.binlocation";
		mysql_query($sql) or sql_error($sql);				
	}
	
		/**
		 * @todo add store_id's and maybe hosts
		 */	
	public static function get_ipo($store_id) {
	//need hub_price_settings data in the xcart db so create there
		$sql = "SELECT * FROM hub_price_settings";
		$hub_result = mysql_query($sql) or sql_error($sql);		
		

		$link = open_xcart_connection($store_id);
			
		$sql = "DROP TABLE IF EXISTS hub_price_settings";
		mysql_query($sql, $link) or sql_error($sql);			
		//temporary tables do no have a long enough span so needs to be a regular table
		$sql = "CREATE TABLE  `hub_price_settings` (
					  `store_id` int(11) default '0',
					  `min_qty_avail_code_2` int(11) default '0',
					  `oversize_surcharge` int(11) default '0',
					  `SWE_min_qty_under_cost_threshold` double default '0',
					  `SWE_cost_threshold` double default '0',
					  `SWE_min_order_profit` double default '0',
					  `SWE_min_order_profit_instock` double default '0',
					  `competitor_price_threshold` double default '0',
					  `SWE_min_markup` double default '0',
					  `SWE_max_markup` double default '0',
					  `order_days` int(11) default '0',
					  KEY `store_id` (`store_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		mysql_query($sql, $link) or sql_error($sql);	
		
		while ($row = mysql_fetch_array($hub_result)) {			
			$sql = "INSERT INTO `hub_price_settings` VALUES(
							'{$row['store_id']}',
							'{$row['min_qty_avail_code_2']}',
							'{$row['oversize_surcharge']}',
							'{$row['SWE_min_qty_under_cost_threshold']}',
							'{$row['SWE_cost_threshold']}',
							'{$row['SWE_min_order_profit']}',					
							'{$row['SWE_min_order_profit_instock']}',										
							'{$row['competitor_price_threshold']}',			
							'{$row['SWE_min_markup']}',			
							'{$row['SWE_max_markup']}',		
							'{$row['order_days']}'																						
						)";
			mysql_query($sql, $link) or sql_error($sql);					
		}
//		$sql = "INSERT INTO `hub_price_settings` (`store_id`, `min_qty_avail_code_2`, `oversize_surcharge`, `SWE_min_qty_under_cost_threshold`, `SWE_cost_threshold`, `SWE_min_order_profit`, `SWE_min_order_profit_instock`, `competitor_price_threshold`, `SWE_min_markup`, `SWE_max_markup`, `order_days`) VALUES
//						(1, NULL, 2, 0, 0, 40, 40, 0.05, 0.21, 0.35, 120),
//						(2, NULL, 2, 0, 0, 40, 40, 0.05, 0.21, 0.35, 120)";
//		mysql_query($sql, $link) or sql_error($sql);			
				
		//grab result from xcart
//		$sql = "SELECT x.productid AS productid, avg_items_per_order AS avg_items_per_order, avg_price AS avg_price, avg_line_items AS avg_line_items_per_order
//						FROM (SELECT ip.productid, avg(te.amount) AS avg_items_per_order, avg(ch.cost) AS avg_price
//						     FROM (xcart_order_details te INNER JOIN xcart_products ip ON te.productid = ip.productid) 
//							 INNER JOIN cost_history as ch ON (te.orderid = ch.orderid and te.productid = ch.productid)
//						     INNER JOIN xcart_orders AS t ON te.orderid = t.orderid
//						     WHERE DateDiff(FROM_UNIXTIME(t.date),now()) < (select order_days from hub_price_settings where store_id = 1) 
//						     GROUP BY ip.productid) AS x INNER JOIN (SELECT productid, avg(tot_line_items) as avg_line_items 
//						          from (SELECT distinct  ip1.productid, te.orderid, t.tot_line_items
//						                   FROM (xcart_order_details AS te 
//						                               INNER JOIN xcart_products AS ip1 ON te.productid = ip1.productid) 
//						                               INNER JOIN (select t2.orderid as tnum, t2.date, tli.tot_line_items 
//						                                                    from xcart_orders t2 
//						                                                    INNER JOIN (select te2.orderid, count(*) as tot_line_items 
//						                                                                         FROM xcart_order_details as te2  
//						                                                                         INNER JOIN xcart_products AS ip2 ON te2.productid = ip2.productid                                                                          
//						                                                                         group by te2.orderid
//						                                                                         ) tli on t2.orderid = tli.orderid
//						                                                   WHERE DateDiff(FROM_UNIXTIME(t2.date),now()) < (select order_days from hub_price_settings where store_id = 1) 
//						                                                    ) t ON te.orderid = t.tnum) as mine
//						                                            group by productid
//						      ) AS a ON x.productid = a.productid";
//April 14, 2011, changed   DateDiff(FROM_UNIXTIME(t2.date),now()) to DateDiff(now(), FROM_UNIXTIME(t2.date))
		$sql = "SELECT x.productid AS productid, avg_items_per_order AS avg_items_per_order, avg_price AS avg_price, avg_line_items AS avg_line_items_per_order
						FROM (SELECT ip.productid, avg(te.amount) AS avg_items_per_order, avg(ch.cost) AS avg_price
						     FROM (xcart_order_details te INNER JOIN xcart_products ip ON te.productid = ip.productid) 
							 INNER JOIN cost_history as ch ON (te.orderid = ch.orderid and te.productid = ch.productid)
						     INNER JOIN xcart_orders AS t ON te.orderid = t.orderid
						     WHERE DateDiff(now(), FROM_UNIXTIME(t.date)) < 140 
						     AND t.status = 'C'
						     GROUP BY ip.productid) AS x INNER JOIN (SELECT productid, avg(tot_line_items) as avg_line_items 
						          from (SELECT distinct  ip1.productid, te.orderid, t.tot_line_items
						                   FROM (xcart_order_details AS te 
						                               INNER JOIN xcart_products AS ip1 ON te.productid = ip1.productid) 
						                               INNER JOIN (select t2.orderid as tnum, t2.date, tli.tot_line_items 
						                                                    from xcart_orders t2 
						                                                    INNER JOIN (select te2.orderid, count(*) as tot_line_items 
						                                                                         FROM xcart_order_details as te2  
						                                                                         INNER JOIN xcart_products AS ip2 ON te2.productid = ip2.productid                                                                          
						                                                                         group by te2.orderid
						                                                                         ) tli on t2.orderid = tli.orderid
						                                                   WHERE DateDiff(now(), FROM_UNIXTIME(t2.date)) < (select order_days from hub_price_settings where store_id = 1) 
						                                                   AND t2.status = 'C'
						                                                    ) t ON te.orderid = t.tnum) as mine
						                                            group by productid
						      ) AS a ON x.productid = a.productid";
		
		$result = mysql_query($sql, $link) or sql_error($sql);		
		$sql = "DROP TABLE IF EXISTS hub_price_settings";
		mysql_query($sql, $link) or sql_error($sql);				
		mysql_close($link);		
		return $result;
	}

}//end class