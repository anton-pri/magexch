<?php
if (empty($customer_id)) {
    $linkedin_redirect_url = $http_location.'/index.php?target=login_with_linkedin';

    //include linkedin api files
    cw_include('addons/linkedin_login/include/lib/http.php');
    cw_include('addons/linkedin_login/include/lib/oauth_client.php');

    $linkedin_login_client = new oauth_client_class;
    $linkedin_login_client->server = 'LinkedIn';
    $linkedin_login_client->debug = 0;
    $linkedin_login_client->redirect_uri = $linkedin_redirect_url;

    $linkedin_login_client->client_id = $config['linkedin_login']['linkedin_login_key'];
    $application_line = __LINE__;
    $linkedin_login_client->client_secret = $config['linkedin_login']['linkedin_login_secret'];

    $linkedin_login_info = &cw_session_register('linkedin_login_info');

    $linkedin_login_client->scope = 'r_fullprofile r_emailaddress';
/*
    if (($success = $linkedin_login_client->Initialize())) {

        if (($success = $linkedin_login_client->Process())) {

            if(strlen($linkedin_login_client->authorization_error)) {
                $linkedin_login_client->error = $linkedin_login_client->authorization_error;
                $success = false;
            } elseif(strlen($linkedin_login_client->access_token)) {
                $success = $linkedin_login_client->CallAPI(
               'https://apis.live.net/v5.0/me',
               'GET', array(), array('FailOnAccessError'=>true), $user);
            }

        }

        $success = $linkedin_login_client->Finalize($success);
    }
*/
    if(($success = $linkedin_login_client->Initialize())) {

        if(($success = $linkedin_login_client->Process())) {

            if(strlen($linkedin_login_client->authorization_error)) {

                $linkedin_login_client->error = $linkedin_login_client->authorization_error;
                $success = false;

            } elseif(strlen($linkedin_login_client->access_token)) {
                $success = $linkedin_login_client->CallAPI(
                    'https://api.linkedin.com/v1/people/~',
                    'GET', array(
                        'format'=>'json'
                    ), array('FailOnAccessError'=>true), $user);

                /*
                 * Use this if you just want to get the LinkedIn user email address
                 */

                $success = $linkedin_login_client->CallAPI(
                    'https://api.linkedin.com/v1/people/~/email-address',
                    'GET', array(
                        'format'=>'json'
                    ), array('FailOnAccessError'=>true), $linkedin_email);

            }
        }
        $success = $linkedin_login_client->Finalize($success);
    }

    cw_log_add('linkedin_login', array($linkedin_email, $user, $success));

    if ($success) {
        //For logged in user, get details from linkedin using access token
        $linkedin_user_id              = md5($linkedin_email); 
//        $linkedin_uname                = $user->firstName . ' ' . $user->lastName;
        $linkedin_user_firstname       = $user->firstName;
        $linkedin_user_lastname        = $user->lastName;

        cw_load('user');

        $user_data = cw_query_first("SELECT $tables[customers].*
                                    FROM $tables[customers] 
                                    WHERE oauth_uid='" . $linkedin_user_id . "' AND oauth_type='L'  
                                    AND usertype='" . $current_area . "'");

        if (!empty($user_data)) {   // login user
            if ($user_data['status'] != 'Y')  {
                //error message disabled login
                cw_add_top_message(cw_get_langvar_by_name('err_account_temporary_disabled'),'E');
                if (!empty($linkedin_login_info['return_url'])) {
                    cw_header_location($linkedin_login_info['return_url']);
                } else {
                    cw_header_location($linkedin_redirect_url);
                }
            }
        } else {
            //add new customer to database
            $register = array();
            $register['email'] = $linkedin_email;
            $register['usertype'] = $current_area;
            $partner_membership = &cw_session_register('partner_membership');
            $register['membership_id'] = $partner_membership;

            $customer_id = cw_user_create_profile($register);

            cw_array2update('customers',  array('oauth_uid' => $linkedin_user_id, 'oauth_type' => 'L'), "customer_id='$customer_id'");

            $user_name = array($linkedin_user_firstname, $linkedin_user_lastname);

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

            if (!empty($linkedin_login_info['return_url'])) {
                cw_header_location($linkedin_login_info['return_url']);
            } else { 
                cw_header_location($linkedin_redirect_url);
            }
        }
    }
}
