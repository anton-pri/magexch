<?php
cw_call('cw_license_check');
$license = cw_cache_get('license');

if ($REQUEST_METHOD == 'POST' && $mode == 'register') {

    if ($license['response']['can_be_registered']) {
        
        $params = array();
        $params['api_key'] = cw_license_id();
        $params['version'] = 1;
        $params['method']  = 'license_register';
        $params['request'] = array(
            'domain'=> $reg['domain'],
            'email' => $reg['email'],
            'company'=> $reg['company'],
        );

        $result = cw_api_server_call($params,SERVICE_SERVER.SERVICE_SERVER_SCRIPT);
        
        if ($result['status'] == 'error') {
            cw_add_top_message($result['message'],'E');
        } else {
            cw_add_top_message("License has been registered");
        }
    }
   
    cw_header_location('index.php?target=license');
}



//cw_var_dump($license);
$smarty->assign('license_code',cw_license_id());

$smarty->assign('license',$license);

$smarty->assign('current_section_dir', 'settings');
$smarty->assign('main','license');
