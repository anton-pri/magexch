<?php
if (empty($customer_id) && ($target != 'login_with_linkedin')) {

    $linkedin_login_info = &cw_session_register('linkedin_login_info');

    $linkedin_login_authUrl = $http_location.'/index.php?target=login_with_linkedin'; 
    $smarty->assign('linkedin_login_authUrl', $linkedin_login_authUrl);   

    if (!$is_ajax) { 
        $linkedin_login_info = array('return_url' => $current_host_location.$_SERVER['REQUEST_URI']);
    }
}
