<?php
set_time_limit(86400);
//error_reporting(E_ERROR);

cw_flush("<h2>Generating clean urls...</h2>");
cw_call('cw_clean_url_generate_all');


    cw_flush("<h2>Activating history clean urls...</h2>");

    cw_csvxc_logged_query("update cw_attributes_values av set av.value=(select concat(clean_url, '.html') from cw_datahub_xcart_clean_urls where resource_type='P' and resource_id=av.item_id) where av.attribute_id=32 and av.item_type='P' and code='EN' and av.item_id in (select resource_id from cw_datahub_xcart_clean_urls where resource_type='P')");

    cw_flush("<h2>Size converting...</h2>");
    cw_call('sw_product_update_size');


cw_flush('<br><br><h3> Done </h3>');

    print("<br><br><br><h3><a target='_blank' href='../index.php?target=cleanup'>Run cache cleanup</a></h3><br><br><br>");

    print("<h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a></h3><br><br><br>");



die;
