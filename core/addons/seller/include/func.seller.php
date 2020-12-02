<?php

/**
 * Get shares of the orders distributed between admin and sellers in case the payments go directly to sellers.
 * Shares are returned in absolute amounts, not as percentage
 * 
 * @param array $cart
 * 
 * @return
 * array(
 *  [0] => <admin_share_amount>,
 *  [seller1_id] => <seller_share_amount>,
 *  [seller2_id] => <seller_share_amount>,
 * )
 */
function cw_seller_get_payment_shares($cart) {
    global $config;
    
    $shares = array(0=>0.00);
    
    foreach ($cart['orders'] as $order) {
        $seller = cw_call('cw_user_get_info', array($order['warehouse_customer_id'], 65535));
        $seller['custom_fields'] = cw_user_get_custom_fields($seller['customer_id'],0,'','field');
    
        if (empty($seller) || $seller['usertype'] == 'A') $seller['customer_id'] = 0;
       
        if ($config['seller']['seller_enable_admin_commission_share'] == 'Y') 
            $admin_commision = ($seller['custom_fields']['admin_commission_rate']>0?$seller['custom_fields']['admin_commission_rate']:$config['seller']['seller_admin_commission_rate']);
        else
            $admin_commission = 0.00;    
        
        $shares[$seller['customer_id']] += $_seller_share = (1-$admin_commision/100)*$order['info']['total'];
        $shares[0] += $order['info']['total']-$_seller_share;
    }

    cw_log_add('seller_shares', $shares);

    return $shares;
    
}

// start hooks
function cw_seller_get_info($creation_customer_id) {

    $seller = array();

    if (!empty($creation_customer_id)) {
        $user_data = cw_call('cw_user_get_info', array($creation_customer_id, 1|32));
        $userphoto = cw_call('cw_user_get_avatar', array($creation_customer_id));
        if ($user_data['usertype'] == seller_area_letter || $user_data['usertype'] == 'C' || $user_data['usertype'] == 'S') {
            $address = empty($user_data['main_address']) ? $user_data['current_address'] : $user_data['main_address'];
            $seller = $user_data;
            $seller['id'] = $user_data['customer_id'];
            $seller['name'] = trim($address['firstname'] . ' ' . $address['lastname']);
            $seller['address'] = $address;//trim($address['countryname'] . ', ' . $address['statename'] . ', ' . $address['city']);
            $seller['avatar'] = $userphoto;
            $seller['system_info'] = $user_data['system_info'];
        }
     }
     return $seller; 
}


function cw_seller_product_get($params, $return) {
    global $tables;

    if (!empty($params['id'])) {
        if(!empty($return['system']['creation_customer_id']))
            $return['seller'] = cw_call('cw_seller_get_info', array($return['system']['creation_customer_id']));
    }
    return $return;
}

function cw_seller_product_search($params, $return) {
    global $tables, $customer_id;

    $return['query_joins']['products_system_info'] = array(
        'parent' => 'products',
        'on' => "$tables[products_system_info].product_id = $tables[products].product_id",
        'is_inner' => 1,
    );

    $return['where'][] = "$tables[products_system_info].creation_customer_id = '$customer_id'";
    
    
    // TODO: This watchers/wishlist_id conditions for printdrop only. Must be deleted from this common seller addon.
    $return['fields'][] = "COUNT(*) as watchers";
    $return['fields'][] = "IFNULL($tables[wishlist].wishlist_id, 0) as wishlist_id";
    $return['query_joins']['wishlist'] = array(
        'parent' => 'products',
        'on' => "$tables[wishlist].product_id = $tables[products].product_id and $tables[wishlist].customer_id != '$customer_id'"   
    );
    $return['groupbys'][] = "$tables[wishlist].product_id";


    return $return;
}

function cw_seller_auth_check_security_targets() {
    global $target, $mode;

    if (!in_array($target, array('index', 'login', 'acc_manager'))) {
        return true;
    }

    return false;
}

function cw_seller_product_is_pending($params) {
	if ($params['value'] == 2) return true;
	return false;
}

function cw_seller_product_update_status($product_id, $status) {
	global $tables, $smarty, $user_account;
	
	$product = cw_func_call('cw_product_get', array('id'=>$product_id,'info_type'=>0));
	
	if ($product['status']==2 && $status!=2) {
		$smarty->assign('status', $status);
		$smarty->assign('product', $product);
		$owner = cw_call('cw_user_get_info', array($product['seller']['id']));
		$smarty->assign('seller', $owner);
		
		cw_load('mail');
		cw_call('cw_send_mail', array($user_account['email'], $owner['email'], 'addons/seller/mail/approved_subj.tpl', 'addons/seller/mail/approved.tpl'));
	}
	
}

/**
 * Replace warehouse_id to seller customer id
 * 
 * @see old replace hook for cw_cart_summarize
 * @note OLD HOOK NOTATION used
 * 
 * @return array $cart
 */
function cw_seller_cart_summarize($params, $return) {
    
    
    foreach ($return['orders'] as $k=>$v) {
   
        reset($v['products']);
        $_product = current($v['products']);
        $seller_id =  isset($_product['seller']['id'])?$_product['seller']['id']:$_product['owner_id'];
        $return['orders'][$k]['warehouse_customer_id'] = $seller_id;
        
    }
    
    return $return;
}

/**
 * Add seller email to list of order notifications
 * 
 * @see cw_doc_order_status_emails
 * 
 * @return array $emails
 */
function cw_seller_doc_order_status_emails($doc_data, $status_code, $area_name) {
    global $tables;
    
    $emails = cw_get_return();
    
    if ($area_name == 'seller' && 
         cw_query_first_cell("select email_$area_name from $tables[order_statuses] where code='$status_code'") &&
        ($email = cw_query_first_cell ("SELECT email FROM $tables[customers] WHERE customer_id='".$doc_data['info']['warehouse_customer_id']."'"))
       ) {
        $emails[] = $email;
    }

    return $emails;
}

// end hooks

// events

function cw_seller_on_doc_change_status_emails_send($doc_data, $status) {
    global $config, $smarty, $tables;

    if ($notify_emails = cw_call('cw_doc_order_status_emails', array($doc_data, $status, 'seller'))) {
        $smarty->assign('usertype_layout', 'V');
        $smarty->assign('is_email_invoice', 'Y');
        foreach ($notify_emails as $notify_email) {
            $to_customer  = cw_query_first_cell("SELECT language FROM $tables[customers] WHERE email='$notify_email' ORDER BY customer_id DESC");
            if (empty($to_customer))
                $to_customer = $config['default_admin_language'];
            $current_language = $to_customer;
            cw_call('cw_send_mail', array($config['Company']['orders_department'], $notify_email,
                     'mail/docs/status_changed_seller_subj.tpl', 'mail/docs/status_changed_seller.tpl', $config['default_admin_language'], true));
        }
        $smarty->assign('is_email_invoice', 'N');
        $smarty->assign('usertype_layout', '');
    }

}

/**
 * Add seller ID as part of hash to split cart to orders
 * 
 * @see event on_build_order_hash
 * 
 * @param array $product - product info
 * 
 * @return string
 */
 /* TODO: Move to seller addon */
function cw_seller_on_build_order_hash($product) {
    //return 'S'.$product['system']['creation_customer_id'];
    //MAGEXCH fix, to be moved to addon as replace hook	
    return 'S';
}

// end events
