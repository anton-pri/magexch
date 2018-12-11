<?php

$customer_products = array();

if (empty($remind_customer_id)) {
    if (empty($remind_email))
        $remind_email = 'antonpribytov@gmail.com';

    $remind_customer_id = cw_query_first_cell("select customer_id from $tables[customers] where email='$remind_email' and usertype='C' limit 1");
} else {
    $remind_email = cw_query_first_cell("select email from $tables[customers] where customer_id='$remind_customer_id'");
}

$status_remind = $config['estore_products_review']['order_status_start_reminder'];

    $not_reviews_products = cw_query($s ="
        SELECT DISTINCT di.product_id, d.doc_id
        FROM $tables[docs] d
        LEFT JOIN $tables[docs_items] di ON di.doc_id = d.doc_id
        LEFT JOIN $tables[docs_user_info] ui ON ui.doc_info_id = d.doc_info_id
        LEFT JOIN $tables[products_reviews] r ON r.product_id = di.product_id
        LEFT JOIN $tables[products_reviews_reminder] rr ON rr.product_id = di.product_id
            AND ui.customer_id = rr.customer_id
        WHERE d.type = 'O' AND d.status = '$status_remind'
            AND ui.customer_id <> 0 AND ui.email = '$remind_email'  
            AND r.product_id IS NULL
            AND di.product_id IS NOT NULL
        ORDER BY d.doc_id DESC LIMIT 5 
    ");

    if (!empty($not_reviews_products) && is_array($not_reviews_products)) {
            $ordered_products = array();

            foreach ($not_reviews_products as $_r) {
                $ordered_products[$_r['doc_id']][] = $_r['product_id'];
                $_customer_id = cw_query_first_cell("SELECT ui.customer_id FROM $tables[docs_user_info] ui INNER JOIN $tables[docs] d ON ui.doc_info_id = d.doc_info_id WHERE d.doc_id='".$_r['doc_id']."' LIMIT 1");
                $return_str .= "Reminder sent on order #".$_r['doc_id']." to customer #".$_customer_id." on product #".$_r['product_id']."<br>";
            }


        print($return_str);  

        global $test_order_review_reminder;
        $test_order_review_reminder = true;
print_r($ordered_products);
        cw_review_send_order_review_reminder_email($ordered_products);

    }
print('Done');
die;
