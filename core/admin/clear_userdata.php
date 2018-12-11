<?php

set_time_limit(86400);

if ($REQUEST_METHOD == 'POST') {
    switch ($action) {
        case 'delete_docs':
            cw_load('doc');
            $orders_to_delete = cw_query_column("SELECT doc_id FROM $tables[docs] WHERE type='O' ORDER BY doc_id");
            if (!empty($orders_to_delete)) {

                foreach ($orders_to_delete as $v) {
                    if ($v%20 == 0) cw_flush($v.' ... ');
                    cw_call('cw_doc_delete', array($v));
                }

                db_query("ALTER TABLE $tables[docs] AUTO_INCREMENT = 1");
                db_query("ALTER TABLE $tables[docs_items] AUTO_INCREMENT = 1");

                $top_message = array('content' => cw_get_langvar_by_name('msg_adm_all_orders_del'));
            } else {
                $top_message = array('content' => 'No orders found in database', 'type' => 'W');
            }
        break;
        case 'delete_customers':
            cw_load('user');
            $users_to_delete = cw_query_column("SELECT customer_id FROM $tables[customers] WHERE usertype='C' ORDER BY customer_id");
            if (!empty($users_to_delete)) {
                foreach ($users_to_delete as $v) {
                    if ($v%20 == 0) cw_flush($v.' ... ');
                    cw_func_call('cw_user_delete', array('customer_id' => $v, 'send_mail' => false));
                }
                $top_message = array('content' => 'All customers have been successfully deleted.');
            } else {
                $top_message = array('content' => 'No customers found in database', 'type' => 'W');
            }
        break;
        case 'delete_sellers':
            cw_load('user');
            $users_to_delete = cw_query_column("SELECT customer_id FROM $tables[customers] WHERE usertype='V' ORDER BY customer_id");
            if (!empty($users_to_delete)) {
                foreach ($users_to_delete as $v) {
                    if ($v%5 == 0) cw_flush($v.' ... ');
                    cw_func_call('cw_user_delete', array('customer_id' => $v, 'send_mail' => false));
                }
                $top_message = array('content' => 'All sellers have been successfully deleted.');
            } else {
                $top_message = array('content' => 'No sellers found in database', 'type' => 'W');
            }
        break;
        case 'delete_products':
            cw_load('product', 'category');
            $products_to_delete = cw_query_column("SELECT product_id FROM $tables[products] ORDER BY product_id LIMIT 10000");
            //$categories_to_delete = cw_query_column("SELECT category_id FROM cw_categories ORDER BY category_id");
            $categories_to_delete = cw_query_column("SELECT category_id FROM cw_categories_imported1809 WHERE category_id in (select category_id FROM cw_categories) ORDER BY category_id LIMIT 1000");
            if (!empty($products_to_delete) || !empty($categories_to_delete)) {

                print("<h2>Found ".count($products_to_delete)." products, dropping them</h2>");
                foreach ($products_to_delete as $v) {
                    if ($v%20 == 0) cw_flush($v.' ... ');
                    cw_call('cw_delete_product', array('product_id' => $v, 'update_categories' => false));
                }

                print("<h2>Found ".count($categories_to_delete)." categories, dropping them</h2>");
                foreach ($categories_to_delete as $v) {
                    if ($v%5 == 0) cw_flush($v.' ... ');
                    cw_call('cw_category_delete', array($v, false));
                }

                $products_left_count = cw_query_first_cell("SELECT count(*) from $tables[products]"); 
                $cats_left_count = cw_query_first_cell("SELECT count(*) from $tables[categories]"); 
                if ($products_left_count || $cats_left_count) { 
                    $top_message = array('content' => "$products_left_count products are left; $cats_left_count are left, you might need run the script again"); 
                } else {
                    $top_message = array('content' => 'All products and categories have been successfully deleted.');
                }

            } else {
                $top_message = array('content' => 'No products/categories have been found in database', 'type' => 'W');
            }
        break;
    }
    cw_header_location('index.php?target=clear_userdata');
}

$smarty->assign('main', 'clear_userdata');
