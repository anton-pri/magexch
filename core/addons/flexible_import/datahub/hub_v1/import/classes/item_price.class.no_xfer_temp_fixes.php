<?php
/**
 * for now make the pk store_id + item_id
ALTER TABLE `item_price` ADD INDEX ( `store_id` )  ;
ALTER TABLE `item_price` ADD INDEX ( `item_id` );
 */

class item_price extends feed {
	public static $table = 'item_price';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  pricing_del_item_price
	public static function pricing_del_item_price() {
		$sql = "DELETE FROM " . self::table_name();
 		mysql_query($sql) or sql_error($sql);		
	}
	
//Equivalent:  pricing_set_BWL_max_markup
	public static function pricing_set_BWL_max_markup() {
		$sql = "UPDATE item_price AS p INNER JOIN item_xref AS xf ON p.item_id = xf.item_id 
						SET p.price = round(xf.cost_per_bottle*1.125,0) - .06
						WHERE left(xf.xref,3) = 'BWL' and xf.cost_per_bottle >= 50 and p.store_id = '2'";
 		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  pricing_set_manual_price_SWE	
	public static function pricing_set_manual_price_SWE() {
		$sql = "UPDATE (item_price AS p LEFT JOIN item_store2 AS si ON p.item_id = si.item_id and si.store_id = '1') 
						LEFT JOIN SWE_store_feed AS sf ON sf.sku = si.store_sku 
						SET p.price = IF(COALESCE(sf.manual_price,0) > 0,sf.manual_price,p.price)
						WHERE p.store_id = '1'";
 		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  pricing_round_to_4
	public static function pricing_round_to_4() {
		$sql = "UPDATE item_price AS i 
						SET price = IF(Right(CAST(IF(Right(CAST(price as CHAR),1)<>'4',round(price,1)-0.06,price) as CHAR),2)='04' Or Right(CAST(IF(Right(CAST(price as CHAR),1)<>'4',round(price,1)-0.06,price) as CHAR),2)='14',round(IF(Right(CAST(price as CHAR),1)<>'4',round(price,1)-0.06,price),0)-0.06,IF(Right(CAST(price as CHAR),1)<>'4',round(price,1)-0.06,price))
						WHERE not isnull(i.price)";
 		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function optimize() {
		$sql = 'OPTIMIZE table ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	 				
	}
//Equivalent:  pricing_calc_DWB	
	public static function pricing_calc_DWB() {
//		$sql = "INSERT INTO item_price ( item_id, price, store_id )
//						SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)),bminprice,price), 2
//						FROM (SELECT x.ID, Round(IF(calc_price<bCost*(1+SWE_min_markup),bCost*(1+SWE_min_markup),IF(calc_price>bCost*(1+SWE_max_markup),bCost*(1+SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice
//						FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 2),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
//						 bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE(IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 2),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)),2)+.01)) AS calc_price, bCost, bminprice
//						FROM 
//						hub_price_settings ps 
//						    inner join (select i.ID, Min(COALESCE(sf.Cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0))) as bcost, Max(IF(COALESCE(xf.min_price,0)=0,CAST(Trim(COALESCE(sf.`DWB Min Price`,'0')) as DECIMAL(10,2)),xf.min_price)) as bminprice, Null as bmanual_price, Max(COALESCE(sf.`Avail Qty`,0) + COALESCE(xf.qty_avail,0)) as btqty, Sum(sf.`Avail Qty`) as store_qty, Avg(pa.aipo) as baipo, 2 as store_id
//						                     from (((item i
//						                    left join item_store2 si on (i.ID = si.item_id and si.store_id = 2))
//						                    left join DWB_store_feed sf on (si.store_sku = sf.`Item #`))
//						                    left join item_xref xf on (i.ID = xf.item_id and xf.store_id = 2))
//						                    left join pricing_aipo pa on i.id = pa.item_id
//						                    WHERE COALESCE(sf.Cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0)) > 0 
//						                    AND CAST(COALESCE(sf.`Avail Qty`,0) as SIGNED) + COALESCE(xf.qty_avail,0) > 0
//						                    group by i.ID
//						                    ) b on b.store_id = ps.store_id
//						)  AS x) AS z";
		
		
		
//		$sql = "INSERT INTO item_price ( item_id, price, store_id, cost, xref, store_sku, supplier_id, stock )
//						SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price), 1, bCost, item_xref, store_sku, supplierid, item_price_stock
//						FROM (SELECT id, round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup),bCost * (1 + SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice, bCost, item_xref, store_sku, supplierid, item_price_stock
//						FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
//						 bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE((IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9))),2)+.01)) AS calc_price, bCost, bminprice, item_xref, store_sku, supplierid, item_price_stock		
                db_query("drop table if exists item_xref_applied");
                db_query("create table item_xref_applied like item_xref");
                db_query("insert into item_xref_applied select ix.* from item_xref ix inner join xfer_temp xt on ix.xref=xt.xref");
						
		$sql = "INSERT INTO item_price ( item_id, price, store_id, cost, xref, store_sku, supplier_id, stock )				
						SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price), 2, bCost, item_xref, store_sku, supplierid, item_price_stock
						FROM (SELECT x.ID, Round(IF(calc_price<bCost*(1+SWE_min_markup),bCost*(1+SWE_min_markup),IF(calc_price>bCost*(1+SWE_max_markup),bCost*(1+SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice, bCost, item_xref, store_sku, supplierid, item_price_stock
						FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 2),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
						 bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE(IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 2),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)),2)+.01)) AS calc_price, bCost, bminprice, item_xref, store_sku, supplierid, item_price_stock
						FROM 
						hub_price_settings ps 
						    inner join (select i.ID, Min(COALESCE(sf.Cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0))) as bcost, Max(IF(COALESCE(xf.min_price,0)=0,CAST(Trim(COALESCE(sf.`DWB Min Price`,'0')) as DECIMAL(10,2)),xf.min_price)) as bminprice, Null as bmanual_price, Max(COALESCE(sf.`Avail Qty`,0) + COALESCE(xf.qty_avail,0)) as btqty, Sum(sf.`Avail Qty`) as store_qty, Avg(pa.aipo) as baipo, 2 as store_id, xf.xref as item_xref, si.store_sku as store_sku, xf.supplier_id as supplierid, xf.qty_avail as item_price_stock  
						                     from (((item i
						                    left join item_store2 si on (i.ID = si.item_id and si.store_id = 2))
						                    left join DWB_store_feed sf on (si.store_sku = sf.`Item #`))
						                    left join item_xref_applied xf on (i.ID = xf.item_id and xf.store_id = 2))
						                    left join pricing_aipo pa on i.id = pa.item_id
						                    WHERE COALESCE(sf.Cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0)) > 0 
						                    AND CAST(COALESCE(sf.`Avail Qty`,0) as SIGNED) + COALESCE(xf.qty_avail,0) > 0
						                    group by i.ID
						                    ) b on b.store_id = ps.store_id
						)  AS x) AS z";		
 		mysql_query($sql) or sql_error($sql);	

                db_query("drop table if exists item_xref_applied");	

		self::optimize();
	}
	
//Equivalent:  pricing_calc_SWE	
	public static function pricing_calc_SWE() {
//		$sql = "INSERT INTO item_price ( item_id, price, store_id )
//						SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price), 1
//						FROM (SELECT id, round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup),bCost * (1 + SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice
//						FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
//						 bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE((IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9))),2)+.01)) AS calc_price, bCost, bminprice
//						FROM 
//						hub_price_settings ps 
//						    inner join (select i.ID, Min(COALESCE(sf.cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0))) as bcost, Max(IF(COALESCE(xf.min_price,0)=0,COALESCE(sf.min_price,0),COALESCE(xf.min_price,0))) as bminprice, Max(sf.manual_price) as bmanual_price, Max(COALESCE(sf.qty,0) + COALESCE(xf.qty_avail,0)) as btqty, Sum(sf.qty) as store_qty, Avg(pa.aipo) as baipo, 1 as store_id
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

                // item_xref_applied - single records from item_xref per product (from built previously xfer_temp)
                // 
                db_query("drop table if exists item_xref_applied");
                db_query("create table item_xref_applied like item_xref");
                db_query("insert into item_xref_applied select ix.* from item_xref ix inner join xfer_temp xt on ix.xref=xt.xref");
		
		$sql = "INSERT INTO item_price ( item_id, price, store_id, cost, xref, store_sku, supplier_id, stock )
                            SELECT id, IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price), 1, bCost, item_xref, store_sku, supplierid, item_price_stock
                            FROM (SELECT id, round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup),bCost * (1 + SWE_max_markup),calc_price)),1)-0.06 AS price, bminprice, bCost, item_xref, store_sku, supplierid, item_price_stock
                            FROM (SELECT ID, ps.SWE_min_markup, ps.SWE_max_markup, IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit, IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
                             bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE((IF(bCost <= (select SWE_cost_threshold from hub_price_settings where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9))),2)+.01)) AS calc_price, bCost, bminprice, item_xref, store_sku, supplierid, item_price_stock
                            FROM 
                            hub_price_settings ps 
                                inner join (select i.ID, Min(COALESCE(sf.cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0))) as bcost, Max(IF(COALESCE(xf.min_price,0)=0,COALESCE(sf.min_price,0),COALESCE(xf.min_price,0))) as bminprice, Max(sf.manual_price) as bmanual_price, Max(COALESCE(sf.qty,0) + COALESCE(xf.qty_avail,0)) as btqty, Sum(sf.qty) as store_qty, Avg(pa.aipo) as baipo, 1 as store_id, xf.xref as item_xref, si.store_sku as store_sku, xf.supplier_id as supplierid, xf.qty_avail as item_price_stock  
                                                 from (((item i
                                                left join item_store2 si on (i.ID = si.item_id and si.store_id = 1))
                                                left join SWE_store_feed sf on (si.store_sku = sf.sku and sf.qty > 0))
                                                left join item_xref_applied xf on (i.ID = xf.item_id and xf.store_id = 1))
                                                left join pricing_aipo pa on i.id = pa.item_id
                                                where COALESCE(sf.cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0)) > 0 
                                                AND (COALESCE(sf.qty,0) + COALESCE(xf.qty_avail,0) > 0 OR 1)
                                                group by i.ID
                                                ) b on b.store_id = ps.store_id
                            )  AS x
                            ) AS z";		
		
 		mysql_query($sql) or sql_error($sql);
                db_query("drop table if exists item_xref_applied");	
		self::optimize();	
	}

}//end class
