<?php

print("<h1>Price Recalculation script running</h1><br />");

cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS `cw_datahub_transfer_item_price` (
  `store_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `price` decimal(19,2) DEFAULT '0.00',
  `cost` decimal(19,2) DEFAULT '0.00',
  `xref` varchar(255) DEFAULT NULL,
  `store_sku` varchar(111) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  PRIMARY KEY (`store_id`,`item_id`),
  KEY `store_id` (`store_id`),
  KEY `item_id` (`item_id`),
  KEY `xref` (`xref`),
  KEY `store_sku` (`store_sku`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1");

cw_csvxc_logged_query("delete from cw_datahub_transfer_item_price");

$price_update_query =
"INSERT INTO cw_datahub_transfer_item_price ( item_id, price, store_id, cost, xref, store_sku, supplier_id, stock )
SELECT id,
       IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price),
       1, bCost, item_xref, store_sku, supplierid, item_price_stock
FROM (
      SELECT id,
             round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup),bCost * (1 + SWE_max_markup),calc_price)),1)-0.06 AS price,
             bminprice, bCost, item_xref, store_sku, supplierid, item_price_stock
      FROM (
            SELECT ID,
                   ps.SWE_min_markup, ps.SWE_max_markup,
                   IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit,
                   IF(bCost <= (select SWE_cost_threshold from $tables[datahub_price_settings] where store_id = 1), SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
                   bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE((IF(bCost <= (select SWE_cost_threshold from $tables[datahub_price_settings] where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9))),2)+.01)) AS calc_price,
                   bCost, bminprice, item_xref, store_sku, supplierid, item_price_stock
            FROM
                   $tables[datahub_price_settings] ps
              INNER JOIN (
                          select i.ID,
                                 Min(i.cost + COALESCE(i.split_case_charge,0)) as bcost,
                                 Max(COALESCE(i.min_price,0)) as bminprice,
                                 Max(i.manual_price) as bmanual_price,
                                 Max(0 + COALESCE(i.stock,0)) as btqty,
                                 0 as store_qty,
                                 Avg(pa.aipo) as baipo,
                                 1 as store_id, i.initial_xref as item_xref,
                                 '' as store_sku,
                                 i.supplier_id as supplierid,
                                 i.stock as item_price_stock
                          from $tables[datahub_main_data] i
                               left join cw_datahub_pricing_aipo pa on i.id = pa.item_id
                          where (i.cost + COALESCE(i.split_case_charge,0)) > 0
                                AND COALESCE(i.stock,0) > 0
                          group by i.ID
                          ) b on b.store_id = ps.store_id
           )  AS x
     ) AS z";

cw_csvxc_logged_query($price_update_query);


$new_prices_count = cw_query_first_cell("select count(*) from cw_datahub_transfer_item_price");
print("<h3>Recalculated $new_prices_count items</h3>");

cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS `cw_datahub_transfer_item_price_twelve_bottle` (
  `store_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `price` decimal(19,2) DEFAULT '0.00',
  `cost` decimal(19,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`store_id`,`item_id`),
  KEY `store_id` (`store_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1");

cw_csvxc_logged_query("UPDATE $tables[datahub_main_data] dmd, cw_datahub_transfer_item_price dtip SET dmd.price=dtip.price WHERE dmd.ID=dtip.item_id AND dmd.manual_price=0");
cw_csvxc_logged_query("UPDATE $tables[datahub_main_data] dmd SET dmd.price=dmd.manual_price WHERE dmd.manual_price>0");

cw_csvxc_logged_query("delete from cw_datahub_transfer_item_price_twelve_bottle");

$price_12_update_query = 
"INSERT INTO cw_datahub_transfer_item_price_twelve_bottle ( item_id, price, store_id, cost )
SELECT id,
       IF(CAST(price as DECIMAL(10,2)) < CAST(bminprice as DECIMAL(10,2)), bminprice, price), 1, bCost
FROM (
      SELECT id, 
             round(IF(calc_price < bCost * (1 + SWE_min_markup), bCost * (1 + SWE_min_markup), IF(calc_price > bCost * (1 + SWE_max_markup),bCost * (1 + SWE_max_markup),calc_price)),1)-0.06 AS price,
             bminprice, bCost
      FROM (
            SELECT ID, 
                   ps.SWE_min_markup, ps.SWE_max_markup,                  
                   IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit) as min_order_profit,
                   IF(bCost <= (select SWE_cost_threshold from $tables[datahub_price_settings] where store_id = 1), 
                      SWE_min_qty_under_cost_threshold,COALESCE(baipo,9)) as items_per_order,
                   bCost+(IF(store_qty > 0,ps.SWE_min_order_profit_instock,ps.SWE_min_order_profit)/(COALESCE((IF(bCost <= (select SWE_cost_threshold from $tables[datahub_price_settings] where store_id = 1),SWE_min_qty_under_cost_threshold,COALESCE(baipo,9))),2)+.01)) AS calc_price, 
                   bCost, bminprice
            FROM 
                $tables[datahub_price_settings] ps  
              INNER JOIN (
                          select i.ID, 
                                 Min(COALESCE(i.cost, 0)) as bcost, 
                                 Max(COALESCE(i.min_price,0)) as bminprice, 
                                 Max(i.twelve_bot_manual_price) as bmanual_price,
                                 Max(0 + COALESCE(i.stock,0)) as btqty,
                                 0 as store_qty, 
                                 Avg(pa.aipo) as baipo,
                                 1 as store_id
                          from $tables[datahub_main_data] i
                               left join cw_datahub_pricing_aipo pa on i.id = pa.item_id
                          where (i.cost + COALESCE(i.split_case_charge,0)) > 0
                                AND COALESCE(i.stock,0) > 0
                          group by i.ID
                         ) b on b.store_id = ps.store_id
           )  AS x
     ) AS z";
cw_csvxc_logged_query($price_12_update_query);

cw_csvxc_logged_query("UPDATE $tables[datahub_main_data] dmd, cw_datahub_transfer_item_price_twelve_bottle dtiptb SET dmd.twelve_bot_price=dtiptb.price WHERE dmd.ID=dtiptb.item_id AND dmd.twelve_bot_manual_price=0");
cw_csvxc_logged_query("UPDATE $tables[datahub_main_data] dmd SET dmd.price=dmd.twelve_bot_manual_price WHERE dmd.twelve_bot_manual_price>0");



print("<h3>done...</h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
die;
