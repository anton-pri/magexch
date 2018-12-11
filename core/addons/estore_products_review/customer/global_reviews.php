<?php

if ($REQUEST_METHOD == "POST" && $action == "add_review") {
    if (!empty($review_new['message'])) {
        $review_new['status'] = $config['estore_products_review']['status_created_reviews'];
        $review_new['email'] = $user_account['email'];  
        $review_id = cw_call('cw_review_add_new_review_manual', array($review_new, 0));
    }

    if (!empty($review_id)) {

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

        cw_add_top_message(cw_get_langvar_by_name('txt_thank_you_for_review'));
    } else {
        cw_add_top_message(cw_get_langvar_by_name('err_filling_form'),'E');
    }

    cw_header_location("index.php?target=global_reviews");
} 

$items_per_page_targets[$target] = 10;

$total_items = cw_call('cw_review_get_global_review', array());

$navigation = cw_core_get_navigation($target, $total_items, $page);

$global_reviews = cw_call('cw_review_get_global_review', array('',''," ORDER BY $tables[products_reviews].ctime DESC ",
                                                    "LIMIT $navigation[first_page], $navigation[objects_per_page]", FALSE));

$items_per_page_targets[$target] = PHP_INT_MAX;

$navigation['script'] = 'index.php?target='.$target;

$smarty->assign('navigation', $navigation);

if (!empty($global_reviews)) {

    $rating_cond = "type='global_rating'";

    list ($attributes, $nav) = cw_func_call(
        'cw_attributes_search',
        array('data'=>array('active'=>1,'is_show'=>1,'sort_field'=>'orderby')), array('where' => array($rating_cond))
    );

    if (!empty($attributes)) {
        foreach ($global_reviews as $rev_k => $rev_v) {
            $global_reviews[$rev_k]['votes'] = array();
            foreach ($attributes as $att) {
                $global_reviews[$rev_k]['votes'][] = 
                    array('attribute' => $att,
                          'vote_value' => 
                         cw_query_first_cell("SELECT 100*($tables[products_votes].vote_value/5.00) FROM $tables[products_votes] WHERE $tables[products_votes].review_id = '$rev_v[review_id]' AND $tables[products_votes].attribute_id='$att[attribute_id]' AND $tables[products_votes].product_id = 0"));
            }
        }
        $smarty->assign('vote_attributes', $attributes); 
    }
}

$smarty->assign('global_reviews', $global_reviews);

$smarty->assign('main', 'global_reviews');
