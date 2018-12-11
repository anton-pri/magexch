<?php

function datahub_import_transfer_after_import_reduced($dh_update_step, $is_web_mode=false) {
    global $tables, $config, $app_config_file;

    cw_display_service_header("");
    print("<H1>Saratogawine reduced after import setup</H1><br>");

    cw_flush("<h2>Updating In Stock and Availability code fields...</h2>");

    $index_exists = cw_query_first_cell("select count(*) from information_schema.statistics where table_name = 'tmp_load_PRODUCTS' and index_name = 'PRODUCTID' and TABLE_SCHEMA='".$app_config_file['sql']['db']."'");
    if ($index_exists)
        cw_csvxc_logged_query("alter table tmp_load_PRODUCTS drop index PRODUCTID");

    cw_csvxc_logged_query("alter table tmp_load_PRODUCTS add index PRODUCTID (PRODUCTID)");

    cw_flush("<h2>Updating multicase bottles price and cost data...</h2>");

    $swe_12bot_price_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='swe_12bot_price' and addon='extension_empowerwine_backorder'");
    $swe_12bot_cost_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='swe_12bot_cost' and addon='extension_empowerwine_backorder'");

    cw_csvxc_logged_query("delete from cw_products_prices where quantity>1 and product_id in (select catalogid from xfer_products_SWE where hide=0)");

    cw_csvxc_logged_query("insert into cw_products_prices (product_id, variant_id, membership_id, quantity, price, list_price) "
        . "select p.item_id, 0, 0, IF(ix.bot_per_case>0,ix.bot_per_case,12), p.value, c.value "
        . " from cw_attributes_values p "
        . " left join cw_attributes_values c on p.item_id = c.item_id and c.attribute_id = $swe_12bot_cost_attr_id "
        . " inner join xfer_products_SWE tlp on p.item_id=tlp.catalogid "
        . " inner join item_xref ix on tlp.catalogid=ix.item_id and tlp.ccode=ix.xref"
        . "     where p.attribute_id = $swe_12bot_price_attr_id and p.value > 0 "
        . "     group by p.item_id, ix.bot_per_case");

    cw_csvxc_logged_query("delete ppt.* from cw_products_prices ppt inner join xfer_products_SWE xps on xps.catalogid=ppt.product_id and ppt.price=xps.ctwelvebottleprice and (xps.bot_qty2=ppt.quantity or xps.bot_qty3=ppt.quantity or xps.bot_qty4=ppt.quantity or xps.bot_qty5=ppt.quantity or xps.bot_qty6=ppt.quantity) inner join pos on pos.`Alternate Lookup`=ppt.product_id and pos.`Custom Price 4`=0");

    for ($i=2; $i<=6; $i++) {
         cw_csvxc_logged_query("INSERT INTO cw_products_prices (product_id, variant_id, membership_id, quantity, price, list_price) SELECT catalogid, 0, 0, bot_qty$i, price_qty$i, cost_qty$i FROM xfer_products_SWE INNER JOIN cw_products ON xfer_products_SWE.catalogid=cw_products.product_id INNER JOIN pos ON pos.`Alternate Lookup`=xfer_products_SWE.catalogid AND pos.`Custom Price 4`=0 WHERE bot_qty$i>0 AND price_qty$i>0");
    }

    cw_call('cw_datahub_add_extra_cases_discounts', array());

    // SW-544. Add surcharge to all magnums (1.5 Ltr)
    define('FI_MAGNUM_SURCHARGE', 3.0);
    cw_csvxc_logged_query("update $tables[products_prices] pp inner join $tables[products] p on p.product_id=pp.product_id and p.size>=1.5 set price=price+'".constant('FI_MAGNUM_SURCHARGE')."'");
      
    cw_csvxc_logged_query("delete from cw_attributes_values where value in (select attribute_value_id from cw_attributes_default where value='' and attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P'))");

    cw_csvxc_logged_query("delete from cw_attributes_default where value='' and attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P')");


    cw_csvxc_logged_query("drop table if exists cw_temp_disabled_items");
    cw_csvxc_logged_query("create table cw_temp_disabled_items as select product_id from cw_products where status=1 and product_id not in (select ALU from cw_qbwc_pos_items_buffer)");
    cw_csvxc_logged_query("update cw_products p inner join cw_temp_disabled_items tdi on tdi.product_id=p.product_id set p.status=0");
    cw_csvxc_logged_query("drop table if exists cw_temp_disabled_items");

     // Delete any prices lower limit
    cw_csvxc_logged_query("delete from $tables[products_prices] where price < '".$config['extension_empowerwine']['sw_product_search_price_limit']."'");

    cw_csvxc_logged_query("create table cw_products_manual_status_disabled_tmp as select av.item_id from cw_attributes_values av inner join cw_attributes a on a.attribute_id=av.attribute_id and a.field='manual_status' inner join cw_attributes_default ad on ad.attribute_id=a.attribute_id and av.value=ad.attribute_value_id and ad.value_key='no'");
    cw_csvxc_logged_query("update cw_products p, cw_products_manual_status_disabled_tmp msd set p.status=0 where p.product_id=msd.item_id");
    cw_csvxc_logged_query("drop table if exists cw_products_manual_status_disabled_tmp");

    //DOMS (Domaine Select) supplier disable
    //cw_csvxc_logged_query("update cw_products set status=0 where product_id in (select catalogid from xfer_products_SWE where supplierid=31 and cstock<=0)");

    //Angels supplier disable
    cw_csvxc_logged_query("update cw_products set status=0 where product_id in (select catalogid from xfer_products_SWE where supplierid=182 and cstock<=0)");

//    cw_csvxc_logged_query("update cw_products set min_amount=0 where status=1 and min_amount>0");

    cw_call('cw_datahub_compare_product_data_snapshot', array());

    cw_call('cw_datahub_history_cost_correction', array());

    cw_call('cw_datahub_live_items_dept_correction', array());

    //cw_call('cw_datahub_add_extra_cases_discounts', array());

    cw_flush("<h2>Cleaning search caches...</h2>");

    cw_cache_clean('PF_home');
    print("cw_cache_clean('PF_home')<br>");
    cw_cache_clean('PF_search');
    print("cw_cache_clean('PF_search')<br>");
    cw_cache_clean('manufacturers_all');
    print("cw_cache_clean('manufacturers_all')<br>");

    cw_load('files');
    $result = cw_call('cw_cleanup_cache', array());

    if ($is_web_mode) {
        cw_add_top_message("After Import Setup Completed");
        print("<h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a></h3><br><br><br>");
    } else {
        return -1;
    }

}
