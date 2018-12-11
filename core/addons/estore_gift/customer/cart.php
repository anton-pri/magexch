<?php
// Apply giftcert to the cart (AJAX)
if ($action == 'apply_gc') {
	global $app_catalogs;

	$cart = &cw_session_register('cart', array());
	if (empty($cart)) return;

	$top_message = &cw_session_register('top_message');
    
    $gc_id = trim($gc_id);
    $gc_error_code = cw_giftcert_check($gc_id);

    if ($gc_error_code == 1) {
	    $top_message = array('content' => cw_get_langvar_by_name("err_filling_form"), 'type' => 'E', 'anchor' => 'coupon');
    }

    if ($gc_error_code == 2) {
	    $top_message = array('content' => cw_get_langvar_by_name('err_gc_used'), 'type' => 'E', 'anchor' => 'coupon');
    }

    if (!$gc_error_code) {
	    $gc = cw_giftcert_data($gc_id, true);

	    if (empty($gc)) {
		    $top_message = array('content' => cw_get_langvar_by_name('err_gc_not_found'), 'type' => 'E', 'anchor' => 'coupon');
	    }
	    else {	
		    $gc_applied = cw_giftcert_apply($gc);
		
		    if (!$gc_applied) {
				$top_message = array('content' => cw_get_langvar_by_name('txt_gc_not_enough_money'), 'type' => 'E', 'anchor' => 'coupon');
		    }
	    }
    }

    cw_session_save();
    global $action;
    $action = 'update';

    if (!$is_ajax) {
    	cw_header_location("index.php?target=cart&mode=checkout");
    }
}

// Unset giftcert (AJAX or GET)
if (
	($action == 'unset_gc' || $mode == 'unset_gc')
 	&& $gc_id
 ) {
    cw_giftcert_unset($gc_id);
    
    global $action;
    $action = 'update';
    
    if (!$is_ajax) {
    	cw_header_location("index.php?target=cart");
    }
}

if ($action == 'add2wl') {
    cw_gift_add2wishlist($product_id, $amount, $product_options);
    cw_header_location('index.php?target=gifts&mode=wishlist');
}
