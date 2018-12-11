<?php
cw_load('check_user_field');

if ($request_prepared['cat'] != 'shipping_ups') return;

$top_message = &cw_session_register('top_message');
$ups_reg = &cw_session_register('ups_reg', 0);
$ups_account = &cw_session_register('ups_account');

# kornev, if there are no any account - first step of the registration
if (!$config['shipping_ups']['accesskey'] && (!$ups_reg || $ups_reg == 1)) {
    $ups_reg = 1;
    $ups_license = cw_ups_get_license();
    file_put_contents($var_dirs['tmp'].'/ups_license.txt', $ups_license);
    $smarty->assign('license', $ups_license);
}

if ($action == 'ups_agree') {
    if ($confirmed == 'Y')
        $ups_reg = 2;
    cw_header_location('index.php?target=settings&cat=shipping_ups');
}

if ($action == 'ups_register') {
    $rules = array(
        'contact_name' => '',
        'title_name' => '',
        'company' => '',
        'address' => '',
        'city' => '',
        'state' => array('func' => 'cw_error_check_state'),
        'country' => '',
        'postal_code' => '',
        'phone' => '',
        'url' => '',
        'email' => array('func' => 'cw_check_email'),
    );
    $fillerror = cw_error_check($posted_data, $rules);

    if ($fillerror)  {
        $ups_account = $posted_data;
        $top_message = array('content' => $fillerror, 'type' => 'E');
        cw_header_location('index.php?target=settings&cat=shipping_ups');
    }

    $ret = cw_ups_register(file_get_contents($var_dirs['tmp'].'/ups_license.txt'), $posted_data);
    $ups_reg = 0;
    if ($ret) {
        $top_message = array('content' => $ret, 'type' => 'E');
        cw_header_location('index.php?target=settings&cat=shipping_ups');
    }

    $top_message = array('content' => cw_get_langvar_by_name('lbl_ups_registration_successful'), 'type' => 'I');
    cw_header_location('index.php?target=settings&cat=shipping_ups');
}

if ($ups_reg == 2) {
    $smarty->assign('userinfo', $ups_account);
}

$smarty->assign('ups_reg', $ups_reg);
