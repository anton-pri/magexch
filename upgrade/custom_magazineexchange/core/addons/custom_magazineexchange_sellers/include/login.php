<?php
namespace cw\custom_magazineexchange_sellers;

if ($action == 'login') {
    if (strpos($email,'@')===false) {
        // Try to find email by username
        $field_id = cw_query_first_cell("SELECT field_id FROM $tables[register_fields] WHERE field='username'");
        $uid = cw_query_first_cell("SELECT customer_id FROM $tables[register_fields_values] WHERE value='$email' and field_id='$field_id'");
        $cust = \Customer\get($uid);
        $email  = $cust['email'];
        unset($field_id, $uid, $cust);
    }
}
