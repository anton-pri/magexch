<?php
global $smarty, $tables, $customer_id;

$extended_review_customer_id = &cw_session_register("extended_review_customer_id", 0);
$_customer_id = $customer_id;

if (!empty($extended_review_customer_id)) {
    $_customer_id = $extended_review_customer_id;
}

if (defined('IS_AJAX') && !empty($review_id)) {
    cw_load('ajax');

    $review = cw_query_first("
        SELECT r.*, v.vote_value
        FROM $tables[products_reviews] r
        LEFT JOIN $tables[products_votes] v ON r.review_id = v.review_id
        WHERE r.review_id='$review_id' AND r.customer_id='$_customer_id'
    ");
    $review['attribute_votes'] = cw_review_get_attribute_vote_values($review);
    $review['p_vote'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[products_reviews_ratings] WHERE review_id =  $review[review_id] AND rate = 1");
    $review['n_vote'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[products_reviews_ratings] WHERE review_id =  $review[review_id] AND rate = 2");
    $smarty->assign('review_item', $review);
    $avail_by_settings = cw_review_avail_by_settings($review['product_id'], $_customer_id, $extended_review_customer_id);
    $smarty->assign('avail_by_settings', $avail_by_settings);

    cw_add_ajax_block(array(
        'id' => 'customer_review_item_' . $review_id,
        'action' => 'update',
        'template' => 'addons/estore_products_review/customer_reviews_management_item.tpl'
    ));
}
