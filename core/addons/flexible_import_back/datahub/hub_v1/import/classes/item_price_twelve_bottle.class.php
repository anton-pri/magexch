<?php

class item_price_twelve_bottle extends feed {
	public static $table = 'item_price_twelve_bottle';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}

	public static function delete_table() {
		$sql = "DELETE FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);		
	}
	
	
	public static function calc_twelve_bottle_price_DWB() {
		$sql = "INSERT INTO item_price_twelve_bottle ( item_id, price, store_id, cost )
						SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)),bminprice,price), 2, bCost
						FROM (SELECT x.ID, Round(IF(calc_price<bCost*(1+SWE_min_markup),bCost*(1+SWE_min_markup),IF(calc_price>bCost*(1+SWE_max_markup),bCost*(1+SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice, bCost
						FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 2),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
						 bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE(IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 2),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)),2)+.01)) AS calc_price, bCost, bminprice
						FROM 
						hub_price_settings ps 
						    inner join (select i.ID, Min(COALESCE(sf.Cost, xf.cost_per_bottle)) as bcost, Max(IF(COALESCE(xf.min_price,0)=0,CAST(Trim(COALESCE(sf.`DWB Min Price`,'0')) as DECIMAL(10,2)),xf.min_price)) as bminprice, Null as bmanual_price, Max(COALESCE(sf.`Avail Qty`,0) + COALESCE(xf.qty_avail,0)) as btqty, Sum(sf.`Avail Qty`) as store_qty, Avg(pa.aipo) as baipo, 2 as store_id
						                     from (((item i
						                    left join item_store2 si on (i.ID = si.item_id and si.store_id = 2))
						                    left join DWB_store_feed sf on (si.store_sku = sf.`Item #`))
						                    left join item_xref xf on (i.ID = xf.item_id and xf.store_id = 2))
						                    left join pricing_aipo pa on i.id = pa.item_id
						                    WHERE COALESCE(sf.Cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0)) > 0 
						                    AND CAST(COALESCE(sf.`Avail Qty`,0) as SIGNED) + COALESCE(xf.qty_avail,0) > 0
						                    group by i.ID
						                    ) b on b.store_id = ps.store_id
						)  AS x) AS z";
 		mysql_query($sql) or sql_error($sql);							

	}	
	
	public static function calc_twelve_bottle_price_SWE() {
//		$sql = "INSERT INTO item_price_twelve_bottle ( item_id, price, store_id )
//						SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price), 1
//						FROM (SELECT id, round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup),bCost * (1 + SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice
//						FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
//						 bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE((IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9))),2)+.01)) AS calc_price, bCost, bminprice
//						FROM 
//						hub_price_settings ps 
//						    inner join (select i.ID, Min(COALESCE(sf.cost, xf.cost_per_bottle)) as bcost, Max(IF(COALESCE(xf.min_price,0)=0,COALESCE(sf.min_price,0),COALESCE(xf.min_price,0))) as bminprice, Max(sf.manual_price) as bmanual_price, Max(COALESCE(sf.qty,0) + COALESCE(xf.qty_avail,0)) as btqty, Sum(sf.qty) as store_qty, Avg(pa.aipo) as baipo, 1 as store_id
//						                     from (((item i
//						                    left join item_store2 si on (i.ID = si.item_id and si.store_id = 1))
//						                    left join SWE_store_feed sf on (si.store_sku = sf.sku and sf.qty > 0))
//						                    left join item_xref xf on (i.ID = xf.item_id and xf.store_id = 1))
//						                    left join pricing_aipo pa on i.id = pa.item_id
//						                    where COALESCE(sf.cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0)) > 0 
//						                    AND COALESCE(sf.qty,0) + COALESCE(xf.qty_avail,0) > 0
//						                    group by i.ID
//						                    ) b on b.store_id = ps.store_id
//						)  AS x
//						) AS z";
		
		$sql = "INSERT INTO item_price_twelve_bottle ( item_id, price, store_id, cost )
						SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price), 1, bCost
						FROM (SELECT id, round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup),bCost * (1 + SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice, bCost
						FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
						 bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE((IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9))),2)+.01)) AS calc_price, bCost, bminprice
						FROM 
						hub_price_settings ps 
						    inner join (select i.ID, Min(COALESCE(sf.cost, xf.cost_per_bottle)) as bcost, Max(IF(COALESCE(xf.min_price,0)=0,COALESCE(sf.min_price,0),COALESCE(xf.min_price,0))) as bminprice, Max(sf.twelve_bot_manual_price) as bmanual_price, Max(COALESCE(sf.qty,0) + COALESCE(xf.qty_avail,0)) as btqty, Sum(sf.qty) as store_qty, Avg(pa.aipo) as baipo, 1 as store_id
						                     from (((item i
						                    left join item_store2 si on (i.ID = si.item_id and si.store_id = 1))
						                    left join SWE_store_feed sf on (si.store_sku = sf.sku and sf.qty > 0))
						                    left join item_xref xf on (i.ID = xf.item_id and xf.store_id = 1))
						                    left join pricing_aipo pa on i.id = pa.item_id
						                    where COALESCE(sf.cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0)) > 0 
						                    AND COALESCE(sf.qty,0) + COALESCE(xf.qty_avail,0) > 0
						                    group by i.ID
						                    ) b on b.store_id = ps.store_id
						)  AS x
						) AS z";
		
		
 		mysql_query($sql) or sql_error($sql);		
	}
	
	//swe wines with a manual price get no 12 bottle price
	public static function set_for_manual_price() {
//		$sql = "UPDATE (item_price_twelve_bottle AS p 
//						LEFT JOIN item_store2 AS si ON p.item_id = si.item_id and si.store_id = '1') 
//						LEFT JOIN SWE_store_feed AS sf ON sf.sku = si.store_sku 
//						SET p.price = IF(COALESCE(sf.manual_price,0) > 0, 0, p.price)
//						WHERE p.store_id = '1'";
// 		mysql_query($sql) or sql_error($sql);		

		$sql = "UPDATE (item_price_twelve_bottle AS p 
						LEFT JOIN item_store2 AS si ON p.item_id = si.item_id and si.store_id = '1') 
						LEFT JOIN SWE_store_feed AS sf ON sf.sku = si.store_sku 
						SET p.price = IF(COALESCE(sf.twelve_bot_manual_price, 0) > 0, sf.twelve_bot_manual_price, p.price)
						WHERE p.store_id = '1'";
 		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function mark_down($store_id) {
		$sql = "UPDATE item_price_twelve_bottle
						SET price = IF(price <= 0, price,  CAST(price as DECIMAL(10,2)) - (CAST(price as DECIMAL(10,2)) * (CAST((SELECT twelve_bottle_discount FROM hub_price_settings WHERE store_id = '$store_id')  as DECIMAL(10,2)))))";
 		mysql_query($sql) or sql_error($sql);				
	}

	
	public static function build_twelve_bottle_price() {
		 self::delete_table();		
		 self::calc_twelve_bottle_price_SWE(); 
		 //self::calc_twelve_bottle_price_DWB(); 		 
		 self::mark_down(1);
		 //self::mark_down(2);		
		 self::set_for_manual_price();		  
	}

}//end class
