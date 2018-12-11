<?php
cw_load('profile_fields', 'check_user_field');
cw_load('ajax');

global $self_modification, $current_area, $usertype, $address_type;
$avail_area = cw_profile_fields_get_area($user, 0, $self_modification, (AREA_TYPE == 'A'?(empty($usertype)?'C':$usertype):'C'));
list($profile_sections, $profile_fields) = cw_profile_fields_get_sections('U', true, $avail_area);
$smarty->assign('profile_fields', $profile_fields);


$smarty->assign('is_checkout', $is_checkout);

$user_address = &cw_session_register('user_address', array());    // Declare session var for addresses

// In customer area $user and $customer_id must be equal
// The same also if it is not possible to define user by address_id.
if ($current_area != 'A' || (empty($user) && empty($address_id))) {
    $user = $customer_id;
}

// Request main/current address from session
// ?action=load&address_type=main
if (empty($address_id) && ($address_type == 'main' || $address_type == 'current')) {
    $address = cw_user_get_address_by_type($address_type);
    $address_id = intval($address['address_id']);
}

// Request main/current address from address book
// ?action=load&address_id=main[&user=uid]
if (empty($address_type) && ($address_id == 'main' || $address_id == 'current')) {
//    $address_type = $address_id;
    $address = cw_user_get_address($user, $address_id);
    $address_id = intval($address['address_id']);
}

// Request address by ID from address book without user
// Define user by address
// ?action=load&address_id=ID
if (isset($address_id) && empty($user)) {
    $address = cw_user_get_address(null, $address_id);
    $user = $address['customer_id'];
}

if (empty($address_type)) {
    $address_type = $address_id;
}

if ($action == 'set_same') {
    $temp_current_address = &cw_session_register('temp_current_address', cw_user_get_address_by_type('current'));
    if ($same == 1)  {
        $temp_current_address = $user_address['current_address'];
        $user_address['current_address'] = $user_address['main_address'];
    }
    else {
        $user_address['current_address'] = $temp_current_address;
        $user_address['current_address']['address_id'] = 'current';
        cw_session_unregister('temp_current_address');
    }

    $smarty->assign('is_checkout', 1);

    // cw_add_ajax_block(array('id'=>'debug','action'=>'ignore','content'=>print_r($user_address,true)));

    if (!$same) {
        $action = 'load';
        $address_type = $address_id = 'current';
        $address = $user_address['current_address'];
    } else {
        cw_add_ajax_block(array(
        'id' =>'current_address',
        'content' => '',
        ),'current_address');
    }
}

if ($action == 'save' && !empty($user)) {
    $fill_error = array();

    $update_fields['address'] = cw_user_address_array($update_fields['address']); // make sure we processes array of addresses

    foreach ($update_fields['address'] as $addr_type => $address) {


        if ($is_checkout) $address['address_id'] = $addr_type;        

	    foreach ($address as $k => $v) {
	        if (!is_array($v)) $address[$k] = trim($v);	
	    }

	    foreach (array('city', 'state', 'country', 'zipcode') as $v) {
	        if (!$profile_fields['address'][$v]['is_avail'] && empty($address[$v]))
	            $address[$v] = $config['General']['default_' . $v];
	    }
	
	    $fill_error = cw_check_user_field_validate($user, array('address'=>$address), $profile_fields);
	
	    $prefilled_address = array();

	    if (count($fill_error)) {
	        $prefilled_address = $address;
	        $smarty->assign('address_errors', cw_check_user_get_error($fill_error));
            cw_add_top_message(cw_check_user_get_error($fill_error),'E');
	    }
	    else {
	        $address['customer_id'] = $user;

            // Main and current addresses are saved in session only (not in DB)
	        if (in_array($address['address_id'], array('main', 'current'))) {
	            $user_address[$address['address_id'] . '_address'] = $address;
	        }
            
            // If main is updated and current is defined as "same", then copy address to current
            if ($address['address_id'] == 'main' 
            && $user_address['main_address']['address_id']==$user_address['current_address']['address_id']) {
                $user_address['current_address'] = $user_address['main_address'];
            }
            
	        if ($address['as_new']) {
	        	$address['address_id'] = 0;
	        	unset($address['as_new']);
	        }

	        if (!in_array($address['address_id'], array('main', 'current'), true)) {
	            $address_id = intval($address['address_id']);
	            $naid = cw_user_update_address($user, $address_id, $address);
	            $address_id = $naid;

	            // For correct shipping cost calculate after address changing
	            cw_load('user');
	            
	            $user_address = &cw_session_register('user_address');
	            $user_address = array();

	            global $userinfo;	            
	            $userinfo['current_address'] = cw_user_get_address_by_type('current');
	        }
            cw_add_top_message('Address has been updated');
	    }
    }

    cw_user_check_addresses($user);

    $action = 'load';

    if ($is_checkout && empty($fill_error)) $action = 'load_checkout';
}

if ($action == 'delete') {
    cw_user_delete_address($user, $address_id);
    cw_user_check_addresses($user);
    cw_add_top_message('Address has been deleted');
    $address_id = $address_type = 0;
    $address = null;
    $action = 'load';
}

if ($action == 'load_checkout') {
    cw_load('checkout');
    $userinfo = cw_call('cw_checkout_userinfo', array($user_account));
    $smarty->assign('userinfo', $userinfo);
    $smarty->assign('is_same', $userinfo['main_address']['address_id']==$userinfo['current_address']['address_id']);
    cw_add_ajax_block(array(
        'id' => 'address',
        'action' => 'update',
        'template' => 'customer/checkout/address.tpl',
    ),'address');
}

if ($action == 'load') {
    if ($fill_error) {
        $address = $prefilled_address;
    } elseif (empty($address)) {
        $address = cw_user_get_address($user, $address_id);
    }

    $smarty->assign('address', $address);
    $smarty->assign('address_id', $address_id);

//    $smarty->assign('is_main', $is_main);
    $smarty->assign('name_prefix', 'update_fields[address]['.(in_array($address_type, array('main','current'), true)?$address_type:$address_id).']');
    if (APP_AREA == 'admin') {
      cw_add_ajax_block(array(
        'id' => (in_array($address_type, array('main','current'), true)?$address_type.'_address':'address'),
        'action' => 'update',
        'template' => 'admin/users/sections/address_modify.tpl',
      ),$address_type.'_address');
    } else {
      cw_add_ajax_block(array(
        'id' => (in_array($address_type, array('main','current'), true)?$address_type.'_address':'address'),
        'action' => 'update',
        'template' => 'main/users/sections/address_modify.tpl',
      ),$address_type.'_address');
    }
}

if ($action == 'set_main' || $action == 'set_current') {
    $field = ($action=='set_main' ? 'main' : 'current');
    Customer\Address\setAddressType($user, $field, $address_id);
    cw_user_check_addresses($user);

    // For correct shipping cost calculate after address changing
    cw_load('user');

    $user_address = &cw_session_register('user_address');
    $user_address = array();

    global $userinfo;
    $userinfo['current_address'] = cw_user_get_address_by_type('current');
    
    cw_add_top_message('Address has been updated');
}

$smarty->assign('address_type', $address_type);
$smarty->assign('user', $user);

if (defined('IS_AJAX')) {
		if ($user)
			$addresses = cw_user_get_addresses(intval($user));
        $smarty->assign('addresses', $addresses);
        if (APP_AREA == 'admin') {
          cw_add_ajax_block(array(
            'id' => 'address_book',
            'action' => 'replace',
            'template' => 'admin/users/address_book.tpl',
          ),'address_book');
        } else {
          cw_add_ajax_block(array(
            'id' => 'address_book',
            'action' => 'replace',
            'template' => 'main/users/address_book.tpl',
          ),'address_book');
        }
} else {
    cw_header_location("index.php?target=$target&user=$user");
}

