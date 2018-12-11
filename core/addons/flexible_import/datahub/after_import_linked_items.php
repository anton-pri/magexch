<?php

set_time_limit(86400);

    cw_flush("<h2>Running rebuild of the linked products data, please wait...</h2> <br>");

    $manufacturer_id_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='manufacturer_id' and addon='manufacturers'");

    cw_csvxc_logged_query("delete from cw_linked_products");

    cw_csvxc_logged_query("drop table if exists cw_attributes_values_man_id");

    cw_csvxc_logged_query("create table cw_attributes_values_man_id like cw_attributes_values");

    cw_csvxc_logged_query("insert into cw_attributes_values_man_id select * from cw_attributes_values where attribute_id=$manufacturer_id_attr_id");

    cw_csvxc_logged_query("alter table cw_attributes_values_man_id change column value value int(11) not null default 0");

    cw_csvxc_logged_query("alter table cw_attributes_values_man_id add index avmi_value (value)");
  
    cw_csvxc_logged_query("delete cw_attributes_values_man_id.* from cw_attributes_values_man_id inner join cw_products on cw_products.product_id=cw_attributes_values_man_id.item_id and cw_products.status!=1");   

    cw_csvxc_logged_query("insert into cw_linked_products (product_id, linked_product_id, orderby, active, link_type) select x.item_id as p1, y.item_id as p2, 0, 'Y', 1 from cw_attributes_values_man_id x inner join cw_attributes_values_man_id y on x.value = y.value and x.item_id <> y.item_id where coalesce(x.value,0) > 0");

    cw_csvxc_logged_query("drop table cw_attributes_values_man_id");

    cw_csvxc_logged_query("replace into cw_products_lng (code, product_id, product, descr, fulldescr) select 'EN', p.product_id, p.product, p.descr, p.fulldescr from cw_products p inner join tmp_load_PRODUCTS on tmp_load_PRODUCTS.PRODUCTID=p.product_id");

    cw_flush("Updated Related Products Links<br />");


print("<h3>Done...<a href='index.php?target=datahub_main_edit'>Return to main edit page</a></h3><br><br><br>");
die;
