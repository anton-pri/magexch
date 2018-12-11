<?php
cw_load('mail');

function cw_wl_get_product($wlitem) {
    cw_load('product');
    $p = cw_wl_get_item($wlitem);
    return cw_product_get(array('id'=>$p['product_id']));
}

function cw_wl_get_item($wlitem) {
    global $tables, $customer_id;

    if (empty($customer_id)) {
        $customer_wishlist = cw_session_register('customer_wishlist');
        return $customer_wishlist[$wlitem];
    }
    else {
        return cw_query_first("SELECT * FROM $tables[wishlist] WHERE wishlist_id='$wlitem'");
    }
}

function cw_gift_get_events($customer_id) {
    global $tables;
    $events_list = cw_query("SELECT * FROM $tables[giftreg_events] WHERE customer_id='$customer_id' ORDER BY event_date, title");
    if (is_array($events_list))
        foreach($events_list as $k=>$v) {
            $events_list[$k]['emails'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[giftreg_maillist] WHERE event_id='$v[event_id]'");
            $events_list[$k]['products'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[wishlist] WHERE customer_id='$customer_id' AND event_id='$v[event_id]'");
        }

    return $events_list;
}

function cw_giftcert_check($gc_id) {
	global $cart;

	if (empty($gc_id))
		return 1;

	if ($cart['info']['applied_giftcerts']) {
		foreach ($cart['info']['applied_giftcerts'] as $k => $v)
			if (strcasecmp($v['giftcert_id'], $gc_id) == 0)
				return 2;
	}

	return 0;
}

#
# This function gather the gift certificate data
#
function cw_giftcert_data($gc_id, $unblock=false) {
	global $config, $tables;

	if ($unblock) {
		# Unblock GC after $config['estore_gift']['gc_blocking_period'] minutes of blocking
		$gc_blocking_period = $config['estore_gift']['gc_blocking_period'] * 60;
		db_query("UPDATE $tables[giftcerts] SET status='A' WHERE gc_id='$gc_id' AND status='B' AND block_date+'$gc_blocking_period' < '".time()."' AND debit > '0'");
	}

	$gc = cw_query_first("SELECT * FROM $tables[giftcerts] WHERE gc_id='$gc_id' AND status='A' AND debit > '0'");

	# If Gift certificate does not exists
	if (empty($gc))
		return false;

	return $gc;

}

#
# This function applies the Gift certificate to the cart
#
function cw_giftcert_apply($gc_data) {
	global $cart, $tables;

	$cart['info']['applied_giftcerts'][] = array(
		'giftcert_id' => $gc_data['gc_id'],
		'giftcert_cost' => $gc_data['debit']
	);

	# Block the Gift certificate
	db_query("UPDATE $tables[giftcerts] SET status='B', block_date='".time()."' WHERE gc_id='$gc_data[gc_id]'");

	if ($gc_data['debit'] < $cart['info']['total'])
		return false;

	return true;

}

#
# Remove Gift certificate from the cart
#
function cw_giftcert_unset($gc_id) {
	global $cart, $tables;

	if (empty($cart['info']['applied_giftcerts']) || !is_array($cart['info']['applied_giftcerts']))
		return false;

	foreach ($cart['info']['applied_giftcerts'] as $k=>$v) {
		if ($v['giftcert_id'] != $gc_id)
			continue;

		$cart['info']['total'] = $cart['info']['total'] + $v['giftcert_cost'];
		$cart['info']['giftcert_discount'] -= $v['giftcert_cost'];

		db_query("UPDATE $tables[giftcerts] SET status='A' WHERE gc_id='$gc_id'");
		unset($cart['info']['applied_giftcerts'][$k]);
	}

	$cart['info']['applied_giftcerts'] = array_values($cart['info']['applied_giftcerts']);

	return true;
}

function cw_gift_get_wishlist($customer_id, $is_md5 = false) {
    global $tables, $current_area;

    $cond_customer_id = $is_md5?"md5($tables[wishlist].customer_id)":"$tables[wishlist].customer_id";
    $status = cw_core_get_required_status($current_area);

    $products = cw_query($sql="select $tables[wishlist].* from $tables[wishlist], $tables[products] as p
	where $cond_customer_id='$customer_id' AND $tables[wishlist].event_id='0'
	AND $tables[wishlist].product_id = p.product_id and p.status in ('".implode("', '", $status)."')");
    return cw_gift_prepare_products($products);
}

function cw_gift_get_giftreg_wishlist($customer_id, $event_id) {
    global $tables;

    if (empty($customer_id)) {

        if ($event_id == '') {
            $event_id = 0;
        }

        $customer_wishlist = cw_session_register('customer_wishlist');
        $products = array();

        if (is_array($customer_wishlist)) {

            foreach ($customer_wishlist as $k => $v) {

                if ($v['event_id'] == $event_id) {
                    $products[$k] = $v;
                }
            }
        }
    }
    else {
        $products = cw_gift_get_giftreg_wishlist_database ($customer_id, $event_id);
    }

    return cw_gift_prepare_products($products);
}

function cw_gift_get_giftreg_wishlist_database ($customer_id, $event_id) {
    global $tables;
    global $items_per_page, $page, $sort, $sort_direction, $items_per_page_targets;
    global $smarty, $target, $action, $mode;

    $wishlist_items_nav = array();

    if (isset($items_per_page))
        $wishlist_items_nav['items_per_page'] = $items_per_page;

    if (isset($page))
        $wishlist_items_nav['page'] = $page;

    $page = 1;

    if (isset($sort))
        $wishlist_items_nav['sort_field'] = $sort;

    if (isset($sort_direction))
        $wishlist_items_nav['sort_direction'] = $sort_direction;

    $items_per_page_targets['wishlist'] = PHP_INT_MAX;

    $items_fields = array("$tables[wishlist].*");

    $items_from_tbls = array('wishlist');

    $items_where = array("$tables[wishlist].customer_id='$customer_id' and $tables[wishlist].event_id='$event_id'");

    $items_groupbys = array();
    $items_having = array();

    if ($wishlist_items_nav['sort_field'] == 'forsale')
        $items_orderbys = array("$tables[wishlist].product_id".($wishlist_items_nav['sort_direction']?' DESC':' ASC'));

    $items_count_query = cw_db_generate_query(array('count(*)'), $items_from_tbls, $items_query_joins, $items_where, $items_groupbys, $items_having, null);

    $items_total_items = cw_query_first_cell($items_count_query);

    $items_qry = cw_db_generate_query($items_fields, $items_from_tbls, $items_query_joins, $items_where, $items_groupbys, $items_having, $items_orderbys);

    $items_per_page_targets['wishlist'] = $wishlist_items_nav['items_per_page'];

    $navigation = cw_core_get_navigation($target, $items_total_items, $wishlist_items_nav['page']);
    $navigation['script'] = "index.php?target=$target&action=$action&mode=$mode";
    $smarty->assign('navigation', $navigation);

    if ($items_total_items > 0) {
        $wishlist_items = cw_query($items_qry." LIMIT $navigation[first_page], $navigation[objects_per_page]");
    }

    if (!empty($wishlist_items)) {
        $smarty->assign('wishlist_items_nav', $wishlist_items_nav);
    }

    return $wishlist_items;
    
}

function cw_gift_get_giftcert_wishlist($customer_id) {
    global $tables;
    
    $wl_giftcerts = array();

    if (empty($customer_id)) {
        return $wl_giftcerts;
    }

    $wl_raw = cw_query("select wishlist_id, amount, amount_purchased, object from $tables[wishlist] WHERE customer_id='$customer_id' AND event_id='0' AND product_id='0'");

	if (is_array($wl_raw)) {

		foreach ($wl_raw as $k=>$v) {
			$object = unserialize($v['object']);
			$wl_giftcerts[] = cw_array_merge($v, $object);
		}
	}
	
	return $wl_giftcerts;
}


function cw_gift_prepare_products($products) {
    global $addons, $user_account;

    if (is_array($products))
    foreach ($products as $k=>$product) {
        if (!empty($product['options'])) {
            $products[$k]['options'] = unserialize($product['options']);
            if (!empty($products[$k]['options']) && $addons['product_options'])
                $products[$k]['variant_id'] = cw_get_variant_id($products[$k]['options'], $product['product_id']);
        } 

        $products[$k]['amount_requested'] = $product['amount'];
        if ($product['amount'] > $product['amount_purchased'])
            $products[$k]['amount'] = $product['amount'] - $product['amount_purchased'];
    }

    return cw_products_from_scratch($products, $user_account, true);
}

function cw_gift_get_confirmation_code() {
    global $tables;

    while (true) {
        $confid = strtoupper(md5(uniqid(rand())));
        if (!cw_query_first_cell("select count(*) from $tables[giftreg_maillist] where confirmation_code='$confid'")) return $confid;
    }
}

function cw_gift_add2wishlist($product_id, $amount, $product_options = null) {
    global $addons, $tables;
    global $user_account, $customer_id;

    if (is_array($amount)) $amount = array_sum($amount);

    $_options = array();
    if ($addons['product_options']) {
        if (is_array($product_options) && cw_check_product_options($product_id, $product_options))
            $product_options = cw_array_map('stripslashes', $product_options);
        else
            $product_options = cw_get_default_options($product_id, $amount, $user_account['membership_id']);

        if (!is_array($product_options)) unset($product_options);
        else $_options = addslashes(serialize($product_options));
    }

    $added_product = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 3));

    $oamount = 0;
    $wlid = FALSE;
    $object = '';
    // Save to session for not loggin in customers
    if (empty($customer_id)) {
        $customer_wishlist = &cw_session_register('customer_wishlist');

            if (
                (empty($added_product['distribution']) || !$addons['egoods'])
                && !empty($customer_wishlist)
            ) {
                $wlid = cw_gift_get_session_wishlist_id($product_id, $_options);

                if ($wlid !== FALSE) {
                    $oamount = $customer_wishlist[$wlid]['amount'];
                }
            }

        if ($wlid !== FALSE) {
            $customer_wishlist[$wlid]['amount'] = $amount + $oamount;
        }
        else {
            $customer_wishlist[] = array(
                'product_id' => $product_id,
                'amount' => $amount,
                'amount_purchased' => 0,
                'options' => $_options,
                'object' => $object,
                'event_id' => 0
            );
            $index = count($customer_wishlist)-1;
            $customer_wishlist[$index]['wishlist_id'] = $index;
        }
    }
    else {
            if (empty($added_product['distribution']) || !$addons['egoods'])
                $oamount = cw_query_first_cell("SELECT amount FROM $tables[wishlist] WHERE customer_id='$customer_id' AND product_id='$product_id' AND options='$_options' AND event_id='0'");
            $wlid = cw_query_first_cell("SELECT wishlist_id FROM $tables[wishlist] WHERE customer_id='$customer_id' AND product_id='$product_id' AND options='$_options' AND event_id='0'");

        if (!empty($wlid))
            cw_array2update('wishlist', array('amount' => $amount+$oamount), "wishlist_id='$wlid'");
        else {
            cw_array2insert('wishlist',
                array(
                    'customer_id' => $customer_id,
                    'product_id' => $product_id,
                    'amount' => $amount,
                    'options' => $_options,
                    'object' => $object
                )
            );
        }
    }
}

function cw_gift_payment_get_methods($params, $return) {

	if ($return['processor'] == 'payment_gift_certificate') {
        $return['ccinfo'] = false;
        $return['code'] = 3;
        $return['payment_template'] = 'addons/estore_gift/gc_payment_template.tpl';
    }

    return $return;
}

function cw_gift_payment_run_processor($params, $return) {
    $cart = &cw_session_register('cart');
    extract($params);

    if ($params['payment_data']['processor'] != 'payment_gift_certificate') {

    	if (!empty($cart['info']['applied_giftcerts'])) {
    		cw_gift_giftcerts_process($cart, $doc_ids);
    	}
    	return $return;
    }

    global $app_catalogs;
    
    $payment_id = $payment_data['payment_id'];
    $gc_id 		= trim($userinfo['gc_id']);

    $top_message = &cw_session_register('top_message');
   
	// Check non existing/updated Gift certificate in the applied_giftcerts array
	if (!empty($cart['info']['applied_giftcerts'])) {
	    $_invalid_gcs = cw_gift_check_applied_giftcerts($payment_id);

	    if (
	        !empty($_invalid_gcs)
	        && is_array($_invalid_gcs)
	    ) {
	        $top_message = array(
	            'type' => 'E',
	            'content' => cw_get_langvar_by_name('err_gc_invalid_gcs', array('invalid_gcs' => implode(', ', $_invalid_gcs)))
	        );	
	        cw_header_location($app_catalogs['customer'] . "/index.php?target=cart&mode=checkout&err=fields&payment_id=" . $payment_id);
	    }
	}

    if ($cart['info']['total'] == 0) {
    	cw_gift_giftcerts_process($cart, $doc_ids);
    	return array('code' => 3);
    }

    $gc_error_code = cw_giftcert_check($gc_id);

    if ($gc_error_code == 1) {
	    $top_message = array('content' => cw_get_langvar_by_name("err_filling_form"), 'type' => 'E');
    	cw_header_location($app_catalogs['customer'] . "/index.php?target=cart&mode=checkout&err=fields&payment_id=" . $payment_id);
    }

    if ($gc_error_code == 2) {
	    $top_message = array('content' => cw_get_langvar_by_name('err_gc_used'), 'type' => 'E');
	    cw_header_location($app_catalogs['customer'] . '/index.php?target=cart&mode=checkout&err=fields&payment_id=' . $payment_id);
    }

    $gc = cw_giftcert_data($gc_id, true);

    if (empty($gc)) {
	    $top_message = array('content' => cw_get_langvar_by_name('err_gc_not_found'), 'type' => 'E');
        cw_header_location($app_catalogs['customer'] . '/index.php?target=cart&mode=checkout&err=fields&payment_id=' . $payment_id);
    }

    $gc_applied = cw_giftcert_apply($gc);

    if (!$gc_applied) {
		$top_message = array('content' => cw_get_langvar_by_name('txt_gc_not_enough_money'), 'type' => 'E');
        cw_header_location($app_catalogs['customer'] . '/index.php?target=cart&mode=checkout&err=fields&payment_id=' . $payment_id);
    }

    $cart['info']['applied_giftcerts'][count($cart['info']['applied_giftcerts'])-1]['giftcert_cost'] = $cart['info']['total'];
    $cart['info']['giftcert_discount'] += $cart['info']['total'];
    $cart['info']['total'] = 0;

    if ($cart['orders']) {

	    foreach($cart['orders'] as $k => $v) {
	        $cart['orders'][$k]['info']['total'] = 0;
    	}
    }

    cw_gift_giftcerts_process($cart, $doc_ids);

    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

    return array('code' => 3);
}

function cw_gift_send_gc($from_email, $giftcert) {
    global $smarty, $config;

    $giftcert["purchaser_email"] = $from_email;
    $giftcert['message'] = stripslashes($giftcert['message']);
    $smarty->assign("giftcert", $giftcert);

    cw_call('cw_send_mail', array($from_email, $giftcert["recipient_email"], 'mail/giftcert/subj.tpl', 'mail/giftcert/body.tpl'));
}

/* Event handlers */
function cw_gift_get_menu_list(&$main_menu_list) {
	$main_menu_list[] = array(
		"path" => "index.php?target=gifts&mode=wishlist",
		"name" => cw_get_langvar_by_name("lbl_wishlist")
	);
	$main_menu_list[] = array(
		"path" => "index.php?target=gifts&mode=events",
		"name" => cw_get_langvar_by_name("lbl_gift_registry"),
        'need_login' => true,
	);
	$main_menu_list[] = array(
		"path" => "index.php?target=gifts&mode=giftcert",
		"name" => cw_get_langvar_by_name("lbl_gift_certificate"),
        'need_login' => true,
	);
}

/* Hooks */
function cw_gift_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables;

    if ($delete_all === true) {
        db_query("delete from ".$tables['wishlist']);
    }
    db_query("delete from $tables[wishlist] where product_id='$product_id'");
}

function cw_gift_doc_update_item($doc_id, &$product) {
    global $tables;

    if ($product['wishlist_id']) {
        db_query("UPDATE $tables[wishlist] SET amount_purchased=amount_purchased+'$product[amount]' WHERE wishlist_id='$product[wishlist_id]'");
    }
}

function cw_gift_doc_update($doc_id, $current_order) {
    global $tables, $cart, $config;

	if (!empty($cart['giftcerts']) && empty($current_order['products'])) {

		// Save bought certificates
		foreach($cart['giftcerts'] as $gk => $giftcert) {

			if (empty($giftcert['gc_id'])) {
				$gcid = cw_gift_get_gcid();
				$insert_data = array(
					'gc_id'               => $gcid,
					'doc_id'              => $doc_id,
					'purchaser'           => addslashes($giftcert['purchaser']),
					'recipient'           => addslashes($giftcert['recipient']),
					'send_via'            => $giftcert['send_via'],
					'recipient_email'     => @$giftcert['recipient_email'],
					'recipient_firstname' => addslashes(@$giftcert['recipient_firstname']),
					'recipient_lastname'  => addslashes(@$giftcert['recipient_lastname']),
					'recipient_address'   => addslashes(@$giftcert['recipient_address']),
					'recipient_city'      => addslashes(@$giftcert['recipient_city']),
					'recipient_county'    => @$giftcert['recipient_county'],
					'recipient_state'     => addslashes(@$giftcert['recipient_state']),
					'recipient_country'   => addslashes(@$giftcert['recipient_country']),
					'recipient_zipcode'   => addslashes(@$giftcert['recipient_zipcode']),
					'recipient_phone'     => addslashes(@$giftcert['recipient_phone']),
					'message'             => addslashes($giftcert['message']),
					'amount'              => $giftcert['amount'],
					'debit'               => $giftcert['amount'],
					'status'              => 'P',
					'add_date'            => time()
				);

				if ($giftcert['send_via'] == 'P') {
					$insert_data['tpl_file'] = $giftcert['tpl_file'];
				}

				cw_array2insert('giftcerts', $insert_data);
				unset($insert_data);
				$cart['giftcerts'][$gk]['gc_id'] = $gcid;
			}
		}
	}
}

/**
 * Get the gift certificate printable template
 */
function cw_gift_get_templates($base_dir) {
    $basedir = $base_dir . '/addons/estore_gift';
    $result = array();

    $dp = opendir($basedir);
    if ($dp !== false) {
        while ($file = readdir($dp)) {
            if (!preg_match('!^template_.*\.tpl$!S', $file))
                continue;

            if (!is_file($basedir . '/' . $file))
                continue;

            $result[] = $file;
        }

        closedir($dp);
    }

    return $result;
}

/**
 * Check if the gift certificate template is wrong file
 */
function cw_gift_wrong_template($gc_template) {
    global $app_dir, $app_skin_dir;

    $gc_templates_dir = $app_dir . $app_skin_dir . '/addons/estore_gift/';

    return (
        empty($gc_template)
        || !cw_allowed_path($gc_templates_dir, $gc_templates_dir . $gc_template)
        || !in_array($gc_template, cw_gift_get_templates($app_dir . $app_skin_dir))
    );
}

/**
 * Get unique id for giftcerts
 */
function cw_gift_get_gcid($is_uniq=CHECK_UNIQ_ID) {   
    global $tables;

    $stop_counter = 0;
    while (true) {
        $gcid = substr(strtoupper(md5(uniqid(rand()))), 0, 16);
        $stop_counter++;

        // Exit conditions from infinite while loop
        if (
            $is_uniq == CHECK_UNIQ_ID
            || $stop_counter > 10
            || cw_query_first_cell("SELECT COUNT(gc_id) FROM $tables[giftcerts] WHERE gc_id='$gcid'") == 0
        ) {
            break;
        }
    }

    return $gcid;
}

/**
 * Check applied giftcerts in the cart
 */
function cw_gift_check_applied_giftcerts($payment_id) {
    global $cart, $tables;

    $invalid_gcs = array();

    if (!empty($cart['info']['applied_giftcerts']) && is_array($cart['info']['applied_giftcerts'])) {

        // Check if the payment_giftcert payment_method is active
        if (!cw_query_first_cell("SELECT payment_id FROM $tables[payment_methods] WHERE payment_id='$payment_id' AND active='1'")) {
            foreach ($cart['info']['applied_giftcerts'] as $v)
                $invalid_gcs[] = $v['giftcert_id'];

            return $invalid_gcs;
        }

        foreach ($cart['info']['applied_giftcerts'] as $v) {
            // Check if the applied_giftcert exists
            $_gc = cw_query_first("SELECT * FROM $tables[giftcerts] WHERE gc_id='$v[giftcert_id]' AND status='B' AND debit > '0'");

            if (empty($_gc))
                $invalid_gcs[] = $v['giftcert_id'];
        }
    }

    return $invalid_gcs;
}

// Process order giftcerts
function cw_gift_giftcerts_process($order, $doc_ids) {
	global $tables;
	
	if (!empty($order['info']['applied_giftcerts'])) {
		// Search for enabled to applying GC
		$flag = true;

		foreach ($order['info']['applied_giftcerts'] as $k=>$v) {
			$res = cw_query_first("SELECT gc_id FROM $tables[giftcerts] WHERE gc_id='$v[giftcert_id]' AND debit>='$v[giftcert_cost]'");

			if (!$res['gc_id']) {
				$flag = false;
				break;
			}
		}

		// Decrease debit for applied GC
		if (!$flag) return false;

		$giftcert_str = '';

		foreach ($order['info']['applied_giftcerts'] as $k=>$v) {
			$giftcert_str = join("*", array($giftcert_str, "$v[giftcert_id]:$v[giftcert_cost]"));

			db_query("UPDATE $tables[giftcerts] SET debit=debit-'$v[giftcert_cost]' WHERE gc_id='$v[giftcert_id]'");
			db_query("UPDATE $tables[giftcerts] SET status='A' WHERE debit>'0' AND gc_id='$v[giftcert_id]'");
			db_query("UPDATE $tables[giftcerts] SET status='U' WHERE debit<='0' AND gc_id='$v[giftcert_id]'");
		}
		
		// Save giftcert used in order
		foreach ($doc_ids as $doc_id) {
			$doc_info_id = cw_query_first_cell("SELECT doc_info_id FROM $tables[docs] WHERE doc_id='$doc_id'");
			
			if (is_numeric($doc_info_id)) {
				cw_array2update(
					'docs_info', 
					array(
						"giftcert_discount" => $order['info']['giftcert_discount'],
						"giftcert_ids" => $giftcert_str
					), 
					"doc_info_id='$doc_info_id'"
				);
			}
		}
	}
}

function cw_gift_get_session_wishlist_id($product_id, $_options) {
    $customer_wishlist = &cw_session_register('customer_wishlist');

    if (!empty($customer_wishlist)) {

        foreach ($customer_wishlist as $_k => $_v) {

            if (
                $_v['product_id'] == $product_id
                && $_v['options'] == $_options
            ) {
                return $_k;
            }
        }
    }

    return FALSE;
}

function cw_gift_delete_session_wishlist($wlitem) {
    $customer_wishlist = &cw_session_register('customer_wishlist');
    unset($customer_wishlist[$wlitem]);

    if (!empty($customer_wishlist)) {
        $tmp_wishlist = array();

        foreach ($customer_wishlist as $_w) {
            $tmp_wishlist[] = $_w;
            $index = count($tmp_wishlist)-1;
            $tmp_wishlist[$index]['wishlist_id'] = $index;
        }
        $customer_wishlist = $tmp_wishlist;
    }
}

function cw_gift_update_session_wishlist($wlitem, $eventid, $quantity) {
    $customer_wishlist = &cw_session_register('customer_wishlist');

    if (isset($customer_wishlist[$wlitem])) {
        $customer_wishlist[$wlitem]['event_id'] = $eventid;
        $customer_wishlist[$wlitem]['amount'] = $quantity;
    }
}

function cw_gift_on_login($customer_id, $area, $on_register) {
    global $tables;

    $customer_wishlist = &cw_session_register('customer_wishlist');

    if (!empty($customer_wishlist) && is_array($customer_wishlist)) {

        foreach ($customer_wishlist as $_cw) {
            $wishlist = cw_query_first(
                "SELECT wishlist_id, amount FROM $tables[wishlist]
                WHERE customer_id='$customer_id' AND product_id='$_cw[product_id]'
                    AND options='$_cw[options]' AND event_id='0'"
            );

            if (!empty($wishlist)) {
                cw_array2update(
                    'wishlist',
                    array(
                        'amount' => $wishlist['amount'] + $_cw['amount']
                    ),
                    "wishlist_id = '$wishlist[wishlist_id]'"
                );
            }
            else {
                cw_array2insert('wishlist',
                    array(
                        'customer_id' => $customer_id,
                        'product_id' => $_cw['product_id'],
                        'amount' => $_cw['amount'],
                        'options' => $_cw['options'],
                        'object' => $_cw['object']
                    )
                );
            }
        }

        cw_session_unregister('customer_wishlist');
    }
}
