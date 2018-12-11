<?php
die('Enable this script ');
$blowfish_key ='f39bd56f34854570cfdbef2c7f2d172b';

cw_load ('crypt', 'user');

$users = cw_query("select * from $tables[customers] where xc_login != '' and usertype in ('C', 'V')");
foreach ($users as $u) {

    $new_passwd = cw_user_get_hashed_password($old_passwd = text_decrypt($u['password'],$blowfish_key));
    print('<pre>'); print_r(array($u, $old_passwd, $new_passwd));print('</pre>');
    print("<br><br>");
    db_query("update $tables[customers] set password='$new_passwd' where customer_id = '$u[customer_id]'");
}
die;
