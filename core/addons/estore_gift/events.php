<?php
if (!defined('APP_START')) die('Access denied');

if ($action == 'move_product' && !empty($wlitem)) {
	$customer_id_cond = "customer_id='$customer_id' AND wishlist_id='$wlitem'";
	$quantity = intval($quantity);
	if ($quantity > 0) {

		$wlitem_data = cw_query_first("SELECT * FROM $tables[wishlist] WHERE $customer_id_cond");

		if ($wlitem_data['product_id'] == 0) {
		#
		# Move the Gift Certificate
		#
			db_query("UPDATE $tables[wishlist] SET event_id='$eventid' WHERE $customer_id_cond");
		}
		else {
		#
		# Move product to the wish list for other event
		#
			$quantity = min($quantity, $wlitem_data['amount']);
			$rest_quantity = $wlitem_data['amount'] - $quantity;

			$same_item_exists = cw_query_first("SELECT wishlist_id FROM $tables[wishlist] WHERE customer_id='$customer_id' AND product_id='$wlitem_data[product_id]' AND options='$wlitem_data[options]' AND object='$wlitem_data[object]' AND event_id='$eventid'");
			if ($same_item_exists) {
			#
			# If this product already exists in the destination wish list
			#
				db_query("UPDATE $tables[wishlist] SET event_id='$eventid', amount=amount+$quantity WHERE wishlist_id='$same_item_exists[wishlist_id]'");
				if ($rest_quantity == 0)
				#
				# Delete product from the source wish list
				#
					db_query("DELETE FROM $tables[wishlist] WHERE $customer_id_cond");
				else
					db_query("UPDATE $tables[wishlist] SET amount='$rest_quantity' WHERE $customer_id_cond");
			}
			else {
			#
			# If this item is not exists - insert it
			#
				foreach ($wlitem_data as $k=>$v) {
					if ($k == "amount")
						$v = $quantity;
					if ($k == "event_id")
						$v = $eventid;
					if ($k != "wishlist_id") {
						$fields[] = $k;
						$values[] = "'".addslashes($v)."'";
					}
				}
				db_query("INSERT INTO $tables[wishlist] (".implode(",", $fields).") VALUES (".implode(",", $values).")");
				db_query("UPDATE $tables[wishlist] SET amount='$rest_quantity' WHERE $customer_id_cond");
			}
		}

		if (cw_session_is_registered("mail_data"))
			cw_session_unregister("mail_data");
	}
	if ($wlitem_data['event_id'] == 0)
		cw_header_location("index.php?target=cart&mode=wishlist");
	else
		cw_header_location("index.php?target=gifts&eventid=$wlitem_data[event_id]&mode=events");
}

$events_list =  cw_call('cw_gift_get_events',array($customer_id));

$smarty->assign('events_list', $events_list);
$smarty->assign('events_lists_count', (is_array($events_list) ? count($events_list) : 0));


$location[] = array(cw_get_langvar_by_name('lbl_giftreg_events_list', ''));
$smarty->assign('current_main_dir', 'addons/estore_gift');
$smarty->assign('current_section_dir','');
$smarty->assign('main', 'events');
?>
