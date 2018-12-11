<?php
global $config, $customer_id;

cw_load('user');

$review_store_place = &cw_session_register("review_store_place");
// flag for user, had come by link. It gives ability review the product
$extended_review_customer_id = &cw_session_register("extended_review_customer_id", 0);
$preset_rating = &cw_session_register("review_store_place", array());
$user_ip = $_SERVER["REMOTE_ADDR"] . '_' . $_SERVER["HTTP_X_FORWARDED_FOR"] . '_' . $_SERVER["HTTP_CLIENT_IP"];

$_customer_id = $customer_id;

$reviews_per_page = 10;
if ($view_all == 'all') $reviews_per_page = 150;

if (empty($page)) $page = 1;
// review by link
if ($action == 'review_product') {

    if (!empty($review_key)) {

        $key_hashed = md5($review_key); 
        $earliest_key_date = (intval($config['estore_products_review']['login_key_active_days']) > 0)?(time()-intval($config['estore_products_review']['login_key_active_days'])*24*3600):0;
        $review_object = cw_query_first_cell("SELECT customer_id FROM $tables[products_reviews_login_keys] WHERE login_key='$key_hashed' AND date_created > $earliest_key_date");
        if ($review_object) {
            $result = cw_user_get_info($review_object);

            // if user exist and keys equal
            if (!empty($result)) {
                $extended_review_customer_id = $review_object;
            }
        }
    }
}

if (!empty($extended_review_customer_id)) {
    $_customer_id = $extended_review_customer_id;
    
    if ($action == 'review_product') { 

        if ($customer_id != $_customer_id) { 
            $customer_id = $_customer_id;

            $identifiers['C'] = array (
                'customer_id' => $customer_id
            );
  
            // Update addresses in session from database
            $user_address = &cw_session_register('user_address', array());
            $user_address['current_address'] = cw_user_get_address($customer_id, 'current');
            $user_address['main_address'] = cw_user_get_address($customer_id, 'main');

            db_query("update $tables[customers_system_info] set last_login='".cw_core_get_time()."' where customer_id='$customer_id'");
            $current_language = cw_query_first_cell("select language from $tables[customers] where customer_id='$customer_id'");
            cw_include('init/lng.php');
            $cart = &cw_session_register('cart', array());

            $cart = cw_user_get_stored_cart($customer_id);
            $userinfo = cw_user_get_info($customer_id);
            $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
            $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

            cw_event('on_login', array($customer_id, $current_area, 0));
        }

        if (!empty($rating)) {
            $preset_rating[$product_id] = $rating;
        }

        cw_session_save();

        cw_header_location("index.php?target=product&product_id=$product_id#write_rev");
    }
}

$where = cw_review_get_where_query_by_settings($_customer_id);
$avail_by_settings = cw_review_avail_by_settings($product_id, $_customer_id, $extended_review_customer_id);

$return_url =  cw_call('cw_core_get_html_page_url', array(array(
//	'var' => 'product','product_id' => $product_id,'js_tab' => 4,'delimiter '=> '&')));
        'var' => 'product','product_id' => $product_id)));

// Place review and rates
if (
	$action == 'review' 
	&& $product_id 
	&& ($config['estore_products_review']['customer_reviews'] == 'Y' || $config['estore_products_review']['customer_voting'] == 'Y')
    && $avail_by_settings
) {
	// Check captcha antibot code
	$antibot_err = &cw_session_register("antibot_err");
	$page = "on_reviews";
	if (!empty($addons['image_verification']) && $show_antibot_arr[$page] == 'Y') {

		if (isset($antibot_input_str) && !empty($antibot_input_str)) {
			$antibot_err = cw_validate_image($antibot_validation_val[$page], $antibot_input_str);
		}
		else {
			$antibot_err = true;
		}
	}

	// validate form
	$review_author = htmlspecialchars(trim($review_author));
    $review_main_title = htmlspecialchars(trim($review_main_title));
	$review_message = htmlspecialchars(trim($review_message));

	if (empty($review_author) || empty($review_message) || $antibot_err) {
		cw_add_top_message(cw_get_langvar_by_name('err_filling_form'),'E');
		$top_message = array('content' => cw_get_langvar_by_name('err_filling_form'), 'type' => 'E');
        if ($config['estore_products_review']['customer_voting'] == 'Y' && !empty($rating)) {
            $review_store_place['rating'] = $rating;
        }
		$review_store_place['author'] = $review_author;
        $review_store_place['main_title'] = $review_main_title;    
		$review_store_place['message'] = $review_message;
		$review_store_place['antibot_err'] = true;
		$review_store_place['error'] = true;
		cw_header_location($return_url);
	}

	// Create a new review
	$review_id = cw_array2insert(
		'products_reviews',
		array(
			'remote_ip' => $user_ip,
			'email' => $review_author,
            'main_title' => $review_main_title,
			'message' => $review_message,
			'product_id' => $product_id,
			'customer_id' => $_customer_id,
			'status' => $config['estore_products_review']['status_created_reviews'],
			'ctime' => time()
		)
	);

	// Create votes
    if ($config['estore_products_review']['customer_voting'] == 'Y' && !empty($rating)) {
		foreach ($rating as $attribute_id=>$vote) {
		   cw_array2insert(
				'products_votes',
				array(
					'remote_ip' => $user_ip,
					'vote_value' => $vote,
					'product_id' => $product_id,
					'customer_id' => $_customer_id,
					'attribute_id' => $attribute_id,
					'review_id' => $review_id
				)
			);
			cw_review_recalculate_avg_rating($product_id, $attribute_id);
		}
	
		cw_review_recalculate_avg_rating($product_id, 0);
    }

	// Update attribute 'has_review'
	// has_review has dropdown type with single option "Yes" to avoid 
	// useless option "No" in product filter when we use "yes_no" type
	$attribute_value_id = cw_query_first_cell(
		"SELECT ad.attribute_value_id
		FROM $tables[attributes_default] ad
		INNER JOIN $tables[attributes] a ON a.attribute_id = ad.attribute_id
		WHERE a.field = 'has_review' AND a.addon = 'estore_products_review'"
	);
	cw_func_call(
		'cw_attributes_save_attribute',
		array(
			'item_id' => $product_id,
			'item_type' => 'P',
			'attributes' => array('has_review' => $attribute_value_id)
		)
	);

	cw_add_top_message(cw_get_langvar_by_name('txt_thank_you_for_review'),'I');
    cw_header_location($return_url);
}

$vote_result = cw_query_first("
    SELECT COUNT(v.remote_ip) AS total, AVG(v.vote_value) AS rating
    FROM $tables[products_votes] v
    LEFT JOIN $tables[products_reviews] r ON r.review_id = v.review_id
    WHERE v.product_id='$product_id' AND IF(v.review_id = 0, 1, r.status) = 1 
");

if ($vote_result['total'] == 0) {
	$vote_result['rating'] = 0;
}

$vote_result['rating'] = price_format($vote_result['rating']);
$smarty->assign('vote_result', $vote_result);



$attribute_votes = cw_review_get_attribute_votes($where, $product_id);
$smarty->assign('attribute_votes', $attribute_votes);

$product_rates = cw_review_get_product_rates($product_id);
$smarty->assign('product_rates', $product_rates);

if (!empty($preset_rating[$product_id])) {
    $smarty->assign('preset_rating', $preset_rating[$product_id]);
    unset($preset_rating[$product_id]);
}

// Get reviews from DB
$fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

$from_tbls['r'] = 'products_reviews';
$fields[] = "r.*";
$where[] = "r.product_id='$product_id'";
$where[] = 'r.stoplist = 0 AND r.status = 1';

// Review rating vote
$query_joins['v'] = array(
    'tblname' => 'products_votes',
    'on' => 'v.review_id =  r.review_id',
);
$fields[] = 'avg(v.vote_value) as vote';

// Review useful votes
$query_joins['p_vote_tbl'] = array(
    'tblname' => 'products_reviews_ratings',
    'on' => 'p_vote_tbl.review_id =  r.review_id AND p_vote_tbl.rate = 1',
);
$query_joins['n_vote_tbl'] = array(
    'tblname' => 'products_reviews_ratings',
    'on' => 'n_vote_tbl.review_id =  r.review_id AND n_vote_tbl.rate = 2',
);
$fields['p_vote'] = "count(p_vote_tbl.id) as p_vote";
$fields['n_vote'] = "count(n_vote_tbl.id) as n_vote";

if (empty($rsort)) {
    $rsort = 'time';
    $rsort_direction = 1;
}

$orderbys[] = "r.customer_id ASC";

if ($rsort == 'rate') {
    // Sort by rate
    $orderbys[] = 'vote '.($rsort_direction == 1 ? 'DESC':'ASC');
} elseif ($rsort == 'helpful') {
    // Sort by useful
    $orderbys[] = 'p_vote DESC';
    $orderbys[] = 'n_vote ASC';
}
$orderbys[] = 'r.ctime '. ($rsort_direction == 1 ? 'DESC':'ASC');

$groupbys[] = 'r.review_id';

//    $search_query_count = cw_db_generate_query('count(*)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);
$search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
$reviews = cw_query($search_query);
$total_items = count($reviews);

$navigation = cw_core_get_navigation('reviews', $total_items, $page, $reviews_per_page);
$navigation['script'] = 'index.php?target='.$target.'&product_id='.$product_id.($rsort?'&rsort='.$rsort:'').($rsort_direction?'&rsort_direction='.$rsort_direction:'');
$smarty->assign('reviews_navigation', $navigation);

$smarty->assign('page', $page);
$smarty->assign('rsort_direction', $rsort_direction);
$smarty->assign('rsort', $rsort);

if (count($reviews)) {
    $vote_reviews = array();
    foreach ($reviews as $k=>$review) {
        if ($review['vote'] > 4.7) $vote_reviews[5][0]++;
        elseif ($review['vote'] >= 4) $vote_reviews[4][0]++;
        elseif ($review['vote'] >= 3) $vote_reviews[3][0]++;
        elseif ($review['vote'] >= 2) $vote_reviews[2][0]++;
        elseif ($review['vote'] >= 1) $vote_reviews[1][0]++;
        else $vote_reviews[0][0]++;
    }
    for ($i=0; $i<=5; $i++) {
        $vote_reviews[$i][1] = intval($vote_reviews[$i][0]/count($reviews)*100);
    }
    $smarty->assign('vote_reviews', $vote_reviews);
}
$reviews = array_slice($reviews, ($page-1)*$reviews_per_page, $reviews_per_page);

foreach ($reviews as $k=> $review) {
    $reviews[$k]['customer_vote'] = cw_query_first_cell("SELECT rate FROM $tables[products_reviews_ratings] WHERE customer_id = '$customer_id' AND review_id = $review[review_id]");
    $reviews[$k]['attribute_votes'] = cw_review_get_attribute_vote_values($reviews[$k]);
}

// Restore review data to fill form if error occured
if (!empty($review_store_place)) {
	$smarty->assign ("review", $review_store_place);
	$review_store_place = false;
}

$stoplist_where = "remote_ip='$user_ip'";
if (!empty($_customer_id)) {
    $stoplist_where .= " OR customer_id='$_customer_id'";
}
$block_by_stop_list = cw_query_first_cell("
    SELECT review_id FROM $tables[products_reviews]
    WHERE ($stoplist_where) AND stoplist = 1
");
$smarty->assign("block_by_stop_list", $block_by_stop_list);

$smarty->assign("user_is_purchasers", cw_review_check_is_purchasers($product_id, $_customer_id));

$smarty->assign("reviews", $reviews);
$smarty->assign("extended_review_customer_id", $extended_review_customer_id);
$smarty->assign("avail_by_settings", $avail_by_settings);
