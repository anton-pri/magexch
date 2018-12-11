<?php
if (empty($customer_id)) {
    $twitter_redirect_url = $http_location.'/index.php?target=twitter_login_verified';

    //include twitter api files
    cw_include('addons/twitter_login/include/twitteroauth/twitteroauth.php');

    $twitter_login_info = &cw_session_register('twitter_login_info');

    if (empty($twitter_login_info['twitter_login_token'])) {

        $connection = new TwitterOAuth($config['twitter_login']['twitter_login_consumer_key'], $config['twitter_login']['twitter_login_consumer_secret']);
        $request_token = $connection->getRequestToken($twitter_redirect_url);

        // any value other than 200 is failure, so continue only if http code is 200
        if ($connection->http_code=='200') {

            //received token info from twitter
            $twitter_login_info['twitter_login_token'] = $request_token['oauth_token'];
            $twitter_login_info['twitter_login_token_secret'] = $request_token['oauth_token_secret'];

            //redirect user to twitter
            $twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
            cw_header_location($twitter_url);
        } else{
            $top_message = array('type' => 'E', 'content' => "error connecting to twitter! try again later! HTTP code: ".($connection->http_code));
            cw_header_location($twitter_login_info['return_url']);
        }
    } else {

        if (isset($_REQUEST['oauth_token']) && $twitter_login_info['twitter_login_token'] !== $_REQUEST['oauth_token']) {

            // if token is old, distroy any session and redirect user to index.php
            $twitter_login_info['twitter_login_token'] = '';
            $twitter_login_info['twitter_login_token_secret'] = '';
            cw_header_location('index.php?target=twitter_login_verified');
        } elseif(isset($_REQUEST['oauth_token']) && $twitter_login_info['twitter_login_token'] == $_REQUEST['oauth_token']) {

            $connection = new TwitterOAuth($config['twitter_login']['twitter_login_consumer_key'],
                                   $config['twitter_login']['twitter_login_consumer_secret'],
                                   $twitter_login_info['twitter_login_token'],
                                   $twitter_login_info['twitter_login_token_secret']);

            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

            if ($connection->http_code=='200') {

                $user = $connection->get('account/verify_credentials');

                // unset no longer needed request tokens
                $twitter_login_info['twitter_login_token'] = '';
                $twitter_login_info['twitter_login_token_secret'] = '';

                //For logged in user, get details from twitter using access token
                $twitter_user_id              = $user->id;
                $twitter_uname                = $user->name;
                $user_name                    = explode(" ", $twitter_uname);
                $twitter_user_firstname       = $user_name[0];
                $twitter_user_lastname        = $user_name[1];

                $twitter_email                =  "twitter-" . $twitter_user_id . "-tmp-email";

                cw_load('user');

                $user_data = cw_query_first("SELECT $tables[customers].*
                                             FROM $tables[customers] 
                                             WHERE oauth_uid='" . $twitter_user_id . "' AND oauth_type='T'  
                                             AND usertype='" . $current_area . "'");

                if (!empty($user_data)) {   // login user
                    if ($user_data['status'] != 'Y')  {
                        //error message disabled login
                        cw_add_top_message(cw_get_langvar_by_name('err_account_temporary_disabled'),'E');

                        if (!empty($twitter_login_info['return_url'])) {
                            cw_header_location($twitter_login_info['return_url']);
                        } else {
                            cw_header_location($twitter_redirect_url);
                        }
                    }
                } else {
                    //add new customer to database
                    $register = array();
                    $register['email'] = $twitter_email;
                    $register['usertype'] = $current_area;
                    $partner_membership = &cw_session_register('partner_membership');
                    $register['membership_id'] = $partner_membership;

                    $customer_id = cw_user_create_profile($register);

                    cw_array2update('customers',  array('oauth_uid' => $twitter_user_id, 'oauth_type' => 'T'), "customer_id='$customer_id'");

                    $user_name = array($twitter_user_firstname, $twitter_user_lastname);

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
                }

                if (!empty($twitter_login_info['return_url'])) {
                    cw_header_location($twitter_login_info['return_url']);
                } else {
                    cw_header_location('index.php');
                }

            } else {
                cw_header_location('index.php?target=twitter_login_verified');
            }
        }
    }
}
