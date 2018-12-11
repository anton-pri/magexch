<?php

if (constant('IS_AJAX') && is_numeric($doc_id)) {
    global $tables;

    $remind_products = cw_query("
        SELECT DISTINCT di.product_id, ui.customer_id
        FROM $tables[docs] d
        LEFT JOIN $tables[docs_items] di ON di.doc_id = d.doc_id
        LEFT JOIN $tables[docs_user_info] ui ON ui.doc_info_id = d.doc_info_id
        WHERE d.doc_id = $doc_id
    ");

    if (!empty($remind_products) && is_array($remind_products)) {
        $customer_products = array();

        foreach ($remind_products as $_r) {
            $customer_products[$_r['customer_id']][] = $_r['product_id'];
        }

        cw_review_send_order_review_reminder_email($customer_products);
        $lbl_name = 'lbl_email_sent_successfully';
    }
    else {
        $lbl_name = 'lbl_email_not_sent';
    }

    cw_add_ajax_block(array(
        'id' 		=> 'additional_doc_action',
        'action' 	=> 'update',
        'content' 	=> cw_get_langvar_by_name($lbl_name, NULL, FALSE, TRUE)
    ));
}