<?php
global $smarty, $tables;
// check if customer has placed review for this order
$estore_customer_review = FALSE;

if (!empty($doc_id) && !empty($doc_data) && $doc_data['type'] == 'O') {
    $customer_reviews = cw_query("
        SELECT r.review_id
        FROM $tables[docs] d
        LEFT JOIN $tables[docs_items] di ON di.doc_id = d.doc_id
        LEFT JOIN $tables[docs_user_info] ui ON ui.doc_info_id = d.doc_info_id
        LEFT JOIN $tables[products_reviews] r ON r.product_id = di.product_id
        WHERE d.doc_id IN ($doc_id) AND d.type = 'O' AND ui.customer_id <> 0
            AND r.product_id IS NOT NULL AND di.product_id IS NOT NULL
    ");

    if (!empty($customer_reviews) && is_array($customer_reviews)) {
        $review_ids = array();

        foreach ($customer_reviews as $customer_review) {
            $review_ids[] = $customer_review['review_id'];
        }
        $estore_customer_review = implode(',', $review_ids);
    }
}

$smarty->assign('estore_customer_review', $estore_customer_review);
