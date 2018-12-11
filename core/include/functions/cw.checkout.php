<?php
cw_load('cart', 'profile_fields');

function cw_checkout_login_prepare() {
    global $smarty, $user_account, $customer_id, $user_address;

    $customer_id = &cw_session_register('customer_id', 0);
    $fields_area = cw_profile_fields_get_area($customer_id, $salesman_membership, 1);

    list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);
# kornev, the login information is not required here.
    if ($customer_id) unset($profile_sections['web']);
    $smarty->assign('profile_fields', $profile_fields);
    $smarty->assign('profile_sections', $profile_sections);

    cw_include('include/check_userdata.php');
    cw_include('include/check_usercart.php');

    $userinfo = cw_call('cw_checkout_userinfo', array($user_account));
    $smarty->assign('userinfo', $userinfo);
    $smarty->assign('user_account', $user_account);
}

function cw_checkout_login() {
    global $smarty;

    cw_call('cw_checkout_login_prepare');

    $top_message = &cw_session_register('top_message');
    $smarty->assign('top_message', $top_message);
    $top_message = array();

    header("Content-type: application/xml");
    cw_display('customer/checkout/xml_login.tpl', $smarty);
    die();
}

// TODO: Rework to standard AJAX/XML response in cart controller
function cw_checkout_show_cart($action) {
    global $smarty;

    $top_message = &cw_session_register('top_message');
    $smarty->assign('top_message', $top_message);
    $top_message = array();

    $cart = &cw_session_register('cart', array());
    $smarty->assign('cart', $cart);

    $smarty->assign('action', $action);

    header("Content-type: application/xml");
    cw_display('customer/checkout/xml_cart.tpl', $smarty);
    die();
}

function cw_checkout_userinfo($user_account) {
    global $customer_id, $config;

    $userinfo = $user_account;

    if ($customer_id) {
        $userinfo = array_merge(cw_user_get_info($customer_id, 65535),$userinfo);
    }

    $userinfo['main_address'] = cw_user_get_address_by_type('main');
    $userinfo['current_address'] = cw_user_get_address_by_type('current');

    return $userinfo;
}

function cw_checkout_prepare() {
    $cart = &cw_session_register('cart', array());

	cw_check_product_warehouses($cart);
}
