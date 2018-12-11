<?php
if (empty($customer_id)) {
    ########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
    $google_client_id       =  $config['googleplus_login']['googleplus_login_client_id'];
    $google_client_secret   =  $config['googleplus_login']['googleplus_login_client_secret'];
    $google_redirect_url    =  $http_location.'/index.php?action=googleplus_login';
    $google_developer_key   =  $config['googleplus_login']['googleplus_login_developer_key'];

    //include google api files
    cw_include('addons/googleplus_login/include/src/Google_Client.php');
    cw_include('addons/googleplus_login/include/src/contrib/Google_Oauth2Service.php');

    $gClient = new Google_Client();
    $gClient->setApplicationName('Test Google+ Login CW');
    $gClient->setClientId($google_client_id);
    $gClient->setClientSecret($google_client_secret);
    $gClient->setRedirectUri($google_redirect_url);
    $gClient->setDeveloperKey($google_developer_key);

    $google_oauthV2 = new Google_Oauth2Service($gClient);

    $googleplus_login_info = &cw_session_register('googleplus_login_info');
    //If code is empty, redirect user to google authentication page for code.
    //Code is required to aquire Access Token from google
    //Once we have access token, assign token to session variable
    //and we can redirect user back to page and login.
    if (isset($_GET['code'])) {
        if ($_GET['action'] == 'googleplus_login') {
        $gClient->authenticate($_GET['code']);
        $googleplus_login_info['token'] = $gClient->getAccessToken();
        cw_header_location($google_redirect_url); //--? probably no need to redirect
        }  
    }

    if (isset($googleplus_login_info['token'])) { 
       $gClient->setAccessToken($googleplus_login_info['token']);
    }

    if ($gClient->getAccessToken()) {
        //For logged in user, get details from google using access token
        $googleplus_user                 = $google_oauthV2->userinfo->get();
        $googleplus_user_id              = $googleplus_user['id'];
        $googleplus_user_name            = filter_var($googleplus_user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $googleplus_email                = filter_var($googleplus_user['email'], FILTER_SANITIZE_EMAIL);
        $googleplus_profile_url          = filter_var($googleplus_user['link'], FILTER_VALIDATE_URL);
        $googleplus_profile_image_url    = filter_var($googleplus_user['picture'], FILTER_VALIDATE_URL);
        $googleplus_personMarkup         = "$googleplus_email<div><img src='$googleplus_profile_image_url?sz=50'></div>";
        $googleplus_login_info['token']    = $gClient->getAccessToken();
//================================================================
        cw_load('user');

        $user_data = cw_query_first("SELECT $tables[customers].*
                                    FROM $tables[customers] 
                                    WHERE oauth_uid='" . $googleplus_user_id . "' AND oauth_type='G'  
                                    AND usertype='" . $current_area . "'");

        if (!empty($user_data)) {   // login user
            if ($user_data['status'] != 'Y')  {
                //error message disabled login
                cw_add_top_message(cw_get_langvar_by_name('err_account_temporary_disabled'),'E');
                cw_header_location($google_redirect_url);
            }
        } else {
            //add new customer to database
            $register = array();
            $register['email'] = $googleplus_email;
            $register['usertype'] = $current_area;
            $partner_membership = &cw_session_register('partner_membership');
            $register['membership_id'] = $partner_membership;

            $customer_id = cw_user_create_profile($register);

            cw_array2update('customers',  array('oauth_uid' => $googleplus_user_id, 'oauth_type' => 'G'), "customer_id='$customer_id'");

            $user_name = explode(" ", $googleplus_user_name);

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
            //if ($is_ajax && $is_checkout) cw_call('cw_checkout_login');
            if (!empty($googleplus_login_info['return_url'])) {
                cw_header_location($googleplus_login_info['return_url']);
            } else { 
                cw_header_location($google_redirect_url);
            }
        }
    } else {
        //For Guest user, get google login url
        $googleplus_login_authUrl = $gClient->createAuthUrl();
        $smarty->assign('googleplus_login_authUrl', $googleplus_login_authUrl);

        if (!$is_ajax)   
            $googleplus_login_info['return_url'] = $current_host_location.$_SERVER['REQUEST_URI'];

    }
}
