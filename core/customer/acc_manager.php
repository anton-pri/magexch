<?php
/* TODO: Damn, this controller repeats actions implemented in include/user/info.php and its subcontrollers */
cw_load('mail','check_user_field');

$cart = &cw_session_register('cart', array());
$from_checkout = &cw_session_register('from_checkout');
$prefilled_info = &cw_session_register('prefilled_info', array());
$fill_error = &cw_session_register('fill_error', array());
$partner_membership = &cw_session_register('partner_membership');
$top_message = &cw_session_register('top_message', array());

if ($action == 'register_customer' || $action == 'register_reseller') {
    $required_fields = array('email', 'password', 'password2');
    $usertype = 'C';

    if (empty($register['password'])) {
        $register['password2'] = $register['password'] = cw_user_generate_password();
    }

    $fill_error = array();
    foreach($required_fields as $val)
        if (empty($register[$val])) $fill_error[$val] = true;
    if (count($fill_error)) $fill_error = array(cw_get_langvar_by_name('lbl_fill_in_required_fields'));

// artem, TODO: add fields validation

    if ($register['email']) {
        $is_user = Customer\getByEmailCustomer($register['email']);
        if ($is_user) {
            $fill_error['email'] = cw_get_langvar_by_name('lbl_email_already_used');
        }
    }

    if ($register['password'] != $register['password2']) {
        $fill_error['password'] = cw_get_langvar_by_name('lbl_password_confirmation_wrong');
    }
    
    // Handlers of on_register_validate should return array('field'=>'message') if field is failed
    $validation = cw_event('on_register_validate', array($register, $usertype), array());
    foreach ($validation as $res) {
        if (is_array($res))
            $fill_error = cw_array_merge_assoc($fill_error, $res);
    }

    if (count($fill_error)) {
        $prefilled_info = $register;
        $top_message = array('content' => join('<br/>',$fill_error), 'type' => 'E');
    }
    else  {
        $usertype = 'C';
        if ($action == 'register_reseller') $usertype = 'R';
        $register['usertype'] = $usertype;
        $register['membership_id'] = $partner_membership;
        $customer_id = cw_call('cw_user_create_profile', array($register));
        cw_user_send_modification_mail($customer_id, true);
        $identifiers = &cw_session_register('identifiers',array());
        $identifiers[$usertype] = array (
            'customer_id' => $customer_id,
        );
        $prefilled_info = array();
    
	    $remember_data = &cw_session_register("remember_data");
        if (isset($remember_data['URL']) && !empty($remember_data['URL'])) {
            cw_header_location($remember_data['URL']);
        }
        if ($customer_id)
            db_query("update $tables[customers_system_info] set last_login='".cw_core_get_time()."' where customer_id='$customer_id'");
    }

    cw_header_location("index.php?target=$target&usertype=$usertype");
}

if ($action == 'login_customer' || $action == 'login') {
    global $action, $current_area;
    $action = 'login';
    $current_area = 'C';

    if (defined('IS_AJAX') && $config['Security']['use_https_login']=='Y' && $_SERVER['HTTP_ORIGIN'] == 'http://'.$app_config_file['web']['http_host']) {
        // Allow cross-domain ajax requests between http and https
        header('Access-Control-Allow-Origin: http://'.$app_config_file['web']['http_host']);
    }
    cw_include('include/login.php');
    cw_header_location("index.php?target=$target");
}

if ($action == 'login_reseller') {
    global $action, $current_area;
    $action = 'login';
    $current_area = 'R';
    cw_include('include/login.php');
    cw_header_location("index.php?target=$target");
}

if ($action == 'logout') {
    cw_include('include/login.php');
}

if ($mode == 'need_login' && defined('IS_AJAX')) {
        $usertype = 'C';
    cw_add_ajax_block(array(
        'id' => 'login_dialog',
        'template' => 'customer/acc_manager/login_customer.tpl',
    ));
    cw_add_ajax_block(array(
        'id' => 'script',
        'content' => "sm('login_dialog', need_login_width, need_login_height, true, 'Need login');",
    ));

    $remember_data = &cw_session_register("remember_data");
    $remember_data['URL'] = $redirect_to;
}

if ($is_ajax) {
    global $user, $user_to_modify;
    $user_to_modify = $user = $customer_id;
    $self_modification = 1;
    cw_include('include/users/modify.php');
}
elseif ($mode == 'delete') {
    cw_include('include/users/modify.php');
}
elseif(empty($customer_id)) {
	$location[] = array(cw_get_langvar_by_name('lbl_acc_manager'), '');
    $location[] = array(cw_get_langvar_by_name('lbl_register'), '');
    $smarty->assign('main', 'acc_manager');
}
else {
    global $user, $user_to_modify, $self_modification;
	$user_to_modify = $user = $customer_id;
	$self_modification = 1;

/*
    if ($mode == 'addresses')
        cw_include('include/users/addresses.php');
*/
    if ($mode == 'photos')
        cw_include('include/users/photos.php');
    elseif ($mode == 'discounts') {
        $action = null;
        cw_include('include/users/discounts.php');
    }
    elseif ($mode == 'purchased_products')
        cw_include('include/users/purchased_products.php');
    else {
        cw_include('include/users/modify.php');
        $smarty->assign('main', 'profile');
        $location[] = array(cw_get_langvar_by_name('lbl_acc_manager'), '');
    }
    $smarty->assign('mode', $mode);
}

$smarty->assign('usertype', $usertype);
$smarty->assign('prefilled', $prefilled_info);
$smarty->assign('fill_error', $fill_error);
$fill_error = array();

$smarty->assign('current_section_dir', 'acc_manager');
