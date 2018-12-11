<?php

cw_display_service_header("lbl_saratogawine_after_import_setup");
print("<H1>Saratogawine after import setup</H1><br>");

    $index_exists = cw_query_first_cell("select count(*) from information_schema.statistics where table_name = 'tmp_load_PRODUCTS' and index_name = 'PRODUCTID'");
    if ($index_exists)
        cw_csvxc_logged_query("alter table tmp_load_PRODUCTS drop index PRODUCTID");

    cw_csvxc_logged_query("alter table tmp_load_PRODUCTS add index PRODUCTID (PRODUCTID)");

    cw_flush("Updated In Stock and Availability code fields <br /><br />");

    $manufacturer_id_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='manufacturer_id' and addon='manufacturers'");

    cw_csvxc_logged_query("update cw_manufacturers m left join 
    (select value as manufacturer_id, count(*) as tot from cw_attributes_values where attribute_id = $manufacturer_id_attr_id group by value) a 
    on m.manufacturer_id = a.manufacturer_id 
    set avail = if(coalesce(a.tot,0) > 0, 1, 0)");
    cw_flush("Manufacturers list updated <br/><br />");


    $swe_12bot_price_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='swe_12bot_price' and addon='custom_saratogawine_backorder'");
    $swe_12bot_cost_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='swe_12bot_cost' and addon='custom_saratogawine_backorder'");

    cw_csvxc_logged_query("delete from cw_products_prices where quantity=12 and product_id in (select PRODUCTID from tmp_load_PRODUCTS)");

    cw_csvxc_logged_query("insert into cw_products_prices (product_id, variant_id, membership_id, quantity, price, list_price) select p.item_id, 0, 0, 12, p.value, c.value from cw_attributes_values p left join cw_attributes_values c on p.item_id = c.item_id and c.attribute_id = $swe_12bot_cost_attr_id inner join tmp_load_PRODUCTS tlp on p.item_id=tlp.PRODUCTID where p.attribute_id = $swe_12bot_price_attr_id and p.value > 0");

//    cw_csvxc_logged_query("delete from cw_attributes_values where attribute_id in ($swe_12bot_price_attr_id, $swe_12bot_cost_attr_id)");

    cw_flush("Updated 12 bottles price and cost data <br /><br />");

    cw_csvxc_logged_query("insert into cw_products_system_info_ext (product_id, creation_customer_id, creation_date, modification_customer_id, modification_date, supplier_customer_id) select product_id, 999995, creation_date, modification_customer_id, modification_date, supplier_customer_id from cw_products_system_info where product_id not in (select product_id from cw_products_system_info_ext) and product_id in (select PRODUCTID from tmp_load_PRODUCTS)");

    cw_csvxc_logged_query("update cw_products_system_info, cw_register_fields_values set cw_products_system_info.supplier_customer_id=cw_register_fields_values.customer_id where cw_register_fields_values.field_id=143 and cw_register_fields_values.value = cw_products_system_info.supplier_customer_id and cw_products_system_info.product_id in (select PRODUCTID from tmp_load_PRODUCTS)");
    cw_flush("Updated suppliers links to products <br /><br />");


    cw_csvxc_logged_query("delete from cw_attributes_values where value in (select attribute_value_id from cw_attributes_default where value='' and attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P'))");

    cw_csvxc_logged_query("delete from cw_attributes_default where value='' and attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P')");

    cw_csvxc_logged_query("update cw_attributes_default set orderby = if(cast(value as unsigned) > 0,2020-value,200) where attribute_id in (select attribute_id from cw_attributes where addon='' and item_type='P' and field='vintage')");

    $manufacturer_clean_url_attribute = cw_query_first_cell("select attribute_id from cw_attributes where addon='clean_urls' and item_type='M' and field='clean_url'");

    if ($manufacturer_clean_url_attribute) {
        cw_csvxc_logged_query("insert into cw_attributes_values (item_id, attribute_id, value, code, item_type) select manufacturer_id, $manufacturer_clean_url_attribute, replace(replace(replace(manufacturer, ' ', '-'),'`','-'),'\'','-'), 'EN', 'M' from cw_manufacturers where manufacturer != '' and manufacturer_id not in (select item_id from cw_attributes_values where attribute_id = $manufacturer_clean_url_attribute)");
    }


    cw_flush("Running rebuild of the linked products data, please wait... <br>");

    $manufacturer_id_attr_id = cw_query_first_cell("select attribute_id from cw_attributes where field='manufacturer_id' and addon='manufacturers'");

    cw_csvxc_logged_query("delete from cw_linked_products");

    cw_csvxc_logged_query("drop table if exists cw_attributes_values_man_id");

    cw_csvxc_logged_query("create table cw_attributes_values_man_id like cw_attributes_values");

    cw_csvxc_logged_query("insert into cw_attributes_values_man_id select * from cw_attributes_values where attribute_id=$manufacturer_id_attr_id");

    cw_csvxc_logged_query("alter table cw_attributes_values_man_id change column value value int(11) not null default 0");

    cw_csvxc_logged_query("alter table cw_attributes_values_man_id add index avmi_value (value)");

    cw_csvxc_logged_query("insert into cw_linked_products (product_id, linked_product_id, orderby, active, link_type) select x.item_id as p1, y.item_id as p2, 0, 'Y', 1 from cw_attributes_values_man_id x inner join cw_attributes_values_man_id y on x.value = y.value and x.item_id <> y.item_id where coalesce(x.value,0) > 0");

    cw_csvxc_logged_query("drop table cw_attributes_values_man_id");

    cw_csvxc_logged_query("replace into cw_products_lng (code, product_id, product, descr, fulldescr) select 'EN', p.product_id, p.product, p.descr, p.fulldescr from cw_products p inner join tmp_load_PRODUCTS on tmp_load_PRODUCTS.PRODUCTID=p.product_id");

    cw_flush("Updated Related Products Links<br />");



cw_flush("Creating ".$config['custom_saratogawine']['prod_option_cart_name']." options");

$product_ids = cw_query_column("select product_id from $tables[products] where product_id not in (select distinct(product_id) from $tables[product_options] where field='".$config['custom_saratogawine']['prod_option_cart_name']."')");

//print_r($product_ids);

$options2add = array(1=>'Ask First', 2=>'No', 3=>'Yes');

foreach ($product_ids as $product_id) {
    $product_option_id = cw_array2insert('product_options',
        array('product_id' => $product_id,
              'field' => $config['custom_saratogawine']['prod_option_cart_name'],
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

cw_flush("<h2>Generating clean urls...</h2>");
cw_call('cw_clean_url_generate_all');

cw_csvxc_logged_query("update cw_attributes_values av set av.value=(select concat(clean_url, '.html') from cw_datahub_xcart_clean_urls where resource_type='P' and resource_id=av.item_id) where av.attribute_id=32 and av.item_type='P' and code='EN' and av.item_id in (select resource_id from cw_datahub_xcart_clean_urls where resource_type='P')");

cw_flush("<h2>Size converting...</h2>");
cw_call('sw_product_update_size');

$spirits_aid = cw_query_first_cell("select attribute_id from cw_attributes where field='spirits'");

$varietal_aid = cw_query_first_cell("select attribute_id from cw_attributes where field='varietal'");

$spirits_names = array('Scotch', 'Bourbon');

$_varietal_spirits_avids = cw_query("select attribute_value_id, value from cw_attributes_default where value in ('".implode("','", $spirits_names)."') and attribute_id='$varietal_aid'");

$varietal_spirits_avids = array();

foreach ($_varietal_spirits_avids as $v) {
    $varietal_spirits_avids[$v['attribute_value_id']] = $v['value'];
}
print_r($varietal_spirits_avids);

$spirits_pids = cw_query("select item_id, value from cw_attributes_values where attribute_id='$varietal_aid' and item_type='P' and value in ('".implode("','", array_keys($varietal_spirits_avids))."')");

print_r($spirits_pids);

foreach ($spirits_pids as $p) {
    $spirit_name = $varietal_spirits_avids[$p['value']]." Whiskey";

    $spirit_value_aid = cw_query_first_cell("select attribute_value_id from cw_attributes_default where value='$spirit_name' and attribute_id='$spirits_aid'");
    if (empty($spirit_value_aid)) {
        $spirit_value_aid = cw_array2insert("attributes_default",
            array("value" => $spirit_name, "attribute_id" => $spirits_aid, "active"=>1, "facet"=>1)
        );
    }
    if (!empty($spirit_value_aid)) {
        db_query("delete from cw_attributes_values where item_type='P' and item_id='$p[item_id]' and attribute_id='$spirits_aid'");
        print_r(array("attributes_values",
            array("item_type"=>'P', "item_id"=>$p['item_id'], "attribute_id"=>$spirits_aid, "value"=>$spirit_value_aid, "code"=>'EN')
        ));
        cw_array2insert("attributes_values", array("item_type"=>'P', "item_id"=>$p['item_id'], "attribute_id"=>$spirits_aid, "value"=>$spirit_value_aid, "code"=>'EN'));
    }

}

cw_add_top_message("After Import Setup Completed");

    cw_cache_clean('PF_home');
    print("cw_cache_clean('PF_home')<br>");
    cw_cache_clean('PF_search');
    print("cw_cache_clean('PF_search')<br>");
    cw_cache_clean('manufacturers_all');
    print("cw_cache_clean('manufacturers_all')<br>");


print("<h3>Done...<a href='index.php?target=datahub_main_edit'>Return to main edit page</a></h3><br><br><br>");
die;
