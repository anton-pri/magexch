<?php

cw_load('user');

global $user;

$addresses2erase = 
    cw_query_column("select address_id from $tables[customers_addresses] where customer_id = '$user' union all select main_address_id from $tables[docs_user_info] where customer_id = '$user' union all select current_address_id from $tables[docs_user_info] where customer_id = '$user'");

$replace_text = '[Customer Deleted]';

if ($addresses2erase) {
    cw_array2update('customers_addresses', 
        array('company'=>$replace_text, 
              'title'=>'', 
              'firstname'=>$replace_text, 
              'lastname'=>$replace_text, 
              'address'=>$replace_text, 
              'address_2'=>$replace_text, 
              'city'=>$replace_text, 
              'county'=>$replace_text, 
              'region'=>$replace_text, 
              'state'=>$replace_text, 
              'zipcode'=>$replace_text, 
              'phone'=>$replace_text, 
              'fax'=>$replace_text), 
        "address_id IN ('".implode("','", $addresses2erase)."')");
}
cw_array2update('customers', array('email'=>$replace_text), "customer_id='$user'");
cw_array2update('docs_user_info', array('email'=>$replace_text, 'company'=>$replace_text), "customer_id='$user'");

db_query("delete from $tables[register_fields_values] where customer_id='$user'");

 
cw_header_location("index.php?target=user_C&mode=modify&user=$user");
