<?php
cw_load('mail');

$cart = &cw_session_register('cart', array());
$prefilled_info = &cw_session_register('prefilled_info');
$partner_membership = &cw_session_register('partner_membership');

if ($action == 'register_customer' || $action == 'register_reseller') {
    $required_fields = array('email', 'password', 'password2');
    if ($action == 'register_reseller') $required_fields[] = 'tax_number';

    $fill_error = array();
    foreach($required_fields as $val)
        if (empty($register[$val])) $fill_error[$val] = true;
    $error_text = '';
    if (count($fill_error)) $error_text = cw_get_langvar_by_name('lbl_fill_in_required_fields');


    if ($register['email']) {
        $is_user = cw_query_first_cell("select count(*) from $tables[customers] where email='$register[email]'");
        if ($is_user) {
            $fill_error['email'] = true;
            $error_text .= '<br/>'.cw_get_langvar_by_name('lbl_username_already_used');
        }
    }

    if ($register['password'] != $register['password2']) {
        $fill_error['password'] = true;
        $error_text .= '<br/>'.cw_get_langvar_by_name('lbl_password_confirmation_wrong');
    }
    if (count($fill_error)) {
        $prefilled_info = $register;
        $top_message['content'] = $error_text;
        $top_message['type'] = 'E';
    }
    else  {
        $usertype = 'C';
        if ($action == 'register_reseller') $usertype = 'R';
        $register['usertype'] = $usertype;
        $register['membership_id'] = $partner_membership;
        $customer_id = cw_user_create_profile($register);
        cw_user_send_modification_mail($customer_id, true);
        $identifiers = &cw_session_register("identifiers",array());
        $identifiers[$usertype] = array (
            'customer_id' => $customer_id,
        );
        $prefilled_info = array();
    }
    cw_header_location("index.php?target=$target&usertype=$usertype");
}

if ($action == 'login_customer') {
    $action = 'login';
    $current_area = 'C';
    include $app_main_dir.'/include/login.php';
    cw_header_location("index.php?target=$target");
}

if ($action == 'login_reseller') {
    $action = 'login';
    $current_area = 'R';
    include $app_main_dir.'/include/login.php';
    cw_header_location("index.php?target=$target");
}

if(empty($customer_id)) {
    cw_load('map');
    $smarty->assign('countries', cw_map_get_countries());

    $smarty->assign('main', 'acc_manager');
}
else {
    $user_to_modify = $customer_id;
    $self_modification = 1;
    include $app_main_dir.'/include/users/modify.php';
    $smarty->assign('main', 'profile');
    $location[] = array(cw_get_langvar_by_name('lbl_acc_manager'), '');
}

$smarty->assign('usertype', $usertype);
$smarty->assign('prefilled', $prefilled_info);

$location[] = array(cw_get_langvar_by_name('lbl_login_salesman'));

$smarty->assign('current_section_dir', 'acc_manager');
?>
