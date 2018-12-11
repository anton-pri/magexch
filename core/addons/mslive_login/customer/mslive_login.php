<?php
if (empty($customer_id)) {
    $mslive_redirect_url = $http_location.'/index.php?target=mslive_login';

    //include mslive api files
    cw_include('addons/mslive_login/include/lib/http.php');
    cw_include('addons/mslive_login/include/lib/oauth_client.php');

    $mslive_login_client = new oauth_client_class;
    $mslive_login_client->server = 'Microsoft';
    $mslive_login_client->debug = 0;
    $mslive_login_client->redirect_uri = $mslive_redirect_url;

    $mslive_login_client->client_id = $config['mslive_login']['mslive_login_consumer_key'];
    $application_line = __LINE__;
    $mslive_login_client->client_secret = $config['mslive_login']['mslive_login_consumer_secret'];

    $mslive_login_info = &cw_session_register('mslive_login_info');

    $mslive_login_client->scope = 'wl.basic wl.emails wl.birthday';
    if (($success = $mslive_login_client->Initialize())) {
        if (($success = $mslive_login_client->Process())) {
            if(strlen($mslive_login_client->authorization_error)) {
                $mslive_login_client->error = $mslive_login_client->authorization_error;
                $success = false;
            } elseif(strlen($mslive_login_client->access_token)) {
                $success = $mslive_login_client->CallAPI(
               'https://apis.live.net/v5.0/me',
               'GET', array(), array('FailOnAccessError'=>true), $user);
            }
        }
        $success = $mslive_login_client->Finalize($success);
    }

    if ($success) {
        //For logged in user, get details from mslive using access token
        $mslive_user_id              = $user->id;
        $mslive_uname                = $user->name;
        $mslive_user_firstname       = $user->first_name;
        $mslive_user_lastname        = $user->last_name;

        if (empty($mslive_user_firstname) && empty($mslive_user_lastname)) {  
            $mslive_user_names = explode(' ', $mslive_uname);
            $mslive_user_firstname = $mslive_user_names[0];
            $mslive_user_lastname = $mslive_user_names[1];
        }

        $mslive_email                = $user->emails->account;

        if (empty($mslive_email))    $mslive_email = $user->emails->preferred; 

        cw_load('user');

        $user_data = cw_query_first("SELECT $tables[customers].*
                                    FROM $tables[customers] 
                                    WHERE oauth_uid='" . $mslive_user_id . "' AND oauth_type='M'  
                                    AND usertype='" . $current_area . "'");

        if (!empty($user_data)) {   // login user
            if ($user_data['status'] != 'Y')  {
                //error message disabled login
                cw_add_top_message(cw_get_langvar_by_name('err_account_temporary_disabled'),'E');
                if (!empty($mslive_login_info['return_url'])) {
                    cw_header_location($mslive_login_info['return_url']);
                } else {
                    cw_header_location($mslive_redirect_url);
                }
            }
        } else {
            //add new customer to database
            $register = array();
            $register['email'] = $mslive_email;
            $register['usertype'] = $current_area;
            $partner_membership = &cw_session_register('partner_membership');
            $register['membership_id'] = $partner_membership;

            $customer_id = cw_user_create_profile($register);

            cw_array2update('customers',  array('oauth_uid' => $mslive_user_id, 'oauth_type' => 'M'), "customer_id='$customer_id'");

            $user_name = array($mslive_user_firstname, $mslive_user_lastname);

            $address = array(
                'main'      => 1,
                'firstname' => $user_name[0],
                'lastname'  => $user_name[1]
            );
            $userinfo = array(
                'addresses' => $address
            );

            cw_user_update($userinfo, $customer_id, $customer_id);
            $user_data = cw_query_first("SELECT $tables[customers].* FROM $tables[customers] WHERE customer_id='$customer_id'");
        }

        if (!empty($user_data)) { 
            //perform login
            $email = $user_data['email'];
            if ($usertype == 'R') {
                $usertype = 'C';
                $product_list_template = &cw_session_register("product_list_template");
                $product_list_template = 2;
            }

            $identifiers[($current_area == 'R'?'C':$current_area)] = array (
                'customer_id' => $user_data['customer_id'],
            );
            $customer_id = $user_data['customer_id'];
            if (in_array($current_area, array("C", "R"))) {
                cw_session_register("login_redirect");
                $login_redirect = 1;
            }

            // Update addresses in session from database
            $user_address = &cw_session_register('user_address', array());
            $user_address['current_address'] = cw_user_get_address($customer_id, 'current');
            $user_address['main_address'] = cw_user_get_address($customer_id, 'main');

            db_query("update $tables[customers_system_info] set last_login='".cw_core_get_time()."' where customer_id='$customer_id'");

            $current_language = $user_data['language'];
            $items_per_page_targets = cw_core_restore_navigation($customer_id);

            cw_include('init/lng.php');

            $cart = &cw_session_register('cart', array());

            if ($current_area == "C" && cw_is_cart_empty($cart)) {
                $cart = cw_user_get_stored_cart($customer_id);
            }

            $userinfo = cw_user_get_info($customer_id);
            $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
            $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

            cw_event('on_login', array($customer_id, $current_area, 0));

            if (!empty($mslive_login_info['return_url'])) {
                cw_header_location($mslive_login_info['return_url']);
            } else { 
                cw_header_location($mslive_redirect_url);
            }
        }
    }
}
