<?php
if (!defined('APP_START')) die('Access denied');

cw_load('cart');

if ($eventid) {
	#
	# Get the event information
	#
	$event_data = cw_query_first("SELECT $tables[giftreg_events].*, $tables[customers].title as creator_title, $tables[customers].firstname, $tables[customers].lastname FROM $tables[giftreg_events], $tables[customers] WHERE $tables[customers].customer_id=$tables[giftreg_events].customer_id AND event_id='$eventid'");

	if (empty($event_data) or $event_data['status'] == "D")
		cw_header_location("index.php?target=error_message&error=page_not_found");

	$location[] = array($event_data['title'], "");

	if ($event_data['guestbook'] == "Y")
		$event_data['gb_count'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[giftreg_guestbooks] WHERE event_id='$eventid'");

	if ($event_data['customer_id'] != $customer_id && $event_data['status'] == "P" && $access_status[$eventid] != "Y") {
		#
		# Private events
		#
		$has_access = 1;
		if (!empty($customer_id)) {
			$email = cw_query_first_cell("SELECT email FROM $tables[customers] WHERE customer_id='$customer_id'");
			$has_access = cw_query_first_cell("SELECT COUNT(*) FROM $tables[giftreg_maillist] WHERE recipient_email='".addslashes($email)."' AND event_id='$eventid'");
		}
		else {
			$has_access = 0;
		}

		if ($has_access == 0)
			cw_header_location("index.php?target=error_message&error=giftreg_is_private");

		$access_status[$eventid] = "Y";
	}

	$smarty->assign('eventid', $eventid);
	$smarty->assign('event_data', $event_data);

	#
	# Get the products information
	#
	$wl_raw = cw_query("select wishlist_id, product_id, amount, amount_purchased, options from $tables[wishlist] where event_id='$eventid' AND product_id>0");

	if (is_array($wl_raw)) {
		foreach ($wl_raw as $index=>$wl_product) {
			$wl_raw[$index]['options'] = unserialize($wl_product['options']);
			$wl_product['amount_requested'] = $wl_product['amount'];
			if( $wl_product['amount'] > $wl_product['amount_purchased'] && $mode == "friend_wl" )
				$wl_raw[$index]['amount'] = $wl_product['amount'] - $wl_product['amount_purchased'];
		}

		$wl_products = cw_products_from_scratch($wl_raw, $user_account['membership_id'], true);

	}

	$smarty->assign('wl_products', $wl_products);

		$wl_raw = cw_query("select wishlist_id, amount, amount_purchased, object from $tables[wishlist] where event_id='$eventid' AND product_id=0");
		if (is_array($wl_raw)) {
			foreach ($wl_raw as $k=>$v) {
				$object = unserialize($v['object']);
				$wl_giftcerts[] = cw_array_merge($v, $object);
			}

			$smarty->assign('wl_giftcerts', $wl_giftcerts);
		}
}

$mode = "event_details";

$smarty->assign('mode', $mode);

$smarty->assign('main', "giftreg");
?>
