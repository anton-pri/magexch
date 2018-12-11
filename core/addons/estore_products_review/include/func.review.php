<?php
// This function detects common rating attribute for smarty to override its appearance with stars
// Only one attribute should have "rating" name and it collectes average ratings of attributes with "rating" type
function cw_review_is_rating_attribute($params) {
    if ($params['attribute']['field'] == 'rating') return true;
    return false;
}

function cw_review_product_search($data, $result) {
# kornev, select it in some of the cases only
    if ($data['info_type'] & 32 && $result[0])
    foreach($result[0] as $k=>$v) {
        $tmp = cw_func_call('cw_attributes_get', array('item_id' => $v['product_id'], 'item_type' => 'P', 'attribute_fields' => array('rating')));
        $result[0][$k]['rating'] = round($tmp['rating']['value'],2);
        $result[0][$k]['comments'] = cw_review_get_comments_count($v['product_id']);
    }
    return $result;
}

function cw_review_product_get($data, $result) {
    if (empty($result)) return $result;
    $result = cw_review_product_search($data, array(0=>array($result)));
    return $result[0][0];
}

function cw_review_product_filter_get_slider_value($data, $result) {
    if ($data['field'] == 'rating') return price_format($data['value']);
    return $result;
}

// get testimonials
function cw_review_get_testimonials() {
    global $tables;

    $result = cw_query("
        SELECT email, message FROM $tables[products_reviews]
        WHERE testimonials = 1 ORDER BY ctime DESC
    ");

    if (!empty($result) && is_array($result)) {

        foreach ($result as $k => $v) {
            $result[$k]['message'] = nl2br($v['message']);
        }
        return $result;
    }

    return "";
}

// get review query by query string params
function cw_review_get_reviews_query($where="", $orderby="", $limit="", $count_query=FALSE) {
    global $tables;

    if ($count_query) {
        $select = "count($tables[products_reviews].review_id)";
    }
    else {
        $select = "$tables[products_reviews].*, avg($tables[products_votes].vote_value) as vote_value,
            $tables[products].productcode as sku, $tables[customers].email as real_email,
            $tables[customers_addresses].firstname, $tables[customers_addresses].lastname";
    }

    $query = "
        SELECT $select
        FROM $tables[products_reviews]
        LEFT JOIN $tables[products_votes] ON $tables[products_reviews].review_id = $tables[products_votes].review_id
            AND $tables[products_votes].vote_value > 0
        LEFT JOIN $tables[products] ON $tables[products_reviews].product_id = $tables[products].product_id
        LEFT JOIN $tables[customers] ON $tables[products_reviews].customer_id = $tables[customers].customer_id
        LEFT JOIN $tables[customers_addresses] ON $tables[products_reviews].customer_id = $tables[customers_addresses].customer_id
            AND $tables[customers_addresses].customer_id <> 0 AND $tables[customers_addresses].main = 1
        $where
        group by $tables[products_reviews].review_id
        $orderby
        $limit
    ";

    return $query;
}

// get count reviews by query string params
function cw_review_get_management_reviews_count($where="", $orderby="", $limit="") {
    $count_query = cw_review_get_reviews_query($where, $orderby, $limit);
    $_res = db_query($count_query);
    $total_items = db_num_rows($_res);
    db_free_result($_res);

    return $total_items;
}

// get data for review list by query string params
function cw_review_get_management_reviews($where="", $orderby="", $limit="") {
    global $config;

    $reviews = cw_query(cw_review_get_reviews_query($where, $orderby, $limit));

    if (!empty($reviews) && is_array($reviews)) {
        $date_format = (!empty($config['Appearance']['date_format']) ? $config['Appearance']['date_format'] : '%Y-%m-%d');
        $time_format = (!empty($config['Appearance']['time_format']) ? $config['Appearance']['time_format'] : '%H:%M:%S');
        $count_review_signs = 50;

        foreach ($reviews as $k => $v) {
            // date
            $reviews[$k]['date'] = strftime(
                $date_format . ' ' . $time_format,
                $v['ctime'] + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR
            );

            // customer
            if (!empty($v['firstname']) || !empty($v['lastname'])) {
                $name = trim($v['firstname'] . ' ' . $v['lastname']);
            }
            else {
                $name = $v['email'];
            }
            $reviews[$k]['customer'] = '<b>' . $name . '</b>';
            if (!empty($v['customer_id'])) {
                $reviews[$k]['customer'] .= '<br>ID' . $v['customer_id'];
            }
            if (!empty($v['real_email'])) {
                $reviews[$k]['customer'] .= '<br>(' . $v['real_email'] . ')';
            }
            if (!empty($v['remote_ip'])) {
                $ips = explode('_', $v['remote_ip']);
                $ips = array_unique($ips);
                $remote_ip = (isset($ips[0]) && !empty($ips[0]) ? $ips[0] : '');
                $reviews[$k]['customer'] .= '<br>[' . $remote_ip . ']';
            }

            // review
            $reviews[$k]['message'] = substr($v['message'], 0, $count_review_signs);
            if (strlen($v['message']) > $count_review_signs) {
                $reviews[$k]['message'] .=  '...';
            }

            // status
            $statuses = array(
                cw_get_langvar_by_name('lbl_pending', NULL, FALSE, TRUE),
                cw_get_langvar_by_name('lbl_approved', NULL, FALSE, TRUE),
                cw_get_langvar_by_name('lbl_declined', NULL, FALSE, TRUE)
            );
            $reviews[$k]['status'] = $statuses[$v['status']];

            // flag
            if ($v['testimonials'] == 1) {
                $reviews[$k]['flag'] = cw_get_langvar_by_name('lbl_testimonials', NULL, FALSE, TRUE);
            }
            elseif ($v['stoplist'] == 1) {
                $reviews[$k]['flag'] = cw_get_langvar_by_name('lbl_stop_list', NULL, FALSE, TRUE);
            }
            else {
                $reviews[$k]['flag'] = '';
            }
        }
    }

    return $reviews;
}

// get stop list
function cw_review_get_stop_list() {
    global $tables;

    $result = cw_query("
        SELECT pr.*, p.product, c.email as real_email
        FROM $tables[products_reviews] pr
        LEFT JOIN $tables[products] p ON p.product_id = pr.product_id
        LEFT JOIN $tables[customers] c ON c.customer_id = pr.customer_id
        WHERE pr.stoplist = 1 ORDER BY pr.ctime DESC
    ");

    if (!empty($result) && is_array($result)) {

        foreach ($result as $k => $v) {

            if (!empty($result[$k]['remote_ip'])) {
                $ips = explode('_', $result[$k]['remote_ip']);
                $ips = array_unique($ips);
                $result[$k]['remote_ip'] = (isset($ips[0]) && !empty($ips[0]) ? $ips[0] : '');
            }
            $result[$k]['message'] = nl2br($v['message']);
        }
        return $result;
    }

    return "";
}

/*
 * Return rates as average votes
 */
function cw_review_get_product_rates($product_id) {
	global $tables;
	
    $rating_cond = ($product_id > 0)?("type='rating'"):("type='global_rating'");

    list ($attributes, $nav) = cw_func_call(
		'cw_attributes_search',
		array('data'=>array('active'=>1,'is_show'=>1,'sort_field'=>'orderby')), array('where' => array($rating_cond))
	);
	
   $ratings = array();
    
    if (!empty($attributes)) {
        foreach ($attributes as $k=>$a) {
				$ratings[$k] = array(
					'rating' => cw_call('cw_attribute_get_value',array($a['attribute_id'], $product_id)),
					'attribute_id' => $a['attribute_id'],
					'name' => $a['name']
				);
				
		}
	}
	
	return $ratings;
    
}

// get available attributes votes and user vote if exist
function cw_review_get_attribute_votes($where, $product_id) {
    global $tables;

    $rating_cond = ($product_id > 0)?("type='rating'"):("type='global_rating'");
 
    list ($attributes, $nav) = cw_func_call(
		'cw_attributes_search',
		array('data'=>array('active'=>1,'is_show'=>1,'sort_field'=>'orderby')), array('where' => array($rating_cond))
	);

    $votes = array();

    if (!empty($attributes)) {

        foreach ($attributes as $attribute) {
            $vote = cw_query_first("
                SELECT vote_id, vote_value FROM $tables[products_votes]
                WHERE $where AND product_id='$product_id' AND attribute_id = $attribute[attribute_id]
            ");
            $votes[] = array(
                'vote_id' => $vote['vote_id'],
                'attr_id' => $attribute['attribute_id'],
                'name' => $attribute['name'],
                'vote' => (empty($vote['vote_value']) ? "" : $vote['vote_value'])
            );
        }
    }
    return $votes;
}

// get available attributes vote values for review
function cw_review_get_attribute_vote_values($review) {
    global $tables;

    $rating_cond = ($review['product_id'] > 0)?("type='rating'"):("type='global_rating'");

    list ($attributes, $nav) = cw_func_call(
		'cw_attributes_search',
		array('data'=>array('active'=>1,'is_show'=>1,'sort_field'=>'orderby')), array('where' => array($rating_cond))
	);

    $votes = array();

    if (!empty($attributes)) {
		
        $where = cw_review_get_where_query_by_settings($review['customer_id']);
		$rates = cw_query_hash("SELECT vote_value as vote, attribute_id FROM $tables[products_votes]
          WHERE $where AND review_id='$review[review_id]' AND attribute_id IN ('".join("','",array_column($attributes,'attribute_id'))."')",
          'attribute_id',false,true);

         foreach ($attributes as $v) {
			 $votes[$v['attribute_id']]['name'] = $v['name'];
			 $votes[$v['attribute_id']]['vote'] = $rates[$v['attribute_id']];
		 }
    }

    return $votes;
}

// delete review from stop list
function cw_review_delete_from_stop_list($id) {
    global $tables;

    if (!empty($id) && is_numeric($id)) {
        cw_query("
            UPDATE $tables[products_reviews] SET stoplist = 0
            WHERE review_id = $id
        ");
    }
}

// check that the user is the purchasers of the product
function cw_review_check_is_purchasers($product_id, $customer_id) {
    global $tables;

    if (
        !empty($product_id) && is_numeric($product_id)
        && !empty($customer_id) && is_numeric($customer_id)
    ) {
        $result = cw_query_first_cell("
            SELECT d.doc_id
            FROM $tables[docs] d
            LEFT JOIN $tables[docs_items] di ON di.doc_id = d.doc_id
            LEFT JOIN $tables[docs_user_info] ui ON ui.doc_info_id = d.doc_info_id
            WHERE di.product_id = '$product_id' AND ui.customer_id = '$customer_id' AND d.type = 'O'
        ");

        return empty($result) ? FALSE : TRUE;
    }

    return FALSE;
}

// generate 'where' string for search query
// used for review list in admin
function cw_review_generate_where_search($review_data, $mandatory_where=array()) {
    global $tables;

    $where = "";
    $where_items = array();

    if (trim($review_data['search']['substring']) != "") {
        $substring = trim($review_data['search']['substring']);

        if ($review_data['search']['by_customer']) {
            $wher_item = "$tables[products_reviews].remote_ip LIKE '%$substring%'";
            $wher_item .= " OR $tables[products_reviews].email LIKE '%$substring%'";
            $wher_item .= " OR $tables[customers].customer_id LIKE '%$substring%'";
            $wher_item .= " OR $tables[customers].email LIKE '%$substring%'";
            $wher_item .= " OR $tables[customers_addresses].firstname LIKE '%$substring%'";
            $wher_item .= " OR $tables[customers_addresses].lastname LIKE '%$substring%'";
            $where_items[] = $wher_item;
        }

        if ($review_data['search']['by_sku']) {
            $where_items[] = "$tables[products].productcode LIKE '%$substring%'";
        }

        if ($review_data['search']['by_status']) {
            $statuses = array(
                cw_get_langvar_by_name('lbl_pending', NULL, FALSE, TRUE),
                cw_get_langvar_by_name('lbl_approved', NULL, FALSE, TRUE),
                cw_get_langvar_by_name('lbl_declined', NULL, FALSE, TRUE)
            );
            $keys = array();

            foreach ($statuses as $key => $status) {

                if (stripos($status, $substring) !== FALSE) {
                    $keys[] = $key;
                }
            }

            if (!empty($keys)) {
                $wher_item = "$tables[products_reviews].status = ";
                $wher_item .= implode(" OR $tables[products_reviews].status = ", $keys);
                $where_items[] = $wher_item;
            }
            else {
                $where_items[] = "$tables[products_reviews].status = -1";
            }
        }

        if ($review_data['search']['by_flag']) {
            $testimonials = cw_get_langvar_by_name('lbl_testimonials', NULL, FALSE, TRUE);
            $stop_list = cw_get_langvar_by_name('lbl_stop_list', NULL, FALSE, TRUE);

            if (stripos($testimonials, $substring) !== FALSE) {
                $where_items[] = "$tables[products_reviews].testimonials = 1";
            }
            else {
                $where_items[] = "$tables[products_reviews].testimonials = -1";
            }

            if (stripos($stop_list, $substring) !== FALSE) {
                $where_items[] = "$tables[products_reviews].stoplist = 1";
            }
            else {
                $where_items[] = "$tables[products_reviews].stoplist = -1";
            }
        }
    }

    if (!empty($review_data['ids'])) {
        $review_ids = explode(',', $review_data['ids']);

        if (is_array($review_ids)) {

            foreach ($review_ids as $review_id) {
                $where_items[] = "$tables[products_reviews].review_id = $review_id";
            }
        }
    }

    if (!empty($mandatory_where)) {
        $where = "WHERE " . implode(" AND ", $mandatory_where);
    }

    if (!empty($where_items)) {

        if (!empty($mandatory_where)) {
            $where .= " AND (" . implode(" OR ", $where_items) . ")";
        }
        else {
            $where = "WHERE " . implode(" OR ", $where_items);
        }
    }

    return $where;
}

// delete review and rating value
function cw_review_delete_review($review_id) {
    global $tables;

    cw_load('attributes');

    $product_id = cw_query_first_cell("
        SELECT product_id
        FROM $tables[products_reviews]
        WHERE review_id = $review_id
    ");

    db_query("DELETE FROM $tables[products_votes] WHERE review_id = '$review_id'");
    db_query("DELETE FROM $tables[products_reviews] WHERE review_id = '$review_id'");

    cw_review_recalculate_avg_rating($product_id);
}

// Post-hook on cw_attributes_delete()
// delete votes when attribute deleted
function cw_review_delete_product_votes($attribute_id) {
    global $tables;

    $product_ids = cw_query("
        SELECT DISTINCT product_id
        FROM $tables[products_votes]
        WHERE attribute_id = '$attribute_id'
    ");

    db_query("DELETE FROM $tables[products_votes] WHERE attribute_id='$attribute_id'");

    if (!empty($product_ids)) {

        foreach ($product_ids as $product_id) {
            cw_review_recalculate_avg_rating($product_id['product_id'], $attribute_id);
        }
    }

    return $attribute_id;
}

// calculate average rating
function cw_review_recalculate_avg_rating($product_id, $attribute_id=0) {
    global $tables;

    cw_load('attributes');

    $query = "";
    $field = "rating";

    if (!empty($attribute_id)) {
        $query = " AND attribute_id = " . $attribute_id;
    }
    else {
        $attribute_id = cw_query_first_cell("
            SELECT attribute_id
            FROM $tables[attributes]
            WHERE field = 'rating' AND addon = 'estore_products_review'
        ");
    }

    // recalculate avg rating
    $avg = cw_query_first_cell("
        SELECT avg(vote_value)
        FROM $tables[products_votes]
        WHERE product_id = '$product_id' $query AND vote_value>0
    ");

    // the average rating is stored with the attributes
    $item_id = $product_id;
    $item_type = 'P';
    cw_attributes_cleanup($item_id, $item_type, null, $attribute_id);
    cw_array2insert(
        'attributes_values',
        array(
            'item_id' => $item_id,
            'item_type' => $item_type,
            'attribute_id' => $attribute_id,
            'value' => $avg
        )
    );
}

// prepare and send reminder email by cron
function cw_review_prepare_and_send_reminder($time, $last_run) {
    global $tables, $config;

    $return_str = "";

    // Follow up email notifications
    if (!empty($config['estore_products_review']['amount_days_order_review_product'])) {
        $count_days = $config['estore_products_review']['amount_days_order_review_product'];
        $count_seconds = SECONDS_PER_DAY * $count_days;
        $check_seconds = cw_core_get_time() - $count_seconds;
        $check_seconds_max = $check_seconds - SECONDS_PER_DAY*10; 
        $status_remind = $config['estore_products_review']['order_status_start_reminder'];

        $not_reviews_products = cw_query($s="
            SELECT DISTINCT di.product_id, d.doc_id
            FROM $tables[docs] d
            LEFT JOIN $tables[docs_items] di ON di.doc_id = d.doc_id
            INNER JOIN $tables[products] p ON p.product_id=di.product_id AND p.status=1
            LEFT JOIN $tables[docs_user_info] ui ON ui.doc_info_id = d.doc_info_id
            LEFT JOIN $tables[products_reviews] r ON r.product_id = di.product_id
            LEFT JOIN $tables[products_reviews_reminder] rr ON rr.product_id = di.product_id
                AND ui.customer_id = rr.customer_id
            WHERE d.status_change < $check_seconds AND d.status_change > $check_seconds_max AND d.type = 'O' AND d.status = '$status_remind'
                AND ui.customer_id <> 0 AND r.product_id IS NULL
                AND rr.product_id IS NULL AND di.product_id IS NOT NULL
            ORDER BY d.doc_id LIMIT 5 
        ");

        if (!empty($not_reviews_products) && is_array($not_reviews_products)) {
            $ordered_products = array();

            foreach ($not_reviews_products as $_r) {
                $ordered_products[$_r['doc_id']][] = $_r['product_id'];
                $_customer_id = cw_query_first_cell("SELECT ui.customer_id FROM $tables[docs_user_info] ui INNER JOIN $tables[docs] d ON ui.doc_info_id = d.doc_info_id WHERE d.doc_id='".$_r['doc_id']."' LIMIT 1");
                $return_str .= "Reminder sent on order #".$_r['doc_id']." to customer #".$_customer_id." on product #".$_r['product_id']."\n";
            }

            cw_review_send_order_review_reminder_email($ordered_products);
        }
    }

    // Return log record
    return $return_str;
}

function cw_review_generate_one_time_login_key($customer_id) {
    global $tables;

    cw_load('user');

    $key = cw_user_generate_password();
    $key_hashed = md5($key);  
    cw_array2insert('products_reviews_login_keys', array('login_key'=>$key_hashed, 'date_created'=>time(), 'customer_id'=>$customer_id));
    return $key;
}

// send order review reminder email for customer
function cw_review_send_order_review_reminder_email($ordered_products) {
    global $tables, $config, $smarty, $current_location;
    global $test_order_review_reminder;

    cw_load('email', 'user', 'doc', 'product', 'accounting', 'web');

    cw_log_add('review_send_order_review_reminder_email', array('products'=>$ordered_products));

    if (!empty($ordered_products) && is_array($ordered_products)) {

        foreach ($ordered_products as $doc_id => $product_ids) {
            $doc_data = cw_call('cw_doc_get', array($doc_id, 65535));  
            $user_info = $doc_data['userinfo'];

            $customer_id = cw_query_first_cell("SELECT ui.customer_id FROM $tables[docs_user_info] ui INNER JOIN $tables[docs] d ON ui.doc_info_id = d.doc_info_id WHERE d.doc_id='$doc_id' LIMIT 1");

            if ($test_order_review_reminder) 
                print("Customer #$customer_id ".$user_info['email']."<br>");

            if (empty($user_info['email']) || empty($product_ids)) {
                continue;
            }

            $key = cw_review_generate_one_time_login_key($customer_id);  
            $link = $current_location . "/index.php?target=product";
            $link .= "&action=review_product&review_key=$key";

            $alinks = array();

            foreach ($product_ids as $product_id) {
                $product_name = cw_query_first_cell("SELECT product FROM $tables[products] WHERE product_id = $product_id");

                if ($test_order_review_reminder)
                    print("Product name $product_name<br>");

                if (empty($product_name)) {
                    continue;
                }
                $alinks[$product_id] = array(
                    'product_id' => $product_id,
                    'link' => $link . "&product_id=$product_id",
                    'product_name' => $product_name
                );

                cw_array2insert(
                    'products_reviews_reminder',
                    array(
                        'product_id' => $product_id,
                        'customer_id' => $customer_id,
                        'ctime' => cw_core_get_time()
                    ),
                    TRUE
                );
            }

            if (!empty($alinks)) {

                if ($doc_data['info']['layout_id'])
                   $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
                else
                   $layout = cw_call('cw_web_get_layout', array('docs_'.$doc_data['type']), true);

                $smarty->assign('layout_data', $layout);
                $smarty->assign('info', $doc_data['info']);

                $doc_data_products = array();
                foreach($doc_data['products'] as $prd) {
                    if (in_array($prd['product_id'], array_keys($alinks)))
                        $doc_data_products[] = $prd;
                }
                $smarty->assign('products', $doc_data_products);

                $smarty->assign('doc', $doc_data);
                $smarty->assign('order', $doc_data);

                cw_log_add('review_send_order_review_reminder_email', array('doc_id'=>$doc_id, 'customer_id'=>$customer_id, 'alinks'=>$alinks, 'email'=>$user_info['email']));

                $smarty->assign('reminders', $alinks);
                cw_call('cw_send_mail', array(
                    $config['Company']['site_administrator'],
                    $user_info['email'],
                    'addons/estore_products_review/mail/reminder_subj.tpl',
                    'addons/estore_products_review/mail/reminder_body.tpl',
                    null, false, false, array(), $test_order_review_reminder
                ));
            }
        }
    }

    return TRUE;
}

// change search query params for order search
function cw_review_prepare_search_orders($data, $docs_type, &$fields, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
    global $tables;

    if (
        $data['search_sections']['tab_search_orders_advanced']
        && $docs_type == 'O'
        && $data['estore']['no_review']
    ) {
        $query_joins['products_reviews'] = array(
            'on' => "$tables[products_reviews].product_id = $tables[docs_items].product_id",
        );
        $where[] = "$tables[products_reviews].product_id IS NULL";
        $where[] = "$tables[docs_items].product_id IS NOT NULL";
    }
}

// add layout lng value for place review column
function cw_review_get_product_layout_elements() {
    $return = cw_get_return();

    if (!empty($return) && is_array($return)) {
        $return['review_note'] = 'lbl_review';
    }

    return $return;
}

// add layout value for place review column
function cw_review_doc_get($doc_id, $info_type=0) {
    global $tables, $current_location;

    $return = cw_get_return();

    if (
        !empty($return)
        && $return['type'] == 'O'
        && (!empty($return['products']) || !empty($return['giftcerts']))
        && !empty($return['userinfo']['customer_id'])
    ) {
        if (!empty($return['giftcerts']) && is_array($return['giftcerts'])) {

            foreach ($return['giftcerts'] as $key => $giftcert) {
                $return['giftcerts'][$key]['review_note'] = '';
            }
        }

        if (!empty($return['products']) && is_array($return['products'])) {
            foreach ($return['products'] as $key => $product) {
                $product_id = $product['product_id'];
                $customer_id = $return['userinfo']['customer_id'];

                $result = cw_query_first_cell("
                    SELECT review_id
                    FROM $tables[products_reviews]
                    WHERE product_id='$product_id' AND customer_id='$customer_id'
                ");

                $review_note = '';

                if (!$result) {
                    $review_note = "<a href='$current_location/index.php?target=product&product_id=$product_id' target='_blank'>Place review</a>";
                }
                $return['products'][$key]['review_note'] = $review_note;
            }
        }

        return new EventReturn($return);
    }

    return $return;
}

// return TRUE or FALSE if place review or voting avail by settings
function cw_review_avail_by_settings($product_id, $customer_id, $extended_review_customer_id) {
    global $config;

    $avail_by_settings = (
        $config['estore_products_review']['writing_reviews'] == 'A'
        || (
            $config['estore_products_review']['writing_reviews'] == 'R'
            && !empty($customer_id)
        )
        || (
            $config['estore_products_review']['writing_reviews'] == 'P'
            && cw_review_check_is_purchasers($product_id, $customer_id)
        )
        || (
            $extended_review_customer_id
            && $config['estore_products_review']['writing_reviews'] != 'N'
        )
    );

    return $avail_by_settings;
}

// return query by some settings
function cw_review_get_where_query_by_settings($customer_id) {
    global $config;

    $user_ip = $_SERVER["REMOTE_ADDR"] . '_' . $_SERVER["HTTP_X_FORWARDED_FOR"] . '_' . $_SERVER["HTTP_CLIENT_IP"];

    $where = "remote_ip='$user_ip'";
    if (
        $config['estore_products_review']['writing_reviews'] == 'P'   // Allow to purchasers
        || $config['estore_products_review']['writing_reviews'] == 'R'    // Allow to registered
        || ($customer_id && $config['estore_products_review']['writing_reviews'] != 'N')
    ) {
        $where = "customer_id='$customer_id'";
    }

    return $where;
}

// add new attribute type 'rating'
function cw_review_attributes_get_types($params, $return) {
    $return[] = 'rating';
    $return[] = 'global_rating';

    return $return;
}

function cw_review_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables;

    if ($delete_all == true) {
        db_query('TRUNCATE TABLE ' . $tables['products_reviews_reminder']);
    } else {
        $product_id = (int) $product_id;
        if (!empty($product_id)) {
            $product_id_condition = 'product_id = "' . $product_id . '"';
            db_query('DELETE FROM ' . $tables['products_reviews_reminder'] . ' WHERE ' . $product_id_condition);
        }
    }

}

function cw_review_get_comments_count($product_id) {
	global $tables;
	return cw_query_first_cell("
		SELECT count(r.review_id)
		FROM $tables[products_reviews] r
		LEFT JOIN $tables[products_votes] v ON r.review_id = v.review_id
		WHERE r.product_id='$product_id' AND r.stoplist = 0 AND r.status = 1 AND IF(v.attribute_id IS NULL, 0, v.attribute_id) = 0
		ORDER BY r.ctime DESC
	");
}

function cw_review_add_new_review_manual($review_new, $product_id) {
    global $tables;

    if (!empty($review_new['message'])) {
        $review_new['product_id'] = $product_id;
        $review_new['testimonials'] = ($review_new['addto'] == 'testimonials' ? 1 : 0);
        $review_new['stoplist'] = ($review_new['addto'] == 'stoplist' ? 1 : 0);
        unset($review_new['addto']);
        $review_new['ctime'] = time();
        $inserted_id = cw_array2insert("products_reviews", $review_new);

        if (!empty($review_new['vote'])) {
            $vote_new = array();
            $vote_new['product_id'] = $product_id;
            $vote_new['vote_value'] = $review_new['vote'];
            $vote_new['customer_id'] = 0;
            $vote_new['remote_ip'] = "";
            $vote_new['review_id'] = $inserted_id;
            cw_array2insert("products_votes", $vote_new);
            cw_review_recalculate_avg_rating($product_id);
        }

        if ($product_id) {
            $attribute_value_id = cw_query_first_cell(
                "SELECT ad.attribute_value_id
                FROM $tables[attributes_default] ad
                LEFT JOIN $tables[attributes] a ON a.attribute_id = ad.attribute_id
                WHERE a.field = 'has_review' AND a.addon = 'estore_products_review'"
            );
            cw_func_call('cw_attributes_save_attribute', array('item_id' => $product_id, 'item_type' => 'P', 'attributes' => array('has_review' => $attribute_value_id)));
        }
    } 
    return $inserted_id;
}


function cw_review_get_global_review($where="", $having="", $orderby="", $limit="", $count_query=TRUE, $vote_where="") {
    global $tables;
/*
    if ($count_query) {
        $select = "count($tables[products_reviews].review_id)";
    }
    else {
        $select = "$tables[products_reviews].*, avg($tables[products_votes].vote_value) as vote_value,
            $tables[products].productcode as sku, $tables[customers].email as real_email,
            $tables[customers_addresses].firstname, $tables[customers_addresses].lastname";
    }
*/
    $select = "$tables[products_reviews].*, avg($tables[products_votes].vote_value) as vote_value, $tables[customers_addresses].firstname, $tables[customers_addresses].lastname";

    $query = "
        SELECT $select
        FROM $tables[products_reviews]
        LEFT JOIN $tables[products_votes] ON $tables[products_reviews].review_id = $tables[products_votes].review_id
            AND $tables[products_votes].vote_value > 0 $vote_where
        LEFT JOIN $tables[customers] ON $tables[products_reviews].customer_id = $tables[customers].customer_id
        LEFT JOIN $tables[customers_addresses] ON $tables[products_reviews].customer_id = $tables[customers_addresses].customer_id
            AND $tables[customers_addresses].customer_id <> 0 AND $tables[customers_addresses].main = 1
        WHERE $tables[products_reviews].product_id = 0 and $tables[products_reviews].status=1 
        $where
        group by $tables[products_reviews].review_id
        $having 
        $orderby
        $limit
    ";

    if ($count_query)
        $result = count(cw_query($query));
    else
        $result = cw_query($query);

    return $result;
}


function cw_review_get_quick_global_info () {
    global $tables, $config;

    $all_feedback_count = cw_call('cw_review_get_global_review',array()); 

    $result = array();

    if ($all_feedback_count > 0) {
        $positive_lim = " having vote_value >= '".$config['estore_products_review']['positive_review_treshold']."'";
        $result['positive_count'] = cw_call('cw_review_get_global_review', array('', $positive_lim));

        $result['positive_percent'] = 100*$result['positive_count']/$all_feedback_count;

        $negative_lim = " having vote_value <= '".$config['estore_products_review']['negative_review_treshold']."'";
        $result['negative_count'] = cw_call('cw_review_get_global_review',array('', $negative_lim)); 
        $result['neutral_count'] = $all_feedback_count - $result['positive_count'] - $result['negative_count'];

        $three_months_lim = " and $tables[products_reviews].ctime >= '".(time() - 91*24*3600)."'";
        $all_feedback_3_months_count = cw_call('cw_review_get_global_review',array($three_months_lim));

        if ($all_feedback_3_months_count > 0) { 
  
            $result['positive_3_months_count'] = cw_call('cw_review_get_global_review',array($three_months_lim,$positive_lim));

            $result['negative_3_months_count'] = cw_call('cw_review_get_global_review',array($three_months_lim,$negative_lim));

            $result['neutral_3_months_count'] = $all_feedback_3_months_count - $result['positive_3_months_count'] - $result['negative_3_months_count'];

            $one_month_lim = " and $tables[products_reviews].ctime >= '".(time() - 30*24*3600)."'";
            $all_feedback_1_month_count = cw_call('cw_review_get_global_review',array($one_month_lim));  
            if ($all_feedback_1_month_count > 0) {
                $result['positive_1_month_count'] = cw_call('cw_review_get_global_review',array($one_month_lim,$positive_lim));

                $result['negative_1_month_count'] = cw_call('cw_review_get_global_review',array($one_month_lim,$negative_lim));

                $result['neutral_1_month_count'] = $all_feedback_1_month_count - $result['positive_1_month_count'] - $result['negative_1_month_count'];
            }
        }

        $rating_cond = "type='global_rating'";

        list ($attributes, $nav) = cw_func_call(
            'cw_attributes_search',
            array('data'=>array('active'=>1,'is_show'=>1,'sort_field'=>'orderby')), array('where' => array($rating_cond))
        );

        if ($attributes) {
            $result['ratings'] = array();
            foreach ($attributes as $att) {
                $att_vote = 
                    cw_query_first_cell("SELECT avg($tables[products_votes].vote_value) as vote_value 
                                         FROM $tables[products_reviews]
                                         LEFT JOIN $tables[products_votes] ON 
                                             $tables[products_reviews].review_id = $tables[products_votes].review_id
                                             AND $tables[products_votes].vote_value > 0 AND $tables[products_votes].attribute_id='$att[attribute_id]'
                                         WHERE $tables[products_reviews].product_id = 0 and $tables[products_reviews].status=1"); 

                $result['ratings'][] = 
                    array('attribute' => $att,
                          'avg' => 100*($att_vote/5.00), 
                          'total_count' => cw_query_first_cell("SELECT count(*) 
                                         FROM $tables[products_reviews]
                                         LEFT JOIN $tables[products_votes] ON 
                                             $tables[products_reviews].review_id = $tables[products_votes].review_id
                                             AND $tables[products_votes].vote_value > 0 AND $tables[products_votes].attribute_id='$att[attribute_id]'
                                         WHERE $tables[products_reviews].product_id = 0 and $tables[products_reviews].status=1")); 
            }    
        }
    } else {
        return array('positive_count' => 0, 
                 'positive_percent'   => 0, 
                 'neutral_count'      => 0, 
                 'negative_count'     => 0);
    }
    return $result;

}

function cw_review_name_initials($str) {
    $ret = '';
    foreach (explode(' ', $str) as $word)
        $ret .= strtoupper($word[0]);
    return $ret;
}
