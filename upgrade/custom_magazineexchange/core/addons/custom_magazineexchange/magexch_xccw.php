<?php

//die('Clean off the userdata and orders first, then enable this script');

function cw_magexch_import_logged_query($query, $comment = '') {
    global $cw_fi_preview_mode;
    static $timer;

    $timer_query = cw_core_microtime();

    if (is_null($timer)) {
        $timer = $timer_query;
    }


    if (empty($comment) && !$cw_fi_preview_mode)
        cw_flush("<b>Running query</b>: $query <br>");

    if (!empty($query))
        db_query($query);
    else
        $comment = "<b>$comment</b>";

    $_timer = cw_core_microtime();

    $time_query  = $_timer - $timer_query; // timer to exec query
    $time_overal = $_timer - $timer;       // timer from last call of cw_magexch_import_logged_query()
    $timer = $_timer;

    cw_log_add(__FUNCTION__, array($query, $comment,['query_time'=>$time_query,'step_time'=>$time_overal]));

    if (!$cw_fi_preview_mode) {
        if (empty($comment))
            cw_flush("..done<br>");
        else
            cw_flush($comment."<br>");
    }
}


//cw_magexch_import_logged_query("alter table cw_customers add column xc_login varchar(32) not null default ''");

cw_magexch_import_logged_query("insert into cw_customers (usertype, password, email, status, change_password, membership_id, language, xc_login) select 'C', password,email,status,IF(change_password='Y',1,0),1,'EN',login from xcart_customers where usertype='C' and membershipid=1");
cw_magexch_import_logged_query("insert into cw_customers (usertype, password, email, status, change_password, membership_id, language, xc_login) select 'C', password,email,status,IF(change_password='Y',1,0),0,'EN',login from xcart_customers where usertype='C' and membershipid=0");

cw_magexch_import_logged_query("alter table xcart_customers add column cw_id int(11) not null default 0");
cw_magexch_import_logged_query("update xcart_customers xc set cw_id = (select customer_id from cw_customers where xc_login=xc.login limit 1)");
cw_magexch_import_logged_query("insert ignore into cw_customers_system_info (customer_id, creation_customer_id, creation_date, modification_customer_id, modification_date, last_login, referer) select cw_id, cw_id, first_login, cw_id, first_login, last_login, referer from xcart_customers where cw_id!=0");
cw_magexch_import_logged_query("insert into cw_customers_addresses (customer_id, main, current, company, title, firstname, lastname, address, address_2, city, county, region, state, country, zipcode, phone, fax) select cw_id, 1, 0, company, b_title, b_firstname, b_lastname, b_address, '', b_city, b_county, '', b_state, b_country, b_zipcode, phone, fax from xcart_customers where cw_id!=0");
cw_magexch_import_logged_query("insert into cw_customers_addresses (customer_id, main, current, company, title, firstname, lastname, address, address_2, city, county, region, state, country, zipcode, phone, fax) select cw_id, 0, 1, company, s_title, s_firstname, s_lastname, s_address, '', s_city, s_county, '', s_state, s_country, s_zipcode, phone, fax from xcart_customers where cw_id!=0");

cw_magexch_import_logged_query("insert into cw_customers (usertype, password, email, status, change_password, membership_id, language, xc_login) select 'V',password,email,status,IF(change_password='Y',1,0),95,'EN',login from xcart_customers where usertype='P' and membershipid=5");
cw_magexch_import_logged_query("insert into cw_customers (usertype, password, email, status, change_password, membership_id, language, xc_login) select 'V',password,email,status,IF(change_password='Y',1,0),96,'EN',login from xcart_customers where usertype='P' and membershipid=7");
cw_magexch_import_logged_query("update xcart_customers xc set cw_id = (select customer_id from cw_customers where xc_login=xc.login limit 1) where usertype='P' and membershipid in (5,7)");
cw_magexch_import_logged_query("insert ignore into cw_customers_system_info (customer_id, creation_customer_id, creation_date, modification_customer_id, modification_date, last_login, referer) select cw_id, cw_id, first_login, cw_id, first_login, last_login, referer from xcart_customers where cw_id!=0 and usertype='P' and membershipid in (5,7)");
cw_magexch_import_logged_query("insert into cw_customers_addresses (customer_id, main, current, company, title, firstname, lastname, address, address_2, city, county, region, state, country, zipcode, phone, fax) select cw_id, 1, 0, company, b_title, b_firstname, b_lastname, b_address, '', b_city, b_county, '', b_state, b_country, b_zipcode, phone, fax from xcart_customers where cw_id!=0 and usertype='P' and membershipid in (5,7)");
cw_magexch_import_logged_query("insert into cw_customers_addresses (customer_id, main, current, company, title, firstname, lastname, address, address_2, city, county, region, state, country, zipcode, phone, fax) select cw_id, 0, 1, company, s_title, s_firstname, s_lastname, s_address, '', s_city, s_county, '', s_state, s_country, s_zipcode, phone, fax from xcart_customers where cw_id!=0 and usertype='P' and membershipid in (5,7)");

cw_magexch_import_logged_query("replace into cw_register_fields_values select (select field_id from cw_register_fields where field='username' limit 1) as field_id, cw_id as customer_id, 0 as key_id, '' as key_value, login as value from xcart_customers where usertype='P' and membershipid in (5, 7) and cw_id!=0");


print("<br><hr><a href='index.php?target=magexch_transfer_passwords' target='_blank'>Convert Passwords from blowfish to md5 hashes</a>");

print("<br><hr><a href='index.php?target=magexch_transfer_orders' target='_blank'>Orders Transfer</a><br>");

cw_magexch_import_logged_query("truncate table cw_magazine_sellers_product_data");

cw_magexch_import_logged_query("insert into cw_magazine_sellers_product_data (seller_id, seller_login, product_id, `condition`, quantity, price, comments) select customer_id, login, xsd.productid, xsd.`condition`, xsd.avail, price, xsd.comments from xcart_seller_data xsd inner join xcart_pricing xp on xp.seller_data_id=xsd.id and xsd.id!=0 and xp.productid=xsd.productid inner join cw_customers on usertype='V' and xc_login=login and xc_login!=''");

die('<h1>Data transfer is complete!</h1>');




