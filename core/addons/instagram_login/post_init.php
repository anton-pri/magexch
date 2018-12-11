<?php
if (empty($customer_id)) {
    $instagram_client_id       = $config['instagram_login']['instagram_login_client_id'];
    $instagram_client_secret   = $config['instagram_login']['instagram_login_client_secret'];
    $instagram_redirect_url    = $http_location.'/index.php?action=instagram_login';

    //'http://dev.cartworks.com/product_stages/index.php?action=instagram_login'; //path to your script

    //include instagram api files
    cw_include('addons/instagram_login/include/src/Instagram.php');

    $instagram = new MetzWeb\Instagram\Instagram(array(
        'apiKey'      => $instagram_client_id,
        'apiSecret'   => $instagram_client_secret,
        'apiCallback' => $instagram_redirect_url
    ));

    $instagram_login_info = &cw_session_register('instagram_login_info');

    //If code is empty, redirect user to instagram authentication page for code.
    //Code is required to aquire Access Token from instagram
    //Once we have access token, assign token to session variable
    //and we can redirect user back to page and login.
    if (isset($_GET['code'])) {
       if ($_GET['action'] == 'instagram_login') {
       // receive OAuth token object
       $data = $instagram->getOAuthToken($_GET['code']);

       // store user access token
       $instagram->setAccessToken($data);

       // now you have access to all authenticated user methods
       $result = $instagram->getUserMedia();

       $instagram_login_info['data'] = $data;
       $instagram_login_info['result'] = $result;
       }
    }


    if ($instagram_login_info['data']->user->id) {

        //For logged in user, get details from instagram using access token
        $instagram_user_id              = $instagram_login_info['data']->user->id;
        $instagram_uname                = $instagram_login_info['data']->user->username;
        $instagram_user_name            = $instagram_login_info['data']->user->full_name;

        if (empty($instagram_user_name))
            $instagram_user_name = $instagram_uname;

        $instagram_email                = "instagram-" . $instagram_user_id . "-tmp-email";
        $instagram_profile_image_url    = $instagram_login_info['data']->user->profile_picture;
//        $instagram_personMarkup         = "$instagram_email<div><img src='$instagram_profile_image_url?sz=50'></div>";
//================================================================
        cw_load('user');

        $user_data = cw_query_first("SELECT $tables[customers].*
                                    FROM $tables[customers] 
                                    WHERE oauth_uid='" . $instagram_user_id . "' AND oauth_type='I'  
                                    AND usertype='" . $current_area . "'");

        if (!empty($user_data)) {   // login user
            if ($user_data['status'] != 'Y')  {
                //error message disabled login
                cw_add_top_message(cw_get_langvar_by_name('err_account_temporary_disabled'),'E');
                cw_header_location($instagram_redirect_url);
            }
        } else {
            //add new customer to database
            $register = array();
            $register['email'] = $instagram_email;
            $register['usertype'] = $current_area;
            $partner_membership = &cw_session_register('partner_membership');
            $register['membership_id'] = $partner_membership;

            $customer_id = cw_user_create_profile($register);

            cw_array2update('customers',  array('oauth_uid' => $instagram_user_id, 'oauth_type' => 'I'), "customer_id='$customer_id'");

            $user_name = explode(" ", $instagram_user_name);

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

            if (!empty($instagram_login_info['return_url'])) {
                cw_header_location($instagram_login_info['return_url']);
            } else { 
                cw_header_location($instagram_redirect_url);
            }
        }
    } else {
        //For Guest user, get instagram login url
        $instagram_login_authUrl = $instagram->getLoginUrl();
        $smarty->assign('instagram_login_authUrl', $instagram_login_authUrl);

        if (!$is_ajax)   
            $instagram_login_info['return_url'] = $current_host_location.$_SERVER['REQUEST_URI'];

    }
}
