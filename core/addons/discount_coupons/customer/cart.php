<?php
$cart = &cw_session_register('cart', array());
$message = array('content'=>'','type'=>'I');

global $action;

if (empty($cart)) return;

if (!empty($cart['info']['coupon']) && cw_discount_coupons_is_valid($cart['info']['coupon'], $cart['products']) > 0)
	$cart['info']['coupon'] = '';

if ($action == 'add_coupon') {
	$my_coupon = cw_discount_coupons_is_valid($coupon, $cart['products']);

	if ($my_coupon == 2)
		$message['content'] = cw_get_langvar_by_name("err_bad_coupon_warehouse_msg");
	elseif ($my_coupon == 5)
		$message['content'] = cw_get_langvar_by_name("txt_coupon_already_used_by_customer");
	elseif ($my_coupon == 3)
		$message['content'] = cw_get_langvar_by_name("txt_overstepping_coupon_order_total");
	elseif ($my_coupon == 4)
		$message['content'] = cw_get_langvar_by_name("txt_cart_not_contain_coupon_products");
	elseif ($my_coupon == 1)
		$message['content'] = cw_get_langvar_by_name("err_bad_coupon_code_msg");
	elseif($my_coupon == 0)
		$cart['info']['coupon'] = $coupon;

	if($my_coupon > 0) {
        $cart['info']['coupon'] = '';
		$message['type'] = 'E';
	}

    cw_add_top_message($message['content'], $message['type']);

    $action = 'update';

}
elseif ($action == 'unset_coupons') {
	$cart['info']['coupon'] = '';
    $action = 'update';
}
