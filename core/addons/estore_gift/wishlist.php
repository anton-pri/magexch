<?php
cw_load('product', 'cart', 'mail');
global $top_message, $cart, $userinfo;
$redirect_mode = (!empty($script)?$script:$mode);

if (!isset($eventid)) {
    $eventid=$event_id;
}

if ($action == 'delete') {

    if (empty($customer_id)) {
        cw_gift_delete_session_wishlist($wlitem);
    }
    else {
        db_query("delete from $tables[wishlist] WHERE customer_id='$customer_id' AND wishlist_id='$wlitem' AND event_id='$event_id'");
    }
    $top_message = array('type' => 'I', 'content' => 'Wishlist item has been updated');
}

if ($action == 'update') {

    if ($quantity > 0) {

        if (empty($customer_id)) {
            cw_gift_update_session_wishlist($wlitem, $eventid, $quantity);
        }
        else {
            db_query("UPDATE $tables[wishlist] SET amount='$quantity', event_id='$eventid' WHERE wishlist_id='$wlitem' and  customer_id='$customer_id'");
        }
    }
    $top_message = array('type' => 'I', 'content' => 'Wishlist item has been updated');
}

if ($action == 'wlclear') {

    if (empty($customer_id)) {
        cw_session_unregister('customer_wishlist');
    }
    else {
        db_query("delete from $tables[wishlist] where customer_id='$customer_id' AND event_id='$event_id'");
    }
    $top_message = array('type' => 'I', 'content' => 'Wishlist has been cleared');
}

if ($action == 'entire_list') {
    $wishlist = cw_gift_get_wishlist($customer_id);
    $smarty->assign('wl_products', $wishlist);

    $smarty->assign('wlid', md5($customer_id));
    $smarty->assign('userinfo', $userinfo);
    cw_call('cw_send_mail', array($user_account['email'], $friend_email, 'mail/wishlist/sendall2friend_subj.tpl', 'mail/wishlist/sendall2friend.tpl', false));

    $top_message = array('type' => 'I', 'content' => cw_get_langvar_by_name('txt_wishlist_sent'));
}

if ($action == 'add2cart') {
    global $product_id, $amount, $action, $result;
    define('DO_NOT_REDIRECT_CART', true);
    $action = 'add';
    $wl = cw_wl_get_item($wlitem);
    $product_id = $wl['product_id'];
    $amount = $wl['amount'];
    cw_include('customer/cart.php');
    $top_message = array('type' => 'I', 'content' => 'Wishlist item has been added to cart');
}

if ($action == 'addgc2wl') {
	global $tables, $customer_id;

    // Add Gift Certificate to the wish list
    if (!empty($gcindex)) {
        db_query("UPDATE $tables[wishlist] SET object='" . addslashes(serialize($giftcert)) . "' WHERE wishlist_id='$gcindex'");
        $eventid = cw_query_first_cell("SELECT event_id FROM $tables[wishlist] WHERE wishlist_id='$gcindex'");

        if ($eventid > 0) {
            cw_header_location("index.php?target=gifts&mode=events&event_id=$eventid");
        } else {
            cw_header_location("index.php?target=gifts&mode=wishlist");
        }
    }
    else {
        db_query("insert into $tables[wishlist] (customer_id, amount, options, object) values ('$customer_id', '1', '', '".addslashes(serialize($giftcert))."')");
    }
    cw_header_location("index.php?target=gifts&mode=wishlist");
}

if ($mode == 'wl2cart' && $wlitem) {
	// if in cart products from invoice, then new giftcert can't be added
	if (!empty($cart['info']['quote_doc_id'])) {
		$top_message['type'] = "E";
		$top_message['content'] = cw_get_langvar_by_name("err_add_product_to_cart_with_quote");
		cw_header_location('index.php?target=gifts&mode=wishlist');
	}
	define('DO_NOT_REDIRECT_CART', true);
	$action = 'add';
    // Add to cart product from wish list
    $wl = cw_wl_get_item($wlitem);

    if ($wl) {
		// Add gift certificate to the cart
		$giftcert = unserialize($wl['object']);

		if (!isset($cart['giftcerts'])) {
			$cart['giftcerts'] = array();
		}
		$cart['giftcerts'][] = cw_array_merge($giftcert, array('wishlistid' => $wlitem));
		$top_message = array('type' => 'I', 'content' => 'Wishlist item has been added to cart');

        $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    	$cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
    }
}

// Redirect
if (!empty($action)) {
    cw_header_location("index.php?target=$target&mode=$redirect_mode&event_id=$event_id&js_tab=$js_tab");
}

if ($mode == 'friends')
    $wishlist = cw_gift_get_wishlist($wlid, true);
else
    $wishlist = cw_gift_get_giftreg_wishlist($customer_id, $event_id);

if ($mode != 'friends') {
    $events_list = cw_call('cw_gift_get_events',array($customer_id));
    $smarty->assign('events_list', $events_list);
}
$smarty->assign('event_id', $event_id);

$wl_giftcerts = cw_gift_get_giftcert_wishlist($customer_id);
if (!empty($wl_giftcerts)) {
	$smarty->assign('wl_giftcerts', $wl_giftcerts);
}

$smarty->assign('from_quote', (!empty($cart['info']['quote_doc_id']) ? 1 : 0));
$smarty->assign('wl_products', $wishlist);
$smarty->assign('allow_edit', 1);

$location[] = array(cw_get_langvar_by_name('lbl_wish_list'), '');
$smarty->assign('current_main_dir', 'addons/estore_gift');
$smarty->assign('current_section_dir','');
$smarty->assign('main', 'wishlist');
