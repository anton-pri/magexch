<?php
if (empty($customer_id) && ($target != 'mslive_login')) {

    $mslive_login_info = &cw_session_register('mslive_login_info');

    $mslive_login_authUrl = $http_location.'/index.php?target=mslive_login'; 
    $smarty->assign('mslive_login_authUrl', $mslive_login_authUrl);   

    if (!$is_ajax) 
        $mslive_login_info['return_url'] = $current_host_location.$_SERVER['REQUEST_URI']; 
}
