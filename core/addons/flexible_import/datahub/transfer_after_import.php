<?php
set_time_limit(86400);
error_reporting(E_ERROR);

global $ais_step;

if (!isset($ais_step))
$ais_step = 1;

cw_display_service_header("");
print("<H1>Saratogawine after import setup, running step $ais_step of 8</H1><br>");

if ($ais_step == 1) {
    cw_flush("<h2>Updating In Stock and Availability code fields...</h2>");

    $index_exists = cw_query_first_cell("select count(*) from information_schema.statistics where table_name = 'tmp_load_PRODUCTS' and index_name = 'PRODUCTID' and TABLE_SCHEMA='".$app_config_file['sql']['db']."'");

    if ($index_exists)
        cw_csvxc_logged_query("alter table tmp_load_PRODUCTS drop index PRODUCTID");

    cw_csvxc_logged_query("alter table tmp_load_PRODUCTS add index PRODUCTID (PRODUCTID)");

    cw_flush("<h2>Updating Manufacturers list...</h2>");

    $manufacturer_id_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='manufacturer_id' and addon='manufacturers'");

    cw_csvxc_logged_query("update cw_manufacturers m left join 
    (select value as manufacturer_id, count(*) as tot from cw_attributes_values where attribute_id = $manufacturer_id_attr_id group by value) a 
    on m.manufacturer_id = a.manufacturer_id 
    set avail = if(coalesce(a.tot,0) > 0, 1, 0)");

    $ais_step++;

    cw_header_location("index.php?target=datahub_transfer_after_import&ais_step=$ais_step");
}


if ($ais_step == 2) {

    cw_flush("<h2>Updating multicase bottles price and cost data...</h2>");

    $swe_12bot_price_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='swe_12bot_price' and addon='extension_empowerwine_backorder'");
    $swe_12bot_cost_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='swe_12bot_cost' and addon='extension_empowerwine_backorder'");

/*
    cw_csvxc_logged_query("delete from cw_products_prices where quantity=12 and product_id in (select catalogid from xfer_products_SWE)");

    cw_csvxc_logged_query("insert into cw_products_prices (product_id, variant_id, membership_id, quantity, price, list_price) select p.item_id, 0, 0, 12, p.value, c.value from cw_attributes_values p left join cw_attributes_values c on p.item_id = c.item_id and c.attribute_id = $swe_12bot_cost_attr_id inner join xfer_products_SWE tlp on p.item_id=tlp.catalogid where p.attribute_id = $swe_12bot_price_attr_id and p.value > 0");

    cw_csvxc_logged_query("delete from cw_products_prices where quantity>1 and quantity!=12 and product_id in (select catalogid from xfer_products_SWE)");

    cw_csvxc_logged_query("delete ppt.* from cw_products_prices ppt inner join xfer_products_SWE xps on xps.catalogid=ppt.product_id and ppt.quantity=12 and ppt.price=xps.ctwelvebottleprice and (xps.bot_qty2=12 or xps.bot_qty3=12 or xps.bot_qty4=12 or xps.bot_qty5=12 or xps.bot_qty6=12) inner join pos on pos.`Alternate Lookup`=ppt.product_id and pos.`Custom Price 4`=0");

    for ($i=2; $i<=6; $i++) {
         cw_csvxc_logged_query("INSERT INTO cw_products_prices (product_id, variant_id, membership_id, quantity, price, list_price) SELECT catalogid, 0, 0, bot_qty$i, price_qty$i, cost_qty$i FROM xfer_products_SWE INNER JOIN cw_products ON xfer_products_SWE.catalogid=cw_products.product_id INNER JOIN pos ON pos.`Alternate Lookup`=xfer_products_SWE.catalogid AND pos.`Custom Price 4`=0 WHERE bot_qty$i>0 AND price_qty$i>0");  
    }
*/
cw_log_add("qty_update_issue", array('xfer_count'=>cw_query("select count(*) from xfer_products_SWE"), cw_query("select quantity, count(*) as c  from cw_products_prices where product_id in (select catalogid from xfer_products_SWE) group by quantity order by c desc limit 10"), cw_query("select quantity, count(*) as c  from cw_products_prices where product_id in (select product_id from cw_products where status=1) group by quantity order by c desc limit 10")));

    cw_csvxc_logged_query("delete from cw_products_prices where quantity>1 and product_id in (select catalogid from xfer_products_SWE where hide=0)");

cw_log_add("qty_update_issue", array(cw_query("select quantity, count(*) as c  from cw_products_prices where product_id in (select catalogid from xfer_products_SWE) group by quantity order by c desc limit 10"), cw_query("select quantity, count(*) as c  from cw_products_prices where product_id in (select product_id from cw_products where status=1) group by quantity order by c desc limit 10")));

    cw_csvxc_logged_query("insert into cw_products_prices (product_id, variant_id, membership_id, quantity, price, list_price) select p.item_id, 0, 0, IF(ix.bot_per_case>0,ix.bot_per_case,12), p.value, c.value from cw_attributes_values p left join cw_attributes_values c on p.item_id = c.item_id and c.attribute_id = $swe_12bot_cost_attr_id inner join xfer_products_SWE tlp on p.item_id=tlp.catalogid inner join item_xref ix on tlp.catalogid=ix.item_id and tlp.ccode=ix.xref where p.attribute_id = $swe_12bot_price_attr_id and p.value > 0 group by p.item_id, ix.bot_per_case");

    cw_csvxc_logged_query("delete ppt.* from cw_products_prices ppt inner join xfer_products_SWE xps on xps.catalogid=ppt.product_id and ppt.price=xps.ctwelvebottleprice and (xps.bot_qty2=ppt.quantity or xps.bot_qty3=ppt.quantity or xps.bot_qty4=ppt.quantity or xps.bot_qty5=ppt.quantity or xps.bot_qty6=ppt.quantity) inner join pos on pos.`Alternate Lookup`=ppt.product_id and pos.`Custom Price 4`=0");

    for ($i=2; $i<=6; $i++) {
         cw_csvxc_logged_query("INSERT INTO cw_products_prices (product_id, variant_id, membership_id, quantity, price, list_price) SELECT catalogid, 0, 0, bot_qty$i, price_qty$i, cost_qty$i FROM xfer_products_SWE INNER JOIN cw_products ON xfer_products_SWE.catalogid=cw_products.product_id INNER JOIN pos ON pos.`Alternate Lookup`=xfer_products_SWE.catalogid AND pos.`Custom Price 4`=0 WHERE bot_qty$i>0 AND price_qty$i>0");
    }



    $ais_step++;

    cw_header_location("index.php?target=datahub_transfer_after_import&ais_step=$ais_step");
}

if ($ais_step == 3) {

    cw_flush("<h2>Updated empty attributes values...</h2>");

    cw_csvxc_logged_query("insert into cw_products_system_info_ext (product_id, creation_customer_id, creation_date, modification_customer_id, modification_date, supplier_customer_id) select product_id, 999995, creation_date, modification_customer_id, modification_date, supplier_customer_id from cw_products_system_info where product_id not in (select product_id from cw_products_system_info_ext) and product_id in (select PRODUCTID from tmp_load_PRODUCTS)");

    cw_csvxc_logged_query("update cw_products_system_info, cw_register_fields_values set cw_products_system_info.supplier_customer_id=cw_register_fields_values.customer_id where cw_register_fields_values.field_id=143 and cw_register_fields_values.value = cw_products_system_info.supplier_customer_id and cw_products_system_info.product_id in (select PRODUCTID from tmp_load_PRODUCTS)");

    //SW-404 - merge old and new wildman supplier items
    cw_csvxc_logged_query("update cw_products_system_info set supplier_customer_id=175 WHERE supplier_customer_id=228");

    // merge old and new angel supplier items
    cw_csvxc_logged_query("update cw_products_system_info set supplier_customer_id=178 WHERE supplier_customer_id=115520");

    cw_flush("Updated suppliers links to products <br /><br />");


    cw_csvxc_logged_query("delete from cw_attributes_values where value in (select attribute_value_id from cw_attributes_default where value='' and attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P'))");

    cw_csvxc_logged_query("delete from cw_attributes_default where value='' and attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P')");

    cw_csvxc_logged_query("update cw_attributes_default set orderby = if(cast(value as unsigned) > 0,2020-value,200) where attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P' and field='vintage')");

    $manufacturer_clean_url_attribute = cw_query_first_cell("select attribute_id from cw_attributes where addon='clean_urls' and item_type='M' and field='clean_url'");

    if ($manufacturer_clean_url_attribute) {
        cw_csvxc_logged_query("insert into cw_attributes_values (item_id, attribute_id, value, code, item_type) select manufacturer_id, $manufacturer_clean_url_attribute, replace(replace(replace(manufacturer, ' ', '-'),'`','-'),'\'','-'), 'EN', 'M' from cw_manufacturers where manufacturer != '' and manufacturer_id not in (select item_id from cw_attributes_values where attribute_id = $manufacturer_clean_url_attribute)");
    }

    $ais_step++;

    cw_header_location("index.php?target=datahub_transfer_after_import&ais_step=$ais_step");
}

if ($ais_step == 4) {

    cw_flush("<h2>Creating ".$config['extension_empowerwine']['prod_option_cart_name']." options...</h2>");

    $product_ids = cw_query_column("select product_id from $tables[products] where product_id not in (select distinct(product_id) from $tables[product_options] where field='".$config['extension_empowerwine']['prod_option_cart_name']."')");

//print_r($product_ids);

    $options2add = array(1=>'Ask First', 2=>'No', 3=>'Yes');

    foreach ($product_ids as $product_id) {
        $product_option_id = cw_array2insert('product_options',
            array('product_id' => $product_id,
              'field' => $config['extension_empowerwine']['prod_option_cart_name'],
              'name'  => 'Allow substitute vintage?',
              'avail' => 1,
              'type'  => 'Y'));
        if (!empty($product_option_id)) {
            $added_options = array();
            foreach ($options2add as $orderby => $name) {
                $added_options[] = cw_array2insert('product_options_values', array(
                    'product_option_id' => $product_option_id,
                    'name' => $name,
                    'orderby' => $orderby,
                    'avail' => 1,
                    'price_modifier' => 0.00,
                    'modifier_type' => 0
                ));
            }
        }
        cw_flush("<br> Product $product_id, added option $product_option_id with option values:<br>\n");
        print_r($added_options);print("<br>\n");
    }
    $ais_step++;

    cw_header_location("index.php?target=datahub_transfer_after_import&ais_step=$ais_step");
}

if ($ais_step == 5) {
    cw_flush("<h2>Generating clean urls...</h2>");
    cw_call('cw_clean_url_generate_all');

    $ais_step++;

    cw_header_location("index.php?target=datahub_transfer_after_import&ais_step=$ais_step");
}

if ($ais_step == 6) {

    cw_flush("<h2>Activating history clean urls...</h2>");

    cw_csvxc_logged_query("update cw_attributes_values av set av.value=(select concat(clean_url, '.html') from cw_datahub_xcart_clean_urls where resource_type='P' and resource_id=av.item_id) where av.attribute_id=32 and av.item_type='P' and code='EN' and av.item_id in (select resource_id from cw_datahub_xcart_clean_urls where resource_type='P')");

    cw_flush("<h2>Size converting...</h2>");
    cw_call('sw_product_update_size');
    $ais_step++;

    cw_header_location("index.php?target=datahub_transfer_after_import&ais_step=$ais_step");
}

if ($ais_step == 7) {

    cw_flush("<h2>Building spirits attributes...</h2>");
 
    $spirits_aid = cw_query_first_cell("select attribute_id from cw_attributes where field='spirits'");

    $varietal_aid = cw_query_first_cell("select attribute_id from cw_attributes where field='varietal'");

    $spirits_names = array('Scotch', 'Bourbon');

    $_varietal_spirits_avids = cw_query("select attribute_value_id, value from cw_attributes_default where value in ('".implode("','", $spirits_names)."') and attribute_id='$varietal_aid'");

    $varietal_spirits_avids = array();

    foreach ($_varietal_spirits_avids as $v) {
        $varietal_spirits_avids[$v['attribute_value_id']] = $v['value'];
    }
    //print_r($varietal_spirits_avids);

    $spirits_pids = cw_query("select item_id, value from cw_attributes_values where attribute_id='$varietal_aid' and item_type='P' and value in ('".implode("','", array_keys($varietal_spirits_avids))."')");

    //print_r($spirits_pids);

    foreach ($spirits_pids as $p) {
        $spirit_name = $varietal_spirits_avids[$p['value']]." Whiskey";

        $spirit_value_aid = cw_query_first_cell("select attribute_value_id from cw_attributes_default where value='$spirit_name' and attribute_id='$spirits_aid'");
        if (empty($spirit_value_aid)) {
            $spirit_value_aid = cw_array2insert("attributes_default",
                array("value" => $spirit_name, "attribute_id" => $spirits_aid, "active"=>1, "facet"=>1)
            );
        }
        if (!empty($spirit_value_aid)) {
            cw_csvxc_logged_query("delete from cw_attributes_values where item_type='P' and item_id='$p[item_id]' and attribute_id='$spirits_aid'");
/*
            print_r(array("attributes_values",
                array("item_type"=>'P', "item_id"=>$p['item_id'], "attribute_id"=>$spirits_aid, "value"=>$spirit_value_aid, "code"=>'EN')
            ));
*/
            cw_array2insert("attributes_values", array("item_type"=>'P', "item_id"=>$p['item_id'], "attribute_id"=>$spirits_aid, "value"=>$spirit_value_aid, "code"=>'EN'));
        }

    }

    $ais_step++;

    cw_header_location("index.php?target=datahub_transfer_after_import&ais_step=$ais_step");
}

if ($ais_step == 8) {

    cw_flush("<h2>Bevnetwork images path updating...</h2>");

    cw_csvxc_logged_query("drop table if exists cw_temp_disabled_items");
    cw_csvxc_logged_query("create table cw_temp_disabled_items as select product_id from cw_products where status=1 and product_id not in (select ALU from cw_qbwc_pos_items_buffer)");
    cw_csvxc_logged_query("update cw_products p inner join cw_temp_disabled_items tdi on tdi.product_id=p.product_id set p.status=0");
    cw_csvxc_logged_query("drop table if exists cw_temp_disabled_items");

    cw_csvxc_logged_query("delete from $tables[products_prices] where price < '".$config['extension_empowerwine']['sw_product_search_price_limit']."'");

    cw_csvxc_logged_query("create table cw_products_manual_status_disabled_tmp as select av.item_id from cw_attributes_values av inner join cw_attributes a on a.attribute_id=av.attribute_id and a.field='manual_status' inner join cw_attributes_default ad on ad.attribute_id=a.attribute_id and av.value=ad.attribute_value_id and ad.value_key='no'");
    cw_csvxc_logged_query("update cw_products p, cw_products_manual_status_disabled_tmp msd set p.status=0 where p.product_id=msd.item_id");
    cw_csvxc_logged_query("drop table if exists cw_products_manual_status_disabled_tmp");

    //DOMS (Domaine Select) supplier disable
    //cw_csvxc_logged_query("update cw_products set status=0 where product_id in (select catalogid from xfer_products_SWE where supplierid=31 and cstock<=0)");

    //Angels supplier disable
    cw_csvxc_logged_query("update cw_products set status=0 where product_id in (select catalogid from xfer_products_SWE where supplierid=182 and cstock<=0)");

    cw_csvxc_logged_query("update cw_products_images_det set image_path=replace(image_path,'http://library.bevnetwork', 'https://library.bevnetwork') where image_path like 'http://library.bevnetwork.com%'");
    cw_csvxc_logged_query("update cw_products_images_thumb set image_path=replace(image_path,'http://library.bevnetwork', 'https://library.bevnetwork') where image_path like 'http://library.bevnetwork.com%'");

    cw_csvxc_logged_query("update cw_products set min_amount=0 where status=1 and min_amount>0");

    cw_call('cw_datahub_compare_product_data_snapshot', array());

    cw_call('cw_datahub_history_cost_correction', array());

    cw_call('cw_datahub_live_items_dept_correction', array());

    cw_flush("<h2>Extra cases discounts and magnums prices</h2>");
    cw_call('cw_datahub_add_extra_cases_discounts', array());
    
    // SW-544. Add surcharge to all magnums (1.5 Ltr)
    define('FI_MAGNUM_SURCHARGE', 3.0);
    cw_csvxc_logged_query("update $tables[products_prices] pp inner join $tables[products] p on p.product_id=pp.product_id and p.size>=1.5 set price=price+'".constant('FI_MAGNUM_SURCHARGE')."'");
  
    cw_flush("<h2>Cleaning search caches...</h2>");

    cw_cache_clean('PF_home');
    print("cw_cache_clean('PF_home')<br>");
    cw_cache_clean('PF_search');
    print("cw_cache_clean('PF_search')<br>");
    cw_cache_clean('manufacturers_all');
    print("cw_cache_clean('manufacturers_all')<br>");

    cw_add_top_message("After Import Setup Completed");

    //print("<br><br><br><h3><a target='_blank' href='index.php?target=datahub_transfer_after_import_gen_clean_urls'>Clean URLS generate</a></h3><br><br><br>");
    //cw_header_location("index.php?target=datahub_transfer_after_import_gen_clean_urls");

//    print("<br><br><br><h3><a target='_blank' href='../index.php?target=cleanup'>Run cache cleanup</a></h3><br><br><br>");
    print("<br><br>Cache cleanup starts..<br><iframe src='../index.php?target=cleanup' style='border:0px;margin-left:-8px;height:55px'></iframe><br><br><br>");

    cw_datahub_delay_autoupdate_release_lock();
    print("<h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a></h3><br><br><br>");
}

die;
