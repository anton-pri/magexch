<?php
global $config, $customer_id, $user_account, $user_address;

$customer_id = &cw_session_register('customer_id');
$identifiers = &cw_session_register('identifiers', array());

if (!empty($_GET['operate_as_user']) && !empty($identifiers['A'])) {
	// operate as user
	$tmp_user = cw_query_first("SELECT usertype, email 
    FROM $tables[customers] 
    WHERE customer_id='$operate_as_user'");
	if ($tmp_user['usertype'] == 'C') {
        $identifiers['C'] = array (
            'customer_id' => $operate_as_user,
        );
        $customer_id = $operate_as_user;

        cw_load('cart');
        $cart =& cw_session_register('cart', array());
        $cart       = cw_user_get_stored_cart($customer_id);
        $userinfo   = cw_user_get_info($customer_id);
        $products   = cw_call('cw_products_in_cart',array($cart, $userinfo));
        $cart       = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

	$search_data = &cw_session_register('search_data', array());
	unset($search_data['orders']);	

        cw_add_top_message('You operate as user '.$tmp_user['email'], 'W');
	} else {
        cw_add_top_message('You cannot operate as this user', 'E');
    }
    
    cw_header_location('index.php');
}

$smarty->assign_by_ref('identifiers', $identifiers);

if (defined('AREA_TYPE') && !empty($identifiers[AREA_TYPE]))
    $customer_id = $identifiers[AREA_TYPE]['customer_id'];
else
    $customer_id = 0;

cw_include('include/check_userdata.php');

if($current_area == 'A')
    $merchant_password = &cw_session_register("merchant_password");

$is_merchant_password = '';

if ($customer_id && AREA_TYPE != 'Y') {
	if($current_area == 'A') {
		if(($config['mpassword'] != md5($merchant_password) && $merchant_password) || (!$merchant_password))
			$merchant_password = '';
		else
			$is_merchant_password = 'Y';
	}
}

if ($addons['now_online'] && !defined('IS_ROBOT'))
	cw_include('addons/now_online/users_online.php');

$smarty->assign('is_merchant_password', $is_merchant_password);

if ($customer_id && $current_area == 'C') {
    $real_usertype = cw_user_get_real_usertype();
    $smarty->assign('real_usertype', $real_usertype);
}

if (in_array(AREA_TYPE, array('A', 'P', 'G')) && $customer_id) {
    cw_load('auth');
    cw_include('include/auth.php');

    global $accl;
    $accl = unserialize(cw_query_first_cell($sql="select level from $tables[access_levels] where membership_id='".$user_account['membership_id']."' and area='".AREA_TYPE."'"));

    $sc = array();
    if (is_array($accl)) $sc = cw_auth_rec($arr_auth[AREA_TYPE], $accl, false);

    $is_deny = false;
    if (!in_array($target, array('error_message', 'home', 'login', 'ajax', 'tabs'))) {
        $is_deny = true;
        $fl1 = true;
        foreach($sc as $v)
        if ($v['target'] == $target && $fl1) {
# kornev, when the modify is allowed, all of the actions are allowed.
# kornev, temp modification
            if (!$accl['__'.$v['key']] && is_array($v['actions']) && in_array($action, $v['actions'])) {
                $is_deny = true;
            }
            elseif ($v['par']) {
                $str = 'if (!('.$v['par'].')) $is_deny=true; else {$is_deny=false; $fl1=false;}';
                eval($str);
            }
            else
                $is_deny = false;
        }
    }

/*
    if ($is_deny && in_array(AREA_TYPE, array('P', 'G', 'A'))) {
        $top_message = array('content' => cw_get_langvar_by_name('txt_access_denied'), 'type' => 'E');
        cw_header_location('index.php', true, true);
    }
*/

    $smarty->assign('accl', $accl);
    $smarty->assign('acc', true); #acc - use access levels
#on modify
#    cw_auth_perm($target, $arr_auth_m, $ma);
#on delete
#    cw_auth_perm($target, $arr_auth_d, $ma);

    if (in_array(AREA_TYPE, array('A'))) {
        $user_account['additional_info'] = cw_query_first("select can_change_company_id, company_id from $tables[customers_customer_info] where customer_id='$customer_id' ");
    }

}
elseif (AREA_TYPE == 'C') {
    cw_include('include/check_usercart.php');
}
elseif (AREA_TYPE == 'B') {
}

$smarty->assign('user_account', $user_account);
