<?php
if (empty($customer_id) && ($target != 'twitter_login_verified')) {

    $twitter_login_info = &cw_session_register('twitter_login_info');

    $twitter_login_authUrl = $http_location.'/index.php?target=twitter_login_verified'; 
    $smarty->assign('twitter_login_authUrl', $twitter_login_authUrl);   

    if (!$is_ajax) { 
        $twitter_login_info = array('return_url' => $current_host_location.$_SERVER['REQUEST_URI']);
    }
//print($target);
//    print_r($twitter_login_info);   die;
}
