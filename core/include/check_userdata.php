<?php
	global $smarty, $user_account, $customer_id, $user_address;

    $user_address = &cw_session_register('user_address', array());

    $user_account['membership_id'] = 0;

	if ($customer_id) {
	    $user_account = cw_user_get_user_account($customer_id, "status='Y' and usertype in ('".(AREA_TYPE == 'C'?"C', 'R":AREA_TYPE)."')");
	    $user_address['current_address'] = cw_user_get_address_by_type('current');
	    $user_address['main_address'] = cw_user_get_address_by_type('main');
	
	    $user_account = array_merge($user_account, (array)$user_address['current_address']);	
	
	    if (!$user_account['customer_id']) {
	        cw_unset($identifiers, AREA_TYPE);
	        $customer_id = 0;
	        $user_account = array();
	    }
	
	    // Force redirect to change password page
	    if (
	    	$user_account["change_password"] 
	    	&& !in_array($target, array('change_password', 'ajax', 'acc_manager', 'user'), true) 
	    	&& !defined('IS_AJAX')
	    ) {
	        cw_header_location('index.php?target=change_password&redirect=Y');
	    }	
	}
	
    $user_address['current_address'] = cw_user_get_address_by_type('current');
    $user_address['main_address'] = cw_user_get_address_by_type('main');

    $smarty->assign('user_address', $user_address);
	$smarty->assign('customer_id', $customer_id);
	$smarty->assign('usertype', $current_area);
	$smarty->assign('current_area', $current_area);
	
