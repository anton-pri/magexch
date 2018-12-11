<?php

cw_display_service_header("Running monthly price routine");

echo '<h2>Running monthly price update routine</h2><br>';

cw_csvxc_logged_query("DELETE FROM cw_datahub_pricing_IPO_avg_price");

$step1_query = 
"INSERT INTO cw_datahub_pricing_IPO_avg_price
(SELECT
    x.product_id AS product_id,
    avg_items_per_order AS avg_items_per_order,
    avg_price AS avg_price,
    avg_line_items AS avg_line_items_per_order
FROM (
      SELECT ip.product_id,
             avg(te.amount) AS avg_items_per_order,
             avg(te.price) AS avg_price
      FROM (cw_docs_items te INNER JOIN cw_products ip ON te.product_id = ip.product_id)
        INNER JOIN cw_docs AS t ON te.doc_id = t.doc_id
      WHERE DateDiff(now(), FROM_UNIXTIME(t.date)) < 140 AND t.status = 'C'
     GROUP BY ip.product_id
     ) AS x
INNER JOIN (
         SELECT product_id,
                avg(tot_line_items) as avg_line_items
         FROM (
               SELECT distinct  ip1.product_id,
                      te.doc_id,
                      t.tot_line_items
               FROM (cw_docs_items AS te INNER JOIN cw_products AS ip1 ON te.product_id = ip1.product_id)
                 INNER JOIN (
                      select t2.doc_id as tnum,
                             t2.date,
                             tli.tot_line_items
                      from cw_docs t2
                        INNER JOIN (
                                    select te2.doc_id,
                                          count(*) as tot_line_items
                                    FROM cw_docs_items as te2
                                      INNER JOIN cw_products AS ip2 ON te2.product_id = ip2.product_id                                                                                    group by te2.doc_id
                                   ) tli on t2.doc_id = tli.doc_id
                      WHERE DateDiff(now(), FROM_UNIXTIME(t2.date)) < (select order_days from cw_datahub_price_settings where store_id = 1)
                               AND t2.status = 'C'
                      ) t ON te.doc_id = t.tnum
               ) as mine group by product_id
           ) AS a ON x.product_id = a.product_id)";

cw_csvxc_logged_query($step1_query);


$cnt_pricing_IPO_avg_price = cw_query_first_cell("select count(*) from cw_datahub_pricing_IPO_avg_price");
print("<h3>Updated table pricing_IPO_avg_price, new records count: $cnt_pricing_IPO_avg_price</h3><br>");


cw_csvxc_logged_query("DELETE FROM cw_datahub_pricing_aipo");

$step2_query = 
"INSERT INTO cw_datahub_pricing_aipo
SELECT b.ID AS item_id,
       avg(a.aaipo) AS aipo
FROM (
      select i.ID,
             avg(i.cost + COALESCE(i.split_case_charge,0)) as acost
      from cw_datahub_main_data i
      group by i.ID
     ) AS b
LEFT JOIN (
           select acost,
                  avg(aipo) as aaipo
           from (
                select round(avg_price,0) as acost,
                       avg_items_per_order * avg_line_items_per_order as aipo
                from cw_datahub_pricing_IPO_avg_price
                ) as x
           group by acost
           ) AS a ON (b.acost < a.acost*1.3) AND (b.acost > a.acost*.7)
WHERE COALESCE(b.acost,0) > 0
GROUP BY b.ID";

cw_csvxc_logged_query($step2_query);

$cnt_cw_datahub_pricing_aipo = cw_query_first_cell("select count(*) from cw_datahub_pricing_aipo");
print("<h3>Updated table cw_datahub_pricing_aipo, new records count: $cnt_cw_datahub_pricing_aipo</h3><br>");


print("<h3>done...</h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
exit;
