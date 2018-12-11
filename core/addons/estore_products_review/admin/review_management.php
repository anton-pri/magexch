<?php
global $smarty, $tables;

if ($action == 'update' && defined('IS_AJAX')) {
    cw_load('ajax');

    if (!empty($review_id) && !empty($review)) {
        $review_data = cw_query_first("SELECT * FROM $tables[products_reviews] WHERE review_id='$review_id'");

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

        // update review
        $update_review_data = array();
        $update_review_data['testimonials'] = ($review['addto'] == 'testimonials' ? 1 : 0);
        $update_review_data['stoplist'] = ($review['addto'] == 'stoplist' ? 1 : 0);
        $update_review_data['name'] = $review['name'];
        $update_review_data['email'] = $review['email'];
        $update_review_data['main_title'] = $review['main_title'];  
        $update_review_data['message'] = $review['message'];
        $update_review_data['status'] = $review['status'];
        cw_array2update(
            'products_reviews',
            $update_review_data,
            "review_id = '$review_id'"
        );
    }

}

$review = array();

if (
    !empty($review_id)
) {
	$review  = cw_query_first("SELECT  * FROM $tables[products_reviews] r  WHERE r.review_id='$review_id'");
	$attribute_votes  = cw_review_get_attribute_vote_values($review);
}

$smarty->assign('review', $review);
$smarty->assign('attribute_votes', $attribute_votes);
$smarty->assign('current_section_dir', 'main');
$smarty->assign('main', 'estore_review_management');
$smarty->assign('home_style', 'popup');
