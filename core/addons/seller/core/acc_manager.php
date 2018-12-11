<?php
cw_load('mail');
$user_type = $usertype = 'V';
$cart = &cw_session_register('cart', array());
$from_checkout = &cw_session_register('from_checkout');
$prefilled_info = &cw_session_register('prefilled_info', array());
$partner_membership = &cw_session_register('partner_membership');
$top_message = &cw_session_register('top_message', array());

if ($action == 'register_seller') {
    $required_fields = array('email', 'password', 'password2');
    $usertype = seller_area_letter;

    $fill_error = array();
    foreach($required_fields as $val)
        if (empty($register[$val])) $fill_error[$val] = true;
    if (count($fill_error)) $fill_error = array(cw_get_langvar_by_name('lbl_fill_in_required_fields'));

// artem, TODO: add fields validation

    if ($register['email']) {
// artem, TODO: no direct SQL, only api calls
        $is_user = cw_query_first_cell("select count(*) from $tables[customers] where email='$register[email]'");
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
        cw_add_top_message(join('<br/>',$fill_error),'E');
    }
    else  {
        $register['usertype'] = $usertype;
        $register['membership_id'] = $partner_membership;
        $register['status'] = 'N'; // Suspended by default until approval
        $customer_id = cw_user_create_profile($register);
        cw_add_top_message(cw_get_langvar_by_name('lbl_b2b_account_suspended', array('email' => $register['email']), false, true), 'W');
        cw_user_send_modification_mail($customer_id, true);
        $identifiers = &cw_session_register('identifiers',array());
        $identifiers[$usertype] = array (
            'customer_id' => $customer_id,
        );
        $prefilled_info = array();

        cw_header_location("index.php");

    }
    
    cw_header_location("index.php?target=$target&usertype=$usertype");
}

if ($action == 'login_seller') {
    global $action, $current_area;
    $action = 'login';
    $current_area = seller_area_letter;
    cw_include('include/login.php');
    cw_header_location("index.php?target=$target");
}

if ($action == 'logout') {
    cw_include('include/login.php');
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
elseif (empty($customer_id)) {
    $location[] = array(cw_get_langvar_by_name('lbl_acc_manager'), '');
    $location[] = array(cw_get_langvar_by_name('lbl_login_register'), '');
    $smarty->assign('main', 'acc_manager');
}
else {
    global $user, $user_to_modify;
    global $usertype;

    $usertype = seller_area_letter;
    $user_to_modify = $user = $customer_id;
    $self_modification = 1;
    
    if ($mode == 'photos') {
        cw_include('include/users/photos.php');
    } else {
        cw_include('include/users/modify.php');
        $smarty->assign('main', 'modify');
        $location[] = array(cw_get_langvar_by_name('lbl_acc_manager'), '');
    }
    $smarty->assign('mode', $mode);
}

$smarty->assign('usertype', $usertype);
$smarty->assign('prefilled', $prefilled_info);

$smarty->assign('current_section_dir', 'acc_manager');
