<?php

$checkbox_fields = array_map(
        function ($v) {return $v['Field'];},
        array_filter(
            cw_query("desc $tables[order_statuses]"), function($v) {return ($v['Type'] == 'int(1)');}
        )
    );

unset($checkbox_fields[array_search('is_system', $_checkbox_fields)]);

$new_os = &cw_session_register('new_os', array());

if ($REQUEST_METHOD == 'POST') {
    if ($action == 'update_statuses') {

        foreach ($posted_data as $status_code => $code_data) {
            $code_data['name'] = trim($code_data['name']);

            if (empty($code_data['name']))
                unset($code_data['name']); 

            if (!isset($code_data['deleted']))
                $code_data['deleted'] = 0;
/*
            if (!isset($code_data['email_customer']))
                $code_data['email_customer'] = 0;

            if (!isset($code_data['email_admin']))
                $code_data['email_admin'] = 0;

            if (!isset($code_data['inventory_decreasing']))
                $code_data['inventory_decreasing'] = 0;

*/
            foreach ($checkbox_fields as $checkbox_field) {
                if (!isset($code_data[$checkbox_field]))
                    $code_data[$checkbox_field] = 0;
            }


            cw_array2update('order_statuses', $code_data, "code = '$status_code'");
        }


        if (!empty($added_data['code'])) {
            $added_data['code'] = trim($added_data['code']);  
            $added_data['name'] = trim($added_data['name']);

            $new_os = $added_data;
            if (cw_query_first_cell("select count(*) from $tables[order_statuses] where code='$added_data[code]'")) {
                cw_add_top_message('Order Status code is already used, please enter different one', 'E');
                $item_hash = '#new_os_code';
            } elseif (empty($added_data['name'])) {
                cw_add_top_message('Cannot add new order status with empty name, please correct', 'E'); 
                $item_hash = '#new_os_code';
            } else {

                if (!intval($added_data['orderby'])) {
                    $added_data['orderby'] = 1 + intval(cw_query_first_cell("select max(orderby) from $tables[order_statuses]"));
                }
/*
                if (!isset($added_data['email_customer']))
                    $added_data['email_customer'] = 0;

                if (!isset($added_data['email_admin']))
                    $added_data['email_admin'] = 0;

                if (!isset($added_data['inventory_decreasing']))
                    $added_data['inventory_decreasing'] = 0;
              
*/
                foreach ($checkbox_fields as $checkbox_field) {
                    if (!isset($added_data[$checkbox_field]))
                        $added_data[$checkbox_field] = 0;
                }
 
                cw_array2insert('order_statuses', $added_data);
                $new_os = array();
                $item_hash = '#order_status_'.$added_data['code'];  
            }
        }
    }
    cw_header_location("index.php?target=orders_statuses$item_hash");
}

$where_cond = "";

$order_statuses = cw_query("select * from $tables[order_statuses] $where_cond order by deleted, orderby, code");
$smarty->assign('new_os', $new_os);
$smarty->assign('order_statuses', $order_statuses);
$smarty->assign('main', 'order_statuses');
