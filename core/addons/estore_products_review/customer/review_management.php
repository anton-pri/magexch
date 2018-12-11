<?php
global $smarty, $tables, $customer_id, $config;

$extended_review_customer_id = &cw_session_register("extended_review_customer_id", 0);
$_customer_id = $customer_id;

if (!empty($extended_review_customer_id)) {
    $_customer_id = $extended_review_customer_id;
}

if ($action == 'update' && defined('IS_AJAX')) {
    cw_load('ajax');

    if (
        !empty($review_id)
        && !empty($review)
        && $config['estore_products_review']['customer_reviews'] == 'Y'
        && $config['estore_products_review']['writing_reviews'] != 'N'
    ) {

        $review_data = cw_query_first("
            SELECT *
            FROM $tables[products_reviews]
            WHERE review_id='$review_id' AND customer_id='$_customer_id'
        ");

        if (!empty($review_data)) {
            $status = $review_data['status'];

            foreach ($rating as $attr_id=>$vote) {
                // update vote value
       			$exists = cw_query_first_cell("SELECT vote_id FROM $tables[products_votes] WHERE review_id = '$review_id' AND attribute_id ='$attr_id'");
				if ($exists) {
					cw_array2update('products_votes', 
						array('vote_value' => $vote),
						"review_id = '$review_id' AND attribute_id ='$attr_id'"
					 );
				} else {
					cw_array2insert('products_votes',array(
						'remote_ip' => $review_data['remote_ip'],
						'vote_value' => $vote,
						'product_id' => $review_data['product_id'],
						'customer_id' => $review_data['customer_id'],
						'review_id' => $review_id,
						'attribute_id' => $attr_id,
					));
				}
                cw_review_recalculate_avg_rating($review_data['product_id'], $attr_id);
            }
            cw_review_recalculate_avg_rating($review_data['product_id']);
            
            // if some changed
            if (
                $review_data['email'] != $review['email']
                || $review_data['message'] != $review['message']
            ) {
                $status = $config['estore_products_review']['status_created_reviews'];
            }

            // update review
            $update_review_data = array();
            $update_review_data['email'] = $review['email'];
            $update_review_data['message'] = $review['message'];
            $update_review_data['status'] = $status;
            cw_array2update(
                'products_reviews',
                $update_review_data,
                "review_id = '$review_id'"
            );
        }
    }

    cw_add_ajax_block(array(
        'id' => 'review_management_container',
        'action' => 'append',
        'content' => ''
    ));
}

$review = array();

if (
    !empty($review_id)
    && !empty($product_id)
    && $config['estore_products_review']['customer_reviews'] == 'Y'
    && cw_review_avail_by_settings($product_id, $_customer_id, $extended_review_customer_id)
) {
	$review  = cw_query_first("SELECT  * FROM $tables[products_reviews] r  WHERE r.review_id='$review_id' AND r.customer_id='$_customer_id'");
	if ($config['estore_products_review']['customer_voting'] == 'Y') {
		$attribute_votes  = cw_review_get_attribute_vote_values($review);
	}
}

$smarty->assign('review', $review);
$smarty->assign('attribute_votes', $attribute_votes);
$smarty->assign('current_section_dir', 'main');
$smarty->assign('main', 'estore_review_management');
$smarty->assign('home_style', 'popup');
