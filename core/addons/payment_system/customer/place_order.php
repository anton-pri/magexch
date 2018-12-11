<?php
cw_load('cart', 'cart_process', 'crypt', 'doc', 'accounting', 'attributes');

$cart = &cw_session_register('cart');
$payment_cc_fields = &cw_session_register('payment_cc_fields');
$top_message = &cw_session_register('top_message');
$user_address = &cw_session_register('user_address');

if (!isset($card_expire) && $card_expire_Month)
    $card_expire = $card_expire_Month.substr($card_expire_Year, 2);
if ($card_valid_from_Month)
    $card_valid_from = $card_valid_from_Month.substr($card_valid_from_Year, 2);
$request_prepared['card_expire'] = $_POST['card_expire'] = $card_expire;
$request_prepared['card_valid_from'] = $_POST['card_valid_from'] = $card_valid_from;

if ($REQUEST_METHOD != 'POST')
	cw_header_location($current_location.'/index.php?target=cart&mode=checkout');

$payment_data = cw_func_call('cw_payment_get_methods', $cart['info']);
if (empty($payment_data)) cw_header_location('index.php?target=cart&mode=checkout');

$rules = array();
if ($payment_data['payment_type'] == 'cc') {
    $rules = array(
        'card_type' => '',
        'card_name' => '',
        'card_number' =>  '',
        'card_expire' => '',
        'card_cvv2' => '',
    );
    if ($payment_data['ccinfo'] || (!$payment_data['ccinfo'] && $config['General']['enable_manual_cc_cvv2'] == 'Y'))
        $rules['card_cvv2'] = '';
    if ($config['General']['uk_oriented_ccinfo'] == 'Y') {
        $rules['card_valid_from'] = '';
        $rules['card_issue_no'] = '';
    }
}
elseif ($payment_data['payment_type'] == 'ch') {
    $rules = array(
        'check_name' => '',
        'check_ban' => '',
        'check_brn' => '',
        'check_number' => '',
    );
}
elseif ($payment_data['payment_type'] == 'dd') {
    $rules = array(
        'debit_name' => '',
        'debit_bank_account' => '',
        'debit_bank_number' => '',
        'debit_bank_name' => '',
    );
}

if ($rules) {
    $fillerror = cw_error_check($_POST, $rules);
    if ($fillerror) {
        $top_message = array('content' => $fillerror, 'type' => 'E');
        cw_header_location('index.php?target=cart&mode=checkout');
    }
    $ord_tmp = array();
    foreach ($rules as $k => $tmp) {
//        $ord_tmp[] = $k.": ".stripslashes($request_prepared[$k]);
          $ord_tmp[] = $k.": --not saved--"; 
    }
    $order_details = implode("\n", $ord_tmp);
}

cw_payment_header();

global $userinfo, $app_catalogs;
if ($customer_id) {
	$userinfo = cw_user_get_info($customer_id, 65535);
}
if (empty($userinfo) || cw_is_cart_empty($cart)) {
    cw_header_location($current_location . '/index.php?target=error_message&error=ccprocessor_baddata');
}

$userinfo = cw_array_merge($userinfo, $_POST);
$userinfo = cw_array_merge($userinfo, $user_address);

$order_type = 'O';

if ($action == 'request_for_quote') {
	$order_type = 'I';
}

$secure_oid = &cw_session_register("secure_oid");
if (!$secure_oid) {
    $doc_ids = cw_func_call('cw_doc_place_order', array('order_type'=>$order_type, 'order_status' => 'I', 'order_details' => $order_details, 'customer_notes' => $customer_notes, 'userinfo' => $userinfo, 'prefix' => $config[$payment_data['processor']]['prefix']));

	if (!$doc_ids) {
		cw_header_location('index.php?target=error_message&error=product_in_cart_expired');
	}

	if (!empty($cart['info']['quote_doc_id'])) {
		// Change invoice status to "paid"
		$doc_id = $cart['info']['quote_doc_id'];
		$status = 'C';	
		cw_call('cw_doc_change_status', array($doc_id, $status));
		
		// Make relation
		$doc_data = cw_call('cw_doc_get', array($doc_id));

	    foreach ($doc_ids as $relation_doc_id) {
	    	
	    	if ($doc_data['products']) {
			    foreach ($doc_data['products'] as $v) {
			    	$rel_item_id = $v['item_id'];
			    	
			        if (!is_numeric($relation_doc_id)) {
			        	$relation_doc_id = cw_doc_make_relation_doc('O', $doc_id, $rel_item_id, $v['amount'], 1);
			        }
			        else {
			        	cw_doc_make_relation($relation_doc_id, $rel_item_id, $v['amount']);
			        }
			        cw_doc_recalc($relation_doc_id);
			    }
		    }

	    	if ($doc_data['giftcerts'] && is_numeric($relation_doc_id)) {
	    		cw_doc_make_related_doc($doc_id, $relation_doc_id);
		    }
	    }
	}

	$secure_oid = $doc_ids;
}
else {
    $doc_ids = $secure_oid;
}

cw_session_save();

if ($action == 'request_for_quote') {
	cw_call('cw_doc_change_status', array($doc_ids, "Q"));
	$request = $app_catalogs['customer'] . "/index.php?target=order-message&doc_ids=" . implode(",", $doc_ids);
	$cart = $secure_oid = array();
	cw_save_customer_cart($customer_id, $cart);
	cw_header_location($request);
}
else {
	$return = cw_func_call('cw_payment_run_processor', array('payment_data' => $payment_data, 'doc_ids' => $doc_ids, 'userinfo' => $userinfo));
	if ($return && $payment_data['payment_type'] == 'cc') {
	    $a = strlen($userinfo['card_cvv2']);
	    $return['cvvmes'] = (($a)?($a." digit(s)"):("not set"))." / ";
	}
	$return = cw_call('cw_payment_check_results', array($return));
	cw_call('cw_payment_stop', array($return));
}

exit;
