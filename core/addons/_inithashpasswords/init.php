<?php

cw_load('crypt');
cw_load('user');
$all_accounts = cw_query("select customer_id, password from $tables[customers]");

foreach($all_accounts as $account) {
    $parts = explode(':', $account['password']);
    if (count($parts) == 2) { 
        if (strlen($parts[0]) == 32 && strlen($parts[1]) == 8) { 
//            print("$account[customer_id] $account[password] <br>");
            continue;
        }
    }

    $account_password = text_decrypt($account['password']);
//    print("$account[customer_id] $account_password <br>");
    if (!empty($account_password)) {
        cw_array2update('customers', 
                        array('password'=>cw_call('cw_user_get_hashed_password', array($account_password))),
                        "customer_id=$account[customer_id]"); 
    }
}
db_query("delete from cw_addons where addon='_inithashpasswords'");
